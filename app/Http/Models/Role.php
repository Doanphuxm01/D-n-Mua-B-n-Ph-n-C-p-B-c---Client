<?php
/**
 * Created by khoait109@gmail.com
 * Website: https://kayn.pro
 */


namespace App\Http\Models;


use App\Elibs\Debug;
use App\Elibs\Helper;

class Role extends BaseModel
{

    public $timestamps = FALSE;
    const table_name = 'setting_roles';//key role
    protected $table = self::table_name;
    static $unguarded = TRUE;
    static $currentRole = [];//luu id member và id project làm key, value là danh sách role tương ứng

    static $permissionByGroup = [];
    const SESSION_KEY_FOR_ROLE_MEMBER = 'SESSION_KEY_FOR_ROLE_MEMBER';

    static function isAdmin()
    {
        return self::isRoot();
    }

    static function isRoot()
    {

        if (!Member::$currentMember) {
            return FALSE;
        }
        return in_array(Member::$currentMember['account'], Member::ROOT_ACCOUNT);
    }

    /*
     * Các module cần phân quyền
     * */
    static $KAYN_SYSTEM = 'SYSTEM';
    static $KAYN_MEMBER = 'MEMBER';
    static $KAYN_POSITION = 'POSITION';
    static $KAYN_DEPARTMENT = 'DEPARTMENT';
    static $KAYN_NEWS = 'NEWS';
    static $KAYN_CALENDAR = 'CALENDAR';
    static $KAYN_MEDIA = 'MEDIA';
    static $KAYN_CATEGORY = 'CATEGORY_';
    static $KAYN_PRODUCT = 'PRODUCT';

    /*
     * CÁC ACTION CHO MEMBER
     * */

    static $ACTION_LIST = 'LIST_';      // XEM ALL DANH SÁCH
    static $ACTION_LIST_OF_ME = 'LIST_OF_ME_';   // XEM DANH SÁCH CỦA CHÍNH MÌNH
    static $ACTION_LIST_OF_NOT_ME = 'LIST_OF_NOT_ME_';   // XEM DANH SÁCH CỦA NGƯỜI KHÁC
    static $ACTION_EDIT = 'EDIT_';   // SỬA ALL VĂN BẢN
    static $ACTION_EDIT_OF_ME = 'EDIT_OF_ME_';   // SỬA VĂN BẢN CỦA CHÍNH MÌNH
    static $ACTION_EDIT_OF_NOT_ME = 'EDIT_OF_NOT_ME_';   // SỬA VĂN BẢN CỦA NGƯỜI KHÁC
    static $ACTION_DELETE = 'DELETE_';   // XÓA ALL VĂN BẢN
    static $ACTION_DELETE_OF_ME = 'DELETE_OF_ME_';   // xÓA VĂN BẢN CỦA CHÍNH MÌNH
    static $ACTION_DELETE_OF_NOT_ME = 'DELETE_OF_NOT_ME_';   // xÓA VĂN BẢN CỦA NGƯỜI KHÁC
    static $ACTION_APPROVE = 'APPROVE_';   // XÉT DUYỆT
    static $ACTION_ROLE = 'ROLE_';   // PHÂN QUYỀN
    static $ACTION_ROLE_ADMIN = 'ROLE_ADMIN';   // BỐ MÀY LÀ CHỦ TỊCH
    static $ACTION_PASSWORD = 'PASSWORD';   // tài khoản/mật khẩu

    static $KAYN_ROLE_GROUP = [
        [
            'key' => 'role_admin',
            'label' => 'Toàn quyền'
        ],[
            'key' => 'news',
            'label' => 'Nhóm quyền tin tức, sự kiện'
        ],[
            'key' => 'product',
            'label' => 'Nhóm quyền sản phẩm'
        ],[
            'key' => 'media',
            'label' => 'Nhóm quyền upload ảnh, tài liệu'
        ],[
            'key' => 'calendar',
            'label' => 'Nhóm quyền lịch làm việc'
        ],[
            'key' => 'system',
            'label' => 'Nhóm quyền hệ thống'
        ],[
            'key' => 'member',
            'label' => 'Nhóm quyền nhân sự'
        ],[
            'key' => 'department',
            'label' => 'Nhóm quyền phòng ban'
        ],[
            'key' => 'position',
            'label' => 'Nhóm quyền chức danh/chức vụ'
        ],
    ];

    static $KAYN_ROLE_GROUP_DETAILS = [
        'role_admin' => [
            ['key' => 'ROLE_ADMIN', 'text' => 'Toàn quyền (ưu tiên cao nhất)'],
        ],
        'system' => [
            ['key' => 'LIST_SYSTEM', 'text' => 'Xem logs hệ thống'],
        ],
        'news' => [
            ['key' => 'LIST_OF_ME_NEWS', 'text' => 'Xem danh sách tin bài/sự kiện của tôi'],
            ['key' => 'LIST_OF_NOT_ME_NEWS', 'text' => 'Xem danh sách tin bài/sự kiện của người khác'],
            ['key' => 'EDIT_OF_ME_NEWS', 'text' => 'Chỉnh sửa tin bài/sự kiện của tôi'],
            ['key' => 'EDIT_OF_NOT_ME_NEWS', 'text' => 'Chỉnh sửa tin bài/sự kiện của người khác'],
            ['key' => 'DELETE_OF_ME_NEWS', 'text' => 'Xóa tin bài/sự kiện của chính tôi'],
            ['key' => 'DELETE_OF_NOT_ME_NEWS', 'text' => 'Xóa tin bài/sự kiện của người khác'],
            ['key' => 'APPROVE_NEWS', 'text' => 'Xét duyệt tin bài/sự kiện'],
            ['key' => 'LIST_CATEGORY_NEWS', 'text' => 'Xem danh sách danh mục bài viết'],
            ['key' => 'EDIT_CATEGORY_NEWS', 'text' => 'Sửa danh mục bài viết'],
            ['key' => 'DELETE_CATEGORY_NEWS', 'text' => 'Xóa danh mục bài viết'],
        ],
        'product' => [
            ['key' => 'LIST_PRODUCT', 'text' => 'Xem danh sách sản phẩm'],
            ['key' => 'EDIT_PRODUCT', 'text' => 'Chỉnh sửa sản phẩm'],
            ['key' => 'DELETE_PRODUCT', 'text' => 'Xóa sản phẩm'],
            ['key' => 'APPROVE_PRODUCT', 'text' => 'Xét duyệt sản phẩm'],
            ['key' => 'LIST_CATEGORY_PRODUCT', 'text' => 'Xem danh sách danh mục sản phẩm'],
            ['key' => 'EDIT_CATEGORY_PRODUCT', 'text' => 'Sửa danh mục sản phẩm'],
            ['key' => 'DELETE_CATEGORY_PRODUCT', 'text' => 'Xóa danh mục sản phẩm'],
        ],
        'media' => [
            ['key' => 'LIST_OF_ME_MEDIA', 'text' => 'Chỉ xem danh sách ảnh, tài liệu của tôi'],
            ['key' => 'LIST_OF_NOT_ME_MEDIA', 'text' => 'Xem danh sách ảnh, tài liệu của người khác'],
            ['key' => 'EDIT_OF_ME_MEDIA', 'text' => 'Chỉ chỉnh sửa ảnh, tài liệu của tôi'],
            ['key' => 'EDIT_OF_NOT_ME_MEDIA', 'text' => 'Chỉnh sửa ảnh, tài liệu của người khác'],
            ['key' => 'DELETE_OF_ME_MEDIA', 'text' => 'Xóa ảnh, tài liệu của chính tôi'],
            ['key' => 'DELETE_OF_NOT_ME_MEDIA', 'text' => 'Xóa ảnh, tài liệu của người khác'],
        ],
        'calendar' => [
            ['key' => 'LIST_OF_NOT_ME_CALENDAR', 'text' => 'Xem danh sách lịch làm việc của người khác'],
            ['key' => 'LIST_OF_ME_CALENDAR', 'text' => 'Xem danh sách lịch làm việc của tôi'],
            ['key' => 'EDIT_OF_NOT_ME_CALENDAR', 'text' => 'Chỉnh sửa lịch làm việc của người khác'],
            ['key' => 'EDIT_OF_ME_CALENDAR', 'text' => 'Chỉnh sửa lịch làm việc của tôi'],
            ['key' => 'DELETE_OF_ME_CALENDAR', 'text' => 'Xóa lịch làm việc của tôi'],
            ['key' => 'DELETE_OF_NOT_ME_CALENDAR', 'text' => 'Xóa lịch làm việc của người khác'],
            ['key' => 'APPROVE_CALENDAR', 'text' => 'Xét duyệt lịch làm việc'],
        ],
        'member' => [
            ['key' => 'LIST_OF_ME_MEMBER', 'text' => 'Xem danh sách nhân sự của tôi'],
            ['key' => 'LIST_OF_NOT_ME_MEMBER', 'text' => 'Xem danh sách nhân sự của thành viên khác'],
            ['key' => 'EDIT_OF_ME_MEMBER', 'text' => 'Sửa thông tin nhân sự của mình'],
            ['key' => 'EDIT_OF_NOT_ME_MEMBER', 'text' => 'Sửa thông tin nhân sự của thành viên khác'],
            ['key' => 'DELETE_OF_ME_MEMBER', 'text' => 'Xóa nhân sự của mình'],
            ['key' => 'DELETE_OF_NOT_ME_MEMBER', 'text' => 'Xóa nhân sự của thành viên khác'],
            ['key' => 'APPROVE_MEMBER', 'text' => 'Xét duyệt nhân sự'],
            ['key' => 'PASSWORD_MEMBER', 'text' => 'Thay đổi tài khoản mật khẩu truy cập hệ thống'],
        ],
        'department' => [
            ['key' => 'LIST_DEPARTMENT', 'text' => 'Xem phòng ban'],
            ['key' => 'EDIT_DEPARTMENT', 'text' => 'Sửa phòng ban'],
            ['key' => 'DELETE_DEPARTMENT', 'text' => 'Xóa phòng ban'],
        ],
        'position' => [
            ['key' => 'LIST_POSITION', 'text' => 'Xem chức vụ'],
            ['key' => 'EDIT_POSITION', 'text' => 'Sửa chức vụ'],
            ['key' => 'DELETE_POSITION', 'text' => 'Xóa chức vụ'],
            ['key' => 'ROLE_MEMBER', 'text' => 'Phân quyền'],
        ],
    ];

    static function isAllowTo($role_key = "")
    {

        if (self::isAdmin()) {
            return true;
        }

        $currentPosition = Member::getCurrentPosition();
        if (empty($role_key)) {
            return true;
        }

        $currentRole = collect(@$currentPosition['roles']);

        if (!$currentRole->count()) {
            return false;
        }
        $isAllow = $currentRole->first(function ($item) use ($role_key) {
            return $item == $role_key || $item ===Role::$ACTION_ROLE_ADMIN;
        });
        return $isAllow;
    }
    static $isOwn = "not-set";
    static function isMyOwn($obj)
    {
        $currentMember = Member::getCurent();

        if (Role::$isOwn !== 'not-set') {
            return Role::$isOwn;
        }
        if (!$obj) {
            Role::$isOwn = 'yes';
        }
        @$obj['created_by']['id'] == $currentMember ['_id'] ? Role::$isOwn = 'yes' : Role::$isOwn = 'no';
        return Role::$isOwn ==='yes';
    }

    static function isOwner($obj, $member_id = "")
    {
        if ($member_id) {
            $currentId = $member_id;
        } else {
            $currentId = Member::getCurentId();
        }

        if (isset($obj['created_by'])) {
            if ($obj['created_by'] == $currentId) {
                return true;
            }
        }
        if (isset($obj['created_by']['id'])) {
            if ($obj['created_by']['id'] == $currentId) {
                return true;
            }
        }
        return false;


    }
}
