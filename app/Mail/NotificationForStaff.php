<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotificationForStaff extends Mailable
{
    use Queueable, SerializesModels;

    public $tpl = [];

    public function __construct($tpl)
    {
        $this->tpl = $tpl;
    }

    public function build()
    {

        return $this->subject('Thông báo từ Texo.vn')->view('mail.notification', $this->tpl);
    }
}
