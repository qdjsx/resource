<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\UserSign;

class dealConitueDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deal:continueDay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '处理连续签到天数';

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
        var_dump(1);
die;
        $items = User::where('continue_sign_day',0)->get();
        if ($items) {
            foreach ($items as $item) {
                $userSign = UserSign::where('user_id',$item->id)
                    ->where('sign_time','>=',date('Y-m-d').' 00:00:00')->first();
                if (!$userSign) continue;
                $item->continue_sign_day = 1;
                $item->save();
            }
        }
    }
}
