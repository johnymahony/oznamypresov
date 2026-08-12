<?php
namespace App\Traits;

use App\Mail\MailSender;

trait Emailable
{

    public function sendEmail($template, $vars = [], $attachments = [], $sender = null)
    {
        (new MailSender($this->resolveEmail(), $template, $this, $vars, $attachments, $sender))->queue();
    }

    public function resolveEmail()
    {
        return $this->email;
    }
}