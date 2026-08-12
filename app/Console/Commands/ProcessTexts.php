<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Parcel;
use Illuminate\Console\Command;

class ProcessTexts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ProcessTexts';

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
        with('attachments')
            ->join('attachments as a', 'a.board_id', 'boards.id')
            ->where('status', '!=', Board::STATUS_DONE)
            ->whereNotNull('a.plain_text')
            ->get();

        $p = $this->output->createProgressBar($boards->count());
        foreach ($boards as $board) {

            $hasFoundParcel = $board->parcels->filter(function (Parcel $parcel) {
                return $parcel->points();
            });

            if ($hasFoundParcel) {
                continue;
            }
            try {
                /** @var Board $board */
                $board->parcels()->delete();
                /** @var Attachment $attachment */
                $attachment = $board->attachments()->first();
                if (empty($attachment)) {

                } else {
                    $attachment->processText();
                }
                if (city()->isCategoryForWholeCity($board->category->name) || city()->isTitleForWholeCity($board->title)) {
                    $board->setWholeCity();
                }
                $p->advance();
            } catch (\Exception $e) {
                echo "\nProblem {$board->id} $e\n";
            }
        }
        $p->finish();
    }
}
