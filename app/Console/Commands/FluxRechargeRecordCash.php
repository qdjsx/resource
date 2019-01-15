<?php

namespace App\Console\Commands;

use App\Models\Flux;
use App\Models\FluxRechargeRecord;
use Illuminate\Console\Command;

class FluxRechargeRecordCash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:flux_recharge_record_cash';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化流量兑换表flux_id';

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
        $i = 0;
        while (true) {
            $fluxRechargeAll = FluxRechargeRecord::where('flux_id', 0)->with(['orderInfoPay.orderInfo.orderGoods.flux' => function ($query) {
                $query->select('flux.*');
            }])->skip($i * 50)->take(50)->get();
            if ($fluxRechargeAll->isEmpty()) {
                echo '已更新完毕', PHP_EOL;
                break;
            }
            foreach ($fluxRechargeAll as $data) {
                $serviceProviderId = $data['service_provider_id'] ?? 0;
                $num = $data['orderInfoPay']['orderInfo']['orderGoods']['flux']['num'] ?? 0;
                $unit = $data['orderInfoPay']['orderInfo']['orderGoods']['flux']['unit'] ?? 0;
                $range = $data['orderInfoPay']['orderInfo']['orderGoods']['flux']['range'] ?? 0;
                $operator = $data['orderInfoPay']['orderInfo']['orderGoods']['flux']['operator'] ?? 0;
                $area = $data['orderInfoPay']['orderInfo']['orderGoods']['flux']['area'] ?? '';
                $dataArr = [
                    'service_provider_id' => $serviceProviderId,
                    'num' => $num,
                    'unit' => $unit,
                    'range' => $range,
                    'operator' => $operator,
                    'area' => $area,
                ];
                $flux = Flux::where($dataArr)->first();
                if (!$flux) continue;
                $data->flux_id = $flux['id'];
                if ($data->cash == 0 && isset($flux['procurement_price'])) {
                    $data->cash = $flux['procurement_price'] ?? 0;
                    $data->save();
                }
                $i++;
                echo '第', $i, '次', '循环', PHP_EOL;
            }
        }
    }
}