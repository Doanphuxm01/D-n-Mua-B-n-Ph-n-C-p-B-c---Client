<?php

namespace App\Http\Controllers\FrontEnd\FeRegistration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeRegistration extends Controller
{
    public function rgegistration(){
        HtmlHelper::getInstance()->setTitle('Đăng ký thành viên Minh Phúc Group');
        $tpl = [];
        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }
}
