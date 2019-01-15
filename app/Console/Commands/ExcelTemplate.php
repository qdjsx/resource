<?php

namespace App\Console\Commands;

use App\Models\TmpCouponMap;
use App\Models\FluxAnnex;
//use App\Models\RefinenessRecommend;
use Illuminate\Console\Command;
use Excel;

class ExcelTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:excel_template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '线下处理excel导入的代码';

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
        $filePath = storage_path('566.xlsx'); //放到storage里面 ，就直接导入到库里
        Excel::load($filePath, function ($reader) {
            $reader = $reader->all()->toArray();
            $data = array();
            foreach ($reader as $v) {
                $data[]= array(
                    'ex_id'=>$v['id'],
                    'ex_code'=>$v['code'],
                    'bid'=>84
                );
            }
            TmpCouponMap::insert($data);
        });
    }
}
