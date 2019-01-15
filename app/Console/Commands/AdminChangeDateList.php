<?php

namespace App\Console\Commands;

use App\Models\AdminChangeData;
use App\Util\ArrayDifference;
use Illuminate\Console\Command;

class AdminChangeDateList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:admin_change_data_list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将更改前更改后的数据放入队列中';

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
        //取出队列中的数据
        $redis           = app('redis')->connection('channel_user');
        while($redis->llen('data_change_data')!=0)
        {
            $data = $redis->rpop('data_change_data');
            $changeData = json_decode($data, true);
            if (!empty($changeData))
            {
                $before = $changeData['before']['before'] ?? '';
                $after  = $changeData['after'] ?? '';
                $before = json_decode($before, true);
                $after  = json_decode($after, true);
                if (!empty($before) || !empty($after))
                {
                    unset($before['updated_at']);
                    unset($after['updated_at']);

                }
                $differenceData = array_diff_assoc($after,$before);
//                $differenceData = ArrayDifference::difference($before, $after);
                if (!empty($differenceData))
                {
                    $after['difference'] = $differenceData;
                    $afterData = json_encode($after);
                    $AdminChangeData = new AdminChangeData;
                    $AdminChangeData->ad_uid     = $changeData['before']['ad_uid'] ?? 0;
                    $AdminChangeData->table_name = $changeData['before']['table_name'] ?? '';
                    $AdminChangeData->key_id     = $changeData['before']['key_id'] ?? 0;
                    $AdminChangeData->type       = $changeData['before']['type'] ?? 0;
                    $AdminChangeData->before     = $changeData['before']['before'] ?? '';
                    $AdminChangeData->created_at = date('Y-m-d H:i:s');
                    $AdminChangeData->after      = isset($afterData) ? $afterData:(isset($changeData['after'])?$changeData['after']:'');
                    $AdminChangeData->save();
                }
            }
        }
    }
}
