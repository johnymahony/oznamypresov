<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Board;
use App\Problem;
use App\Traits\CronLogging;
use Illuminate\Console\Command;

class Ocr extends Command
{
    use CronLogging;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Ocr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ocr';

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
        $this->logStart();

        $attachments = Attachment::
        select('attachments.*')
            ->withoutText()
            ->join('boards as b', 'b.id', 'attachments.board_id')
            ->where('attachments.path', 'like', '%pdf%')
            ->orderBy('attachments.created_at', 'desc')->limit(100)->get();
        $bar = $this->output->createProgressBar($attachments->count());
                $problemText = "UploadToCruncher exception";
        foreach ($attachments as $attachment) {
            /** @var Attachment $attachment */
            try {
                $attachment->uploadForOcr();
                Problem::where([
                    'text' => $problemText,
                    'board_id' => $attachment->board_id
                ])->delete();
            } catch (\Exception $e) {
                $title = intval($attachment->title) + 1;
                $attachment->update(['title' => $title]);

                $board = $attachment->board;
                if ($title === 3 && $board->status === Board::STATUS_NEW) {
                    Problem::make($problemText, $attachment->board_id, $e . '');
                    $board->status = Board::STATUS_READY;
                    $board->comment = 'OCR zlyhalo, treba to vyriesiť ručne.';
                    $board->save();
                }

            }
            $bar->advance();
        }
        $bar->finish();
        $this->logEnd();
    }
}
