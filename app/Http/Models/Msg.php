<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Pager;
use App\Elibs\Helper;
use App\Mail\NotificationForStaff;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Msg extends BaseModel
{
    public $timestamps = false;
    const table_name = 'msg';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];

    const type_global = 'global'; //global , tât cả mọi người đều có thể xem và đẩy đủ thông tin
    const type_original = 'original';     //root chứa content lúc tạo thông báo
    const type_ref = 'ref';       //Thể hiện quan hệ với thông báo root,chứa thông tin người nhận

    static function getTableDetail()
    {
        return DB::table('msg_details');
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

    static function sendNotif($content = [], $listReceiverId = [], $options = ['send' => true])
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
        $obj['read_by'] = [strval(Member::getCurentId())];
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
                }
            }
            return ['result' => true, 'msg' => 'ok'];
        } else if ($obj['type'] === self::type_global) {
            self::insertGetId($obj);
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
        $listObj = self::where(['$or' => [
            [
                'receiver.id' => Member::getCurentId(),
                'type' => self::type_ref,
                'send' => true],
            [
                'sender.id' => Member::getCurentId(),
                'type' => self::type_original,
                'send' => true],
        ]
        ])->orderBy('_id', 'desc');


        $listObj = Pager::getInstance()->getPager($listObj, 30, 'all');
//        Debug::show($listObj->toArray());
//        die;
        $listTempContent = [];
        foreach ($listObj as $item) {
            $listTempContent [] = $item['root_id'];
        }
        $listTempContent = self::whereIn('_id', $listTempContent)->get();

        $mapContent = [];

        foreach ($listTempContent as $item) {
            $mapContent[strval($item['_id'])] = $item;
        }

        foreach ($listObj as $key => $item) {
            $tempId = strval($item['_id']);

            if (isset($mapContent[$item['root_id']])) {
                $listObj[$key] = array_merge($item->toArray(), $mapContent[$item['root_id']]->toArray(), ['ref_id' => $tempId]);
            }

        }

        return $listObj;

    }

    /**
     * @param string $to : Gửi cho ai
     * @param $tpl : các biến chứa dữ liệu
     * Nội dung email soạn trong: resources/views/mail/notification.blade.php
     */
    static function sendMail($to = 'ngankt2@gmail.com', $tpl)
    {
        /// tham khảo thêm tại http://backend.com/demo_sendmail
        ///
        Mail::to($to)->send(new NotificationForStaff($tpl));
        ///
        /// Hoặc gửi qua base
        ///
        ///
    }


}
