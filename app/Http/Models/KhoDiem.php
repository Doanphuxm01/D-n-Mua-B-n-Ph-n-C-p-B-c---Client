<?php


namespace App\Http\Models;


class KhoDiem extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_khodiem';
    protected $table = self::table_name;
    static $unguarded = TRUE;
}