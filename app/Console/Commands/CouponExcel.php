<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use App\Models\CouponMap;
use App\Models\CouponMapBatch;
use App\Models\CouponStock;
use Illuminate\Console\Command;
use Excel;
use DB;

class CouponExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:coupon_excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代金券附件导入处理';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //=========================新的
        set_time_limit(0);
        $i = 0;
        while (true){
            $bid = app('redis')->connection('channel_user')->lpop('l_coupon_excel');
            if($bid){
                try {
                    DB::beginTransaction();
                    $item = CouponMapBatch::onWriteConnection()->find($bid);
                    $item->handle_status = 1;  //处理中，改一下状态。
                    $item->save();
                    $coupon = Coupon::find($item->coupon_id);
                    //开始逻辑。
                    Excel::load($item->file_src, function ($reader)use($item ,$coupon){
                        $reader = $reader->all()->toArray();
                        $setId = isset($reader[0]['id']) ? true : false;
                        $coupon_id = $coupon->id;
                        $data = array();
                        if($setId){  //传id了，不能有空的。
                            foreach ($reader as $v) {
                                $v['validdate'] = $v['validdate'] ?? '';
                                if(empty($v['id']) || empty($v['code'])) continue;
                                $data[] =array(
                                    'bid'=>$item->id,
                                    'coupon_id' => $coupon_id,
                                    'ex_id' => trim(str_replace("'","",$v['id'])),
                                    'ex_code' => trim(str_replace("'","",$v['code'])),
                                    'info' => json_encode(array('ex_id' => trim($v['id']),'ex_code' =>  trim($v['code']))),
                                    'valid_date' => $v['validdate']?trim(str_replace("'","",$v['validdate'])):$coupon->valid_date,
                                );
                            }
                        }else{
                            foreach ($reader as $v) {
                                $v['validdate'] = $v['validdate'] ?? '';
                                if(empty($v['code'])) continue;
                                $data[] =array(
                                    'bid'=>$item->id,
                                    'coupon_id' => $coupon_id,
                                    'ex_id' => '',
                                    'ex_code' => trim(str_replace("'","",$v['code'])),
                                    'info' => json_encode(array('ex_id' => '','ex_code' =>  trim($v['code']))),
                                    'valid_date' => $v['validdate']?trim(str_replace("'","",$v['validdate'])):$coupon->valid_date,
                                );
                            }
                        }
                        $total_num = count($data);
                        $key = $setId ? 'all' : 'ex_code';
                        $uniqueData =  $this->array_unique_fb($data,$key);
                        $unique_num = count($uniqueData);   //去重之后的数据量，插入map表中。
                        DB::table('coupon_map')->insert($uniqueData);
                        //去重操作。连接主库,查出所有重复的。
                        $repeat = CouponMap::onWriteConnection()->where('coupon_id',$coupon_id)->where('status','>=',0)->whereRaw("(ex_id,ex_code) in
(select ex_id,ex_code from coupon_map  where  status >= 0 and coupon_id = " .  $coupon_id. " group by ex_id,ex_code  having count(ex_code) >1)
and id not in  (select min(id) from coupon_map  where   status >= 0  and coupon_id = ". $coupon_id ." group by ex_id,ex_code  having count(ex_code) >1)")->get();



                        if(!empty($repeat->toArray())){
                            //得出新增加的数量
                            //去重。
                            $repeatCount = count($repeat->toArray());

                            $unique_num = $unique_num - $repeatCount;
                            if($repeatCount >= 20){

                                DB::delete("delete from coupon_map where (ex_id,ex_code) in
(
select ex_id,ex_code from 
(select ex_id,ex_code from coupon_map  where   status >= 0 and coupon_id = {$coupon_id} group by ex_id,ex_code having count(ex_code) >1) as tab1
)
and id not in
(
select id from 
	(select min(id) id from coupon_map  where  status >= 0 and coupon_id = {$coupon_id} group by ex_id,ex_code  having count(ex_code) >1) as tab2
)
and coupon_id = {$coupon_id} and status >= 0");
                            }else{
                                foreach ($repeat as $rep){
                                    CouponMap::where('id',$rep->id)->delete();
                                }
                            }

                        }
                        //去重结束之后，查看库存分配表有没有，有的话写入库存分配表
                        if(CouponStock::where('coupon_id',$coupon_id)->where('channel_id',0)->first()){
                            DB::table('coupon_stock')->where('coupon_id', $coupon_id)->where('channel_id', 0)->increment('stock_all', $unique_num, ['stock_remain' => DB::raw("stock_remain + " . $unique_num)]);
                        }
                        //最后更新一下批次表
                        DB::table('coupon_map_batch')->where('id',$item->id)->update(['total_num'=>$total_num,'unique_num'=>$unique_num,'handle_status'=>2]);
                        if (CouponMapBatch::where('id',$item->id)->first()->is_deleted != 1) {
                            DB::commit();
                        }else {
                            DB::rollBack();
                            CouponMapBatch::where('id',$item->id)->update(['unique_num'=>0,'handle_status'=>-1]);
                        }
                    });

                }catch (\Exception $e) {
                    DB::rollBack();
                    CouponMapBatch::where('id',$item->id)->update(['unique_num'=>0,'handle_status'=>-1]);
                }
            }else{
                $i++;
                if($i == 11) $i = 1;
                sleep($i); //单位为秒
            }
        }
    }


    function array_unique_fb($arr,$key){
        $res = array();
        foreach ($arr as $k=> $value) {
            if($key == 'all'){
                $resKey = $value['ex_id'] . '_' . $value['ex_code'];
            }else{
                $resKey = $value[$key];
            }
            if(isset($res[$resKey])){
                unset($arr[$k]);
            }else{
                $res[$resKey] = 1;
            }
        }
        unset($res);
        return $arr;
    }

}
