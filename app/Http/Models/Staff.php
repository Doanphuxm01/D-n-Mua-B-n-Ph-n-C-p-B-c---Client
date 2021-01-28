<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\Helper;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Staff extends Member
{
    const DEP_GROUP_SAN_XUAT = [
        'id' => 'DEP_GROUP_SAN_XUAT',
        'name' => 'Nhóm sản xuất'
    ];
    const DEP_GROUP_QUAN_LY = [
        'id' => 'DEP_GROUP_QUAN_LY',
        'name' => 'Nhóm quản lý'
    ];

    const DEP_GROUP_GIAM_DOC = [
        'id' => 'DEP_GROUP_GIAM_DOC',
        'name' => 'Nhóm giám đốc'
    ];

    const DEP_GROUP_NAME = [
        'DEP_GROUP_SAN_XUAT' => 'Nhóm sản xuất',
        'DEP_GROUP_QUAN_LY' => 'Nhóm quản lý',
        'DEP_GROUP_GIAM_DOC' => 'Nhóm giám đốc',
    ];

    /**
     * @param $deparment
     * @note: Lấy danh sách nhân viên theo phòng ban
     */
    static function getAllStaffByDepartment($list_id)
    {
        $query = ['department.id' => ['$in' => []]];
        if (is_string($list_id)) {
            $query['department.id']['$in'] [] = $list_id;
        }
        if (is_array($list_id)) {
            foreach ($list_id as $item) {
                $query['department.id']['$in'] [] = $item;
            }
        }
        if (!empty($query['department.id']['$in'])) {
            return Member::where($query)->select( ["_id", "emails", "phones", "account", "code"])->get();
        } else {
            return collect();
        }

    }

    static function getAllStaffByProject($list_id)
    {
        $listProjectPermission = [];
        $query = ['project.id' => ['$in' => []]];
        if (is_string($list_id)) {
            $query['project.id']['$in'] [] = $list_id;
        }
        if (is_array($list_id)) {
            foreach ($list_id as $item) {
                $query['project.id']['$in'] [] = $item;
            }
        }
        if (!empty($query['project.id']['$in'])) {
            $listProjectPermission = ProjectPermission::where($query)->get();
        } else {
            $listProjectPermission = [];
        }

        if (empty($listProjectPermission)) {
            return collect();
        }
        $listAccountId = collect($listProjectPermission)->pluck('account.id');
        if ($listAccountId->count() > 0) {
            return Member::whereIn('_id', $listAccountId->values()->toArray())->get();
        } else {
            return collect();
        }
    }

    //@param $obj đối tượng cần quan tâm
    //@param $mng_obj là đối tượng xử lý
    static function getStaffCanViewObj($obj, $mngObj, $options = ['manage' => false])
    {
        $listId = collect([]);
        $listMember = collect();
        #region case relate auto pass
        if (isset($obj['related'])) {
            $related = $obj['related'];
            if (is_string($related) && $related === 'all') {
                return self::all();
            }
            if (isset($related['staff']) && is_array($related['staff'])) {
                $listId = $listId->merge(
                    collect($related['staff'])->pluck(('id'))->values()
                );
            }

            if (isset($related['project']) && is_array($related['project'])) {
                $listProjectId = collect($related['project'])->pluck(('id'))->values();
                $listMember = $listMember->merge(self::getAllStaffByProject($listProjectId)->keyBy('_id'));
            }
            if (isset($related['department']) && is_array($related['department'])) {
                $listDepId = collect($related['department'])->pluck(('id'))->values();
                $listMember = $listMember->merge(Staff::getAllStaffByDepartment($listDepId)->keyBy('_id'));
            }
        }
        #endregion

        #region options by manage
        if (isset($options['manage']) && $options['manage']) {
            $listManageMember = Member::whereIn('role_group', [Role::group_admin,
                Role::group_quan_tri,
                Role::group_khoi_quan_ly_nhan_vien,
                Role::group_khoi_quan_ly_ql_hop_dong,
                Role::group_khoi_quan_ly_ql_nhan_su,
                Role::group_khoi_quan_ly_quan_ly,])->get()->keyBy('_id');

            $listMember = $listMember->merge($listManageMember);

        }
        #endregion

        #handle riêng cho trường project
        $roleCanView = collect(Role::first())->filter(function ($item) use ($mngObj) {
            return collect(@$item['value'])->first(function ($rolekey) use ($mngObj) {
                return $rolekey == Role::getRoleKey($mngObj, Role::mng_action_view);
            });
        })->pluck('key');
        if (isset($obj['project']['id']) && Helper::isMongoId($obj['project']['id'])) {
            $projectMember = $listMember->merge(self::getAllStaffByProject($obj['project']['id'])->keyBy('_id'))->filter(function ($item) use ($roleCanView) {
                return in_array(@$item['group_role'], $roleCanView->toArray());
            });
            $listMember = $listMember->merge($projectMember);
        } else if (isset($obj['project']) && Helper::isMongoId($obj['project'])) {
            $projectMember = $listMember->merge(self::getAllStaffByProject($obj['project'])->keyBy('_id'));
            $projectMember = $projectMember->filter(function ($item) use ($roleCanView) {
                return in_array(@$item['role_group'], $roleCanView->values()->toArray());
            });
            $listMember = $listMember->merge($projectMember);
        }
        #endregion

        if ($listId->count() > 0) {
            $listMember = $listMember->merge(Member::whereIn('_id', $listId)->get()->keyBy('_id'));

        }

        return $listMember->values()->toArray();

    }

    /**
     * @param $return_string => 'true' or object
     * @param $staff => staff object hoacj arrray hoac staff id (member)
     * @return bool
     * Laays toan bo danh sachs phong ban cua nhan vien
     */
    static function getPartmentOfStaff($staff, $return_string = true)
    {
        if (is_string($staff)) {
            $staff = Staff::find($staff);
        }
        if (isset($staff['departments'])) {
            $lsDepId = @array_column($staff['departments'], 'id');
            $dep = MetaData::whereIn('_id', $lsDepId)->get();
            if ($return_string === 'id_only') {
                if ($dep) {
                    $dep = $dep->keyBy('_id')->toArray();
                    return array_keys($dep);
                } else {
                    return [];
                }
            }
            if ($return_string === 'id_and_name') {
                if ($dep) {
                    $dep = $dep->keyBy('_id')->toArray();
                    return $dep;
                } else {
                    return [];
                }
            } elseif ($return_string == true) {
                if ($dep) {
                    return implode(', ', $dep->pluck('name')->toArray());
                } else {
                    return '';
                }
            }
        }
        return false;
    }

    /**
     * @param $staff_id
     * @param $return
     * @return array
     */
    static function getProjectOfStaff($staff_id, $return = 'id_only')
    {
        $where = [
            'staff_id' => $staff_id,
        ];
        $item = ProjectPermission::where($where)->select(['project_id'])->get()->keyBy('project_id')->toArray();
        if ($item) {
            $item = array_keys($item);
            if ($return === 'id_only') {
                return $item;
            }
            $lsProject = Project::whereIn('_id', $item)->select(['_id', 'name'])->get();
            return $lsProject;
        }
        return [];

    }
    /**
     * phần phần quyền
     * - trong phân quyền chức vụ không quan trọng nữa
     * 1: add nhân viên vào phòng công tác
     * 2: add nhân viên vào nhóm quyền
     * 3: show danh sách dự án mà nhân viên này đang được gán
     *
     * bước 2:
     * phần quản lý dự án:
     * - add dự án cho phòng
     * -- sau khi add thì gửi thông báo (notifcation + email) cho ông nào có quyền  10.2    Phân quyền nhân viên trong dự án để ông này vào trong đó chọn phòng, chọn dự án và add nhân viên của phòng vào dự án
     *
     * bước 3: Check quyền
     *1: khi nhân viên login: Lấy quyền, lấy dự án, lấy phòng của nhân viên này
     * - Vào các chức năng cần check dự án: CÔNG VĂN, HỒ SƠ TÀI LIỆU thì check chức năng xong check dự án: bao gồm các thao tác và listing
     * - Vào các chức năng khác k liên quan dự án thì chỉ cần check chức năng
     */

}
