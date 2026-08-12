<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Cruncher\DefaultDriver;
use App\Log;
use App\Traits\CronLogging;
use Illuminate\Console\Command;

class CruncherDownload extends Command
{
    use CronLogging;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CruncherDownload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'CruncherDownload';

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
        /** @var DefaultDriver $cruncher */
        $cruncher = resolve(DefaultDriver::class);
        $attachments = Attachment::whereNull('plain_text')
            ->where('created_at', '<=', dateTimeNow('-5 minutes'))
            ->whereNotNull('cruncher_id')
            ->get();
        $bar = $this->output->createProgressBar($attachments->count());
        $attachments->each(function (Attachment $attachment) use ($bar, $cruncher) {
            $bar->advance();
            try {
                $text = $cruncher->get($attachment->cruncher_id)->text;
            } catch (\Exception $e) {
                Log::debug("Cruncher get error " . $e->getMessage());
                return;
            }
            if ($text) {
                $attachment->update([
                    'plain_text' => $text
                ]);
                $attachment->processText();
            }
        });
        $bar->finish();
        $this->logEnd();
    }
}
