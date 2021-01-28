<?php


namespace App\Http\Models;


class ViChietKhau extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_vichietkhau';
    protected $table = self::table_name;
    static $unguarded = true;
}