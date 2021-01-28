<?php namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\DB;

class Cate extends BaseModel
{

    const table_name = 'io_category';
    protected $table = self::table_name;

    protected $fillable = [];
    static $unguarded = true;
    public $timestamps = false;

    static $allCate = [];
    public static $cateRoot   = [
        'games'     => [
            'alias' => 'hang-tieu-dung',
            'title' => 'Hàng tiêu dùng',
        ],
        'apps'   => [
            'alias' => 'apps',
            'title' => 'Apps',
        ],
        'family' => [
            'alias' => 'family',
            'title' => 'Family',
        ],
    ];
    static $cateTypeRegister = [
        'cate' => [
            //bắt buộc và là mặc định
            'key' => 'cate',
            'name' => 'Danh mục',
        ],
        'tag' => [
            'key' => 'tag',
            'name' => 'Tag ',
        ],
    ];
    static $cateObjectRegister = [
        'news' => [
            'key' => 'news',
            'name' => 'Tin tức chung',
        ],
        'product' => [
            'key' => 'product',
            'name' => 'Danh mục sản phẩm',
        ],
    ];

    static $allMenuClass = [];
    static $allMenuSubject = [];

    public static function buildLink($val)
    {
        if (is_array($val)) {
            $val = (object)$val;
        }
        //Debug::show($val);
        $alias = $val->alias;


        return asset('/' . $alias);

    }

    private static function _get_all_cate($where = [], $delcache = false, $where_extra = false)
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
        $listCate = Cate::where($where);
        if ($where_extra) {
            foreach ($where_extra['condition'] as $key => $value) {
                $listCate = $listCate->where($value['key'], $value['operator'], $value['value']);
            }
        }
        $listCate = $listCate->select('_id', 'name', 'alias', 'parents', 'parent_id', 'object', 'status', 'type', 'is_show_home', 'limit_show_home')->get()->toArray();
        //Debug::show($where);
        if ($listCate) {
            $menu_data = [];
            foreach ($listCate as $key => $val) {
                $val['link'] = self::buildLink($val);

                $menu_data['items'][$val['alias']] = $val;
                if (isset($val['parents']) && $val['parents']) {
                    foreach ($val['parents'] as $parent) {
                        $menu_data['parents'][$parent['alias']][] = $val['alias'];
                    }
                } else {
                    $menu_data['parents'][0][] = $val['alias'];
                }
            }
            self::$allCate[$cache_key] = $menu_data;
        } else {
            self::$allCate[$cache_key] = 'NULL';
        }
        eCache::add($cache_key, self::$allCate[$cache_key], 84000);
        //Debug::show(self::$allCate[$cache_key]);

        return self::$allCate[$cache_key];
    }


    /***
     * @param bool $delcache
     *
     * @return mixed
     * @note: Lấy toàn bộ danh sách danh mục của sản phẩm
     */
    static function getAllProductCate($delcache = false)
    {

        $where = [
            'object' => self::$cateObjectRegister['product']['key'],
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    static function getAllProductCateShowHome($delcache = false)
    {

        $where = [
            'object' => self::$cateObjectRegister['product']['key'],
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
            'is_show_home' => true,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    /***
     * @param bool $delcache
     *
     * @return mixed
     * @note: Lấy toàn bộ danh sách danh sách tag
     */
    static function getAllTag($delcache = false)
    {

        $where = [
            'type' => self::$cateTypeRegister['tag']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    /***
     * @param bool $delcache
     *
     * @return mixed
     * @note: lấy toàn bộ dang mục của tin tức
     */
    static function getAllNewsCate($delcache = false)
    {
        $where = [
            'object' => self::$cateObjectRegister['news']['key'],
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }


    public static function delCacheAll($shop_id)
    {
        self::getAllNewsCate('DEL_CACHE');
        self::getAllProductCate('DEL_CACHE');
    }

    /**
     * @param $menu_data
     * @param $parent_id
     * @param array $selected
     * @param int $loop
     *
     * @return string
     */
    static function buildCateSelectOption($menu_data, $parent_id, $selected = [], $loop = 0)
    {
        $data = '';
        if (is_array($menu_data) && isset($menu_data['parents'][$parent_id])) {
            if ($loop) {
                $loop++;
            }
            foreach ($menu_data['parents'][$parent_id] as $item_id) {
                unset($menu_data['parents'][$parent_id]);
                $data .= '<option value="' . $item_id . '" ';
                if ($selected && in_array($item_id, $selected)) {
                    $data .= ' selected ';
                }
                $data .= '>';
                if ($loop - 2 > 0) {
                    $data .= ' ' . str_repeat('--', $loop - 2);
                }
                $data .= ' ' . $menu_data['items'][$item_id]['name'] . '</option>';

                // find childitems recursively
                if ($loop) {
                    $data .= self::buildCateSelectOption($menu_data, $item_id, $selected, $loop);
                }
            }
        }
        return $data;
    }

    /**
     * @param $menu_data
     * @param $parent_id
     * @param array $selected
     * @param int $loop
     * @param string $inputName
     *
     * @return string
     */
    static function buildCateCheckbox(&$menu_data, $parent_id, $selected = [], $loop = 0, $inputName = 'CATE')
    {
        // dd($selected);
        $data = '';
        if (is_array($menu_data) && isset($menu_data['parents'][$parent_id])) {

            $data .= '<ul ' . (($loop == 1) ? ' class="list-cate" ' : '') . '>';
            if ($loop) {
                $loop++;
            }

            foreach ($menu_data['parents'][$parent_id] as $item_id) {
                unset($menu_data['parents'][$parent_id]);
                $data .= '<li><div class="checkbox"><label>';

                $data .= '<input name="' . $inputName . '[]" value="' . $item_id . '"  id="' . $inputName . $item_id . '" type="checkbox" class="styled" ';
                if ($selected && in_array($item_id, $selected)) {
                    $data .= ' checked="checked" ';
                }
                $data .= '> ' . $menu_data['items'][$item_id]['name'] . '</label></div>';

                // find childitems recursively
                if ($loop) {
                    $data .= self::buildCateCheckbox($menu_data, $item_id, $selected, $loop);
                }
                $data .= '</li>';
            }
            $data .= '</ul>';
        }
        $data .= '';
        return $data;
    }

    /**
     * @param $menu_data
     * @param $parent_id
     * @param array $selected
     * @param int $loop
     *
     * @return string
     */
    static function buildMenuLeft(&$menu_data, $parent_id, $selected = [], $loop = 1)
    {
        $data = '';
        //Debug::show(count($menu_data['parents']));
        if (isset($menu_data['parents'][$parent_id])) {

            $data .= '<ul ' . (($loop == 1) ? ' class="chapter-left" ' : '') . '>';
            if ($loop) {
                $loop++;
            }
            //if(isset($menu_data['items'][]))
            $itemInParents = $menu_data['parents'][$parent_id];
            unset($menu_data['parents'][$parent_id]);
            foreach ($itemInParents as $item_id) {
                if (isset($menu_data['items'][$item_id])) {
                    $_item = $menu_data['items'][$item_id];
                    // unset($menu_data['items'][$item_id]);
                    $parent_id = $_item['alias'];
                    $class = '';
                    $data .= '<li ';
                    if ($selected && in_array($item_id, $selected)) {
                        $class .= ' active ';
                    }
                    $sub = '';
                    if ($loop) {
                        $sub = self::buildMenuLeft($menu_data, $parent_id, $selected, $loop);
                    }
                    if ($class) {
                        $data .= ' class="' . $class . '"';
                    }
                    $data .= '>';
                    $data .= '<a ';
                    if ($sub) {
                        $data .= ' class="root" ';
                    }
                    $data .= ' title="' . $_item['name'] . '" href="' . $_item['link'] . '"> ' . $_item['name'] . '</a>';


                    // find childitems recursively
                    if ($loop) {
                        $data .= $sub;
                    }
                    $data .= '</li>';
                } else {
                    break;
                }
            }
            $data .= '</ul>';
        }
        $data .= '';
        return $data;
    }


    static function buildTreeMenuLeft($menu_data, $parent_id = 0, $selected = [], $loop = 0) {
        $data = [];
        foreach ($menu_data as $item) {
            if (isset($item['parent_id']) && $item['parent_id'] === $parent_id || $item['parent_id'] === @$item['key']) {
                if(@$item['key']) {
                    $children = self::buildTreeMenuLeft($menu_data, $item['key']);
                }else {
                    $children = self::buildTreeMenuLeft($menu_data, $item['alias']);
                }
                if ($children) {
                    $item['children'] = $children;
                }
                if(@$item['key']) {
                    $data[$item['key']] = $item;
                }else {
                    $data[$item['alias']] = $item;
                }
            }
        }
        return $data;
    }

    static function buildMenuLeftMPG2($menu_data)
    {
        $html = "";
        if (isset($menu_data)) {
            foreach ($menu_data as $item) {
                if (empty($item['children'])) {
                    $html .= '<li class="dd-item" data-id="'.$item['_id'].'">
                                <div class="dd-handle">' . @$item['name'] . '
                                    <a title="Cập nhật thông tin" href="'.admin_link('/customer/input?id='.$item['_id']).'"
                                        class="float-right"> <i class="icon-pencil7"></i>
                                    </a>
                                </div>
                            </li>';
                }
                if (!empty($item['children'])) {
                    $html .= '<li class="menu-item" data-id="'.$item['_id'].'">
                            <div class="dd-handle">' . @$item['name'] . '
                                <a title="Cập nhật thông tin" href="'.admin_link('/customer/input?id='.$item['_id']).'"
                                   class="float-right"> <i class="icon-pencil7"></i>
                                </a>
                            </div>
                            <ol class="dd-list">';
                    $html .= self::buildMenuLeftMPG($item['children']);
                    $html .= '</ol>';
                }
            }

        }
        return $html;
    }
    static function buildMenuLeftMPG($menu_data)
    {
        $html = "";
        if (isset($menu_data)) {
            foreach ($menu_data as $item) {
                    // dd($item);
                if (empty($item['children'])) {
                    $html .= '<li class="menu-item" data-id="'.$item['_id'].'">
                                <a href="'.$item['link'].".html".'" class="menu-link">
                                <img src="mpg-tmp/giaodienmuahang/images/ic_menu-1.png" alt="">
                                ' . @$item['name'] . '
                                </a>
                            </li>';
                }
                if (!empty($item['children'])) {
                    $html .= '<li class="menu-item hasSub" data-id="'.$item['_id'].'">
                            <a title="thông tin sản phẩm" class="menu-link" href="'.$item['link'].".html".'" class="menu-link">
                                <img src="mpg-tmp/giaodienmuahang/images/ic_menu-2.png" alt="">
                             '. @$item['name'] . '
                                
                            </a>
                            <ul class="sub-menu">';
                    $html .= self::buildMenuLeftMPG($item['children']);
                    $html .= '</ul>';
                }
            }

        }
        return $html;
    }

    static function getTagByAlias($alias)
    {
        $where = [
            'alias' => $alias,
            'type' => self::$cateTypeRegister['tag']['key'],
        ];
        $tag = Cate::where($where)->first();
        if ($tag) {
            $tag = $tag->toArray();
        }
        return $tag;
    }

    static function getObjByAlias($alias)
    {
        $where = [
            'alias' => $alias,
            'status' => self::STATUS_ACTIVE
        ];
        $tag = Cate::where($where)->first();
        if ($tag) {
            $tag = $tag->toArray();
        }
        return $tag;
    }

    static function getAllCateNewsEdu($delcache = false)
    {
        $where = [
            'object' => Cate::$cateObjectRegister['news-edu']['key'],
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    static function getAllCateByObject($object, $delcache = false)
    {
        $where = [
            'object' => $object,
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    static function getAllCate($delcache = false)
    {
        $where = [
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    static function getAllMenuClass($delcache = false)
    {
        if (self::$allMenuClass) {
            return self::$allMenuClass;
        }
        $where = [
            'type' => self::$cateTypeRegister['menu-class']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        self::$allMenuClass = self::_get_all_cate($where, $delcache);
        return self::$allMenuClass;

    }

    static function getAllMenuSubject($delcache = false)
    {
        if (self::$allMenuSubject) {
            return self::$allMenuSubject;
        }
        $where = [
            'type' => self::$cateTypeRegister['menu-subject']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        self::$allMenuSubject = self::_get_all_cate($where, $delcache);
        return self::$allMenuSubject;
    }


    static function getAllIndexByCate($cate, $delcache = false)
    {
        $cache_key = "_all_index_by_cate" . $cate->alias;

        $where = [
            //'type'=>Cate::$cateTypeRegister['main-index']['key'],
            'object' => Cate::$cateObjectRegister['index']['key'],
            'status' => Cate::STATUS_ACTIVE,
        ];
        $whereExtra = [
            'condition' => [
                [
                    'key' => 'parents',
                    'operator' => 'elemMatch',
                    'value' => array('alias' => $cate->alias)
                ]
            ],
            'key' => $cache_key
        ];
        return self::_get_all_cate($where, $delcache, $whereExtra);

    }

    /***
     * @param bool $delcache
     *
     * @return mixed
     * @note: lấy toàn bộ dang mục của tin tức
     */
    static function getAllCateSubject($delcache = false)
    {
        $where = [
            'object' => self::$cateObjectRegister['subject']['key'],
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }

    static function getAllCateBook($delcache = false)
    {
        $where = [
            'object' => self::$cateObjectRegister['book']['key'],
            'type' => self::$cateTypeRegister['cate']['key'],
            'status' => self::STATUS_ACTIVE,
        ];
        return self::_get_all_cate($where, $delcache);
    }


    static function getCateByAlias($alias)
    {
        if (!$alias) {
            return null;
        }
        $where = [
            'alias' => $alias,
        ];
        return Cate::where($where)->first();
    }


    public static function getTableTmp()
    {
        return DB::table('io_cate_tmp');
    }

    static function buildTree(array &$menu_data, $parent_id = '0', $selected = [], $loop = 0) {
        $data = [];
        foreach ($menu_data as $k => &$item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::buildTree($menu_data, $item['ma_gioi_thieu']??$item['account']);
                if ($children) {
                    $item['children'] = $children;
                }
                $data[@$item['account']] = $item;
                unset($menu_data[$k]);
            }
        }
        return $data;
    }

}
