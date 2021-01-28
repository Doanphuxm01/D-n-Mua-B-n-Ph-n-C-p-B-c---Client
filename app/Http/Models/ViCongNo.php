<?php


namespace App\Http\Models;


class ViCongNo extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_vicongno';
    protected $table = self::table_name;
    static $unguarded = true;
}