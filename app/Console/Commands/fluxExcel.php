<?php

namespace App\Console\Commands;

use App\Models\Flux;
use App\Models\FluxAnnex;
//use App\Models\RefinenessRecommend;
use Illuminate\Console\Command;
use Excel;

class fluxExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:flux_excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '流量表格导入';

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
        $filePath = storage_path('360.xlsx');
        Excel::load($filePath, function ($reader) {
            $reader = $reader->all()->toArray();
            foreach ($reader as $v) {
                $sql = 'insert into ad_summary_vendor_report (ad_id,vendor_id,date,impression,click,consume) value('.$v['adid'].',14,'."'".$v['date']."'".','.$v['pv'].','.$v['click'].','.$v['consume'].')';
                echo $sql,";\r\n";
            }
        });
    }
}
