<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use App\Http\Models\Member;


use Illuminate\Support\Facades\DB;

class ProjectPermission extends BaseModel
{
    const table_name = 'poroject_permission';

    public $timestamps = false;
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];
    static $permission = [];

    static function getPermissionOfStaff($staff_id, $project_id = false)
    {
        $where = [
            'staff.id' => $staff_id,
        ];
        if ($project_id) {
            $where['project.id'] = $project_id;
            $item = self::where($where)->first();
        } else {
            $item = self::where($where)->get()->keyBy('project.id')->toArray();
        }

        return $item;

    }

    static function getAllPermissionOfStaff($member)
    {
        if (isset(self::$permission[$member['_id']])) {
            return self::$permission[$member['_id']];
        }
        $where = [
            'staff.id' => $member['_id'],
        ];
        $lsObj = ProjectPermission::where($where);
        if (isset($member['departments'][0]['id']) && $member['departments'][0]['id']) {
            $dep_ids = @array_column($member['departments'], 'id');
            $lsObj = $lsObj->orWhereIn('department.id', $dep_ids);
        }

        self::$permission[$member['_id']] = $lsObj->get()->keyBy('project.id');

        return self::$permission[$member['_id']];
    }

    static function getPermissionOfDepartment($department_id, $project_id = false)
    {
        $where = [
            'department.id' => $department_id,
        ];
        if ($project_id) {
            $where['project.id'] = $project_id;
            $item = self::where($where)->first();
        } else {
            $item = self::where($where)->get()->keyBy('project.id')->toArray();
        }

        return $item;

    }
    //Cách check quyền hoạt động như sau
    /*
      1. Kiểm tra nhóm quyền yêu cầu ? ,
      2. Có 2 tham chiếu yêu cầu, department_id  và project_id
      3. Cái nào yêu cầu department_id thì query với department_id ,
      4. Ngooài ra có thể có 2 cơ chế check quyền là 1.sử dụng phân quyền cho chức vụ trong phòng ban (không ổn lắm nhỉ), 2 là phân quyền phòng ban cho nhân viên
     */
    static function getAccessListProjectId()
    {
        if (Role::isRoot() || Role::isAdmin() || Role::isBelongGroupManage()) {
            return false;
        }

        $curAccount = Member::getCurent();
        $listPermissionAccess = self::where('account.id', strval($curAccount['_id']))->get();

        $listOfProjectForCurrentDep = [];
        if (isset($curAccount ['department']['id'])) {
            $listOfProjectForCurrentDep = Project::where('departments.id', $curAccount ['department']['id'])->get();
            $listOfProjectForCurrentDep = collect($listOfProjectForCurrentDep->toArray())
                ->pluck('_id')
                ->map(function ($item) {
                    return strval($item);
                })->toArray();
        }

        $listIdFromPermission = collect($listPermissionAccess->toArray())->pluck('project')->pluck('id')->toArray();

        $ret = [];
        foreach ($listIdFromPermission as $item) {
            $ret[$item] = 1;
        }

        $mng_obj = Role::mng_project;
        $mng_action = Role::mng_action_role;
        $requireRole = [Role::getRoleKey($mng_obj, $mng_action)];
        if (Role::haveRole2($requireRole)) {
            foreach ($listOfProjectForCurrentDep as $item) {
                $ret[$item] = 1;
            }
        }

        $ret_list = [];
        foreach ($ret as $key => $item) {
            $ret_list[] = $key;
        }


        return $ret_list;
    }


}
