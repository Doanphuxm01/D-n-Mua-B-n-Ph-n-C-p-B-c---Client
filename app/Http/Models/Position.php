<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;

/**
 * Class Position
 * @package App\Http\Models
 * Quản lý chức vụ phân quyền
 */
class Position extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'kayn_position';
    protected $table = self::table_name;
    static $unguarded = TRUE;

    static function getAll()
    {
        $lsObj = BaseModel::table(self::table_name)
            ->where('removed', BaseModel::REMOVED_NO)
            ->orderBy('_id', 'DESC')->get();
        return $lsObj;
    }

    /**
     * Danh sách vị trí, chức vụ
     */
    static function getPositionStaff($_where = [])
    {
        $where = [
            'status' => BaseModel::STATUS_ACTIVE,
        ];
        if (!empty($_where)) {
            foreach ($_where as $key => $item) {
                $where[$key] = $item;
            }
        }

        return self::where($where)->get();
    }



}
