<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\EmailHelper;
use App\Elibs\Helper;
use App\Mail\NotificationForStaff;
use Illuminate\Support\Facades\DB;

class Logs extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'logs';
    protected $table     = self::table_name;
    static    $unguarded = TRUE;
    const table_search_name = '_logs_io_search';
    const table_logorder_name = '_logs_io_order';

    /**
     * @param $log
     * @param $name
     *  Logs.create({
     * created_by: authMiddle.getCurrent(),
     * type: 'created',,up[date
     * note: 'Thêm thông tin xe',
     * data_object: docToSave,
     * collection_name: 'car',
     * client_info: {
     * agent: req.headers["user-agent"], // User Agent we get from headers
     * referrer: req.headers["referrer"], //  Likewise for referrer
     * ip: req.ip // Get IP - allow for proxy
     * }
     * });
     */
    static function createLog($log, $name)
    {
        /**
         * Ai làm: nhân viên nào
         * Làm gì: insert, delete, update...
         * lúc nào? created_at
         * dự án nào?
         * phòng ban nào? deparment_id, nghĩa là ông này ở phòng nào
         * đối tượng nào? => object_id
         */
        if(Member::getCurentAccount()) {
            $member = [
                'id'=>Member::getCurentId(),
                'name'=>Member::getCurentAccount(),
                'department_of_staff'=>@Member::$currentMember['department']['name'],
            ];
            $log['created_by'] = (object)$member;
        }

        $log['collection_name'] = $name;
        //$log['project_id'] = Project::getCurentProjectId();
        $log['created_at'] = Helper::getMongoDate();
        $log['client_info'] = (object)[
            'agent'   => $_SERVER['HTTP_USER_AGENT'],
            'referer' => @$_SERVER['HTTP_REFERER'],
            'ip'      => $_SERVER['REMOTE_ADDR'],
        ];

        $project_id = '';
        if(isset($log['data_object']['project_id']) && $log['data_object']['project_id']){
            $project_id  = $log['data_object']['project_id'];
        }elseif(isset($log['data_object']['project']) && $log['data_object']['project']){
            $project_id  = $log['data_object']['project'];
        }



        if($project_id){
            if(is_array($project_id)){
                /*
        * Fix cho vấn đề liên quan đến project name khi xuất log file
        * case Hồ sơ OBJECT_PROFILE nó lưu trữ kiểu khác
        *  "project" : [
           "5da7b141b8e0e3618e7fd442"
       ], => tạm xử lý bằng cách lấy cái dự án đầu tiên , sau này phát sinh có thể xử lý = cách lấy all dự án rồi gán name cách nhau bởi dấu |
        */
                //$project_id = $project_id[0];
                $projectName = [];
                foreach ($project_id as $p=>$v){
                    $project = Project::select('name')->where('_id',$v)->first();
                    if($project) {
                        $projectName[] = $project['name'];
                    }
                }
                $log['project_name']  = implode(' | ',$projectName);
            }else {
                $project = Project::select('name')->where('_id', $project_id)->first();
                if ($project) {
                    $log['project_name'] = $project['name'];
                }
            }
        }

        if (isset($log['data_object']['department']) && $log['data_object']['department']) {
            if(is_string($log['data_object']['department'])){
                $dep = MetaData::select('name')->where('_id', $log['data_object']['department'])->first();
                if ($dep) {
                    $log['department_name'] = $dep['name'];
                }
            }

        }

        self::insert($log);

    }

    static function createLogNew($log, $name, $dataBeforSave = [], $dataAffterSave = [], $notification = true)
    {
        /**
         * Ai làm: nhân viên nào
         * Làm gì: insert, delete, update...
         * lúc nào? created_at
         * dự án nào?
         * phòng ban nào? deparment_id, nghĩa là ông này ở phòng nào
         * đối tượng nào? => object_id
         */
        $log['created_by'] = Staff::getCreatedByToSaveDb();
        $log['table'] = $name;
        $log['created_at'] = Helper::getMongoDateTime();
        $log['client_info'] = (object)[
            'agent' => @$_SERVER['HTTP_USER_AGENT'],
            'referer' => @$_SERVER['HTTP_REFERER'],
            'ip' => @$_SERVER['REMOTE_ADDR'],
        ];
        $log['before'] = $dataBeforSave;
        $log['after'] = $dataAffterSave;
        //update thêm vào bảng All data để thực hiện search cho máu
        if (isset($dataAffterSave['name'])) {
            $s = [
                //'object_id'  => (string)$dataAffterSave['_id'],
                '_id' => $dataAffterSave['_id'],
                'table' => $log['table'],
                'name' => @$dataAffterSave['name'],
                'keyword' => mb_strtolower(@$dataAffterSave['name']) . mb_strtolower(@$dataAffterSave['description']),
                'updated_by' => $log['created_by'],
                'created_at' => $log['created_at'],
                'removed' => isset($dataAffterSave['removed']) ? $dataAffterSave['removed'] : 'no',
            ];
            //$saveSearch = array_merge($dataAffterSave, $s);
            $saveSearch = $s;
            /*DB::getCollection('io_search')->findOneAndUpdate(
                array('_id' => $dataAffterSave['_id']),
                array('$set' => $saveSearch),
                array('new' => true, 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER)
            );  */

            DB::getCollection(self::table_search_name)->findOneAndUpdate(
                ['_id' => $dataAffterSave['_id']],
                ['$set' => $saveSearch],
                ['new' => true, 'upsert' => true]
            );

            if ($dataBeforSave) {
                $title = $log['created_by']['name'] . ' đã cập nhật ' . $dataAffterSave['name'];
            } else {
                $title = $log['created_by']['name'] . ' đã thêm mới ' . $dataAffterSave['name'];
            }

            if ($notification) {
                Notification::pushNotificationChangeObject(@$title, $dataAffterSave, $name);
                if (@$dataAffterSave['created_by']['email'] || @$dataAffterSave ['members'][0]['email']) {

                    $cc = collect(@$dataAffterSave['members'])->map(function ($item) {
                        return @$item['email'];
                    })->filter(function ($item) {
                        return $item;
                    })->toArray();
                    $link = '';
                    if (@$s['table']) {
                        if ($s['table'] === Post::table_name) {
                            $link = admin_link('/news/input?id=' . $dataAffterSave['_id']);
                        } else if ($s['table'] === Agency::table_name) {
                            $link = admin_link('/agency/input?id=' . $dataAffterSave['_id']);
                        } else if ($s['table'] === Staff::table_name) {
                            $link = admin_link('/staff/input?id=' . $dataAffterSave['_id']);
                        } else if ($s['table'] === Product::table_name) {
                            $link = admin_link('/products/input?id=' . $dataAffterSave['_id']) . '&stab=' . @$dataAffterSave['type'];
                        }  else if ($s['table'] === Profile::table_name) {
                            $link = admin_link('/profile/input?id=' . $dataAffterSave['_id']);
                        } else if ($s['table'] === Project::table_name) {
                            $link = admin_link('/project/input?id=' . $dataAffterSave['_id']);
                        } else if ($s['table'] === Position::table_name) {
                            $link = admin_link('/staff/position/?id=' . $dataAffterSave['_id']);
                        }
                    }

                    $tpl['success'] = true;
                    $tpl['name'] = 'Xác thực email';
                    $tpl['subject'] = '[Hệ thống quản lý MinhPhucGroup] Yêu cầu xác thực tài khoản';
                    $tpl['template'] = "mail.verified_account";
                    $to = $dataAffterSave['created_by']['email'] ?: @$dataAffterSave ['members'][0]['email'];
                    EmailHelper::sendMail($to, $tpl);

                }
            }

        }

        self::insert($log);
    }

    static function sendMail($to = 'jekayn109@gmail.com', $tpl, $cc = [])
    {
        /// tham khảo thêm tại http://backend.com/demo_sendmail
        ///
        if (!empty($cc)) {
            Mail::to($to)->cc($cc)->send(new NotificationForStaff($tpl));

        } else {
            Mail::to($to)->send(new NotificationForStaff($tpl));

        }
        ///
        /// Hoặc gửi qua base
        ///
        ///
    }

    const TYPE_LOGIN  = 'login';
    const TYPE_CREATE  = 'created';
    const TYPE_UPDATED = 'updated';
    const TYPE_DELETE  = 'deleted';

    const TYPE_COMPANY          = 'OBJECT_COMPANY';
    const TYPE_DOC              = 'OBJECT_DOC_TYPE';

    const OBJECT_DEPARTMENT     = 'OBJECT_DEPARTMENT';// phòng ban
    const OBJECT_PRODUCT     = 'OBJECT_PRODUCT';// phòng ban
    const OBJECT_POSITION_STAFF = 'OBJECT_POSITION_STAFF';// nhân sự
    const OBJECT_STAFF_INFO = 'OBJECT_STAFF_INFO';// nhân sự cơ bản
    const OBJECT_STAFF_WORK = 'OBJECT_STAFF_WORK';// nhân sự công việc
    const OBJECT_STAFF_FAMILY = 'OBJECT_STAFF_FAMILY';// nhân sự gia đình,nhân thân
    const OBJECT_STAFF_EDU = 'OBJECT_STAFF_EDU';// nhân sự học vấn
    const OBJECT_STAFF          = 'OBJECT_STAFF';// nhân viên
    const OBJECT_NEWS           = 'OBJECT_NEWS';// tin tức
    const OBJECT_CALENDAR       = 'OBJECT_CALENDAR';// lịch cơ quan
    const OBJECT_DOCUMENT       = 'OBJECT_DOCUMENT';//văn bản
    const OBJECT_FORUM_TOPIC       = 'OBJECT_FORUM_TOPIC';//Chuyên đề diễn đàn
    const OBJECT_FORUM_POST       = 'OBJECT_FORUM_POST';//bài viết trong chuyên đề diễn đàn
    const OBJECT_PROJECT       = 'OBJECT_PROJECT';//văn bản
    const OBJECT_ROLE       = 'OBJECT_ROLE';//quyền
    const OBJECT_FOLDER       = 'OBJECT_FOLDER';//folder
    const OBJECT_CATEGORY       = 'OBJECT_CATEGORY';//category
    const OBJECT_FILE       = 'OBJECT_FILE';//thư viện số
    const OBJECT_CONTRACT      = 'OBJECT_CONTRACT';//hợp đồng
    const OBJECT_PROFILE      = 'OBJECT_PROFILE';//hồ sơ
    const OBJECT_LIBRARY      = 'OBJECT_LIBRARY';//tài liệu tham khảo
    const OBJECT_MEDIA      = 'OBJECT_MEDIA';
    const OBJECT_ALBUM      = 'OBJECT_ALBUM';//ảnh

    const OBJECT_CUSTOMER      = 'OBJECT_CUSTOMER';//ví tiêu dùng
    const OBJECT_KHOHANG      = 'OBJECT_KHOHANG';//ví tiêu dùng
    const OBJECT_DONHANG      = 'OBJECT_DONHANG';//ví tiêu dùng
}
