<?php


namespace App\Http\Models;


class QuaTang extends Product
{
    public $timestamps = false;
    const table_name = 'io_quatang';
    const STATUS_DELETED = 'deleted';
    protected $table = self::table_name;
}