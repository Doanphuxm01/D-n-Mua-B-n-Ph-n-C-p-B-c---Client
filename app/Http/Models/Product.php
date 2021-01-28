<?php


namespace App\Http\Models;
use App\Elibs\eCache;
use App\Elibs\Helper;

class Product extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_products';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = ['_id', 'name', 'sku', 'sort', 'regularPrice', 'amount', 'finalPrice', 'avatar_url', 'status', 'tags', 'cate_primary', 'alias', 'categories'];
    static $allProduct = [];
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

        if (isset(self::$allProduct[$cache_key])) {
            return self::$allProduct[$cache_key];
        }
        self::$allProduct[$cache_key] = eCache::get($cache_key);

        if (self::$allProduct[$cache_key] == 'NULL') {
            return false;
        } elseif (self::$allProduct[$cache_key]) {
            return self::$allProduct[$cache_key];
        }
        //'category', 'elemMatch', array('alias' => $cate_alias));
        $listCate = self::where($where);
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
            self::$allProduct[$cache_key] = $menu_data;
        } else {
            self::$allProduct[$cache_key] = 'NULL';
        }
        eCache::add($cache_key, self::$allProduct[$cache_key], 84000);
        //Debug::show(self::$allProduct[$cache_key]);

        return self::$allProduct[$cache_key];
    }
    
    static function getByProductId($id) {
        $item = eCache::get(__FUNCTION__ . $id);
        if ($item) {
            return $item;
        }
        $item = self::find($id);
        if ($item) {
            $item = $item->toArray();
        }
        eCache::add(__FUNCTION__ . $id, $item);

        return $item;
    }

    static function getByProductIdAndAlias($id, $alias) {
        $item = eCache::get(__FUNCTION__ . $id);
        if ($item) {
            return $item;
        }
        $where =  [
            '_id' => Helper::getMongoId($id),
            'alias' => $alias
        ];
        $item = self::where($where)->first();
        if ($item) {
            $item = $item->toArray();
        }
        eCache::add(__FUNCTION__ . $id, $item);

        return $item;
    }

    static function getByTag($alias,  $limit = 6) {
        $where = [
            'tags.alias' => $alias,
            'status' => Cate::STATUS_ACTIVE,
        ];
        return self::table(self::table_name)->limit($limit)->select(self::$basicFiledsForList)->where($where)->get();
    }

    public static function getProductByCate($cate, $limit = 6)
    {
        $listItem = self::where('status', self::STATUS_ACTIVE)->where('categories.alias', $cate)
            ->select(self::$basicFiledsForList)->limit($limit)->orderBy('actived_at', 'DESC')->get();
        return $listItem;
    }

    public static function getProductByIdsCate($where = [], $groupBy = false,  $limit = false)
    {
        $listItem = self::where($where);
        if($groupBy) {
            $listItem = $listItem->groupBy($groupBy);
        }

        if($limit) {
            $listItem = $listItem->limit($limit);
        }

        return $listItem->select(self::$basicFiledsForList)->get()->toArray();
    }


}