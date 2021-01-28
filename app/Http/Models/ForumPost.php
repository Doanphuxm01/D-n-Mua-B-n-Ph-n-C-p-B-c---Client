<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Member;
use App\Http\Models\MetaData;
use App\Http\Models\ForumLog;

/*
 * CẤU TRÚC forum_post
 *
 * name -> tên bài viết
 * alias-> slug bài viết
 *
 * content -> nội dung (lưu trực tiếp hay ánh xạ ? )
 * type -> post/reply/main
 * brief -> giới thiệu ngắn
 * ----Phân loại
 * tags -> thẻ  liên quan
 * departments -> phòng ban liên quan
 * topic_id -> id của topic
 * ----Các thông tin realtime khác
 * last_reply_at Thời gian gần nhất được reply (dành riêng cho bài viết kiểu post)
 * last_reply_by Người gần nhất reply (dành riêng cho bài viết kiểu post)
 *
 * ----các thành phần liên quan
 * staff_id_list -> có thể gắn người vào bài thảo luận được không (?)
 * post_reply_id -> id của nội dung reply //id của post chỉ được gắn với 1 post type cha
 * post_id -> id của nội dung reply //id của post chỉ được gắn với 1 post type cha
 *
 * ----thông cập nhật tạo
 * created_by -> người mở chuyên mục
 * created_at -> Thời gian tạo
 * updated_at -> Thời gian cập nhật
 * updated_by -> được cập nhật gần nhất bởi ai
 * ----Thống kê
 * view_count -> xem lượt view
 * comment_count -> lượt comment
 * rating -> cùng bảng hay bảng phụ
 * like_count -> Đếm số lượt like
 * dislike_count -> đếm só lượt dislike
 * ----trạng thái
 * status -> xoá hay không xoá
 * state -> active hay không
 * */

class ForumPost extends BaseModel
{
    public $timestamps = false;
    const table_name = 'forum_post';
    protected $table = self::table_name;
    static $unguarded = true;

    const type_reply_to_post = "reply_to_post";
    const type_reply_to_comment = "reply_to_post";
    const type_post = "post";


    static function getPostByAlias($alias)
    {
        $where = [
            'alias' => $alias
        ];
        return self::where($where)->first();
    }

    public static function buildLinkDelete($id, $router = '')
    {
        return admin_link('forum/delete_post?id=' . $id . '&token=' . Helper::buildTokenString($id));
    }


    /*Xử lý input của html*/
    static function validate($obj)
    {
        $output = [];
        $output['valid'] = false;
        $output['msg'] = '';
        $output['data'] = [];
        /*Các trường cần phải có*/
        if (!isset($obj) || empty($obj)) {
            $output['msg'] = 'Dữ liệu chuyên đề bị trống';
        }

        $obj['type'] = isset($obj['type']) ? $obj['type'] : 'post';
        if ($obj['type'] === 'reply_to_post') {
            if (!isset($obj['post_id']) || !Helper::isMongoId($obj['post_id'])) {
                $output['msg'] = 'Thiếu post_id hoặc post_id không đúng';
                return $output;
            }

        } else if ($obj['type'] === 'reply_to_comment') {
            if (!isset($obj['post_reply_id']) || !Helper::isMongoId($obj['post_reply_id'])) {
                $output['msg'] = 'Thiếu post_id hoặc post_id không đúng';
                return $output;
            }

        } else if (!isset($obj['name']) || !is_string($obj['name']) || empty($obj['name'])) {
            $output ['msg'] = 'Tên bài viết thảo luận không được để trống không được để trống';
            return $output;
        }
        $output['data']['type'] = $obj['type'];
        $output['data'] ['name'] = isset($obj['name']) ? $obj['name'] : '';

        if (!isset($obj['content']) || !is_string($obj['content']) || empty($obj['content'])) {
            $output ['msg'] = 'Nội dung cuộc thảo luận không được để trống';
            return $output;
        }

        $output['data'] ['content'] = $obj['content'];


        $output['data'] ['description'] = isset($obj['description']) ? $obj['description'] : '';

        if (!isset($obj['topic_id']) || !Helper::isMongoId($obj['topic_id'])) {
            $output ['msg'] = 'Bạn cần chọn chuyên mục';
            return $output;
        }


        $output['data']['post_id'] = isset($obj['post_id']) ? $obj['post_id'] : '';
        $output['data']['topic_id'] = isset($obj['topic_id']) ? $obj['topic_id'] : '';
        $output['data']['post_reply_id'] = isset($obj['post_reply_id']) ? $obj['post_reply_id'] : '';

        $output['data']['topics'] = [$obj['topic_id']];

        $output['data']['departments'] = isset($obj['departments']) ? $obj['departments'] : [];
        $output['data']['status'] = isset($obj['status']) ? $obj['status'] : self::STATUS_ACTIVE;

        $output['data']['projects'] = isset($obj['projects']) ? $obj['projects'] : [];

        $output['data']['files'] = isset($obj['files']) ? $obj['files'] : [];

        /*Dữ liệu ôk*/
        $output ['valid'] = true;
        return $output;

    }

    /*Các dữ liệu tự sync sẽ được tạo ở đây*/
    static function computedBeforeUpdate($obj)
    {
        $obj['alias'] = Helper::convertToAlias($obj['name']);
        if (isset($obj['_id']) || isset($obj['id'])) {
            $obj ['updated_at'] = Helper::getMongoDate();
            $obj['updated_by'] = [
                'id' => Member::getCurentId(),
                'email' => Member::getCurrentEmail()
            ];

        } else {
            $obj ['updated_at'] = Helper::getMongoDate();
            $obj['updated_by'] = [
                'id' => Member::getCurentId(),
                'email' => Member::getCurrentEmail()
            ];
            $obj ['created_at'] = Helper::getMongoDate();
            $obj['created_by'] = [
                'id' => Member::getCurentId(),
                'email' => Member::getCurrentEmail()
            ];
        }
        /*Xử lý departments*/
        if (isset($obj['departments'])) {
            $listId = collect($obj['departments'])->map(
                function ($item) {
                    return Helper::getMongoId($item);
                }
            );
            $departments = MetaData::whereIn('_id', $listId)->get();
            $obj['departments'] = collect($departments)->map(
                function ($item) {
                    return [
                        'id' => $item->_id,
                        'name' => $item->name,
                        'alias' => $item->alias
                    ];
                }
            )->toArray();

        }
        /*Xử lý projects*/
        if (isset($obj['projects'])) {
            $listId = collect($obj['projects'])->map(
                function ($item) {
                    return Helper::getMongoId($item);
                }
            );
            //Debug::show($listId);
            $projects = Project::whereIn('_id', $listId)->get();
            $obj['projects'] = collect($projects)->map(
                function ($item) {
                    return [
                        'id' => $item->_id,
                        'name' => $item->name,
                        'alias' => $item->alias
                    ];
                }
            )->toArray();
        }

        /*Xử lý topic*/
        if (isset($obj['topics'])) {
            $listId = collect($obj['topics'])->map(
                function ($item) {
                    return Helper::getMongoId($item);
                }
            );
            $topics = ForumTopic::whereIn('_id', $listId)->get();
            $obj['topics'] = collect($topics)->map(
                function ($item) {
                    return [
                        'id' => $item->_id,
                        'name' => $item->name,
                        'alias' => $item->alias
                    ];
                }
            )->toArray();
        }

        return $obj;
    }

    /**
     * Tăng lượng view cho Bài viết
     *
     * @param String|String[] $id là id Bài viết
     *
     * @return int lượng view
     */
    static function isViewed($id)
    {
        $currentObj = self::find($id);
        $count = null;
        if ($currentObj) {
            if ($currentObj->view_count) {
                $count = $currentObj->increment('view_count');
            } else {
                $count = 1;
                $currentObj->view_count = 1;
                $currentObj->save();
            }
        }
        return $count;
    }


    /**
     * Lấy các bài viết dựa theo id của topic
     *
     * @param String|String[] $topic_id là id topic
     * @param Query $query là query muốn viết
     *                                  chèn thêm vào
     *
     * @return Illuminate\Database\Eloquent\Collection chứa của các bài viết trong topic
     */
    static function getPostByTopicId($topic_id, $query = null)
    {
        if (!isset($query) || !$query) {
            if (is_string($topic_id)) {
                return self::where('topic_id', $topic_id);
            }
            if (is_array($topic_id)) {
                return self::whereIn('topic_id', $topic_id);
            }
        } else {
            if (is_string($topic_id)) {
                return $query->where('topic_id', $topic_id);
            }
            if (is_array($topic_id)) {
                return $query->whereIn('topic_id', $topic_id);
            }
        }
    }

    static function countPostByTopicId($topic_id, $query = null)
    {
        /*todo: cần tối ưu query*/
        $currentQuery = null;
        if (!isset($query) || !$query) {
            if (is_string($topic_id)) {
                $currentQuery = self::where('topic_id', $topic_id);
            }
            if (is_array($topic_id)) {
                $currentQuery = self::whereIn('topic_id', $topic_id);
            }
        } else {
            if (is_string($topic_id)) {
                $currentQuery = $query->where('topic_id', $topic_id);
            }
            if (is_array($topic_id)) {
                $currentQuery = $query->whereIn('topic_id', $topic_id);
            }
        }
        return $currentQuery->count();
    }

    static function countPostReplyToPost($post_id_list)
    {

        $cursor = self::raw(
            function ($collection) use ($post_id_list) {

                return $collection->aggregate(
                    [
                        ['$match' => ['post_id' => ['$in' => $post_id_list]]],
                        ['$group' => ['_id' => '$post_id', 'count' => ['$sum' => 1]]]
                    ]
                );

            }
        );
        return $cursor;

    }

    /**
     * Lấy các bài viết dựa theo id của topic
     *
     * @param String|String[] $topic_id là id topic
     * @param Query $query là query muốn viết
     *                                  chèn thêm vào
     *
     * @return Illuminate\Database\Eloquent\Collection chứa của các bài viết trong topic
     */
    static function buildLinkDetailPost($id)
    {
        return admin_link('forum/detail-post/?id=' . strval($id));
    }


    static function buildLinkInputPost($id)
    {
        return admin_link('forum/input-post/?id=' . strval($id));
    }


    /**
     * Lấy các bài viết gần nhất mới được cập nhật
     *
     * @param String|String[] $listTopicId là danh sách của các topic muốn tìm kiếm nếu để trống sẽ tìm kiếm toàn bộ
     *
     * @return Illuminate\Database\Eloquent\Collection chứa của các bài viết trong topic
     */
    static function getRecentPost($listTopicId = [])
    {
        if (empty($listTopicId)) {
            return self::where(
                [
                    'topic_id' => ['$in' => $listTopicId],
                    'status' => self::STATUS_ACTIVE,
                ]
            )->orderBy('last_reply_at', 'desc')->get();
        } else {
            return self::where(
                [
                    'status' => self::STATUS_ACTIVE
                ]
            )->orderBy('last_reply_at', 'desc')->get();
        }
    }


    /**
     * Hàm dùng để kiểm tra người dùng đã like chưa và cập nhật
     *
     * @param string $post_id id của bài viết
     *
     * @return number số lượt like mới của bài viết=
     */
    static function togglePostLike($post_id)
    {
        $like = ForumLog::checkLikePost($post_id);


        if ($like && $like['is_active']) {
            ForumLog::doUnLike($post_id);
            $currentPost = ForumPost::where('_id', $post_id)->first();
            if (isset($currentPost['like_count'])) {
                $currentPost['like_count'] = $currentPost['like_count'] - 1;
            } else {
                $currentPost['like_count'] = 0;
            }
            $currentPost->update();


        }
        if (!$like || !$like['is_active']) {
            ForumLog::doLike($post_id);
            $currentPost = ForumPost::where('_id', $post_id)->first();
            if (isset($currentPost['like_count'])) {
                $currentPost['like_count'] = $currentPost['like_count'] + 1;
            } else {
                $currentPost['like_count'] = 1;
            }

            $currentPost->update();

        }

        return ForumPost::where('_id', $post_id)->first()->like_count;

    }

}
