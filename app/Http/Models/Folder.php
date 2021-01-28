<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Folder extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'folders';
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];

    const FOLDER_DOCUMENT = 'tai-lieu-tham-khao'; //tài liệu tham khảo
    const FOLDER_PROFILE   = 'ho-so-du-an'; //hồ sơ dự án
    const FOLDER_CONTRACT   = 'hop-dong'; //hợp đồng
    const TYPE_FOLDER = [
        self::FOLDER_PROFILE => [
            'key'   => 'ho-so-du-an',
            'label' => "Hồ sơ/ Tài liệu dự án",
        ],

        self::FOLDER_DOCUMENT => [
            'key'   => 'tai-lieu-tham-khao',
            'label' => "Tài liệu tham khảo",
        ],
        self::FOLDER_CONTRACT => [
            'key'   => 'hop-dong',
            'label' => "Quản lý hợp đồng",
        ]
    ];

    const FOLDER_INTERNAL = [
        'key'   => 'noi-bo',
        'label' => "Tài liệu nội bộ"
    ];
static $allCate = [];
    static function getAllFolder($only_me = FALSE)
    {
        return self::orderBy('_id','desc')->get()->keyBy('_id')->toArray();
    }

    static function getByAlias($alias)
    {
        $where = [
            'alias' => $alias,
        ];

        return self::where($where)->first();
    }

    static function createTree(&$list, $parent_id=0){
        $tree = array();
        if(isset($list['parent'][$parent_id])) {
            foreach ($list['parent'][$parent_id] as $item_id) {
                if (isset($list['items'][$item_id])) {
                    $list['items'][$item_id]['children'] = self::createTree($list, $item_id);
                    $tree[] = $list['items'][$item_id];
                }
            }
        }
        return $tree;
    }

    static function _get_all_cate($where = [], $delcache = false, $where_extra = false)
    {
        $cache_key = "_cate_" . http_build_query($where);
        if ($where_extra) {
            $cache_key .= $where_extra['key'];
        }

        if ($delcache) {
            eCache::del($cache_key);

            return true;
        }

        if (isset(self::$allCate[$cache_key])) {
            return self::$allCate[$cache_key];
        }
        self::$allCate[$cache_key] = eCache::get($cache_key);

        if (self::$allCate[$cache_key] == 'NULL') {
            return false;
        } elseif (self::$allCate[$cache_key]) {
            return self::$allCate[$cache_key];
        }
        //'category', 'elemMatch', array('alias' => $cate_alias));
        $listCate = self::where($where);
        if ($where_extra) {
            foreach ($where_extra['condition'] as $key => $value) {
                $listCate = $listCate->where($value['key'], $value['operator'], $value['value']);
            }
        }
        $listCate = $listCate->get()->toArray();
        //Debug::show($listCate);
        if ($listCate) {
            $menu_data = [];
            foreach ($listCate as $key => $val) {
                //$val['link'] = self::buildLink($val);
                $val['key'] = $val['_id'];
                $val['title'] = $val['name'];
                $val['folder'] = true;
                //$val['expanded'] = true;
                $val['token'] = Helper::buildTokenString($val['_id']);
                $val['type_folder'] = $val['type'];

                $menu_data['items'][$val['_id']] = $val;
                if (isset($val['parent']) && $val['parent']) {
                    /*foreach ($val['parent'] as $parent) {
                        $menu_data['parent'][$parent['_id']][] = $val['_id'];
                    }*/
                    $menu_data['parent'][$val['parent']][] = $val['_id'];
                } else {
                    $menu_data['parent'][0][] = $val['_id'];
                }
            }
            self::$allCate[$cache_key] = $menu_data;
        } else {
            self::$allCate[$cache_key] = 'NULL';
        }
        //eCache::add($cache_key, self::$allCate[$cache_key], 84000);
        eCache::add($cache_key, self::$allCate[$cache_key], 1);
        //Debug::show(self::$allCate[$cache_key]);

        return self::$allCate[$cache_key];
    }

}



