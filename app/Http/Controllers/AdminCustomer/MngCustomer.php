<?php

/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 5/16/2016
 * Time: 12:24 PM
 */

namespace App\Http\Controllers\AdminCustomer;

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
use App\Http\Models\MetaData;
use App\Http\Models\Orders;
use App\Http\Models\Role;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTichLuy;
use App\Http\Models\ViTieuDung;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;

class MngCustomer extends Controller
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
        HtmlHelper::getInstance()->setTitle('Quản lý thành viên - khách hàng');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');
        //select Cái này dùng cho export
        $select_field = Request::capture()->input('select_field', []);
        $action = Request::capture()->input('action', 0);
        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;

        $where = [];

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $listObj = Customer::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('name', 'LIKE', '%' . $q . '%')
                ->OrWhere('phone', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('email', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('addr', 'LIKE', '%' . trim($q) . '%');
        }
        $listObj = $listObj->select(Customer::$basicFiledsForList)->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;
        if ($action && $action == 'export_excel') {
            // $listObj = $listObj->get();
            $tpl['listObj'] = $listObj;
            $tpl['select_field'] = $select_field;
            return eView::getInstance()->setViewBackEnd(__DIR__, 'table-to-excel', $tpl);
        }
        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }

    function view_tree() {
        HtmlHelper::getInstance()->setTitle('Quản lý thành viên - khách hàng');
        $tpl = array();
        $cond = [];
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;

        $where = [];

        if ($q_status) {
            $where['status'] = $q_status;
        }
        $select = ['name', 'account', '_id', 'parent_id', 'ma_gioi_thieu'];

        $itemPerPage = (int)Request::capture()->input('row', 500000);
        $listObj = BaseModel::table(Customer::table_name)->select($select)->where($where);
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $html = '';
        $data = $listObj->toArray();
        $lsTree = Customer::buildTree($data['data'], 0, [], 1);
        $html .= '<div class="col-md-6"><ol class="dd-list">'.$this->buildMenu($lsTree).'</ol></div>';

        $tpl['html'] = $html;
        $tpl['listObj'] = $listObj;
        return eView::getInstance()->setViewBackEnd(__DIR__, 'list-tree', $tpl);
    }

    function buildMenu($menu_data)
    {
        $html = "";
        if (isset($menu_data)) {
            foreach ($menu_data as $item) {
                if (empty($item['children'])) {
                    $html .= '<li class="dd-item" data-id="'.$item['_id'].'">
                                <div class="dd-handle">' . @$item['name'] . '
                                    <a title="Cập nhật thông tin" href="'.admin_link('/customer/input?id='.$item['_id']).'"
                                        class="float-right"> <i class="icon-pencil7"></i>
                                    </a>
                                </div>
                            </li>';
                }
                if (!empty($item['children'])) {
                    $html .= '<li class="dd-item" data-id="'.$item['_id'].'">
                            <div class="dd-handle">' . @$item['name'] . '
                                <a title="Cập nhật thông tin" href="'.admin_link('/customer/input?id='.$item['_id']).'"
                                   class="float-right"> <i class="icon-pencil7"></i>
                                </a>
                            </div>
                            <ol class="dd-list">';
                    $html .= $this->buildMenu($item['children']);
                    $html .= '</ol>';
                }
            }

        }
        return $html;
    }

    /***
     * Danh sách thành vien
     *
     * @url: admin/member/input
     */
    public function input()
    {
        if (!empty($_POST)) {
            return $this->_save();
        }
        $tpl = [];
        HtmlHelper::getInstance()->setTitle('Cập nhật thông tin khách hàng - Quản lý khách hàng');
        $id = Request::capture()->input('id', 0);
        if ($id) {
            #region check role
            $isAllow = Role::isAllowTo(Role::$ACTION_LIST. Role::$KAYN_CUSTOMER);
            if (!$isAllow) {
                return eView::getInstance()->getJsonError('Bạn không có quyền xem thông tin khách hàng này');
            }
            #endregion

        } else {
            #region check role
            $isAllow = Role::isAllowTo(Role::$ACTION_EDIT. Role::$KAYN_CUSTOMER);
            if (!$isAllow) {
                return eView::getInstance()->getJsonError('Bạn không có quyền cập nhật khách hàng');
            }
            #endregion
        }
        if ($id) {
            $obj = Customer::find($id);
            $tpl['image_avatar'] = Media::buildImageLink(isset($obj['image']) ? $obj['image'] : '');
            $tpl['obj'] = $obj;
        }

        if ($id && isset($obj) && $obj) {
            // case ViChietKhau
            $vichietkhau = ViChietKhau::getViByAccount($obj['account']);
            $tpl['vichietkhau'] = $vichietkhau;

            // case ViTichLuy
            $vitichluy = ViTichLuy::getViByAccount($obj['account']);
            $tpl['vitichluy'] = $vitichluy;

            // case ViHoaHong
            $vihoahong = ViHoaHong::getViByAccount($obj['account']);
            $tpl['vihoahong'] = $vihoahong;

            // case ViCongNo
            $vicongno = ViCongNo::getViByAccount($obj['account']);
            $tpl['vicongno'] = $vicongno;

            // case vitieudung
            $vitieudung = ViTieuDung::getViByAccount($obj['account']);
            $tpl['vitieudung'] = $vitieudung;
            $where['customer_key'] = $obj['phone'];
            $listObj = Car::where($where)->orderBy('_id', 'DESC');
            $listObj = Pager::getInstance()->getPager($listObj, 50, 'all');

            $tpl['listObj'] = $listObj;
        }


        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }

    /***
     * Danh sách thành vien
     *
     * @url: admin/member/_save
     */
    public function _save()
    {

        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj', []);

        if ($id) {
            #region check role
            $isAllow = Role::isAllowTo(Role::$ACTION_EDIT. Role::$KAYN_CUSTOMER);
            if (!$isAllow) {
                return eView::getInstance()->getJsonError('Bạn không có quyền chỉnh sửa thông tin khách hàng');
            }
            #endregion

        } else {
            #region check role
            $isAllow = Role::isAllowTo(Role::$ACTION_EDIT. Role::$KAYN_CUSTOMER);
            if (!$isAllow) {
                return eView::getInstance()->getJsonError('Bạn không có quyền cập nhật khách hàng');
            }
            #endregion
        }
        if(!empty($obj['password'])) {
            if(!empty($obj['cfpassword']) && $obj['cfpassword'] != $obj['password']) {
                return eView::getInstance()->getJsonError('Mật khẩu xác nhận không khớp');
            }elseif(empty($obj['cfpassword'])) {
                return eView::getInstance()->getJsonError('Vui lòng nhập mật khẩu xác nhận.');
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
        /*$objByPhone = Customer::getByPhone($savePost['phone']);
        if ($objByPhone && $objByPhone->_id !== $id) {
            return eView::getInstance()->getJsonError('Số điện thoại này đã được đăng ký. Vui lòng chọn số khác');
        }
        if ($savePost['email']) {
            $objByEmail = Customer::getByEmail($savePost['email']);
            if ($objByEmail && $objByEmail->_id !== $id) {
                return eView::getInstance()->getJsonError('Email này đã được đăng ký. Vui lòng chọn email khác');

            }
        }*/


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

        //case avatar_url
        if (isset($obj['avatar_url']) && $obj['avatar_url']) {
            $savePost['avatar_url'] = $obj['avatar_url'];
        }

        //case status
        if (isset($obj['status']) && Customer::getListStatus($obj['status'])) {
            $savePost['status'] = $obj['status'];
        }else {
            $savePost['status'] = Customer::STATUS_INACTIVE;
        }



        //case tk_ngan_hang

        if (isset($obj['tk_ngan_hang'])) {
            $savePost['tk_ngan_hang'] = array_values($obj['tk_ngan_hang']);
            foreach ($savePost['tk_ngan_hang'] as $key => $value) {
                if (isset($value['id']) && Helper::isMongoId($value['id'])) {
                    $temp = MetaData::where('_id', $value['id'])->first();
                    if ($temp) {
                        $savePost['tk_ngan_hang'][$key]['name'] = $temp['name'];
                    }
                }

            }
        }

        //case lien_he_khac
        if (isset($obj['lien_he_khac'])) {
            $savePost['lien_he_khac'] = array_values($obj['lien_he_khac']);
            foreach ($savePost['lien_he_khac'] as $key => $value) {
                if (isset($value['id']) && Helper::isMongoId($value['id'])) {
                    $temp = MetaData::where('_id', $value['id'])->first();
                    if ($temp) {
                        $savePost['lien_he_khac'][$key]['name'] = $temp['name'];
                    }
                }
            }
        }

        //case file_thong_tin_co_ban
        if (isset($obj['files_thong_tin_co_ban']) ) {
            if(is_array($obj['files_thong_tin_co_ban'])){
                if (isset($obj['files_thong_tin_co_ban']['name'])) {
                    foreach ($obj['files_thong_tin_co_ban']['name'] as $k => $v) {
                        $savePost['files_thong_tin_co_ban'][$k]['name'] = $v;
                    }
                }
                if (isset($obj['files_thong_tin_co_ban']['path'])) {
                    foreach ($obj['files_thong_tin_co_ban']['path'] as $k => $v) {
                        $savePost['files_thong_tin_co_ban'][$k]['path'] = $v;
                    }
                }
            }else{
                $savePost['files_thong_tin_co_ban'] = [];
            }

        }

        if ($id) {
            $status = $obj['status'];
            $customer = Customer::where('_id', $id)->first();
            $listStatus = Customer::getListStatus();
            if (!isset($listStatus[$status]) && $status != Customer::STATUS_DELETED_PHY) {
                return eView::getInstance()->getJsonError('Trạng thái của danh mục không hợp lệ');
            }elseif (isset($listStatus[$status]) && $status == Customer::STATUS_DELETED) {
                return eView::getInstance()->getJsonError('Trạng thái của danh mục không hợp lệ');
            }elseif (isset($listStatus[$status]) && $status == Customer::STATUS_INACTIVE) {
                return eView::getInstance()->getJsonError('Tài khoản này đã được kích hoạt vào lúc '. Helper::showMongoDate($customer['actived_at'], 'd/m/Y H:i:s'));
            }
            $savePost['status'] = $status;
            $savePost['updated_at'] = Helper::getMongoDate();
            $savePost['updated_by'] = Member::getCreatedByToSaveDb();
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
        $customer = Customer::where('_id', $id)->first();
        // case cập nhật, tạo ví
        // case ViChietKhau
        $vi = [
            'account' => $customer['account'],
            'total_money' => 0,
            'created_at' => Helper::getMongoDate(),
            'status' => ViHoaHong::STATUS_ACTIVE,
        ];
        $vichietkhau = ViChietKhau::getViByAccount($customer['account']);
        if(!$vichietkhau) {
            ViChietKhau::insertGetId($vi);
        }

        // case ViTichLuy
        $vitichluy = ViTichLuy::getViByAccount($customer['account']);
        if(!$vitichluy) {
            ViTichLuy::insertGetId($vi);
        }

        // case ViHoaHong
        $vihoahong = ViHoaHong::getViByAccount($customer['account']);
        if(!$vihoahong) {
            ViHoaHong::insertGetId($vi);
        }

        // case ViCongNo
        $vicongno = ViCongNo::getViByAccount($customer['account']);
        if(!$vicongno) {
            ViCongNo::insertGetId($vi);
        }

        // case vitieudung
        $vitieudung = ViTieuDung::getViByAccount($customer['account']);
        if(!$vitieudung) {
            ViTieuDung::insertGetId($vi);
        }

        $data['link'] = Menu::buildLinkAdmin('customer/input?id=' . $id);
        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $data);
    }


    public function _update_status() {
        $isAllow = Role::isAllowTo(Role::$ACTION_APPROVE. Role::$KAYN_CUSTOMER);
        if (!$isAllow) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện hành động này');
        }
        $token = Request::capture()->input('token');
        $idC = Helper::validateToken($token);
        if (!$idC) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ!');
        }
        $customer = Customer::find($idC);
        if(!$customer) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ!');
        }
        $status = Request::capture()->input('value');
        $listStatus = Customer::getListStatus();
        if (!isset($listStatus[$status]) && $status != Customer::STATUS_DELETED_PHY) {
            return eView::getInstance()->getJsonError('Trạng thái của danh mục không hợp lệ');
        }elseif (isset($listStatus[$status]) && $status == Customer::STATUS_DELETED) {
            return eView::getInstance()->getJsonError('Trạng thái của danh mục không hợp lệ');
        }elseif (isset($listStatus[$status]) && $status == Customer::STATUS_INACTIVE) {
            return eView::getInstance()->getJsonError('Tài khoản này đã được kích hoạt vào lúc '. Helper::showMongoDate($customer['actived_at'], 'd/m/Y H:i:s'));
        }
        $objToSave = [
            'status' => $status,
            'updated_at' => Helper::getMongoDate(),
            'updated_by' => Member::getCreatedByToSaveDb(),
        ];
        if($status == Customer::STATUS_DISABLE) {
            $objToSave['disabled_at'] = Helper::getMongoDate();
        }else {
            $objToSave['actived_at'] = Helper::getMongoDate();
        }
        Customer::where('_id', $idC)->update($objToSave);
        Logs::createLog([
            'type' => Logs::TYPE_UPDATED,
            'data_object' => $objToSave,
            'note' => "Khách hàng " . $customer['account'] . ' được sửa thông tin bởi ' . Member::getCurentAccount()
        ], 'customer');
        $customer = Customer::where('_id', $idC)->first();
        // case cập nhật, tạo ví
        // case ViChietKhau
        $vi = [
            'account' => $customer['account'],
            'total_money' => 0,
            'created_at' => Helper::getMongoDate(),
            'status' => ViHoaHong::STATUS_ACTIVE,
        ];
        $vichietkhau = ViChietKhau::getViByAccount($customer['account']);
        if(!$vichietkhau) {
            ViChietKhau::insertGetId($vi);
        }

        // case ViTichLuy
        $vitichluy = ViTichLuy::getViByAccount($customer['account']);
        if(!$vitichluy) {
            ViTichLuy::insertGetId($vi);
        }

        // case ViHoaHong
        $vihoahong = ViHoaHong::getViByAccount($customer['account']);
        if(!$vihoahong) {
            ViHoaHong::insertGetId($vi);
        }

        // case ViCongNo
        $vicongno = ViCongNo::getViByAccount($customer['account']);
        if(!$vicongno) {
            ViCongNo::insertGetId($vi);
        }

        // case vitieudung
        $vitieudung = ViTieuDung::getViByAccount($customer['account']);
        if(!$vitieudung) {
            ViTieuDung::insertGetId($vi);
        }
        return eView::getInstance()->getJsonSuccess('Cập nhật trạng thái thành công!');
    }

    public function _delete()
    {
        return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        $id = Request::capture()->input('id', 0);
        $token = Request::capture()->input('token', 0);
        if (!Helper::validateToken($token, $id)) {
            return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        }
        if (!Member::haveRole(Member::mng_member_delete)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền xóa thông tin thành viên');
        }
        $customer = Customer::where('_id', $id)->first();
        Logs::createLog([
            'type' => Logs::TYPE_DELETE,
            'data_object' => $customer->toArray(),
            'note' => "Khách hàng " . $customer['phone'] . ' bị xóa bởi ' . Member::getCurentAccount()
        ], 'customer');

        $customer->delete();

        return eView::getInstance()->getJsonSuccess('Xóa đối tượng thành công. Bạn không thể khôi phục lại', []);
    }
    public function export_popup()
    {
        $tpl = [];
        return eView::getInstance()->setViewBackEnd(__DIR__, 'export-popup', $tpl);
    }

}