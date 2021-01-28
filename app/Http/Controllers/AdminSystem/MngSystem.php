<?php

namespace App\Http\Controllers\AdminSystem;

use App\Elibs\Debug;
use App\Elibs\EmailHelper;
use App\Elibs\eCalendar;
use App\Elibs\eView;
use App\Elibs\FileHelper;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Calendar;
use App\Http\Models\Document;
use App\Http\Models\ForumPost;
use App\Http\Models\KhoDiem;
use App\Http\Models\Library;
use App\Http\Models\Logs;
use App\Http\Models\Member;
use App\Http\Models\MetaData;
use App\Http\Models\Post;
use App\Http\Models\Profile;
use App\Http\Models\Project;
use App\Http\Models\ProjectPermission;
use App\Http\Models\Role;
use App\Http\Models\Staff;
use App\Http\Models\ViChietKhau;
use App\Http\Models\ViCongNo;
use App\Http\Models\ViHoaHong;
use App\Http\Models\ViTichLuy;
use App\Http\Models\ViTieuDung;
use App\Http\Models\Cate;
use App\Http\Models\Product;
use App\Http\Requests;

use App\Mail\MailBase;
use App\Mail\NotificationForStaff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MngSystem extends Controller
{

    public function index()
    {
        // return eView::getInstance()->setViewBackEnd(__DIR__, 'baotri');
        // exit();
        if (isset($_GET['reset']) && $_GET['reset'] == true) {
            Helper::delSession(Project::SESSION_CURRENT_PROJECT);
            return redirect('/');
        }
        $currentMember = Member::getCurent();

        // echo strtotime(Carbon::createFromFormat('d/m/Y H:i', '24/05/2018 08:20')->toDateTimeString());
        HtmlHelper::getInstance()->setTitle("Hệ thống quản lý");

        $tpl = [];

        #endregion new document

        // case ViChietKhau
        $vichietkhau = ViChietKhau::getViByAccount(Member::getCurentAccount());
        $tpl['vichietkhau'] = $vichietkhau;

        // case ViTichLuy
        $vitichluy = ViTichLuy::getViByAccount(Member::getCurentAccount());
        $tpl['vitichluy'] = $vitichluy;

        // case ViHoaHong
        $vihoahong = ViHoaHong::getViByAccount(Member::getCurentAccount());
        $tpl['vihoahong'] = $vihoahong;

        // case ViCongNo
        $vicongno = ViCongNo::getViByAccount(Member::getCurentAccount());
        $tpl['vicongno'] = $vicongno;

        // case vitieudung
        $vitieudung = ViTieuDung::getViByAccount(Member::getCurentAccount());
        $tpl['vitieudung'] = $vitieudung;

        // case vitieudung
        $khodiem = KhoDiem::getViByAccount(Member::getCurentAccount());
        $tpl['khodiem'] = $khodiem;


        $where = [
            'removed' => BaseModel::REMOVED_NO,
        ];

        $tpl['dataGroup'] = MetaData::getAllByType();
        eView::getInstance()->setMsgInfo("Chào mừng bạn đến với hệ thống quản lý dữ liệu");

        return eView::getInstance()->setViewBackEnd(__DIR__, 'dashboard', $tpl);
    }

    public function list_role()
    {
        HtmlHelper::getInstance()->setTitle('Danh sách quyền hệ thống');

        $tpl['listRole'] = Member::getListRole();

        return eView::getInstance()->setViewBackEnd(__DIR__, 'list_role', $tpl);

    }

    public function list_log_access()
    {



        $lsObject = [
            Logs::OBJECT_DEPARTMENT => 'Công ty',
            Logs::OBJECT_POSITION_STAFF => 'Chức vụ nhân sự',
            //Logs::OBJECT_STAFF=>'Nhân sự',
            Logs::OBJECT_STAFF_FAMILY => 'Nhân sự - Thông tin gia đình',
            Logs::OBJECT_STAFF_WORK => 'Nhân sự - Thông tin công việc',
            Logs::OBJECT_STAFF_INFO => 'Nhân sự - Thông tin cơ bản',
            Logs::OBJECT_STAFF_EDU => 'Nhân sự - Thông tin học vấn',
            Logs::OBJECT_NEWS => 'Tin tức',
            Logs::OBJECT_CALENDAR => 'Lịch làm việc',
            Logs::OBJECT_DOCUMENT => 'Văn bản',
            Logs::OBJECT_FORUM_TOPIC => 'Chuyên đề diễn đàn',
            Logs::OBJECT_FORUM_POST => 'Bài viết diễn đàn',
            Logs::OBJECT_PROJECT => 'Thông tin dự án',
            Logs::OBJECT_ROLE => 'Phân quyền',
            Logs::OBJECT_FOLDER => 'Quản lý folder',
            Logs::OBJECT_CATEGORY => 'Quản lý danh mục',
            Logs::OBJECT_FILE => 'Thư viện số',
            Logs::OBJECT_CONTRACT => 'Hợp đồng',
            Logs::OBJECT_PROFILE => 'Hồ sơ',
            Logs::OBJECT_LIBRARY => 'Tài liệu tham khảo',
            Logs::OBJECT_MEDIA => 'File, Media',
            Logs::OBJECT_ALBUM => 'Album',
        ];
        $tpl['lsObject'] = $lsObject;
        if (isset($_GET['id'])) {
            if (isset($_GET['token'])) {
                if (Helper::validateToken($_GET['token'], $_GET['id'])) {
                    if (isset($_GET['action']) && $_GET['action'] == 'delete') {

                    } else {
                        $tpl['data'] = Logs::find($_GET['id']);

                        return eView::getInstance()->setViewBackEnd(__DIR__, 'log_access_detail', $tpl);
                    }
                }
            }
        }
        HtmlHelper::getInstance()->setTitle('Quản lý log thao tác hệ thống');


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_action = Request::capture()->input('q_action', '');
        $q_time = Request::capture()->input('q_time', '');
        $q_object = Request::capture()->input('q_object', '');
        $q_created_by = Request::capture()->input('created_by', '');
        $q_excel = Request::capture()->input('excel', '');

        $tpl['q_action'] = $q_action;
        $tpl['q_object'] = $q_object;
        $tpl['q'] = $q;

        $where = [];

        if ($q_object) {
            $where['collection_name'] = $q_object;
        }
        if ($q_created_by) {
            $where['created_by.id'] = $q_created_by;
        }
        if ($q_action) {
            $where['type'] = $q_action;
        }
        $listObj = Logs::where($where);
        if ($q_time) {
            $listObj = BaseModel::helperBuilderQueryByDate($listObj, 'created_at', $q_time);
        }
        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('note', 'LIKE', '%' . $q . '%');
        }
        $listObj = $listObj->orderBy('_id', 'desc');

        if ($q_excel) {
            $listObj = Pager::getInstance()->getPager($listObj, 5000, 'all');
            $tpl['listObj'] = $listObj;
            return eView::getInstance()->setViewBackEnd(__DIR__, 'log_access_to_excel', $tpl);
        } else {

            $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
            $tpl['listObj'] = $listObj;
            return eView::getInstance()->setViewBackEnd(__DIR__, 'log_access', $tpl);
        }


        // Debug::show($listObj);


    }

    public function contact()
    {
        HtmlHelper::getInstance()->setTitle('Thông tin hỗ trợ kỹ thuật');

        return eView::getInstance()->setViewBackEnd(__DIR__, 'support', []);
    }

    /**
     *
     */
    public function demo_send_mail()
    {
        $tpl = [
            'name' => 'ngannv',
            'content' => 'Chào thân ái vào quyết thắng',
        ];
//        Mail::to('ngankt2@gmail.com')->send(new NotificationForStaff($tpl));
        Mail::to('Doanphuxm01@gmail.com')->send(new NotificationForStaff($tpl));
        $code = rand(100000, 900000);
        $customer['email'] = 'Doanphuxm01@gmail.com';
        $id = '123466789';
        if (!empty($customer['email'])) {
            $tpl['success'] = true;
            $tpl['code'] = $code;
            $tpl['name'] = 'Xác thực email';
            $tpl['tokenString'] = Helper::buildTokenString($id);
            $tpl['url'] = public_link('auth/verifyEmail?uid=' . $id . '&token=' . Helper::buildTokenString($id));
            $tpl['subject'] = '[Hệ thống quản lý MinhPhucGroup] Yêu cầu xác thực tài khoản';
            $tpl['template'] = "mail.verified_account";
//            dd($tpl);
            EmailHelper::sendMail($customer['email'], $tpl);
            dd(__DIR__);
        }
        Debug::show($tpl);
        ///
        /// Hoặc gửi qua base
        ///
        ///
        die();
        $tpl['subject'] = 'Subject của email';
        $tpl['template'] = 'mail.notification';
        Mail::to('ngankt2@gmail.com')->cc(['ngankt2@gmail.com', 'huyhuy17@gmail.com'])->send(new MailBase($tpl));
        Debug::show($tpl);
    }



}
