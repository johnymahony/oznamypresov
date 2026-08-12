<?php

namespace App\Console\Commands;

use App\Attachment;
use App\Helpers\CadasterNumber;
use Illuminate\Console\Command;

class FindCadasters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FindCadasters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FindCadasters';

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
        $attachments = Attachment::with('board')->withText()->get();
        $bar = $this->output->createProgressBar($attachments->count());


        $attachments->each(function (Attachment $attachment) use ($bar) {
            resolve(CadasterNumber::class)->resolve($attachment->plain_text);
            resolve(CadasterNumber::class)->resolve($attachment->board->description);
            $bar->advance();
        });
        $bar->finish();
    }
}
