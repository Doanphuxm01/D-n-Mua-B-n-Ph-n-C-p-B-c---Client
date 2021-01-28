<?php


namespace App\Http\Models;


class ViHoaHong extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_vihoahong';
    protected $table = self::table_name;
    static $unguarded = true;

    static $moneyForF = [0.05, 0.04, 0.03, 0.01, 0.005];


}