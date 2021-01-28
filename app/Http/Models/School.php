<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

/***
 * Class School
 * name: Tên trường
 * local_name:Tên địa phương (ví dụ trung quốc có tên hán tự)
 * alias: alias build link
 * content: Nội dung chi tiết
 * brief: Mô tả ngắn
 * level: cấp học
 * categories:[]: danh muc
 * status: trạng thái
 * profile_online:website,fanpage,....
 * location: {name,alias,type:country|region} Quốc gia
 * avatar: logo
 * cover:Ảnh cover nếu có
 * link_source:'nguon thong tin';
 * info:' Các thông tin mở rộng khác, ngày thành lập, tên gọi khác)
 * @package App\Http\Models
 */
class School extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_school';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = ['profile_online', 'alias', 'brief', 'name', 'avatar', 'info', 'level', 'location'];

    public static function tmp()
    {
        return DB::table('io_schoolx');
    }
    /***
     * @var array
     */
    static $locationRegister = [
        'khu-vuc-tp-hcm' => [
            'key' => 'khu-vuc-tp-hcm',
            'name' => 'Khu vực TP Hồ Chí Minh',
        ],
        'khu-vuc-ha-noi' => [
            'name' => 'Khu vực TP Hà Nội',
        ],
        'a' => [
            'key' => 'a',
            'name' => 'a',
        ],

    ];
    static $levelRegister = [
        'dai-hoc' => 'Đại học',
        'cao-dang' => 'Cao đẳng',
        'cao-dang-nghe' => 'Cao đẳng nghề',
        'trung-cap' => 'Trung cấp',
        'trung-cap-chuyen-nghiep' => 'Trung cấp chuyên nghiệp',
        'trung-hoc-pho-thong' => 'Trung học phổ thông',
    ];

    static function buildLinkDetail($post)
    {
        return url('/truong-hoc/' . $post->alias . '.html');
    }

    static function getByAlias($alias)
    {
        $where = [
            'alias' => $alias
        ];
        return self::where($where)->first();
    }

    public static function getByCate($cate, $limit = 6)
    {
        $listItem = self::where(['status' => self::STATUS_ACTIVE])->where('categories', 'elemMatch', ['alias' => $cate])
            ->select(self::$basicFiledsForList)->limit($limit)->orderBy('actived_at', 'DESC')->get();
        return $listItem;

    }

    public static function buildLinkDelete($object,$router='')
    {
        return admin_link('school/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

    public static function buildLinkLocation($val)
    {
        $alias = '';
        if ($val->type == 'region') {
            $alias = 'khu-vuc/' . $val->alias;
        } else if ($val->type == 'country') {
            $alias = 'quoc-gia/' . $val->alias;
        }
        return asset('/truong-hoc/' . $alias);
    }

    public
    static function getMetaDataTop($limit = 25)
    {
        $cursor = self::raw(function ($collection) {

            return $collection->aggregate([
                ['$unwind' => '$meta_data'],
                ['$match' =>
                    [
                        'status' => self::STATUS_ACTIVE,
                    ]
                ],
                ['$group' => [
                    "_id" => '$meta_data.alias',
                    "name" => ['$first' => '$meta_data.name'],
                    "alias" => ['$first' => '$meta_data.alias'],
                    "object" => ['$first' => '$meta_data.object'],
                    "type" => ['$first' => '$meta_data.type'],
                    "count" => ['$sum' => 1]
                ]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 30]
            ]);

        });
        return $cursor;

    }

    public
    static function getListLocation($limit = 25)
    {
        $cursor = self::raw(function ($collection) {

            return $collection->aggregate([
                ['$unwind' => '$location'],
                ['$match' =>
                    [
                        'status' => self::STATUS_ACTIVE,
                    ]
                ],
                ['$group' => [
                    "_id" => '$location.alias',
                    "name" => ['$first' => '$location.name'],
                    "alias" => ['$first' => '$location.alias'],
                    "type" => ['$first' => '$location.type'],
                    "count" => ['$sum' => 1]
                ]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 25]
            ]);

        });
        return $cursor;

    }
}
