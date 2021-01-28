<?php


namespace App\Http\Controllers\AdminTransaction;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\Customer;
use App\Http\Models\Member;
use App\Http\Models\Transaction;
use Illuminate\Http\Request;

class MngOrderCongNo extends Controller
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
        HtmlHelper::getInstance()->setTitle('Quản lý lịch sử giao dịch ví công nợ');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;
        $tpl['title_module'] = 'lịch sử giao dịch ví công nợ';
        $curentMemberAccount = Member::getCurentAccount();
        $where = [];

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $where['type_giaodich'] = Transaction::DIEM_CONGNO;

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
                                    'type_giaodich' => Transaction::DIEM_CONGNO,
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
                                    'type_giaodich' => Transaction::DIEM_CONGNO,
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

}