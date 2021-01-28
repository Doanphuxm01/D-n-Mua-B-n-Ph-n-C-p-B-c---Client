<?php

namespace App\Http\Controllers\FrontEnd\FeHome;

use App\Elibs\Pager;
use App\Http\Models\Cate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Elibs\eView;
use App\Elibs\HtmlHelper;
use App\Elibs\Helper;
use App\Http\Models\Product;
use App\Http\Models\BaseModel;

use SEOMeta;
use OpenGraph;
use Twitter;

class FeHome extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->home();
        }
    }

    public function home()
    {
        HtmlHelper::getInstance()->setTitle('Trang Chủ');
        $tpl = [];
        $where = [
            ['status' => BaseModel::STATUS_ACTIVE],
            '$expr' => [
                '$lt' => ['$finalPrice','$regularPrice']
            ],
        ];
        // tìm kiếm sản phẩm
        $flag = false;
        $q = trim(Request::capture()->input('q'));
        $itemPerPage = (int)Request::capture()->input('row', 100);
        $tpl['q'] = $q;
        if ($q) {
            $flag = true;
            $curPage = request('page', 1);
            $itemPerPage = 35;
            $where = [];
            $lsObj = Product::where($where);
            $lsObj = $lsObj->where('name', 'LIKE', '%' . trim($q) . '%')->where(['status' => Product::STATUS_ACTIVE]);
            $lsObj = $lsObj->orderBy('created_at', 'desc');
            $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage, 'all');
            $goiYDanhChoBan = Product::getByTag('goiydanhchoban');
            $tpl['lsObj'] = $lsObj;
            $tpl['goiYDanhChoBan'] = $goiYDanhChoBan;
            $tpl['page'] = $curPage;
            $tpl['itemPerPage'] = $itemPerPage;
            return eView::getInstance()->setView(__DIR__, 'search', $tpl);
        }
        // case bigsale
        if(!$flag){
            $bigSale = Product::getByTag('bigsale');
            $sanPhamNoiBat = Product::getByTag('sanphamnoibat');
            $goiYDanhChoBan = Product::getByTag('goiydanhchoban');

            $tpl['bigSale'] = $bigSale;
            $tpl['goiYDanhChoBan'] = $goiYDanhChoBan;
            $tpl['sanPhamNoiBat'] = $sanPhamNoiBat;

            // lấy ra mảng danh mục
            $lsCate = Cate::getAllProductCateShowHome();
            $groupAndCountByCategory = [
                [
                    '$unwind' => '$categories',
                ],
                [
                    '$group' => [
                        '_id' => '$categories.id',
                        'label' => ['$last' => '$categories.name'],
                        'alias' => ['$last' => '$categories.alias'],
                        'count' => ['$sum' => 1],
                        'products' => ['$push' => '$$ROOT']
                    ]
                ]
            ];

            $aggregate = [
                [
                    '$match' => [
                        'status' => BaseModel::STATUS_ACTIVE,
                        'categories.alias' => ['$in' => $lsCate['parents'][0]]
                    ]
                ],
                [
                    '$facet' => [
                        'groupAndCountByCategory' => $groupAndCountByCategory
                    ]
                ]


            ];
            $lsObj = BaseModel::table(Product::table_name)->raw(function ($collection) use ($aggregate) {
                return $collection->aggregate($aggregate);
            })->toArray();
            $tpl['lsObj'] = Helper::BsonDocumentToArray($lsObj)[0];
            return eView::getInstance()->setView(__DIR__, 'home', $tpl);
        }
    }
}
