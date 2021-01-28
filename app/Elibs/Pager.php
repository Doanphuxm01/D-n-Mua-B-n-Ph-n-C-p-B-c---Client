<?php
/**
 * Created by PhpStorm.
 * User: ngannv
 * Date: 9/13/15
 * Time: 12:11 AM
 */

namespace App\Elibs;


use Illuminate\Pagination\LengthAwarePaginator;

class Pager
{
    static private $instance = false;
    const PAGER_SIMPLE    = 'simple_paginate';
    const PAGER_FULL_PAGE = 2;
    const ITEM_PER_PAGE = 25;//số bản ghi mặc định trên 1 site
    static $disableLink = false;

    public function __construct()
    {
        self::$instance = &$this;
    }

    public static function &getInstance()
    {
        if (!self::$instance) {
            new self();
        }

        return self::$instance;
    }

    public $pages = [];
    /***
     * @param $object
     * @param int $item_per_page
     * @param string $request : array(key=>value) || string
     * @des: khoong co phan trang (chi co phan trang data)
     * @return mixed
     */
    function getPagerBasic($object, $item_per_page = 25,$curPage=1, $request = 'GET')
    {
        $a =  $object->skip($item_per_page * ($curPage - 1))->take($item_per_page)->get();
        //Debug::show($a->count());
        return $a;
        //return $this->getPager($object, $item_per_page, $request, self::PAGER_SIMPLE);
    }

    /***
     * @param $object
     * @param int $item_per_page
     * @param string $request : array(key=>value) || string
     *
     * @return mixed
     */
    function getPagerSimple($object, $item_per_page = 25, $request = 'GET')
    {
        return $this->getPager($object, $item_per_page, $request, self::PAGER_SIMPLE);
    }

	/**
     * @param        $object
     * @param int    $item_per_page
     * @param string $request
     * @param int    $type
     * @return LengthAwarePaginator
     */
    function getPager($object, $item_per_page = 25, $request = 'GET', $type = self::PAGER_FULL_PAGE)
    {
        if ($type == self::PAGER_FULL_PAGE) {
            $object = $object->paginate($item_per_page);
        } else {
            $object = $object->simplePaginate($item_per_page);
        }
        $param_link = false;
        if (is_array($request)) {
            $param_link = $request;
        }
        switch (strtolower($request)) {
            case 'get': {
                $param_link = $_GET;
                break;
            }
            case 'post': {
                $param_link = $_POST;
                break;
            }
            case 'all': {
                $param_link = array_merge($_POST, $_GET);
                break;
            }
        }
        if ($param_link) {
            $object->appends($param_link);
        }
        if(!self::$disableLink) {
            $link = $object->render();
        }

       /* $object                  = $object->toArray();
        $object['current_page']  = (String)$object['current_page'];
        $object['per_page']      = (String)$object['per_page'];
        $object['next_page_url'] = (String)$object['next_page_url'];
        $object['prev_page_url'] = (String)$object['prev_page_url'];
        if(!self::$disableLink) {
            $object['link'] = $link;
        }
        unset($object['from']);
        unset($object['to']);
        unset($link);*/

        return $object;
    }

    /**
     * @param bool|false $selected
     *
     * @return string
     */
    function optionItemPerPage($selected = false)
    {
        $listOption = [
            '15'     => [
                'id'   => 15,
                'text' => '15 bản ghi trên 1 trang',
            ], '25'  => [
                'id'   => 25,
                'text' => '25 bản ghi trên 1 trang',
            ], '40'  => [
                'id'   => 40,
                'text' => '40 bản ghi trên 1 trang',
            ], '80'  => [
                'id'   => 80,
                'text' => '80 bản ghi trên 1 trang',
            ], '100' => [
                'id'   => 100,
                'text' => '100 bản ghi trên 1 trang',
            ],
        ];
        if (isset($listOption[$selected])) {
            $listOption[$selected]['checked'] = 'checked';
        }
        $opt = '';
        foreach ($listOption as $key => $val) {
            $opt .= '<option value="' . $key . '"' . ' >' . $val['text'] . '</option>';
        }

        return $opt;
    }



}