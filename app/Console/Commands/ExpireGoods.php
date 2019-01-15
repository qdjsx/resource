<?php

namespace App\Console\Commands;

use App\Models\GoodsRecommend;
use App\Models\RefinenessRecommend;
use Illuminate\Console\Command;

class ExpireGoods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:expire_goods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理过期的商品';

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
        $items = GoodsRecommend::where('is_deleted', GoodsRecommend::UN_DELETED)->where('status', GoodsRecommend::OPEN)
            ->with(['goods'])->get();
        $time = strtotime(date('Y-m-d H:i:s'));
        if ($items) {
            foreach ($items as $item) {
                if (strtotime($item->goods->start_date) <= $time && $time <= strtotime($item->goods->end_date)) continue;
                $item->status = GoodsRecommend::CLOSE;
                $item->is_deleted = GoodsRecommend::DELETED;
                $item->save();
            }
        }
        $items = RefinenessRecommend::where('status',RefinenessRecommend::OPEN)->where('is_deleted',RefinenessRecommend::UN_DELETED)
            ->with(['goods'])->get();
        if ($items) {
            foreach ($items as $item) {
                if (strtotime($item->goods->start_date) <= $time && $time <= strtotime($item->goods->end_date)) continue;
                $item->status = RefinenessRecommend::CLOSE;
                $item->is_deleted = RefinenessRecommend::DELETED;
                $item->save();
            }
        }
    }
}
