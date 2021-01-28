<?php

namespace App\Http\Controllers\AdminSystem;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Folder;
use App\Http\Models\Logs;
use App\Http\Models\Member;
use App\Http\Models\MetaData;
use App\Http\Models\Project;
use App\Http\Models\Role;
use App\Http\Requests;

use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MngCategory extends Controller
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

    /***
     * Danh sách folder
     *
     * @url: admin/folder/list
     */
    public function _list()
    {
        #region check role
        $mng_obj = Role::mng_category;
        $mng_action = Role::mng_action_view;
        $requireRole = [Role::getRoleKey($mng_obj, $mng_action)];
        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->cannnotAccess(['msg' => 'Bạn không có quyền này']);
        }
        #endregion
        HtmlHelper::getInstance()->setTitle('Quản lý Hạng mục/ Kiểu/ Loại');
        $tpl = array();

        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;

        $where = [];
        //$where['type'] = "";
        $where['removed'] = BaseModel::REMOVED_NO;
        if ($q_status) {
            $where['type'] = $q_status;
        }

        $listObj = MetaData::where($where);

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where('name', 'LIKE', '%' . $q . '%');
        }
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');

        $tpl['data'] = $listObj;

        return eView::getInstance()->setViewBackEnd(__DIR__, 'category/list', $tpl);
    }

    /***
     * Thêm folder
     *
     * @url: admin/xxx/input
     */
    public function input()
    {
        if (!empty($_POST)) {
            return $this->_save();
        }
        $tpl = [];
        $id = Request::capture()->input('id', 0);

        if ($id) {
            $obj = MetaData::find($id);
            $tpl['obj'] = $obj;
        }

        return eView::getInstance()->setViewBackEnd(__DIR__, 'category/input', $tpl);
    }

    /***
     * Thêm folder
     *
     * @url: admin/folder/_save
     */
    public function _save()
    {

        #region check role
        $mng_obj = Role::mng_category;
        $mng_action = Role::mng_action_edit;
        $requireRole = [Role::getRoleKey($mng_obj, $mng_action)];
        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->cannnotAccess(['msg' => 'Bạn không có quyền này']);
        }
        #endregion

        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj');

        if (!isset($obj['name']) || !$obj['name']) {
            return eView::getInstance()->getJsonError('Bạn chưa nhập tên');
        }
        if (!isset($obj['type']) || !$obj['type']) {
            return eView::getInstance()->getJsonError('Bạn chưa chọn Thuộc kiểu nào');
        }
        if (isset(MetaData::$typeRegister[$obj['type']]['object'])) {
            $obj['object'] = MetaData::$typeRegister[$obj['type']]['object'];
        }


        if (!$id) {
            //thêm mới
            $name = Helper::splitAreaContent($obj['name'], '\n');
            foreach ($name as $item) {
                if (trim($item)) {
                    $obj['name'] = $item;
                    $obj['removed'] = BaseModel::REMOVED_NO;
                    MetaData::insert($obj);
                    Logs::createLog([
                        'type' => Logs::TYPE_CREATE,
                        'data_object' => $obj,
                        'note' => "Hạng mục " . $obj['name'] . ' được tạo mới bởi ' . Member::getCurentAccount() . '',
                    ], Logs::OBJECT_CATEGORY);
                }
            }

            return eView::getInstance()->getJsonSuccess('Thêm thành công!', ['reload' => true]);
        } else {
            //update
            $objInDb = MetaData::find($id);
            if (!$objInDb) {
                return eView::getInstance()->getJsonError('Bản ghi này không tồn tại hoặc đã bị xóa');
            }
            $objInDb->update($obj);

            Logs::createLog([
                'type' => Logs::TYPE_UPDATED,
                'data_object' => $obj,
                'note' => "Hạng mục " . $obj['name'] . ' được sửa bởi ' . Member::getCurentAccount() . '',
            ], Logs::OBJECT_CATEGORY);

            return eView::getInstance()->getJsonSuccess('Cập nhật thành công!', ['reload' => true]);
        }

    }


    public function _delete()
    {
        $id = Request::capture()->input('id', 0);
        $token = Request::capture()->input('token', 0);
        if (!Helper::validateToken($token, $id)) {
            return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        }
        if (!Member::haveRole(Member::mng_category)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền xóa Mã số');
        }
        $item = MetaData::find($id);
        if (!$item) {
            return eView::getInstance()->getJsonError('Đối tượng này không tồn tại hoặc đã bị xóa');
        }
        Logs::createLog([
            'type' => Logs::TYPE_DELETE,
            'object_id' => $id,
            'data_object' => $item->toArray(),
            'note' => "Đối tượng " . $item['name'] . ' bị xóa bởi ' . Member::getCurentAccount() . '',
        ], Logs::OBJECT_CATEGORY);

        $item->update([
            'removed' => BaseModel::REMOVED_YES,
        ]);
        //MetaData::where('_id', $id)->delete();
        return eView::getInstance()->getJsonSuccess('Xóa đối tượng thành công.', []);
    }

//    public function _sendCate(){
//
//    }

}
