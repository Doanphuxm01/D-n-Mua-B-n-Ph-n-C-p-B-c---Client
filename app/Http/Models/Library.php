<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Library extends BaseModel
{
    public $timestamps = FALSE;
    const table_name        = 'library';//Tài liệu tham khảo
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];


    /**
     * @param $object
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    static function buildLinkDelete($object, $router = '')
    {
        return admin_link('library/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

}
