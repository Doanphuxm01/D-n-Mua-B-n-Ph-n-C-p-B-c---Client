<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Post extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_post';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = ['name', 'alias', 'brief', 'avatar', 'type', 'link_source', 'object', 'categories', 'departments', 'created_at', 'updated_at', 'actived_at'];

    static $objectRegister = [
        'news-edu' => [
            'key' => 'news-edu',
            'name' => 'Tin tức giáo dục',
        ],
        'news' => [
            'key' => 'news',
            'name' => 'Tin tức chung',
        ],
        'help' => [
            'key' => 'help',
            'name' => 'Bài hướng dẫn',
        ],
    ];


    const TYPE_IS_POST = 'post';
    const TYPE_IS_MEDIA = 'media';

    static function buildLinkDetail($post)
    {
        if ($post->object == Post::$objectRegister['news-edu']['key']) {
            return url('/giao-duc/' . $post->alias . '.html');
        }

    }

    static function getPostByAlias($alias)
    {
        $where = [
            'alias' => $alias
        ];
        return self::where($where)->first();
    }

    public static function getPostByCate($cate, $limit = 6)
    {
        $listItem = self::where(['status' => self::STATUS_ACTIVE])->where('categories', 'elemMatch', ['alias' => $cate])
            ->select(self::$basicFiledsForList)->limit($limit)->orderBy('actived_at', 'DESC')->get();
        return $listItem;

    }

    public static function getNewsLastest($limit = 6)
    {
        $where = [
            'status' => Post::STATUS_ACTIVE,
            'object' => Post::$objectRegister['news']['key']
        ];
        return Post::where($where)->select(Post::$basicFiledsForList)->limit($limit)->orderBy('actived_at','DESC')->get();

    }

    public static function buildLinkDelete($object,$router='')
    {
        return admin_link('news/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    }

}
