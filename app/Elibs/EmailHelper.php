<?php


namespace App\Elibs;

use App\Mail\MailBase;
use Illuminate\Support\Facades\Mail;

class EmailHelper
{
    static function sendMail($to, $tpl)
    {
        Mail::to($to)->send(new MailBase($tpl));
    }

    /**
     * @param $staff
     * @param $new_pass
     * Khi mật khẩu của nhân viên được thay đổi, nhân viên này sẽ nhận được thông báo và kèm theo mật khẩu mới
     * phần này không tự động mà được tick bởi người thay đổi mật khẩu (vì trong 1 số trường hợp thay đổi mật khẩu để khóa tài khoản)
     */
    static function sendMailAlertPasswordChange($staff,$new_pass){
        if(!$staff['email']){
            return false;
        }
        if(!isset($staff['account'])){
            return false;
        }
        $tpl['name'] = $staff['name'];
        $tpl['obj'] = $staff;
        $tpl['new_password'] = $new_pass;
        $tpl['subject'] = 'Thông báo mật khẩu thay đổi';
        $tpl['template'] = 'mail.change_password';
        EmailHelper::sendMail($staff['email'],$tpl);
    }
}