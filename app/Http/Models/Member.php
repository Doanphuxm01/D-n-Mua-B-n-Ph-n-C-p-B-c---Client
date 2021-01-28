<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Cấu trúc khách hàng
 */
class Member extends BaseModel
{
    const table_name = 'io_customers';
    protected $table = self::table_name;
    const SESSION_KEY_FOR_CUR_MEMBER = 'clgt_session';
    const COOKIE_KEY_FOR_CUR_MEMBER = 'clgt';
    static $currentMember = [];
    static $currentPosition = [];
    static $unguarded = true;


    const ROLE_ROOT = 'root';//root
    const ROLE_ADMIN = 'admin';
    const IS_DAILY = 'daily';// ĐẠI LÝ
    const IS_CTV = 'ctv';// ĐẠI LÝ
    const IS_MPMART = 'mpmart';// ĐẠI LÝ
    const ROLE_AUTHOR = 'author';//quyền tác giả => được viết, được sửa nhưng không được xóa
    const ROLE_CONTENT_EDITOR = 'content';//quyền quản lý nội dung
    const ROLE_MEMBER = 'member';//Thành viên đăng ký thông thường
    const ROOT_ACCOUNT = ['ngannv', 'sakura', 'Khoa'];


    const STATUS_NO_WOKING = 'no-working';//nghỉ việc

    const is_root = 'is_root';
    const min_mpg = 500000;
    const min_mpg_after_register = 50000;
    const min_dai_ly = 20000000;
    const min_mpmart = 300000000;
    const DEBT_YES = 'yes'; // nợ vl
    const DEBT_NO = 'no';   // không nợ
    const MP_MART = 'mp_mart';

    /**
     * Khách hàng
     */
    const mng_member = 'mng_member';
    const mng_member_add = 'mng_member_add';
    const mng_member_update = 'mng_member_update';
    const mng_member_delete = 'mng_member_delete';




    const mng_media = 'mng_media';
    const mng_media_delete = 'mng_media_delete';
    const mng_media_ad = 'mng_media_ad';
    const mng_media_update = 'mng_media_update';

    /**
     * Ảnh
     */
    const mng_album = 'mng_album';
    const mng_album_delete = 'mng_album_delete';
    const mng_album_ad = 'mng_album_ad';
    const mng_album_update = 'mng_album_update';


    /**
     * Tài khoản, nhân viên
     */
    const mng_staff_account = 'mng_staff_account';//quản lý tài khoản nhân viên
    const mng_staff_update = 'mng_staff_update';//cập nhật thông tin nhân viên
    const mng_staff_password = 'mng_staff_password';
    const mng_staff_delete = 'mng_staff_delete';
    const mng_staff_position = 'mng_staff_position';//quản lý vị trí chức vụ
    const mng_staff_department = 'mng_staff_department';//sửa, xóa phòng ban

    const mng_role = 'mng_role';
    const mng_role_update = 'mng_role_update';

    const mng_calendar = 'mng_calendar';
    const mng_calendar_delete = 'mng_calendar_delete';


    const mng_payment_add = 'mng_payment_add';
    const mng_payment_update = 'mng_payment_update';

    const mng_notification = 'mng_notification';

    const mng_folder = 'mng_folder';
    const mng_category = 'mng_category';

    /**
     * Customer schema
     */


    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    static function isLogin()
    {
        return self::$currentMember;
    }

    static function isContentAuthor()
    {
        if (!self::$currentMember) {
            return false;
        }
        if (self::isAdmin()) {
            return true;
        }
        if (in_array(self::$currentMember['role'], [self::ROLE_CONTENT_EDITOR, self::ROLE_AUTHOR])) {
            return true;
        }
    }

    static function isContentEditor()
    {
        return true;
        if (!self::$currentMember) {
            return false;
        }
        if (in_array(self::$currentMember['role'], [self::ROLE_CONTENT_EDITOR, self::ROLE_ADMIN, self::ROLE_ROOT])) {
            return true;
        }
    }

    static function setLogin($member)
    {
        if (!is_array($member)) {
            if (!$member) {
                Helper::delSession(Member::SESSION_KEY_FOR_CUR_MEMBER);
                Helper::delSession(Project::SESSION_CURRENT_PROJECT);
                Helper::delCookie(Member::COOKIE_KEY_FOR_CUR_MEMBER);
                return Redirect(admin_link('/'));
            }
            $member = $member->toArray();
            if (isset($member['extra']) && $member['extra']) {
                $member['extra'] = json_decode($member['extra'], 1);
            }
        }
        if (isset($member['projects'])) {
            $project = [];
            foreach ($member['projects'] as $item) {
                $project[$item['id']] = $item;
            }
            $member['projects'] = $project;
        }
        Helper::setSession(Member::SESSION_KEY_FOR_CUR_MEMBER, $member);
        Helper::setCookie(Member::COOKIE_KEY_FOR_CUR_MEMBER, $member['account'] . ':' . md5($member['account'] . 'ngannv'));
    }

    static function setLogOut()
    {
        Helper::delSession(Member::SESSION_KEY_FOR_CUR_MEMBER);
        Helper::delSession(Project::SESSION_CURRENT_PROJECT);
        Helper::delCookie(Member::COOKIE_KEY_FOR_CUR_MEMBER);
    }

    /***
     *
     * @param $account
     *
     * @return array
     */
    static function getMemberByAccount($account)
    {
        if (!$account) {
            return [];
        }
        $where = [
            'account' => $account,
        ];
        $member = static::where($where)->first();
        return $member;
    }

    /***
     *
     * @param $account
     *
     * @return array
     */
    static function getMemberByMaGioiThieu($account)
    {
        if (!$account) {
            return [];
        }
        $where = [
            'ma_gioi_thieu' => $account,
        ];
        $member = static::where($where)->first();
        return $member;
    }

    /***
     *
     * @param $code
     *
     * @return array
     */
    static function getMemberByMaTaiKhoanKichHoat($code)
    {
        if (!$code) {
            return [];
        }
        $where = [
            'ma_tai_khoan_kich_hoat' => $code,
        ];
        $member = static::where($where)->first();
        return $member;
    }

    /***
     *
     * @param $email
     *
     * @return array
     */
    static function getMemberByEmail($email)
    {
        if (!$email) {
            return [];
        }
        $where = [
            'email' => $email,
        ];
        $member = static::where($where)->first();

        return $member;
    }

    /***
     *
     * @param $cancuoccongdan
     *
     * @return array
     */
    static function getMemberByCanCuocCongDan($cancuoccongdan)
    {
        if (!$cancuoccongdan) {
            return [];
        }
        $where = [
            'can_cuoc_cong_dan' => $cancuoccongdan,
        ];
        $member = static::where($where)->first();

        return $member;
    }

    /***
     *
     * @param $phone
     *
     * @return array
     */
    static function getMemberByPhone($phone)
    {
        if (!$phone) {
            return [];
        }
        $where = [
            'phone' => $phone,
        ];
        $member = static::where($where)->first();

        return $member;
    }

    /***
     *
     * @param $password
     *
     * @return mixed
     * @note:  gen mật khẩu
     */
    static function encodePassword($password)
    {
        return hash_hmac('sha256', $password, 'ngannv-techhandle-sakura');
    }

    static function genPassSave($password)
    {
        return hash_hmac('sha256', $password, 'ngannv-techhandle-sakura');
        //return Hash::make(self::encodePassword($password));
    }

    /**
     * @return array
     * dùng lưu vào db các chỗ created_by
     */
    static function getCreatedByToSaveDb()
    {
        return [
            'id'      => Member::getCurentId(),
            'name'    => Member::getCurrentName(),
            'account' => Member::getCurentAccount(),
            'email' => Member::getCurrentEmail(),
        ];
    }

    static function setCurent($curent = [])
    {
        if ($curent) {
            self::$currentMember = $curent;
        } else {
            self::$currentMember = Helper::getSession(self::SESSION_KEY_FOR_CUR_MEMBER);
        }
    }

    static function getCurent()
    {
        if (self::$currentMember) {
            return self::$currentMember;
        } else {

            self::$currentMember = Helper::getSession(self::SESSION_KEY_FOR_CUR_MEMBER);
            if (isset(self::$currentMember['_id'])) {
                $member = Member::find(self::$currentMember['_id']);
                self::setLogin($member);
            }
            //return self::$currentMember = Helper::getSession(self::SESSION_KEY_FOR_CUR_MEMBER);
        }
    }

    static function getCurentId()
    {
//        dd(self::$currentMember['id']);
        return self::$currentMember['_id'];
    }


    static function getCurentCode()
    {
        return self::$currentMember['code'];
    }

    static function getCurentAccount()
    {
        return @self::$currentMember['account'];
    }

    static function getCurrentEmail()
    {
        return @self::$currentMember['email'];
    }

    static function getCurrentName()
    {
        return @self::$currentMember['name'];
    }

    static function getCurrentChucDanh()
    {
        $member = static::select('_id', 'chuc_danh', 'account')->where('_id', @self::getCurentId())->first();
        return $member['chuc_danh'];
    }

    static function getCurrentFullName()
    {
        $member = static::select('_id', 'fullname', 'account')->where('_id', @self::getCurentId())->first();
        return $member['fullname'];
    }

    static function getCurrentTaiKhoanNganHang()
    {
        $member = static::select('_id', 'tk_ngan_hang', 'account')->where('_id', @self::getCurentId())->first();
        return $member['tk_ngan_hang'];
    }

    /**
     *
     * @param  array $rolesAllow
     * @return bool
     */
    static function haveRole($rolesAllow = [])
    {
        if (Member::isRoot()) {
            return true;
        }
        if (!$rolesAllow) {
            return false;
        }
        if (!is_array($rolesAllow)) {
            $rolesAllow = array($rolesAllow);
        }
        if (!isset(self::$currentMember['roles']) || !is_array(self::$currentMember['roles'])) {
            return false;
        }
        if (count($rolesAllow) < count(self::$currentMember['roles'])) {
            $roleSub = $rolesAllow;
            $roleParent = self::$currentMember['roles'];
        } else {
            $roleSub = self::$currentMember['roles'];
            $roleParent = $rolesAllow;
        }
        if (array_intersect($roleSub, $roleParent) == $roleSub) {
            return true;
        }

        return false;

    }

    static function haveAccessProject($project)
    {
        return true;
        if (self::isAdmin()) {
            return true;
        }
        if (isset($project['_id'])) {
            $project_id = $project['_id'];
        } else {
            $project_id = $project;
        }
        return (isset(self::$currentMember['projects']) && isset(self::$currentMember['projects'][$project_id]));
    }

    static function isAdmin()
    {
        return self::isRoot();
    }

    static function isRoot($account = false)
    {
        if($account) {
            return in_array($account, Member::ROOT_ACCOUNT);
        }elseif ($account == null) {
            return false;
        }

        if (!self::$currentMember) {
            return false;
        }

        return in_array(self::$currentMember['account'], Member::ROOT_ACCOUNT);
    }

    static function getCurrentPosition()
    {
        $currentMember = self::getCurent();
        if (!@$currentMember['position']['id']) {
            self::$currentPosition = null;
        }
        if (empty(self::$currentPosition) && !is_null(self::$currentPosition)) {
            self::$currentPosition = Position::where('_id', $currentMember['position']['id'])->first();
        }

        return self::$currentPosition;
    }


    static function getListRole()
    {
        return Role::getListRole();
    }

    /**
     * Kiểm tra, validate thông tin của công nhân viên
     *
     * @param String|String[] $id là id Bài viết
     *
     * @return array ["msg" =>"Tin nhắn validate", "valid" =>Boolean]
     */
    static function validate_tab_info(&$obj)
    {
        $ret = [
            "valid" => false,
            "msg" => '',
        ];
        if (!isset($obj['info'])) {
            $ret['msg'] = "Bạn chưa nhập thông tin nào cả";
            return $ret;
        }

        /*parse lại dữ liệu dạng mảng*/
        foreach ($obj['info'] as $key => $item) {
            if (is_array($item)) {
                $converted = [];
                foreach ($item as $key_ => $val_) {
                    foreach ($val_ as $key__ => $val__) {
                        /*$key__ là số thứ tự*/
                        /*$key_ là số trường*/
                        /*{key1: [1,2,3], key2:[5,6,7]} => {1:{key1: 1, key2: 5}, 2: {......}}*/
                        $converted[$key__][$key_] = $val__;
                    }

                }
                $obj['info'][$key] = $converted;
            }
        }
        /*xoá nhưng thông tin rỗng trong mảng*/
        foreach ($obj['info'] as $key => $item) {
            if (is_array($item)) {
                $temp = array_filter($item, function ($item_) {
                    foreach ($item_ as $item__) {
                        if ($item__ != "") {
                            return true;
                        }
                    }
                    return false;
                });
                $obj['info'][$key] = $temp;
            }
        }
        /*validate*/
        $schema_tab_info = self::$schema['info'];

        foreach ($schema_tab_info as $field) {
            $field_key = $field['key'];
            $field_text = $field['text'];
            $field_type = $field['type'];
            if (isset($obj['info'][$field_key])) {
                $value = $obj['info'][$field_key];
                if ($field_type == 'date') {
                    if ($value && !strtotime($value)) {
                        $ret['msg'] = "Dữ liệu $field_text: \"$value\" phải là $field_type";
                        return $ret;
                    } else if (!$value) {
                        $obj['info'][$field_key] = "";
                    } else {
                        $obj['info'][$field_key] = strtotime($value);
                    }
                }
                if ($field_type == 'multi' && $value) {
                    $obj['info'][$field_key] = explode(',', $obj['info'][$field_key]);
                }
            }

        }

        $ret = [
            'msg' => 'ok',
            'valid' => true
        ];

        return $ret;
    }


    static function getFieldTypeTabInfo($field_key, $child_field_key)
    {
        $infoSchema = self::$schema['info'];
        $type = '';
        if ($field_key) {
            $temp = array_first($infoSchema, function ($item) use ($field_key) {
                return $item['key'] == $field_key;
            });
            if ($temp) {
                $type = $temp['type'];
            }
        }
        if (isset($temp['rows']) && $child_field_key) {
            $type = '';
            $temp = array_first($temp['rows'], function ($item) use ($field_key) {
                return $item['key'] == $field_key;
            });
            if ($temp) {
                $type = $temp['type'];
            }
        }

        return $type;

    }

    const field_name = [
        'code' => 'Mã nhân viên',
        'department' => 'Phòng ban đang công tác',
        'position' => 'Vị trí đảm nhiệm',
        'name' => 'Tên',
        'account' => 'Tài khoản',
        'gender' => 'Giới tinh',
        'date_of_birth' => 'Ngày sinh',
        'noi_sinh' => 'Nơi sinh',
        'role_group' => 'Nhóm quyền',
        'nguyen_quan' => 'Nguyên quán',
        'dan_toc' => 'Dân tộc',
        'ton_giao' => 'Tôn giáo',
        'quoc_tich' => 'Quốc tịch',
        'ma_so_thue' => 'Mã số thuế',
        'tinh_trang_hon_nhan' => 'Tình trạng hôn nhân',
        'tien_an_tien_su' => 'Tiền án tiền sự',
        'so_bhxh' => 'Số BHXH',
        'ngoai_ngu' => 'Ngoại ngữ',
        'ho_khau_thuong_chu' => 'Hộ khẩu thường trú',
        'emails' => 'Các email',
        'phones' => 'Các số điện thoại',
        'tinh_trang_cong_viec' => 'Tình trạng công việc',
//        'department' => 'Phòng ban hiện tại',
        'chuc_vu_hien_tai' => 'Chức vụ hiện tại',
        'giay_to' => 'Giấy tờ',
        'tk_ngan_hang' => 'Tài khoản ngân hàng',
        'to_chuc_doan_the' => 'Tổ chức đoàn thể',
        'lien_he_khac' => 'Liên hệ khác',
        'files_thong_tin_co_ban' => 'Các file đính kèm',
        'files_thong_tin_gia_dinh' => 'Các file đính kèm',
        'files_thong_tin_dao_tao' => 'Các file đính kèm',
        'files_thong_tin_cong_viec' => 'Các file đính kèm',
    ];

    static function getTaiKhoanToSaveDb($customer)
    {
        return [
            'id'      => (string)@$customer['_id']??$customer['id'],
            'name'    => @$customer['name'],
            'account' => $customer['account'],
            'email' => @$customer['email'],
            'phone' => @$customer['phone'],
            'verified' => @$customer['verified'],
        ];
    }

    static function getFieldName($key)
    {
        if (isset(self::field_name[$key])) {
            return self::field_name[$key];
        } else {
            return $key;
        }
    }

    static function getAllMember()
    {
        return self::orderBy('_id', 'desc')->get()->keyBy('_id')->toArray();
    }

    public static function getDanhSachDongHo(array &$menu_data, $parent_id = '0', $selected = [], $loop = 0)
    {
        $data = [];
        if($loop < 4) {
            $loop++;
            foreach ($menu_data as $k => &$item) {
                if (@$item['parent_id'] == $parent_id) {
                    $children = self::getDanhSachDongHo($menu_data, $item['ma_gioi_thieu']??$item['account'], [], $loop);
                    if ($children) {
                        $item['children'] = $children;
                    }
                    $data[@$item['account']] = $item;
                    unset($menu_data[$k]);
                }
            }
        }else {
            $loop = 0;
        }

        return $data;
    }
    public static function CheckBank($obj){
        if (isset($obj['tk_ngan_hang']['so']) && $obj['tk_ngan_hang']['so'] != '') {
            $flag = false;
                if (!empty($obj['tk_ngan_hang']['_id']) && Helper::isMongoId($obj['tk_ngan_hang']['_id'])) {
                    $flag = true;
                    $temp = MetaData::where('_id', @$obj['tk_ngan_hang']['_id'])->first();
                    if (isset($temp)) {
                        $savePost = [
                            [
                                'id' => $obj['tk_ngan_hang']['_id'],
                                'so' => $obj['tk_ngan_hang']['so'],
                                'name' => $temp['name']
                            ]
                        ];
                        return $savePost;
                    }else{
                        return ['msg' => 'Không tìm thấy tài khoản ngân hàng', 'error' => 1];
                    }
                }

                if(!$flag){
                    return ['msg' => 'Tài khoản ngân hàng có lỗi', 'error' => 1];
                }
        }
        return ['msg' => 'Vui lòng nhập sô tài khoản', 'error' => 1];
    }
    public static function checkDiaChiMember($obj) {
        if(isset($obj['city']) && $obj['city'] != '') {
            $obj['city'] = strip_tags(trim(@$obj['city']));
            $obj['district'] = strip_tags(trim(@$obj['district']));
            $obj['town'] = strip_tags(trim(@$obj['town']));
            $obj['street'] = strip_tags(trim(@$obj['street']));
            $city = Location::getBySlug($obj['city']);
            if(!isset($city)) {
                return ['msg' => 'Thành phố không tồn tại', 'error' => 1];
            }
            $savePost['city'] = [
                'name'  =>  $city['name'],
                'id'    =>  $city['slug'],
            ];
            if(!isset($obj['district']) || $obj['district'] == '') {
                return ['msg' => 'Vui lòng lựa chọn quận/huyện', 'error' => 1];
            }
            $district = Location::getBySlug($obj['district']);
            if(!isset($district)) {
                return ['msg' => 'Quận/huyện không tồn tại', 'error' => 1];
            }
            $savePost['district'] = [
                'name' =>  $district['name'],
                'id' =>  $district['slug'],
            ];
            if(!isset($obj['town']) || $obj['town'] == '') {
                return ['msg' => 'Vui lòng lựa chọn phường/xã', 'error' => 1];
            }
            $town = Location::getBySlug($obj['town']);
            if(!isset($town)) {
                return ['msg' => 'Phường/xã không tồn tại', 'error' => 1];
            }
            $savePost['town'] = [
                'name' =>  $town['name'],
                'id' =>  $town['slug'],
            ];
            if(!isset($obj['street']) || $obj['street'] == '') {
                return ['msg' => 'Vui lòng nhập địa chỉ', 'error' => 1];
            }
            return $savePost;
        }
        return ['msg' => 'Vui lòng lựa chọn thành phố', 'error' => 1];
    }

    public static function checkMaTaiKhoanKichHoat($obj) {
        if(isset($obj['ae_ho_hang']) && $obj['ae_ho_hang'] === 'true') {
            if(@$obj['ma_tai_khoan_nhan_kich_hoat']) {
                $customer = Member::getMemberByMaTaiKhoanKichHoat($obj['ma_tai_khoan_nhan_kich_hoat']);
                if($customer) {
                    return ['msg' => 'Mã tài khoản kích hoạt đã được sử dụng', 'error' => 1];
                }
            }else {
                return ['msg' => 'Mã tài khoản kích hoạt không hợp lệ', 'error' => 1];
            }
        }
        return true;
    }

}

Member::getCurent();


