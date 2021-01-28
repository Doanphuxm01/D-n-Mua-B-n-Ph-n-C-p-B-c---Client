<?php


namespace App\Http\Controllers\AdminBuyMpg;


use App\Elibs\EmailHelper;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Logs;
use App\Http\Models\Member;
use App\Http\Models\Menu;
use App\Http\Models\Orders;
use App\Http\Models\UnauthorizedPersonnel;
use Illuminate\Http\Request;

class MngBuyMPG extends Controller
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

    public function input()
    {
        HtmlHelper::getInstance()->setTitle('Mua MPG');
        $tpl = array();
        $tpl['min_mpg'] = Orders::getMinMPGAfterRegister();
        $tpl['min_dai_ly'] = Orders::getMinDaiLy();
        $tpl['min_mpmart'] = Orders::getMinMPMart();
        $tpl['chuc_danh'] = Member::getCurrentChucDanh();
        $tpl['is_ctv'] = Member::IS_CTV;
        $tpl['is_daily'] = Member::IS_DAILY;
        $tpl['is_mpmart'] = Member::IS_MPMART;
        $tpl['everychietkhautichluyctv'] = Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV();
        $tpl['everychietkhautieudungctv'] = Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV();
        $tpl['everychietkhautichluympmart'] = Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart();
        $tpl['everychietkhautieudungmpmart'] = Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart();
        if (!empty($_POST)) {
            $this->_save();
        }
        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }

    function _save_bak()
    {
        $obj = Request::capture()->input('obj', []);
        if (!empty($obj)) {
            foreach ($obj as $_o => $o) {
                $obj[$_o] = trim(strip_tags($o));
            }
        } else {
            return eView::getInstance()->getJsonError('Vui lòng cập nhật đầy đủ thông tin!');
        }
        $curentMember = Member::getCurent();
        if (!$curentMember) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
        }

        if (!isset($obj['so_diem_mua'])) {
            return eView::getInstance()->getJsonError('Số điểm mua không được bỏ trống');
        }

        if (!is_numeric($obj['so_diem_mua'])) {
            return eView::getInstance()->getJsonError('Số điểm mua không hợp lệ');
        }

        if ($obj['so_diem_mua'] < Orders::getMinMPGAfterRegister()) {
            return eView::getInstance()->getJsonError('Bạn cần mua ít nhất 50.000 điểm MPG');
        }

        if ($obj['so_diem_mua'] >= Orders::getMinDaiLy()) {
            //$saveMember['chuc_danh'] = Member::IS_DAILY;
            // @todo @kayn check có nợ hay ko, nếu ko có thì default là ko nợ
            if (isset($obj['co_no_hay_khong']) && !in_array($obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES, Member::MP_MART])) {
                $saveMember['debt'] = Member::DEBT_NO;
            } elseif (!isset($obj['co_no_hay_khong'])){
                return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
            }else {
                if ($obj['co_no_hay_khong'] === Member::MP_MART) {
                    if ($obj['so_diem_mua'] < Orders::getMinMPMart()) {
                        return eView::getInstance()->getJsonError('Bạn cần mua ít nhất ' . Orders::getMinMPMart() . ' điểm MPG để trở thành MPMart');
                    } else {
                        $saveMember['mpmart'] = $obj['co_no_hay_khong'];
                    }
                } else {
                    $saveMember['debt'] = $obj['co_no_hay_khong'];
                }
            }
        }
        elseif (Member::getCurrentChucDanh() == Member::IS_CTV) {
            if (in_array(@$obj['everyday_percent_ctv'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV()])) {
                if ($obj['everyday_percent_ctv'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV()) {
                    $saveMember['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY;
                } elseif ($obj['everyday_percent_ctv'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV()) {
                    $saveMember['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG;
                } else {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                }
                $saveMember['everyday_percent_ctv_value'] = $obj['everyday_percent_ctv'];
            }else {
                return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
            }
        }

        $id = $curentMember['_id'];
        $saveMember['buympg'] = $obj['so_diem_mua'];
        $token = Helper::buildTokenString($id);
        $saveMember['token'] = $token;
        Member::where('_id', $curentMember['_id'])->update($saveMember);

        Logs::createLog([
            'type' => Logs::TYPE_CREATE,
            'data_object' => $saveMember,
            'object_id' => $curentMember['_id'],
            'note' => "Khách hàng " . @$curentMember['can_cuoc_cong_dan'] ? @$curentMember['fullname'] . ' - CCCD: ' . @$curentMember['can_cuoc_cong_dan'] : @$curentMember['email'] . ' đã đặt mua ' . $saveMember['buympg'] . ' MPG'
        ], 'customer');
        $customer = Member::where('_id', $id)->first();
        $order['so_diem_can_mua'] = $saveMember['buympg'];
        $order['url'] = admin_link('mua-diem/input?id=' . $id . '&token=' . $token);
        if(Member::getCurrentEmail()) {
            $tpl['success'] = true;
            $tpl['name'] = 'Xác thực email';
            $tpl['order'] = $order;
            $tpl['subject'] = '[Hệ thống quản lý MinhPhucGroup] Thông báo đặt mua MPG thành công!';
            $tpl['template'] = "mail.order_buympg";
            EmailHelper::sendMail($customer['email'], $tpl);
        }
        /*Xử lý affiliate*/
        $saveOrder = [
            'created_by' => [
                'id' => (string)$customer['_id'],
                'name' => $customer['fullname'] ?? $customer['name'],
                'account' => $customer['account'],
                'email' => @$customer['email'],
                'phone' => $customer['phone'],
                'verified' => $customer['verified'],
            ],
            'created_at' => Helper::getMongoDate(),
            'status' => Orders::STATUS_NO_PROCESS,
            'type' => Orders::ORDER_BUY_MPG,
            'so_diem_can_mua' => (int)$obj['so_diem_mua'] ?? 0,
            'tai_khoan_nguon' => [
                'id' => (string)$customer['_id'],
                'name' => $customer['fullname'] ?? $customer['name'],
                'account' => $customer['account'],
                'email' => @$customer['email'],
                'phone' => $customer['phone'],
                'verified' => $customer['verified'],
            ],
            'tai_khoan_nhan' => [
                'id' => (string)$customer['_id'],
                'name' => $customer['fullname'] ?? $customer['name'],
                'account' => $customer['account'],
                'email' => @$customer['email'],
                'phone' => $customer['phone'],
                'verified' => $customer['verified'],
            ],
        ];


        if ($obj['so_diem_mua'] >= Orders::getMinDaiLy()) {
            if (isset($saveMember['mpmart'])) {
                $saveOrder['mpmart'] = $saveMember['mpmart'];
                // chiết khấu mpmart
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauDaiLy();
            } else {
                $saveOrder['debt'] = $saveMember['debt'];
                if ($saveMember['debt'] == Orders::DEBT_YES) {
                    $saveOrder['so_diem_duoc_nhan'] = $saveOrder['so_diem_can_mua'] - $saveOrder['so_diem_can_mua'] * Orders::getPercentCongNoDebtYes();
                    $saveOrder['cong_no'] = $saveOrder['so_diem_can_mua'] * Orders::getPercentCongNoDebtYes();
                } else {
                    $saveOrder['so_diem_duoc_nhan'] = $saveOrder['so_diem_can_mua'];
                }
                // chiết khấu dai ly
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauDaiLy();
            }
        } elseif (Member::getCurrentChucDanh() == Member::IS_CTV) {
            $saveOrder['everyday_percent_ctv_value'] = $saveMember['everyday_percent_ctv_value'];
            $saveOrder['everyday_percent_ctv_type'] = $saveMember['everyday_percent_ctv_type'];
            // chiết khấu ctv
            $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauCtv();
        }
        $idOrder = (string)Orders::insertGetId($saveOrder);
        /*End xử lý affiliate*/

        $return['link'] = Menu::buildLinkAdmin('mua-diem/input?id=' . $id);

        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $return);
    }

    function _save()
    {
        $obj = Request::capture()->input('obj', []);
        if (!empty($obj)) {
            foreach ($obj as $_o => $o) {
                $obj[$_o] = trim(strip_tags($o));
            }
        } else {
            return eView::getInstance()->getJsonError('Vui lòng cập nhật đầy đủ thông tin!');
        }
        $curentMember = Member::getCurent();
        if (!$curentMember) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
        }
        if($curentMember['chuc_danh'] != Member::getCurrentChucDanh()) {
            $return['link'] = public_link('/auth/logout');
            Member::setLogOut();
            return eView::getInstance()->getJsonSuccess('Dữ liệu hệ thống đã được thay đổi. Vui lòng đăng nhập lại để cập nhật dữ liệu mới nhất', $return);
        }
        if (!isset($obj['so_diem_mua'])) {
            return eView::getInstance()->getJsonError('Số điểm mua không được bỏ trống');
        }

        if (!is_numeric($obj['so_diem_mua'])) {
            return eView::getInstance()->getJsonError('Số điểm mua không hợp lệ');
        }

        if ($obj['so_diem_mua'] < Orders::getMinMPGAfterRegister()) {
            return eView::getInstance()->getJsonError('Bạn cần mua ít nhất 50.000 điểm MPG');
        }
        if (Member::getCurrentChucDanh() == Member::IS_DAILY) {
            //$saveMember['chuc_danh'] = Member::IS_DAILY;
            // @todo @kayn check có nợ hay ko, nếu ko có thì default là ko nợ
            if(isset($obj['co_no_hay_khong'])) {
                if (!in_array(@$obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES])) {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                } else {
                    $saveMember['debt'] = @$obj['co_no_hay_khong']?:Member::DEBT_NO;
                }
            }
            else if(isset($obj['everyday_percent_mpmart'])) {
                if (in_array(@$obj['everyday_percent_mpmart'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()])) {
                    if ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart()) {
                        $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY;
                    } elseif ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()) {
                        $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;
                    } else {
                        return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                    }
                    $saveMember['everyday_percent_mpmart_value'] = $obj['everyday_percent_mpmart'];
                }else {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                }
            }else {
                return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
            }
        }
        elseif (Member::getCurrentChucDanh() == Member::IS_CTV) {
            if(isset($obj['co_no_hay_khong'])) {
                if (!in_array(@$obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES])) {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                } else {
                    $saveMember['debt'] = $obj['co_no_hay_khong'];
                }
            }else if(isset($obj['everyday_percent_mpmart'])) {
                if (in_array(@$obj['everyday_percent_mpmart'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()])) {
                    if ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart()) {
                        $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY;
                    } elseif ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()) {
                        $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;
                    } else {
                        return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                    }
                    $saveMember['everyday_percent_mpmart_value'] = $obj['everyday_percent_mpmart'];
                }else {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                }
            }else if(isset($obj['everyday_percent_ctv'])) {
                if (in_array(@$obj['everyday_percent_ctv'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV()])) {
                    if ($obj['everyday_percent_ctv'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoCTV()) {
                        $saveMember['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CHIETKHAU_TICHLUY;
                    } elseif ($obj['everyday_percent_ctv'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoCTV()) {
                        $saveMember['everyday_percent_ctv_type'] = Orders::EVERYDAY_PERCENT_CTV_CKIETKHAU_TIEUDUNG;
                    } else {
                        return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                    }
                    $saveMember['everyday_percent_ctv_value'] = $obj['everyday_percent_ctv'];
                }else {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                }
            }else {
                return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
            }
        }
        elseif (Member::getCurrentChucDanh() == Member::IS_MPMART) {
            if (in_array(@$obj['everyday_percent_mpmart'], [Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart(), Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()])) {
                if ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTichLuyDebtNoMpMart()) {
                    $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TICHLUY;
                } elseif ($obj['everyday_percent_mpmart'] == Orders::getEveryDayPercentChietKhauTieuDungDebtNoMpMart()) {
                    $saveMember['everyday_percent_mpmart_type'] = Orders::EVERYDAY_PERCENT_MPMART_CHIETKHAU_TIEUDUNG;
                } else {
                    return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
                }
                $saveMember['everyday_percent_mpmart_value'] = $obj['everyday_percent_mpmart'];
            }else {
                return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ');
            }
        }

        $id = $curentMember['_id'];
        $saveMember['buympg'] = $obj['so_diem_mua'];
        $token = Helper::buildTokenString($id);
        $saveMember['token'] = $token;
        Member::where('_id', $curentMember['_id'])->update($saveMember);

        Logs::createLog([
            'type' => Logs::TYPE_CREATE,
            'data_object' => $saveMember,
            'object_id' => $curentMember['_id'],
            'note' => "Khách hàng " . @$curentMember['can_cuoc_cong_dan'] ? @$curentMember['fullname'] . ' - CCCD: ' . @$curentMember['can_cuoc_cong_dan'] : @$curentMember['email'] . ' đã đặt mua ' . $saveMember['buympg'] . ' MPG'
        ], 'customer');
        $customer = Member::where('_id', $id)->first();
        $order['so_diem_can_mua'] = $saveMember['buympg'];
        $order['url'] = admin_link('mua-diem/input?id=' . $id . '&token=' . $token);
        if(Member::getCurrentEmail()) {
            $tpl['success'] = true;
            $tpl['name'] = 'Xác thực email';
            $tpl['order'] = $order;
            $tpl['subject'] = '[Hệ thống quản lý MinhPhucGroup] Thông báo đặt mua MPG thành công!';
            $tpl['template'] = "mail.order_buympg";
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


        if (Member::getCurrentChucDanh() == Member::IS_DAILY) {
            if(isset($obj['co_no_hay_khong'])) {
                if (in_array(@$obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES])) {
                    $saveOrder['debt'] = $saveMember['debt'];
                    if ($saveMember['debt'] == Orders::DEBT_YES) {
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
                    $saveOrder['everyday_percent_mpmart_value'] = $saveMember['everyday_percent_mpmart_value'];
                    $saveOrder['everyday_percent_mpmart_type'] = $saveMember['everyday_percent_mpmart_type'];
                    // chiết khấu mpmart
                    $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauMpMart();
                }
            }
        } elseif (Member::getCurrentChucDanh() == Member::IS_CTV) {
            if(isset($obj['co_no_hay_khong'])) {
                if (in_array(@$obj['co_no_hay_khong'], [Member::DEBT_NO, Member::DEBT_YES])) {
                    $saveOrder['debt'] = $saveMember['debt'];
                    if ($saveMember['debt'] == Orders::DEBT_YES) {
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
                    $saveOrder['everyday_percent_mpmart_value'] = $saveMember['everyday_percent_mpmart_value'];
                    $saveOrder['everyday_percent_mpmart_type'] = $saveMember['everyday_percent_mpmart_type'];
                    // chiết khấu mpmart
                    $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauMpMart();
                }
            }else if(isset($obj['everyday_percent_ctv'])) {
                $saveOrder['everyday_percent_ctv_value'] = $saveMember['everyday_percent_ctv_value'];
                $saveOrder['everyday_percent_ctv_type'] = $saveMember['everyday_percent_ctv_type'];
                // chiết khấu ctv
                $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauCtv();
            }

        }elseif (Member::getCurrentChucDanh() == Member::IS_MPMART) {
            $saveOrder['everyday_percent_mpmart_value'] = $saveMember['everyday_percent_mpmart_value'];
            $saveOrder['everyday_percent_mpmart_type'] = $saveMember['everyday_percent_mpmart_type'];
            // chiết khấu mpmart
            $saveOrder['so_diem_vi_chiet_khau'] = (int)$obj['so_diem_mua']*Orders::getPercentChietKhauMpMart();
        }
        $saveOrder['percents'] = UnauthorizedPersonnel::getUn();
        $idOrder = (string)Orders::insertGetId($saveOrder);
        /*End xử lý affiliate*/

        $return['link'] = Menu::buildLinkAdmin('mua-diem/input?id=' . $id);

        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $return);
    }

}