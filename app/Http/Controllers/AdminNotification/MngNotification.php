<?php

/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 5/16/2016
 * Time: 12:24 PM
 */

namespace App\Http\Controllers\AdminNotification;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Models\Car;
use App\Http\Models\Customer;
use App\Http\Models\Media;
use App\Http\Models\Member;
use App\Http\Models\Menu;
use App\Http\Models\Notification;
use App\Http\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MngNotification extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->_list();
        }
    }

    public function _list()
    {
        HtmlHelper::getInstance()->setTitle('Quản lý thông báo - notification');
        $tpl = array();


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;

        $where = [];
        $where ['type'] = ['$in' => [Notification::type_original, Notification::type_global]];
        if ($q_status && $q_status > 0) {
            $where['status'] = $q_status;
        }
        $listObj = Notification::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('name', 'LIKE', '%' . $q . '%');
        }
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;


        return eView::getInstance()->setViewBackEnd(__DIR__, 'list', $tpl);
    }

    /**
     * @url: notification/quick-input
     */
    public function quick_input()
    {
        $tpl = [];
        $id = Request::capture()->input('id', 0);
        if ($id) {
            $tpl['id'] = $id;
        }

        $obj = Notification::where('_id', $id)->first();

        if ($obj) {
            $tpl['obj'] = $obj;
        }
        $listObj = [];
        if ($obj && $obj['type'] == Notification::type_original) {
            $listObj = Notification::where(['type' => Notification::type_ref, "root_id" => $id])->get();
        }

        $currentMember = Member::getCurent();
        $tpl['currentMember'] = $currentMember;


        $tpl['listObj'] = $listObj;
        return eView::getInstance()->setViewBackEnd(__DIR__, 'popup.quick-input', $tpl);
    }

    public function save()
    {
        $obj = Request::capture()->input('obj', []);
        $id = Request::capture()->input('id', 0);

        $content = $obj['content'];

        $listReceiverId = isset($obj['listReceiverId']) ? explode(',', $obj['listReceiverId']) : [];

        $updateResult = Notification::sendNotif($content, $listReceiverId, ['send' => true]);
        if (!$updateResult['result']) {
            return eView::getInstance()->getJsonError($updateResult['msg']);
        }
        return eView::getInstance()->getJsonSuccess("Cập nhật thành công");
    }

    public function my_notif()
    {
        $listObj = Notification::getCurrentMemberNotif();
        $tpl['listObj'] = $listObj;
        return eView::getInstance()->setViewBackEnd(__DIR__, 'my-notif', $tpl);

    }

    public function toggle_read()
    {
        $id = Request::capture()->input('id', 0);
        $read = Request::capture()->input('read', 0);
        if ($id) {
            $current = Notification::where('_id', $id)->first();
            if ($current) {
                if (!isset($current['read_at'])) {
                    $update = ["read_at" => ""];

                } elseif ($current['read_at']) {

                    $update = ["read_at" => ""];
                } else {
                    $update = ["read_at" => Helper::getMongoDateTime()];
                }
                if ($read && !@$current['read_at']) {
                    $update = ["read_at" => Helper::getMongoDateTime()];
                }
                if ($read && @$current['read_at']) {
                    $update = ["read_at" => $current['read_at']];
                }
                if ($current['type'] === 'global') {
                    $update['type'] = 'ref-global';
                    $update['root_id'] = $current['id'];
                    $currentMember = Member::getCurent();
                    $update['receiver'] = [
                        'id' => Member::getCurentId(),
                        'account' => Member::getCurentAccount(),
                        'emails' => @$currentMember['emails'],
                        'phones' => @$currentMember['phones'],

                    ];
                    $tempCurrent = Notification::where("root_id", $id)->where('type', 'ref-global')->where('receiver.id', Member::getCurentId())->first();
                    if ($tempCurrent) {
                        Notification::where("_id", $tempCurrent['_id'])->update($update);
                    } else {
                        $id = Notification::insertGetId($update);
                    }
                } else {
                    Notification::where("_id", $id)->update($update);
                }

                return ['data' => Notification::where('_id', $id)->get(), 'update' => $update];

            } else {
                return eView::getInstance()->getJsonSuccess('Không tìm thấy');
            }
            return eView::getInstance()->getJsonSuccess('ok', $current);
        } else {
            return eView::getInstance()->getJsonSuccess('Yêu cầu không đúng');

        }

    }

    public function toggle_read_multi()
    {
        $id = Request::capture()->input('id', 0);
        $read = Request::capture()->input('read', 1);
        if (empty($id)) {
            return eView::getInstance()->getJsonSuccess('Bạn chưa lựa chọn bản ghi nào');
        }
        $currentMember = Member::getCurent();

        if (is_array($id)) {
            foreach ($id as $_id) {
                $current = Notification::where('_id', $_id)->first();
                if ($current) {
                    if (!isset($current['read_at'])) {
                        $update = ["read_at" => ""];

                    } elseif ($current['read_at']) {

                        $update = ["read_at" => ""];
                    } else {
                        $update = ["read_at" => Helper::getMongoDateTime()];
                    }
                    if ($read && !@$current['read_at']) {
                        $update = ["read_at" => Helper::getMongoDateTime()];
                    }
                    if ($read && @$current['read_at']) {
                        $update = ["read_at" => $current['read_at']];
                    }

                    if ($current['type'] === 'global') {
                        $update['type'] = 'ref-global';
                        $update['root_id'] = $current['id'];
                        $update['receiver'] = [
                            'id' => Member::getCurentId(),
                            'account' => Member::getCurentAccount(),
                            'emails' => @$currentMember['emails'],
                            'phones' => @$currentMember['phones'],

                        ];
                        $tempCurrent = Notification::where("root_id", $id)->where('type', 'ref-global')->where('receiver.id', Member::getCurentId())->first();
                        if ($tempCurrent) {
                            Notification::where("_id", $tempCurrent['_id'])->update($update);
                        } else {
                            $id = Notification::insertGetId($update);
                        }
                    } else {
                        Notification::where("_id", $id)->update($update);
                    }

                    Notification::where("_id", $_id)->update($update);


                } else {
                    return eView::getInstance()->getJsonSuccess('Không tìm thấy');
                }
            }

            return eView::getInstance()->getJsonSuccess('Cập nhật thành công');
        } else {
            return eView::getInstance()->getJsonSuccess('Yêu cầu không đúng');

        }

    }

    public function remove_multi()
    {
        $id = Request::capture()->input('id', 0);
        if (empty($id)) {
            return eView::getInstance()->getJsonSuccess('Bạn chưa lựa chọn bản ghi nào');
        }
        if (is_array($id)) {
            foreach ($id as $_id) {
                $current = Notification::where('_id', $_id)->first()->delete();

            }

            return eView::getInstance()->getJsonSuccess('Đã xoá thông báo thành công');
        } else {
            return eView::getInstance()->getJsonSuccess('Yêu cầu không đúng');

        }

    }

    /***
     * Danh sách thành vien
     *
     * @url: admin/member/input
     */
    public function input()
    {
        if (!empty($_POST)) {
            return $this->_save();
        }
        $tpl = [];
        HtmlHelper::getInstance()->setTitle('Cập nhật thông báo - quản lý thông báo');
        $id = Request::capture()->input('id', 0);

        if ($id) {
            $obj = Notification::find($id);
            $tpl['obj'] = $obj;
        }
        return eView::getInstance()->setViewBackEnd(__DIR__, 'input', $tpl);
    }

    /***
     * Danh sách thành vien
     *
     * @url: admin/member/_save
     */
    public function _save()
    {

        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj', []);
        $send = Request::capture()->input('send');
        if (!Member::haveRole(Member::mng_notification)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
        }
        if ($id) {
            $curentObj = Notification::find($id);
            if (!$curentObj) {
                return eView::getInstance()->getJsonError('Không tìm thấy đối tượng. Vui lòng kiểm tra lại');
            }
        }

        $savePost = [
            'title' => (isset($obj['title']) && $obj['title']) ? trim($obj['title']) : '',
            'brief' => (isset($obj['brief']) && $obj['brief']) ? $obj['brief'] : '',
            'filter' => (isset($obj['filter']) && $obj['filter']) ? $obj['filter'] : [],
            'updated_at' => Helper::getMongoDate(),
        ];


        if (!$savePost['title']) {
            return eView::getInstance()->getJsonError('Bạn vui lòng nhập tiêu đề thông báo');
        }
        if (!$savePost['brief']) {
            return eView::getInstance()->getJsonError('Bạn vui lòng nhập nội dung thông báo');
        }

        if ($id) {
            Notification::where('_id', $id)->update($savePost);
        } else {
            $savePost['created_at'] = Helper::getMongoDate();
            $id = Notification::insertGetId($savePost);
        }

        if ($send) {
            die(__FILE__);
        }

        $data['link'] = Menu::buildLinkAdmin('notification/input?id=' . $id);
        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $data);
    }

    function filter_member()
    {
        if (!Member::haveRole(Member::mng_notification)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
        }
        $obj = Request::capture()->input('obj', []);
        $filter = isset($obj['filter']) ? $obj['filter'] : [];

        $listCustomer = Customer::select();
        $haveFilter = false;
        $filterGroupString = false;
        if (isset($filter['all']) && $filter['all'] == 'all') {
            $haveFilter = true;
        } else {
            if (isset($filter['phone']) && $filter['phone']) {

                $listInData = explode(',', $filter['phone']);
                foreach ($listInData as $ks => $vp) {
                    if (!Helper::isPhoneNumber($vp)) {
                        unset($listInData[$ks]);
                    }
                }
                if (isset($listInData) && $listInData) {
                    $haveFilter = true;
                    $filterGroupString = true;
                    $listCustomer = $listCustomer->orWhereIn('phone', $listInData);
                }
            }
            if (isset($filter['email']) && $filter['email']) {
                $listInData = explode(',', $filter['email']);
                foreach ($listInData as $ks => $vp) {
                    if (!Helper::isEmail($vp)) {
                        unset($listInData[$ks]);
                    }
                }
                if (isset($listInData) && $listInData) {
                    $haveFilter = true;
                    $filterGroupString = true;
                    $listCustomer = $listCustomer->orWhereIn('email', $listInData);
                }
            }
            if (!$filterGroupString) {
                if (isset($filter['have_email'])) {
                    switch ($filter['have_email']) {
                        case 'no-email':
                            {
                                //$where['email'] = '';
                                $haveFilter = true;
                                $listCustomer = $listCustomer->where('email', '');
                                break;
                            }
                        case 'co-email':
                            {
                                $haveFilter = true;
                                $listCustomer = $listCustomer->where('email', '<>', '');

                                break;
                            }
                        case 'co-email-chua-kich-hoat':
                            {
                                $haveFilter = true;
                                $listCustomer = $listCustomer->where('email', '<>', '')->where('verified.email', 'false');
                                break;
                            }
                        case 'co-email-da-kich-hoat':
                            {
                                $haveFilter = true;
                                $listCustomer = $listCustomer->where('email', '<>', '')->where('verified.email', 'true');
                                break;

                            }

                        default:
                            {

                            }
                    }
                }
                if (isset($filter['have_phone'])) {
                    switch ($filter['have_phone']) {
                        case 'co-phone-da-kich-hoat':
                            {
                                $haveFilter = true;
                                $listCustomer = $listCustomer->where('verified.phone', 'true');
                                break;
                            }
                        case 'co-phone-chua-kich-hoat':
                            {
                                $haveFilter = true;
                                $listCustomer = $listCustomer->where('verified.phone', 'false');
                                break;

                            }

                        default:
                            {

                            }
                    }
                }
                if (isset($filter['end_at'])) {
                    switch ($filter['end_at']) {
                        case 'in-3day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->addDays(3);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate(), Helper::getMongoDate($a->toDateTimeString())));
                                break;
                            }
                        case 'in-7day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->addDays(7);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate(), Helper::getMongoDate($a->toDateTimeString())));
                                break;
                            }
                        case 'in-14day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->addDays(14);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate(), Helper::getMongoDate($a->toDateTimeString())));
                                break;
                            }
                        case 'in-30day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->addDays(30);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate(), Helper::getMongoDate($a->toDateTimeString())));
                                break;
                            }
                        case 'out-3day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->subDays(3);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate($a->toDateTimeString()), Helper::getMongoDate()));
                                break;
                            }
                        case 'out-7day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->subDays(7);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate($a->toDateTimeString()), Helper::getMongoDate()));
                                break;
                            }
                        case 'out-14day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->subDays(14);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate($a->toDateTimeString()), Helper::getMongoDate()));
                                break;
                            }
                        case 'out-30day':
                            {
                                $haveFilter = true;
                                $a = Carbon::now()->subDays(30);
                                $listCustomer = $listCustomer->whereBetween('end_at', array(Helper::getMongoDate($a->toDateTimeString()), Helper::getMongoDate()));
                                break;
                            }

                        default:
                            {

                            }
                    }
                }
                if (isset($filter['birthday'])) {
                    if (isset($filter['birthday']['start']) && $filter['birthday']['start']) {
                        $haveFilter = true;
                        $listCustomer = $listCustomer->where('birthday', '>=', Helper::getMongoDate($filter['birthday']['start']));

                    }
                    if (isset($filter['birthday']['end']) && $filter['birthday']['end']) {
                        $haveFilter = true;
                        $listCustomer = $listCustomer->where('birthday', '<=', Helper::getMongoDate($filter['birthday']['end']), '/', false);
                    }

                }
            }
        }
        if (!$haveFilter) {
            return eView::getInstance()->getJsonError('Bạn cần chọn bộ lọc');
        }

        return eView::getInstance()->getJsonError('Tổng số member sẽ nhận tin nhắn = ' . $listCustomer->count(), $listCustomer->get());

        //locj member theo filter

        //$have_email;
    }

    public function _delete()
    {
        $id = Request::capture()->input('id', 0);
        $token = Request::capture()->input('token', 0);
        if (!Helper::validateToken($token, $id)) {
            return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        }
        if (!Member::haveRole(Member::mng_member_delete)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền xóa thông tin thành viên');
        }
        Customer::where('_id', $id)->delete();
        return eView::getInstance()->getJsonSuccess('Xóa đối tượng thành công. Bạn không thể khôi phục lại', []);
    }

}