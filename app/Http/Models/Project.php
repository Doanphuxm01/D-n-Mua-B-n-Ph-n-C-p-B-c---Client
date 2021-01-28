<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\ProjectPermission;

class Project extends BaseModel
{
    public $timestamps = false;
    const table_name = 'projects';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];
    const SESSION_CURRENT_PROJECT = 'current_project';


    static $curentProject = [];// id, name, mô tả......

    static $allProjectByMe = [];

    static function getAllProject($only_me = false)
    {
        //return project by me
        return self::where('removed', BaseModel::REMOVED_NO)->orderBy('_id', 'desc')->get()->keyBy('_id')->toArray();
    }

    static function getAllProjectByMe()
    {
        if (!self::$allProjectByMe) {
            self::$allProjectByMe = ProjectPermission::getAllPermissionOfStaff(Member::$currentMember);
        }
        return self::$allProjectByMe;
    }

    static function getByAlias($alias)
    {
        $where = [
            'alias' => $alias,
        ];

        return self::where($where)->first();
    }

    static function setCurentProject($project)
    {
        self::$curentProject = $project;
        Helper::setSession(self::SESSION_CURRENT_PROJECT, $project);
    }

    static function getCurentProject()
    {
        if (self::$curentProject) {
            return self::$curentProject;
        }

        $project_id = Request::capture()->input('project_id');
        if ($project_id) {
            $project = self::find($project_id);
            if (!$project) {
                return false;
            } else {
                self::$curentProject = $project;
            }
        } else {
            self::$curentProject = Helper::getSession(self::SESSION_CURRENT_PROJECT);
        }
        return self::$curentProject;
    }

    static function getCurentProjectId()
    {
        return @self::$curentProject['_id'];
    }

    static function getListProjects($account_id = "", $options = [])
    {
        $listProjectId = ProjectPermission::getAccessListProjectId();
//        if(false &&  Member::isRoot()) {
        if (empty(Role::getCurrentPermissionGroup())) {
            return [];
        }
        if (Role::isBelongGroupManage() || Role::isRoot() || Role::isAdmin()) {
            return self::where(['removed'=>'no'])->orderBy('_id', -1)->get();
        } else if (isset($options['working']) && isset($account_id)) {
            $listProjectId = collect(ProjectPermission::where("account_id", $account_id)->get()->toArray())->pluck('project_id')->toArray();
            return self::whereIn('_id', $listProjectId)->orderBy('_id', -1)->get();
        } else {
            return self::whereIn('_id', $listProjectId)->orderBy('_id', -1)->get();
        }

    }
}
