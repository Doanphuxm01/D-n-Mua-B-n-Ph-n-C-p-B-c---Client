<?php


namespace App\Http\Controllers\KaynHome;


use App\Elibs\eView;
use App\Http\Controllers\Controller;

use SEOMeta;
use OpenGraph;
use Twitter;
class KaynHome extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->home();
        }
    }

    public function home($action = '')
    {
        $tpl = [];
        $seo_title = 'Kayn\'s Office';
        $seo_des = 'Chào mừng bạn đến với Kayn\'s Office';

        return eView::getInstance()->setView(__DIR__, 'index', $tpl);
    }
}