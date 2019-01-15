<?php

namespace App\Console\Commands;

use App\Models\Coupon;
use Illuminate\Console\Command;

class CouponOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:coupon_overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代金券过期处理';

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
        $items = Coupon::where('status',1)->where('is_deleted',-1)->get();
        foreach($items as $item) {
            if($item->valid_date  < date('Y-m-d')){
                $item->status = -1;
                $item->save();
            }
        }
    }
}
