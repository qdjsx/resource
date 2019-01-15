<?php

namespace App\Console\Commands;

use App\Models\CouponMap;
use App\Models\Flux;
use App\Models\FluxAnnex;
//use App\Models\RefinenessRecommend;
use App\Models\HUHUGoodsCouponCode;
use Illuminate\Console\Command;
use Excel;

class DealCouponMapMove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:coupon_map_move';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '代金券库存表，洗move数据';

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
        $result = HUHUGoodsCouponCode::where('move', 0)->get();
        foreach ($result as $re) {
            $lanren = CouponMap::onWriteConnection()->where('id', $re->code_id)->where('status', 1)->first();
            if ($lanren) {
                $lanren->move = 1;
                $lanren->save();
                echo $re->code_id . '成功1'."\r\n";

                $log = fopen(
                    storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_coupon_map_id_ok.log'),
                    'a+'
                );
                $wr = $re->code_id;
                fwrite($log, $wr."\r\n");
                fclose($log);

            } else {
                echo $re->code_id. '失败0'."\r\n";
                $logFile = fopen(
                    storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_coupon_map_id_pro.log'),
                    'a+'
                );
                $w = $re->code_id;
                fwrite($logFile, $w."\r\n");
                fclose($logFile);


            }
        }
    }
}
