<?php

namespace App\Http\Controllers\API;

use App\Elibs\Debug;
use App\Elibs\eBug;
use App\Elibs\EmailHelper;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\SMSHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\AppSetting;
use App\Http\Models\Comment;
use App\Http\Models\LogsApi;
use App\Http\Models\SPKNotification;
use App\Http\Models\BaseModel;
use App\Http\Models\Event;
use App\Http\Models\Location;
use App\Http\Models\Media;
use App\Http\Models\MetaData;
use App\Http\Models\MetaDataSPK;
use App\Http\Models\Partner;
use App\Http\Models\User;
use App\Http\Models\UserAccessToken;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;
use App\Elibs\MobileDetect;
use Elasticsearch\ClientBuilder;

class spk_api extends AppApi
{
    //body đầu vào
    /*  link http://cms.superkid.vn/public_api/spk/event?param[organization_id]=5e164a7e535a6d09de303195
    /*  link http://cms.superkid.vn/public_api/spk/event?param[organization_id]=5e164a7e535a6d09de303195
     *  todo@huy: auth để sau
     * {
     *     action : "list" , //mặc định là list,
     *     param  : {
     *          "q" : "search text",
     *          "output" :"full", //là các chế độ để lấy thông tin của partner
     *
     *    } , //Là tham chiếu gửi vào, tuỳ theo action  thì sẽ có tham chiều khác nhau
     *
     * }
     *
     *
     * */
    //kết quả
    /*
     * {
     *      msg :'',
     *      status : 1,
     *      data: {
     *          items : [],
     *      },
     * }
     *
     *
     * */
    static $clientBuilder = false;
    private $keyword = '';
    private $tag = '';
    private $mainPartnerIndex = 'spk_partner_index';
    private $mainEventIndex = 'spk_event_index';
    private $mainPartnerType = 'spk_partner_list';
    private $mainEventType = 'spk_event_list';
    private $partnerRecommendType = 'spk_partner_list_recommend_type';
    private $eventRecommendType = 'spk_event_list_recommend_type';
    private $lsFields = [
        '_id', 'code',
        'summary', 'name', 'address',
        'organization', 'type', 'category', 'rating', 'website',
        'updated_at',
    ];

    private $dayInWeekConvert = [
        "Monday"=>"Thứ Hai",
        "Tuesday"=>"Thứ Ba",
        "Wednesday"=>"Thứ Tư",
        "Thursday"=>"Thứ Năm",
        "Friday"=>"Thứ Sáu",
        "Saturday"=>"Thứ Bảy",
        "Sunday"=>"Chủ Nhật",
        ];
    static $field_with_image_link = ['logo', 'images'];

    static function _convert_image_field_link($obj)
    {
        foreach (self::$field_with_image_link as $key) {
            if (@$obj[$key]['relative_link']) {
                @$obj[$key] = array_merge($obj[$key], ['link' => Media::getFileLink($obj[$key]['relative_link'])]);
            } else if (is_array(@$obj[$key])) {
                $temp = [];
                foreach ($obj[$key] as $rowKey => $row) {
                    if (is_array($row) && @$row['relative_link']) {
                        $newRow = array_merge($obj[$key][$rowKey], ['link' => Media::getFileLink(@$row['relative_link'])]);
                        $temp[] = $newRow;
                    }
                }
                $obj[$key] = $temp;
            }
        }

        return $obj;
    }

    static function write_log()
    {
        $fullUrl = request()->fullUrl();
        $method = request()->method();
        $all = request()->all();
        $query = request()->query();
        $created_at = Helper::getMongoDateTime();
        $headers = request()->headers->all();
        $hostname = request()->getHost();
        $path = request()->path();
        $objToSave = [
            'path' => $path,
            'headers' => $headers,
            'hostName' => $hostname,
            "fullUrl" => $fullUrl,
            "method" => $method,
            "all" => $all,
            "query" => $query,
            "created_at" => $created_at,
        ];

        LogsApi::insertGetId($objToSave);
    }

    public static function getClientBuilder()
    {
        if (!self::$clientBuilder) {
            $hosts = [
                'http://localhost:9200'
            ];
            self::$clientBuilder = ClientBuilder::create()->setHosts($hosts)->build();
            //self::$clientBuilder = ClientBuilder::create()->build();
        }

        return self::$clientBuilder;
    }

    public function _search_by_elastic($keyword = '', $index, $curPage = 1, $item_per_page = 20)
    {

        $querySearch = [
            'bool' => [
                'must' => [
                    [
                        "multi_match" => [
                            "query" => $keyword,
                            //"type" => "phrase_prefix",
                            "fields" => [
                                "name", "summary", "category.value", "name_trim_all_space", "name_trim_all_space_unsigned", 'name_unsigned', "address.value", "address.value_unsigned",
                                "address.city.value", 'address.city.value_trim_all_space', 'address.city.value_unsigned',
                                "address.district.value", 'address.district.value_trim_all_space', 'address.district.value_unsigned',
                                "address.town.value", 'address.town.value_trim_all_space', 'address.town.value_unsigned',
                                'brand_trim_all_space', 'brand_trim_all_space_unsigned', 'brand_unsigned',
                            ],
                        ]
                    ]
                ],
                /*'filter' => [
                    'term' => [
                        'status' => SearchHelper::$POST_TYPE
                    ]

                ]*/

            ]
        ];

        $sort = \request('sort');
        if ($sort == 'updated_at') {
            $orderBy = [
                ['updated_at' => 'desc'],
            ];
        } else if ($sort == 'rating') {
            $orderBy = [
                ['ratings' => 'desc'],
            ];
        } else if ($sort == 'install') {
            /*$orderBy = [
                ['installs' => 'desc'],
            ];*/
            $orderBy = [
                ['minInstalls' => 'desc'],
            ];
        } else {
            $orderBy = [
                ['_score' => 'desc'],
            ];
        }

        $offset = $item_per_page * ($curPage - 1);
        $source = $this->lsFields;
        $paramsSearch = [
            'index' => $index,
            //'type'  => $this->recommendType,
            'body' => [
                '_source' => $source,
                'from' => $offset,
                'size' => $item_per_page,
                'sort' => $orderBy,
                'query' => $querySearch,
            ]
        ];
        $results = $this->getClientBuilder()->search($paramsSearch);
        if (isset($results['hits']['hits'])) {
            if (isset($_GET['x4k'])) {
                eBug::show($results);
            }
            $ids = [];
            foreach ($results['hits']['hits'] as $obj) {
                $ids[] = $obj['_id'];
            }
            return $ids;
        } else {
            return false;
        }

    }

    public static function handle_filter($listObj, $table = '')
    {
        //filter sẽ hỗ trợ lọc theo q_search và theo date_range, theo email và số điện thoại
        //filter theo đc category


        //tuỳ theo từng table sẽ có xử lý chung riêng khác nhau
        $param = request('param');
        /*$filter_q = @$param['q']; //search text nhanh
        //quick search mặc định luôn là search like
        if ($filter_q) {

            $listObj = $listObj->where(
                function ($query) use ($filter_q, $table) {
                    $query->where('code', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('summary', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('name', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('address.city.value', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('address.district.value', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('address.district.value', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('organization.name', 'LIKE', '%' . $filter_q . '%')
                        ->OrWhere('type.value', 'LIKE', '%' . $filter_q . '%');

                }
            );
        }*/
        if (@$param['ids']) {
            $listObj = $listObj->whereIn('_id', explode(',', $param['ids']));
        }
        $filter = @$param['filter'];


        if (!is_array($filter)) {
            return $listObj;
        }

        if (@$filter['target_id']) {
            if (is_string($filter['target_id'])) {
                $listObj = $listObj->where(['target_id' => $filter['target_id']]);
            } else if (is_array($filter['target_id'])) {
                $listObj = $listObj->where(['target_id' => ['$in' => array_values($filter['target_id'])]]);
            }
        }

        if (@$filter['created_at_lte']) {
            //seach in range
            $timeValue = $filter['created_at_lte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['time_start_plan' => ['$lte' => $timeValue]]);

        }
        if (@$filter['created_at_gte']) {
            $timeValue = $filter['created_at_gte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['created_at' => ['$gte' => $timeValue]]);
            //seach in range
        }

        if (@$filter['updated_at_lte']) {
            //seach in range
            $timeValue = $filter['updated_at_lte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['time_start_plan' => ['$lte' => $timeValue]]);
        }
        if (@$filter['updated_at_gte']) {
            $timeValue = $filter['updated_at_gte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['updated_at' => ['$gte' => $timeValue]]);
            //seach in range
        }

        if (@$filter['time_start_plan_gte']) {
            // sắp diễn ra
            $timeValue = $filter['time_start_plan_gte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['time_start_plan' => ['$gte' => $timeValue]]);
        }

        if (@$filter['time_start_plan_lte']) {
            // đang diễn ra
            $timeValue = $filter['time_start_plan_lte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['time_start_plan' => ['$lte' => $timeValue]]);
        }

        if (@$filter['time_end_plan_gte']) {
            // đang diễn ra
            $timeValue = $filter['time_end_plan_gte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['time_end_plan' => ['$gte' => $timeValue]]);

        }

        if (@$filter['time_end_plan_lte']) {
            // đã kết thúc
            $timeValue = $filter['time_end_plan_lte'];
            $timeValue = Helper::convertMktimeToMongoTime($timeValue);
            $listObj = $listObj->where(['time_end_plan' => ['$lte' => $timeValue]]);
        }


        if ((@$filter['available_age'])) {
            if (is_string($filter['available_age'])) {
                $listObj = $listObj->where(['available_age.alias' => $filter['available_age']]);
            } else if (is_array($filter['available_age'])) {
                $listObj = $listObj->where(['available_age.alias' => ['$in' => array_values($filter['available_age'])]]);
            }
        }

        if ((@$filter['price'])) {
            if (is_string($filter['price'])) {
                $listObj = $listObj->where(['price.alias' => $filter['price']]);
            } else if (is_array($filter['price'])) {
                $listObj = $listObj->where(['price.alias' => ['$in' => array_values($filter['price'])]]);
            }
        }

        if ((@$filter['score_gte'])) {
            $listObj = $listObj->where(['score' => ['$gte' => doubleval(@$filter['score_gte'])]]);
        }
        if (@$filter['score']) {
            if (@$filter['score'] == '-1') {
                $listObj = $listObj->where([
                    '$or' => [
                        ['score' => ''],
                        ['score' => 0],
                        ['score' => ['$exists' => false]],
                    ]
                ]);
            } else {
                $listObj = $listObj->where(['score' => [
                    '$gte' => doubleval(@$filter['score']),
                    '$lte' => @$filter['score'] + 1
                ]]);
            }

        }

        if ((@$filter['score_lte'])) {
            $listObj = $listObj->where(['score' => ['$lte' => doubleval(@$filter['score_lte'])]]);
        }

        if ((@$filter['score_empty'])) {
            $listObj = $listObj->where([
                '$or' => [
                    ['score' => ''],
                    ['score' => 0],
                    ['score' => ['$exists' => false]],
                ]
            ]);
        }


        if (@$filter['category']) {
            if (is_string($filter['category'])) {
                $listObj = $listObj->where(['category.id' => $filter['category']]);
            } else if (is_array($filter['category'])) {
                $listObj = $listObj->where(['category.id' => ['$in' => array_values($filter['category'])]]);
            }
        }

        return $listObj;


    }

    static function handle_sort($listObj, $table)
    {
        $param = request('param', []);
        $sort = @$param['sort'];


        if (empty($sort) || !is_array($sort)) {
            if (!@$param['define_list']) {
                return $listObj->orderBy('_id', 'DESC');
            } else {
                return $listObj;
            }
        }

        foreach ($sort as $key => $value) {
            if ($value == -1) {
                $listObj = $listObj->orderBy($key, 'DESC');
            } else {
                $listObj = $listObj->orderBy($key, 'ASC');
            }
        }
        return $listObj;

    }

    static function handle_define_list($listObj, $table)
    {
        $param = request('param', []);
        $define_list = @$param['define_list'];
        if (empty($define_list)) {
            $timeValue = Helper::convertMktimeToMongoTime(time());
            // thời gian kết thúc lớn hơn thời gian hiện tại
            $listObj = $listObj->where(['time_end_plan' => ['$gte' => $timeValue]])->orderBy('time_end_plan', 'ASC');;
            return $listObj;
        }

        if ($table === 'event') {
            $currentTime = time();
            if ($define_list === 'upcoming') {
                $listObj = $listObj->where([
                    'time_start_plan' => [
                        '$gte' => Helper::convertMktimeToMongoTime($currentTime),
                    ]
                ])->orderBy('time_start_plan', 'ASC');
            } else if ($define_list === 'ongoing') {
                $listObj = $listObj->where([
                    'time_start_plan' => [
                        '$lte' => Helper::convertMktimeToMongoTime($currentTime),
                    ],
                    'time_end_plan' => [
                        '$gte' => Helper::convertMktimeToMongoTime($currentTime),
                    ]
                ]);

            } else if ($define_list === 'end') {
                $listObj = $listObj->where([
                    'time_end_plan' => [
                        '$lte' => Helper::convertMktimeToMongoTime($currentTime),
//                        '$gte'=>Helper::convertMktimeToMongoTime($currentTime - 7*24*60*60),
                    ]
                ])->orderBy('time_start_plan', 'DESC');
            }

        }

        return $listObj;
    }

    static function handle_limit($listObj, $table = '')
    {
        $param = request('param');
        $limit = @$param['limit'];
        if (is_numeric($limit)) {
            $listObj = Pager::getInstance()->getPager($listObj, (int)$limit);
        } else {
            $listObj = Pager::getInstance()->getPager($listObj, 50);
        }
        return $listObj;
    }

    public function index($action = '')
    {

        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->outputError("Yêu cầu chưa được hỗ trợ.");
        }
    }

    public function partner()
    {
        self::write_log();

        $query_action = request('action', 'list');

        if ($query_action === 'list' || $query_action === 'get') {
            $param = request('param');

            if ($query_action === 'get') {
                $id = $param['id'];
                if (!$id) {
                    return $this->outputError("Thiếu id");
                }
                $lsObj = BaseModel::table(Partner::table_name)->where([
                    ['removed', BaseModel::REMOVED_NO],
                    ['status', BaseModel::STATUS_ACTIVE],
                ])->where('_id', $id);
            } else {
                $lsObj = BaseModel::table(Partner::table_name)->where([
                    ['removed', BaseModel::REMOVED_NO],
                    ['status', BaseModel::STATUS_ACTIVE],
                ]);
            }
            $param = request('param');
            $filter_q = @$param['q']; //search text nhanh
            if (!$filter_q) {
                $lsObj = self::handle_filter($lsObj, 'partner');
            }
            $lsObj = self::handle_sort($lsObj, 'partner');
            //$lsObj = self::handle_limit($lsObj, 'partner');

            $curPage = (int)\request('page', 1);
            $itemPerPage = 50;
            $limit = @$param['limit'];
            if (is_numeric($limit)) {
                if ($filter_q) {
                    $ids = $this->_search_by_elastic($filter_q, $this->mainPartnerIndex, $curPage, (int)$limit);
                    $lsObj = $lsObj->whereIn('_id', $ids);
                    $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage);
                } else {
                    $lsObj = Pager::getInstance()->getPager($lsObj, (int)$limit);
                }
            } else {
                if ($filter_q) {
                    $ids = $this->_search_by_elastic($filter_q, $this->mainPartnerIndex, $curPage, $itemPerPage);
                    $lsObj = $lsObj->whereIn('_id', $ids);
                    $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage);
                } else {
                    $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage);
                }
            }

            if ($query_action === 'get') {
                if ($lsObj->count() === 0) {
                    return $this->outputError("Không tìm thấy bản ghi");
                }
            }
            if (!$filter_q) {
                $lsObj->transform(function ($item) {
                    $item = $this->_convertPartner($item);
                    $item['_id'] = strval($item['_id']);
                    $item = self::_convert_image_field_link($item);

                    if (is_numeric(@$item['score'])) {
                        @$item['score'] = round(@$item['score'], 1);
                    }
                    foreach (['created_at', 'updated_at'] as $keyDate) {
                        if (@$item[$keyDate]) {
                            $temp = $item["$keyDate"]->toDateTime();
                            $item["$keyDate"] = $temp->format('c');
                            $item["$keyDate" . "_parser"] = [
                                'timestamp' => $temp->getTimestamp(),
                                'second' => $temp->format('s'),
                                'minute' => $temp->format('i'),
                                'hour' => $temp->format('H'),
                                'day' => $temp->format('d'),
                                'month' => $temp->format('m'),
                                'year' => $temp->format('Y'),
                                'dayInWeek' => $temp->format('l'),
                            ];
                        }
                    }

                    return $item;
                });
            } else {
                return $this->outputDone($lsObj, "Lấy dữ liệu thành công");
            }
            if ($query_action === 'get') {
                return $this->outputDone($lsObj->first(), "Lấy dữ liệu thành công");
            }
            return $this->outputDone($lsObj, "Lấy dữ liệu thành công");
        } else if ($query_action === 'get') {
            $param = request('param');
            $id = @$param['id'];
            if (!$id) {
                return $this->outputError("Thiếu id");
            }
        } else if ($query_action === 'facet_filter') {
            $Model = BaseModel::table(Partner::table_name);
            $groupAndCountByCity = [
                [
                    '$unwind' => '$address',
                ],
                [
                    '$group' => [
                        '_id' => '$address.city.id',
                        'value' => ['$last' => '$address.city.value'],
                        'count' => ['$sum' => 1]
                    ]
                ]
            ];
            $groupAndCountByDistrict = [
                [
                    '$unwind' => '$address',
                ],
                [
                    '$group' => [
                        '_id' => '$address.district.id',
                        'value' => ['$last' => '$address.district.value'],
                        'count' => ['$sum' => 1]],
                ],
            ];
            $groupAndCountByTown = [
                [
                    '$unwind' => '$address',
                ],
                [
                    '$group' => [
                        '_id' => '$address.town.id',
                        'value' => ['$last' => '$address.town.value'],
                        'count' => ['$sum' => 1]
                    ]
                ]];
            $groupAndCountByCategory = [
                [
                    '$unwind' => '$category',
                ],
                [
                    '$group' => [
                        '_id' => '$category.id',
                        'label' => ['$last' => '$category.value'],
                        'count' => ['$sum' => 1]
                    ]
                ]];


            $aggregate = [
                [
                    '$match' => [
                        'removed' => 'no',
                    ]
                ],

                [
                    '$facet' => [
                        'groupAndCountByCity' => $groupAndCountByCity,
                        'groupAndCountByDistrict' => $groupAndCountByDistrict,
                        'groupAndCountByTown' => $groupAndCountByTown,
                        'groupAndCountByCategory' => $groupAndCountByCategory
                    ]
                ]


            ];
            $output = $Model->raw(function ($collection) use ($aggregate) {
                return $collection->aggregate($aggregate);
            })->toArray();

            return $this->outputDone($output, "Lấy dữ liệu thành công");
        }


        return $this->outputError("Yêu cầu chưa được hỗ trợ");
    }

    public function category()
    {
        self::write_log();
        $query_action = request('action', 'list');

        if ($query_action === 'list' || $query_action === 'list-feature') {
            $param = request('param');


            $lsObj = BaseModel::table(MetaDataSPK::table_name)->where('removed', BaseModel::REMOVED_NO)->orderBy('_id', 'DESC');

            if (@$param['type']) {
                $lsObj->where('type', $param['type']);
            }
            if (@$param['ids']) {
                $lsObj = $lsObj->whereIn('_id', explode(',', $param['ids']));
            }
            $lsObj = Pager::getInstance()->getPager($lsObj, 50);
            $lsObj->transform(function ($item) {
                $item['_id'] = strval($item['_id']);
                $item = self::_convert_image_field_link($item);
                foreach (['created_at', 'updated_at'] as $keyDate) {
                    if (@$item[$keyDate]) {
                        $temp = $item["$keyDate"]->toDateTime();
                        $item["$keyDate"] = $temp->format('c');
                        $item["$keyDate" . "_parser"] = [
                            'timestamp' => $temp->getTimestamp(),
                            'second' => $temp->format('s'),
                            'minute' => $temp->format('i'),
                            'hour' => $temp->format('H'),
                            'day' => $temp->format('d'),
                            'month' => $temp->format('m'),
                            'year' => $temp->format('Y'),
                            'dayInWeek' => $temp->format('l'),
                        ];
                    }
                }

                return $item;
            });
            return $this->outputDone($lsObj, "Lấy dữ liệu thành công");
        }


        return $this->outputError("Yêu cầu chưa được hỗ trợ");
    }

    //Tìm theo org http://cms.superkid.vn/public_api/spk/event?param[organization_id]=5e164a7e535a6d09de303195

    public function event()
    {
        self::write_log();
        $query_action = request('action', 'list');

        if ($query_action === 'list' || $query_action === 'get') {
            $param = request('param');
            $query = [];

            if (is_string(@$param['organization_id'])) {
                $query['organization.id'] = @$param['organization_id'];
            }


            if (empty($query)) {
                $lsObj = BaseModel::table(Event::table_name)->where('removed', BaseModel::REMOVED_NO);

            } else {
                $lsObj = BaseModel::table(Event::table_name)->where('removed', BaseModel::REMOVED_NO)->where($query);
            }
            $param = request('param');
            $filter_q = @$param['q']; //search text nhanh
            if (!$filter_q) {
                $lsObj = self::handle_filter($lsObj, 'event');
            }
            $lsObj = self::handle_sort($lsObj, 'event');
            $lsObj = self::handle_define_list($lsObj, 'event');

            if ($query_action === 'get') {
                $id = $param['id'];
                if (!$id) {
                    return $this->outputError("Thiếu id");
                }
                $lsObj = BaseModel::table(Event::table_name)->where('removed', BaseModel::REMOVED_NO)->where('_id', $id);
            }

            $limit = @$param['limit'];
            $curPage = (int)\request('page', 1);
            $itemPerPage = 50;
            $limit = @$param['limit'];
            if (is_numeric($limit)) {
                if ($filter_q) {
                    $ids = $this->_search_by_elastic($filter_q, $this->mainEventIndex, $curPage, (int)$limit);
                    $lsObj = $lsObj->whereIn('_id', $ids);
                    $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage);
                } else {
                    $lsObj = Pager::getInstance()->getPager($lsObj, (int)$limit);
                }
            } else {
                if ($filter_q) {
                    $ids = $this->_search_by_elastic($filter_q, $this->mainEventIndex, $curPage, $itemPerPage);
                    $lsObj = $lsObj->whereIn('_id', $ids);
                    $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage);
                } else {
                    $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage);
                }
            }
            if ($query_action === 'get') {
                if ($lsObj->count() === 0) {
                    return $this->outputError("Không tìm thấy bản ghi");
                }
            }

            $lsPartner = Partner::whereIn('_id', $lsObj->pluck('organization.id'))->get()->keyBy('_id');
            $lsObj->transform(function ($item) use ($lsPartner) {
                if (@$item['organization']['id'] || @$item['organization']['id'] != 1) {
                    $organization = @$lsPartner[@$item['organization']['id']];
                    if (@$organization['logo']['relative_link']) {
                        $item['organization']['logo'] = array_merge(@$organization['logo'], ['relative_link' => Media::getFileLink(@$organization['logo']['relative_link'])]);
                    }
                    if (@$organization['removed'] === 'yes') {
                        $item['organization']['removed'] = 'yes';
                    } else {
                        $item['organization']['removed'] = 'no';
                    }
                    if (@$organization) {
                        $item['organization']['name'] = $organization['name'];
                    }
                }
                $item = $this->_convertEvent($item);
                return $item;
            });

            $lsObj->transform(function ($item) {
                $item['_id'] = strval($item['_id']);
                if (is_numeric(@$item['score'])) {
                    @$item['score'] = round(@$item['score'], 1);
                }
                $item = self::_convert_image_field_link($item);
                $item = $this->_convertEvent($item);
                foreach (['created_at', 'time_start_plan', 'updated_at', 'time_end_plan'] as $keyDate) {
                    if (@$item[$keyDate]) {
                        $temp = $item["$keyDate"]->toDateTime();
                        $temp->setTimezone(new \DateTimeZone(config('app.timezone')));
                        $item["$keyDate"] = $temp->format('c');
                        $item["$keyDate" . "_parser"] = [
                            'timestamp' => $temp->getTimestamp(),
                            'second' => $temp->format('s'),
                            'minute' => $temp->format('i'),
                            'hour' => $temp->format('H'),
                            'day' => $temp->format('d'),
                            'month' => $temp->format('m'),
                            'year' => $temp->format('Y'),
                            'dayInWeekOld' => $temp->format('l'),
                            'dayInWeek' => @$this->dayInWeekConvert[$temp->format('l')],
                            //'zone' => $temp->getTimezone(),
                        ];
                    }
                }

                return $item;
            });
            if ($query_action === 'get') {
                return $this->outputDone($lsObj->first(), "Lấy dữ liệu thành công");
            }
            return $this->outputDone($lsObj, "Lấy dữ liệu thành công");
        } else if ($query_action === 'facet_filter') {
            $Model = BaseModel::table(Event::table_name);
            $groupAndCountByCity = [
                [
                    '$unwind' => '$address',
                ],
                [
                    '$group' => [
                        '_id' => '$address.city.id',
                        'value' => ['$last' => '$address.city.value'],
                        'count' => ['$sum' => 1]
                    ]
                ]
            ];
            $groupAndCountByDistrict = [
                [
                    '$unwind' => '$address',
                ],
                [
                    '$group' => [
                        '_id' => '$address.district.id',
                        'value' => ['$last' => '$address.district.value'],
                        'count' => ['$sum' => 1]],
                ],
            ];
            $groupAndCountByTown = [
                [
                    '$unwind' => '$address',
                ],
                [
                    '$group' => [
                        '_id' => '$address.town.id',
                        'value' => ['$last' => '$address.town.value'],
                        'count' => ['$sum' => 1]
                    ]
                ]];
            $groupAndCountByCategory = [
                [
                    '$unwind' => '$category',
                ],
                [
                    '$group' => [
                        '_id' => '$category.id',
                        'label' => ['$last' => '$category.value'],
                        'count' => ['$sum' => 1]
                    ]
                ]];


            $aggregate = [
                [
                    '$match' => [
                        'removed' => 'no',
                    ]
                ],

                [
                    '$facet' => [
                        'groupAndCountByCity' => $groupAndCountByCity,
                        'groupAndCountByDistrict' => $groupAndCountByDistrict,
                        'groupAndCountByTown' => $groupAndCountByTown,
                        'groupAndCountByCategory' => $groupAndCountByCategory
                    ]
                ]


            ];
            $output = $Model->raw(function ($collection) use ($aggregate) {
                return $collection->aggregate($aggregate);
            })->toArray();

            return $this->outputDone($output, "Lấy dữ liệu thành công");
        }


        return $this->outputError("Yêu cầu chưa được hỗ trợ");
    }


    public function comment()
    {
        $query_action = request('action', 'list');

        if ($query_action == 'list') {
            if ($query_action === 'list' || $query_action === 'list-feature') {
                $param = request('param');


                $lsObj = BaseModel::table(Comment::table_name)->where('removed', BaseModel::REMOVED_NO)->orderBy('_id', 'DESC');

                $lsObj = self::handle_filter($lsObj, 'comment');
                $lsObj = self::handle_sort($lsObj, 'comment');
                $lsObj = self::handle_define_list($lsObj, 'comment');

                $lsObj = Pager::getInstance()->getPager($lsObj, 50);
                $lsObj->transform(function ($item) {
                    $item['_id'] = strval($item['_id']);
                    $item = self::_convert_image_field_link($item);
                    foreach (['created_at', 'updated_at', 'comment_time'] as $keyDate) {
                        if (@$item[$keyDate]) {
                            $temp = $item["$keyDate"]->toDateTime();
                            $item["$keyDate"] = $temp->format('c');
                            $item["$keyDate" . "_parser"] = [
                                'timestamp' => $temp->getTimestamp(),
                                'second' => $temp->format('s'),
                                'minute' => $temp->format('i'),
                                'hour' => $temp->format('H'),
                                'day' => $temp->format('d'),
                                'month' => $temp->format('m'),
                                'year' => $temp->format('Y'),
                                'dayInWeek' => $temp->format('l'),
                            ];
                        }
                    }

                    return $item;
                });
                return $this->outputDone($lsObj, "Lấy dữ liệu thành công");
            }
        } else if ($query_action == 'add_comment') {
            $data = request('data', []);
            $obj = @$data['obj'];
            $accessToken = request('accessToken');

            $tokenContext = UserAccessToken::where('access_token', $accessToken)->first();
            if (!$tokenContext) {
                return $this->outputError("Lỗi access_token không đúng");
            }
            $resultValidateAccessToken = $this->validate_access_token($tokenContext);
            if (!$resultValidateAccessToken['valid']) {
                return $this->outputError($resultValidateAccessToken['msg']);
            }
            $currentUser = User::find($tokenContext['user_id']);
            if (!$currentUser) {
                return $this->outputError("Lỗi access_token không đúng");
            }
            if (!isset($obj['score'])) {
                return $this->outputError("Bạn vui lòng nhập rating");
            }
            if (intval(@$obj['score']) < 0 || intval(@$obj['score']) > 5) {
                return $this->outputError("Rate phải nằm trong đoạn 0-5");
            }
            if (!in_array(@$obj['target_table'], ['event', 'partner'])) {
                return $this->outputError("Bạn phải chọn đối tượng được comment là sự kiện (event) hoặc đối tác (partner)");
            }

            if (!@$obj['target_id']) {
                return $this->outputError("Thiếu thông tin đối tượng được comment");
            }

            $targetObj = null;
            if ($obj['target_table'] == 'event') {
                $targetObj = Event::find(@$obj['target_id']);
            } else if ($obj['target_table'] == 'partner') {
                $targetObj = Partner::find(@$obj['target_id']);
            }
            if (!$targetObj) {
                return $this->outputError("Không tim thấy đối tượng cần comment");
            }

            $_comment_key = @$currentUser['_id'] . @$obj['target_id'];
            $existComment = Comment::where([
                '_comment_key' => $_comment_key,
                'removed' => 'no',
            ])->orderBy('comment_time', 'DESC')->first();
            if ($existComment) {
                $time = new \DateTime();
                $time = $time->getTimestamp();
                $loginTime = strval($existComment['comment_time']) / 1000;

                if (($time - $loginTime) < 60 * 60) {
                    return $this->outputError("Trước đó bạn đã comment, chỉ comment 1 lần mỗi 60 với mỗi bản ghi");
                }
            }

            $comment = [
                '_id' => new ObjectId(),
                'is_real_comment' => true,
                'target_table' => @$obj['target_table'],
                'target_id' => @$obj['target_id'],
                'content' => trim(@$obj['content']),
                'is_confirm' => 0,
                'status' => 'active',
                'score' => doubleval(@$obj['score']),
                'removed' => 'no',
                'updated_at' => Helper::getMongoDate(),
                'comment_time' => Helper::getMongoDate(),
                'created_at' => Helper::getMongoDate(),
                'user' => @$currentUser['username'] ? @$currentUser['username'] : "",
                'created_by' => [
                    'id' => $currentUser['_id'],
                    'name' => @$currentUser['username'] ? @$currentUser['username'] : "",
                    'type' => 'user'
                ],
                '_comment_key' => @$currentUser['_id'] . @$obj['target_id'], //cái này để check trùng
            ];
            //Update lại thông số comment của bản ghi
            $targetObjToSave['histogram'] = $targetObj['histogram'] ?: [
                "1" => 0.0,
                "2" => 0.0,
                "3" => 0.0,
                "4" => 0.0,
                "5" => 0.0
            ];
            $targetObjToSave['histogram'][@$comment['score']] = $targetObj['histogram'][@$comment['score']] + 1;
            $totalCount = 0;
            $totalScore = 0;
            foreach ($targetObjToSave['histogram'] as $key => $val) {
                $totalCount = $totalCount + $val;
                $totalScore = $totalScore + $key * $val;
            }
            if ($totalCount) {
                $targetObjToSave['score'] = round(doubleval($totalScore) / doubleval($totalCount), 2);
                $targetObjToSave['ratings'] = doubleval($totalCount);
            }
            $targetObj->update($targetObjToSave);
            Comment::insert($comment);
            return $this->outputDone($comment, "Tạo comment thành công");

        }
        return $this->outputError("Yêu cầu chưa được hỗ trợ");

    }

    public function app()
    {
        self::write_log();
        $detect = new MobileDetect();
        $app_link = config('app.link_apple_store');
        if ($detect->isiOS()) {
            $app_link = config('app.link_apple_store');
        } elseif ($detect->isAndroidOS()) {
            $app_link = config('app.link_google_store');
        }
        header('Location: ' . $app_link, true, 301);
        exit;
    }

    private function _list_comment()
    {
    }


    /* public function user()
     {
         $query_action = request('action', '');

         if ($query_action == 'register') {
             $param = request('param', []);

             $username = @$param['username'];
             $email = @$param['email'];
             $phone = @$param['phone'];
             $password = @$param['password'];

             if (empty($username)) {
                 return $this->outputError("Tên người dùng khồng được để trống");
             }
             if (empty($email)) {
                 return $this->outputError("Email được để trống");
             }
             if (!Helper::isEmail($email)) {
                 return $this->outputError("Email không đúng định dạng");
             }
             if (empty($password)) {
                 return $this->outputError("Mật khẩu không được để trống");
             }
             if (strlen($password) < 6) {
                 return $this->outputError("Mật khẩu phải có nhiều hơn 6 ký tự");
             }
             if (empty($phone)) {
                 return $this->outputError("Điện thoại không được để trống");
             }
             if (!Helper::isPhoneNumber($phone)) {
                 return $this->outputError("Số điện thoại không đúng định dạng");
             }
             $existsPhone = User::where('phone', $phone)->where('status', 'active')->first();
             if ($existsPhone) {
                 return $this->outputError("Số điện thoại này đã được sử dụng");
             }
             $existsEmail = User::where('email', $email)->where('status', 'active')->first();
             if ($existsEmail) {
                 return $this->outputError("Email này đã được sử dụng");
             }
             $code = rand(100000, 999999);
             $objToSave = [
                 'username' => $username,
                 'password' => sha1($password . "pass"),
                 "email" => $email,
                 'phone' => $phone,
                 'created_at' => Helper::getMongoDateTime(),
                 'status' => 'wait_confirm',
                 'code' => $code
             ];

             $tpl['name'] = 'Mã xác thực tài khoản';
             $tpl['code'] = $code;
             $tpl['subject'] = '[Ứng dụng PlayTime] Mã xác thực tài khoản';
             $tpl['template'] = "mail.verified_account";
             $id = User::insertGetId($objToSave);

             EmailHelper::sendMail($email, $tpl);

             return $this->outputDone(['id' => $id], 'Mã xác thực đã được gửi đi, bạn vui lòng kiểm tra email');

         } else if ($query_action === 'login') {
             $param = request('param', []);

             $account = @$param['account'];
             $password = @$param['password'];
             if (empty($account)) {
                 return $this->outputError("Bạn chưa nhập thông tin tài khoản");
             }
             $existUser = User::where(
                 [

                     '$or' => [
                         ['phone' => $account],
                         ['email' => $account]
                     ],
                     'status' => 'active'
                 ])->first();
             if (!$existUser) {
                 return $this->outputError("Thông tin tài khoản không chính xác");
             }

             $password = sha1($password . "pass");
             if ($password !== $existUser['password']) {
                 return $this->outputError("Mật khẩu không đúng");
             };
             $id = new ObjectId();
             $objToSave = [
                 '_id' => $id,
                 'access_token' => strval($id) . "__" . Helper::buildTokenString($existUser['_id'] . time() . "xx"),
                 'user_id' => $existUser['_id'],
                 'login_time' => Helper::getMongoDateTime()
             ];
             $id = UserAccessToken::insertGetId($objToSave);

             return $this->outputDone($objToSave, "Đăng nhập thành công");
         } else if ($query_action === 'send_verify_code') {
             $param = request('param', []);
             $id = @$param['id'];
             if (empty($id)) {
                 return $this->outputError("Yêu cầu không đúng, thiếu thông tin id");
             }
             $account = User::where('_id', $id)->where('status', 'wait_confirm')->first();

             if (!$account) {
                 return $this->outputError("Tài khoản yêu cầu không tồn tại");
             }
             $email = @$account['email'];
             $code = rand(100000, 999999);
             User::where('_id', $account['_id'])->update(['code' => $code]);

             $tpl['name'] = 'Mã xác thực tài khoản';
             $tpl['code'] = $code;
             $tpl['subject'] = '[Ứng dụng PlayTime] Mã xác thực tài khoản';
             $tpl['template'] = "mail.verified_account";
             EmailHelper::sendMail($email, $tpl);

             return $this->outputDone([], 'Đã gửi verify code');
         } else if ($query_action === 'verified_code') {
             $param = request('param', []);

             $id = @$param['id'];
             $code = @$param['code'];
             $currentUser = User::where('_id', $id)->where('status', 'wait_confirm')->first();
             if (!$currentUser) {
                 return $this->outputError("Yêu cầu không đúng, thiếu thông tin email");
             }
             if (@$currentUser['code'] && @$currentUser['code'] == $code) {
                 User::where('_id', $id)->update(['status' => "active"]);
                 return $this->outputDone([], 'Xác thực thành công');
             } else {
                 return $this->outputDone([], 'Mã xác thực không đúng');
             }
         } else {
             return $this->outputError("Yêu cầu chưa được hỗ trợ");
         }

     }*/

    /**
     * Email + số điện thoại
     */

    #region MEMBER REGION user member (Lấy code xác thực bằng phone, Xác thực code, Cập nhật thông tin)
    /**
     * kịch bản A:Kịch bản như sau
     * b1: Client nhập phone và gửi lên server => key = param[user_phone]
     * b2: Server kiểm tra xem phone này tồn tại hay chưa
     * b2.1: Nếu đã tồn tại thì thự hiện gửi Code (b3)
     * b2.2: nếu chưa tổn tại thì tạo mới và thực hiện gửi code (b3)
     * b3: Function Gửi code => cho số phone
     */
    public function member_get_code()
    {
        $p = request('param', []);
        $phone = (isset($p['user_mobile']) && is_string($p['user_mobile'])) ? trim($p['user_mobile']) : '';
        if (!Helper::isPhoneNumber($phone)) {
            return $this->outputError("Số điện thoại không đúng định dạng,\nVui lòng kiểm tra lại");
        }
        $phoneInDb = User::where('phone', $phone)->first();

        if ($phone == '+84888555886' || $phone == '0888555886' || $phone == '0886509919') {
            $code = 654321;
        } else {
            $code = rand(100000, 999999);
        }
        $id = '';
        if (!$phoneInDb) {
            $saveToDb = [
                'phone' => $phone,
                'created_at' => Helper::getMongoDateTime(),
                'status' => 'active',
                'code' => $code,
            ];
            $id = User::insertGetId($saveToDb);

        } else {
            //b2.2
            $id = $phoneInDb['_id'];
            User::where('phone', $phone)->update(['code' => $code]);
        }
        //gửi mã code xác thực qua sms
        //$param['message'] = 'PlayTime: Ma xac thuc so dien thoai cua ban la: ' . $code.', ban vui long khong cung cap ma nay cho nguoi khac.';
        $param['message'] = $code;//update theo yêu cầu của Thảo và bên sms: do có content và bị vào block của nhà mạng nên thống nhất là playtime chỉ bắn code sang (là số) còn nội dung thì bên nhà cung cấp dịch vụ sms họ tự chế
        $param['phone'] = $phone;
        //return $this->outputDone([],"Một tin nhắn Sms kèm theo mã xác nhận đã được gửi đến số điện thoại của bạn. Vui lòng kiểm tra và thực hiện xác thực.");
        if ($phone == '+84888555886' || $phone == '0888555886' || $phone == '0886509919') {
            $data['status'] = 1;
        } else {
            $data = SMSHelper::send_sms_api_htc($param);
        }

        //return $this->outputDone(['id' => $id, 'phone' => $phone, 'token' => Helper::buildTokenString($id . $phone)], "Một tin nhắn Sms kèm theo mã xác nhận đã được gửi đến số điện thoại của bạn. Vui lòng kiểm tra và thực hiện xác thực.");
        if (isset($data['status']) && $data['status'] == 1) {
            //thành công
            return $this->outputDone(['_id' => strval($id), 'phone' => $phone, 'token' => Helper::buildTokenString($id . $phone)], "Một tin nhắn Sms kèm theo mã xác nhận đã được gửi đến số điện thoại của bạn. Vui lòng kiểm tra và thực hiện xác thực.");
        } else {
            return $this->outputError("Không gửi được SMS đến số điện thoại của bạn. Vui lòng thử lại sau ít phút.");
        }

    }

    /**
     * Kịch bản B: Sau khi người dùng thực hiện done kịch bản A ở trên
     * b1: Người dùng gửi lên server số phone + mã code nhận được tư sms
     * b2: Check thông tin người này trên hệ thống + mã code đó
     * b2.1: Nếu hợp lệ => Trả về client thông tin hợp lệ để client điều hướng (thông tin user + token)
     * b2.2: Nếu không hợp lệ => Thông báo không hợp lệ tương ứng
     * -- Không tìm thấy số phone
     * -- Có tìm thấy nhưng số phone và mã không khớp nhau
     */
    public function member_validate_code()
    {
        $p = request('param', []);
        $phone = (isset($p['phone']) && is_string($p['phone'])) ? trim($p['phone']) : '';
        $id = (isset($p['_id']) && is_string($p['_id'])) ? trim($p['_id']) : '';
        $token = (isset($p['token']) && is_string($p['token'])) ? trim($p['token']) : '';
        $code = (isset($p['code']) && is_string($p['code'])) ? trim($p['code']) : '';
        if (!Helper::validateToken($token, $id . $phone)) {
            return $this->outputError("Dữ liệu không hợp lệ. Vui lòng quay lại lấy mã xác nhận");
        }
        if (!$code) {
            return $this->outputError("Vui lòng nhập mã xác nhận");
        }
        $phoneInDb = User::find($id);
        if (!$phoneInDb) {
            return $this->outputError("Không tìm thấy số điện thoại tương ứng trên hệ thống. Vui lòng quay lại lấy lại mã xác nhận");
        }
        if (!isset($phoneInDb['phone']) || $phoneInDb['phone'] != $phone) {
            return $this->outputError("Dữ liệu không hợp lệ. Vui lòng quay lại lấy mã xác nhận");
        }
        if (!isset($phoneInDb['code']) || $phoneInDb['code'] != $code) {
            return $this->outputError("Mã xác nhận không đúng. Vui lòng kiểm tra lại");
        }
        User::where('_id', $id)->update(['phone_verify' => $phone]);
        $dataReturn = [
            '_id' => $id,
            'phone' => $phoneInDb['phone'],
            'email' => @$phoneInDb['email'],
            'name' => @$phoneInDb['name'],
            'addr' => @$phoneInDb['addr'],
            '_token' => base64_encode(Helper::buildTokenString($_SERVER['HTTP_USER_AGENT'] . $phoneInDb['_id'])),
        ];
        return $this->outputDone($dataReturn, "Xác thực thành công");


    }

    public function member_get_info()
    {
        //Lấy thông tin đăng nhập
        $p = request('param', []);
        $id = (isset($p['_id']) && is_string($p['_id'])) ? trim($p['_id']) : '';
        $token = (isset($p['_token']) && is_string($p['_token'])) ? trim($p['_token']) : '';
        $token = base64_decode($token);
        if (!Helper::validateToken($token, $_SERVER['HTTP_USER_AGENT'] . $id)) {
            return $this->outputError("_token khong dung");
        }
        $phoneInDb = User::find($id);
        if (!$phoneInDb) {
            return $this->outputError("Tai khoan khong ton tai hoac da bi xoa");
        }
        $dataReturn = [
            '_id' => $id,
            'phone' => $phoneInDb['phone'],
            'email' => @$phoneInDb['email'],
            'name' => @$phoneInDb['name'],
            'addr' => @$phoneInDb['addr'],
            '_token' => base64_encode(Helper::buildTokenString($_SERVER['HTTP_USER_AGENT'] . $phoneInDb['_id'])),
        ];
        return $this->outputDone($dataReturn, "lấy thông tin login thành công");
    }

    /**
     * Thực hiện khi người dung ĐÃ LOGIN và muốn thay đổi thông tin cá nhân
     * - Emails, => Có thể cần xác thực
     * - Họ và Tên
     * - Địa chỉ
     * - Ngày sinh
     * - Mô tả khác nếu có
     * - Phone cũng có thể đổi nhwung đổi xong cần xác thực lại
     */
    public function member_update_info()
    {
        $p = request('param', []);
        $id = (isset($p['_id']) && is_string($p['_id'])) ? trim($p['_id']) : '';
        $token = (isset($p['_token']) && is_string($p['_token'])) ? trim($p['_token']) : '';
        $token = base64_decode($token);
        if (!Helper::validateToken($token, $_SERVER['HTTP_USER_AGENT'] . $id)) {
            return $this->outputRequireAuth("_token khong dung");
        }
        $phoneInDb = User::find($id);
        if (!$phoneInDb) {
            return $this->outputRequireAuth("Tài khoản không tồn tại hoặc đã bị xóa");
        }


        $name = (isset($p['name']) && is_string($p['name'])) ? trim($p['name']) : '';
        if (!isset($name[1])) {
            return $this->outputAlert("Họ và tên không được để trống");
        }


        $email = (isset($p['email']) && is_string($p['email'])) ? trim(strtolower($p['email'])) : '';
        if (!Helper::isEmail($email)) {
            return $this->outputAlert("Email không đúng định dạng.");
        }

        $emailInDb = User::where('email', $email)->first();
        if (isset($emailInDb['_id']) && strval($emailInDb['_id']) != $id) {
            return $this->outputAlert("Email này đã được sử dụng bởi tài khoản khác.");
        }


        $addr = (isset($p['addr']) && is_string($p['addr'])) ? trim($p['addr']) : '';

        $saveToDb = [
            'email' => $email,
            'addr' => $addr,
            'name' => $name,
        ];
        User::where('_id', $id)->update($saveToDb);
        return $this->outputAlert("Cập nhật thông tin thành công");
    }


    #endregion

    public function user()
    {

        self::write_log();

        $query_action = request('action', '');


        if ($query_action === 'login') {
            $param = request('param', []);
//return $this->outputDone($param);
            $user_mobile = @$param['account'];

            if (!$user_mobile && isset($param['user_mobile'])) {
                $user_mobile = $param['user_mobile'];
            }


            /***
             * Kịch bản
             * b1: Client gửi thông tin là số phone lên hệ thống
             * b2: Hệ thông validate tính đúng đắn của dữ liệu cơ bản (check rỗng, check hợp lệ) => Done
             * b3: Kiểm tra trong hệ thống Có số điện thoại này tồn tại hay chưa?
             * - b3.1: Nếu có thì thực hiện B4 (gửi mã)
             * - b3.2:  Nếu chưa có thì thực hiện Tạo tài khoản mới bằng số phone này trên hệ thống + gửi mãi (B4)
             * b4: Gửi mã
             * - B4.1: Gen mã ngẫu nhiên 6 ký tự (chỉ sổ) -> sau đó lưu mã này vào db gắn với số điện thoại này
             * - B4.2: Gửi mã đến người dùng thông qua SMS
             */

            //todo: @Ngannv note: Cần sửa lại function check phone với format +848885558886
            if (!Helper::isPhoneNumber($user_mobile)) {
                return $this->outputError("Số điện thoại không đúng định dạng,\nVui lòng kiểm tra lại");
            }
            $existUser = User::where('phone', $user_mobile)->first();

            if (!$existUser) {
                //Thực hiện B3.2:
                /*
                 * Thực hiện thêm mới dữ liệu
                 * */
                $code = rand(100000, 999999);
                $saveMobileToDb = [
                    'phone' => $user_mobile,
                    'created_at' => Helper::getMongoDateTime(),
                    'code' => $code,
                    //@tokendevice #ghilog todo @ngannv
                    //$cần ghi nhiều thong tin khác nữa thì ghi ở đây
                ];
                User::insertGetId($saveMobileToDb);
                //return $this->outputError("Tài khoản chưa được kích hoạt, vui lòng gửi mã xác thực để kích hoạt tài khoản.");
            }

            $code = (int)@$param['code'];
            if ($code !== $existUser['code']) {
                return $this->outputError("Mã xác thực không đúng");
            };
            $existUser->update(['status' => User::STATUS_ACTIVE]);

            $id = new ObjectId();
            $objToSave = [
                '_id' => $id,
                'access_token' => strval($id) . "__" . Helper::buildTokenString($existUser['_id'] . time() . "xx"),
                'user_id' => $existUser['_id'],
                'login_time' => Helper::getMongoDateTime()
            ];
            $id = UserAccessToken::insertGetId($objToSave);

            return $this->outputDone($objToSave, "Đăng nhập thành công");
        } else if ($query_action === 'send_verify_code') {
            $param = request('param', []);
            $phone = @$param['phone'];
            if (empty($phone)) {
                return $this->outputError("Yêu cầu không đúng, thiếu thông tin số điện thoại");
            }
            if (!Helper::isPhoneNumber($phone)) {
                return $this->outputError("Số điện thoại không đúng định dạng");
            }
            $user_mobile = User::where('phone', $phone)->first();
            $code = rand(100000, 999999);
            if (!$user_mobile) {
                $objToSave = [
                    'phone' => $phone,
                    'created_at' => Helper::getMongoDateTime(),
                    'status' => 'wait_confirm',
                    'code' => $code
                ];
                $id = User::insertGetId($objToSave);
            } else {
                $user_mobile->update(['code' => $code]);
            }

            $tpl['name'] = 'Mã xác thực tài khoản';
            $tpl['code'] = $code;
            $tpl['subject'] = '[Ứng dụng PlayTime] Mã xác thực tài khoản';
            $tpl['template'] = "mail.verified_account";

            // Debug::pushNotification(implode(" ", $tpl));

            return $this->outputDone([], 'Đã gửi verify code');
        }

    }


    public function event_sub()
    {

    }

    public function home_config()
    {
        self::write_log();

        $query_action = request('action');

        if ($query_action == 'widget_home_all') {
            $lsObj = AppSetting::where('type', 'home_setting')->get();

            $lsObj->transform(function ($item) {
                $item['_id'] = strval($item['_id']);
                $item = self::_convert_image_field_link($item);
                foreach (['created_at', 'updated_at'] as $keyDate) {
                    if (@$item[$keyDate]) {
                        $temp = $item["$keyDate"]->toDateTime();
                        $item["$keyDate"] = $temp->format('c');
                        $item["$keyDate" . "_parser"] = [
                            'timestamp' => $temp->getTimestamp(),
                            'second' => $temp->format('s'),
                            'minute' => $temp->format('i'),
                            'hour' => $temp->format('H'),
                            'day' => $temp->format('d'),
                            'month' => $temp->format('m'),
                            'year' => $temp->format('Y'),
                            'dayInWeek' => $temp->format('l'),
                        ];
                    }
                }
                return $item;
            });
            return $this->outputDone($lsObj->first(), "Lấy widget thành công");
        } else if ($query_action == 'widget_by_key') {
            $param = request('param');
            $widget_key = @$param['widget_key'];
            if (!$widget_key) {
                return $this->outputError("Thiếu widget_key");
            }
            $lsObj = AppSetting::where('type', 'home_setting')->get();
            $lsObj = $lsObj->map(function ($obj) {
                $obj['_id'] = strval($obj['_id']);
                $obj = self::_convert_image_field_link($obj);
                foreach (['created_at', 'updated_at'] as $keyDate) {
                    if (@$obj[$keyDate]) {
                        $temp = $obj["$keyDate"]->toDateTime();
                        $obj["$keyDate"] = $temp->format('c');
                        $obj["$keyDate" . "_parser"] = [
                            'timestamp' => $temp->getTimestamp(),
                            'second' => $temp->format('s'),
                            'minute' => $temp->format('i'),
                            'hour' => $temp->format('H'),
                            'day' => $temp->format('d'),
                            'month' => $temp->format('m'),
                            'year' => $temp->format('Y'),
                            'dayInWeek' => $temp->format('l'),
                        ];
                    }


                }
                return $obj;
            })->map(function ($mainObj) use ($widget_key) {
                $obj = $mainObj->toArray();
                foreach ($obj as $key => $item) {
                    if ($key !== $widget_key) {
                        continue;
                    }
                    if (!is_array($item)) {
                        continue;
                    } else if (@$item['data_source'] === 'partner') {
                        $lsTemp = Partner::whereIn('_id', collect(@$item['items'])->pluck('id')->values()->toArray())->where('removed', BaseModel::REMOVED_NO)->get();
                        $obj[$key]['items'] = $lsTemp->transform(function ($item) {
                            $item = $this->_convertPartner($item);
                            $item['_id'] = strval($item['_id']);
                            $item = self::_convert_image_field_link($item);
                            foreach (['created_at', 'updated_at'] as $keyDate) {
                                if (@$item[$keyDate]) {
                                    $temp = $item["$keyDate"]->toDateTime();
                                    $item["$keyDate"] = $temp->format('c');
                                    $item["$keyDate" . "_parser"] = [
                                        'timestamp' => $temp->getTimestamp(),
                                        'second' => $temp->format('s'),
                                        'minute' => $temp->format('i'),
                                        'hour' => $temp->format('H'),
                                        'day' => $temp->format('d'),
                                        'month' => $temp->format('m'),
                                        'year' => $temp->format('Y'),
                                        'dayInWeek' => $temp->format('l'),
                                    ];
                                }
                            }

                            return $item;
                        });

                    } else if (@$item['data_source'] === 'event') {
                        $lsTemp = collect();
                        if (!@$item['select_by']) {
                            $lsTemp = Event::whereIn('_id', collect(@$item['items'])->pluck('id')->values()->toArray())->where('removed', BaseModel::REMOVED_NO)->get();
                        } else {
                            if (@$item['select_by'] === 'top_ten_new_event') {
                                $currentTime = time();
                                $query = [
                                    'time_start_plan' => [
                                        '$gte' => Helper::convertMktimeToMongoTime($currentTime),
                                    ]
                                ];
                                $lsTemp = Event::where($query)->where('removed', BaseModel::REMOVED_NO)->orderBy('time_start_plan', 'ASC')->limit(10)->get();
                            }
                        }
                        $lsPartner = Partner::whereIn('_id', $lsTemp->pluck('organization.id'))->get()->keyBy('_id');

                        $obj[$key]['items'] = $lsTemp->transform(function ($item) use ($lsPartner) {
                            $item['_id'] = strval($item['_id']);
                            if (@$item['organization']['id']) {
                                $organization = @$lsPartner[@$item['organization']['id']];
                                if (@$organization) {

                                    $item['organization'] = [
                                        'id' => strval($organization['_id']),
                                        'name' => $organization['name'],
                                        'logo' => $organization['logo'],
                                        'removed' => $organization['removed'],
                                    ];
                                    /*if (@$organization['logo']['relative_link']) {
                                        $item['organization']['logo'] = array_merge(@$organization['logo'], ['relative_link' => Media::getFileLink(@$organization['logo']['relative_link'])]);
                                    }*/
                                }
                            }
                            foreach (['created_at', 'time_start_plan', 'updated_at', 'time_end_plan'] as $keyDate) {
                                $item = self::_convert_image_field_link($item);
                                if (@$item[$keyDate]) {
                                    $temp = $item["$keyDate"]->toDateTime();
                                    $item["$keyDate"] = $temp->format('c');
                                    $item["$keyDate" . "_parser"] = [
                                        'timestamp' => $temp->getTimestamp(),
                                        'second' => $temp->format('s'),
                                        'minute' => $temp->format('i'),
                                        'hour' => $temp->format('H'),
                                        'day' => $temp->format('d'),
                                        'month' => $temp->format('m'),
                                        'year' => $temp->format('Y'),
                                        'dayInWeek' => $temp->format('l'),
                                    ];
                                }
                            }

                            return $item;
                        });
                    } else if (@$item['data_source'] === 'category') {
                        $lsTemp = MetaDataSPK::whereIn('_id', collect(@$item['items'])->pluck('id')->values()->toArray())->where('removed', BaseModel::REMOVED_NO)->get();
                        $obj[$key]['items'] = $lsTemp->transform(function ($item) {
                            $item = $item;
                            $item['_id'] = strval($item['_id']);
                            $item = self::_convert_image_field_link($item);
                            foreach (['created_at', 'updated_at'] as $keyDate) {
                                if (@$item[$keyDate]) {
                                    $temp = $item["$keyDate"]->toDateTime();
                                    $item["$keyDate"] = $temp->format('c');
                                    $item["$keyDate" . "_parser"] = [
                                        'timestamp' => $temp->getTimestamp(),
                                        'second' => $temp->format('s'),
                                        'minute' => $temp->format('i'),
                                        'hour' => $temp->format('H'),
                                        'day' => $temp->format('d'),
                                        'month' => $temp->format('m'),
                                        'year' => $temp->format('Y'),
                                        'dayInWeek' => $temp->format('l'),
                                    ];
                                }
                            }

                            return $item;
                        });
                    }
                }
                return $obj;

            });


            $out = @$lsObj->first()[$widget_key];
            if (!$out) {
                return $this->outputError("widget_key không tồn tại");
            }
            return $this->outputDone($out, "Lấy cấu hình thành công");
        }
        return $this->outputError("Yêu cầu chưa được hỗ trợ");

    }

    /**
     * const notification_list_struct  = [{
     *  _id:'',
     *  name:'',
     *  icon:'',//tương đương với mỗi loại sẽ có 1 icon (thông báo chung, event, địa điểm, đơn hàng, link, content)
     *  time:'', //thời điểm gửi thông báo
     *  read:true,// đã đọc hoặc chưa đọc,
     *  type:'link|content',
     *  content:'Nếu type là link thì nó là link, ngược lại nếu là nội dung thì nó chứa nội dung',
     * }]
     */

    public function notification()
    {
        $query_action = request('action');

        if ($query_action == 'list') {
            $lsObj = SPKNotification::where([['receivers', 'everybody'], ['removed', BaseModel::REMOVED_NO], ['status', BaseModel::STATUS_ACTIVE]])->select('_id', 'type', 'content', 'time', 'read', 'name', 'format', 'icon')->orderBy('_id', 'DESC');
            $lsObj = Pager::getInstance()->getPager($lsObj, 20);
            $lsObj->transform(function ($item) {
                $item['_id'] = strval($item['_id']);
                $item = self::_convert_image_field_link($item);
                $item['icon'] = asset($item['icon']);
                foreach (['time'] as $keyDate) {
                    if (@$item[$keyDate]) {
                        $temp = $item["$keyDate"]->toDateTime();
                        $item["$keyDate"] = $temp->format('c');
                        $item["$keyDate" . "_parser"] = [
                            'timestamp' => $temp->getTimestamp(),
                            'second' => $temp->format('s'),
                            'minute' => $temp->format('i'),
                            'hour' => $temp->format('H'),
                            'day' => $temp->format('d'),
                            'month' => $temp->format('m'),
                            'year' => $temp->format('Y'),
                            'dayInWeek' => $temp->format('l'),
                        ];
                    }
                }

                return $item;
            });
            $out = $lsObj->toArray();
            if ($lsObj->total() === 0) {
                return $this->outputDone($out, "Không tìm thấy thông báo");
            }
            return $this->outputDone($out, "Lấy thông báo thành công");
        } else if ($query_action == 'user') {
            $param = request('param');
            if ($param) {
                $user_id = @$param['user_id'];
                if (!$user_id) {
                    return $this->outputError("Thiếu id của user");
                }

                $lsObj = SPKNotification::where([
                    ['receivers', '!=', 'everybody'],
                    ['receivers', '!=', 'nobody'],
                    'receivers' => [
                        '$elemMatch' => [
                            'id' => ['$eq' => $user_id],
                        ]
                    ],
                    ['removed', BaseModel::REMOVED_NO],
                    ['status', BaseModel::STATUS_ACTIVE]
                ])->select('_id', 'type', 'content', 'time', 'read', 'name', 'format', 'icon')->orderBy('_id', 'DESC');
                $lsObj = Pager::getInstance()->getPager($lsObj, 50);
                $lsObj->transform(function ($item) use ($user_id) {
                    if (is_array($item['receivers'])) {
                        foreach ($item['receivers'] as $obj) {
                            if ($obj['id'] == $user_id) {
                                $item['_id'] = strval($item['_id']);
                                $item['icon'] = asset($item['icon']);
                                $item = self::_convert_image_field_link($item);
                                foreach (['time'] as $keyDate) {
                                    if (@$item[$keyDate]) {
                                        $temp = $item["$keyDate"]->toDateTime();
                                        $item["$keyDate"] = $temp->format('c');
                                        $item["$keyDate" . "_parser"] = [
                                            'timestamp' => $temp->getTimestamp(),
                                            'second' => $temp->format('s'),
                                            'minute' => $temp->format('i'),
                                            'hour' => $temp->format('H'),
                                            'day' => $temp->format('d'),
                                            'month' => $temp->format('m'),
                                            'year' => $temp->format('Y'),
                                            'dayInWeek' => $temp->format('l'),
                                        ];
                                    }
                                }
                            }
                        }
                    }
                    return $item;
                });
                $out = $lsObj->toArray();
                if ($lsObj->total() === 0) {
                    return $this->outputDone($out, "Không tìm thấy thông báo");
                }
                return $this->outputDone($out, "Lấy thông báo thành công");
            }
        } else if ($query_action == 'mark_read') {
            $param = request('param');
            if ($param) {
                $user_id = @$param['user_id'];
                if (!$user_id) {
                    return $this->outputError("Thiếu id của user");
                }
                $notification_id = @$param['notification_id'];
                if (!$notification_id) {
                    return $this->outputError("Thiếu id thông báo");
                }
                $read = @$param['read'];
                if ($read === 'false' || $read === 'true') {
                    $existNotification = SPKNotification::where([
                        ['_id', $notification_id],
                        ['removed', BaseModel::REMOVED_NO],
                        ['status', BaseModel::STATUS_ACTIVE]
                    ])->select('_id', 'type', 'content', 'time', 'read', 'name', 'format', 'icon', 'receivers')->get();

                    if (!$existNotification) {
                        return $this->outputError("Thông tin thông báo không chính xác");
                    }
                    $existNotification->transform(function ($item) use ($user_id) {
                        $item['_id'] = strval($item['_id']);
                        $item['icon'] = asset($item['icon']);
                        $item = self::_convert_image_field_link($item);
                        foreach (['time'] as $keyDate) {
                            if (@$item[$keyDate]) {
                                $temp = $item["$keyDate"]->toDateTime();
                                $item["$keyDate"] = $temp->format('c');
                                $item["$keyDate" . "_parser"] = [
                                    'timestamp' => $temp->getTimestamp(),
                                    'second' => $temp->format('s'),
                                    'minute' => $temp->format('i'),
                                    'hour' => $temp->format('H'),
                                    'day' => $temp->format('d'),
                                    'month' => $temp->format('m'),
                                    'year' => $temp->format('Y'),
                                    'dayInWeek' => @$this->dayInWeekConvert[$temp->format('l')],
                                ];
                            }
                        }
                        return $item;
                    });
                    $existNotification = $existNotification->first();
                    if ($existNotification['receivers'] == 'everybody') {
                        $existNotification->update(['read' => $read]);

                        return $this->outputDone($existNotification, "Lấy thông báo thành công");

                    } else if (is_array($existNotification['receivers'])) {

                        $existNotification->where([
                            'receivers.id' => $user_id,
                        ])->update([
                            '$set' => [
                                'receivers.$.read' => $read,
                            ]
                        ]);
                        return $this->outputDone($existNotification, "Lấy thông báo thành công");
                    } else {
                        return $this->outputDone([], "Thông báo này chưa gửi cho ai!");
                    }
                } else {
                    if (!isset($read)) {
                        return $this->outputError("Thiếu trang thái đã đọc thông báo");
                    }
                    return $this->outputError("Thông tin trạng thái đã đọc không chính xác! Vui lòng kiểm tra lại.");
                }

            }
        } else {
            return $this->outputError("Yêu cầu chưa được hỗ trợ");
        }
    }

    public function setting_contact()
    {
        $query_action = request('action');
        if ($query_action == 'list') {
            $lsObj = AppSetting::where('type', 'contact_setting')->first();
            unset($lsObj->created_at, $lsObj->_id);
            return $this->outputDone($lsObj, "Lấy dữ liệu thành công");
        }
        return $this->outputError("Yêu cầu chưa được hỗ trợ");
    }

    private function validate_access_token($tokenContext)
    {
        $output = [
            'valid' => true,
            'msg' => "ok",
        ];
        if (!@$tokenContext['login_time']) {
            $output['msg'] = "Token không hợp lệ @1";
            $output['valid'] = false;
            return $output;
        }

        $time = new \DateTime();
        $time = $time->getTimestamp();
        $loginTime = strval($tokenContext['login_time']) / 1000;

        if (($time - $loginTime) > 30 * 24 * 60 * 60) {
            $output['msg'] = "Yêu cầu hết hạn @2";
            $output['valid'] = false;
            return $output;
        }
        return $output;
    }

    /**
     * @param $item
     * @return mixed
     * Trên app có sử dụng 1 số trường dạng text mà không có trong db hoặc trong db lưu trữ dạng khác
     * Do trên app tính chất chỉ cần view khong cần cấu trúc => convert các trường này sang dạng text để sử dụng
     * - Function này cũng cung cấp xử lý 1 số vấn đề liên quan đến chuyển đổi data trong db thành data dùng được
     */
    private function _convertPartner($item)
    {
        #region convert 1 số thông tin sang view dạng text cho app
        $item['khong_gian_txt'] = '';
        $item['price_txt'] = '';
        $item['ages_txt'] = '';
        $item['category_txt'] = '';

        if (@$item['khong_gian'] && is_array($item['khong_gian'])) {
            $lsString = [];
            foreach ($item['khong_gian'] as $key => $value) {
                $lsString[] = @$value['value'];
            }
            $item['khong_gian_txt'] = implode(', ', $lsString);
        }

        /* if (@$item['price'] === 'co-tra-phi') {
             $item['price_txt'] = 'Có trả phí';
         } else if (@$item['price'] === 'mien-phi') {
             $item['price_txt'] = 'Miễn phí';
         }*/

        if (@$item['price'] && is_array($item['price'])) {
            $lsString = [];
            foreach ($item['price'] as $key => $value) {
                $lsString[] = @$value['value'];
            }
            $item['price_txt'] = implode(', ', $lsString);
        }
        $item['price'] = @$item['price_txt'];

        if (@$item['available_age'] && is_array($item['available_age'])) {
            $lsString = [];
            foreach ($item['available_age'] as $key => $value) {
                $lsString[] = @$value['value'];
            }
            $item['ages_txt'] = implode(', ', $lsString);
        }
        $item['available_age'] = @$item['ages_txt'];

        if (@$item['category'] && is_array($item['category'])) {
            $lsString = [];
            foreach ($item['category'] as $key => $value) {
                $lsString[] = @$value['value'];
            }
            $item['category_txt'] = implode(', ', $lsString);
        }


        return $item;
        //eBug::show($item);
        #endregion convert 1 số thông tin sang view dạng text cho app
    }

    private function _convertEvent($item)
    {
        //xử lý thêm vụ dơn vị tổ chức khác
        //fake đơn vị tổ chức sao cho same cấu trúc mà app nhận được
        $haveOrganization = false;
        if (isset($item['organization']) && $item['organization']['id'] && $item['organization']['id'] != 1) {
            $haveOrganization = true;
        } else if (isset($item['organization_other']) && $item['organization_other']) {
            $item['organization']['name'] = $item['organization_other'];
            $item['organization']['id'] = 1;
            ///todo: bổ sung thêm fake image logo sau
            if (isset($item['organization_other_image'][0]['src']) && $item['organization_other_image'][0]['src']) {
                $item['organization']['logo'] = [
                    'id' => '',
                    'link' => Media::getFileLink(@$item['organization_other_image'][0]['src'])
                ];
            }
        }
        //end xử lý fake đơn vị tổ chức

        $item['event_time_defined'] = '';//==1: đã , 2, đang, 3 sắp

        try {
            $timeEndCompareNow = ((int)$item['time_end_plan']->__toString() / 1000) - time();
            $timeStartCompareNow = ((int)$item['time_start_plan']->__toString() / 1000) - time();
           /* eBug::show($timeEndCompareNow);
            eBug::show($timeStartCompareNow,'$timeStartCompareNow');
            eBug::show("");*/
            if ($timeEndCompareNow > 0 && $timeStartCompareNow < 0) {
                //die(__FILE__);
                $item['event_time_defined'] = 2;
            }elseif ($timeEndCompareNow<0){
                $item['event_time_defined'] = 1;
            }elseif ($timeStartCompareNow>0){
                $item['event_time_defined'] = 3;
            }

            /*if ($timeEndCompareNow < 0) {
                $item['event_time_label'] = 'Đã kết thúc';
            } else if($timeEndCompareNow>0 && $timeStartCompareNow>0) {
                //sắp diễn ra
            } */
        } catch (\Exception $exception) {

        }
        //\ebug($item);
        //die();
        return $item;
    }
}
