<?php

/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 5/16/2016
 * Time: 12:24 PM
 */

namespace App\Http\Controllers\AdminMember;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Models\Agency;
use App\Http\Models\BaseModel;
use App\Http\Models\Car;
use App\Http\Models\Customer;
use App\Http\Models\KhoDiem;
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

class MngMember extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->my_info();
        }
    }

    /***
     * Danh sách thành vien
     *
     * @url: admin/customer/list
     */

    public function my_info()
    {
        return redirect(admin_link('/staff/input?id=' . Member::getCurentId()));
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

        $obj = Customer::find(Member::getCurentId());

        if ($id && isset($obj) && $obj) {
            $temp = Customer::get()->toArray();
            $lsTree = Customer::getDanhSachDongHo($temp, $obj['ma_gioi_thieu'], 5);
            //dd($lsTree);
            $html = '';
            $html .= '<div class="col-md-12"><div class="custom-dd" id="nestable_list_1"><ol class="dd-list">'.$this->buildMenu($lsTree).'</ol></div></div>';
            $tpl['dongHoNhaEm'] = $html;
            $tpl['image_avatar'] = Media::buildImageLink(isset($obj['image']) ? $obj['image'] : '');
            $lsAgency = Agency::getLsAgencyByIdCustomer($obj['account']);
            $tpl['lsAgency'] = $lsAgency;
            $tpl['obj'] = $obj;

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

            $khodiem = KhoDiem::getViByAccount($obj['account']);
            $tpl['khodiem'] = $khodiem;
            $where['customer_key'] = $obj['phone'];
            $listObj = Car::where($where)->orderBy('_id', 'DESC');
            $listObj = Pager::getInstance()->getPager($listObj, 50, 'all');

            $tpl['listObj'] = $listObj;
        }else {
            return eView::getInstance()->setView404();
        }
        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }

    function buildMenu($menu_data)
    {
        $html = "";
        if (isset($menu_data)) {
            foreach ($menu_data as $item) {
                if(!isset($item['name'])) {
                    $item['name'] = @$item['fullname'];
                }
                if (empty($item['children'])) {
                    $html .= '<li data-popup="tooltip" title="SĐT: '.@$item['phone'].' - ĐC: '.Helper::showContent(@$item['addr']).'"
                                  data-placement="top" class="dd-item" data-id="'.$item['_id'].'">
                                <div class="dd-handle">Họ tên: ' . @$item['name'] .' - Acc: '. @$item['account'] . '
                                    
                                </div>
                            </li>';
                }
                if (!empty($item['children'])) {

                    $html .= '<li  class="dd-item" data-id="'.$item['_id'].'">
                            <div data-popup="tooltip" title="SĐT: '.@$item['phone'].' - ĐC: '.Helper::showContent(@$item['addr']).'"
                                  data-placement="top" class="dd-handle">Họ tên: ' . @$item['name'] .' - Acc: '. @$item['account'] . '
                                
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
     * @url: admin/member/_save
     */
    public function _save()
    {

        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj', []);

        if ($id != Member::getCurentId()) {
            return eView::getInstance()->getJsonError('Thông tin cá nhân của bạn không hợp lệ');
        }

        if(!empty($obj['password'])) {
            if(!empty($obj['cfpassword']) && $obj['cfpassword'] != $obj['password']) {
                return eView::getInstance()->getJsonError('Mật khẩu xác nhận không khớp');
            }elseif(empty($obj['cfpassword'])) {
                return eView::getInstance()->getJsonError('Vui lòng nhập mật khẩu xác nhận.');
            }
        }




        $savePost = [
            'fullname' => (isset($obj['fullname']) && $obj['fullname']) ? strip_tags(trim($obj['fullname'])) : '',
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

        if (!Helper::isCanCuocCongDan($obj['can_cuoc_cong_dan'])) {
            eView::getInstance()->setMsgError('Căn cước công dân hoặc chứng minh thư không hợp lệ');
        }
        $customer = Member::getMemberByCanCuocCongDan($obj['can_cuoc_cong_dan']);
        if ($customer) {
            eView::getInstance()->setMsgError('Căn cước công dân đã được sử dụng');
        }
        $savePost['can_cuoc_cong_dan'] = $obj['can_cuoc_cong_dan'];

        if (!$savePost['fullname']) {
            return eView::getInstance()->getJsonError('Bạn vui lòng nhập tên');
        }
        $savePost['name'] = $savePost['fullname'];
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

        $data['link'] = Menu::buildLinkAdmin('staff/input?id=' . $id);
        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $data);
    }

    public function export_popup()
    {
        $tpl = [];
        return eView::getInstance()->setViewBackEnd(__DIR__, 'export-popup', $tpl);
    }

}