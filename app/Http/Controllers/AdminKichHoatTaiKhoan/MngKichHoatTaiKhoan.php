<?php


namespace App\Http\Controllers\AdminKichHoatTaiKhoan;


use App\Elibs\eView;
use App\Elibs\HtmlHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MngKichHoatTaiKhoan extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->input();
        }
    }

    function input() {
        $tpl = [];
        HtmlHelper::getInstance()->setTitle('Yêu cầu chuyển điểm');
        $id = Request::capture()->input('id', 0);

        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }
}