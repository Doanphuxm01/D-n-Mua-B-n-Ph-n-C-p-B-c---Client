<?php
namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Elibs\Pager;

require_once app_path('Elibs/simple_html_dom.php');

class crm extends Controller
{
    //link  = _crawl/crm/_check;
    function _check(){
        echo "ngannv";
    }
}
