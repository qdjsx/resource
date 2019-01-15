<?php

namespace App\Console\Commands;

use App\Models\DirectChargeSkus;
use App\Models\DirectChargeSkusMultiple;
//use App\Models\RefinenessRecommend;
use Illuminate\Console\Command;

class InitializationData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:initialization_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '用于初始化直冲倍数商品数据';

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
        header('Content-Type:text/plain;charset=utf-8');
        set_time_limit(0);
        $needHandle = DirectChargeSkus::where('buy_multiple',1)->get();
        foreach ($needHandle as $sku_idItem){
            echo '开始处理skus表' .$sku_idItem->id . "\r\n";
            $multiple = DirectChargeSkusMultiple::where('sku_id',$sku_idItem->id)->get();
            foreach ($multiple as $item){
                $num = $item->num;
                echo  'Multiple表'. $item->id .'处理'."\r\n";
                $item->original_price = number_format($sku_idItem->original_price * $num,2,'.','');
                $item->red_packet = number_format($sku_idItem->red_packet * $num,2,'.','');
                $item->cash = number_format($sku_idItem->cash * $num,2,'.','');
                $item->save();
                echo  'Multiple表'. $item->id .'处理成功'."\r\n";
            }
        }
    }
}
