<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Contract extends BaseModel
{
    public $timestamps = FALSE;
    const table_name        = 'contract';//hợp đồng
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];

    //trạng thái hợp đồng
    const CONTRACT_PROCESSING = 'dang-thuc-hien';
    const CONTRACT_FINISH = 'ket-thuc';
    const CONTRACT_STOP = 'dung-thi-cong';
    const CONTRACT_WAIT_TO_PAID = 'cho-quyet-toan';
    const CONTRACT_DONE = 'da-hoan-thanh';
    const CONTRACT_NOT_START = 'chua-thuc-hien';

    const CONTRACT_STATUS = [
        self::CONTRACT_NOT_START=> [
            'key'   => 'chua-thuc-hien',
            'label' => "Chưa thực hiện",
            'style' => "bg-orange-300",
        ],
        self::CONTRACT_PROCESSING => [
            'key'   => 'dang-thuc-hien',
            'label' => "Đang thực hiện",
            'style' => "label-info",
        ],
        self::CONTRACT_STOP => [
            'key'   => 'dung-thi-cong',
            'label' => "Dừng thi công",
            'style' => "bg-orange-800",
        ],

//        self::CONTRACT_DONE => [
//            'key'   => 'da-hoan-thanh',
//            'label' => "Đã hoàn thành",
//            'style' => "success",
//        ],
        self::CONTRACT_WAIT_TO_PAID => [
            'key'   => 'cho-quyet-toan',
            'label' => "Chờ quyết toán",
            'style' => "label-warning",
        ],

        self::CONTRACT_FINISH => [
            'key'   => 'ket-thuc',
            'label' => "Kết thúc",
            'style' => "label-success",
        ],

    ];

    //Kiểu hợp đồng
    const CONTRACT_TYPE_CUSTOMER = 'khach-hang';
    const CONTRACT_TYPE_IN = 'noi-bo';
    const CONTRACT_TYPE_PARTNER = 'doi-tac';
    const CONTRACT_TYPE = [
        self::CONTRACT_TYPE_CUSTOMER => [
            'key'   => 'khach-hang',
            'name' => "Khách hàng",
        ],
        self::CONTRACT_TYPE_IN => [
            'key'   => 'noi-bo',
            'name' => "Nội bộ",
        ],
        self::CONTRACT_TYPE_PARTNER => [
            'key'   => 'doi-tac',
            'name' => "Đối tác",
        ]
    ];

    //Tổng mức đầu tư
    const INVEST_VAL_1 = '500-ty';
    const INVEST_VAL_2 = '500-1000-ty';
    const INVEST_VAL_3 = '1000-2000-ty';
    const INVEST_VAL_4 = '2000-5000-ty';
    const INVEST_VAL_5 = '5000-10000-ty';
    const INVEST_VAL_6 = '10000-ty';
    const SUM_INVEST = [
            self::INVEST_VAL_1 => [
                    'key'   => '500-ty',
                    'name' => "< 500 tỷ",
                    'value_f'   => '0',
                    'value_t'   => '500000000000',
                ],
            self::INVEST_VAL_2 => [
                    'key'   => '500-1000-ty',
                    'name' => "500-1000 tỷ",
                    'value_f'   => '500000000000',
                    'value_t'   => '1000000000000',
                ],
            self::INVEST_VAL_3 => [
                    'key'   => '1000-2000-ty',
                    'name' => "1000-2000 tỷ",
                    'value_f'   => '1000000000000',
                    'value_t'   => '2000000000000',
                ],
            self::INVEST_VAL_4 => [
                    'key'   => '2000-5000-ty',
                    'name' => "2000-5000 tỷ",
                    'value_f'   => '2000000000000',
                    'value_t'   => '5000000000000',
                ],
            self::INVEST_VAL_5 => [
                    'key'   => '5000-10000-ty',
                    'name' => "5000-10000 tỷ",
                    'value_f'   => '5000000000000',
                    'value_t'   => '10000000000000',
                ],
            self::INVEST_VAL_6 => [
                    'key'   => '10000-ty',
                    'name' => ">10000 tỷ",
                    'value_f'   => '10000000000000',
                    'value_t'   => '10000000000000',
                ],
        ];

    //Giá trị hợp đồng
    const CONTRACT_VAL_1 = '100-trieu';
    const CONTRACT_VAL_2 = '100-500-trieu';
    const CONTRACT_VAL_3 = '500-1-ty';
    const CONTRACT_VAL_4 = '1-2-ty';
    const CONTRACT_VAL_5 = '2-5-ty';
    const CONTRACT_VAL_6 = '5-10-ty';
    const CONTRACT_VAL_7 = '10-15-ty';
    const CONTRACT_VAL_8 = '15-20-ty';
    const CONTRACT_VAL_9 = '20-30-ty';
    const CONTRACT_VAL_10 = '30-ty';
    const CONTRACT_VALUE = [
            self::CONTRACT_VAL_1 => [
                    'key'   => '100-trieu',
                    'name' => "< 100 triệu",
                    'value_f'   => '100000000',
                    'value_t'   => '100000000',
                ],
            self::CONTRACT_VAL_2 => [
                    'key'   => '100-500-trieu',
                    'name' => "100-500 triệu",
                    'value_f'   => '100000000',
                    'value_t'   => '500000000',
                ],
            self::CONTRACT_VAL_3 => [
                    'key'   => '500-1-ty',
                    'name' => "500-1 tỷ",
                    'value_f'   => '500000000',
                    'value_t'   => '1000000000',
                ],
            self::CONTRACT_VAL_4 => [
                    'key'   => '1-2-ty',
                    'name' => "1-2 tỷ",
                    'value_f'   => '1000000000',
                    'value_t'   => '2000000000',
                ],
            self::CONTRACT_VAL_5 => [
                    'key'   => '2-5-ty',
                    'name' => "2-5 tỷ",
                    'value_f'   => '2000000000',
                    'value_t'   => '5000000000',
                ],
            self::CONTRACT_VAL_6 => [
                    'key'   => '5-10-ty',
                    'name' => "5-10 tỷ",
                    'value_f'   => '5000000000',
                    'value_t'   => '10000000000',
                ],
            self::CONTRACT_VAL_7 => [
                    'key'   => '10-15-ty',
                    'name' => "10-15 tỷ",
                    'value_f'   => '10000000000',
                    'value_t'   => '15000000000',
                ],
            self::CONTRACT_VAL_8 => [
                    'key'   => '15-20-ty',
                    'name' => "15-20 tỷ",
                    'value_f'   => '15000000000',
                    'value_t'   => '20000000000',
                ],
            self::CONTRACT_VAL_9 => [
                    'key'   => '20-30-ty',
                    'name' => ">20-30 tỷ",
                    'value_f'   => '20000000000',
                    'value_t'   => '30000000000',
                ],
            self::CONTRACT_VAL_10 => [
                    'key'   => '30-ty',
                    'name' => ">30 tỷ",
                    'value_f'   => '30000000000',
                    'value_t'   => '30000000000',
                ],
        ];

    const TIME_1 = '6-month';
    const TIME_2 = '6-12-month';
    const TIME_3 = '12-24-month';
    const TIME_4 = '24-48-month';
    const TIME_5 = '48-month';
    const TIME_IMPLEMENT_CONTRACT = [
        self::TIME_1 => [
            'key'   => '6-month',
            'name' => "< 6 tháng",
        ],
        self::TIME_2 => [
            'key'   => '6-12-month',
            'name' => "6-12 tháng",
        ],
        self::TIME_3 => [
            'key'   => '12-24-month',
            'name' => "12-24 tháng",
        ],
        self::TIME_4 => [
            'key'   => '24-48-month',
            'name' => "24-48 tháng",
        ],
        self::TIME_5 => [
            'key'   => '48-month',
            'name' => ">48 tháng",
        ],
    ];




    static function getAllContract()
    {
        return self::orderBy('_id','desc')->get()->keyBy('_id')->toArray();
    }

    /**
     * @param $object
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    static function buildLinkDelete($object, $router = '')
    {
        return admin_link('contract/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

    static function buildLinkEdit($object)
    {
        return admin_link('contract/input?id=' . $object['_id']);
    }

}
