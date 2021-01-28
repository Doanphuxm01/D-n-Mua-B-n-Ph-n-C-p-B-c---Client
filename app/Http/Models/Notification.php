<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Pager;
use App\Elibs\Helper;
use App\Mail\NotificationForStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Notification extends BaseModel
{
    public $timestamps = false;
    const table_name = 'notifications';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];

    const type_global = 'global'; //global , tât cả mọi người đều có thể xem và đẩy đủ thông tin
    const type_original = 'original';     //root chứa content lúc tạo thông báo
    const type_ref = 'ref';       //Thể hiện quan hệ với thông báo root,chứa thông tin người nhận

    static function getTableDetail()
    {
        return DB::table('notification_details');
    }

    /**
     * @struct
     * @field type        (all type)                         string  Loại notif, global|ref|original , với global, tất cả mọi người đều nhận được, với user chỉ tài khoản nào có receiver phù hợp được nhận
     * @field created_at  (all type)                         date    Ngày tạo thông báo
     * @field updated_at  (all type)                         date    Ngày cập nhật thông báo
     * @field title        (type==original|global)           string  Tiêu đề của thông báo
     * @field brief (type==original|global)            string  Mô tả đơn giản về thông báo
     * @field ref_obj     (type==original|global)            object  Là object chứa thông tin, đường dẫn mà thông báo muốn nhắc tới, tối thiểu cần có 2 key , name và link
     * @field sender      (type==original|global)            object  Là object chứa thông tin người gửi thông báo
     * @field detail      (type==original|global)            string  Chứa thông tin chi tiết của thông báo
     * @field root_id     (type==ref)                        string  Là id của notif gốc,
     * @field receiver    (type==ref)                        object  Là object chứa thông tin người nhận bao gồm name và id của tài khoản nhận
     * @field read_at     (type==ref)                        date    là thời gian mà người nhận thông báo đọc thông báo
     */

    static function writeNotif()
    {
        /*Tìm các account cần phải báo notif*/

        /*Viết ra các notif tương ứng với thành phân tìm được*/
    }

    /*
     * @return  ["result" => true/false , msg=>"", ] trả về kết quả của việc tạo notif
     * */

    //Hàm viết gửi notif

        static function sendNotif($content = [], $listReceiverId = [], $options = ['send' => true ,'not_allow_send_mail'=>false])
    {
        //Các loại options
        //$options['send'] =>đánh dấu trạng thái gửi cho tất cả các thành viên được nhận nằm trong list người nhận, và gửi luôn
        //

        $currentAccount = Staff::getCurent();
        // $content cần phải có "name" , "brief", "ref", "detail" , "type"
        $obj = [];
        $id = '';

        if (isset($content['id']) && $content['id']) {
            $temp = self::where('_id', $content['id'])->first();
            if ($temp) {
                $id = $content['id'];
                $currentRoot = $temp;
            }
        }

        if (!isset($content['title']) || empty($content['title'])) {
            return ["result" => false, 'msg' => 'Thiếu tiêu đề của thông báo'];
        }


        //ref_obj chứa link liên kết nhảy sang
        if (isset($content['ref_obj'])) {
            if (!isset($content['ref_obj']['name'])) {
                return ["result" => false, 'msg' => 'Thiếu tên liên kết'];
            }
            if (!isset($content['ref_obj']['link'])) {
                return ["result" => false, 'msg' => 'Đường dẫn liên kết'];
            }
            $obj['ref_obj'] = $content['ref_obj'];
        }
        $obj['content']['title'] = $content['title'];
        $obj['content']['brief'] = isset($content['brief']) ? $content['brief'] : '';
        $obj['content']['detail'] = isset($content['detail']) ? $content['detail'] : "";
        $obj['updated_at'] = Helper::getMongoDateTime();
        $obj['created_at'] = Helper::getMongoDateTime();
        if (isset($options['send'])) {
            $obj['send'] = true;
            $obj['send_at'] = Helper::getMongoDateTime();
        }

        $obj['sender'] = [
            'id' => strval($currentAccount['_id']),
            "name" => isset($currentAccount['name']) ? $currentAccount['name'] : '',
            "account" => isset($currentAccount['account']) ? $currentAccount['account'] : '',
            "emails" => isset($currentAccount['emails']) ? $currentAccount['emails'] : '',
            "phones" => isset($currentAccount['phones']) ? $currentAccount['phones'] : '',
        ];


        $obj['type'] = isset($content['type']) ? $content['type'] : self::type_original;
        if ($obj['type'] === self::type_original) {
            if (empty($listReceiverId)) {
                return ['result' => false, 'msg' => 'Thiều thông tin người nhận'];
            }
            if ($id && isset($currentRoot) && $currentRoot) {
                if ($currentRoot && (!isset($currentRoot['send']) || !$currentRoot['send'])) {
                    self::where('root_id', $id)->delete();
                }
                self::where('_id', $id)->update($obj);
                $root_id = $id;
            } else {
                $root_id = self::insertGetId($obj);
            }

            foreach ($listReceiverId as $account_id) {
                $receiver = Staff::where('_id', $account_id)->first();

                if ($receiver) {
                    self::insertGetId([
                        "type" => self::type_ref,
                        "send" => isset($options['send']) ? $options['send'] : false,
                        "receiver" => [
                            "id" => strval($receiver['_id']),
                            "name" => isset($receiver['name']) ? $receiver['name'] : '',
                            "account" => isset($receiver['account']) ? $receiver['account'] : '',
                            "emails" => isset($receiver['emails']) ? $receiver['emails'] : [],
                            "phones" => isset($receiver['phones']) ? $receiver['phones'] : [],
                        ],
                        'created_at' => Helper::getMongoDateTime(),
                        "root_id" => strval($root_id)

                        //trường read_at sẽ được bổ sung người nhận notif đọc thông báo
                    ]);
                    if (isset($obj['mail_notice']) && $obj['mail_notice'] == 0) {
                        //không gửi mail
                    } else {
                        foreach (collect($receiver->emails)->pluck('value') as $email) {
                            !@$options['not_allow_send_mail'] && self::sendMail($email, ['obj' => $obj]);
                        };

                    }

                }

            }
            return ['result' => true, 'msg' => 'ok'];
        } else if ($obj['type'] === self::type_global) {
            self::insertGetId($obj);
            $listObj = Member::where(
                function ($query) {
                    $query->OrWhere('tinh_trang_cong_viec', 'exists', false)
                        ->OrWhere('tinh_trang_cong_viec', 'Đang công tác');
                })->where('emails', 'exists', true)->select(['emails', '_id'])->get()->toArray();
            $lsEmail = [];
            foreach ($listObj as $receiver) {
                if ($receiver['emails']) {
                    foreach (collect($receiver['emails'])->pluck('value') as $email) {
                        $lsEmail[] = $email;
                    };
                }
            }
            if($lsEmail){
                @$options['not_allow_send_mail'] &&  self::sendMail($lsEmail[0], ['obj' => $obj], $lsEmail);
            }


            return ['result' => true, 'msg' => 'ok'];
        } else {
            return ["result" => false, 'msg' => 'Không đúng kiểu type notif'];
        }
    }


    static function createNotifContent($content)
    {
        $currentAccount = Staff::getCurent();
        // $content cần phải có "name" , "brief", "ref", "detail" , "type"
        $obj = [];
        if (!isset($content['title']) || empty($content['title'])) {
            return ["result" => false, 'msg' => 'Thiếu tiêu đề của thông báo'];
        }
        if (isset($content['ref_obj'])) {
            if (!isset($content['ref_obj']['name'])) {
                return ["result" => false, 'msg' => 'Thiếu tên liên kết'];
            }
            if (!isset($content['ref_obj']['link'])) {
                return ["result" => false, 'msg' => 'Đường dẫn liên kết'];
            }
            $obj['ref_obj'] = $content['ref_obj'];
        }
        $obj['content']['title'] = $content['title'];
        $obj['content']['brief'] = isset($content['brief']) ? $content['brief'] : '';
        $obj['content']['detail'] = isset($content['detail']) ? $content['detail'] : "";
        $obj['updated_at'] = Helper::getMongoDateTime();
        $obj['created_at'] = Helper::getMongoDateTime();

        $obj['sender'] = [
            'id' => strval($currentAccount['_id']),
            "name" => isset($currentAccount['name']) ? $currentAccount['name'] : '',
            "account" => isset($currentAccount['account']) ? $currentAccount['account'] : '',
            "emails" => isset($currentAccount['emails']) ? $currentAccount['emails'] : '',
            "phones" => isset($currentAccount['phones']) ? $currentAccount['phones'] : '',
        ];

        if ($obj['type'] === self::type_original) {
            self::insertGetId($obj);
        } else if ($obj['type'] === self::type_global) {
            self::insertGetId($obj);
            return ['result' => true, 'msg' => 'ok'];
        } else {
            return ["result" => false, 'msg' => 'Không đúng kiểu type notif'];
        }

    }

    static function getCurrentMemberNotif()
    {
        $listObj = self::where([
            '$or' => [
                [
                    'receiver.id' => Member::getCurentId(),
                    'type' => self::type_ref,
                    'send' => true
                ],
                [
                    'type' => self::type_global,
                    'send' => true
                ]
            ]

        ])->orderBy('_id', 'desc');


        $listObj = Pager::getInstance()->getPager($listObj, 30, 'all');
        $listTempContent = [];
        foreach ($listObj as $item) {
            $listTempContent [] = $item['root_id'];
        }
        $listTempContent = self::whereIn('_id', $listTempContent)->get();

        $mapContent = [];
        foreach ($listTempContent as $item) {
            $mapContent[strval($item['_id'])] = $item;
        }
        $listGlobalId = $listObj->filter(function ($item) {
            return @$item['type'] === Notification::type_global;
        })->map(function ($item) {
            return strval($item['_id']);
        });
        $tempRefGlobal = Notification::whereIn("root_id", $listGlobalId)->where('receiver.id', Member::getCurentId())->where('type', 'ref-global')->get()->keyBy('root_id');
        foreach ($listObj as $key => $item) {
            if (isset($mapContent[$item['root_id']])) {
                $listObj[$key] = array_merge($mapContent[$item['root_id']]->toArray(), $item->toArray());

            } else {
                if ($item['type'] === 'global') {
//                    $tempCurrent = Notification::where("root_id", $item['_id'])->where('receiver.id', Member::getCurentId())->where('type', 'ref-global')->first();
                    $tempCurrent = @$tempRefGlobal[$item['_id']];
                    if ($tempCurrent) {
                        $listObj[$key] ['read_at'] = @$tempCurrent['read_at'];
                    }
                }
//                $listObj[$key] = [];
            }
        }


        return $listObj;

    }

    static function pushNotificationChangeObject($title, $obj, $table)
    {
        //nếu có member thì bắn cho những member đó, ngược lại khong có member thì bắn all
        $member = Member::getCreatedByToSaveDb();
        $s = [
            //'object_id'  => (string)$dataAffterSave['_id'],
            'table'      => $table,
            'name'       => $title,
            'updated_by' => $member,
            'mail_sent'  => false,
            'updated_at' => Helper::getMongoDateTime(),
            'members'=> []
        ];
        if (isset($obj['members']) && $obj['members']) {
            $s['members'] = $obj['members'];
        }
        $members = [];
        if (@$obj['created_by']) {
            $s['members'][] = [$obj['created_by']];
        }


        //Tạm thời ko bắn all nữa
        //gán all nhân viên vào cho nhanh
//            $members = [];
//            foreach (all_staff_basic() as $key=>$value){
//                $members[]= [
//                    'id'=>(string)$value['_id'],
//                    'name'=>$value['name'],
//                    'email'=>$value['email'],
//                ];
//            }
//            $s['members'] = $members;
        //$s['members'] = [['id' => 'all']];
        $saveSearch = $s;
        DB::getCollection(self::table_name)->findOneAndUpdate(
            ['_id' => @$obj['_id']],
            ['$set' => $saveSearch],
            ['new' => true, 'upsert' => true]
        );
        //todo: send mail: Dùng cronjob quét những thằng có mail_sent == false để gửi mail cho những member bên trong kia: link  domain.com/notification/job-sent-mail
        //handler việc bắn thông báo qua email
    }

    /**
     * @param string $to : Gửi cho ai
     * @param $tpl : các biến chứa dữ liệu
     * Nội dung email soạn trong: resources/views/mail/notification.blade.php
     */
    static function sendMail($to = 'ngankt2@gmail.com', $tpl, $cc = [])
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


}
