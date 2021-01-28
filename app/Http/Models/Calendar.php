<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

/**
 * Class Calendar
 * @package App\Http\Models
 * Lịch làm việc
 */
class Calendar extends BaseModel
{
    public $timestamps = false;
    const table_name = 'kayn_calendars';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];


}
