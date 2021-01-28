<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthActive extends Mailable
{
    use Queueable, SerializesModels;

    public $tpl = [];

    public function __construct($tpl)
    {
        $this->tpl = $tpl;
    }

    public function build()
    {
        return $this->subject('Xác thực thông tin tài khoản')->view('mail.auth.active', $this->tpl);    }
}
