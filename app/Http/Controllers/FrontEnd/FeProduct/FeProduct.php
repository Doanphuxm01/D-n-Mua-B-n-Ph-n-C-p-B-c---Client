<?php

namespace App\Http\Controllers\Frontend\FeProduct;

use App\Elibs\Helper;
use App\Http\Models\QuaTang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Elibs\eView;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Models\Product;
use App\Http\Models\Cate;
use App\Http\Models\BaseModel;
// use App\Elibs\Pager;

use SEOMeta;
use OpenGraph;
use Twitter;


class FeProduct extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->detail();
        }
    }

    public function detail($alias, $id)
    {
        $tpl = [];
        $obj = Product::getByProductIdAndAlias($id, $alias);
        if (!$obj) {
            return eView::getInstance()->setView404();
        }

        if (!isset($obj['status']) || $obj['status'] !== BaseModel::STATUS_ACTIVE) {
            return redirect('/');
        }
        HtmlHelper::getInstance()->setTitle($obj['name']);
        $lsQuaTang = QuaTang::where('san_pham_ap_dung.id', Helper::getMongoId($id))->get()->keyBy('sku')->toArray();
        $goiYDanhChoBan = Product::getByTag('goiydanhchoban');
        $tpl['goiYDanhChoBan'] = $goiYDanhChoBan;
        $tpl['lsQuaTang'] = $lsQuaTang;
        $tpl['obj'] = $obj;
        $seo_title = $obj['name'];
        $seo_des = isset($obj['sumary']) ? $obj['sumary'] : @$obj['recentChanges'];
        $seo_keyword = ['sản phẩm hot', 'mua ngay'];
        $this->seo($seo_title, $seo_des, $seo_keyword);
        return eView::getInstance()->setView(__DIR__, 'detail', $tpl);
    }

    public function cate($alias){
        if (!$alias) {
            return redirect('/');
        }
        $curCate = Cate::getCateByAlias($alias);
        if (!$curCate) {
            return eView::getInstance()->setView404();
        }
        $tpl['curCate'] = $curCate;
        HtmlHelper::getInstance()->setTitle($curCate['name']);
        $allCate = Cate::getAllCate();
        
        
        $tpl['allCate'] = $allCate;
        #region danh sách item theo danh mục
        $curPage = request('page', 1);
        $itemPerPage = 35;
        $lsObj = Product::where(['status' => Product::STATUS_ACTIVE])
            ->where(['categories.alias' => $curCate['alias']])
            ->select(Product::$basicFiledsForList);
        $sort = request('sort');
        if ($sort == 'rating') {
            $lsObj = $lsObj->orderBy('ratings', 'DESC');
        } else {
            $lsObj = $lsObj->orderBy('updated_at', 'DESC');
        }
        $lsObj = Pager::getInstance()->getPager($lsObj, $itemPerPage, $curPage);
        $tpl['lsObj'] = $lsObj;
        #endregion danh sách item theo danh mục


        $tpl['page'] = $curPage;
        $tpl['itemPerPage'] = $itemPerPage;
        
        return eView::getInstance()->setView(__DIR__, 'cate', $tpl);
    }

    private function seo($seo_title, $seo_des, $seo_keyword)
    {

        SEOMeta::setTitle($seo_title);
        SEOMeta::setDescription($seo_des);
        SEOMeta::setKeywords($seo_keyword);

        OpenGraph::setTitle($seo_title);
        OpenGraph::setDescription($seo_des);
        OpenGraph::setUrl(url()->current());
        OpenGraph::addProperty('type', 'object');
        OpenGraph::setSiteName(config('app.brand_name'));

        Twitter::setTitle($seo_title); // title of twitter card tag
        Twitter::setSite(config('app.brand_name')); // site of twitter card tag
        Twitter::setDescription($seo_des); // description of twitter card tag
        Twitter::setUrl(url()->current()); // url of twitter card tag
    }
}
