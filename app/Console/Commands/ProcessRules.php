<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Parcel;
use App\Process\BoardProcessor;
use Illuminate\Console\Command;

class ProcessRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessRules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $boards = Board::
        where('status', '!=', Board::STATUS_DONE)
            ->get();

        $p = $this->output->createProgressBar($boards->count());
        $processor = new BoardProcessor();
        foreach ($boards as $board) {
            try {
                $processor->process($board);
                $p->advance();
            } catch (\Exception $e) {
                echo "\nProblem {$board->id} $e\n";
            }
        }
        $p->finish();
    }
}
