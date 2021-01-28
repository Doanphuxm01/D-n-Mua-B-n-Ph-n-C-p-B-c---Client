<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

/**
 * name:tên gọi, hoặc hiển thị
 * alias: unique => chính là value
 * type:loại
 * object: đối tượng (nhóm)
 * seo:
 * content:
 *
 * Class MetaData
 *
 * @package App\Http\Models
 */
class MetaData extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_meta_data';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = ['name', 'note', 'type', 'object'];

    const SERVICE_GROUP = 'service_group'; //nhóm dịch vụ
    const PROJECT_TYPE = 'project_type'; //loại hình dự án
    const PROJECT_MODEL = 'project_model'; //hạng mục dự án
    const PROFILE_TYPE = 'profile_type'; //Loại hồ sơ
    const PROFILE_MODEL = 'profile_model'; //Kiểu hồ sơ
    const PROFILE_STAGE = 'profile_stage'; //Giai đoạn lập hồ sơ
    const DOCUMENT_TYPE = 'document_type'; //Loại văn bản
    const DOCUMENT_MODEL = 'document_model'; //Kiểu văn bản
    const CONTRACT_TYPE = 'contract_type'; //Loại Hợp đồng
    const CONSTRUCTION_LEVEL = 'construction_level'; //Cấp công trình
    const LIBRARY_TYPE = 'library_type'; //Loại tài liệu
    const COMPANY = 'company'; //cơ quan ban hành

    //Các thông tin liên quan đến member

    const  STAFF_TON_GIAO = 'staff_ton_giao';
    const  STAFF_DAN_TOC = 'staff_dan_toc';
    const  STAFF_QUOC_TICH = 'staff_quoc_tich';
    const  STAFF_GIAY_TO = 'staff_giay_to';
    const  STAFF_NGAN_HANG = 'staff_ngan_hang';
    const  STAFF_NGOAI_NGU = 'staff_ngoai_ngu';
    const  STAFF_TO_CHUC_DOAN_THE = 'staff_to_chuc_doan_the';
    const  STAFF_CHUC_VU_DOAN_THE = 'staff_chuc_vu_doan_the';
    const  STAFF_LIEN_HE_KHAC = 'staff_lien_he_khac';
    const  STAFF_NGHE_NGHIEP = 'staff_nghe_nghiep';
    const  STAFF_BANG_CAP = 'staff_bang_cap';
    const  STAFF_CHUNG_CHI = 'staff_chung_chi';
    const  STAFF_NOI_CAP_BANG_CAP = 'staff_noi_cap_bang_cap';
    const  STAFF_CHUYEN_MON = 'staff_chuyen_mon';
    const  STAFF_CHUYEN_NGANH = 'staff_chuyen_nganh';


    const TOOL_EXTRA = 'tool_extra';
    const TOOL_EXTRA_2 = 'tool_extra_2';
    const POSITION = 'position'; //Chức vụ
    const PROJECT = 'project'; //Chức vụ
    const DEPARTMENT = 'department'; //Phòng ban
    const LOCATION_REGION = 'location_region';
    const LOCATION_COUNTRY = 'location_country';
    static $allType = [];


    const DEPARTMENT_LEVEL = [
        'level_1' => [
            'id' => 'level_1',
            'name' => 'Phòng ban cha',
        ], 'level_2' => [
            'id' => 'level_2',
            'name' => 'Phòng ban con',
        ],
    ];

    static $typeRegister = [
        self::TOOL_EXTRA => [
            'key' => self::TOOL_EXTRA,
            'name' => 'Công cụ tiện ích',
            'object' => 'other',
        ],
        self::TOOL_EXTRA_2 => [
            'key' => self::TOOL_EXTRA_2,
            'name' => 'Công cụ tiện ích (bổ sung)',
            'object' => 'other',
        ],
        self::POSITION => [
            'key' => 'position',
            'name' => 'Chức vụ, vị trí nhân viên',
            'object' => 'staff',
        ],
        self::DEPARTMENT => [
            'key' => 'department',
            'name' => 'Phòng ban',
            'object' => 'staff',
        ],
        self::LOCATION_REGION => [
            'key' => 'location_region',
            'name' => 'Vùng miền, tỉnh thành ',
        ],
        self::LOCATION_COUNTRY => [
            'key' => 'location_country',
            'name' => 'Quốc gia, lãnh thổ ',
        ],
        self::SERVICE_GROUP => [
            'key' => 'service_group',
            'name' => 'Nhóm Dịch vụ',
            'object' => 'profile',
        ],
        self::PROJECT_TYPE => [
            'key' => 'project_type',
            'name' => 'Loại hình Dự án',
            'object' => 'profile',
        ],
        self::PROJECT_MODEL => [
            'key' => 'project_model',
            'name' => 'Hạng mục Dự án',
            'object' => 'profile',
        ],
        self::PROFILE_TYPE => [
            'key' => 'profile_type',
            'name' => 'Loại Hồ sơ',
            'object' => 'profile',
        ],
        self::PROFILE_MODEL => [
            'key' => 'profile_model',
            'name' => 'Kiểu Hồ sơ',
            'object' => 'profile',
        ],
        self::PROFILE_STAGE => [
            'key' => 'profile_stage',
            'name' => 'Giai đoạn lập Hồ sơ',
            'object' => 'profile',
        ],
        self::DOCUMENT_TYPE => [
            'key' => 'document_type',
            'name' => 'Loại Văn bản',
            'object' => 'document',
        ],
        /*self::DOCUMENT_MODEL => [
            'key'  => 'document_model',
            'name'   => 'Kiểu Văn bản',
            'object' => 'document',
        ],*/
        /*self::CONTRACT_TYPE => [
            'key'  => 'contract_type',
            'name'   => 'Loại Hợp đồng',
            'object' => 'contract',
        ],*/
        self::CONSTRUCTION_LEVEL => [
            'key' => 'construction_level',
            'name' => 'Cấp công trình',
            'object' => 'contract',
        ],
        self::LIBRARY_TYPE => [
            'key' => 'library_type',
            'name' => 'Loại tài liệu tham khảo',
            'object' => 'library',
        ],
        self::COMPANY => [
            'key' => 'company',
            'name' => 'Cơ quan ban hành',
            'object' => 'company',
        ],
        self::STAFF_TON_GIAO => [
            'key' => 'staff_ton_giao',
            'name' => 'Tôn giáo',
            'object' => 'staff'
        ],
        self::STAFF_DAN_TOC => [
            'key' => 'staff_dan_toc',
            'name' => 'Dân tộc',
            'object' => 'staff'
        ], self::STAFF_GIAY_TO => [
            'key' => 'staff_giay_to',
            'name' => 'Giấy tờ',
            'object' => 'staff'
        ], self::STAFF_NGAN_HANG => [
            'key' => 'staff_ngan_hang',
            'name' => 'Ngân hàng',
            'object' => 'staff'
        ], self::STAFF_NGOAI_NGU => [
            'key' => 'staff_ngoai_ngu',
            'name' => 'Ngoại ngữ',
            'object' => 'staff'
        ], self::STAFF_TO_CHUC_DOAN_THE => [
            'key' => 'staff_to_chuc_doan_the',
            'name' => 'Tổ chức đoàn thể',
            'object' => 'staff'
        ], self::STAFF_CHUC_VU_DOAN_THE => [
            'key' => 'staff_chuc_vu_doan_the',
            'name' => 'Chức vụ đoàn thể',
            'object' => 'staff'
        ], self::STAFF_LIEN_HE_KHAC => [
            'key' => 'staff_lien_he_khac',
            'name' => 'Liên hệ khác',
            'object' => 'staff'
        ], self::STAFF_NGHE_NGHIEP => [
            'key' => 'staff_nghe_nghiep',
            'name' => 'Nghề nghiệp',
            'object' => 'staff'
        ], self::STAFF_BANG_CAP => [
            'key' => 'staff_bang_cap',
            'name' => 'Bằng cấp',
            'object' => 'staff'
        ], self::STAFF_CHUNG_CHI => [
            'key' => 'staff_chung_chi',
            'name' => 'Chứng chỉ',
            'object' => 'staff'
        ], self::STAFF_NOI_CAP_BANG_CAP => [
            'key' => 'staff_noi_cap_bang_cap',
            'name' => 'Nơi cấp bằng cấp',
            'object' => 'staff'
        ], self::STAFF_CHUYEN_MON => [
            'key' => 'staff_chuyen_mon',
            'name' => 'Chuyên môn',
            'object' => 'staff'
        ], self::STAFF_CHUYEN_NGANH => [
            'key' => 'staff_chuyen_nganh',
            'name' => 'Chuyên ngành',
            'object' => 'staff'
        ],

    ];

    const BO_VA_CO_QUAN_NGANG_BO = 'bo-va-co-quan-ngang-bo';
    const UBND_TINH = 'ubnd-tinh';
    const LIEN_BO = 'lien-bo';
    const THU_TUONG = 'thu-tuong';
    const CHINH_PHU = 'chinh-phu';
    const QUOC_HOI = 'quoc-hoi';
    const TEXO = 'texo';

    const COMPANY_ISSUED = [
        self::BO_VA_CO_QUAN_NGANG_BO => [
            'key' => 'bo-va-co-quan-ngang-bo',
            'name' => "Bộ & Cơ quan ngang bộ"
        ],

        self::CHINH_PHU => [
            'key' => 'chinh-phu',
            'name' => "Chính phủ"
        ],
        self::TEXO => [
            'key' => 'texo',
            'name' => "Công ty Cổ phần TEXO Tư vấn và Đầu tư"
        ],
        self::LIEN_BO => [
            'key' => 'lien-bo',
            'name' => "Liên bộ"
        ],

        self::QUOC_HOI => [
            'key' => 'quoc-hoi',
            'name' => "Quốc Hội"
        ],

        self::UBND_TINH => [
            'key' => 'ubnd-tinh',
            'name' => "UBND Tỉnh/ Thành phố"
        ],

        self::THU_TUONG => [
            'key' => 'thu-tuong',
            'name' => "Thủ tướng"
        ],

    ];

    static $objectRegister = [
        'staff' => [
            'key' => 'staff',
            'name' => 'Thuộc về nhân sự',
        ],
    ];

    public static function buildLinkDelete($object, $router = 'meta')
    {
        return admin_link('meta/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

    static function getAllByObject($object)
    {
        $where = [
            'object' => $object,
            'removed' => 'no',
        ];

        return self::where($where)->get();
    }

    static function getAllByType($type = "")
    {
        $where = [];
        if ($type) {
            $where['type'] = $type;
        }
        $where['removed'] = BaseModel::REMOVED_NO;
        return self::where($where)->get()->toArray();
    }

    static function getAllByTypeId($type = "")
    {
        $where = [];
        $where['removed'] = BaseModel::REMOVED_NO;
        if ($type) {
            $where['type'] = $type;
        } else {
            if (self::$allType) {
                return self::$allType;
            }
            self::$allType = self::where($where)->get()->keyBy('_id')->toArray();
            return self::$allType;
        }

        return self::where($where)->get()->keyBy('_id')->toArray();
    }





    static function getDepartmentInMyGroup()
    {
        $curMember = Member::getCurent();

        if (Role::isRoot()) {
            $where = [
                'type' => self::$typeRegister['department']['key'],
            ];

        } else if (isset($curMember['department']['id'])) {
            $where = [
                '_id' => Helper::getMongoId($curMember['department']['id'])
            ];
        } else {
            return [];
        }
        $curDep = self::where($where)->first();
        $listObj = [];

        if (isset($curDep['group'])) {
            $listObj = self::where(
                [
                    'group' => $curDep['group'],
                    'type' => self::$typeRegister['department']['key'],
                ]
            )->get();
        }
        return $listObj;

    }




    static $member_filter = [
        [
            'field_key' => 'quoc_tich.key',
            'field_label' => 'Quốc tịch',
            'placeholder' => '',
            'type' => 'select',
            'meta_key' => '',
        ],
        [
            'field_key' => 'tinh_trang_hon_nhan',
            'field_label' => 'Tình trạng hôn nhân',
            'placeholder' => '',
            'type' => ''
        ]
    ];
    static $member_data_filter =
        [
            "_id",
            "account" => [
                "field_key" => "account",
                "field_label" => "Tài khoản",
                "type" => 'text'
            ],
            "name" => [
                "field_key" => "name",
                "field_label" => "Tên",
                "type" => 'text',

            ],
            "gender" => [
                "field_key" => "gender",
                "field_label" => 'Giới tính',
                'type' => 'select',
                'options' => [
                    [
                        "text" => "Nam",
                        "id" => 'male'
                    ],
                    [
                        "text" => "Nữ",
                        "id" => 'female'
                    ]

                ]
            ],
            "noi_sinh" => [
                "field_key" => "noi_sinh.key",
                "field_label" => 'Nơi sinh',
                "type" => 'text'
            ],
            "date_of_birth" => [
                "field_key" => "date_of_birth",
                'field_label' => 'Ngày sinh',
                'type' => 'date',
            ],
            "nguyen_quan" => [
                "field_key" => "nguyen_quan",
                "field_label" => "Nguyên quán",
                'type' => 'text',

            ],
            "quoc_tich.id" => [
                "field_key" => "quoc_tich.id",
                'field_label' => 'Quốc tịch',
                'type' => 'select',
                'metadata_type' => self::STAFF_QUOC_TICH
            ],
            "tinh_trang_hon_nhan" => [
                "field_key" => "tinh_trang_hon_nhan",
                'field_label' => 'Tình trạng hôn nhân',
                'type' => 'select',
                'options' => [
                    [
                        "text" => "Độc thân",
                        "id" => 'Độc thân'
                    ],
                    [
                        "text" => "Kết hôn",
                        "id" => 'Kết hôn'
                    ]

                ]
            ],
            "tien_an_tien_su" => [
                "field_key" => "tien_an_tien_su",
                'field_label' => 'Tiền án tiền sự',
                'options' => [
                    [
                        "text" => "Có",
                        "id" => 'có'
                    ],
                    [
                        "text" => "Không",
                        "id" => 'không'
                    ]

                ],
                'type' => 'select',
            ],
            "ho_khau_thuong_chu" => [
                "field_key" => "ho_khau_thuong_chu",
                'field_label' => 'Hộ khẩu thường trú',
                'type' => 'text',
                'search_fields' => [
                    'ho_khau_thuong_chu.chi_tiet',
                    'ho_khau_thuong_chu.tinh.key',
                    'ho_khau_thuong_chu.huyen.key',
                    'ho_khau_thuong_chu.xa.key',
                ]
            ],
            "noi_o_hien_nay" => [
                "field_key" => "noi_o_hien_nay",
                'field_label' => 'Nơi ở hiện nay',
                'search_fields' => [
                    'ho_khau_thuong_chu.chi_tiet',
                    'ho_khau_thuong_chu.tinh.key',
                    'ho_khau_thuong_chu.huyen.key',
                    'ho_khau_thuong_chu.xa.key',
                ],
                'type' => 'text'
            ],
            "code" => [
                "field_key" => "code",
                'field_label' => 'Mã nhân viên',
                "type" => 'text'
            ],
            "emails.value" => [
                "field_key" => "emails.value",
                'field_label' => 'Địa chỉ email',
                "type" => 'text'
            ],
//        , "department" => [
//            "field_key" => "department",
//            'field_label' => 'Phòng ban'
//        ]
//        , "position" => [
//            "field_key" => "position",
//            'field_label' => 'Vị trí'
//        ]
//        , "role_group" => [
//            "field_key" => "role_group",
//            'field_label' => 'Nhóm quyền'
//        ]

            "giay_to.id" => [
                "field_key" => "giay_to.id",
                'field_label' => 'Giấy tờ',
                'type' => 'select',
                'metadata_type' => self::STAFF_GIAY_TO,

            ],
            "lien_he_khac.id" => [
                "field_key" => "lien_he_khac.id",
                'field_label' => 'Liên hệ khác',
                'type' => 'select',
                'metadata_type' => self::STAFF_LIEN_HE_KHAC,
            ],
            "phones.value" => [
                "field_key" => "phones.value",
                "field_label" => "Số điện thoại",
                "type" => 'text'
            ],
            "tk_ngan_hang.id" => [
                "field_key" => "tk_ngan_hang.id",
                'field_label' => 'Tài khoản ngân hàng',
                "type" => 'select',
                'metadata_type' => self::STAFF_NGAN_HANG,

            ],
            "to_chuc_doan_the.id" => [
                "field_key" => "to_chuc_doan_the.id",
                'field_label' => 'Tổ chức đoàn thể',
                'type' => 'select',
                'metadata_type' => self::STAFF_TO_CHUC_DOAN_THE,

            ],
            "dan_toc.id" => [
                "field_key" => "dan_toc.id",
                'field_label' => 'Dân tộc',
                'type' => 'select',
                'metadata_type' => self::STAFF_DAN_TOC,

            ],
//        , "date_of_birth" => [
//            "field_key" => "date_of_birth",
//            'field_label' => 'Ngày sinh nhật',
//            'type' => 'date'
//        ]
            "ma_so_thue" => [
                "field_key" => "ma_so_thue",
                'field_label' => 'Mã số thuế',
                'type' => 'text'
            ],
            "ngoai_ngu.id" => [
                "field_key" => "ngoai_ngu.id",
                'field_label' => 'Ngoại ngữ',
                'type' => 'select',
                'metadata_type' => self::STAFF_NGOAI_NGU,

            ],
            "so_bhxh" => [
                "field_key" => "so_bhxh",
                'field_label' => 'Số bảo hiểm xã hội',
                'type' => 'text'
            ],
            "ton_giao" => [
                "field_key" => "ton_giao.id",
                'type' => 'select',
                'field_label' => 'Tôn giáo',
                'metadata_type' => self::STAFF_TON_GIAO,
            ],
//        , "qua_trinh_cong_tac" => [
//            "field_key" => "qua_trinh_cong_tac",
//            'field_label' => 'Quá trình công tác'
//        ]
//        , "thong_tin_hop_dong_lao_dong" => [
//            "field_key" => "thong_tin_hop_dong_lao_dong",
//            'field_label' => 'Thông tin hợp đồng lao động'
//        ]
            "bang_cap.loai_bang_cap.id" => [
                'group' => 'bang_cap',
                'group_name' => 'Bằng cấp',
                "field_key" => "bang_cap.loai_bang_cap.id",
                'field_label' => 'Bằng cấp',
                'type' => 'select',
                'metadata_type' => self::STAFF_BANG_CAP,

            ],
            "bang_cap.chuyen_mon.id" => [
                'group' => 'bang_cap',
                'group_name' => 'Bằng cấp',
                "field_key" => "bang_cap.chuyen_mon.id",
                'field_label' => 'Chuyên môn',
                'type' => 'select',
                'metadata_type' => self::STAFF_CHUYEN_MON,

            ],
            "bang_cap.chuyen_nganh.id" => [
                'group' => 'bang_cap',
                'group_name' => 'Bằng cấp',
                "field_key" => "bang_cap.chuyen_nganh.id",
                'field_label' => 'Chuyên ngành',
                'type' => 'select',
                'metadata_type' => self::STAFF_CHUYEN_NGANH,

            ],
            "bang_cap.noi_cap.id" => [
                'group' => 'bang_cap',
                'group_name' => 'Bằng cấp',
                "field_key" => "bang_cap.noi_cap.id",
                'field_label' => 'Nơi cấp bằng',
                'type' => 'select',
                'metadata_type' => self::STAFF_NOI_CAP_BANG_CAP,

            ],

            "chung_chi_dao_tao.loai_chung_chi.id" => [
                'group' => 'chung_chi_dao_tao',
                'group_name' => 'Chứng chỉ đào tạo',
                "field_key" => "chung_chi_dao_tao.loai_chung_chi.id",
                'field_label' => 'Loại chứng chỉ',
                'type' => 'select',
                'metadata_type' => self::STAFF_CHUNG_CHI,

            ],

            "chung_chi_dao_tao.noi_cap.id" => [
                'group' => 'chung_chi_dao_tao',
                'group_name' => 'Chứng chỉ đào tạo',
                "field_key" => "chung_chi_dao_tao.noi_cap.id",
                'field_label' => 'Nơi cấp',
                'type' => 'select',
                'metadata_type' => self::STAFF_NOI_CAP_BANG_CAP,
            ],
            "chung_chi_dao_tao.ngay_cap" => [
                'group' => 'chung_chi_dao_tao',
                'group_name' => 'Chứng chỉ đào tạo',
                "field_key" => "chung_chi_dao_tao.ngay_cap",
                'field_label' => 'Ngày cấp',
                'type' => 'date',
            ],

            "chung_chi_dao_tao.hang_chung_chi" => [
                'group' => 'chung_chi_dao_tao',
                'group_name' => 'Chứng chỉ đào tạo',
                "field_key" => "chung_chi_dao_tao.hang_chung_chi",
                'field_label' => 'Hạng chứng chỉ',
                'type' => 'text',
            ],


            "thong_tin_hop_dong_lao_dong.tinh_trang" => [
                'group' => 'thong_tin_hop_dong_lao_dong',
                'group_name' => 'Hợp đồng lao động',
                "field_key" => "thong_tin_hop_dong_lao_dong.tinh_trang",
                'field_label' => 'Tình trạng',
                'type' => 'select',
                "options" => [
                    ["id" => "Đang công tác", "text" => "Đang công tác"],
                    ["id" => "Đã nghỉ việc", "text" => "Đã nghỉ việc"],
                    ["id" => "Tạm nghỉ", "text" => "Tạm nghỉ"],

                ],
            ],
            "thong_tin_hop_dong_lao_dong.loai_hop_dong" => [
                'group' => 'thong_tin_hop_dong_lao_dong',
                'group_name' => 'Hợp đồng lao động',
                "field_key" => "thong_tin_hop_dong_lao_dong.loai_hop_dong",
                'field_label' => 'Loại hợp đồng lao động',
                'type' => 'select',
                "options" => [
                    ["id" => "Cộng tác viên",
                        "text" => "Cộng tác viên"
                    ],
                    ["id" => "Ngắn hạn",
                        "text" => "Ngắn hạn"
                    ],
                    ["id" => "Có thời hạn",
                        "text" => "Có thời hạn"
                    ],
                    ["id" => "Không xác định",
                        "text" => "Không xác định"
                    ],
                    ["id" => "Chưa ký Hợp đồng",
                        "text" => "Chưa ký Hợp đồng"
                    ],
                ]

            ],
            "thong_tin_hop_dong_lao_dong.ngay_bat_dau" => [
                'group' => 'thong_tin_hop_dong_lao_dong',
                'group_name' => 'Hợp đồng lao động',
                "field_key" => "thong_tin_hop_dong_lao_dong.ngay_bat_dau",
                'field_label' => 'Ngày bắt đầu hợp đồng',
                'type' => 'date',

            ],
            "thong_tin_hop_dong_lao_dong.ngay_ket_thuc" => [
                'group' => 'thong_tin_hop_dong_lao_dong',
                'group_name' => 'Hợp đồng lao động',
                "field_key" => "thong_tin_hop_dong_lao_dong.ngay_ket_thuc",
                'field_label' => 'Ngày kết thúc hợp đồng',
                'type' => 'date',

            ],


            "qua_trinh_cong_tac.department.id" => [
                'group' => 'qua_trinh_cong_tac',
                'group_name' => 'Quá trình công tác',
                "field_key" => "qua_trinh_cong_tac.department",
                'field_label' => 'Phòng ban',
                'type' => 'select',
                'metadata_type' => self::DEPARTMENT,

            ],
            "qua_trinh_cong_tac.position.id" => [
                'group' => 'qua_trinh_cong_tac',
                'group_name' => 'Quá trình công tác',
                "field_key" => "qua_trinh_cong_tac.position.id",
                'field_label' => 'Vai trò đảm nhiệm	',
                'type' => 'select',
                'metadata_type' => self::POSITION,


            ],
            "qua_trinh_cong_tac.ngay_bat_dau" => [
                'group' => 'qua_trinh_cong_tac',
                'group_name' => 'Quá trình công tác',
                "field_key" => "qua_trinh_cong_tac.ngay_bat_dau",
                'field_label' => 'Ngày bắt đầu ',
                'type' => 'date',

            ],
            "qua_trinh_cong_tac.ngay_ket_thuc" => [
                'group' => 'qua_trinh_cong_tac',
                'group_name' => 'Quá trình công tác',
                "field_key" => "qua_trinh_cong_tac.ngay_ket_thuc",
                'field_label' => 'Ngày kết thúc',
                'type' => 'date',

            ],
            "qua_trinh_cong_tac.project.id" => [
                'group' => 'qua_trinh_cong_tac',
                'group_name' => 'Quá trình công tác',
                "field_key" => "qua_trinh_cong_tac.project.id",
                'field_label' => 'Dự án tham gia',
                'type' => 'select',
                'metadata_type' => self::PROJECT,
                //todo

            ],


            "thong_tin_gia_dinh.ho_ten" => [
                'group' => 'thong_tin_gia_dinh',
                'group_name' => 'Thông tin gia đình',
                "field_key" => "thong_tin_gia_dinh.ho_ten",
                'field_label' => 'Tên thành viên',
                'type' => 'text',


            ],
            "thong_tin_gia_dinh.ngay_sinh" => [
                'group' => 'thong_tin_gia_dinh',
                'group_name' => 'Thông tin gia đình',
                "field_key" => "thong_tin_gia_dinh.ngay_sinh",
                'field_label' => 'Ngày sinh',
                'type' => 'date',

            ],


            "thong_tin_gia_dinh.moi_quan_he_gia_dinh" => [
                'group' => 'thong_tin_gia_dinh',
                'group_name' => 'Thông tin gia đình',
                "field_key" => "thong_tin_gia_dinh.moi_quan_he_gia_dinh",
                'field_label' => 'Mối quan hệ',
                'type' => 'select',
                'options' => [
                    ['id' => "Anh trai", 'text' => "Anh trai"],
                    ['id' => "Em trai", 'text' => "Em trai"],
                    ['id' => "Chị gái", 'text' => "Chị gái"],
                    ['id' => "Em gái", 'text' => "Em gái"],
                    ['id' => "Bố đẻ", 'text' => "Bố đẻ"],
                    ['id' => "Bố dượng", 'text' => "Bố dượng"],
                    ['id' => "Mẹ đẻ", 'text' => "Mẹ đẻ"],
                    ['id' => "Mẹ kế", 'text' => "Mẹ kế"],
                    ['id' => "Con gái", 'text' => "Con gái"],
                    ['id' => "Con trai", 'text' => "Con trai"],
                    ['id' => "Con nuôi", 'text' => "Con nuôi"],
                    ['id' => "Vợ", 'text' => "Vợ"],
                    ['id' => "Chồng", 'text' => "Chồng"],
                ]

            ],
            "thong_tin_gia_dinh.tinh_trang" => [
                'group' => 'thong_tin_gia_dinh',
                'group_name' => 'Thông tin gia đình',
                "field_key" => "thong_tin_gia_dinh.tinh_trang",
                'field_label' => 'Tình trạng',
                'type' => 'select',
                'options' => [
                    ["id" => "còn sống", 'text' => "còn sống",],
                    ["id" => "Đã mất", 'text' => "Đã mất",],

                ]

            ],
            "thong_tin_gia_dinh.nghe_nghiep.id" => [
                'group' => 'thong_tin_gia_dinh',
                'group_name' => 'Thông tin gia đình',
                "field_key" => "thong_tin_gia_dinh.nghe_nghiep.id",
                'field_label' => 'Nghề nghiệp',
                'type' => 'select',
                'metadata_type' => self::STAFF_NGHE_NGHIEP,

            ]



//        , "chung_chi_dao_tao" => [
//            "field_key" => "chung_chi_dao_tao",
//            'field_label' => 'Chứng chỉ đào tạo'
//        ]
//        , "thong_tin_gia_dinh" => [
//            "field_key" => "thong_tin_gia_dinh",
//            'field_label' => 'Thông tin gia đình'
//        ]
        ];

}
