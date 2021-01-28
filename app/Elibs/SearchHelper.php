<?php
/**
 * Created by PhpStorm.
 * User: sakura
 * Date: 2/5/18
 * Time: 2:59 PM
 */

namespace App\Elibs;


use App\Http\Models\Book;
use Elasticsearch\ClientBuilder;

class SearchHelper
{
    public static $mainIndex = 'data_search_index';
    public static $mainType = 'data_search_type';
    static $clientBuilder = false;

    public static function getClientBuilder()
    {
        if (!self::$clientBuilder) {
            $hosts = [
                'http://localhost:9200'
            ];
            self::$clientBuilder = ClientBuilder::create()->setHosts($hosts)->build();
        }
        return self::$clientBuilder;
    }

    /**
     * @param $name
     * @param $limit
     * @param $colection == book, post
     * @description: Lấy danh sách truyện liên quan same same tên với truyện đang đọc
     */
    public static function getPostSameName($name, $colection='book',$limit = 11)
    {
        $keyword = $name;
        $querySearch = [
            'bool' => [
                'must' => [
                    [
                        "multi_match" => [
                            "query" => $keyword,
                            //"type" => "phrase_prefix",
                            "fields" => ["content"],
                        ]
                    ]
                ]
            ]
        ];
        $orderBy = [
            ['_score' => 'desc'],
        ];

        $paramsSearch = [
            'index' => self::$mainIndex,
            'type' => self::$mainType,
            'body' => [
                '_source' => Book::$basicFiledsForList,
                'from' => 0,
                'size' => $limit,
                'sort' => $orderBy,
                'query' => $querySearch,
            ]
        ];
        try {
            $results = self::getClientBuilder()->search($paramsSearch);
            if (isset($results['hits']['hits'])) {
                return $results['hits']['hits'];
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

    }
}