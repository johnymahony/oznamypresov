<?php

namespace App\Console\Commands;

use App\Board;
use App\Cityzen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WeeklySender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WeeklySender';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WeeklySender';

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
        $cityzens = Cityzen::where('receive_type', Cityzen::WEEKLY)->whereNotNull('email')->get();
        $sent = [];
        foreach ($cityzens as $cityzen) {
            $boards = Board::select('boards.*')->forCityzen($cityzen, 'sent_weekly')
                ->get();
            $cityzen->sendEmail('daily', [
                'boards' => $boards
            ]);
            $sent[] = $boards->pluck('id');
        }
        $sent = collect($sent);
        Board::whereIn('id', $sent->collapse()->toArray())->update(['sent_weekly' => true]);
    }
}
