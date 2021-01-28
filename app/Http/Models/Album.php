<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;

class Album extends BaseModel
{
    public $timestamps = FALSE;
    const table_name        = 'albums';
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];

    static function getAllAlbum()
    {
        return self::orderBy('_id','desc')->get()->keyBy('_id')->toArray();
    }

    /**
     * @param $object
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    static function buildLinkDelete($object, $router = '')
    {
        return admin_link('album/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

    static function buildLinkEdit($object)
    {
        return admin_link('album/input?id=' . $object['_id']);
    }

    static function getFileLink($link)
    {
        if (Helper::isLink($link)) {
            return $link;
        }
        return url('/data/' . $link);
    }

}
