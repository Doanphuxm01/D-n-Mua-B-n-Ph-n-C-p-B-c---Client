<?php


namespace App\Http\Controllers\FrontEnd\FeCart;

use App\Elibs\eView;
use App\Elibs\HtmlHelper;
use App\Elibs\Helper;
use App\Http\Models\Logs;
use App\Elibs\EmailHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Agency;
use App\Http\Models\Bank;
use App\Http\Models\BaseModel;
use App\Elibs\Cart;
use App\Http\Models\Customer;
use App\Http\Models\KhoDiem;
use App\Http\Models\Location;
use App\Http\Models\Member;
use App\Http\Models\Order;
use App\Http\Models\Product;
use App\Http\Models\PurchaseOrder;
use App\Http\Models\QuaTang;
use App\Http\Models\Transaction;
use App\Http\Models\ViTieuDung;
use Illuminate\Http\Request;
class FeCart extends Controller
{
    protected $GUEST_CART_KEY = 'GuestCart';
    protected $AUTH_CART_KEY = 'AuthCart';
    public function index($action =  '') {

        $action = str_replace('-', '_', $action);
        if(method_exists($this, $action)){
            return $this->$action();
        }
        return $this->cart();
    }

    function cart() {
        $tpl = [];
        $lsObj = Cart::getInstance()->content();
        $tpl['lsObj'] = $lsObj;
        $tpl['lsQuaTang'] = $this->giftCaiDauBoi($lsObj);
        $tpl['info'] = Member::getCurent();
        HtmlHelper::getInstance()->setTitle('Giỏ hàng');

        return eView::getInstance()->setView(__DIR__, 'home', $tpl);
    }

    function save() {
        $_action = Request::capture()->input('ref');
        $_action = '_save_'.$_action;
        if(method_exists($this, $_action)){
            return $this->$_action();
        }
        return eView::getInstance()->setView404();
    }

    function load() {
        $number = Cart::getInstance()->get('number', false);
        return eView::getInstance()->getJsonSuccess('Thông tin giỏ hàng', ['number' => $number]);
    }

    function giftCaiDauBoi($lsObj) {
        $quatang4Cart = [];
        $quatang = \DB::table(QuaTang::table_name)->where('status', QuaTang::STATUS_ACTIVE)->orderBy('max', 'DESC')->get()->keyBy('_id')->toArray();
        if(!empty($quatang)){
            foreach ($quatang as $k => $qt) {
                if (isset($qt['min']) && isset($qt['max']) && $qt['min'] <= $lsObj['grandTotal'] && $lsObj['grandTotal'] <= $qt['max'] || $lsObj['grandTotal'] > @$qt['max']) {
                    $quatang4Cart = $qt;
                    break;
                }
            }
            $currentMember = Member::getMemberByAccount(Member::getCurentAccount());

            if (!empty($quatang4Cart)) {
                $danh_sach_qua_tang = Product::table(Product::table_name)->whereIn('_id', array_column($quatang4Cart['san_pham_ap_dung'], 'id'))->get()->keyBy('_id')->toArray();
                $quatang4Cart['san_pham_ap_dung'] = collect($quatang4Cart['san_pham_ap_dung'])->keyBy('id')->toArray();
                if($danh_sach_qua_tang) {
                    foreach ($danh_sach_qua_tang as $k => &$item) {
                        unset($item['amount']);
                        $amount = @$quatang4Cart['san_pham_ap_dung'][$k]['amount'];
                        $quatang4Cart['san_pham_ap_dung'][$k] = [
                            'name' => @$item['name'],
                            'sku' => @$item['sku'],
                            'alias' => @$item['alias'],
                            'regularPrice' => @$item['regularPrice'],
                            'finalPrice' => @$item['finalPrice'],
                            'avatar_url' => @$item['avatar_url'],
                            'amount' => @$amount,
                        ];
                    }

                }
                if(isset($currentMember['gifts']) && !empty($currentMember['gifts']) && in_array((string)$quatang4Cart['_id'], $currentMember['gifts'])) {
                    $quatang4Cart['da_tang'] = true;
                }
            }
        }
        return $quatang4Cart;
    }


    function shipping() {
        $tpl = [];
        $lsObj = Cart::getInstance()->content();
        if($lsObj['number'] > 0) {
            $tpl['lsObj'] = $lsObj;
            $tpl['action'] = 'shipping';
            $auth = false;  // giả lập là guest
            if(!$auth) {
                $guest = Helper::getSession($this->GUEST_CART_KEY);
                if(!isset($guest) || empty($guest)) {
                    return redirect(public_link('checkout/shipping'));
                }
                $info = $guest;
            }else {
                $auth = Helper::getSession($this->AUTH_CART_KEY);
                if(!isset($auth) || empty($auth)) {
                    return redirect(public_link('checkout/shipping'));
                }
                $info = $auth;
            }
            $tpl['info'] = $info;
            HtmlHelper::getInstance()->setTitle('Thông tin giao hàng | Fxbot.com');
            return eView::getInstance()->setView(__DIR__, 'list-step', $tpl);
        }
        return redirect()->route('FeCart');
    }

    private function _save_shipping() {
        $vitieudung = ViTieuDung::getViByAccount(Member::getCurentAccount());
        $currentMemberAccount = Member::getCurentAccount();
        $currentMember = Member::getMemberByAccount($currentMemberAccount);
        if($currentMember) {
            $currentMember = $currentMember->toArray();
        }
        $content = Cart::getInstance()->content();

        if($content['number'] <= 0) {
            return eView::getInstance()->getJsonError("Giỏ hàng chưa có sản phẩm. Vui lòng lựa chọn sản phẩm vào giỏ hàng.");
        }
        foreach ($content['details'] as $it) {
            $ids =  [$it['sku']];
        }
        $prds = Product::whereIn('sku', $ids)->get();
        foreach ($prds as $p) {
            if (isset($content['details'][$p['sku']]) && $p['amount'] < $content['details'][$p['sku']]['amount']) {
                return eView::getInstance()->getJsonError("Sản phẩm trong kho chỉ còn tối đa ".$p['amount']. " sản phẩm");
            }elseif(!isset($content['details'][$p['sku']])) {
                return eView::getInstance()->getJsonError("Không tìm thấy sản phẩm có mã ".$p['sku']. '. Vui lòng tìm kiếm sản phẩm khác');
            }
        }
        $payMethod = Request::capture()->input('pay-method', 'payment_vitieudung');
        $resPay = PurchaseOrder::paymentType(false, $payMethod);
        if (!$resPay) {
            return eView::getInstance()->getJsonError("Hình thức thanh toán không tồn tại.");
        }
        if (Member::getCurrentChucDanh() == Member::IS_DAILY || Member::getCurrentChucDanh() == Member::IS_MPMART) {

            if ($resPay['id'] == PurchaseOrder::PAYMENT_KHODIEM) {
                $khodiem = KhoDiem::getViByAccount(Member::getCurentAccount());
                if($khodiem['total_money'] < $content['grandTotal']) {
                    return eView::getInstance()->getJsonSuccess('Kho diểm của bạn hiện tại không đủ để thực hiện giao dịch này. Bạn cần mua thêm điểm để có thể thực hiện giao dịch này.');
                }
            }else if ($resPay['id'] == PurchaseOrder::PAYMENT_VITIEUDUNG) {
                if($vitieudung['total_money'] - $vitieudung['so_diem_treo_gio'] < $content['grandTotal']) {
                    return eView::getInstance()->getJsonSuccess('Ví tiêu dùng của bạn hiện tại không đủ để thực hiện giao dịch này. Bạn cần mua thêm điểm để có thể thực hiện giao dịch này.');
                }
            }else {
                return eView::getInstance()->getJsonError("Hình thức thanh toán không tồn tại.");
            }



        }elseif(Member::getCurrentChucDanh() == Member::IS_CTV) {
            if($vitieudung['total_money'] - $vitieudung['so_diem_treo_gio'] < $content['grandTotal']) {
                return eView::getInstance()->getJsonSuccess('Ví tiêu dùng của bạn hiện tại không đủ để thực hiện giao dịch này. Bạn cần mua thêm điểm để có thể thực hiện giao dịch này.');
            }
        }

        if($content['number'] > 0) {
            $data = Request::capture()->input('order');
            $data['fullname'] = strip_tags(trim(@$data['full_name']));
            $data['phone'] = strip_tags(trim(@$data['telephone']));
            unset($data['telephone'], $data['full_name']);
            $data['email'] = strip_tags(trim(@$data['email']));
            $data['city'] = strip_tags(trim(@$data['city']));
            $data['district'] = strip_tags(trim(@$data['district']));
            $data['town'] = strip_tags(trim(@$data['town']));
            $data['street'] = strip_tags(trim(@$data['street']));
            $data['note'] = strip_tags(trim(@$data['note']));
            if(!isset($data['fullname']) || $data['fullname'] == '') {
                return eView::getInstance()->getJsonError("Vui lòng nhập họ tên");
            }
            if(!isset($data['phone']) || $data['phone'] == '') {
                return eView::getInstance()->getJsonError("Vui lòng nhập số điện thoại");
            }
            if(!Helper::isPhoneNumber($data['phone'])) {
                return eView::getInstance()->getJsonError("Số điện thoại không hợp lệ");
            }
            if (isset($data['email']) && !empty($data['email']) && !Helper::isEmail($data['email'])) {
                return eView::getInstance()->getJsonError("Email không đúng định dạng");
            }
            if(!isset($data['city']) || $data['city'] == '') {
                return eView::getInstance()->getJsonError("Vui lòng nhập thành phố");
            }
            $city = Location::getBySlug($data['city']);
            if(!isset($city)) {
                return eView::getInstance()->getJsonError('Thành phố không tồn tại');
            }
            $data['city'] = [
                'name' =>  $city['name'],
                'id' =>  $city['slug'],
            ];
            if(!isset($data['district']) || $data['district'] == '') {
                return eView::getInstance()->getJsonError("Vui lòng nhập quận/huyện");
            }
            $district = Location::getBySlug($data['district']);
            if(!isset($district)) {
                return eView::getInstance()->getJsonError('Quận/huyện không tồn tại');
            }
            $data['district'] = [
                'name' =>  $district['name'],
                'id' =>  $district['slug'],
            ];
            if(!isset($data['town']) || $data['town'] == '') {
                return eView::getInstance()->getJsonError("Vui lòng nhập phường/xã");
            }
            $town = Location::getBySlug($data['town']);
            if(!isset($town)) {
                return eView::getInstance()->getJsonError('Phường/xã không tồn tại');
            }
            $data['town'] = [
                'name' =>  $town['name'],
                'id' =>  $town['slug'],
            ];
            if (!$data['dai_ly_tra_hang']) {
                return eView::getInstance()->getJsonError('Vui lòng chọn đại lý trả hàng');
            }
            $agency = Agency::getDayLyTraHangId($data['dai_ly_tra_hang']);
            if(!isset($agency)) {
                return eView::getInstance()->getJsonError('Đại lý trả hàng không tồn tại');
            }
            if(!isset($agency['member']) || $agency['status'] != Agency::STATUS_ACTIVE) {
                return eView::getInstance()->getJsonError('Đại lý trả hàng này hiện tại không hoạt động');
            }
            $customer = Member::getMemberByAccount($agency['member']['account']);
            if (!$customer) {
                return eView::getInstance()->getJsonError('Đại lý trả hàng này hiện tại không hoạt động');
            }
            if(Member::getCurrentChucDanh() == Member::IS_MPMART || Member::getCurrentChucDanh() == Member::IS_DAILY) {
                if (!isset($agency['is_cty']))
                    return eView::getInstance()->getJsonError('Bạn chỉ có thể chọn đại lý trả hàng là công ty Minh Phúc Group');
            }

            if($customer['account'] == Member::getCurentAccount()) {
                return eView::getInstance()->getJsonError('Bạn không thể chọn đại lý trả hàng là chính mình');
            }

            $data['agency'] = [
                'name' =>  $agency['name'],
                'id' =>  $agency['_id'],
                'account_chu_dai_ly' => $agency['member']['account']
            ];
            // lấy ra quà tặng và validate chỉ đc áp dụng tại cty
            $gifts = $this->giftCaiDauBoi($content);
            if(isset($agency['is_cty']) && $agency['is_cty'] == true) {
                if (!empty($gifts) && !isset($gifts['da_tang'])) {
                    if(!isset($currentMember['gifts'][0])) {
                        $currentMember['gifts'][] = (string)$gifts['_id'];
                    }else {
                        $currentMember['gifts'] = [];
                        array_push($currentMember['gifts'], (string)$gifts['_id']);
                        array_unique($currentMember['gifts']);
                    }
                    $data['gifts'] = $gifts['san_pham_ap_dung'];
                }
            }
            unset($data['dai_ly_tra_hang']);
            if(!isset($data['street']) || $data['street'] == '') {
                return eView::getInstance()->getJsonError("Vui lòng nhập địa chỉ");
            }

            $auth = true;  // giả lập là guest
            if(!$auth) {
                $guest = Helper::getSession($this->GUEST_CART_KEY);
                Helper::setSession($this->GUEST_CART_KEY, $data);
                $ref  = public_link('checkout/payment');

                return eView::getInstance()->getJsonSuccess('Cập nhật địa chỉ thành công!', ['redirect' => $ref]);
            }else {

                $order = array_merge($data, $content);
                $order['created_at'] = Helper::getMongoDateTime();
                $order['status'] = PurchaseOrder::STATUS_PENDING;
                $order['created_by'] = Member::getCreatedByToSaveDb();
                $order['payment_type'] = $resPay['id'];
                $order['code'] = uniqid('ord');
                $order['token_tracking'] = '';
                if($order['email']) {
                    $mail['template'] = "mail.order";
                    $mail['order'] = $order;
                    EmailHelper::sendMail($order['email'], $mail);
                }
                $order_id = PurchaseOrder::createOrder($order);

                if (!empty($gifts) && !isset($gifts['da_tang'])) {
                    Member::getMemberByAccount($currentMemberAccount)->update(['gifts' => $currentMember['gifts']]);
                    // cập nhật lại số lượng

                    foreach ($prds as $item) {
                        $quantity = (int)$item['amount'] - (int)$content['details'][$item['sku']]['amount'];
                        $update = [
                            'amount' => $quantity,
                            'updated_by' => Member::getCreatedByToSaveDb(),
                            'updated_at' => Helper::getMongoDate(),
                        ];
                        $item->update($update);
                        Logs::createLogNew([
                            'type' => Logs::OBJECT_KHOHANG,
                            'object_id' => (string)@$item['_id'],
                            'note' => 'Cập nhật số lượng hàng trong kho của sản phẩm ' . (string)@$item['_id']
                        ], Product::table_name, $item->toArray(), Product::select('_id', 'name', 'amount', 'sku', 'description')->find(@$item['_id']));
                    }
                }
                if ($resPay['id'] == PurchaseOrder::PAYMENT_KHODIEM) {
                    $vitieudung = KhoDiem::getViByAccount(Member::getCurentAccount());
                    if($vitieudung) {
                        if (!isset($vitieudung['so_diem_treo_gio'])) {
                            $vitieudung['so_diem_treo_gio'] = 0;        // số điểm treo giò
                        }
                        $up = [
                            'account' => $customer['account'],
                            'total_money' =>  $order['grandTotal'],
                            'created_at' =>  Helper::getMongoDate(),
                        ];
                        $sotientratruoc = $up['total_money'] + $vitieudung['so_diem_treo_gio'];
                        $vitieudung->update(['so_diem_treo_gio' => $sotientratruoc]);
                        // comment kho điểm khi đã đặt hàng thành công ở đại lý
                        //  $khodiem = KhoDiem::getViByAccount($customer['account']);
                        /*if($khodiem) {
                            $up['total_money'] = $khodiem['total_money'] + $order['grandTotal'];
                            KhoDiem::where('account', $customer['account'])->update($up);
                        }else {
                            $up['status'] = KhoDiem::STATUS_ACTIVE;
                            KhoDiem::insertGetId($up);
                        }*/
                    }
                }else if ($resPay['id'] == PurchaseOrder::PAYMENT_VITIEUDUNG) {
                    $vitieudung = ViTieuDung::getViByAccount(Member::getCurentAccount());
                    if($vitieudung) {
                        if (!isset($vitieudung['so_diem_treo_gio'])) {
                            $vitieudung['so_diem_treo_gio'] = 0;        // số điểm treo giò
                        }
                        $up = [
                            'account' => $customer['account'],
                            'total_money' =>  $order['grandTotal'],
                            'created_at' =>  Helper::getMongoDate(),
                        ];
                        $sotientratruoc = $up['total_money'] + $vitieudung['so_diem_treo_gio'];
                        $vitieudung->update(['so_diem_treo_gio' => $sotientratruoc]);
                        // comment kho điểm khi đã đặt hàng thành công ở đại lý
                        //  $khodiem = KhoDiem::getViByAccount($customer['account']);
                        /*if($khodiem) {
                            $up['total_money'] = $khodiem['total_money'] + $order['grandTotal'];
                            KhoDiem::where('account', $customer['account'])->update($up);
                        }else {
                            $up['status'] = KhoDiem::STATUS_ACTIVE;
                            KhoDiem::insertGetId($up);
                        }*/
                    }
                }

                // cập nhật lại số lượng
                if(isset($agency['is_cty']) && $agency['is_cty'] == true) {
                    foreach ($prds as $item) {
                        $quantity = (int)$item['amount'] - (int)$content['details'][$item['sku']]['amount'];
                        $update = [
                            'amount' => $quantity,
                            'updated_by' => Member::getCreatedByToSaveDb(),
                            'updated_at' => Helper::getMongoDate(),
                        ];
                        $item->update($update);
                        Logs::createLogNew([
                            'type' => Logs::OBJECT_KHOHANG,
                            'object_id' => (string)@$item['_id'],
                            'note' => 'Cập nhật số lượng hàng trong kho của sản phẩm ' . (string)@$item['_id']
                        ], Product::table_name, $item->toArray(), Product::select('_id', 'name', 'amount', 'sku', 'description')->find(@$item['_id']));
                    }
                }


                $this->destroyCart();
                $ref  = public_link('checkout/success');
                return eView::getInstance()->getJsonSuccess('Đặt hàng thành công!', ['redirect' => $ref]);

                return eView::getInstance()->getJsonSuccess('Cập nhật địa chỉ thành công!', ['redirect' => $ref]);
            }

        }
        return eView::getInstance()->getJsonSuccess('Bạn cần chọn mua sản phẩm để có thể tiến hành thanh toán.', ['link' => '/']);
    }

    function payment() {
        $tpl = [];
        $lsObj = Cart::getInstance()->content();
        if($lsObj['number'] > 0) {
            $tpl['lsObj'] = $lsObj;
            $auth = false;  // giả lập là guest
            if(!$auth) {
                $guest = Helper::getSession($this->GUEST_CART_KEY);
                if(!isset($guest) || empty($guest)) {
                    return redirect(public_link('checkout/shipping'));
                }
                $info = $guest;
            }else {
                $auth = Helper::getSession($this->AUTH_CART_KEY);
                if(!isset($auth) || empty($auth)) {
                    return redirect(public_link('checkout/shipping'));
                }
                $info = $auth;
            }
            $tpl['action'] = 'payment';
            $tpl['info'] = $info;
            $tpl['payments'] = Order::getListPayment();
            HtmlHelper::getInstance()->setTitle('Thông tin thanh toán | Fxbot.com');
            return eView::getInstance()->setView(__DIR__, 'list-step', $tpl);
        }
        return redirect()->route('FeHome');
    }

    /*
     * @todo @kayn Xác nhận người dùng mail, có thể tạo luôn là member ko cần account. Khi xác nhận thì cần có màn hình confirm
     * */
    private function _save_payment() {
        $content = Cart::getInstance()->content();
        if($content['number'] > 0) {
            $auth = false;  // giả lập là guest
            if(!$auth) {
                $guest = Helper::getSession($this->GUEST_CART_KEY);
                if(!isset($guest) || empty($guest)) {
                    return redirect(public_link('checkout/shipping'));
                }
                $info = $guest;
                $info['auth'] = 'guest';
                $info['created_by'] = [
                    'REMOTE_ADDR' => @$_SERVER['REMOTE_ADDR'],
                    'HTTP_USER_AGENT' => @$_SERVER['HTTP_USER_AGENT'],
                    'HTTP_REFERER' => @$_SERVER['HTTP_REFERER'],
                    'REQUEST_METHOD' => @$_SERVER['REQUEST_METHOD'],
                ];
            }else {
                $auth = Helper::getSession($this->AUTH_CART_KEY);
                if(!isset($auth) || empty($auth)) {
                    return redirect(public_link('checkout/shipping'));
                }
                $info = $auth;
                $info['auth'] = 'customer';
                /*
                 * @todo @kayn lấy created_by khách hàng đã đăng nhập
                 * */
                /*$info['created_by'] = [
                    'id'      => Member::getCurentId(),
                    'name'    => Member::getCurrentName(),
                    'account' => Member::getCurentAccount(),
                    'email' => Member::getCurrentEmail(),
                ];*/
            }
            $payMethod = Request::capture()->input('pay-method');
            $resPay = PurchaseOrder::getListPayment(false, $payMethod);
            if (!$resPay) {
                return eView::getInstance()->getJsonError("Hình thức thanh toán không tồn tại.");
            }
            if($payMethod == Order::BANK_TRANSFER_PAYMENT) {
                $bank = Request::capture()->input('bank');
                $currentBank = Bank::getBankBySlug($bank);
                // tạm thời lấy all key của bank
                if(isset($currentBank)) {
                    $bank = $currentBank;
                }
            }
            $order = array_merge($info, $content);
            $order['created_at'] = Helper::getMongoDateTime();
            $mail['template'] = "mail.order";
            EmailHelper::sendMail($order['email'], $mail);
            Order::createOrder($order);
            $this->destroyCart();
            $ref  = public_link('checkout/success');

            return eView::getInstance()->getJsonSuccess('Đặt hàng thành công!', ['redirect' => $ref]);
        }
        return redirect()->route('FeHome');
    }


    function success() {
        $tpl = [];
        $lsObj = Cart::getInstance()->content();
        if($lsObj['number'] > 0) {
            $tpl['lsObj'] = $lsObj;
            $tpl['action'] = 'success';
            HtmlHelper::getInstance()->setTitle('Đặt hàng thành công | Fxbot.com');
            return eView::getInstance()->setView(__DIR__, 'list-step', $tpl);
        }
        return redirect()->route('FeHome');
    }

    /*
     * @todo @kayn Cần thêm mã coupon, tính toán mã coupon cho từng sản phẩm, đơn hàng
     * */

    function addToCart() {
        $id= Request::capture()->input('id');
        $sku= Request::capture()->input('sku');
        $amount= Request::capture()->input('amount');
        $options= Request::capture()->input('options');
        $type= Request::capture()->input('type', '');
        $amount = (int)$amount;
        $configAmount = 10000;
        $obj = Cart::getInstance()->get($sku);

        if($amount > $configAmount) {
            return eView::getInstance()->getJsonError("Bạn đạt giới hạn mua với số lượng '.$configAmount.' sản phẩm.", $obj);
        }else if($amount < 1) {
            return eView::getInstance()->getJsonError("Số lượng sản phẩm không hợp lệ. Bạn vui lòng kiểm tra lại.", $obj);
        }
        return $this->_checkAndAddToCart($id, $sku, $amount, $options, $type);
    }

    /*
     * Kịck bản:
     * 1. Dữ liệu là 1 item được gửi từ client lên
     * 2. Kiểm tra dữ liệu đó có hợp lệ hay ko (tồn tại, số lượng hàng trong kho còn hay hết)
     * 3. Thêm vào session
     * 3.1 Name, Link, Options, finalPrice, regularPrice, amount, image, store, linkStore
     * */
    private function _checkAndAddToCart($id, $sku, $amount, $options, $type) {
        $obj = Product::where([
            ['status', BaseModel::STATUS_ACTIVE],
        ])->select('name', 'status', 'amount', 'sku', 'images', 'regularPrice', 'finalPrice')->find($id);
        if(!empty($obj)) {
            $obj = $obj->toArray();
            if(isset($obj['amount']) && $obj['amount'] > 0) {
                if ($obj['amount'] < $amount) {
                    return eView::getInstance()->getJsonError("Sản phẩm trong kho chỉ còn tối đa ".$obj['amount']. " sản phẩm");
                }
                if($type === Cart::TYPE_PRODUCT) {
                    $cart = Cart::getInstance()->get($obj['sku']??$obj['_id']);
                    if(isset($cart)) {
                        $amount += $cart['amount'];
                    }
                }
                $data = [
                    'id' => $obj['_id'],
                    'name' => $obj['name'],
                    'link' => link_detail($obj),
                    'amount' => $amount,
                    'sku' => $obj['sku'],
                    'finalPrice' => $obj['finalPrice'],
                    'regularPrice' => $obj['regularPrice'],
                    //'totalPrice' => $obj['finalPrice']*$amount,
                ];
                if(isset($obj['images']) && !empty($obj['images'])) {
                    $data['image'] = reset($obj['images']);
                }
                $result = Cart::getInstance()->add($data);
                if($result === true) {
                    return eView::getInstance()->getJsonSuccess('Thêm giỏ hàng thành công!');
                }
            }else {
                return eView::getInstance()->getJsonError("Sản phẩm đã hết hàng");
            }
            return eView::getInstance()->getJsonError("Sản phẩm đã hết hàng! Bạn vui lòng chọn sản phẩm khác");
        }
        return eView::getInstance()->getJsonError("Sản phẩm của bạn đã bị xóa");
    }

    public function cartRemove(){
        $id = Request::capture()->input('id', 0);
        if($id) {
            Cart::getInstance()->remove($id);
            $content = Cart::getInstance()->content();
            return eView::getInstance()->getJsonSuccess('Xoá sản phẩm ra khỏi giỏ hàng thành công!');
        }
        return eView::getInstance()->getJsonSuccess('Không tìm thấy sản phẩm trong giỏ hàng!');
    }

    function destroyCart() {
        Helper::delSession('ShopCart');
        return redirect(public_link('/'));
    }
}
