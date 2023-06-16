<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Services\SendMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class SaveHistoryMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function() {
            $formats = SendMessageService::messageFormatFromArray($this->campaign);

            info('SaveHistoryMessageJob: ' . $this->campaign->id);

            if (!$formats['status']) {
                $this->campaign->update([
                    'executed_at' => now(),
                    'is_processing' => false,
                    'description' => $formats['message']
                ]);
                return 0;
            }

            foreach ($formats['data'] as $items) {
                foreach ($items as $key => $item) {
                    $this->campaign->messages()->create([
                        'user_id' => $this->campaign->user_id,
                        'receiver' => $item['receiver'],
                        'sender' => $item['sender'],
                        'body' => json_encode($item['data']),
                        'type' => $item['type'],
                        'point' => 0,
                        'status' => 'pending',
                    ]);
                }
            }
        }, 3);
    }
}
