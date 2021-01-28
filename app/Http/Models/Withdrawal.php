<?php


namespace App\Http\Models;


class Withdrawal extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_withdrawal';
    protected $table = self::table_name;
    static $unguarded = TRUE;

    static function getArrayOpenRutTien() {
        return [1, 2, 3, 15, 16, 17];
    }

    static function getLotVi() {
        return 50000;
    }

    static function getListStatus($selected = FALSE, $status = FALSE)
    {
        $listStatus = [
            self::STATUS_NO_PROCESS => [
                'id' => self::STATUS_NO_PROCESS,
                'style' => 'secondary',
                'icon' => 'mdi mdi-bullseye-arrow',
                'text' => 'Chờ xử lý',
                'text-action' => 'Chờ xử lý',
            ],
            self::STATUS_PROCESS_DONE => [
                'id' => self::STATUS_PROCESS_DONE,
                'style' => 'success',
                'icon' => 'mdi mdi-trash-can-outline',
                'text' => 'Thành công',
                'text-action' => 'Thành công',
                'group-action' => []
            ],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }elseif($status !== FALSE) {
            if(isset($listStatus[$status])) {
                return $listStatus[$status];
            }
            return false;
        }

        return $listStatus;
    }
}