<?php

/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 5/16/2016
 * Time: 12:24 PM
 */

namespace App\Http\Controllers\AdminKhoDiem;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Middleware\UnauthorizedPersonnel;
use App\Http\Models\Agency;
use App\Http\Models\BaseModel;
use App\Http\Models\Car;
use App\Http\Models\Customer;
use App\Http\Models\Logs;
use App\Http\Models\Media;
use App\Http\Models\Member;
use App\Http\Models\Menu;
use App\Http\Models\Orders;
use App\Http\Models\Product;
use App\Http\Models\PurchaseOrder;
use App\Http\Models\PurchaseOrderLog;
use App\Http\Models\Role;
use App\Http\Models\Transaction;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\KhoDiem;
use App\Http\Models\ViTieuDung;
use App\Http\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MngOrderMuaHang extends Controller
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

        HtmlHelper::getInstance()->setTitle('Quản lý danh sách đơn hàng');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 100);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('status', '');
        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;
        $where = [
            'agency.account_chu_dai_ly' => (string)Member::getCurentAccount()
        ];
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


        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }


    /***
     * Hàm cập nhật đơn hàng
     *
     * @url: admin/member/_save
     */
    public function _save()
    {

        $id = Request::capture()->input('order_id', 0);
        $nextStep = Request::capture()->input('id', 0);
        $obj = PurchaseOrder::find($id);
        if(!$obj) {
            return eView::getInstance()->getJsonError('Không tìm thấy đơn hàng này.');
        }else {
            // $obj->items->

            if(isset($obj['agency']['id'])) {
                $agency = Agency::find($obj['agency']['id']);
            }else {
                return eView::getInstance()->getJsonError('Không tìm thấy đại lý trả hàng này.');
            }
            if ($agency['status'] != Agency::STATUS_ACTIVE) {
                return eView::getInstance()->getJsonError('Đại lý trả hàng này đang bị tạm khoá.');
            }
            if (!isset($agency['member']['account']) || empty($agency['member']['account'])) {
                return eView::getInstance()->getJsonError('Đại lý trả hàng này chưa cập nhật chủ đại lý.');
            }
            if ($agency['member']['account'] != Member::getCurentAccount()) {
                return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
            }
            if(!isset($obj['city'])) {
                return eView::getInstance()->getJsonError('Đơn hàng này chưa cập nhật tỉnh.');
            }
            $viTieuDung = ViTieuDung::where('account', $agency['member']['account'])->first();
            if(empty($viTieuDung)) {
                return eView::getInstance()->getJsonError('Không tìm thấy ví tiêu dùng của đại lý trả hàng.');
            }

            $flag = false;
            if($nextStep == PurchaseOrder::STATUS_DELIVERED) {
                $o = [
                    'tai_khoan_nguon' => $obj['created_by'],
                    'tai_khoan_nhan' => $agency['member'],
                    'order_id' => $obj['_id'],
                ];
                if(is_string($agency['dai_ly_tra_hang'])) {
                    if ($agency['dai_ly_tra_hang'] == Agency::AGENCY_TRA_HANG_CAP_HUYEN) {
                        $flag = false;
                        $percent = Orders::getPercentKhoDiemTieuDungDeliveredDaiLyHuyen();
                        $diemdanhan = (int)$obj['grandTotal']*$percent;
                        $moneyHH = (int)$viTieuDung['total_money'] + $diemdanhan;
                        $o['diem_da_nhan'] = $diemdanhan;
                        $o['detail_type_giaodich'] = Helper::formatPercent($percent).'% của đơn hàng cho đại lý cấp huyện';
                    }elseif ($agency['dai_ly_tra_hang'] == Agency::AGENCY_TRA_HANG_CAP_TINH) {
                        $flag = true;
                        $percent = Orders::getPercentKhoDiemTieuDungDeliveredDaiLyTinh();
                        $diemdanhan = (int)$obj['grandTotal']*$percent;
                        $moneyHH = (int)$viTieuDung['total_money'] + $diemdanhan;
                        $o['diem_da_nhan'] = $diemdanhan;
                        $o['detail_type_giaodich'] = Helper::formatPercent($percent).'% của đơn hàng cho đại lý cấp tỉnh';
                    }else {
                        return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                    }
                }else if(is_array($agency['dai_ly_tra_hang'])) {
                    if (in_array(Agency::AGENCY_TRA_HANG_CAP_HUYEN, $agency['dai_ly_tra_hang'])) {
                        $flag = false;
                        $percent = Orders::getPercentKhoDiemTieuDungDeliveredDaiLyHuyen();
                        $diemdanhan = (int)$obj['grandTotal']*$percent;
                        $moneyHH = (int)$viTieuDung['total_money'] + $diemdanhan;
                        $o['diem_da_nhan'] = $diemdanhan;
                        $o['detail_type_giaodich'] = Helper::formatPercent($percent).'% của đơn hàng cho đại lý cấp huyện';
                    }elseif (in_array(Agency::AGENCY_TRA_HANG_CAP_TINH, $agency['dai_ly_tra_hang'])) {
                        $flag = true;
                        $percent = Orders::getPercentKhoDiemTieuDungDeliveredDaiLyTinh();
                        $diemdanhan = (int)$obj['grandTotal']*$percent;
                        $moneyHH = (int)$viTieuDung['total_money'] + $diemdanhan;
                        $o['diem_da_nhan'] = $diemdanhan;
                        $o['detail_type_giaodich'] = Helper::formatPercent($percent).'% của đơn hàng cho đại lý cấp tỉnh';
                    }else {
                        return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                    }
                }else {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                }
            }

            $order = PurchaseOrder::find($id)->update(['status' => $nextStep, 'updated_at' => Helper::getMongoDate(), 'updated_by' => Member::getCreatedByToSaveDb()]);
            Logs::createLogNew([
                'type' => Logs::OBJECT_DONHANG,
                'object_id' => (string)$id,
                'note' => 'Cập nhật trạng thái đơn hàng ' . @$id
            ], Product::table_name, $obj->toArray(), PurchaseOrder::find($id)->toArray());

            //Save log order
            //PurchaseOrderLog::createLog($obj->id, $nextStep, 'Thành công');

            // sendmail nếu hàng sang trạng thái sẵn sàng giao
            // trừ số lượng hàng nếu hàng đã thanh toán
            $objToSave = [];
            $ids = [];
            if($nextStep == PurchaseOrder::STATUS_DELIVERED) {
                $temp = [];
                // cập nhật lại số lượng
                if ($obj['payment_type'] == PurchaseOrder::PAYMENT_KHODIEM) {
                    $vitieudungOfCreated = KhoDiem::getViByAccount($obj['created_by']['account']);
                    $vitieudungOfCreated['so_diem_treo_gio'] -= $obj['grandTotal'];
                    $vitieudungOfCreated['total_money'] -= $obj['grandTotal'];


                    KhoDiem::getViByAccount($obj['created_by']['account'])->update([
                        'so_diem_treo_gio' => $vitieudungOfCreated['so_diem_treo_gio'],
                        'total_money' => $vitieudungOfCreated['total_money']
                    ]);
                }else if ($obj['payment_type'] == PurchaseOrder::PAYMENT_VITIEUDUNG) {
                    $vitieudungOfCreated = ViTieuDung::getViByAccount($obj['created_by']['account']);
                    $vitieudungOfCreated['so_diem_treo_gio'] -= $obj['grandTotal'];
                    $vitieudungOfCreated['total_money'] -= $obj['grandTotal'];


                    ViTieuDung::getViByAccount($obj['created_by']['account'])->update([
                        'so_diem_treo_gio' => $vitieudungOfCreated['so_diem_treo_gio'],
                        'total_money' => $vitieudungOfCreated['total_money']
                    ]);
                }




                $khodiem = KhoDiem::getViByAccount($agency['member']['account']);
                $up = [
                    'account' => $agency['member']['account'],
                    'total_money' =>  $obj['grandTotal'],
                    'created_at' =>  Helper::getMongoDate(),
                ];
                if($khodiem) {
                    $up['total_money'] = $khodiem['total_money'] + $obj['grandTotal'];
                    KhoDiem::getViByAccount($agency['member']['account'])->update($up);
                }else {
                    $up['status'] = KhoDiem::STATUS_ACTIVE;
                    KhoDiem::insertGetId($up);
                }

                $oldObj = $viTieuDung;
                $vi = ViTieuDung::find($viTieuDung['_id']);
                if ($vi) {
                    $vi->update(['total_money' => $moneyHH, 'updated_at' => Helper::getMongoDate()]);
                    Transaction::createTransaction($o, Transaction::KHODIEM_TIEUDUNG, Transaction::VITIEUDUNG);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$oldObj['_id'],
                        'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDung['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, $oldObj, ViTieuDung::find($viTieuDung['_id'])->toArray());

                }
                /*Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$oldObj['_id'],
                    'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDung['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                ], ViTieuDung::table_name, $oldObj, ViTieuDung::find($viTieuDung['_id'])->toArray());*/

                // case cập nhật full ví tiêu dùng cho các đại lý
                if(!$flag) {
                    $lsAgencyCity = Agency::getLsAgencyByIdCityNeIdAgency($obj['city']['id'], $obj['agency']['id']);
                    if (count($lsAgencyCity) > 0) {
                        $ChiaDeuTienChoAeTrongTinh = Orders::getPercentKhoDiemTieuDungDeliveredDaiLyTinhForFull();
                        $moneyForFullDaiLyCapTinh = $ChiaDeuTienChoAeTrongTinh / count($lsAgencyCity);
                        foreach ($lsAgencyCity as $agency) {
                            $o = [
                                'tai_khoan_nguon' => $obj['created_by'],
                                'tai_khoan_nhan' => $agency['member'],
                                'order_id' => $obj['_id'],
                                'detail_type_giaodich' => Helper::formatPercent($moneyForFullDaiLyCapTinh) . '% của đơn hàng cho tất cả đại lý cấp tỉnh'
                            ];
                            $vi = ViTieuDung::getViByAccount($agency['member']['account']);
                            if ($vi) {
                                $o['diem_da_nhan'] = (int)$obj['grandTotal'] * $moneyForFullDaiLyCapTinh;
                                $moneyHH = (int)$vi['total_money'] + (int)$obj['grandTotal'] * $moneyForFullDaiLyCapTinh;
                                $vi->update(['total_money' => $moneyHH, 'updated_at' => Helper::getMongoDate()]);
                                Transaction::createTransaction($o, Transaction::KHODIEM_TIEUDUNG, Transaction::VITIEUDUNG);
                                Logs::createLogNew([
                                    'type' => Logs::TYPE_UPDATED,
                                    'object_id' => (string)$vi['_id'],
                                    'note' => 'Ví tiêu dùng của acc: ' . @$agency['member']['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                                ], ViTieuDung::table_name, $vi->toArray(), ViTieuDung::getViByAccount($agency['member']['account'])->toArray());
                            }
                        }
                    }
                }
            }
            elseif($nextStep == PurchaseOrder::STATUS_CANCELLED || $nextStep == PurchaseOrder::STATUS_DELETED) {
                if($obj['payment_type'] == PurchaseOrder::PAYMENT_VITIEUDUNG) {
                    $viTieuDung = ViTieuDung::getViByAccount($obj['created_by']['account']);
                    $oldVi = $viTieuDung->toArray();
                    $viTieuDung->update([
                        'so_diem_treo_gio' => 0,
                    ]);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viTieuDung['_id'],
                        'note' => 'Ví tiêu dùng của acc: ' . @$obj['created_by']['account'] . ' đã được hoàn trả số điểm treo MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, $oldVi, ViTieuDung::getViByAccount($obj['created_by']['account'])->toArray());
                }elseif($obj['payment_type'] == PurchaseOrder::PAYMENT_KHODIEM) {
                    $khodiem = KhoDiem::getViByAccount($obj['created_by']['account']);
                    $oldKho = $khodiem;
                    $khodiem->update([
                        'so_diem_treo_gio' => 0,
                    ]);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$oldKho['_id'],
                        'note' => 'Kho điểm của acc: ' . @$viTieuDung['account'] . ' đã được hoàn trả số điểm treo MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, $oldKho, ViTieuDung::getViByAccount($obj['created_by']['account'])->toArray());
                }
            }
            $data['link'] = Menu::buildLinkAdmin('danh-sach-don-tra-hang');

            return eView::getInstance()->getJsonSuccess('Cập nhật trạng thái đơn hàng thành công', $data);
        }


        $data['link'] = Menu::buildLinkAdmin('danh-sach-don-hang');
        return eView::getInstance()->getJsonSuccess('Không tìm thấy dữ liệu', $data);
    }


    function _update_mpmart_tieudung($obj, $vitieudung = false, $vihoahong = false, $listPercentHoaHong) {
        $giaPha = Customer::buildTreeNguoc('', $temp, $obj['created_by']['account'],Customer::floor); // gia phả dòng họ
        if($giaPha) {
            $listPercentHoaHong = Orders::getPrecentDiemMpMartHoaHongForF();
            foreach ($giaPha as $k => $g) {
                $saveTieuDung = [
                    'account' => $g['account'],
                    'total_money' => $obj['grandTotal']*$listPercentHoaHong[$k],
                    'created_at' => Helper::getMongoDate(),
                    'status' => BaseModel::STATUS_ACTIVE,
                ];
                $saveTransactionTieuDung = [
                    'account' => $g['account'],
                    'diem_da_nhan' => $obj['grandTotal']*$listPercentHoaHong[$k],
                    'tai_khoan_nguon' => $obj['created_by'],
                    'tai_khoan_nhan' => Customer::getTaiKhoanToSaveDb($g),
                    'type_giaodich' => Transaction::KHODIEM_TIEUDUNG,
                    'created_at' => Helper::getMongoDate(),
                    'status' => BaseModel::STATUS_ACTIVE,
                    'order_id' => $obj['_id'],
                ];

                // kiểm tra xem ví của tk này đã được tạo hay chưa.
                $viTieuDungOfCus = ViTieuDung::where('account', $g['account'])->first();
                if($viTieuDungOfCus) {
                    $oldObj = $viTieuDungOfCus;
                    $moneyHH = (int)$viTieuDungOfCus['total_money'] + (int)$saveTransactionTieuDung['diem_da_nhan'];
                    ViTieuDung::find($viTieuDungOfCus['_id'])->update(['total_money' => $moneyHH, 'updated_at' => Helper::getMongoDate()]);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$oldObj['_id'],
                        'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDungOfCus['account'] . ' đã dược cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, $oldObj->toArray(), ViTieuDung::find($viTieuDungOfCus['_id'])->toArray());
                }else {
                    $id = ViTieuDung::insertGetId($saveTieuDung);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_CREATE,
                        'object_id' => (string)$id,
                        'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDungOfCus['account'] . ' đã dược cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, [], ViTieuDung::find($id)->toArray());
                }
                Transaction::insert($saveTransactionTieuDung);
            }
        }
    }

    public function _delete_multi()
    {

        $ids = Request::capture()->input('ids', 0);
        $token = Request::capture()->input('token', 0);
        $isAllow = Role::isAllowTo(Role::$ACTION_DELETE. Role::$KAYN_ODER. Role::$KAYN_MUAHANG);
        if (!$isAllow) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện hành động này');
        }
        $ids = explode(',', $ids);
        if(!$ids) {
            return eView::getInstance()->getJsonError('Không tìm thấy đối tượng này');
        }
        foreach ($ids as $id) {
            $token = explode('&token=', $id);
            if (!Helper::validateToken($token[1], $token[0])) {
                return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
            }
            /*$where = [
                'status' => ['$ne' => PurchaseOrder::STATUS_DELETED],
                '_id' => Helper::getMongoId($token[0]),
            ];*/
            $order = PurchaseOrder::find($token[0]);
            if(!$order) {
                return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
            }
            $objToSave = [
                'status' => Orders::STATUS_DELETED,
                'removed' => Orders::REMOVED_YES,
                'deleted_at' => Helper::getMongoDate(),
                'deleted_by' => Member::getCreatedByToSaveDb(),
            ];
            PurchaseOrder::find($token[0])->update($objToSave);
            Logs::createLogNew([
                'type' => Logs::TYPE_DELETE,
                'object_id' => (string)$id,
                'note' => 'Đơn hàng ' . @$order['_id'] . ' bị xoá bởi '.Member::getCurentAccount()
            ], Orders::table_name, $order->toArray(), PurchaseOrder::find($token[0])->toArray());
        }
        return eView::getInstance()->getJsonSuccess('Xóa đối tượng thành công. Bạn không thể khôi phục lại', []);
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