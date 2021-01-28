<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Code extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'code';
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];

    const CODE_DOC_TO = 'van-ban-di'; //van ban den
    const CODE_DOC_IN = 'van-ban-noi-bo'; //van ban den
    const CODE_PROFILE   = 'ho-so-du-an'; //van ban di
    const TYPE_CODE = [
        self::CODE_PROFILE => [
            'key'   => 'ho-so-du-an',
            'label' => "Hồ sơ/ Tài liệu dự án",
        ],
        self::CODE_DOC_TO => [
            'key'   => 'van-ban-di',
            'label' => "Văn bản đi",
        ],

        self::CODE_DOC_IN => [
            'key'   => 'van-ban-noi-bo',
            'label' => "Văn bản nội bộ",
        ]

    ];

    static function getAllCode($only_me = FALSE)
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


}



