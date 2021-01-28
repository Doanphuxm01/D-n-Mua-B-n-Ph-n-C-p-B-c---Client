<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Car extends BaseModel
{
    public $timestamps = false;
    const table_name = 'cars';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];




    static function getByAlias($alias)
    {
        $where = [
            'alias' => $alias
        ];
        return self::where($where)->first();
    }


    static function getByDevice($alias)
    {
        $where = [
            'device_key' => $alias
        ];
        return self::where($where)->first();
    }

    /**
     * @param $object
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    static function buildLinkDelete($object,$router='')
    {
        return admin_link('car/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }


}
