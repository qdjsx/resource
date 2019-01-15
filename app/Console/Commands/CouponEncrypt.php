<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Navigation;
use App\Util\EncryptTool;
use Illuminate\Console\Command;

class CouponEncrypt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:coupon_encrypt_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对代金券的详情页中的id进行加密';

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
        $list = Activity::all();
        foreach ($list as $item) {
            $value= $item->landing_page;
            $index = strpos($value,'coupon/get/');
            if ($index !== false) {
                $id = substr($value,$index+ strlen('coupon/get/'));
                if(strlen($id) > 40) continue;
                $id = EncryptTool::encrypt($id);
                var_dump($value);
                $value  = substr($value,0,$index + strlen('coupon/get/')) .$id;
                $item->landing_page = $value;
                $item->save();
            }
        }

        /*$list = Navigation::all();
        foreach ($list as $item) {
            $value= $item->landing_page;
            $index = strpos($value,'coupon/get/');
            if ($index !== false) {
                var_dump($value);die;
                $id = substr($value,$index+ strlen('coupon/get/'));
                $id = EncryptTool::encrypt($id);
                $value  = substr($value,0,$index + strlen('coupon/get/')) .$id;
                $item->landing_page = $value;
                $item->save();
            }
        }*/

    }
}
