<?php


namespace App\Http\Models;


class Developer extends BaseModel
{
    public $timestamps = false;
    const table_name = 'developers';
    protected $table = self::table_name;
    static $unguarded = true;
}