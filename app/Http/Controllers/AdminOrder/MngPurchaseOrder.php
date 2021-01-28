<?php


namespace App\Http\Controllers\AdminOrder;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\Member;
use App\Http\Models\Orders;
use App\Http\Models\PurchaseOrder;
use Illuminate\Http\Request;

class MngPurchaseOrder extends Controller
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

    function _list() {
        // Danh sách đơn hàng
        HtmlHelper::getInstance()->setTitle('Quản lý đơn mua điểm');
        $tpl = array();

        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['title_module'] = 'đơn mua hàng';
        $tpl['q'] = $q;
        $curentMemberId = Member::getCurentId();
        $curentMemberAccount = Member::getCurentAccount();
        $where = [];
        $where['created_by.account'] = $curentMemberAccount;

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $listObj = PurchaseOrder::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('tai_khoan_nhan.name', 'LIKE', '%' . $q . '%')
                ->OrWhere('tai_khoan_nhan.phone', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('tai_khoan_nhan.account', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('tai_khoan_nhan.email', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('tai_khoan_nhan.addr', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('so_diem_can_mua', 'LIKE', '%' . trim($q) . '%');
        }
        $listObj = $listObj->orderBy('_id', 'desc');

        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');

        $tpl['listObj'] = $listObj;
        /*$listObjCustomersInOrders = [];
        foreach ($listObj as $obj) {
            $listObjCustomersInOrders[] = $obj['created_by']['account'];
        }
        $listObjCustomersInCustomer = Member::whereIn('_id', $listObjCustomersInOrders)->select('name', 'status', 'verified', '_id', 'account', 'fullname')->get()->keyBy('account')->toArray();
        $tpl['listObjCustomersInCustomer'] = $listObjCustomersInCustomer;
        $resultQuery = \DB::table(Orders::table_name)->raw(function ($collection) use ($q, $curentMemberId, $listObjCustomersInOrders)  {
            return $collection->aggregate([
                [
                    '$facet' => [
                        'danh_sach_tien_don_theo_tung_trang_thai' => [
                            [
                                '$match' => [
                                    'created_by.account' => [
                                        '$in' => $listObjCustomersInOrders
                                    ],
                                    'so_diem_can_mua' => [
                                        '$exists' => true,
                                        '$gt' => 0,
                                    ],
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$status',
                                    'total' => [
                                        '$sum' => '$so_diem_can_mua',
                                    ],
                                ],
                            ],
                        ],
                        'danh_sach_so_luong_don_theo_tung_trang_thai' => [
                            [
                                '$match' => [
                                    'created_by.account' => [
                                        '$in' => $listObjCustomersInOrders
                                    ],
                                    'status' => [
                                        '$exists' => true,
                                    ],
                                ]
                            ],
                            [
                                '$group' => [
                                    '_id' => '$status',
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
        }*/


        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }

    function quick_form() {
        $tpl = [];
        $id = Request::capture()->input('id', 0);
        if ($id) {
            $tpl['id'] = $id;
        }

        $obj = PurchaseOrder::where('_id', $id)->first();

        if ($obj) {
            $tpl['obj'] = $obj;
        }
        $currentMember = Member::getCurent();
        $tpl['currentMember'] = $currentMember;

        return eView::getInstance()->setViewBackEnd(__DIR__, 'popup.quick-preview', $tpl);
    }
}
