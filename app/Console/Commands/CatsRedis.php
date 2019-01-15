<?php

namespace App\Console\Commands;

use App\Models\Cats;
//use App\Models\CouponMap;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CatsRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:cats_redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '淘宝分类缓存';

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
        set_time_limit(0);
        $items = Cats::where('is_parent',1)->get();
        foreach ($items as $item){
            $child = Cats::where(['parent_cid'=>$item->cid])->get();
            $data = array();
            foreach ($child as $v) {
                $data[] = [
                    'cid'=> $v->cid,
                    'name'=>$v->name,
                    'is_parent'=>$v->is_parent,
                ];
            }
            Redis::hset('h_admin_qiandao_oldcats',$item->cid,json_encode($data));
        }
        Redis::rename('h_admin_qiandao_oldcats','h_admin_qiandao_cats');
    }
}
