<?php

namespace App\Console\Commands;

use App\Models\AdminChangeData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class AdminChangeDataScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:admin_change_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新admin_change_data表更新前和更新后数据';

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
        $items = AdminChangeData::where('is_deal',0)->get();
        foreach ($items as $item){
            if($item->before == 'null' || empty($item->before)){
                $item->is_deal = 1;
                $item->save();
                continue;
            }
            if($item->after == 'null' || empty($item->after)) continue;
            $itemArr = json_decode($item->before,true);
            $itemAfterArr = json_decode($item->after,true);
            $before = json_encode(array_diff_assoc($itemArr,$itemAfterArr));
            $after = json_encode(array_diff_assoc($itemAfterArr,$itemArr));
            $item->before = $before;
            $item->after = $after;
            $item->is_deal = 1;
            $item->save();
        }
    }


}
