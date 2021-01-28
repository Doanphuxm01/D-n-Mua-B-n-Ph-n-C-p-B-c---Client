<?php


namespace App\Http\Controllers\AdminTransaction;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Customer;
use App\Http\Models\Member;
use App\Http\Models\Transaction;
use Illuminate\Http\Request;

class MngOrderChietKhau extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->_list();
        }
    }

    public function _list()
    {
        HtmlHelper::getInstance()->setTitle('Quản lý lịch sử giao dịch ví chiết khấu');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;
        $tpl['title_module'] = 'lịch sử giao dịch ví chiết khấu';
        $curentMemberAccount = Member::getCurentAccount();
        $where = [];

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $where['type_giaodich'] = Transaction::DIEM_CHIETKHAU;

        $listObj = Transaction::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('tai_khoan_nhan.name', 'LIKE', '%' . $q . '%')
                ->OrWhere('tai_khoan_nhan.phone', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('tai_khoan_nhan.account', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('tai_khoan_nhan.email', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('tai_khoan_nhan.addr', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('so_diem_can_mua', 'LIKE', '%' . trim($q) . '%');
        }
        $listObj = $listObj->where(
            [
                '$or' => [
                    [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                    [ "tai_khoan_nguon.account" => $curentMemberAccount ],

                ]
            ]);
        $listObj = $listObj->orderBy('_id', 'desc');

        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;
        $listObjCustomersInOrders = [];
        foreach ($listObj as $obj) {
            $listObjCustomersInOrders[] = $obj['tai_khoan_nhan']['account'];
        }
        $listObjCustomersInCustomer = Customer::whereIn('_id', $listObjCustomersInOrders)->select('name', 'status', 'verified', '_id', 'account', 'fullname')->get()->keyBy('_id')->toArray();
        $tpl['listObjCustomersInCustomer'] = $listObjCustomersInCustomer;
        $resultQuery = \DB::table(Transaction::table_name)->raw(function ($collection) use ($curentMemberAccount)  {
            return $collection->aggregate([
                [
                    '$facet' => [
                        'danh_sach_tien_don_theo_tung_trang_thai' => [
                            [
                                '$match' => [
                                    '$or' => [
                                        [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                                        [ "tai_khoan_nguon.account" => $curentMemberAccount ],

                                    ],
                                    'diem_da_nhan' => [
                                        '$exists' => true,
                                        '$gt' => 0,
                                    ],
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$type_giaodich',
                                    'total' => [
                                        '$sum' => '$diem_da_nhan',
                                    ],
                                ],
                            ],
                        ],
                        'danh_sach_so_luong_don_theo_tung_trang_thai' => [
                            [
                                '$match' => [
                                    '$or' => [
                                        [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                                        [ "tai_khoan_nguon.account" => $curentMemberAccount ],

                                    ],
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$type_giaodich',
                                    'total' => [
                                        '$sum' => 1,
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ]);
        });
        if($resultQuery) {
            $re = $resultQuery->toArray();
            $re = Helper::BsonDocumentToArray($re);
            $tpl['moneyFilter'] = $re[0];
        }


        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }

    public function giao_dich_phan_tram_hang_ngay()
    {

        HtmlHelper::getInstance()->setTitle('Quản lý lịch sử giao dịch % hàng ngày');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 100);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $select_field = Request::capture()->input('select_field', []);

        $action = Request::capture()->input('action', 0);
        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;
        $tpl['title_module'] = 'lịch sử giao dịch % hàng ngày';

        $where = [];
        $curentMemberId = Member::getCurentId();
        $curentMemberAccount = Member::getCurentAccount();
        if ($q_status) {
            $where['status'] = $q_status;
        }
        $where = [
            '$or' => [
                ['type_giaodich' => Transaction::CHIETKHAU_TIEUDUNG],
                ['type_giaodich' => Transaction::CHIETKHAU_TICHLUY],
            ]
        ];
        $listObj = Transaction::where($where);
        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj
                ->where('tai_khoan_nguon.account', 'LIKE', '%' . $q . '%')
                ->OrWhere('tai_khoan_nguon.account', 'LIKE', '%' . $q . '%');
        }
        $listObj = $listObj->where(
            [
                '$or' => [
                    [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                    [ "tai_khoan_nguon.account" => $curentMemberAccount ],
                ]
            ]);
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;
        $listObjCustomersInOrders = [];
        $total = 0;
        foreach ($listObj as $obj) {
            $listObjCustomersInOrders[] = $obj['tai_khoan_nhan']['account'];
        }
        $listObjCustomersInCustomer = Customer::whereIn('account', $listObjCustomersInOrders)->select('name', 'status', 'verified', '_id', 'account', 'fullname')->get()->keyBy('account')->toArray();
        $tpl['listObjCustomersInCustomer'] = $listObjCustomersInCustomer;
        /*if ($q) {
            $agr = [
                '$facet' => [
                    'danh_sach_tien_don_theo_tung_trang_thai' => [
                        [
                            '$match' => [
                                'diem_da_nhan' => [
                                    '$exists' => true,
                                    '$gt' => 0,
                                ],
                                'tai_khoan_nguon.account' => $q,
                                'status' => [
                                    '$exists' => true,
                                ],
                                'type_giaodich' => Transaction::CHIETKHAU_TICHLUY,
                            ]
                        ],
                        [
                            '$group' => [
                                '_id' => '$type_giaodich',
                                'total' => [
                                    '$sum' => '$diem_da_nhan',
                                ],
                            ],
                        ],
                    ],
                    'danh_sach_so_luong_don_theo_tung_trang_thai' => [
                        [
                            '$match' => [
                                'status' => [
                                    '$exists' => true,
                                ],
                                'tai_khoan_nguon.account' => $q,
                                'type_giaodich' => Transaction::CHIETKHAU_TICHLUY,
                            ]
                        ],
                        [
                            '$group' => [
                                '_id' => '$type_giaodich',
                                'total' => [
                                    '$sum' => 1,
                                ],
                            ],
                        ],
                    ]
                ]
            ];
        }else {
            $agr = [
                '$facet' => [
                    'danh_sach_tien_don_theo_tung_trang_thai' => [
                        [
                            '$match' => [
                                'diem_da_nhan' => [
                                    '$exists' => true,
                                    '$gt' => 0,
                                ],
                                'status' => [
                                    '$exists' => true,
                                ],
                                'type_giaodich' => Transaction::CHIETKHAU_TICHLUY,
                            ]
                        ],
                        [
                            '$group' => [
                                '_id' => '$type_giaodich',
                                'total' => [
                                    '$sum' => '$diem_da_nhan',
                                ],
                            ],
                        ],
                    ],
                    'danh_sach_so_luong_don_theo_tung_trang_thai' => [
                        [
                            '$match' => [
                                'status' => [
                                    '$exists' => true,
                                ],
                                'type_giaodich' => Transaction::CHIETKHAU_TICHLUY,
                            ]
                        ],
                        [
                            '$group' => [
                                '_id' => '$type_giaodich',
                                'total' => [
                                    '$sum' => 1,
                                ],
                            ],
                        ],
                    ]
                ]
            ];
        }*/
        $resultQuery = \DB::table(Transaction::table_name)->raw(function ($collection) use ($q, $curentMemberAccount)  {
            return $collection->aggregate([
                [
                    '$facet' => [
                        'danh_sach_tien_don_theo_tung_trang_thai' => [
                            [
                                '$match' => [
                                    'diem_da_nhan' => [
                                        '$exists' => true,
                                        '$gt' => 0,
                                    ],
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                    '$or' => [
                                        [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                                        [ "tai_khoan_nguon.account" => $curentMemberAccount ],
                                    ],
                                    'type_giaodich' => Transaction::CHIETKHAU_TICHLUY,
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$type_giaodich',
                                    'total' => [
                                        '$sum' => '$diem_da_nhan',
                                    ],
                                ],
                            ],
                        ],
                        'danh_sach_so_luong_don_theo_tung_trang_thai' => [
                            [
                                '$match' => [
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                    '$or' => [
                                        [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                                        [ "tai_khoan_nguon.account" => $curentMemberAccount ],
                                    ],
                                    'type_giaodich' => Transaction::CHIETKHAU_TICHLUY,
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$type_giaodich',
                                    'total' => [
                                        '$sum' => 1,
                                    ],
                                ],
                            ],
                        ]
                    ]
                ]
            ]);
        });

        if($resultQuery) {
            $re = $resultQuery->toArray();
            $re = Helper::BsonDocumentToArray($re);
            $tpl['moneyFilter'] = $re[0];
        }
        if ($action && $action == 'export_excel') {
            $tpl['listObj'] = $listObj;
            $tpl['select_field'] = $select_field;
            return eView::getInstance()->setViewBackEnd(__DIR__, 'OrderCk.table-to-excel', $tpl);
        }

        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }
}