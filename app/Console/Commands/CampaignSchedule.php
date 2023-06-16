<?php

namespace App\Console\Commands;

use App\Models\Campaign;
use App\Services\SendMessageService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CampaignSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending Campaign by Schedule';

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
     * @return int
     */
    public function handle()
    {
        Campaign::whereNotNull('schedule')->whereNull('executed_at')->where(function ($t) {
            $t->whereNull('is_manual')->orWhere('is_manual', 0);
        })->where('schedule', '<=', Carbon::now())->where('is_processing', false)->whereHas('number', function($q) {
            $q->where('is_active',1)->where('status', 'Connected');
        })->oldest()->chunk(3, function ($campaigns) {
            foreach ($campaigns as $campaign) {
                SendMessageService::send($campaign);
            }
        });
        return 0;
    }
}
