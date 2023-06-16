<?php

namespace App\Console\Commands;

use App\Models\Inbox;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inbox:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete all inboxes from a week ago';

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
        Inbox::where('created_at', '<=', Carbon::now()->subDays(7))->delete();
        return 0;
    }
}
