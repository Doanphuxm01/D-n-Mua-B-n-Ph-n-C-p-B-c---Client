<?php


namespace App\Http\Models;


class UnauthorizedPersonnel extends BaseModel
{
    public $timestamps = false;
    const table_name = 'UnauthorizedPersonnel';
    protected $table = self::table_name;
    static $unguarded = true;

    static function getUn() {
        $data = self::where('xxxxminhphucxxx', 'xxxxminhphucxxx')->first();
        if($data) {
            return $data ->toArray();
        }
        return [];
    }
}