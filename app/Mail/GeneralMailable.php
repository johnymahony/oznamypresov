<?php
namespace App\Mail;

use App\Attachment;
use App\Pdf\TemplateWithParams;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class GeneralMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $textx;

    public $subjectx;

    public $attach;

    public $object;


    public function __construct($text, $subject, $attachments = null, $object = null)
    {
        if (!empty($attachments)) {
            $object->validateAttachments($attachments);
        }
        $this->textx = $text;
        $this->subjectx = $subject;
        $this->attach = $attachments;
        $this->object = $object;
    }

    public function getSubject()
    {
        return $this->subjectx;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $view = $this->view('text')->with('text', $this->textx)->subject($this->subjectx);
        if (!empty($this->attach)) {
            foreach ($this->attach as $type => $label) {
                if (is_object($label)) {
                    $attachment = Attachment::find($label->generate($this->object));
                } else {
                    $attachment = $this->object->getAttachment($type);
                }

                $view->attachData($attachment->getContent(), $attachment->type, ['mime' => $attachment->mime]);
            }
        }
        return $view;
    }
}
