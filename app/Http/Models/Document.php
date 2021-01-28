<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Document extends BaseModel
{
    public $timestamps = FALSE;
    const table_name        = 'documents';//bảng văn bản
    const table_doc_type    = 'doc_type';//bảng loại văn bản
    const table_doc_company = 'doc_company';//bảng cơ quan ban hành
    const table_doc_comment = 'doc_comment';//bảng ý kiến văn bản
    protected $table              = self::table_name;
    static    $unguarded          = TRUE;
    static    $basicFiledsForList = '*';
    protected $dates              = [];


    const TYPE_FROM = 'from';
    const TYPE_TO   = 'to';
    const TYPE_IN   = 'in';

    const DOC_FROM = 'van-ban-den'; //van ban den
    const DOC_TO   = 'van-ban-di'; //van ban di
    const DOC_PRO   = 'van-ban-kiem-soat-san-pham'; //van ban kiem soat san pham

    const DOCUMENT_TYPE = [
        self::DOC_FROM => [
            'key'   => 'van-ban-den',
            'name' => "Văn bản đến",
            'style' => "success",
        ],

        self::DOC_TO => [
            'key'   => 'van-ban-di',
            'name' => "Văn bản đi",
            'style' => "danger",

        ],
        self::DOC_PRO => [
            'key'   => 'van-ban-kiem-soat-san-pham',
            'name' => "Văn bản kiểm soát sản phẩm",
            'style' => "info",

        ],

    ];

    const GOOD   = 'dat';
    const GOOD_COMMENT   = 'dat-co-y-kien';
    const NOT_GOOD   = 'khong-dat';
    const PRODUCT_QUALITY = [
        self::NOT_GOOD => [
            'key'   => 'khong-dat',
            'label' => "Không đạt",
            'style' => "danger",
        ],

        self::GOOD => [
            'key'   => 'dat',
            'label' => "Đạt",
            'style' => "success",

        ],
        self::GOOD_COMMENT => [
            'key'   => 'dat-co-y-kien',
            'label' => "Đạt có ý kiến",
            'style' => "info",
        ],
    ];

    const TYPE_COMPANY_FROM = 'co-quan-ban-hanh-van-ban-den';
    const TYPE_COMPANY_TO = 'co-quan-ban-hanh-van-ban-di';
    const TYPE_COMPANY_IN = 'co-quan-ban-hanh-van-ban-noi-bo';
    const DOC_COMPANY = [
        self::TYPE_COMPANY_FROM => [
            'key'   => 'co-quan-ban-hanh-van-ban-den',
            'label' => "Cơ quan ban hành văn bản đến",
        ],

        self::TYPE_COMPANY_TO => [
            'key'   => 'co-quan-ban-hanh-van-ban-di',
            'label' => "Cơ quan ban hành văn bản đi",
        ],

        self::TYPE_COMPANY_IN => [
            'key'   => 'co-quan-ban-hanh-van-ban-noi-bo',
            'label' => "Cơ quan ban hành văn bản nội bộ",
        ],

    ];

    const TYPE_FORM_COMMENT        = 'form-comment';//xin ý kiến phân phối văn bản
    const TYPE_FORM_COMMENT_LEADER = 'form-comment-leader';//Ý kiến lãnh đạo
    const TYPE_FORM_COMMENT_OFFICE = 'form-comment-office';//Ý kiến Bộ phận Quản lý Văn phòng
    const TYPE_FORM_COMMENT_CENTER = 'form-comment-center';//Ý kiến Bộ phận Quản lý Trung tâm


    const DOC_SECRET_NORMAL    = "thuong";//Ý kiến Bộ phận Quản lý Trung tâm
    const DOC_SECRET_HIGHT     = "khan";//Ý kiến Bộ phận Quản lý Trung tâm
    const DOC_EMERGENCY_NORMAL = "thuong";//Ý kiến Bộ phận Quản lý Trung tâm
    const DOC_EMERGENCY_HIGHT  = "khan";//Ý kiến Bộ phận Quản lý Trung tâm


    static function getByAlias($alias)
    {
        $where = [
            'alias' => $alias,
        ];

        //Helper::convertToAlias('Cơ quan ban hành văn bản đến');
        return self::where($where)->first();
    }

    const STATUS_PROCESS_OK = 'processed';// đã hoàn thành
    const STATUS_NO_PROCESS = 'no_process'; //chưa xử lý
    const STATUS_PROCESSING = 'process'; //đang xử lý
    const STATUS_CANCEL     = 'cancel'; //hủy

    /***
     * @param bool|FALSE $selected
     * @return array
     * @note: Định nghĩa và Lấy danh sách các trạng thái của văn bản trong bảng
     */
    static function getListStatus($selected = FALSE)
    {
        $listStatus = [
            self::STATUS_NO_PROCESS => ['id' => self::STATUS_NO_PROCESS, 'style' => 'warning', 'text' => 'Chưa xử lý ', 'text-action' => 'Chưa xử lý'],
            self::STATUS_PROCESSING => ['id' => self::STATUS_PROCESSING, 'style' => 'info', 'text' => 'Đang xử lý ', 'text-action' => 'Đang xử lý'],
            self::STATUS_PROCESS_OK => ['id' => self::STATUS_PROCESS_OK, 'style' => 'success', 'text' => 'Hoàn thành ', 'text-action' => 'Hoàn thành'],
            self::STATUS_CANCEL     => ['id' => self::STATUS_CANCEL, 'style' => 'danger', 'text' => 'Hủy', 'text-action' => 'Hủy'],
        ];
        if ($selected && isset($listStatus[$selected])) {
            $listStatus[$selected]['checked'] = 'checked';
        }

        return $listStatus;
    }

    /**
     * @param $selected
     * @return array|mixed
     * @note: dùng trong các view tránh if els quá nhiều
     */
    static function getStatus($selected, $case = false)
    {

        $list = self::getListStatus($selected);

        if (isset($list[$selected])) {
            return $list[$selected];
        } else {
            return [
                'id'          => 0,
                'style'       => 'default',
                'text'        => 'Không xác định',
                'text-action' => 'Không xác định',
            ];
        }
    }

    static function getByDevice($alias)
    {
        $where = [
            'device_key' => $alias,
        ];

        return self::where($where)->first();
    }

    public static function getTableChapter()
    {
        return DB::table('doc_type');
    }

    public static function getTableCompany()
    {
        return DB::table('doc_company');
    }

    public static function getTableDocComment()
    {
        return DB::table('doc_comment');
    }

    /**
     * @param $object
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    static function buildLinkDelete($object, $router = '')
    {
        return admin_link('document/_delete?id=' . $object->_id . '&token=' . Helper::buildTokenString($object->_id));
    } /**
 * @param $object
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
    static function buildLinkEdit($object)
    {
        return admin_link('document/input?id=' . $object['_id']);
    }

    /**
     * @param $listProjectId
     * @param int $limit
     * @param string $type
     * @note: Lấy danh sách tài liệu theo dự án (tài liệu mới)
     */
    static function getDocumentByProject($listProjectId, $limit = 10, $type = "all")
    {
        if($type=='all'){
            return self::whereIn('project_id',$listProjectId)->limit($limit)->orderBy('updated_at','DESC')->select()->get();
        }

    }


}
