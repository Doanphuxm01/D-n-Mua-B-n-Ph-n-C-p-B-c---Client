<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuthForgotPass extends Mailable
{
    use Queueable, SerializesModels;

    public $tpl = [];

    public function __construct($tpl)
    {
        $this->tpl = $tpl;
    }

    public function build()
    {
        return $this->subject('Lấy lại mật khẩu đăng nhập')->view('mail.auth.forgotpass', $this->tpl);
    }
}
