<?php
/**
 * Created by PhpStorm.
 * User: ngannv
 * Date: 9/13/15
 * Time: 5:19 PM
 */
namespace App\Http\Controllers\API;

use App\Elibs\Debug;

use App\Elibs\eBug;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;

class AppApi extends Controller
{
    public $member_id = 0;
    public $app_id    = 0;

    //
    private function _return($msg = '', $data = [], $status = '0')
    {
        $dt = [
            'status' => $status,
            'data'   => $data,
            'msg'    => $msg,
        ];
        if (config('app.debug')) {
            $debug = debug_backtrace();
            //Debug::show($debug);
            if (isset($debug[1])) {
                if (!isset($debug[1]['file'])) {
                    $debug[1] = $debug[0];
                }
                $file_name = pathinfo($debug[1]['file']);
                $dt['DEBUG']['msg'] = 'CODE: #' . $file_name['filename'] . '@' . $debug[1]['line'];
                $request = [
                    '_POST'    => $_POST,
                    '_GET'     => $_GET,
                    '_SESSION' => @$_SESSION,
                    '_SERVER'  => $_SERVER,
                    '_COOKIE'  => $_COOKIE,
                    '_FILES'   => $_FILES,
                    'RAW_DATA' => @file_get_contents('php://input'),
                ];
                $dt['DEBUG']['request'] = $request;
            }
            $dt['DEBUG']['sql'] = DB::getQueryLog();
            unset($debug);
        }
        //Session::forget('process');

        header('Content-Type: application/json');
        die(json_encode($dt));

    }

    /**
     * @param string $msg
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outputError($msg = 'error', $status = 0)
    {
        return $this->_return($msg, [], $status);
    }

    /**
     * @param string $msg
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outputExp($msg = 'exception', $status = -10)
    {
        return $this->_return($msg, [], $status);
    }

    /**
     * @param string $msg
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outputAlert($msg = 'alert')
    {
        return $this->_return($msg, [], -3);
    }

    /**
     * @param string $msg
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outputRequireAuth($msg = 'alert')
    {
        return $this->_return($msg, [], -100);
    }

    /**
     * @param string $msg
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outputReload($msg = 'reload')
    {
        return $this->_return($msg, [], -4);
    }


    /***
     * @param string $msg
     * @param array $data
     * @param int $status
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function outputDone($data, $msg = 'done', $status = 1)
    {
        die($this->_return($msg, $data, $status));
    }

    /***
     * @param $class
     * @param $method
     *
     * @return bool
     */
    public function public_api($class = '', $method = '')
    {


        $class .= '_api';
        if (!file_exists(__DIR__ . '/' . $class . '.php')) {
            return $this->_return('Class (' . $class . ') not found', [], -1);
        }

        if($class==='spk_api' && !isset($_GET['fake_header'])){
            $headers = request()->headers->all();
            if(!strpos($headers['user-agent'][0],'Playtime/Superkids/')){
                return $this->_return('Ok success!', [], 1);
            }
        }


        require_once __DIR__ . '/' . $class . '.php';
        $action = strtolower($method);

        $classObject = __NAMESPACE__ . '\\' . $class;
        $classObject = new $classObject;
        $action = str_replace('-', '_', $action);
        if (!$action) {
            $action = 'index';
        }
        if (method_exists($classObject, $action)) {
            return $classObject->$action();
        }

        $this->_return('Action (' . $class . '::' . $method . ') not found', [], -1);
    }


}
