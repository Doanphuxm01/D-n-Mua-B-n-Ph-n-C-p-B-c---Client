<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Payment extends BaseModel
{
    public $timestamps = false;
    const table_name = 'payments';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];
    const METHOD_TRANFER = 'METHOD_TRANFER';
    const METHOD_CASH = 'METHOD_CASH';

    static function getListStatus($selected = FALSE)
    {
        $listStatus = [
            self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Đã thanh toán ', 'text-action' => 'Đã thanh toán'],
            self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Đang khóa ', 'text-action' => 'Khóa lại'],
            self::STATUS_NO_PAID => ['id' => self::STATUS_NO_PAID, 'style' => 'danger', 'text' => 'Chưa thanh toán ', 'text-action' => 'Chưa thanh toán'],
            self::STATUS_PENDING => ['id' => self::STATUS_PENDING, 'style' => 'warning', 'text' => 'Chờ duyệt', 'text-action' => 'Chờ duyệt'],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }
}
