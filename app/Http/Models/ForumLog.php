<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class ForumLog extends BaseModel
{
    public $timestamps = false;
    const table_name = 'forum_logs';
    protected $table = self::table_name;
    static $unguarded = true;


    const TYPE_LIKE = 'like';

    const TARGET_POST_FORUM = 'TARGET_POST_FORUM ';

    //Kiểm tra xem người đang đăng nhập đã like hay chưa
    static function checkLikePost($post_id)
    {


        $like = self::where(
            [
                'status' => self::STATUS_ACTIVE,
                "created_by.id" => Member::getCurentId(),
                "post_id" => $post_id,
                "type" => self::TYPE_LIKE,
                "target" => self::TARGET_POST_FORUM,
            ]
        )->first();

        return $like;
    }

    /**
     * Hàm dùng để thực hiện việc like một bài viết
     *
     * @param String $post_id là id bài viết
     *
     * @return String id của bài viết được like
     */
    static function doLike($post_id)
    {
        $member = Member::getCurent();

        $like = self::checkLikePost($post_id);
        if (!$like) {
            self::insertGetId(
                [
                    'status' => self::STATUS_ACTIVE,
                    "post_id" => $post_id,
                    "type" => self::TYPE_LIKE,
                    "target" => self::TARGET_POST_FORUM,
                    "created_at" => Helper::getMongoDateTime(),
                    "is_active" => true,
                    "created_by" => [
                        'account' => $member['account'],
                        'id' => $member['_id'],
                        'email' => $member['email'],
                    ]
                ]
            );
        } else {
            $like['is_active'] = true;
            $like['updated_at'] = Helper::getMongoDate();
            $like ['updated_by'] = [
                'account' => $member['account'],
                'id' => $member['_id'],
                'email' => $member['email'],
            ];
            $like->update();

        }


        return $like['_id'];
    }

    /**
     * Hàm dùng để unlike bài viết
     *
     * @param string $post_id id của bài viết
     *
     * @return string $id của log like đã update
     */
    static function doUnLike($post_id)
    {
        $member = Member::getCurent();
        $like = self::where(
            [
                'status' => self::STATUS_ACTIVE,
                "created_by.id" => $member['_id'],
                "post_id" => $post_id,
                "type" => self::TYPE_LIKE,
                "target" => self::TARGET_POST_FORUM,
                "is_active" => true
            ]
        )->first();
        $like['is_active'] = false;
        $like['updated_at'] = Helper::getMongoDate();
        $like ['updated_by'] = [
            'account' => $member['account'],
            'id' => $member['_id'],
            'email' => $member['email'],
        ];
        $like->update();
        return $like['_id'];


    }
}
