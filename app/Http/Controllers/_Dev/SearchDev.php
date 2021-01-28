<?php
namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\SearchHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Book;
use Illuminate\Http\Request;
use App\Http\Models\Cate;
use Elasticsearch\ClientBuilder;
use App\Elibs\Pager;

class SearchDev extends Controller
{
    private $keyword = '';
    private $tag = '';
    private $mainIndex = 'doctruyen_index';
    private $mainType = 'doctruyen_io_post_type';
    private $recommendType = 'doctruyen_io_post_recommend_type';
    static $clientBuilder = false;

    function run($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        }
    }

    private function getClientBuilder()
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

    function ck()
    {
        Debug::show(SearchHelper::getPostSameName(\Request::input('q')));
        die();
        $this->keyword = \Request::input('q');
        $cate_id = (int)\Request::input('cate');
        $curent_page = (int)\Request::input('page', 1);
        if ($curent_page <= 0) {
            $curent_page = 1;
        }
        $offset = 25;
        $from = $curent_page * $offset - $offset;

        $querySearch = [
            'bool' => [
                'must' => [
                    [
                        "multi_match" => [
                            "query" => $this->keyword,
                            "type" => "phrase_prefix",
                            "fields" => ["content", "name"],
                            //'filter'=>$filter,
                        ]
                    ]
                ]
            ]
        ];

        //region xu ly tu khoa
        //endregion xu ly tu khoa
        $highlight = [
            "pre_tags" => array('<strong>'),
            "post_tags" => array('</strong>'),
            "fragment_size" => 70,
            'fields' => array('*' => [
                "fragment_size" => 70, "number_of_fragments" => 3
            ])
        ];

        $orderBy = [
//            ['updated_time' => 'desc'],
            ['_score' => 'desc'],
        ];

        $paramsSearch = [
            'index' => $this->mainIndex,
            'type' => $this->recommendType,
            'body' => [
                '_source' => ['id', 'name', 'image', 'type', 'content', 'alias'],
                'from' => $from,
                'size' => $offset,
                'sort' => $orderBy,
                // 'highlight' => $highlight,
                'query' => $querySearch,
                //'filter'=>$filter,
                //"aggregations" => $facetQuery,
            ]
        ];

        $client = $this->getClientBuilder();
        $results = $client->search($paramsSearch);
        Debug::show($results);
    }

    private function _create_index()
    {
        $client = $this->getClientBuilder();
        $indexParams = [
            'index' => SearchHelper::$mainIndex,

            'body' => [
                "settings" => [
                    'number_of_shards' => 25,
                    "max_result_window" => 9000000,
                ],
                'mappings' => [
                    SearchHelper::$mainType => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'id' => [
                                'type' => 'integer'
                            ],
                            'name' => [
                                'type' => 'string',
                                'analyzer' => 'standard'
                            ],
                            'alias' => [
                                'type' => 'string',
                            ],
                            'content' => [
                                'type' => 'string',
                                'analyzer' => 'standard'
                            ],
                            'image' => [
                                'type' => 'string',
                            ],
                            'type' => [
                                'type' => 'string',
                                'analyzer' => 'standard'
                            ],
                            'view_count' => [
                                'type' => 'integer'
                            ],
                            'post_chapter' => [
                                'type' => 'integer'
                            ],
                        ]
                    ]
                ]
            ]
        ];
        // 		Create the index
        if (!$client->indices()->exists(['index' => SearchHelper::$mainIndex,])) {
            $response = $client->indices()->create($indexParams);
            Debug::show('INDEX:"' . SearchHelper::$mainType . '" Created ok!');
            Debug::show($response);

        } else {
            Debug::show('INDEX:"' . SearchHelper::$mainIndex . '" Exists!');
        }

    }

    /****/
    private function _delete_index()
    {
        $client = $this->getClientBuilder();
        $indexParams = [
            'index' => SearchHelper::$mainIndex,
        ];
        $response = $client->indices()->delete($indexParams);

        $this->_create_index();
        die('INDEX: "' . SearchHelper::$mainIndex. '" DELETED! AND CREATED!');
    }

    private function _reset()
    {
        //todo: cần clear index khi full index


    }

    private function _index_all()
    {
        $x = @$_GET['x'];
        $where = [
            'status' => Book::STATUS_ACTIVE,
        ];
        $itemPerPage = 15000;
        $listObj = Book::where($where)->select(Book::$basicFiledsForList);
        $listObj = Pager::getInstance()->getPagerSimple($listObj, $itemPerPage, 'get');
        $count = 0;

        if ($listObj) {

            $paramsIndex = [];
            $bulkString = '';

            $client = $this->getClientBuilder();


            foreach ($listObj as $key => $val) {
                $item = [];

                $item['content'] = $val['name'] . ' ' . $val['alias'] . ' ' . Helper::removeAccent($val['name']);
                $item['content'] .= $val['brief'] . ' ' . Helper::removeAccent($val['brief']);
                $item['id'] = $val['id'];
                $item['name'] = $val['name'];
                $item['alias'] = $val['alias'];
                $item['avatar'] = $val['avatar'];
                $item['type'] = $val['type'];

                $action = [
                    'index' => [
                        '_index' => $this->mainIndex,
                        '_type' => $this->recommendType,
                        '_id' => $val['id'],
                    ]
                ];
                $actionString = json_encode($action);
                $bulkString .= "$actionString\n";
                $bulkString .= json_encode($item) . "\n";

                $count++;
                if ($count % 1000 == 0) {
                    $paramsIndex['index'] = $this->mainIndex;
                    $paramsIndex['type'] = $this->mainType;
                    $paramsIndex['body'] = $bulkString;

                    $client->bulk($paramsIndex);
                    $bulkString = '';
                    echo "\n==================== INDEX 1K =======================\n";
                }
                Debug::show($val['name']);
            }

            if ($bulkString != '') {
                $paramsIndex['index'] = $this->mainIndex;
                $paramsIndex['type'] = $this->recommendType;
                $paramsIndex['body'] = $bulkString;
                $client->bulk($paramsIndex);
            }

        }
        echo "REQUEST:->$x\n";
        echo('INDEX COUNT:' . $count . '/' . $itemPerPage);
        echo "\n===================" . date('Y/d/m H:i:s') . "======================\n";

    }


    function search()
    {
        $this->keyword = \Request::input('q');
        if (!$this->keyword) {
            return redirect('/');
        }
        $cate_id = (int)\Request::input('cate');
        $curent_page = (int)\Request::input('page', 1);
        if ($curent_page <= 0) {
            $curent_page = 1;
        }
        $offset = 25;
        $from = $curent_page * $offset - $offset;

        $querySearch = [
            'bool' => [
                'must' => [
                    [
                        'match' => ['status' => 1]
                    ],
                    [
                        "multi_match" => [
                            "query" => $this->keyword,
                            "type" => "phrase_prefix",
                            "fields" => ["content"],
                            //'filter'=>$filter,
                        ]
                    ]
                ]
            ]
        ];
        if ($cate_id) {
            $querySearch['bool']['must'][]['match'] = ['category_lvl1_id' => $cate_id];
        } else {
            //
        }
        //Debug::show($querySearch);
        //region xu ly tu khoa
        //endregion xu ly tu khoa
        $highlight = [
            "pre_tags" => array('<strong>'),
            "post_tags" => array('</strong>'),
            "fragment_size" => 70,
            'fields' => array('*' => [
                "fragment_size" => 70, "number_of_fragments" => 3
            ])
        ];

        $orderBy = [
//            ['updated_time' => 'desc'],
            ['_score' => 'desc'],
        ];


        $facetQuery = [
            "allCateRootCount" => [
                "terms" => [
                    "field" => "category_lvl1_id",
                ]
            ]
        ];

        $paramsSearch = [
            'index' => $this->mainIndex,
            'type' => $this->mainType,
            'body' => [
                '_source' => ['id', 'name', 'image', 'type', 'content', 'alias'],
                'from' => $from,
                'size' => $offset,
                'sort' => $orderBy,
                'highlight' => $highlight,
                'query' => $querySearch,
                //'filter'=>$filter,
                //"aggregations" => $facetQuery,
            ]
        ];


        $tpl = [];
        $tpl['keyword'] = $this->keyword;
        $tpl['total'] = 0;
        $tpl['cate_id'] = $cate_id;
        $client = $this->getClientBuilder();
        $results = $client->search($paramsSearch);
        if (isset($results['hits']) && $results['hits']) {
            $tpl['total'] = $results['hits']['total'];
            if (isset($results['hits']['hits']) && $results['hits']) {
                $tpl['items'] = $results['hits']['hits'];
                if (!$cate_id) {
                    //luuw laij cais cua  no nay
                    Helper::setSession('allCateRootCount', @$results['aggregations']['allCateRootCount']['buckets']);
                    $tpl['allCateRootCount'] = @$results['aggregations']['allCateRootCount']['buckets'];
                } else {
                    //todo: cần fix cái này sau này
                    $tpl['allCateRootCount'] = Helper::getSession('allCateRootCount');
                    if (!$tpl['allCateRootCount']) {
                        $tpl['allCateRootCount'] = @$results['aggregations']['allCateRootCount']['buckets'];
                    }
                }
                //$tpl['allCateRootCount'] = @$results['aggregations']['allCateRootCount']['buckets'];
            }
        } else {
            //Khong tim thay hoặc có lỗi
        }
        if (isset($_GET['xxx'])) {
            Debug::show($results);
        }


        $pgn = new Pagination($tpl['total'], $curent_page, $offset, 6);
        $tpl['pgn'] = $pgn->render();

        #region facet count
        #endregion facet count


        $title = $this->keyword . ' - Raovat.vn - Tìm kiếm';
        HtmlHelper::getInstance()->setTitle($title);

        $tpl['allPostCate'] = Cate::getAllPostCate(false);
        //Debug::show($tpl['allPostCate']);
        $output = 'search';
        if (Helper::isMobile()) {
            $output = '/mobile/search';
        }
        //Debug::show($tpl['items']);
        return eView::getInstance()->setView(__DIR__, $output, $tpl);
    }


}
