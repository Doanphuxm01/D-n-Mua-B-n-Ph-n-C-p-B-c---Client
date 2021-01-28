<?php

namespace App\Http\Models;


use App\Elibs\eView;
use Illuminate\Support\Facades\DB;

class Menu extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_menu';
    protected $table = self::table_name;
    static $unguarded = TRUE;


    static function getMainMenuBackEnd($selected = '')
    {

        $item = [
            'label' => 'Cá nhân',
            'group' => 'AdminMember',
            'link' => 'javascript:void(0)',
            'class'=> 'should-hide-on-ipad',
            'icon' => '<i class="icon-stack3"></i>',
            'action' => '',
            'sub' => [

                [
                    'label' => 'Thông tin của tôi',
                    'link' => admin_link('staff/my-info'),
                    'icon' => '<i class=" icon-user"></i>',
                ],
            ],
        ];
        $menu['mng_member'] = $item;

        $item = [
            'label' => 'Đơn hàng',
            'group' => 'AdminOrder',
            'link' => 'javascript:void(0)',
            'icon' => '<i class="icon-notebook"></i>',
            'action' => '',
            'sub' => [
                [
                    'label' => 'Danh sách đơn hàng của tôi',
                    'link' => self::buildLinkAdmin('don-hang-cua-toi'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],[],
                [
                    'label' => 'Danh sách đơn trả hàng',
                    'link' => self::buildLinkAdmin('danh-sach-don-tra-hang'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],
            ],
        ];
        $menu['mng_order']  =$item;

        $item = [
            'label' => 'Đơn mua điểm',
            'group' => 'AdminOrderMpg',
            'link' => 'javascript:void(0)',
            'icon' => '<i class="icon-notebook"></i>',
            'action' => '',
            'sub' => [
                [
                    'label' => 'Danh sách đơn mua điểm',
                    'link' => self::buildLinkAdmin('orders-mpg'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],[],
                [
                    'label' => 'Mua MPG',
                    'link' => self::buildLinkAdmin('mua-diem'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],
            ],
        ];
        $menu['mng_order_mpg']  =$item;

        $item = [
            'label' => 'Yêu cầu rút tiền',
            'group' => 'AdminWithdrawal',
            'link' => 'javascript:void(0)',
            'icon' => '<i class="icon-notebook"></i>',
            'action' => '',
            'sub' => [
                [
                    'label' => 'Danh sách lịch sử rút tiền',
                    'link' => self::buildLinkAdmin('lich-su-rut-tien-cua-ban'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],[],
                /*[
                    'label' => 'Thanh toán công nợ',
                    'link' => self::buildLinkAdmin('thanh-toan-cong-no'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],*/
            ],
        ];
        if(in_array(date('d'), Withdrawal::getArrayOpenRutTien())) {
            $item['sub'][] = [
                'label' => 'Yêu cầu rút tiền',
                'link' => self::buildLinkAdmin('rut-tien'),
                'icon' => '<i class=" icon-notification2"></i>',
                'action' => '',
            ];
        }
            $menu['mng_withdrawal']  =$item;

        $item = [
            'label' => 'Lịch sử giao dịch',
            'group' => 'AdminTransaction',
            'link' => 'javascript:void(0)',
            'icon' => '<i class="icon-notebook"></i>',
            'action' => '',
            'sub' => [
                [
                    'label' => 'Danh sách lịch sử giao dịch ví tiêu dùng',
                    'link' => admin_link('orders-tieudung'),
                    'icon' => '<i class="icon-stack4"></i>',
                ],[],
                [
                    'label' => 'Danh sách lịch sử giao dịch ví chiết khấu',
                    'link' => admin_link('orders-chietkhau'),
                    'icon' => '<i class="icon-stack4"></i>',
                ],[],
                [
                    'label' => 'Danh sách lịch sử giao dịch ví công nợ',
                    'link' => admin_link('orders-congno'),
                    'icon' => '<i class="icon-stack4"></i>',
                ],[],
                [
                    'label' => 'Danh sách lịch sử giao dịch ví hoa hồng',
                    'link' => admin_link('orders-hoahong'),
                    'icon' => '<i class="icon-stack4"></i>',
                ],
                [],
                [
                    'label' => 'Danh sách lịch sử giao dịch % hàng ngày',
                    'link' => admin_link('orders-chietkhau/giao-dich-phan-tram-hang-ngay'),
                    'icon' => '<i class="icon-stack4"></i>',
                ],
                [],
                [
                    'label' => 'Danh sách lịch sử giao dịch kho điểm',
                    'link' => admin_link('orders-khodiem'),
                    'icon' => '<i class="icon-stack4"></i>',
                ],
            ],
        ];
        $menu['mng_transaction']  =$item;

        $item = [
            'label' => 'Thông báo',
            'group' => 'AdminNotification',
            'link' => 'javascript:void(0)',
            'icon' => '<i class="icon-notebook"></i>',
            'action' => '',
            'sub' => [
                [
                    'label' => 'Danh sách thông báo',
                    'link' => self::buildLinkAdmin('notification'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],[],
                [
                    'label' => 'Thêm thông báo mới',
                    'link' => self::buildLinkAdmin('notification/input'),
                    'icon' => '<i class=" icon-notification2"></i>',
                    'action' => '',
                ],
            ],
        ];
       /* $menu['mng_notification']  =$item;*/

        #region ITEM MENU

        $item = [
            'label' => 'Khác',
            'group' => 'other',
            'link' => 'javascript:void(0)',
            'class'=> 'should-show-on-ipad',
            'icon' => '<i class="icon-stack3"></i>',
            'action' => '',
            'sub' => [
                [
                    'label' => 'Hỗ trợ kỹ thuật',
                    'link' => admin_link('system-contact'),
                    'icon' => '<i class="icon-info22"></i>',
                    'action' => '',
                ],
            ],
        ];
        $menu['other'] = $item;


        return $menu;
    }

    static function buildLinkAdmin($router)
    {
        return admin_link('' . $router);
    }


}
