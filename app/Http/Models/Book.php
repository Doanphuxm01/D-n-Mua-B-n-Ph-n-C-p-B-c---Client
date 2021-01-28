<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use App\Http\Models\Media;
use Illuminate\Support\Facades\DB;

class Book extends BaseModel
{
    public $timestamps = false;
    const table_name = 'io_book';
    const table_io_cate_post_rel = 'io_cate_post_rel';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = ['name', 'alias', 'type', 'brief', 'avatar','categories'];
    const TYPE_IS_NEWS = 'news';//Định nghĩa value cho trường type trong table, conffig cho gioongs vow Cate
    const TYPE_IS_IMAGE = 'image';
    const TYPE_IS_VIDEO = 'video';
    const TYPE_IS_AUDIO = 'audio';
    const TYPE_IS_BOOK = 'book';
    const TYPE_IS_COMIC = 'comic';
    const TYPE_IS_LIBRARIES_CODE = 'code';


    const TYPE_IS_BAI_TAP = 'bai-tap';
    const TYPE_IS_LY_THUYET = 'ly-thuyet';

    const OBJECT_IS_SUBJECT = 'subject';
    const MONGO_CONNECTION = 'mongodb';
    const COLLECTION_POST = "posts";


    static $objectRegister = [
        'subject' => [
            'key' => 'subject',
            'name' => 'Môn học',
        ],
    ];



    private static $mainIndex = 'doctruyen_index';
    private static $mainType = 'doctruyen_io_post_type';
    private static $recommendType = 'doctruyen_io_post_recommend_type';

    /**
     * Định nghĩa status
     */

    public static function getTableCatePostRelation()
    {
        return DB::table('io_cate_post_rel');
    }


    public static function getTableChapter()
    {
        return DB::table('io_post_chapter');
    }

    public static function buildToken($string)
    {
        return sha1('NGANNV@SAKURA#MINATO$SONGOKU' . $string);
    }

    public static function buildLinkEdit($item, $input = 'input')
    {
        return admin_link('book/' . $input . '?id=') . $item['id'];
        if (!isset($item['type'])) {
            return null;
        }

        switch ($item['type']) {

            case self::TYPE_IS_LIBRARIES_CODE:
                {
                    return admin_link('source-code/' . $input . '?id=') . $item['id'];
                }
            case self::TYPE_IS_AUDIO:
                {
                    return admin_link('audio/' . $input . '?id=') . $item['id'];
                }
            case self::TYPE_IS_BOOK:
                {
                    return admin_link('book/' . $input . '?id=') . $item['id'];
                }
            case self::TYPE_IS_NEWS:
                {
                    return admin_link('blog/' . $input . '?id=') . $item['id'];
                }
            case self::TYPE_IS_VIDEO:
                {
                    return admin_link('video/' . $input . '?id=') . $item['id'];
                }
        }
    }

    public static function buildLinkDelete($item)
    {
        return self::buildLinkEdit($item, '_delete');
    }

    public static function buildLinkDetail($item)
    {
        if (!isset($item->alias) || !$item->alias) {
            $item->alias = Helper::convertToAlias($item->name);
        }
        if (!$item->alias) {
            $item->alias = Helper::convertToAlias($item->name);
        }
        $alias = '';
        if ($item->categories) {
            foreach ($item->categories as $cate) {
                if ($cate['type'] == Cate::$cateTypeRegister['cate']['key']) {
                    $alias .= $cate['alias'] . '/';
                    break;
                }
            }
        }
        return url('/' . $alias . $item->alias . '.html');
    }

    public static function buildLinkAuthor($item)
    {
        return url('/author/' . $item->alias);
    }


    public static function buildAvatarPost($image, $w = 0, $h = 0)
    {
        if (!$image) {
            return Media::getImageSrc('');
        }
        $img = json_decode($image, true);

        if (isset($img['src'])) {
            return Media::getImageSrc($img['src'], $w, $h);
        } else {
            foreach ($img as $key => $val) {
                if (isset($val['avatar']) && $val['avatar'] == 1) {
                    // Debug::show($val['src']);
                    return Media::getImageSrc($val['src'], $w, $h);
                }
            }

            return Media::getImageSrc($img[0]['src'], $w, $h);
        }


    }


    public static function getTags($item)
    {
        if ($item->tags) {
            return explode(",", $item->tags);
        }
        return null;

    }

    /**
     * @param $image_indb => json
     */
    public static function buildImagesLink($image_indb)
    {
        if ($image_indb) {
            $img = json_decode($image_indb, true);
            if ($img) {
                $images = [];
                //Debug::show($img);
                foreach ($img as $key => $val) {
                    if (isset($val['src'])) {
                        $images[] = Media::buildImageLink($val['src']);
                    }
                }
                return $images;
            }
        } else {
            return [];
        }
    }

    public static function getLastest($limit, $type = self::TYPE_IS_BOOK, $delcache = false)
    {
        $cache_key = "_post_lastest_ok" . $type . $limit;


        if ($delcache) {
            eCache::del($cache_key);
            return true;
        }
        $data = eCache::get($cache_key);
        if ($data) {
            return $data;
        }
        $w = [
            'type' => $type,
            'status' => self::STATUS_ACTIVE
        ];
        $data = self::where($w)->orderBy('updated_at', 'DESC')->select(self::$basicFiledsForList)->limit($limit)->get();
        eCache::add($cache_key, $data, 8640);
        return $data;

    }

    public static function getPostByCate($cate, $baseCondition, $limit = 4, $delcache = false)
    {
        $cache_key = "_post_lastest_by_cate_" . http_build_query($baseCondition) . $limit . $cate;

        if ($delcache) {
            eCache::del($cache_key);
            return true;
        }
        $data = eCache::get($cache_key);
        if ($data) {
            return $data;
        }
        $where = [
            'object' => Book::OBJECT_IS_SUBJECT,
            'status' => Book::STATUS_ACTIVE,
        ];
        $itemUpdated = Book::where($where)->where('categories', 'elemMatch', ['alias' => $cate]);
        $itemUpdated = $itemUpdated->select(['name', 'alias', 'type', 'brief', 'avatar','categories'])->limit($limit)->get()->toArray();
        /* $itemUpdated = DB::table('io_post as t1')->join('io_cate_post_rel as t2', 't1.id', '=', 't2.post_id')->where('t2.cate_id', '=', $cate)->where($baseCondition);
         $itemUpdated = $itemUpdated->orderBy('updated_at', 'desc')->select(Book::$basicFiledsForList)->limit($limit)->get();*/
        eCache::add($cache_key, $itemUpdated, 8640);
        return $itemUpdated;
    }


    public static function getListTagTopMongo($limit = 15, $delcache = false)
    {
        $cache_key = "_list_tag_top_";
        if ($delcache) {
            eCache::del($cache_key);

            return true;
        }
        $listCateSave = eCache::get($cache_key);
        if ($listCateSave) {
            return $listCateSave;
        }
        $posts = Book::getPostMongo();
        $cursor = $posts->raw(function ($collection) {
            return $collection->aggregate([
                ['$unwind' => '$tags'],
                ['$match' => ['status' => 1]],
                ['$group' => [
                    "_id" => '$tags.alias',
                    "name" => ['$first' => '$tags.name'],
                    "alias" => ['$first' => '$tags.alias'],
                    "count" => ['$sum' => 1]
                ]],
                ['$sort' => ['students' => -1]],
                ['$limit' => 15]
            ]);

        });
        $cursor->setTypeMap(['root' => 'array', 'document' => 'array', 'array' => 'array']);
        $listCateSave = $cursor->toArray();
        foreach ($listCateSave as $item) {
            $item['link'] = Cate::buildLink($item['alias'], Cate::OBJECT_IS_LIBRARIES_CODE);
        }
        return $listCateSave;
    }

    static function getPostByAlias($alias)
    {
        $where = [
            'alias' => $alias
        ];
        return Book::where($where)->first();
    }

    public static function getTablePostWP()
    {
        return DB::table('site_posts');
    }

    public static function getTabelPostMetaWp()
    {
        return DB::table('site_postmeta');
    }

    public static function getTabelAuthor()
    {
        return DB::table('io_author');
    }

    public static function getAuthorByAlias($alias)
    {
        return self::getTabelAuthor()->where(['alias' => $alias])->first();
    }


}
