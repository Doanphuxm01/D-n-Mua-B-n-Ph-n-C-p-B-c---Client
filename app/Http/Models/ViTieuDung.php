<?php


namespace App\Http\Models;


class ViTieuDung extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_vitieudung';
    protected $table = self::table_name;
    static $unguarded = TRUE;

    static function getViByAccount($account) {
        $where = [
            'account' => $account,
            'status' => ViTieuDung::STATUS_ACTIVE,
        ];

        return self::where($where)->first();
    }
}