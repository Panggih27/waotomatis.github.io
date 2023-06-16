<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Cost;
use App\Models\Message;
use App\Models\Point;
use App\Services\SendMessageService;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SendMessageJob implements ShouldQueue
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

        DB::beginTransaction();
        try {
            $messages = $this->campaign->messages()->where('status', 'pending')->orWhere('status', 'failed')->get();
            $getCost = Cost::all();

            info('SendMessageJob: ' . $this->campaign->id);

            foreach ($messages as $key => $item) {
                $data = [
                    'receiver' => $item->receiver,
                    'sender' => $item->sender,
                    'body' => json_decode($item->body),
                    'type' => $item->type,
                ];

                // $available = (Carbon::parse($this->campaign->number->start_time)->lt(Carbon::now()) && Carbon::parse($this->campaign->number->end_time)->gt(Carbon::now()));

                // if (! $available || ! $this->campaign->is_active) {
                //     return 0;
                // }

                $send = Http::asForm()->post(env('WA_URL_SERVER') . '/send-message-by-job', $data);

                $result = json_decode($send);
                if (isset($result->status) && $result->status) {
                    $point = $getCost->where('slug', $item->type)->first()->point ?? env('DEFAULT_POINT');
                    $item->update([
                        'status' => 'success',
                        'executed_at' => now(),
                        'point' => $point
                    ]);
                } else {
                    $item->update([
                        'status' => 'failed',
                        'status_description' => $result->msg ?? 'Something Went Wrong',
                        'executed_at' => now(),
                    ]);
                }

                if (str_contains($this->campaign->number->delay, '-')) {
                    $delay = explode('-', $this->campaign->number->delay);
                    $delay = rand($delay[0], $delay[1]);
                } else {
                    $delay = $this->campaign->number->delay;
                }

                sleep($delay);
            }

            $cost = Message::whereHasMorph('messageable', Campaign::class, function ($query) {
                $query->where('id', $this->campaign->id);
            })->where('status', 'success')->sum('point');

            $schedule = $this->campaign->is_manual ? 'manual' : (!is_null($this->campaign->schedule) ? 'schedule' : 'now');

            $broadcast_point = $getCost->where('slug', $schedule)->first()->point ?? env('DEFAULT_POINT');

            $cost = $cost + $broadcast_point;
            Point::where('user_id', $this->campaign->user_id)->decrement('point', $cost);

            $this->campaign->update([
                'executed_at' => now(),
                'is_processing' => 0,
                'description' => 'successful',
                'point' => $cost,
                'broadcast_point' => $broadcast_point,
            ]);

            $this->campaign->history()->create([
                'user_id' => $this->campaign->user_id,
                'type' => '-',
                'point' => $cost,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            info('SendMessageJob: ' . $e->getMessage());
            DB::transaction(function () use($e) {
                $this->campaign->update([
                    'executed_at' => now(),
                    'is_processing' => 0,
                    'description' => $e->getMessage(),
                ]);
            });
        }
    }
}
