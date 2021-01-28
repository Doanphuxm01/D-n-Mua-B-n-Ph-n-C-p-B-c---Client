<?php


namespace App\Http\Controllers\AdminChuyenDiem;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Customer;
use App\Http\Models\Logs;
use App\Http\Models\Orders;
use App\Http\Models\Role;
use App\Http\Models\Transaction;
use App\Http\Models\UnauthorizedPersonnel;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTichLuy;
use App\Http\Models\ViTieuDung;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Models\Member;
use App\Http\Models\Withdrawal;

class MngChuyenDiem extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->input();
        }
    }

    /*
     * 1. Chuyển điểm từ ví tích lũy -> ví tiêu dùng
     * */

    public function input()
    {
        if (!empty($_POST)) {
            return $this->_save();
        }
        $tpl = [];
        // case ViTichLuy
        $vitichluy = ViTichLuy::getViByAccount(Member::getCurentAccount());
        $tpl['vitichluy'] = $vitichluy;

        // case ViHoaHong
        $vihoahong = ViHoaHong::getViByAccount(Member::getCurentAccount());
        $tpl['vihoahong'] = $vihoahong;
        $currentMember = Member::getCurent();
        $currentMemberId = $currentMember['_id'];
        HtmlHelper::getInstance()->setTitle('Yêu cầu chuyển điểm');
        $id = Request::capture()->input('id', 0);

        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }

    function _save() {

        $obj = Request::capture()->input('obj', []);
        if (!is_numeric($obj['so_diem_giao_dich'])) {
            return eView::getInstance()->getJsonError('Số điểm mua không hợp lệ');
        }
        if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VITICHLUY') {
            $vi = ViTichLuy::getViByAccount(Member::getCurentAccount());
            $obj['type_giaodich'] = 'VITICHLUY_TIEUDUNG';
        }else if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VIHOAHONG') {
            $vi = ViHoaHong::getViByAccount(Member::getCurentAccount());
            $obj['type_giaodich'] = 'VIHOAHONG_TIEUDUNG';
        }else {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
        }
        if(!$vi) {
            return eView::getInstance()->getJsonError('Không tìm thấy ví giao dịch bạn yêu cầu');
        }
        if($vi['total_money'] <= 0) {
            return eView::getInstance()->getJsonError('Ví của bạn không đủ điểm để giao dịch');
        }

        $phigiaodich = 3000;
        if($obj['so_diem_giao_dich'] <= 5000000){
            $phigiaodich = 2000;
        }elseif($obj['so_diem_giao_dich'] <= 10000000){
            $phigiaodich = 3000;
        }elseif($obj['so_diem_giao_dich'] > 10000000){
            $phigiaodich = 5000;
        }
        $sotiengiaodich = (double)$obj['so_diem_giao_dich'] + $phigiaodich;
        $soducuoi = $vi['total_money'] - $sotiengiaodich;
        if($soducuoi < BaseModel::getLotVi()) {
            return eView::getInstance()->getJsonError('Số dư tối thiểu để duy trì hoạt động trên ví là '.Helper::formatMoney(BaseModel::getLotVi()));
        }
        if (Member::getCurrentChucDanh() == Member::IS_DAILY) {
            //$saveMember['chuc_danh'] = Member::IS_DAILY;
            // @todo @kayn check có nợ hay ko, nếu ko có thì default là ko nợ

            if($obj['so_diem_giao_dich'] >= Orders::getMinMPMart()) {
                $chucdanh = Member::IS_MPMART;
                $saveOrder['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;
                $saveOrder['everyday_percent_mpmart_value'] = Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart();
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_giao_dich']*Orders::getPercentChietKhauMpMart();
                $percent = Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart();
            }else {
                $saveOrder['debt'] = Member::DEBT_NO;
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_giao_dich']*Orders::getPercentChietKhauDaiLy();
                $percent = Orders::getEveryDayPercentChietKhauTieuDungDebtNo();
            }
        }
        elseif (Member::getCurrentChucDanh() == Member::IS_CTV) {
            if($obj['so_diem_giao_dich'] >= Orders::getMinDaiLy() && $obj['so_diem_giao_dich'] < Orders::getMinMPMart()) {
                $chucdanh = Member::IS_DAILY;
                $saveOrder['debt'] = Member::DEBT_NO;
                $percent = Orders::getEveryDayPercentChietKhauTieuDungDebtNo();
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_giao_dich']*Orders::getPercentChietKhauDaiLy();
            }elseif($obj['so_diem_giao_dich'] >= Orders::getMinMPMart()) {
                $chucdanh = Member::IS_MPMART;
                $saveOrder['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;
                $percent = Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart();
                $saveOrder['everyday_percent_mpmart_value'] = $percent;
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_giao_dich']*Orders::getPercentChietKhauMpMart();
            }else {
                $percent = Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV();
                $saveOrder['everyday_percent_ctv'] = $percent;
                $saveOrder['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG;
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_giao_dich']*Orders::getPercentChietKhauCtv();
            }
        }
        elseif (Member::getCurrentChucDanh() == Member::IS_MPMART) {
            $percent = Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart();
            $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;
            $saveMember['everyday_percent_mpmart_value'] = $percent;
            $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_giao_dich']*Orders::getPercentChietKhauMpMart();
        }
        $o = [
            'diem_da_nhan' => (double)$obj['so_diem_giao_dich'],
            'tai_khoan_nguon' => Member::getCreatedByToSaveDb(),
            'tai_khoan_nhan' => Member::getCreatedByToSaveDb(),
            'created_by' => Member::getCreatedByToSaveDb(),
            'created_at' => Helper::getMongoDate(),
            'vi_thanh_toan' => $obj['type_vi_thanh_toan'],
            'vi_nhan_diem' => $obj['type_vi_nhan_diem'],
            'type_giaodich' => $obj['type_giaodich'],
            'detail_type_giaodich' => 'Chuyển điểm qua các ví',
            'object' => 'vitieudung',
            'phi_giaodich' => $phigiaodich,
        ];
        $saveOrder['status'] = BaseModel::STATUS_PROCESS_DONE;
        $saveOrder['tai_khoan_nguon'] = $o['tai_khoan_nguon'];
        $saveOrder['tai_khoan_nhan'] = $o['tai_khoan_nhan'];
        $saveOrder['so_diem_can_mua'] = (int)$obj['so_diem_giao_dich'];
        $saveOrder['so_diem_duoc_nhan'] = (int)$obj['so_diem_giao_dich'];
        $saveOrder['type'] = Orders::ORDER_CHUYENDIEM_MPG;
        $saveOrder['created_at'] = Helper::getMongoDate();
        $saveOrder['created_by'] = Member::getCreatedByToSaveDb();

        $saveOrder['moc_tien'] = $saveOrder['so_diem_vi_chiet_khau'] * $percent;
        $saveOrder['percents'] = UnauthorizedPersonnel::getUn();
        $idOrder = (string)Orders::insertGetId($saveOrder);
        // case cập nhật trừ ví
        $oldVi = $vi->toArray();
        $vi->update(['total_money' => $soducuoi]);
        if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VITICHLUY') {
            $noteLog = 'Ví tích luỹ của acc: ' . @$vi['account'] . ' đã trừ  ' . (double)$sotiengiaodich;
            $tablNameLog = ViTichLuy::table_name;
        }else if(isset($obj['type_vi_thanh_toan']) && $obj['type_vi_thanh_toan'] == 'OBJECT_VIHOAHONG') {
            $noteLog = 'Ví hoa hồng của acc: ' . @$vi['account'] . ' đã trừ  ' . (double)$sotiengiaodich;
            $tablNameLog = ViHoaHong::table_name;
        }
        Logs::createLogNew([
            'type' => Logs::TYPE_UPDATED,
            'object_id' => (string)$vi['_id'],
            'note' => $noteLog,
        ], $tablNameLog, $oldVi, $vi->toArray());
        $o['order_id'] = $idOrder;
        Transaction::insert($o);
        return $this->_update_status($idOrder, $obj);
    }

    public function _update_status($id, $obj) {

        $order = Orders::find($id);
        if(!$order) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ!');
        }

        $customer = Customer::getMemberByAccount(Member::getCurentAccount());
        if(!$customer) {
            return eView::getInstance()->getJsonError('Không tìm thấy khách hàng này.');
        }else {
            if($customer['status'] != Customer::STATUS_ACTIVE) {
                return eView::getInstance()->getJsonError('Khách hàng này không hoạt động');
            }
        }
        $now = date('d/m/Y');
        $objToSave = [
            'start_updated_vi_at' => Helper::getMongoDate($now),
            'updated_at' => Helper::getMongoDate(),
            'actived_at' => Helper::getMongoDate(),
            'updated_by' => Member::getCreatedByToSaveDb(),
        ];
        $a = Carbon::now()->addMonths(Orders::getMonthsEndRunAuto());
        $a = $a->toDateString();
        $objToSave['end_updated_vi_at'] = Helper::getMongoDate($a);
        Orders::where('_id', $id)->update($objToSave);
        Logs::createLogNew([
            'type' => Logs::TYPE_UPDATED,
            'object_id' => (string)$order['_id'],
            'note' => "Đơn mua điểm " . $order['_id'] . ' được sửa thông tin bởi ' . Member::getCurentAccount()
        ], Customer::table_name, $order->toArray(), Orders::find($order['_id'])->toArray());
        $status = $order['status'];
        if($status == Orders::STATUS_PROCESS_DONE) {
            $customer = Customer::getMemberByAccount($customer['account']);
            if(isset($customer['chuc_danh']) && $customer['chuc_danh'] != Customer::IS_DAILY && $order['so_diem_can_mua'] >= Orders::getMinDaiLy(@$order['percents']) && $order['so_diem_can_mua'] < Orders::getMinMPMart(@$order['percents'])) {
                Customer::where('_id', $customer['_id'])->update(['chuc_danh' => Customer::IS_DAILY]);
                $newCus = Customer::find($customer['_id'])->toArray();
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$customer['_id'],
                    'note' => 'Khách hàng acc: ' . @$customer['account'] . ' đã được cập nhật thành đại lý từ đơn hàng ' . (string)$id
                ], Customer::table_name, $customer->toArray(), $newCus);
            }elseif(isset($customer['chuc_danh']) && $customer['chuc_danh'] != Customer::IS_MPMART && $order['so_diem_can_mua'] >= Orders::getMinMPMart(@$order['percents'])) {
                Customer::where('_id', $customer['_id'])->update(['chuc_danh' => Customer::IS_MPMART]);
                $newCus = Customer::find($customer['_id'])->toArray();
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$customer['_id'],
                    'note' => 'Khách hàng acc: ' . @$customer['account'] . ' đã được cập nhật thành siêu thị MP Mart từ đơn hàng ' . (string)$id
                ], Customer::table_name, $customer->toArray(), $newCus);
            }
            if(isset($newCus)) {
                $customer = $newCus;
            }
            // lấy ra đơn hàng vừa cập nhật
            $order = Orders::where('_id', $id)->first()->toArray();
            if(!isset($order['so_diem_duoc_nhan'])) {
                $order['so_diem_duoc_nhan'] = $order['so_diem_can_mua'];
            }
            // case nhảy 100% vào ví tiêu dùng
            $objTransactionTieuDungToSave = [
                'created_by' => [],
                'created_at' => Helper::getMongoDate(),
                'status' => Transaction::STATUS_ACTIVE,
                'type_giaodich' => Transaction::DIEM_TIEUDUNG,
                'object' => Transaction::VITIEUDUNG,
                'diem_da_nhan' => $order['so_diem_can_mua'],
                'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                'vi_thanh_toan' => $obj['type_vi_thanh_toan'],
                'vi_nhan_diem' => $obj['type_vi_nhan_diem'],
                'type_giaodich' => $obj['type_giaodich'],
                'detail_type_giaodich' => 'Chuyển điểm qua các ví',
                'order_id' => $id,
            ];
            Transaction::insertGetId($objTransactionTieuDungToSave);

            $viTieuDungOfCus = ViTieuDung::where('account', $customer['account'])->first();
            $money = (int)$order['so_diem_can_mua'];
            if($viTieuDungOfCus) {
                // cập nhật
                $money += (int)$viTieuDungOfCus['total_money'];
                $objViTieuDungToSave = [
                    'total_money' => $money,
                    'updated_at' => Helper::getMongoDate(),
                ];
                ViTieuDung::find($viTieuDungOfCus['_id'])->update($objViTieuDungToSave);
                Logs::createLogNew([
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => (string)$viTieuDungOfCus['_id'],
                    'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDungOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                ], ViTieuDung::table_name, $viTieuDungOfCus->toArray(), ViTieuDung::find($viTieuDungOfCus['_id'])->toArray());
            }else {
                // thêm mới
                $objViTieuDungToSave = [
                    'account' => $customer['account'],
                    'total_money' => $money,
                    'status' => ViTieuDung::STATUS_ACTIVE,
                    'created_at' => Helper::getMongoDate(),
                ];
                $idVi = ViTieuDung::insertGetId($objViTieuDungToSave);
                $viTieuDung = ViTieuDung::find($idVi)->toArray();
                Logs::createLogNew([
                    'type' => Logs::TYPE_CREATE,
                    'object_id' => (string)$idVi,
                    'note' => 'Ví tiêu dùng của acc: ' . @$viTieuDung['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                ], ViTieuDung::table_name, [], $viTieuDung);
            }

            // case nếu là đại lý thì chiết khấu nhảy 80%
            if(isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_DAILY) {
                $objTransactionChietKhauToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                    'object' => Transaction::VICHIETKHAU,
                    'diem_da_nhan' => $order['so_diem_can_mua']*Orders::getPercentChietKhauDaiLy(@$order['percents']),
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'vi_thanh_toan' => $obj['type_vi_thanh_toan'],
                    'vi_nhan_diem' => $obj['type_vi_nhan_diem'],
                    'type_giaodich' => $obj['type_giaodich'],
                    'detail_type_giaodich' => 'Chuyển điểm qua các ví',
                    'order_id' => $id,
                ];
                Transaction::insertGetId($objTransactionChietKhauToSave);

                $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
                $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
                if($viChietKhauOfCus) {
                    // cập nhật
                    $moneyCK += (int)$viChietKhauOfCus['total_money'];
                    $objViChietKhauToSave = [
                        'total_money' => $moneyCK,
                        'updated_at' => Helper::getMongoDate(),
                    ];
                    ViChietKhau::where('account', $customer['account'])->update($objViChietKhauToSave);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viChietKhauOfCus['_id'],
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhauOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                    ], ViChietKhau::table_name, $viChietKhauOfCus->toArray(), ViChietKhau::find($viChietKhauOfCus['_id'])->toArray());
                }else {
                    // thêm mới
                    $objViChietKhauToSave = [
                        'account' => $customer['account'],
                        'total_money' => $moneyCK,
                        'status' => ViChietKhau::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    $idViCK = ViChietKhau::insertGetId($objViChietKhauToSave);
                    $viChietKhau = ViChietKhau::find($idViCK)->toArray();
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$idViCK,
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhau['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                    ], ViChietKhau::table_name, [], $viChietKhau);
                }

                // case đơn > 20tr có nợ += 25% vào ví công nợ
                if(isset($order['debt']) && $order['debt'] == Orders::DEBT_YES) {
                    if (!isset($order['cong_no'])) {
                        $order['cong_no'] = $order['so_diem_duoc_nhan']*Orders::getPercentCongNoDebtYes(@$order['percents']);
                    }
                    $objTransactionCongNoToSave = [
                        'created_by' => [],
                        'created_at' => Helper::getMongoDate(),
                        'status' => Transaction::STATUS_ACTIVE,
                        'type_giaodich' => Transaction::DIEM_CONGNO,
                        'object' => Transaction::VICONGNO,
                        'diem_da_nhan' => $order['cong_no'],
                        'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                        'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                        'vi_thanh_toan' => $obj['type_vi_thanh_toan'],
                        'vi_nhan_diem' => $obj['type_vi_nhan_diem'],
                        'type_giaodich' => $obj['type_giaodich'],
                        'detail_type_giaodich' => 'Chuyển điểm qua các ví',
                        'order_id' => $id
                    ];
                    Transaction::insertGetId($objTransactionCongNoToSave);

                    $viCongNoOfCus = ViCongNo::where('account', $customer['account'])->first();
                    if($viCongNoOfCus) {
                        // cập nhật
                        $moneyCN = (int)$viCongNoOfCus['total_money'] + (int)$objTransactionCongNoToSave['diem_da_nhan'];
                        ViCongNo::where('account', $customer['account'])->update(['total_money' => $moneyCN, 'updated_at' => Helper::getMongoDate()]);
                        Logs::createLogNew([
                            'type' => Logs::TYPE_UPDATED,
                            'object_id' => (string)$viCongNoOfCus['_id'],
                            'note' => 'Ví công nợ của acc: ' . @$customer['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                        ], ViCongNo::table_name, $viCongNoOfCus->toArray(), ViCongNo::where('account', $customer['account'])->first()->toArray());

                    }else {
                        // thêm mới
                        $objViCongNoToSave = [
                            'account' => $customer['account'],
                            'total_money' => $objTransactionCongNoToSave['diem_da_nhan'],
                            'status' => ViCongNo::STATUS_ACTIVE,
                            'created_at' => Helper::getMongoDate(),
                        ];
                        $idViCongNo = ViCongNo::insertGetId($objViCongNoToSave);
                        Logs::createLogNew([
                            'type' => Logs::TYPE_CREATE,
                            'object_id' => (string)$idViCongNo,
                            'note' => 'Ví công nợ của acc: ' . @$customer['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                        ], ViCongNo::table_name, [], ViCongNo::find($idViCongNo)->toArray());
                    }
                }
            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_CTV) {
                $objTransactionChietKhauToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                    'object' => Transaction::VICHIETKHAU,
                    'diem_da_nhan' => $order['so_diem_can_mua']*Orders::getPercentChietKhauCtv(@$order['percents']),
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'vi_thanh_toan' => $obj['type_vi_thanh_toan'],
                    'vi_nhan_diem' => $obj['type_vi_nhan_diem'],
                    'type_giaodich' => $obj['type_giaodich'],
                    'detail_type_giaodich' => 'Chuyển điểm qua các ví',
                    'order_id' => $id,
                ];
                Transaction::insertGetId($objTransactionChietKhauToSave);

                $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
                $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
                if($viChietKhauOfCus) {
                    // cập nhật
                    $moneyCK += (int)$viChietKhauOfCus['total_money'];
                    $objViChietKhauToSave = [
                        'total_money' => $moneyCK,
                        'updated_at' => Helper::getMongoDate(),
                    ];
                    ViChietKhau::where('account', $customer['account'])->update($objViChietKhauToSave);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viChietKhauOfCus['_id'],
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhauOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                    ], ViChietKhau::table_name, $viChietKhauOfCus->toArray(), ViChietKhau::find($viChietKhauOfCus['_id'])->toArray());
                }else {
                    // thêm mới
                    $objViChietKhauToSave = [
                        'account' => $customer['account'],
                        'total_money' => $moneyCK,
                        'status' => ViChietKhau::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    $idViCK = ViChietKhau::insertGetId($objViChietKhauToSave);
                    $viChietKhau = ViChietKhau::find($idViCK)->toArray();
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$idViCK,
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhau['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                    ], ViChietKhau::table_name, [], $viChietKhau);
                }
            }
            elseif (isset($customer['chuc_danh']) && $customer['chuc_danh'] == Customer::IS_MPMART) {
                $objTransactionChietKhauToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                    'object' => Transaction::VICHIETKHAU,
                    'diem_da_nhan' => $order['so_diem_can_mua']*Orders::getPercentChietKhauMpMart(@$order['percents']),
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'vi_thanh_toan' => $obj['type_vi_thanh_toan'],
                    'vi_nhan_diem' => $obj['type_vi_nhan_diem'],
                    'type_giaodich' => $obj['type_giaodich'],
                    'detail_type_giaodich' => 'Chuyển điểm qua các ví',
                    'order_id' => $id,
                ];
                Transaction::insertGetId($objTransactionChietKhauToSave);

                $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
                $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
                if($viChietKhauOfCus) {
                    // cập nhật
                    $moneyCK += (int)$viChietKhauOfCus['total_money'];
                    $objViChietKhauToSave = [
                        'total_money' => $moneyCK,
                        'updated_at' => Helper::getMongoDate(),
                    ];
                    ViChietKhau::where('account', $customer['account'])->update($objViChietKhauToSave);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viChietKhauOfCus['_id'],
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhauOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                    ], ViChietKhau::table_name, $viChietKhauOfCus->toArray(), ViChietKhau::find($viChietKhauOfCus['_id'])->toArray());
                }else {
                    // thêm mới
                    $objViChietKhauToSave = [
                        'account' => $customer['account'],
                        'total_money' => $moneyCK,
                        'status' => ViChietKhau::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    $idViCK = ViChietKhau::insertGetId($objViChietKhauToSave);
                    $viChietKhau = ViChietKhau::find($idViCK)->toArray();
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$idViCK,
                        'note' => 'Ví chiết khấu của acc: ' . @$viChietKhau['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$id
                    ], ViChietKhau::table_name, [], $viChietKhau);
                }
            }

            /*// case nhảy 80% vào ví chiết khấu
            $objTransactionChietKhauToSave = [
                'created_by' => [],
                'created_at' => Helper::getMongoDate(),
                'status' => Transaction::STATUS_ACTIVE,
                'type_giaodich' => Transaction::DIEM_CHIETKHAU,
                'object' => Transaction::VICHIETKHAU,
                'diem_da_nhan' => $order['so_diem_can_mua']*Orders::getPercentChietKhau(),
                'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                'order_id' => $id,
            ];
            Transaction::insertGetId($objTransactionChietKhauToSave);

            $viChietKhauOfCus = ViChietKhau::where('account', $customer['account'])->first();
            $moneyCK = (int)$objTransactionChietKhauToSave['diem_da_nhan'];
            if($viChietKhauOfCus) {
                // cập nhật
                $moneyCK += (int)$viChietKhauOfCus['total_money'];
                $objViChietKhauToSave = [
                    'total_money' => $moneyCK,
                    'updated_at' => Helper::getMongoDate(),
                ];
                $viChietKhauOfCus->update($objViChietKhauToSave);
            }else {
                // thêm mới
                $objViChietKhauToSave = [
                    'account' => $customer['account'],
                    'total_money' => $moneyCK,
                    'status' => ViChietKhau::STATUS_ACTIVE,
                    'created_at' => Helper::getMongoDate(),
                ];
                ViChietKhau::insert($objViChietKhauToSave);
            }

            Logs::createLog([
                'type' => Logs::TYPE_UPDATED,
                'data_object' => $objViChietKhauToSave,
                'note' => "Khách hàng " . $customer['account'] . ' đã được cập nhật '.$moneyCK.' MPG vào ví chiết khấu ' . Member::getCurentAccount()
            ], Logs::OBJECT_VICHIETKHAU);*/


            // case 5-4-3-2- cho hoa hồng
            //$parentCus = Customer::where('ma_gioi_thieu', $customer['parent_id'])->first();



            #region case đơn < 20tr ko nợ + 6% vào ví công nợ @todo @kayn tạm comment nếu sau nó mở

            /*if($order['so_diem_can_mua'] < Orders::getMinDaiLy()) {
                $objTransactionCongNoToSave = [
                    'created_by' => [],
                    'created_at' => Helper::getMongoDate(),
                    'status' => Transaction::STATUS_ACTIVE,
                    'type_giaodich' => Transaction::DIEM_CONGNO,
                    'object' => Transaction::VICONGNO,
                    'diem_da_nhan' => $order['so_diem_duoc_nhan']*Orders::getPercentCongNoPhiVanChuyen(),
                    'tai_khoan_nguon' => $order['tai_khoan_nguon'],
                    'tai_khoan_nhan' => $order['tai_khoan_nhan'],
                    'order_id' => $id,
                ];
                Transaction::insertGetId($objTransactionCongNoToSave);

                $viCongNoOfCus = ViCongNo::where('account', $customer['account'])->first();
                if($viCongNoOfCus) {
                    // cập nhật
                    $moneyCN = (int)$viCongNoOfCus['total_money'] + (int)$objTransactionCongNoToSave['diem_da_nhan'];
                    $viCongNoOfCus->update(['total_money' => $moneyCN, 'updated_at' => Helper::getMongoDate()]);
                    Logs::createLog([
                        'type' => Logs::TYPE_UPDATED,
                        'data_object' => $objTransactionCongNoToSave,
                        'note' => "Ví công nợ của " . $customer['account'] . ' được thêm ' . $objTransactionCongNoToSave['diem_da_nhan'] .' MPG'
                    ], Logs::OBJECT_CONGNO);
                }else {
                    // thêm mới
                    $objViCongNoToSave = [
                        'account' => $customer['account'],
                        'total_money' => $objTransactionCongNoToSave['diem_da_nhan'],
                        'status' => ViCongNo::STATUS_ACTIVE,
                        'created_at' => Helper::getMongoDate(),
                    ];
                    ViCongNo::insert($objViCongNoToSave);
                    Logs::createLog([
                        'type' => Logs::TYPE_CREATE,
                        'data_object' => $objTransactionCongNoToSave,
                        'note' => "Ví công nợ của " . $customer['account'] . ' được thêm ' . $objTransactionCongNoToSave['diem_da_nhan'] .' MPG'
                    ], Logs::OBJECT_CONGNO);
                }

            }*/
            #endregion


            // case trả tiền hoa hồng 5-4-3-2-1

            // danh sách dòng họ gần nhất
            if($customer['chuc_danh'] == Customer::IS_MPMART) {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemMpMartHoaHongForF(@$order['percents']));
            }else {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemHoaHongForF(@$order['percents']));
            }

            /*if(!isset($order['mpmart'])) {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemHoaHongForF());
            }else {
                $this->_update_hoahong($order, $customer, false, true, Orders::getPrecentDiemMpMartHoaHongForF());
            }*/
        }

        return eView::getInstance()->getJsonSuccess('Cập nhật chuyển điểm thành công!');
    }

    function _update_hoahong($obj, $customer, $vitieudung = false, $vihoahong = false, $listPercentHoaHong) {
        $temp = [];
        $giaPha = Customer::buildTreeNguocBaoGomCaGoc('', $temp, $customer['account'],Customer::floor); // gia phả dòng họ
        if($giaPha) {
            foreach ($giaPha as $k => $g) {
                $saveHoaHong = [
                    'account' => $g['account'],
                    'total_money' => $obj['so_diem_duoc_nhan']*$listPercentHoaHong[$k],
                    'created_at' => Helper::getMongoDate(),
                    'status' => BaseModel::STATUS_ACTIVE,
                ];
                $saveTransactionHoaHong = [
                    'account' => $g['account'],
                    'diem_da_nhan' => $obj['so_diem_duoc_nhan']*$listPercentHoaHong[$k],
                    'tai_khoan_nguon' => Customer::getTaiKhoanToSaveDb($customer),
                    'tai_khoan_nhan' => Customer::getTaiKhoanToSaveDb($g),
                    'type_giaodich' => Transaction::DIEM_HOAHONG,
                    'created_at' => Helper::getMongoDate(),
                    'status' => BaseModel::STATUS_ACTIVE,
                    'order_id' => $obj['_id'],
                ];

                // kiểm tra xem ví của tk này đã được tạo hay chưa.
                $viHoaHongOfCus = ViTieuDung::where('account', $g['account'])->first();
                if($viHoaHongOfCus) {
                    $moneyHH = (int)$viHoaHongOfCus['total_money'] + (int)$saveTransactionHoaHong['diem_da_nhan'];
                    ViTieuDung::where('account', $g['account'])->update(['total_money' => $moneyHH, 'updated_at' => Helper::getMongoDate()]);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_UPDATED,
                        'object_id' => (string)$viHoaHongOfCus['_id'],
                        'note' => 'Ví tiêu dùng của acc: ' . @$viHoaHongOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, $viHoaHongOfCus->toArray(), ViTieuDung::find($viHoaHongOfCus['_id'])->toArray());
                }else {
                    $id = ViTieuDung::insertGetId($saveHoaHong);
                    Logs::createLogNew([
                        'type' => Logs::TYPE_CREATE,
                        'object_id' => (string)$id,
                        'note' => 'Ví tiêu dùng của acc: ' . @$viHoaHongOfCus['account'] . ' đã được cập nhật thêm MPG từ đơn hàng ' . (string)$obj['_id']
                    ], ViTieuDung::table_name, [], ViTieuDung::find($id)->toArray());
                }
                Transaction::insert($saveTransactionHoaHong);

            }
        }
    }

}
