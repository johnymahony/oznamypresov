<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Log;
use App\Mail\GeneralMailable;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $recepient;

    /**
     *
     * @var GeneralMailable
     */
    private $mailable;

    public $user;
    public $messageId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($recepient, GeneralMailable $mailable, $messageId)
    {
        $this->recepient = $recepient;
        $this->mailable = $mailable;
        $this->messageId = $messageId;
        try {
            $this->user = user();
        } catch (\Exception $e) {
            $this->user = 'unknown';
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->job) {
            Log::debug('sendEmail start ' . $this->job->getJobId() . ' job done');
        }
        try {
            setUser($this->user == '' ? 'unknown' : $this->user);
            if (config('app.sendEmail', 'Y') == 'Y') {
                Log::debug('real email:' . $this->recepient);
                Mail::to($this->recepient)->send($this->mailable);
            } else {
                Log::debug('fake email: ' . $this->recepient);
            }
        } catch (\Exception $e) {
            Log::debug('Ex in email job: ' . $e->getMessage());
            throw $e;
        }
        if ($this->job) {
            Log::debug('sendEmail end ' . $this->job->getJobId() . ' job done');
        }
    }

    public function getRecepient()
    {
        return $this->recepient;
    }

    public function getSubject()
    {
        return $this->mailable->getSubject();
    }

    public function getMessageId()
    {
        return $this->messageId;
    }
}
