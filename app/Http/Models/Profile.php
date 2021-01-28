<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Profile extends BaseModel
{
    public $timestamps = FALSE;
    const table_name        = 'profile';//hồ sơ
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];

    static function getAllProfile()
    {
        return self::orderBy('_id','desc')->get()->keyBy('_id')->toArray();
    }

    /**
     * @param $object
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    static function buildLinkDelete($object, $router = '')
    {
        return admin_link('profile/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

    static function buildLinkEdit($object)
    {
        return admin_link('profile/input?id=' . $object['_id']);
    }

}
