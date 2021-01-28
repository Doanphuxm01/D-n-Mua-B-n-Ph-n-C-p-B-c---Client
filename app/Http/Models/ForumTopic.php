<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Member;
use App\Http\Models\MetaData;

/*
 * CẤU TRÚC forum_topic
 *
 * name -> T comment
 * alias -> id bài viết được comment

 * description -> nội dung comment
 *
 * ----thành phần liên quan
 * departments : //list id của người liên quan
 * projects : //list các dự án liên quan
 *
 * ----thông cập nhật tạo
 * created_by -> người viết comment
 * created_at -> Thời gian tạo
 * updated_at -> Thời gian cập nhật
 * updated_by -> được cập nhật gần nhất bởi ai
 *
 * ----Thống kê
 * view_count -> xem lượt view
 * reply_count -> lượt comment
 * ----trạng thái
 * status -> xoá hay không xoá
 * state -> active hay không
 * */

class ForumTopic extends BaseModel
{
    public $timestamps = false;
    const table_name = 'forum_topic';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];


    static function getByAlias($alias)
    {
        $where = [
            'alias' => $alias
        ];
        return self::where($where)->first();
    }

    /**
     * Build xem chi tiết và các bài viết của topic
     *
     * @param String $topic_id input data từ form
     *
     * @return url
     */
    static function buildLinkTopic($topic_id)
    {
        return admin_link('forum/topic-post/?id=' . strval($topic_id));
    }


    /**
     * Xử lý input của html
     *
     * @param array $obj input data từ form
     *
     * @return array  Cấu trúc ['valid'=> bool , 'msg'=>string, 'data'=>array]
     */
    static function validateTopic($obj)
    {
        $output = [];
        $output['valid'] = false;
        $output['msg'] = '';
        $output['data'] = [];
        /*Các trường cần phải có*/
        if (!isset($obj) || empty($obj)) {
            $output['msg'] = 'Dữ liệu chuyên đề bị trống';
        }
        if (!isset($obj['name'])
            || !is_string($obj['name'])
            || empty($obj['name'])
        ) {
            $output ['msg'] = 'Tên chuyên mục không được để trống';
            return $output;
        }
        $output['data'] ['name'] = $obj['name'];
        if (!isset($obj['description'])
            || !is_string($obj['description'])
            || empty($obj['description'])
        ) {
            $output ['msg'] = 'Phần giới thiệu của chuyên mục không được để trống';
            return $output;
        }
        $output['data'] ['description'] = $obj['description'];

        $output['data']['departments'] = isset($obj['departments']) ? $obj['departments'] : [];

        $output['data']['projects'] = isset($obj['projects']) ? $obj['projects'] : [];

        /*Dữ liệu ôk*/
        $output ['valid'] = true;
        return $output;

    }


    /**
     * Các dữ liệu tự sync sẽ được tạo ở đây
     *
     * @param array $topic Dữ liệu thô đã được validate
     *
     * @return array trả về topic với các dữ liệu mẫu đã được xử lý
     */
    static function computedTopic($topic)
    {
        $topic['alias'] = Helper::convertToAlias($topic['name']);
        if (isset($topic['id'])) {
            $topic ['updated_at'] = Helper::getMongoDate();
            $topic['updated_by'] = [
                'id' => Member::getCurentId(),
                'email' => Member::getCurrentEmail()
            ];

        } else {
            $topic ['created_at'] = Helper::getMongoDate();
            $topic['created_by'] = [
                'id' => Member::getCurentId(),
                'email' => Member::getCurrentEmail()
            ];
        }
        /*Xử lý departments*/
        if (isset($topic['departments'])) {
            $listId = collect($topic['departments'])->map(
                function ($item) {
                    return Helper::getMongoId($item);
                }
            );
            $departments = MetaData::whereIn('_id', $listId)->get();
            $topic['departments'] = collect($departments)->map(
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
        if (isset($topic['projects'])) {
            $listId = collect($topic['projects'])->map(
                function ($item) {
                    return Helper::getMongoId($item);
                }
            );
            $projects = Project::whereIn('_id', $listId)->get();
            $topic['projects'] = collect($projects)->map(
                function ($item) {
                    return [
                        'id' => $item->_id,
                        'name' => $item->name,
                        'alias' => $item->alias
                    ];
                }
            )->toArray();
        }
        return $topic;


    }

    /**
     * Tăng lượng view cho Chuyên đề
     *
     * @param String|String[] $id là id Chuyên đề
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


    public static function buildLinkDelete($id, $router = '')
    {
        return admin_link('forum/delete_topic?id=' . $id . '&token=' . Helper::buildTokenString($id));
    }


}
