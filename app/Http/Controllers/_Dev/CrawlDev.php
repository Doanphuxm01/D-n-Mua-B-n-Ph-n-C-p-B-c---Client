<?php
namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\SearchHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Book;
use Illuminate\Http\Request;
use App\Http\Models\Cate;
use App\Http\Models\Product;
use App\Elibs\Pager;

class CrawlDev extends Controller
{

    public function index($class = '', $method = '')
    {

        if (!file_exists(__DIR__ . '/crawl/' . $class . '.php')) {
            return $this->_return('Class (' . $class . ') not found', [], -1);
        }
        require_once __DIR__ . '/crawl/' . $class . '.php';
        $action = strtolower($method);
        $classObject = __NAMESPACE__ . '\\' . $class;
        $classObject = new $classObject;
        $action = str_replace('-', '_', $action);
        if (method_exists($classObject, $action)) {
            return $classObject->$action();
        }

        die('xxx-*--*-xxx');
    }

    public function _inti_product(){
        Product::truncate();
            // for($i = 0; $i<= 100; $i++){
            //     Product::fake_p();
            // }
        die(__FILE__.__FILE__);
    }

}
