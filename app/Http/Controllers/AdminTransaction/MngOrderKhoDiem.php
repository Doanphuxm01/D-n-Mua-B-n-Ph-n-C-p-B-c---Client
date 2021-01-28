<?php


namespace App\Http\Controllers\AdminTransaction;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\Customer;
use App\Http\Models\Member;
use App\Http\Models\Role;
use App\Http\Models\Transaction;
use Illuminate\Http\Request;

class MngOrderKhoDiem extends Controller
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
        HtmlHelper::getInstance()->setTitle('Quản lý lịch sử giao dịch kho điểm');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $select_field = Request::capture()->input('select_field', []);

        $action = Request::capture()->input('action', 0);
        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;
        $tpl['title_module'] = 'lịch sử giao dịch kho điểm';
        $curentMemberAccount = Member::getCurentAccount();
        $where = [];

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $where['type_giaodich'] = Transaction::KHODIEM_TIEUDUNG;

        $listObj = Transaction::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('tai_khoan_nhan.account', 'LIKE', '%' . $q . '%');
        }
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = $listObj->where(
            [
                '$or' => [
                    [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                    [ "tai_khoan_nguon.account" => $curentMemberAccount ],

                ]
            ]);
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;
        $listObjCustomersInOrders = [];
        foreach ($listObj as $obj) {
            $listObjCustomersInOrders[] = $obj['tai_khoan_nhan']['account'];
            $listObjCustomersInOrders[] = $obj['tai_khoan_nguon']['account'];
        }
        $listObjCustomersInCustomer = Customer::whereIn('account', $listObjCustomersInOrders)->select('name', 'status', 'verified', '_id', 'account', 'fullname')->get()->keyBy('account')->toArray();
        $tpl['listObjCustomersInCustomer'] = $listObjCustomersInCustomer;
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
                                    '$or' => [
                                        [ "tai_khoan_nhan.account" => $curentMemberAccount ],
                                        [ "tai_khoan_nguon.account" => $curentMemberAccount ],
                                    ],
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                    'type_giaodich' => Transaction::KHODIEM_TIEUDUNG,
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
                                    'type_giaodich' => Transaction::KHODIEM_TIEUDUNG,
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