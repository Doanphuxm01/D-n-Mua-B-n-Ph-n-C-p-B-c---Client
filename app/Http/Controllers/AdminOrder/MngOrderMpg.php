<?php

/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 5/16/2016
 * Time: 12:24 PM
 */

namespace App\Http\Controllers\AdminOrder;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Models\BaseModel;
use App\Http\Models\Car;
use App\Http\Models\Customer;
use App\Http\Models\Logs;
use App\Http\Models\Media;
use App\Http\Models\Member;
use App\Http\Models\Menu;
use App\Http\Models\Orders;
use App\Http\Models\Role;
use App\Http\Models\Transaction;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTieuDung;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MngOrderMpg extends Controller
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

    /***
     * Danh sách thành vien
     *
     * @url: admin/customer/list
     */
    public function _list()
    {
        HtmlHelper::getInstance()->setTitle('Quản lý đơn mua điểm');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;
        $curentMemberId = Member::getCurentId();
        $curentMemberAccount = Member::getCurentAccount();
        $where = [];
        $where['created_by.account'] = $curentMemberAccount;

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $listObj = Orders::where($where);

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
        $listObjCustomersInOrders = [];
        foreach ($listObj as $obj) {
            $listObjCustomersInOrders[] = $obj['tai_khoan_nhan']['account'];
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
        }


        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }


    /***
     * Hàm cập nhật đơn hàng
     *
     * @url: admin/member/_save
     */
    public function _save()
    {

        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj', []);

        if ($id) {
            if (!Member::haveRole(Member::mng_member_update)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
            }
            $curentObj = Customer::find($id);
            if (!$curentObj) {
                return eView::getInstance()->getJsonError('Không tìm thấy đối tượng. Vui lòng kiểm tra lại');
            }
        } else {
            if (!Member::haveRole(Member::mng_member_add)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
            }
        }


        $savePost = [
            'name' => (isset($obj['name']) && $obj['name']) ? trim($obj['name']) : '',
            'email' => (isset($obj['email']) && $obj['email']) ? $obj['email'] : '',
            'phone' => (isset($obj['phone']) && $obj['phone']) ? $obj['phone'] : '',
            'password' => (isset($obj['password']) && $obj['password']) ? $obj['password'] : '',
            'gender' => (isset($obj['gender']) && $obj['gender']) ? $obj['gender'] : '',
            'addr' => (isset($obj['addr']) && $obj['addr']) ? $obj['addr'] : '',
            'image' => (isset($obj['image']) && $obj['image']) ? $obj['image'] : '',
            'verified' => (isset($obj['verified']) && $obj['verified']) ? $obj['verified'] : ['phone' => false, 'email' => false],
            'status' => (isset($obj['status']) && $obj['status']) ? $obj['status'] : Member::STATUS_ACTIVE,
            'actived_at' => (isset($obj['actived_at']) && $obj['actived_at']) ? Helper::getMongoDate($obj['actived_at']) : Helper::getMongoDate(),
            'end_at' => (isset($obj['end_at']) && $obj['end_at']) ? Helper::getMongoDate($obj['end_at']) : Helper::getMongoDate(),
            'birthday' => (isset($obj['birthday']) && $obj['birthday']) ? Helper::getMongoDate($obj['birthday']) : '',
            'updated_at' => Helper::getMongoDate(),
            //
        ];


        if (!$savePost['name']) {
            return eView::getInstance()->getJsonError('Bạn vui lòng nhập tên');
        }
        if (!Helper::isPhoneNumber($savePost['phone'])) {
            return eView::getInstance()->getJsonError('Số điện thoại không đúng định dạng');
        }
        if ($savePost['email'] && !Helper::isEmail($savePost['email'])) {
            return eView::getInstance()->getJsonError('Email không hợp lệ.');
        }
        $objByPhone = Customer::getByPhone($savePost['phone']);
        if ($objByPhone && $objByPhone->_id !== $id) {
            return eView::getInstance()->getJsonError('Số điện thoại này đã được đăng ký. Vui lòng chọn số khác');
        }
        if ($savePost['email']) {
            $objByEmail = Customer::getByEmail($savePost['email']);
            if ($objByEmail && $objByEmail->_id !== $id) {
                return eView::getInstance()->getJsonError('Email này đã được đăng ký. Vui lòng chọn email khác');

            }
        }


        if (!$id && !$savePost['password']) {
            return eView::getInstance()->getJsonError('Bạn vui lòng nhập mật khẩu sử dụng cho tài khoản này');
        } elseif ($id) {
            if (!$savePost['password']) {
                unset($savePost['password']);
            }
        }

        if (isset($savePost['password']) && $savePost['password']) {
            $savePost['password'] = Member::genPassSave($savePost['password']);
        }


        if ($id) {
            Customer::where('_id', $id)->update($savePost);
            Logs::createLog([
                'type' => Logs::TYPE_UPDATED,
                'data_object' => $savePost,
                'note' => "Khách hàng " . $savePost['phone'] . ' được sửa thông tin bởi ' . Member::getCurentAccount()
            ], 'customer');

        } else {
            $savePost['created_at'] = Helper::getMongoDate();
            $id = Customer::insertGetId($savePost);
            Logs::createLog([
                'type' => Logs::TYPE_CREATE,
                'data_object' => $savePost,
                'note' => "Khách hàng " . $savePost['phone'] . ' được thêm mới bởi ' . Member::getCurentAccount()
            ], 'customer');
        }

        $data['link'] = Menu::buildLinkAdmin('customer/input?id=' . $id);
        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $data);
    }

    public function _delete()
    {

        $id = Request::capture()->input('id', 0);
        $token = Request::capture()->input('token', 0);
        if (!Helper::validateToken($token, $id)) {
            return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        }

        $where = [
            'type' => Orders::ORDER_BUY_MPG,
            '_id' => $id,
            'created_by.account' => Member::getCurentId(),
        ];
        $order = Orders::where($where)->first();
        if(!$order) {
            return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        }
        $objToSave = [
            'status' => Orders::STATUS_DELETED,
            'removed' => Orders::REMOVED_YES,
            'deleted_at' => Helper::getMongoDate(),
            'deleted_by' => Member::getCreatedByToSaveDb(),
        ];
        $order->update($objToSave);
        Logs::createLog([
            'type' => Logs::TYPE_DELETE,
            'data_object' => $order->toArray(),
            'note' => "Đơn hàng " . $order['_id'] . ' bị xóa bởi ' . Member::getCurentAccount()
        ], 'customer');


        return eView::getInstance()->getJsonSuccess('Xóa đối tượng thành công. Bạn không thể khôi phục lại', []);
    }

    function quick_preview() {
        $tpl = [];
        $id = Request::capture()->input('id', 0);
        if ($id) {
            $tpl['id'] = $id;
        }

        $obj = Orders::where('_id', $id)->first();

        if ($obj) {
            $tpl['obj'] = $obj;
        }

        $currentMember = Member::getCurent();
        $tpl['currentMember'] = $currentMember;

        return eView::getInstance()->setViewBackEnd(__DIR__, 'popup.quick-preview-mpg', $tpl);
    }

}