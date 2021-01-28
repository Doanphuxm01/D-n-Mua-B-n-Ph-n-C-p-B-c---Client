<?php


namespace App\Http\Controllers\AdminWithdrawal;


use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Customer;
use App\Http\Models\Member;
use App\Http\Models\MetaData;
use App\Http\Models\Orders;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTichLuy;
use App\Http\Models\Withdrawal;
use Illuminate\Http\Request;

class MngWithdrawalRequest extends Controller
{
    public function __construct() {
        if(!in_array(date('d'), Withdrawal::getArrayOpenRutTien())) {
            return eView::getInstance()->setView404();
        }
    }

    public function index($action = '')
    {
        if(!in_array(date('d'), Withdrawal::getArrayOpenRutTien())) {
            return eView::getInstance()->setView404();
        }
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->input();
        }
    }

    function input() {
        HtmlHelper::getInstance()->setTitle('Yêu cầu rút tiền');
        $tpl = array();
        $obj = Customer::find(Member::getCurentId());
        if(!$obj) {
            return eView::getInstance()->setView404();
        }
        // case ViTichLuy
        $vitichluy = ViTichLuy::getViByAccount(Member::getCurentAccount());
        $tpl['vitichluy'] = $vitichluy;

        // case ViHoaHong
        $vihoahong = ViHoaHong::getViByAccount(Member::getCurentAccount());
        $tpl['vihoahong'] = $vihoahong;
        $tpl['min_mpg'] = Orders::getMinMPGAfterRegister();
        $tpl['min_dai_ly'] = Orders::getMinDaiLy();
        $tpl['min_mpmart'] = Orders::getMinMPMart();

        $tpl['obj'] = $obj;
        if (!empty($_POST)) {
            $this->_save();
        }
        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }

    function _save() {

        $type_vi = Request::capture()->input('type_vi', '');
        $obj = Request::capture()->input('obj', []);
        $tpl[] = [];
        if(!isset($obj['so_tien_muon_rut']) || $obj['so_tien_muon_rut'] <= 0 || !is_numeric($obj['so_tien_muon_rut'])) {
            return eView::getInstance()->getJsonError('Số tiền bạn yêu cầu không hợp lệ.');
        }

        $objToSave = [
            'so_tien_muon_rut' => $obj['so_tien_muon_rut'],
            'status' => Withdrawal::STATUS_NO_PROCESS,
            'created_at' => Helper::getMongoDate(),
            'created_by' => [
                'id'      => Member::getCurentId(),
                'name'    => Member::getCurrentName(),
                'account' => Member::getCurentAccount(),
                'email' => Member::getCurrentEmail(),
            ],
        ];
        //case tk_ngan_hang
        $taikhoannganhang = explode('-xlxx-', $obj['tk_ngan_hang']);
        $tk_nganhang = [
            'tk_ngan_hang.so' => [
                '$in' => [$taikhoannganhang[0]]
            ],
            'tk_ngan_hang.id' => [
                '$in' => [$taikhoannganhang[1]]
            ],
            'account' => Member::getCurentAccount()
        ];
        $curentMember = Member::where($tk_nganhang)->first();
        if(!$curentMember) {
            return eView::getInstance()->getJsonError('Không tìm thấy dữ liệu, vui lòng kiểm tra lại');
        }
        $temp = MetaData::where('_id', $taikhoannganhang[1])->first();
        if ($temp) {
            $objToSave['tk_ngan_hang']['name'] = $temp['name'];
            $objToSave['tk_ngan_hang']['so'] = $taikhoannganhang[0];
            $objToSave['tk_ngan_hang']['id'] = $temp['_id'];
        }else {
            return eView::getInstance()->getJsonError('Tài khoản ngân hàng không hợp lệ');
        }
        if(!in_array($type_vi, ['vitichluy', 'vihoahong'])) {
            return eView::getInstance()->getJsonError('Yêu cầu của bạn không được hỗ trợ');
        }
        $vi = [];
        if($type_vi == 'vitichluy') {
            $vi = ViTichLuy::getViByAccount(Member::getCurentAccount());
        }
        if($type_vi == 'vihoahong') {
            $vi = ViHoaHong::getViByAccount(Member::getCurentAccount());
        }
        if (empty($vi)) {
            return eView::getInstance()->getJsonError('Ví của bạn không đủ số dư để thực hiện giao dịch này');
        }
        $phigiaodich = 3000;
        if($obj['so_tien_muon_rut'] <= 5000000){
            $phigiaodich = 2000;
        }elseif($obj['so_tien_muon_rut'] <= 10000000){
            $phigiaodich = 3000;
        }elseif($obj['so_tien_muon_rut'] > 10000000){
            $phigiaodich = 5000;
        }
        $sotiengiaodich = (double)$obj['so_tien_muon_rut'] + $phigiaodich;
        $soducuoi = $vi['total_money'] - $sotiengiaodich;
        if($soducuoi < Withdrawal::getLotVi()) {
            return eView::getInstance()->getJsonError('Số dư tối thiểu để duy trì hoạt động trên ví là '. Withdrawal::getLotVi(). 'đ');
        }

        $vi->update([
            'total_money' => $soducuoi,
        ]);
        $objToSave['so_tien_muon_rut'] = (double)$obj['so_tien_muon_rut'];

        $objToSave['phi_giao_dich'] = (double)$phigiaodich;
        $objToSave['so_du_cuoi'] = (double)$soducuoi;
        $objToSave['type_vi'] = $type_vi;
        Withdrawal::insert($objToSave);
        return eView::getInstance()->getJsonSuccess('Yêu cầu rút tiền của bạn đã gửi đi thành công.');
    }
}