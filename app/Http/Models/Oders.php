<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Oders extends Model
{
    public $timestamps = FALSE;
    const table_name = 'io_cart';
    protected $table = self::table_name;
    static $unguarded = TRUE;
    const STATUS_NO_PAID = 'no-paid'; // chưa thanh toán
    const STATUS_PENDING = 'pending'; // đang xử lý
    const STATUS_READY_SHIP = 'ready-ship'; // sẵn sàng giao hàng
    const STATUS_SHIPED = 'shiped'; // đang giao hàng
    const STATUS_DELIVERED = 'delivered'; // đã giao hàng
    const STATUS_CANCELLED = 'cancelled'; // đã hủy do khách ko đặt nữa, chỉ đc hủy khi chưa giao hàng
    const STATUS_RETURNED = 'returned'; // khách feedback trả lại khi hàng đã giao do ko đúng yêu cầu
    const STATUS_FAIL_DELIVERY = 'fail-delivery'; // Giao hàng ko thành công (do thay đổi địa chỉ hoặc người nhận boom)
    const STATUS_DELETED = 'deleted'; // đã xóa

    const CASH_PAYMENT = 'cash';    // thanh toán tiền mặt (SHIP COD)
    const BANK_TRANSFER_PAYMENT= 'bank-transfer'; // thanh toán chuyển khoản

    /*
     * Danh sách các trạng thái đơn hàng
     * @var $selected bool
     * @var $status bool
     *
     * @return $selected == keyStatus => trả về danh sách trạng thái được chọn
     * @return $status == keyStatus => trả về 1 trạng thái được chọn
     * @return $status == FALSE && $selected = FALSE => trả về danh sách trạng thái được chọn
     * */
    static function getListStatus($selected = FALSE, $status = FALSE)
    {
        $listStatus = [
            self::STATUS_NO_PAID => [
                'style' => 'secondary',
                'icon' => 'mdi mdi-bullseye-arrow',
                'text' => 'Chưa thanh toán',
                'text-action' => 'Chưa thanh toán',
                'group-action' => [
                    self::STATUS_PENDING, self::STATUS_CANCELLED, self::STATUS_DELETED
                ]
            ],
            self::STATUS_PENDING => [
                'style' => 'warning',
                'icon' => 'mdi mdi-comment-processing-outline',
                'text' => 'Đang xử lý',
                'text-action' => 'Đang xử lý',
                'group-action' => [
                    self::STATUS_NO_PAID, self::STATUS_READY_SHIP, self::STATUS_CANCELLED, self::STATUS_DELETED
                ]
            ],
            self::STATUS_READY_SHIP => [
                'style' => 'info',
                'icon' => 'mdi mdi-truck',
                'text' => 'Sẵn sàng giao hàng',
                'text-action' => 'Sẵn sàng giao hàng',
                'group-action' => [
                    self::STATUS_NO_PAID, self::STATUS_SHIPED, self::STATUS_PENDING, self::STATUS_CANCELLED, self::STATUS_DELETED
                ]
            ],
            self::STATUS_SHIPED => [
                'style' => 'info',
                'icon' => 'mdi mdi-truck-delivery',
                'text' => 'Đang giao hàng',
                'text-action' => 'Đang giao hàng',
                'group-action' => [
                    self::STATUS_FAIL_DELIVERY, self::STATUS_DELIVERED, self::STATUS_CANCELLED, self::STATUS_DELETED
                ]
            ],
            self::STATUS_DELIVERED => [
                'style' => 'info',
                'icon' => 'mdi mdi-truck-check',
                'text' => 'Đã giao hàng',
                'text-action' => 'Đã giao hàng',
                'group-action' => [
                    self::STATUS_RETURNED, self::STATUS_DELETED
                ]
            ],
            self::STATUS_CANCELLED => [
                'style' => 'danger',
                'icon' => 'mdi mdi-cancel',
                'text' => 'Đã hủy',
                'text-action' => 'Đã hủy',
                'group-action' => [
                    // chỉ xóa và tạo đơn mới
                    self::STATUS_DELETED
                ]
            ],
            self::STATUS_RETURNED => [
                'style' => 'danger',
                'icon' => 'mdi mdi-cash-refund',
                'text' => 'Trả hàng',
                'text-action' => 'Trả hàng',
                'group-action' => [
                    // chỉ xóa và tạo đơn mới
                    self::STATUS_DELETED
                ]
            ],
            self::STATUS_FAIL_DELIVERY  => [
                'style' => 'danger',
                'icon' => 'mdi mdi-account-alert',
                'text' => 'Giao hàng không thành công',
                'text-action' => 'Giao hàng không thành công',
                'group-action' => [
                    self::STATUS_DELETED
                ]
            ],
            self::STATUS_DELETED => [
                'style' => 'danger',
                'icon' => 'mdi mdi-trash-can-outline',
                'text' => 'Đã xóa',
                'text-action' => 'Đã xóa',
                'group-action' => []
            ],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }elseif($status !== FALSE) {
            if(isset($listStatus[$status])) {
                return $listStatus[$status];
            }
            return false;
        }

        return $listStatus;
    }

    public static function getListPayment($selected = FALSE, $status = FALSE){
        $listPayments = [
            self::CASH_PAYMENT => [
                'style' => 'secondary',
                'icon' => 'mdi mdi-bullseye-arrow',
                'text' => 'Thanh toán khi nhận hàng',
                'text-action' => 'Thanh toán khi nhận hàng',
                'description' => 'Giao hàng tận nơi, xem hàng tại chỗ, không thích có thể đổi trả lập tức cho nhân viên giao hàng',
            ],
            self::BANK_TRANSFER_PAYMENT => [
                'style' => 'warning',
                'icon' => 'mdi mdi-comment-processing-outline',
                'text' => 'Thẻ ATM nội địa/Internet Banking',
                'text-action' => 'Thẻ ATM nội địa/Internet Banking',
                'description' => 'Thanh toán cực kì tiện lợi, nhanh chóng, và an toàn'
            ],
        ];

        if ($selected && isset($listPayments[$selected])) {
            $listPayments[$selected]['checked'] = 'checked';
        }elseif($status !== FALSE) {
            if(isset($listPayments[$status])) {
                return $listPayments[$status];
            }
            return false;
        }

        return $listPayments;
    }

    /*
     * @var name string
     * @var category array
     * @var images array
     * @var link string
     * @var created_at array
     * @var created_by  array
     * */

    /*
     * @var code string
     * @var payments string
     * @var fullname string
     * @var phone string
     * @var email string
     * @var city string
     * @var town string
     * @var ward string
     * @var address string
     * @var coupon array
     * @var created_at array
     * @var item array
     * @var total number
     * [name: string, filter array, quantity number, total]
     * */

    protected function createOrder($data) {
        return self::create($data);
    }
}
