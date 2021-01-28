<?php

namespace App\Http\Controllers\AdminSystem;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Logs;
use App\Http\Models\Member;
use App\Http\Models\MetaData;
use App\Http\Models\Notification;
use App\Http\Models\Project;
use App\Http\Models\ProjectPermission;
use App\Http\Models\Role;
use App\Http\Models\Staff;
use App\Http\Requests;

use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MngProject extends Controller
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
     * Danh sách thành vien
     *
     * @url: admin/customer/list
     */
    public function _list()
    {
        HtmlHelper::getInstance()->setTitle('Quản lý dự án - Danh sách dự án');
        $tpl = [];
        #region check role
        $mng_obj = Role::mng_project;
        $requireRole [] = Role::getRoleKey($mng_obj, Role::mng_action_list);

        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền gán phòng ban và nhân viên cho dự án');
        }
        #endregion


        $itemPerPage = (int)Request::capture()->input('row', 35);
        $q = trim(Request::capture()->input('q'));
        $q_status = Request::capture()->input('q_status', '');

        $tpl['q_status'] = $q_status;
        $tpl['q'] = $q;


        $where['removed'] = BaseModel::REMOVED_NO;

        if ($q_status) {
            // $where['status'] = $q_status;

        }
        $where['$or'] = [];
        $q_department = Request::capture()->input('q_department', '');
        if ($q_department) {
            $where['departments.id'] = $q_department;
        }
        if (request('q_roled') == 'da_phan_quyen') {
            $where['departments'] = ['$exists' => true, '$nin' => [null, 0, '', [], false]];
        }
        if (request('q_roled') == 'chua_phan_quyen') {

            $where['$or'][] = ['departments' => ['$exists' => false]];
            $where['$or'][] = ['departments' => ['$in' => [null, 0, '', [], false]]];
        }
        $tpl['q_department'] = $q_department;
        if (empty($where['$or'])) {
            unset($where['$or']);
        }
        $listObj = Project::where($where);
        if (!Role::isBelongGroupManage()) {
            $accessProjectListId = ProjectPermission::getAccessListProjectId();
            if ($accessProjectListId) {
                $listObj = $listObj->whereIn('_id', $accessProjectListId);
            }

        }

        // Nếu search theo từ khóa
        if ($q) {
            $listObj = $listObj->where(
                function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . $q . '%')
                        ->OrWhere('brief', 'LIKE', '%' . trim($q) . '%')
                        ->OrWhere('content', 'LIKE', '%' . trim($q) . '%');
                }
            );
        }
        $tpl['dictAllDepartMent'] = Department::getDepartment()->sortBy('name')->keyBy('_id');
        $listObj = $listObj->orderBy('_id', 'desc');
        if (!Request::capture()->input('excel')) {
            $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');


            $tpl['listObj'] = $listObj;


            return eView::getInstance()->setViewBackEnd(__DIR__, 'project/list', $tpl);
        } else {
            $listObj = Pager::getInstance()->getPager($listObj, 5000, 'all');
            $tpl['listObj'] = $listObj;
            return eView::getInstance()->setViewBackEnd(__DIR__, 'project/list-excel', $tpl);
        }
    }

    public function _list_manage()
    {
        #region check role
        $mng_obj = Role::mng_project;
        $requireRole [] = Role::getRoleKey($mng_obj, Role::mng_action_role);

        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền gán phòng ban và nhân viên cho dự án');
        }
        #endregion

        HtmlHelper::getInstance()->setTitle('Quản lý dự án - Danh sách dự án');
        $tpl = [];

        $obj = Request::capture()->input('obj', []);
        $action = Request::capture()->input('action', '');
        $tpl['action'] = $action;
        if (isset($obj['id']) && !empty($obj['id'])) {
            $project = Project::where('_id', $obj['id'])->first()->toArray();
            if (!$project) {
                return eView::getInstance()->getJsonError("Không tìm thấy dự án yêu cầu");
            }

            $tpl['obj'] = $project;
            $tpl['obj']['id'] = $project['_id'];
            $obj = $tpl['obj'];
        }

        $tpl['listProjects'] = Project::getListProjects();
        $tpl['listDepartments'] = MetaData::where('type', 'department')->where('group', 'DEP_GROUP_SAN_XUAT')->get();

        if (isset($obj['departments'])) {
            $whereMngAbleAccount = ['department.id' => ['$in' => collect($obj['departments'])->pluck('id')->toArray()]];
            $listAccount = Member::where($whereMngAbleAccount);
            $listAccount = $listAccount->where(
                function ($query) {
                    $query->OrWhere('tinh_trang_cong_viec', 'exists', false)
                        ->OrWhere('tinh_trang_cong_viec', 'Đang công tác');
                });
            $tpl['listAccount'] = $listAccount->get();
        }
        if (isset($project) && $project) {
            $tpl['listAccountAssign'] = ProjectPermission::where('project.id', $project['_id'])->get();
        }


        return eView::getInstance()->setViewBackEnd(__DIR__, 'project/project-role', $tpl);
    }


    public function assign_project_dep()
    {
        #region check role
        $mng_obj = Role::mng_project;
        $requireRole [] = Role::getRoleKey($mng_obj, Role::mng_action_role);
        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền gán phòng ban và nhân viên cho dự án');
        }
        #endregion
        $currentMember = Member::getCurent();


        $obj = Request::capture()->input('obj', []);
        $select_dep = isset($obj ['departments']) ? $obj ['departments'] : [];
        #region validate dữ liệu
        $select_project = $obj['id'];
        if (!isset($select_project)) {
            return eView::getInstance()->getJsonError('Bạn vui lòng chọn dự án');
        }
        $project = Project::find($select_project);
        if (!$project) {
            return eView::getInstance()->getJsonError('Không tìm thấy dự án yêu cầu');
        }

        #endregion

        #region xoá quyền của các phòng bị uncheck
        $oldDepId = collect(isset($project['departments']) ? $project['departments'] : [])->filter(function ($item) use ($select_dep) {
            return !in_array($item['id'], $select_dep);
        })->pluck('id')->toArray();
        $listAccountRemovePermission = Member::whereIn('department.id', $oldDepId)->get();
        $listAccountIdRemovePermission = collect($listAccountRemovePermission)->pluck('_id')->map(function ($item) {
            return strval($item);
        })->toArray();

        ProjectPermission::whereIn('account_id', $listAccountIdRemovePermission)->where('project_id', $project['_id'])->delete();
        #endregion

        #region update dữ liều
        $select_dep = MetaData::whereIn('_id', $select_dep)->get();

        $objToSave = [];


        $objToSave['departments'] = collect($select_dep->toArray())->map(function ($item) {
            $temp = $item;
            $temp['id'] = $temp['_id'];
            unset($temp['_id']);
            return $temp;
        })->toArray();


        $project->update($objToSave);
        #endregion

        #region bắn notif

        if (!empty($select_dep)) {
            $roles = Role::first();
            //Tìm ra những group có quyền phân quyền nhân viên cho dự án
            $roleReq = collect($roles)->filter(function ($item) {
                $value = isset($item['value']) ? $item['value'] : [];
                return in_array(Role::getRoleKey(Role::mng_project, Role::mng_action_role), $value);
            })->pluck('key')->toArray();
            $listMemberNotif = Member::whereIn('department.id', collect($select_dep)->pluck('id')->toArray())->whereIn('role_group', $roleReq)->get();
            $listMemberNotif = collect($listMemberNotif)->pluck('_id')->map(function ($item) {
                return strval($item);
            })->toArray();

            Notification::sendNotif([
                'title' => 'Dự án ' . @$project['name'] . ' đã được phân cho phòng ban của bạn',
                'brief' => 'Phòng ban của bạn được thêm vào dự án'
                    . $project['name'] . ' bởi ' . Member::getCurentAccount()
                    . '. Bạn hãy vào và phân quyền cho nhân viên',
                'type' => Notification::type_original,
                'ref_obj' => [
                    'link' => '/project/_list_manage?obj[id]=' . $project['_id'] . '&action=assign-member',
                    'name' => 'Gán nhân viên cho dự án'
                ]
            ], $listMemberNotif);
        }
        #endregion bắn notif

        #region ghi log
        Logs::createLog(
            [
                'type' => Logs::TYPE_UPDATED,
                'data_object' => $objToSave,
                'note' => "Dự án " . $project['name'] . ' cập nhập quyền',
            ], 'project'
        );
        #endregion

        return eView::getInstance()->getJsonSuccess('Cấp quyền thành công cho dự án "' . $project['name'] . "'",
            ['reload' => 1]
        );
    }

    public function assign_project_member()
    {
        #region check role
        $mng_obj = Role::mng_project;
        $requireRole [] = Role::getRoleKey($mng_obj, Role::mng_action_role);
        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền gán phòng ban và nhân viên cho dự án');
        }
        #endregion
        $currentMember = Member::getCurent();


        $obj = Request::capture()->input('obj', []);
        $select_dep = isset($obj ['departments']) ? $obj ['departments'] : [];
        #region validate dữ liệu
        $select_project = $obj['id'];
        if (!isset($select_project)) {
            return eView::getInstance()->getJsonError('Bạn vui lòng chọn dự án');
        }
        $project = Project::find($select_project);
        if (!$project) {
            return eView::getInstance()->getJsonError('Không tìm thấy dự án yêu cầu');
        }

//        return $select_dep;
        $obj['account'] = isset($obj['account']) ? $obj['account'] : [];
        if (isset($obj['account']) && is_array($obj['account'])) {

            $perToDelete = ProjectPermission::where('project.id', $project['_id']);
            if (!empty($select_dep)) {
                $accountToRemove = Member::whereIn('department.id', $select_dep)->get()->toArray();
                $accountToRemove = collect($accountToRemove)->pluck('_id')->map(function ($item) {
                    return strval($item);
                })->toArray();
                if (!empty($accountToRemove)) {
                    $perToDelete = $perToDelete->whereIn('account.id', array_values($accountToRemove));
                }
            }

            $perToDelete->delete();
            $members = Member::whereIn('_id', $obj['account'])->where(
                ['department.id' => ['$in' => collect($project['departments'])->pluck('id')->toArray()]]
            )->get()->toArray();
            foreach ($members as $item) {
                ProjectPermission::insertGetId([
                    "project" => [
                        'name' => $project['name'],
                        'id' => $project['_id']
                    ],
                    'project_id' => @$project['_id'],
                    'account_id' => @$item['_id'],
                    "account" => [
                        "name" => @$item['name'],
                        "account" => @$item['account'],
                        "id" => @$item['_id'],
                    ],
                    "created_at" => Helper::getMongoDateTime(),
                    "created_by" => [
                        'id' => $currentMember['_id'],
                        'name' => $currentMember['name'],
                        'account' => $currentMember['account'],
                    ]
                ]);
            }
        }

//        $project->update($objToSave);
        #endregion

        #region bắn notif cho người được gán vào dự án
        Notification::sendNotif([
            'title' => 'Tham gia dự án' . @$project['name'],
            'brief' => 'Bạn được thêm vào dự án ' . $project['name'] . ' bởi ' . Member::getCurentAccount(),
            'type' => Notification::type_original,
        ], $obj['account']);
        #endregion bắn notif

        #region ghi log
        Logs::createLog(
            [
                'type' => Logs::TYPE_UPDATED,
                'data_object' => $obj,
                'note' => "Dự án " . $project['name'] . ' cập nhập quyền',
            ], 'project'
        );
        #endregion

        return eView::getInstance()->getJsonSuccess('Cấp quyền thành công cho dự án "' . $project['name'] . "'",
            ['reload' => 1]
        );
    }

    /***
     * Danh sách thành vien
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
            $obj = Project::find($id);
            $tpl['obj'] = $obj;
        }

        return eView::getInstance()->setViewBackEnd(__DIR__, 'project/input', $tpl);
    }

    /***
     * Danh sách thành vien
     *
     * @url: admin/member/_save
     */
    public function _save()
    {
        #region check role
        $mng_obj = Role::mng_project;
        $requireRole [] = Role::getRoleKey($mng_obj, Role::mng_action_update);
        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền cập nhật thông tin dự án');
        }
        #endregion
        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj', []);

        if ($id) {
            if (!Member::haveRole(Member::mng_project)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
            }
            $currentObj = Project::find($id);
            if (!$currentObj) {
                return eView::getInstance()->getJsonError('Không tìm thấy đối tượng. Vui lòng kiểm tra lại');
            }
            #region check role
            $mng_obj = Role::mng_project;
            $mng_action = Role::mng_action_update;
            $requireRole = [Role::getRoleKey($mng_obj, $mng_action)];
            if (!Role::haveRole2($requireRole)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền này');
            }
            #endregion

        } else {
            #region check role
            $mng_obj = Role::mng_project;
            $mng_action = Role::mng_action_update;
            $requireRole = [Role::getRoleKey($mng_obj, $mng_action)];
            if (!Role::haveRole2($requireRole)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền này');
            }
            #endregion
        }


        if (!isset($obj['name']) || !$obj['name']) {
            return eView::getInstance()->getJsonError('Bạn vui lòng nhập tên dự án');
        }
        $obj['updated_at'] = Helper::getMongoDate();
        $dataReturn = [];
        if ($id) {
            /*$itemInDb = Project::find($id);
            if($itemInDb){
                if(@$itemInDb['removed']!==BaseModel::REMOVED_YES){
                    $obj['removed'] = BaseModel::REMOVED_NO;
                }
            }*/
            Project::where('_id', $id)->update($obj);
            Logs::createLog(
                [
                    'type' => Logs::TYPE_UPDATED,
                    'object_id' => $id,
                    'data_object' => $obj,
                    'note' => "Dự án [" . $obj['name'] . '] được sửa thông tin bởi <b>' . Member::getCurentAccount() . '</b>',
                ], Logs::OBJECT_PROJECT
            );

        } else {
            $obj['created_at'] = Helper::getMongoDate();
            //$obj['removed'] = BaseModel::REMOVED_NO;
            $id = Project::insertGetId($obj);
            Logs::createLog(
                [
                    'type' => Logs::TYPE_CREATE,
                    'object_id' => (string)$id,
                    'data_object' => $obj,
                    'note' => "Dự án [" . $obj['name'] . '] được thêm mới bởi ' . Member::getCurentAccount() . '',
                ], Logs::OBJECT_PROJECT
            );

        }
        $dataReturn['reload'] = true;

        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $dataReturn);
    }


    public function _delete()
    {
        #region check role
        $mng_obj = Role::mng_project;
        $requireRole [] = Role::getRoleKey($mng_obj, Role::mng_action_delete);
        if (!Role::haveRole2($requireRole)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền xoá dự án');
        }
        #endregion

        $id = Request::capture()->input('id', 0);
        $token = Request::capture()->input('token', 0);
        if (!Helper::validateToken($token, $id)) {
            return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
        }
        if (!Member::haveRole(Member::mng_project)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền xóa thông tin thành viên');
        }
        $item = Project::find($id);
        if (!$item) {
            return eView::getInstance()->getJsonError('Dự án này không tồn tại hoặc đã bị xóa');
        }
        Logs::createLog(
            [
                'type' => Logs::TYPE_DELETE,
                'object_id' => $id,
                'data_object' => $item->toArray(),
                'note' => "Dự án " . $item['name'] . ' bị xóa bởi ' . Member::getCurentAccount() . '',
            ], Logs::OBJECT_PROJECT
        );

        $item->update(
            [
                'removed' => BaseModel::REMOVED_YES,
            ]
        );

        return eView::getInstance()->getJsonSuccess('Xóa đối tượng thành công.', []);
    }

    function rule()
    {
        HtmlHelper::getInstance()->setTitle('Phân quyền dự án - Quản lý dự án');

        $id = Request::capture()->input('id', 0);
        $project = Project::find($id);
        $q = trim(Request::capture()->input('q'));
        $tpl['allDepartment'] = Department::getDepartment()->keyBy('_id')->toArray();
        $tpl['project'] = $project;
        $tpl['q'] = $q;

        $curProjectPermissionList = ProjectPermission::where('project.id', $id)->get()->toArray();
        $temp = [];
        $listAssignedId = [];
        //list quyền đã được gán

        foreach ($curProjectPermissionList as $item) {
            if (isset($item['account']['id'])) {
                $temp[$item['account']['id']] = $item;
                $listAssignedId [] = Helper::getMongoId($item['account']['id']);
            }

        }
//        return $curProjectPermissionList;
        $tpl['roleGroup'] = Role::getListGroup();
        $tpl['accountPermission'] = $temp;

//        $listMember = Member::where('projects', 'elemMatch', ['id' => $id]);


        $dependDepartments = isset($project['departments']) ? collect($project['departments']) : 0;

        if ($dependDepartments) {
            $dependDepartmentIds = $dependDepartments->filter(
                function ($item) {
                    return isset($item['id']);
                }
            )->map(
                function ($item) {
                    return $item['id'];
                }
            );

            $listMember = Member::OrWhere('_id', ['$in' => $listAssignedId])
                ->OrWhere('departments.id', ['$in' => $dependDepartmentIds->toArray()]);

        }

        // Nếu search theo từ khóa
        if ($q) {
            $listMember = $listMember->where('name', 'LIKE', '%' . $q . '%')
                ->OrWhere('email', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('account', 'LIKE', '%' . trim($q) . '%')
                ->OrWhere('phone', 'LIKE', '%' . trim($q) . '%');
        }
        $listMember = $listMember->orderBy('_id', 'desc');

        //Phải hiện thị được quyền hiện tại
        //Nếu chưa được gán quyền tại phòng ban thì sao
        //Có 2 thứ cần check quyền, quyền với dự án,
        //Quyền với phòng ban.

        //        $listMemberId  = array_map(function($item){
        //            return $item['_id'];
        //        },$listMember->toArray());
        //        $listRule = [];

        $listMember = Pager::getInstance()->getPager($listMember, 50, 'all');
        $tpl['listObj'] = $listMember;


        return eView::getInstance()->setViewBackEnd(__DIR__, 'project/rule', $tpl);

    }

    function rule_action()
    {
        if (!Member::haveRole(Member::mng_role_update)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện hành động này: (yêu cầu:' . Member::mng_role_update . ')');
        }
        $action = Request::capture()->input('action', 0);
        $project_id = Request::capture()->input('project_id', 0);
        $token = Request::capture()->input('token', 0);
        $staff = Request::capture()->input('staff', 0);

        if (!$project_id) {
            return eView::getInstance()->getJsonError('Không tìm thấy thông tin dự án');
        }
        $project = Project::find($project_id);
        if (!$project) {
            return eView::getInstance()->getJsonError('Dự án không tồn tại hoặc đã bị xóa');
        }
        switch ($action) {
            case "remove":
                {

                    if (!Helper::validateToken($token, $project_id . $staff)) {
                        return eView::getInstance()->getJsonError('Bạn không thể xóa đối tượng này');
                    }
                    $member = Member::find($staff);
                    $query = [
                        'account.id' => $staff,
                        'project.id' => $project_id,
                    ];


                    if (isset($member['projects']) && $member['projects']) {
                        $memberProject = $member['projects'];
                        foreach ($memberProject as $key => $val) {
                            if ($val['id'] == $project_id) {
                                unset($memberProject[$key]);
                            }
                        }
                        if (isset($memberProject) && $memberProject) {
                            $saveMember['projects'] = $memberProject;
                        } else {
                            $saveMember['projects'] = [];
                        }
                        $member->update($saveMember);
                    }
                    ProjectPermission::where($query)->delete();
                    Logs::createLog(
                        [
                            'type' => Logs::TYPE_DELETE,
                            'object_id' => $project_id,
                            'data_object' => [],
                            'note' => "Xoá quyền của" . $member['name'] . '" khỏi dự án "' . $project['name'] . '" bởi ' . Member::getCurentAccount() . '',
                        ], Logs::OBJECT_ROLE
                    );

                    $return['reload'] = true;

                    return eView::getInstance()->getJsonSuccess('Xoá quyền của nhân viên "' . $member['name'] . '" khỏi dự án "' . $project['name'] . '" thành công', $return);

                    break;
                }
            case "add":
                {
                    if (Helper::isEmail($staff)) {
                        $member = Member::getMemberByEmail($staff);
                    } elseif (Helper::isPhoneNumber($staff)) {
                        $member = Member::getMemberByPhone($staff);
                    } else {
                        $member = Member::getMemberByAccount($staff);
                    }
                    if (!$member) {
                        return eView::getInstance()->getJsonError('Không tìm thấy nhân viên có thông tin tương ứng với "' . $staff . '"');
                    }
                    if (isset($member['projects']) && $member['projects']) {
                        foreach ($member['projects'] as $key => $val) {
                            if ($val['id'] == $project_id) {
                                return eView::getInstance()->getJsonError('Nhân viên này đã thuộc dự án');
                            }
                        }
                    }
                    $saveMember = [];
                    $saveMember['projects'] = $member['projects'];
                    $saveMember['projects'][] = [
                        'id' => $project_id,
                        'name' => $project['name'],
                    ];
                    $member->update($saveMember);
                    $return['reload'] = true;

                    Logs::createLog(
                        [
                            'type' => Logs::TYPE_UPDATED,
                            'object_id' => $project_id,
                            'data_object' => [],
                            'note' => "Thêm nhân viên " . $member['name'] . '" vào dự án "' . $project['name'] . '" bởi ' . Member::getCurentAccount() . '',
                        ], Logs::OBJECT_ROLE
                    );

                    return eView::getInstance()->getJsonSuccess('Thêm nhân viên "' . $member['name'] . '" vào dự án "' . $project['name'] . '" thành công', $return);
                    break;
                }
            case "save_dep" :
                {
                    //cập nhật department vào dự án
                    $project_id = Request::capture()->input('project_id', 0);
                    $obj = Request::capture()->input('obj', []);

                    // kiểm tra quyền
                    if ($project_id) {
                        if (!Member::haveRole(Member::mng_project)) {
                            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
                        }
                        $currentObj = Project::find($project_id);
                        if (!$currentObj) {
                            return eView::getInstance()->getJsonError('Không tìm thấy đối tượng. Vui lòng kiểm tra lại');
                        }
                    } else {
                        if (!Member::haveRole(Member::mng_project)) {
                            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
                        }
                    }

                    // cập nhật project

                    // region cập nhật departments
                    if (!isset($obj['departments']['id']) || !is_array($obj['departments']['id'])) {
                        eView::getInstance()->getJsonError('Bạn chưa lựa chọn dự án');
                    }
                    $departments = MetaData::whereIn('_id', $obj['departments']['id'])->get();

                    $objToSave = ['departments' => array_map(
                        function ($item) {
                            $temp = $item;
                            $temp['id'] = strval($item['_id']);
                            unset($temp['_id']);
                            return $temp;
                        }, $departments->toArray()
                    )];

                    Project::where('_id', $project_id)->update($objToSave);
                    // endregion

                    $ret = [
                        'obj' => $obj,
                        'reload' => 1,
                    ];
                    return eView::getInstance()->getJsonSuccess('Cập nhật thành công', $ret);
                    break;
                }
        }

    }

    function show_switch()
    {
        HtmlHelper::getInstance()->setTitle('Chọn dự án làm việc');

        $popup = Request::capture()->input('popup', 0);
        if (!$popup) {
            $tpl['allProjectByMe'] = Project::getAllProject(true);

            return eView::getInstance()->setViewBackEnd(__DIR__, 'project/switch-full', $tpl);
        } else {
            $id = Request::capture()->input('id', 0);
            $token = Request::capture()->input('token', 0);
            if ($id) {
                if (!Helper::validateToken($token, $id)) {
                    return eView::getInstance()->getJsonError('Bạn không có quyền thao tác với dự án này');
                }
                if (!Member::haveAccessProject($id)) {
                    return eView::getInstance()->getJsonError('Bạn không có quyền thao tác với dự án này');
                }
                $project = Project::find($id);
                if (!$project) {
                    return eView::getInstance()->getJsonError('Dự án không tồn tại hoặc đã bị xóa');
                }
                Project::setCurentProject($project->toArray());
                $return = [
                    'link' => admin_link('/'),
                ];

                return eView::getInstance()->getJsonSuccess('', $return);
            }
            $tpl['allProjectByMe'] = Project::getAllProject(true);

            return eView::getInstance()->setViewBackEnd(__DIR__, 'project/switch', $tpl);
        }
    }

    function suggest()
    {
        $q = mb_convert_encoding(Request::capture()->input('q', ''), 'UTF-8');
        $listObj = Project::select(['name', '_id']);
        if ($q) {
            $listObj = $listObj->where(
                function ($query) use ($q) {
                    $query->where('name', 'LIKE', '%' . trim($q) . '%');

                }
            );
        }
        $itemPerPage = 20;
        $listObj = $listObj->orderBy('name', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $re = $listObj->toArray()['data'];
        $dt = [];
        foreach ($re as $k => $v) {
            $dt[] = [
                'id' => $v['_id'],
                'text' => $v['name'],
            ];
        }

        return eView::getInstance()->getJsonSuccess('list', $dt);
    }

    function permission_input()
    {
        if (!empty($_POST)) {
            return $this->permission_save();
        }
        $tpl = [];
        $project_id = Request::capture()->input('project_id', 0);
        $staff_id = Request::capture()->input('staff_id', 0);
        $department_id = Request::capture()->input('department_id', 0);

        if ($project_id) {
            $project = Project::find($project_id);
            $tpl['project'] = $project;
        }

        $member = [];
        if ($staff_id) {
            $member = Staff::find($staff_id);
            $tpl['member'] = $member;
        }
        $tpl['listRoleProject'] = Project::where("departments.id", $department_id)->get();

        return eView::setViewBackEnd(__DIR__, 'project/permission-input', $tpl);
    }

    function permission_save()
    {

        if (!Role::haveRole(Role::mng_role)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
        }

        //update thông tin tại đây
        $staff_id = Request::capture()->input('staff_id', '');
        $staffInDb = [];
        if ($staff_id) {
            $staffInDb = Staff::find($staff_id);
        }
        $department_id = Request::capture()->input('department_id', '');
        $depInDb = [];
        if ($department_id) {
            $depInDb = MetaData::find($department_id);
        }
        if (!$staffInDb && !$depInDb) {
            return eView::getInstance()->getJsonError('Đối tượng cần phân quyền đã bị xóa hoặc không tồn tại');
        }
        $roles = Request::capture()->input('roles', []);
        //Debug::show($roles);
        if (!$roles) {
            return eView::getInstance()->getJsonError('Bạn cần chọn ít nhất 1 dự án hoặc ít nhất 1 quyền');
        }

        foreach ($roles as $key => $value) {
            $projectInDb = Project::find($key);
            if ($projectInDb) {
                $_value = [];
                $_all_permis = [];
                foreach ($value as $ks => $vs) {
                    $_all_permis[$vs] = $vs;
                    $_value[$ks] = [
                        'key' => $ks,
                        'value' => $vs,
                    ];
                }

                $_save = [
                    'project' => [
                        'id' => $key,
                        'name' => $projectInDb['name']
                    ],
                    'permission_list' => $_value,
                    'created_at' => Helper::getMongoDate(),
                ];
                if ($staffInDb) {
                    $_save['staff'] = [
                        'id' => $staff_id,
                        'name' => $staffInDb['name'],
                    ];
                    ProjectPermission::where(['project.id' => $key, 'staff.id' => $staff_id])->delete();
                } else if ($depInDb) {
                    $_save['department'] = [
                        'id' => $department_id,
                        'name' => $depInDb['name'],
                    ];
                    ProjectPermission::where(['project.id' => $key, 'department.id' => $department_id])->delete();
                }

                if (isset($_all_permis['disable']) && count($_all_permis) == 1) {
                    //nghiax laf toàn bộ đều là không được phép=> thì xóa
                } else {
                    ProjectPermission::insert($_save);
                }

            }
            //key=projectid
        }

        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', ['reload' => true]);

    }

    function permission_remove()
    {
        //$project_id = Request::capture()->input('project_id', 0);
        $id = Request::capture()->input('id', 0);
        $token = Request::capture()->input('token', 0);
        if (!$id) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ. Vui lòng thử lại');
        }
        if (!Helper::validateToken($token, $id)) {
            return eView::getInstance()->getJsonError('Bạn không thể thực hiện hành động này');
        }

        /*$project = Project::find($project_id);
        if (!$project) {
            return eView::getInstance()->getJsonError('Dự án không tồn tại hoặc đã bị xóa!');
        }
        if (!Role::haveAccessProject($project)) {
            return eView::getInstance()->getJsonError('Bạn không thể sửa thông tin liên quan đến dự án này!');
        }*/

        if (!Role::haveRole(Role::mng_role)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này!');
        }

        ProjectPermission::where(['_id' => $id])->delete();

        return eView::getInstance()->getJsonSuccess('Xóa thành viên khỏi dự án thành công', ['reload' => true]);
    }


    function permission_input_staff()
    {
        if (!empty($_POST)) {
            return $this->permission_save();
        }
        $tpl = [];
        $project_id = Request::capture()->input('project_id', 0);
        $staff_id = Request::capture()->input('staff_id', 0);
        $id = Request::capture()->input('id', 0);

        if ($project_id) {
            $project = Project::find($project_id);
            $tpl['project'] = $project;
        }

        $member = [];
        if ($staff_id) {
            $member = Staff::find($staff_id);
            $tpl['member'] = $member;
        }
        $tpl['listRoleProject'] = Role::getListRoleProject();
        if ($member && $project_id) {
            $listRoleOfMember = ProjectPermission::getPermissionOfStaff($member['_id'], $project_id);
            if (!$listRoleOfMember) {
                $listRoleOfMember = ProjectPermission::find($id);
            }
            $tpl['listRoleOfMember'] = $listRoleOfMember;
        }

        return eView::setViewBackEnd(__DIR__, 'project/permission-input-staff', $tpl);
    }

    function permission_save_staff()
    {
        $token = Request::capture()->input('token', '');
        if (!Helper::validateToken($token, 'ngannv' . date('d'))) {
            return eView::getInstance()->getJsonError('Bạn không thể thực hiện hành động này');
        }
        if (!Role::haveRole(Role::mng_role)) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
        }

        //update thông tin tại đây
        $obj = Request::capture()->input('obj', []);
        $roles = Request::capture()->input('roles', []);

        if (!isset($obj['projects']) || !$obj['projects']) {
            return eView::getInstance()->getJsonError('Bạn cần chọn ít nhất 1 dự án');
        }
        if (!isset($obj['staffs']) || !$obj['staffs']) {
            return eView::getInstance()->getJsonError('Bạn cần chọn ít nhất 1 nhân viên');
        }

        if (!is_array($roles) || !$roles) {
            return eView::getInstance()->getJsonError('Bạn cần chọn ít nhất 1 quyền cho tài khoản này');
        }

        $allRoleKey = array_keys(Role::getListRoleKey());
        if (!array_intersect(array_keys($roles), $allRoleKey)) {
            return eView::getInstance()->getJsonError('Dữ liệu không hợp lệ. Vui lòng kiểm tra lại');
        }

        $lsStaffId = explode(',', $obj['staffs']);
        $lsProjectId = explode(',', $obj['projects']);
        $saveToStaff = [];
        // Debug::show($roles);
        if ($roles) {
            $_role = [];
            foreach ($roles as $key => $value) {
                $_role[$key] = [
                    'key' => $key,
                    'value' => $value,
                ];
            }
        }
        foreach ($lsProjectId as $project_id) {
            $project = Project::find($project_id);
            if ($project) {
                foreach ($lsStaffId as $staff_id) {
                    $staff = Staff::find($staff_id);
                    if ($staff) {
                        $saveToStaff[$staff_id][$project_id] = [
                            'id' => $project_id,
                            'name' => $project['name'],
                        ];
                        ProjectPermission::where(
                            [
                                'staff.id' => $staff_id,
                                'project.id' => $project_id,
                            ]
                        )->delete();
                        $savePermis = [
                            'project' => ['id' => $project_id, 'name' => $project['name']],
                            'staff' => ['id' => $staff_id, 'name' => $staff['name']],
                            'permission_list' => $_role,
                            'created_at' => Helper::getMongoDate(),
                        ];
                        ProjectPermission::insert($savePermis);
                    }
                }
            }
        }
        if ($saveToStaff) {
            foreach ($saveToStaff as $staff_id => $item) {
                Staff::where('_id', $staff_id)->update(
                    ['projects' => array_values($item)]
                );
            }
        }

        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', ['reload' => true]);

    }

    /**
     * Hiển thị toàn bộ quyền của nhân viên với dự án
     */
    function show_permistion_staff()
    {
        $staff_id = Request::capture()->input('staff_id', '');

        $staffInDb = Staff::find($staff_id);
        $allPermission = [];
        if ($staffInDb) {
            //các phòng ban
            $lsPermissionByStaff = ProjectPermission::where(['staff.id' => $staff_id])->get()->keyBy('project.id');
            //Debug::show($lsPermissionByStaff);
            $departments = @$staffInDb['departments'];
            $allPermission = $lsPermissionByStaff->toArray();
            if ($departments) {
                $departments = array_column($departments, 'id');
                //Debug::show($departments);
                $lsPermissionByDep = ProjectPermission::whereIn('department.id', $departments)->get()->keyBy('project.id')->toArray();
                //Debug::show($lsPermissionByDep);
                foreach ($lsPermissionByDep as $key => $value) {
                    if (!isset($allPermission[$key])) {
                        $allPermission[$key] = $value;
                    } else {
                        //Nếu đã tồn tại thì xem trùng thông tin thì tính sao? ưu tiên cái cho phép hay ưu tiên cái không cho phép? hay cứ ghi đè thôi đến đâu thì đến
                    }
                }


            }

        }
        $tpl['allPermission'] = $allPermission;

        return eView::getInstance()->setViewBackEnd(__DIR__, 'project/department_list', $tpl);

    }

    function rule_popup()
    {
        $tpl = [];
        $project_id = Request::capture()->input('project_id', 0);
        $account_id = Request::capture()->input('account_id', 0);

        if (!isset($project_id) || !isset($account_id)) {
            return eView::getInstance()->getJsonError('Yêu cầu không đúng, thiếu thông tin tài khoản và dự án ');
        }
        $curProject = Project::find($project_id);
        $curAccount = Member::find($account_id);
        if (!$curAccount) {
            return eView::getInstance()->getJsonError('Yêu cầu không đúng, không tìm thấy tài khoản yêu cầu');
        }
        if (!$curProject) {
            return eView::getInstance()->getJsonError('Yêu cầu không đúng, không tìm thấy dự án yêu cầu');
        }
        $allRole = Role::where([])->first();

        $tpl['curAccount'] = $curAccount->toArray();
        $tpl['curProject'] = $curProject->toArray();
        $tpl['allRole'] = $allRole->toArray();
        $tpl['allRoleGroup'] = Role::getListGroup();

        $query = [
            'account.id' => $account_id,
            'project.id' => $project_id,
        ];
        $curPermission = ProjectPermission::where($query)->first();
        if ($curPermission) {
            $tpl['obj'] = $curProject;
        }
        return eView::getInstance()->setViewBackEnd(__DIR__, 'project/rule-popup', $tpl);
    }

    function save_member_role()
    {
        //TODO Cần kiểm tra quyền trước khi chạy

        $obj = Request::capture()->input('obj', []);

        $account_id = isset($obj['account_id']) ? $obj['account_id'] : 0;
        $project_id = isset($obj['project_id']) ? $obj['project_id'] : 0;

        if (!isset($project_id) || !isset($account_id)) {
            return eView::getInstance()->getJsonError('Yêu cầu không đúng, thiếu thông tin tài khoản và dự án ');
        }
        $curProject = Project::find($project_id);
        $curAccount = Member::find($account_id);
        if (!isset($obj['role_group']) || empty($obj['role_group'])) {
            return eView::getInstance()->getJsonError('Bạn vui lòng chọn nhóm quyền cho nhân viên');
        }
        $roleGroup = Role::getListGroup();
        if ($obj['role_group'] != '') {
            if (!isset($roleGroup[$obj['role_group']])) {
                return eView::getInstance()->getJsonError('Yêu cầu không đúng, nhóm quyền không tồn tại');
            }
        }


        if (!$curAccount) {
            return eView::getInstance()->getJsonError('Yêu cầu không đúng, không tìm thấy tài khoản yêu cầu');
        }
        if (!$curProject) {
            return eView::getInstance()->getJsonError('Yêu cầu không đúng, không tìm thấy dự án yêu cầu');
        }

        $query = [
            'account.id' => $account_id,
            'project.id' => $project_id,
        ];
        $objToSave = [
            'account' => ['id' => $account_id, 'name' => $curAccount['name']],
            'project' => ['id' => $project_id, 'name' => $curProject['name']],
            'role_group' => $obj['role_group'],
            'updated_at' => Helper::getMongoDateTime(),
        ];
        $curPermission = ProjectPermission::where($query)->first();
        if ($curPermission) {
            //Cập nhật
            $curPermission->update($objToSave);
        } else {
            $objToSave['created_at'] = Helper::getMongoDateTime();
            $id = ProjectPermission::insertGetId($objToSave);
            $curPermission = ProjectPermission::find($id);
        }
        $ret = [
            'obj' => $curPermission,
            'reload' => 1,
        ];
        Logs::createLog(
            [
                'type' => Logs::TYPE_UPDATED,
                'object_id' => $project_id,
                'project_id' => $project_id,
                'project_name' => $curProject['name'],
                'data_object' => [],
                'note' => "Cập nhật quyền của" . $curAccount['name'] . '" liên quan dự án "' . $curProject['name'] . '" bởi ' . Member::getCurentAccount() . '',
            ], Logs::OBJECT_ROLE
        );

        $retMsg = "'" . $curAccount['name'] . "'"
            . " vừa được cập nhật quyền "
            . "'" . $roleGroup[$obj['role_group']]['name'] . "'"
            . " tại dự án " . $curProject['name'];
        return eView::getInstance()->getJsonSuccess($retMsg, $ret);
    }
}
