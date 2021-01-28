<?php

namespace App\Http\Models;
use App\Elibs\eView;
use App\Elibs\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Orders extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_orders';
    protected $table = self::table_name;
    static $unguarded = TRUE;

    const DEBT_YES = 'yes'; // nợ vl
    const DEBT_NO = 'no';   // không nợ
    const MP_MART = 'mp_mart';
    const min_mpg = 500000;
    const min_dai_ly = 20000000;
    const min_mpmart = 300000000;

    const ORDER_BUY_MPG = 'order_buy_mpg';
    const ORDER_CHUYENDIEM_MPG = 'order_chuyendiem_mpg';
    const ORDER_HOAHONG = 'order_hoahong';

    const EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY = 'EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY';
    const EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG = 'EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG';
    const EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY = 'EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY';
    const EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG = 'EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG';
    const PERCENT_DEBT_CTV = 0.25;
    const PERCENT_CHIETKHAU = 0.8;
    const PERCENT_CONGNO_DEBT_YES = 0.25;
    const PERCENT_CONGNO_PHIVANCHUYEN = 0.6;
    const PERCENT_CHIETKHAU_TO_TICHLUY_DEBT_YES = 0.002;
    static $PERCENT_CHIETKHAU_TO_TICHLUY_DEBT_YES = 0.002;
    static $PERCENT_CHIETKHAU_TO_TIEUDUNG_DEBT_NO = 0.005;

    static $objectRegister = [
        self::DEBT_YES => [
            'key' => self::DEBT_YES,
            'name' => 'Có nợ',
        ],
        self::DEBT_NO => [
            'key' => self::DEBT_NO,
            'name' => 'Không nợ',
        ],
        self::MP_MART => [
            'key' => self::MP_MART,
            'name' => 'MPMart',
        ],
    ];

    static function getPercentChietKhau($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_chietkhau'])) {
            return 0.8;
        }
        return $data['percent_chietkhau']/100;
    }

    static function getPercentCongNoDebtYes($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_congno_debt_yes'])) {
            return 0.25;
        }
        return $data['percent_congno_debt_yes']/100;
    }

    static function getPercentChietKhauDaiLy($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_chietkhau_daily'])) {
            return 0.8;
        }
        return $data['percent_chietkhau_daily']/100;
    }

    static function getPercentChietKhauCtv($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_chietkhau_ctv'])) {
            return 0.7;
        }
        return $data['percent_chietkhau_ctv']/100;
    }

    static function getPercentChietKhauMpMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_chietkhau_daily'])) {
            return 0.25;
        }
        return $data['percent_chietkhau_daily']/100;
    }

    static function getPercentKhoDiemTieuDungDeliveredDaiLyTinh($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_khodiem_tieudung_delivered_daily_tinh'])) {
            return 0.06;
        }
        return $data['percent_khodiem_tieudung_delivered_daily_tinh']/100;
    }

    static function getPercentKhoDiemTieuDungDeliveredDaiLyHuyen($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_khodiem_tieudung_delivered_daily_huyen'])) {
            return 0.045;
        }
        return $data['percent_khodiem_tieudung_delivered_daily_huyen']/100;
    }

    static function getPercentKhoDiemTieuDungDeliveredDaiLyTinhForFull($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_khodiem_tieudung_delivered_full_f'])) {
            return 0.015;
        }
        return $data['percent_khodiem_tieudung_delivered_full_f']/100;
    }

    static function getIsDaiLy($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['order_is_daily'])) {
            return 20000000;
        }
        return $data['order_is_daily'];
    }

    static function getIsMPMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['order_is_mpmart'])) {
            return 300000000;
        }
        return $data['order_is_mpmart'];
    }

    static function getPercentCongNoPhiVanChuyen($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['percent_congno_phivanchuyen'])) {
            return 0.06;
        }
        return $data['percent_congno_phivanchuyen']/100;
    }

    static function getMinMPMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['min_mpmart'])) {
            return 300000000;
        }
        return $data['min_mpmart'];
    }

    static function getMinDaiLy($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['min_dai_ly'])) {
            return 20000000;
        }
        return $data['min_dai_ly'];
    }

    static function getMinMPG($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['min_mpg'])) {
            return 500000;
        }
        return $data['min_mpg'];
    }

    static function getMinMPGAfterRegister($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['min_mpg_after_register'])) {
            return 50000;
        }
        return $data['min_mpg_after_register'];
    }

    static function getPrecentDiemHoaHongForF($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        $moneyForF = [
            @$data['muadiem_hoahong_f1']/100,
            @$data['muadiem_hoahong_f2']/100,
            @$data['muadiem_hoahong_f3']/100,
            @$data['muadiem_hoahong_f4']/100,
            @$data['muadiem_hoahong_f5']/100
        ];
        return $moneyForF;
    }

    static function getPrecentDiemMpMartHoaHongForF($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        $moneyForF = [
            @$data['muadiem_mpmart_hoahong_f1']/100,
            @$data['muadiem_mpmart_hoahong_f2']/100,
            @$data['muadiem_mpmart_hoahong_f3']/100,
            @$data['muadiem_mpmart_hoahong_f4']/100,
            @$data['muadiem_mpmart_hoahong_f5']/100
        ];
        return $moneyForF;
    }

    static function getPreCentChietKhauTieuDungForF($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        $moneyForF = [
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f1']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f2']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f3']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f4']/100,
            @$data['everyday_percent_chietkhau_tieudung_debt_no_f5']/100
        ];
        return $moneyForF;
    }

    static function getEveryDayPercentChietKhauTichLuyDebtYes($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tichluy_debt_yes'])) {
            return 0.002;
        }
        return $data['everyday_percent_chietkhau_tichluy_debt_yes']/100;
    }

    static function getEveryDayPercentChietKhauTieuDungDebtNo($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tieudung_debt_no'])) {
            return 0.005;
        }
        return $data['everyday_percent_chietkhau_tieudung_debt_no']/100;
    }

    static function getEveryDayPercentChietKhauTichLuyDebtNoCTV($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tichluy_debt_no_ctv'])) {
            return 0.002;
        }
        return $data['everyday_percent_chietkhau_tichluy_debt_no_ctv']/100;
    }

    static function getEveryDayPercentChietKhauTieuDungDebtNoCTV($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tieudung_debt_no_ctv'])) {
            return 0.005;
        }
        return $data['everyday_percent_chietkhau_tieudung_debt_no_ctv']/100;
    }

    static function getEveryDayPercentChietKhauTichLuyDebtNoMpMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tichluy_debt_no_mp_mart'])) {
            return 0.002;
        }
        return $data['everyday_percent_chietkhau_tichluy_debt_no_mp_mart']/100;
    }

    static function getEveryDayPercentChietKhauTieuDungDebtNoMpMart($old = false) {
        if($old) {
            $data = $old;
        }else {
            $data = UnauthorizedPersonnel::getUn();
        }
        if (!isset($data['everyday_percent_chietkhau_tieudung_debt_no_mp_mart'])) {
            return 0.005;
        }
        return $data['everyday_percent_chietkhau_tieudung_debt_no_mp_mart']/100;
    }

    static function getListDebtMpMart($selected = FALSE, $case = false)
    {
        if($case) {
            switch ($case) {
                case self::ORDER_BUY_MPG:
                {
                    $listStatus = [
                        self::DEBT_YES => ['id' => self::DEBT_YES, 'style' => 'danger', 'text' => 'Có nợ', 'text-action' => 'Có nợ'],
                        self::DEBT_NO => ['id' => self::DEBT_NO, 'style' => 'secondary', 'text' => 'Không nợ', 'text-action' => 'Không nợ'],
                        self::MP_MART => ['id' => self::MP_MART, 'style' => 'success', 'text' => 'MP Mart', 'text-action' => 'MP Mart'],
                    ]; break;
                }
                case self::ORDER_HOAHONG:
                {
                    $listStatus = [
                        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Chờ', 'text-action' => 'Kích hoạt hiển thị'],
                        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'style' => 'secondary', 'text' => 'Chờ kích hoạt', 'text-action' => 'Chờ kích hoạt'],
                        self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Khóa', 'text-action' => 'Hủy'],
                    ]; break;
                }
            }
        }

        if($selected && !isset($listStatus[$selected])) {
            return false;
        }
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    static function getListStatus($selected = FALSE, $case = false)
    {
        if($case) {
            switch ($case) {
                case self::ORDER_BUY_MPG:
                {
                    $listStatus = [
                        self::STATUS_PROCESS_DONE => ['id' => self::STATUS_PROCESS_DONE, 'style' => 'success', 'text' => 'Đã duyệt', 'text-action' => 'Đã duyệt'],
                        self::STATUS_NO_PROCESS => ['id' => self::STATUS_NO_PROCESS, 'style' => 'secondary', 'text' => 'Chờ xử lý', 'text-action' => 'Chờ xử lý'],
                        self::STATUS_DELETED => ['id' => self::STATUS_DELETED, 'style' => 'danger', 'text' => 'Đã xóa', 'text-action' => 'Đã xóa'],
                    ]; break;
                }
                case self::ORDER_HOAHONG:
                {
                    $listStatus = [
                        self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Chờ', 'text-action' => 'Kích hoạt hiển thị'],
                        self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'style' => 'secondary', 'text' => 'Chờ kích hoạt', 'text-action' => 'Chờ kích hoạt'],
                        self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Khóa', 'text-action' => 'Hủy'],
                    ]; break;
                }
            }
        }

        if($selected && !isset($listStatus[$selected])) {
            return [];
        }
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    static function getMonthsEndRunAuto() {
        return 10;
    }

    static function getOrderDebtByArrIds($ids, $keyBy = false) {
        $now = Helper::getMongoDate('d/m/Y');

        $where = [
            'status' => self::STATUS_PROCESS_DONE,
            '$or' => [
                [
                    'updated_vi_at' => ['$exists' => false],
                ],
                [
                    'updated_vi_at' => ['$lt' => $now],
                ]
            ],
            // chỉ lấy ra những tk có nợ và ko phải là mpmart
            /*'$or' => [
                [
                    'mpmart' => ['$exists' => false],
                ],
                [
                    'debt' => ['$in' => [self::DEBT_YES, self::DEBT_NO]],
                ]
            ],*/
        ];
        $data = self::where($where)->whereIn('_id', $ids)->get();
        if($keyBy) {
            return $data->keyBy($keyBy)->toArray();
        }
        return $data->toArray();
    }

    static function getDanhSachDonHangChuaCapNhatViMoiNgay($keyBy = false) {
        // lấy danh sahcs đơn hàng chưa được 10 tháng cập nhật ví tiêu dùng, tích luỹ mỗi ngày,
        $now = Helper::getMongoDate('d/m/Y');
        $where = [
            'status' => self::STATUS_PROCESS_DONE,
            '$or' => [
                [
                    'end_updated_vi_at' => ['$exists' => false],
                ],
                [
                    'end_updated_vi_at' => ['$gt' => $now],
                ]
            ],
            '$or' => [
                [
                    'updated_vi_at' => ['$exists' => false],
                ],
                [
                    'updated_vi_at' => ['$lt' => $now],
                ]
            ],
        ];
        $data = self::where($where)->get();
        if($keyBy) {
            return $data->keyBy($keyBy)->toArray();
        }
        return $data->toArray();
    }

    static function getDanhSachDonHangCapNhatViMoiNgayTrenSoDuConLai($keyBy = false) {
        // lấy danh sahcs đơn hàng được 10 tháng cập nhật ví tiêu dùng, tích luỹ mỗi ngày,
        $now = Helper::getMongoDate(date('d/m/Y'));
        $where = [
            'status' => self::STATUS_PROCESS_DONE,
            '$or' => [
                [
                    'end_updated_vi_at' => ['$exists' => false],
                ],
                [
                    'end_updated_vi_at' => ['$lt' => $now],
                ]
            ],
            'updated_vi_at' => ['$lt' => $now],
        ];
        $data = self::where($where)->get();
        if($keyBy) {
            return $data->keyBy($keyBy)->toArray();
        }
        return $data->toArray();
    }



}