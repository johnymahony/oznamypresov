<?php

namespace App\Console\Commands;

use App\Board;
use App\Cityzen;
use App\CityzenBoard;
use App\Log;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SendEmails';

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
        $cityzens = Cityzen::where('receive_type',
            Cityzen::DAILY)->whereNotNull('email')
            ->orderBy('id')
            ->get();
        $limit = 0;
        Log::debug("SendEmailsLimit $limit");
        foreach ($cityzens as $cityzen) {
            /** @var Collection $boards */
            $boards = Board::select('boards.*', 'cb.reason')
                ->join('cityzen_boards as cb', 'cb.board_id', 'boards.id')
                ->where(['cb.cityzen_id' => $cityzen->id])
                ->where('cb.date_sent', '0000-00-00 00:00:00')->get();
            if ($boards->count() === 0) {
                continue;
            }
            $boards = $boards->sort(function ($a, $b) {
                return strtotime($a->ready_at) <=> strtotime($b->ready_at);
            });

            $oldestDays = $boards->first()->ready_at->diffInDays(Carbon::now());
            if (($oldestDays >= $cityzen->day_emails || $boards->count() >= $cityzen->min_emails) && $limit < config('app.send_hour_limit',
                    300)) {
                Log::debug("SendEmailsLimit $limit");
                /** @var Cityzen $cityzen */
                $cityzen->sendEmail('daily', [
                    'boards' => $boards
                ]);
                CityzenBoard::where([
                    'cityzen_id' => $cityzen->id,
                    'date_sent' => '0000-00-00 00:00:00',
                ])->update(['date_sent' => Carbon::now()]);
                $limit++;
            }
        }
    }
}
