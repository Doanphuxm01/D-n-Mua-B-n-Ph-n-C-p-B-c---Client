<?php


namespace App\Http\Models;


use App\Elibs\Helper;

class Department extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'kayn_department';
    protected $table = self::table_name;
    static $unguarded = TRUE;
    const DEPARTMENT_LEVEL = [
        'level_1' => [
            'id' => 'level_1',
            'name' => 'Phòng ban cha',
        ], 'level_2' => [
            'id' => 'level_2',
            'name' => 'Phòng ban con',
        ],
    ];
    const DEPARTMENT = 'department'; //Phòng ban

    /**
     * Danh sách phòng ban
     */
    static function getParentDepartment($id = false, $_where = [])
    {
        $where = [
            'department_type' => ['$ne' => self::DEPARTMENT_LEVEL['level_2']['id']],
            'status' => BaseModel::STATUS_ACTIVE,
        ];
        if($id) {
            $where['_id'] = ['$ne' => Helper::getMongoId($id)];
        }
        $listObj = self::where($where)->orderBy('name','asc')->get();


        return $listObj;
    }

    static function getDepartChildren()
    {
        $listChild = self::where(
            [
                'department_type' => self::DEPARTMENT_LEVEL['level_2']['id']
            ]
        )->get();


        $childMap = [];
        foreach ($listChild as $item) {
            if (isset($item['parent_dep']['id'])) {
                $childMap[$item['parent_dep']['id']][] = $item;
            }
        }
        return $childMap;
    }

    /**
     * Danh sách phòng ban
     */
    static function getDepartment($_where = [])
    {
        $where = [
            'status' => BaseModel::STATUS_ACTIVE
        ];
        $listObj = self::where($where)->get();


        return $listObj;
    }

    /**
     * Danh sách phòng ban
     */
    static function getDepartmentId($_where = [])
    {
        $where = [
            'status' => BaseModel::STATUS_ACTIVE
        ];
        $listObj = self::where($where)->get()->keyBy('_id')->toArray();


        return $listObj;
    }

    /**
     * Danh sách phòng ban
     */
    static function getMyDepartment($_where = [])
    {
        $curMember = Member::getCurent();


        if (Role::isRoot()) {
            $where = [
                'status' => BaseModel::STATUS_ACTIVE
            ];

        } else if (isset($curMember['department']['id'])) {
            $where = [
                '_id' => Helper::getMongoId($curMember['department']['id'])
            ];
        } else {
            return [];
        }
        $listObj = self::where($where)->get();

        return $listObj;
    }

    static function getAll()
    {
        $lsObj = BaseModel::table(self::table_name)
            ->where('removed', BaseModel::REMOVED_NO)
            ->orderBy('_id', 'DESC')->get();
        return $lsObj;
    }
}