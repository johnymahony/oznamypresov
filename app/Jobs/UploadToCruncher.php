<?php

namespace App\Jobs;

use App\Attachment;
use App\Board;
use App\Cruncher\Cruncher;
use App\Cruncher\DefaultDriver;
use App\Documents\Version;
use App\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadToCruncher implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var \App\Attachment */
    public $attachment;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Attachment $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        /** @var DefaultDriver $cruncher */
        $cruncher = resolve(DefaultDriver::class);
//        if ( ! in_array($this->attachment->file->extension(), ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'tif'])) {
//            return;
//        }
        try {
            $this->attachment->update([
                'cruncher_id' => $cruncher->add($this->attachment),
            ]);
        } catch (\Exception $e) {
            Log::debug("Cruncher exception: " . $e->getMessage());
//            if (preg_match("/was empty/", $e->getMessage())) {
//                return;
//            }
            throw $e;
        }


    }
}
