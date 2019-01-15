<?php

namespace App\Console\Commands;

use App\Models\Cats;
use App\Models\CouponMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CouponData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:coupon_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新coupon表新字段';

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
//    public function handle()
//    {
//        set_time_limit(0);
//        $a = CouponMap::where('ex_id','')->where('ex_code','')->orderBy('id','asc')->first()->id;
//        echo "start\n";
//        for($i=$a/1000 -1;$i<=500;$i++){
//            $couponmap = CouponMap::whereBetween('id',[$i*1000,($i+1)*1000])->where('ex_id','')->where('ex_code','')->get();
//            if(!$couponmap){
//                break;
//            }
//            foreach ($couponmap as $item){
//                $info = json_decode($item->info,true);
//                $item->ex_id = $info['ex_id'] ?? '';
//                $item->ex_code = $info['ex_code'] ?? '';
//                $item->save();
//                echo "save success".$item->id."\n";
//            }
//            usleep(500);
//        }
//        echo "end \n";
//    }


    public function handle()
    {
        set_time_limit(0);
        $a = CouponMap::where('ex_id','')->where('ex_code','')->orderBy('id','asc')->first()->id;
//        var_dump($a);die;
        if(!$a){
            echo "找不到最小值了\n";
            return ;
        }
        $start = $a ;
        $end = $start + 500 ;
        echo "start\n";
        $num = 1 ;
        $allNum = 100000;
        for($i=$start;$i<=$end;$i++){
            echo "id start ".$i;
            if($num>$allNum){
                echo "info有为空的注意哦\n";
                break;
            }
            $couponmap = CouponMap::whereBetween('id',[$start,$end])->where('ex_id','')->where('ex_code','')->get();
            if(!$couponmap){
                echo "正常执行完毕\n";
                break;
            }
            foreach ($couponmap as $item){
                $info = json_decode($item->info,true);
                $item->ex_id = $info['ex_id'] ?? '';
                $item->ex_code = $info['ex_code'] ?? '';
                $item->save();
                echo "save success".$item->id."\n";
            }
            $start = $end+1;
            $end = $start+500;
            $num++;
            usleep(500);
        }
        echo "end \n";
    }


}
