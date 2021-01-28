<?php


namespace App\Http\Models;


class ViTichLuy extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_vitichluy';
    protected $table = self::table_name;
    static $unguarded = true;
}