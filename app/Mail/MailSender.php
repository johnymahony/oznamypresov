<?php
namespace App\Mail;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendEmail;
use App\Message;

class MailSender
{

    private $recepient;

    /**
     *
     * @var Message
     */
    private $message;

    private $object;

    private $text;

    private $subject;

    private $attachments;

    private $fromAddress;

    public function __construct($recepient, $template, $object, $vars = [], $attachments = [], $sender = null)
    {
        $vars['object'] = $object;
        $vars['blade'] = $template;
        $this->recepient = $recepient;
        $this->object = $object;
        $this->attachments = $attachments;
        $this->fromAddress = $sender ? $sender : config('mail')['from']['address'];
        if ($this->deleted($recepient)) {
            return;
        }
        $this->text = (string)view('email.' . $template)->with($vars)->render();
        $this->subject = (string)view('email.subject.' . $template)->with($vars)->render();
        $this->store();
    }

    private function deleted($recepient)
    {
        if (empty($recepient)) {
            throw new \Exception("Empty recepient in Mailsender::deleted()");
        }
        return DB::select("SELECT id FROM cityzens WHERE email = ? AND deleted = 1", [$recepient]);
    }

    private function store()
    {
        $data = [
            'recepient' => $this->recepient,
            'subject' => $this->subject,
            'text' => $this->text,
            'from' => $this->fromAddress,
            'from_name' => config('mail')['from']['name'],
            'type' => 'email',
            'cityzen_id' => $this->object ? $this->object->id : null
        ];

        $this->message = Message::create($data);
    }

    public static function sendRaw($recepient, $text, $subject)
    {
        $data = [
            'recepient' => $recepient,
            'subject' => $subject,
            'text' => $text,
            'from' => config('mail')['from']['address'],
            'from_name' => config('mail')['from']['name'],
            'type' => 'email'
        ];

        $message = Message::create($data);
        $ref = new \ReflectionClass('\App\Mail\MailSender');
        $sender = $ref->newInstanceWithoutConstructor();
        $sender->queueRaw($recepient, $text, $subject, $message->id);
        return $message;
    }

    public function queue()
    {
        if ($this->deleted($this->recepient)) {
            return;
        }
        $this->message->status = 'Q';
        $this->message->save();
        $job = $this->queueRaw($this->recepient, $this->text, $this->subject, $this->message->id);
        $this->message->job_id = $job;
        $this->message->save();
    }

    public function queueRaw($recepient, $text, $subject, $messageId)
    {
        if ($this->deleted($recepient)) {
            return;
        }
        return dispatchJob(new SendEmail($recepient, new GeneralMailable($text, $subject, $this->attachments, $this->object), $messageId), 1);
    }

    public function send()
    {
        if ($this->deleted($this->recepient)) {
            return;
        }
        Mail::to($this->recepient)->send(new GeneralMailable($this->text, $this->subject));
        $this->message->update([
            'status' => 'S',
            'sent_date' => date('Y-m-d H:i:s')
        ]);
    }
}
