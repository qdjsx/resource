<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class CouponInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:coupon_info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代金券info复制有问题，进行粘贴';

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
        $items = Coupon::where('is_deleted',-1)->get();
        foreach($items as $item) {
            $info = $item->info;
            //  src="http://admin.qiandao.wasair.com/
            if (strpos($info, 'src="http://admin.qiandao.wasair.com/') !== false) {
                $info = str_replace("http://admin.qiandao.wasair.com/",  '/', $info);
                $item->info = $info;
                $item->save();
            }
        }
    }
}
