<?php

namespace App\Http\Models;

//use Illuminate\Database\Eloquent\Model;
use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Traits\AutoNextNumber;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class BaseModel extends Eloquent
{
    use AutoNextNumber;

    protected $table = '';

    protected $fillable = [];

    public $timestamps = FALSE;


    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CRALLW = 'cralw-ok';
    const STATUS_DISABLE = 'disabled';
    const STATUS_DELETED = 'deleted';
    const STATUS_DELETED_PHY = 'remove-phy';//xoa vat ly
    const STATUS_PENDING = 'pending';// chờ duyệt
    const STATUS_DRATF = 'dratf';// bản nháp
    const STATUS_PROCESS_OK = 'processed';// Bản xử lý mọi thứ OK và chờ mở dần dần => dùng job để update mở bài này ra hàng ngày mỗi ngày 1 ít
    const STATUS_PROCESS_DONE = 'done';// Bản xử lý mọi thứ OK và chờ mở dần dần => dùng job để update mở bài này ra hàng ngày mỗi ngày 1 ít
    const STATUS_NO_PAID = 'no_paid'; //chưa thanh toán
    const STATUS_NO_PROCESS = 'no_process'; //chưa xử lý
    const STATUS_PROCESSING = 'process'; //đang xử lý
    const STATUS_CANCEL = 'cancel'; //hủy


    const REMOVED_NO = 'no';
    const REMOVED_YES = 'yes';

    const VERIFIED_NO = 'no';
    const VERIFIED_YES = 'yes';


    const OBJECT_VICHIETKHAU      = 'OBJECT_VICHIETKHAU';//ví chiết khấu
    const OBJECT_VITIEUDUNG      = 'OBJECT_VITIEUDUNG';//ví tiêu dùng
    const OBJECT_VICONGNO      = 'OBJECT_VICONGNO';//ví tiêu dùng
    const OBJECT_VIHOAHONG      = 'OBJECT_VIHOAHONG';//ví tiêu dùng
    const OBJECT_VITICHLUY      = 'OBJECT_VITICHLUY';//ví tiêu dùng
    /***
     * @param bool|FALSE $selected
     * @return array
     * @note: Định nghĩa và Lấy danh sách các trạng thái của danh mục trong bảng
     */

    public static function buildLink($val)
    {
        if (is_array($val)) {
            $val = (object)$val;
        }
        //Debug::show($val);
        $alias = $val->alias;


        return asset('/' . $alias);

    }
    static function getListStatus($selected = FALSE)
    {
        $listStatus = [
            self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Đang hoạt động ', 'text-action' => 'Kích hoạt hiển thị'],
            self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'style' => 'secondary', 'text' => 'Chờ kích hoạt', 'text-action' => 'Chờ kích hoạt'],
            self::STATUS_PROCESSING => ['id' => self::STATUS_PROCESSING, 'style' => 'warning', 'text' => 'Đang xử lý ', 'text-action' => 'Đang xử lý'],
            self::STATUS_NO_PROCESS => ['id' => self::STATUS_NO_PROCESS, 'style' => 'warning', 'text' => 'Chưa xử lý ', 'text-action' => 'Chưa xử lý'],
            self::STATUS_PROCESS_DONE => ['id' => self::STATUS_PROCESS_DONE, 'style' => 'success', 'text' => 'Đã xử lý', 'text-action' => 'Đã xử lý'],
            self::STATUS_DRATF => ['id' => self::STATUS_DRATF, 'style' => 'secondary', 'text' => 'Bản nháp ', 'text-action' => 'Bản nháp'],
            self::STATUS_CANCEL => ['id' => self::STATUS_CANCEL, 'style' => 'warning', 'text' => 'Hủy', 'text-action' => 'Hủy'],
            self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Không sử dụng', 'text-action' => 'Hủy'],
            self::STATUS_DELETED => ['id' => self::STATUS_DELETED, 'style' => 'danger', 'text' => 'Đã xóa', 'text-action' => 'Đã xóa'],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    /**
     * @param $selected
     * @return array|mixed
     * @note: dùng trong các view tránh if els quá nhiều
     */
    static function getStatus($selected, $case = false)
    {
        if($case) {
            $list = self::getListStatus($selected, $case);
        }else {
            $list = self::getListStatus($selected);
        }


        if (isset($list[$selected])) {
            return $list[$selected];
        } else {
            switch ($selected) {
                case self::STATUS_NO_PAID:
                {
                    return [
                        'id' => self::STATUS_NO_PAID,
                        'style' => 'warning',
                        'text' => 'Chưa thanh toán ',
                        'text-action' => 'Chưa thanh toán',
                    ];
                }
                case self::STATUS_NO_PROCESS:
                {
                    return [
                        'id' => self::STATUS_NO_PROCESS,
                        'style' => 'warning',
                        'text' => 'Chưa xử lý',
                        'text-action' => 'Chưa xử lý',
                    ];
                }
                case self::STATUS_PROCESS_DONE:
                {
                    return [
                        'id' => self::STATUS_PROCESS_DONE,
                        'style' => 'success',
                        'text' => 'Đã xử lý',
                        'text-action' => 'Đã xử lý',
                    ];
                }
                case Orders::DEBT_NO:
                {
                    return [
                        'id' => Orders::DEBT_NO,
                        'style' => 'success',
                        'text' => 'Không nợ',
                        'text-action' => 'Không nợ',
                    ];
                }
                case Orders::DEBT_YES:
                {
                    return [
                        'id' => Orders::DEBT_YES,
                        'style' => 'danger',
                        'text' => 'Ghi nợ',
                        'text-action' => 'Ghi nợ',
                    ];
                }
            }
            return [
                'id' => 0,
                'style' => 'warning',
                'text' => 'Không xác định: ' . $selected,
                'text-action' => 'Không xác định',
            ];
        }
    }

    public static function buildLinkDelete($object, $router)
    {

        return admin_link($router . '/_delete?id=' . $object['_id'] . '&token=' . Helper::buildTokenString($object['_id']));
    }


    public static function getNextNumber($table){

        $tableCounter = DB::table('io_counter');
        $counter = $tableCounter->find($table);
        if(!$counter || $counter['count'] == 0 )
        {
            $count = DB::table($table)->count();
            $count++;
        }
        else{
            $count = $counter['count'] + 1;
        }
        if(!$counter)
        {
            $tableCounter->insert(['_id'=>$table,'count'=>$count]);
        }
        else{
            $tableCounter->where(['_id'=>$table])->update(['count'=>$count]);

        }
        return $count;
    }
    public static function getNextNumberWithYear($table){
        $table = $table.date("Y");
        return self::getNextNumber($table);
    }

    public static function checkIsExist($listObj,$field,$value='co'){
        if($value=='co'){
            $listObj = $listObj->where($field, '$exists', true)->where(
                $field, ['$not' => ['$size' => 0]]
            );
        }else{
            $listObj = $listObj->whereRaw([
                '$or' => [
                    [
                        $field => ['$exists' => false]
                    ],
                    [
                        $field => ['$size' => 0]
                    ]
                ]
            ]);
        }
        return $listObj;
    }

    /**
     * @param $listObj
     * @param $field
     * @param $time : 31/02/2018 - 31/05/2018
     */
    public static function helperBuilderQueryByDate($listObj,$field,$time){
        if (is_string($time) && $time) {
            $updated_at_arr = explode('-', $time);
            if ($updated_at_arr && isset($updated_at_arr[0]) && isset($updated_at_arr[1])) {
                $timeStart = trim($updated_at_arr[0]);
                $timeEnd = trim($updated_at_arr[1]);
                if(Helper::validateDateTime($timeStart,'d/m/Y') && Helper::validateDateTime($timeEnd,'d/m/Y')){
                    $timeStart = Helper::getMongoDate($timeStart, '/', true);
                    $timeEnd = Helper::getMongoDate($timeEnd, '/', false);
                    return $listObj->whereBetween($field, array($timeStart, $timeEnd));
                }
            }
        }
        return $listObj;
    }
    public static function table($table)
    {
        return DB::table($table);
    }

    static function getLotVi() {
        return 50000;
    }

    static function getViByAccount($account) {
        $where = [
            'account' => $account,
            'status' => ViTieuDung::STATUS_ACTIVE,
        ];

        return self::where($where)->first();
    }

    static function getListVi($selected = FALSE)
    {
        $listStatus = [
            self::OBJECT_VICHIETKHAU => ['id' => self::OBJECT_VICHIETKHAU, 'style' => 'success', 'text' => 'Ví chiết khấu', 'text-action' => 'Ví chiết khấu'],
            self::OBJECT_VITICHLUY => ['id' => self::OBJECT_VITICHLUY, 'style' => 'success', 'text' => 'Ví tích lũy', 'text-action' => 'Ví tích lũy'],
            self::OBJECT_VIHOAHONG => ['id' => self::OBJECT_VIHOAHONG, 'style' => 'success', 'text' => 'Ví hoa hồng', 'text-action' => 'Ví hoa hồng'],
            self::OBJECT_VICONGNO => ['id' => self::OBJECT_VICONGNO, 'style' => 'success', 'text' => 'Ví công nợ', 'text-action' => 'Ví công nợ'],
            self::OBJECT_VITIEUDUNG => ['id' => self::OBJECT_VITIEUDUNG, 'style' => 'success', 'text' => 'Ví tiêu dùng', 'text-action' => 'Ví tiêu dùng'],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    static function getListViChuyenDiem($selected = FALSE)
    {
        $listStatus = [
            self::OBJECT_VITICHLUY => ['id' => self::OBJECT_VITICHLUY, 'style' => 'success', 'text' => 'Ví tích lũy', 'text-action' => 'Ví tích lũy'],
            self::OBJECT_VIHOAHONG => ['id' => self::OBJECT_VIHOAHONG, 'style' => 'success', 'text' => 'Ví hoa hồng', 'text-action' => 'Ví hoa hồng'],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }
}
