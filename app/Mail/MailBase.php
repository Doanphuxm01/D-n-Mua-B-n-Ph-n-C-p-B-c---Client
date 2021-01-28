<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailBase extends Mailable
{
    use Queueable, SerializesModels;

    public $tpl = [];

    public function __construct($tpl)
    {
        $this->tpl = $tpl;
    }

    public function build()
    {
        $subject = isset($this->tpl['subject'])?$this->tpl['subject']:'Thông báo từ hệ thống';
        $tempate = isset($this->tpl['template'])?$this->tpl['template']:'mail.notification';
        return $this->subject($subject)->view($tempate, $this->tpl);
    }
}
