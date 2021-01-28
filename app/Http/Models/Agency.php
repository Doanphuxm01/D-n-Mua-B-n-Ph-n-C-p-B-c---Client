<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Http\Models\Member;
use App\Elibs\HtmlHelper;

class Agency extends BaseModel
{
    public $timestamps = FALSE;
    const table_name        = 'io_agency';
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = ['name', 'alias', 'city', 'district', 'town', 'street', 'agency', 'member', 'created_at', 'updated_at', 'actived_at'];
    // protected $dates              = [];

    // trang thai
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const AGENCY_TRA_HANG_CAP_TINH  = 'dai-ly-tra-hang-cap-tinh';
    const AGENCY_TRA_HANG_CAP_HUYEN  = 'dai-ly-tra-hang-cap-huyen';
    const AGENCY_TRA_HANG  = 'dai-ly-tra-hang';
    const AGENCY_UY_QUYEN  = 'dai-ly-uy-quyen';
    const AGENCY_MP_MART   = 'dai-ly-mp-mart';
    // const AGENCY_CAP_TINH  = 'dai-ly-cap-tinh';

    static $objectAgency = [
        self::AGENCY_TRA_HANG => [
            'key' => self::AGENCY_TRA_HANG,
            'name' => 'Đại Lý Trả Hàng',
        ],
        self::AGENCY_UY_QUYEN => [
            'key' => self::AGENCY_UY_QUYEN,
            'name' => 'Đại Lý Ủy Quyền',
        ],
        self::AGENCY_MP_MART => [
            'key' => self::AGENCY_MP_MART,
            'name' => 'Đại Lý Mp Mart',
        ],
        // self::AGENCY_CAP_TINH => [
        //     'key' => self::AGENCY_CAP_TINH,
        //     'name' => 'Đại lý Cấp Tỉnh',
        // ],
    ];

    static function getListStatus($selected = FALSE)
    {
        $listStatus = [
            self::STATUS_ACTIVE => ['id' => self::STATUS_ACTIVE, 'style' => 'success', 'text' => 'Đại Lý Đang Hoạt Động', 'text-action' => 'Kích hoạt hiển thị'],
            // self::STATUS_INACTIVE => ['id' => self::STATUS_INACTIVE, 'style' => 'secondary', 'text' => 'Chờ kích hoạt', 'text-action' => 'Chờ kích hoạt'],
            self::STATUS_DISABLE => ['id' => self::STATUS_DISABLE, 'style' => 'warning', 'text' => 'Đại Lý Dừng Hoạt Động', 'text-action' => 'Hủy'],
        ];

        if($selected && !isset($listStatus[$selected])) {
            return false;
        }
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    public static function _insert($data){
        $get = [];
        $get = [
            'name' => $data['name']?? '',
            'agency' => $data['agency']?? '',
            'member' => $data['member']?? '',
            'street' => $data['street']?? '',
            'city' => $data['city']?? '',
            'district' => $data['district']?? '',
            'dai_ly_tra_hang' => ($data['trahang']) ??'',
            'town' => $data['town']?? '',
            'alias' => str_slug($data['name'])?? '',
            'status'    => BaseModel::STATUS_ACTIVE,
            'created_at' => Helper::getMongoDateTime(),
            'created_by' => Member::getCreatedByToSaveDb()
        ];
        return $get;

    }
    public static function _edit($data){
        $get = [];
        $get = [
            'name' => $data['name']?? '',
            'agency' => $data['agency']?? '',
            'member' => $data['member']?? '',
            'street' => $data['street']?? '',
            'city' => $data['city']?? '',
            'district' => $data['district']?? '',
            'dai_ly_tra_hang' => ($data['trahang']) ??'',
            'town' => $data['town']?? '',
            'alias' => str_slug($data['name'])?? '',
            'status'    => BaseModel::STATUS_ACTIVE,
            'updated_by' => Member::$currentMember['_id'],
            'updated_at' => Helper::getMongoDate(),
        ];
        return $get;

    }
    public static function _editNot($data){
        $get = [];
        $get = [
            'name' => $data['name'] ?? '',
            'agency' => $data['agency']?? '',
            'member' => $data['member']?? '',
            'street' => $data['street']?? '',
            'city' => $data['city']?? '',
            'district' => $data['district']?? '',
            'town' => $data['town']?? '',
            'alias' => str_slug($data['name'])?? '',
            'status'    => BaseModel::STATUS_ACTIVE,
            'updated_by' => Member::$currentMember['_id'],
            'updated_at' => Helper::getMongoDate(),
        ];
        return $get;

    }
    public static function _insertNot($data){
        $get = [];
        $get = [
            'name' => $data['name']?? '',
            'agency' => $data['agency']?? '',
            'member' => $data['member']?? '',
            'street' => $data['street']?? '',
            'city' => $data['city']?? '',
            'district' => $data['district']?? '',
            'town' => $data['town']?? '',
            'alias' => str_slug($data['name'])?? '',
            'status'    => BaseModel::STATUS_ACTIVE,
            'created_at' => Helper::getMongoDateTime(),
            'created_by' => Member::getCreatedByToSaveDb()
        ];
        return $get;

    }

    static function getListDaiLyTraHang($city = false, $district = false) {
        $where = [
            'status' => self::STATUS_ACTIVE,
            'dai_ly_tra_hang' => [
                '$in' => [self::AGENCY_TRA_HANG_CAP_TINH, self::AGENCY_TRA_HANG_CAP_HUYEN]
            ],
            '$or' => [
                [
                    'is_cty' => [
                        '$exists' => true
                    ],
                ],
                [
                    'is_cty' => [
                        '$exists' => false
                    ],
                    'city.id' => $city,
                ],
            ],
        ];
        /*if ($district) {
            $where ['$or'][1]['district.id'] = $district;
        }*/
        return self::select('id', 'is_cty','name', 'member', 'status', 'dai_ly_tra_hang', 'city', 'district', 'alias')->where($where)->get()->toArray();
    }


    static function getById($id) {
        $where = [
            '_id' => $id,
        ];

        return self::where($where)->first();
    }

    static function getDayLyTraHangId($id) {
        $where = [
            '_id' => Helper::getMongoId($id),
            'dai_ly_tra_hang' => [
                '$exists' => true,
                '$in' => [self::AGENCY_TRA_HANG_CAP_TINH, self::AGENCY_TRA_HANG_CAP_HUYEN]
            ],
        ];

        return self::where($where)->first();
    }

    static function getLsAgencyByIdCityNeIdAgency($id_city, $id_agency) {
        if(!$id_city) {
            return false;
        }
        $where = [
            'status' => self::STATUS_ACTIVE,
            'city.id' => $id_city,
            'dai_ly_tra_hang' => self::AGENCY_TRA_HANG_CAP_TINH,
            'is_cty' => [
                '$exists' => false
            ],
            'id' => [
                '$ne' => $id_agency
            ],
        ];
        return self::where($where)->get()->toArray();
    }

    static function getLsAgencyByIdCustomer($id_customer) {
        if(!$id_customer) {
            return false;
        }
        $where = [
            'status' => self::STATUS_ACTIVE,
            'member.account' => $id_customer,
        ];
        return self::where($where)->get()->toArray();
    }

}
