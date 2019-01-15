<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\GoldRecord;
use Illuminate\Console\Command;
use Excel;
use DB;

class UserExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:user_excel {--params=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '给这些用户加红包';

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
        $params = $this->option('params');
        $date = '20181229';
        if ($params && $params== 'excel') self::loadExcel($date);
        if  ($params && $params== 'add') self::addGold($date);
        if  ($params && $params== 'userNot') self::userNotEXits($date);
    }

    /**
     * 取数据
     */
    public  static  function loadExcel($date){
        $redis = app('redis')->connection('default');
        $key = 'user_add_gold_'.$date;
        $filePath = storage_path('20181229usergold.xlsx');
        Excel::load($filePath, function ($reader)use($redis,$key) {
            $reader = $reader->all()->toArray();
            foreach ($reader as $v) {
                $user = trim($v['user']);  //用户id减去1w是数据库id
                if(!is_numeric($user) || strpos($user, '.')) continue;
                $user = $user - 10000;
                if($redis->hset($key,$user,1)){  //去重
                    $data[] = $user;
                    echo $user ."\r\n";
                }
            }
            $redis->expire($key,360000); //100天
            $redis->setex($key.'_user_array',360000,json_encode($data));
            echo count($data) ,"存储用户id结束";
        });

    }


    public  static  function addGold($date){
        $redis = app('redis')->connection('default');
        $key = 'user_add_gold_'.$date.'_user_array';
        $userArr = json_decode($redis->get($key),true);
        if(!$userArr) return;
        $m = 3;
        $time = date('Y-m-d H:i:s');
        $haxiKey = $key.'_is_ok';
        foreach ($userArr as $user_id){
            $user = User::where('id',$user_id)->first();
            if(!$user) continue;
                ///开启事务
                try{
                    DB::beginTransaction();
                    \DB::table('user')->where('id',$user_id)->increment('gold', $m);
                    $addGold = [
                        'user_id'=>$user_id,
                        'amount'=>$m,
                        'create_time'=>$time,
                        'channel_id'=>$user->channel_id,
                        'ref_id'=>0,
                        'type'=>'active_order_back_gold',
                        'remark'=>'20181229问卷调查3元',
                    ];
                    GoldRecord::insert($addGold);
                    echo  $user_id ."\r\n";
                    if($redis->hset($haxiKey,$user_id,1)) {  //去重
                        DB::commit();
                    }else{
                        DB::rollBack();
                    }
                }catch(\Exception $e) {
                    DB::rollBack();
                }
        }
        $redis->expire($haxiKey,360000); //100天
    }

    /**
     * 不存在的用户
     */
    public  static  function userNotEXits($date){
        $redis = app('redis')->connection('default');
        $key = 'user_add_gold_'.$date.'_user_array';
        $userArr = json_decode($redis->get($key),true);
        $haxiKey = $key.'_is_ok';
        $userIsSetArr = $redis->hkeys($haxiKey);
        $arr = array_diff($userArr,$userIsSetArr);
        echo count($arr),"\r\n";
//        $redis->setex($date.'user_gold_not_give',360000,json_encode($arr));
        foreach ($arr as $u){
            $logFile = fopen(
                storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_user_not_give.log'),
                'a+'
            );
            $userid = $u + 10000;
            fwrite($logFile, $userid."\r\n");
            fclose($logFile);
        }

    }
}
