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
use App\Elibs\Pager;

require_once app_path('Elibs/simple_html_dom.php');

class v1 extends Controller
{
    function createCate()
    {
        $step = 3;

        if ($step == 3) {

            $allMenuSubject = Cate::where([
                'type' => Cate::$cateTypeRegister['menu-subject']['key']
            ])->get();
            //Debug::show($allMenuSubject);
            foreach ($allMenuSubject as $cate) {
                $link = str_replace('../', 'http://vietjack.com/', $cate->link_cawl);
                $html = Helper::getUrlContent($link);
                if ($html) {
                    $html = str_get_html($html);
                    if (!$html) {
                        // Debug::show('KHONG LAY DC HTML');
                    } else {
                        if ($link == 'http://vietjack.com/series/soan-van.jsp' || $link == 'http://vietjack.com/series/van-mau.jsp') {
                            $listCateDom = $html->find('ul.list', 0);
                            $listCateDom = [$listCateDom];
                        } else {
                            $listCateDom = $html->find('ul.list');
                        }

                        if ($listCateDom) {
                            foreach ($listCateDom as $_item) {
                                foreach ($_item->find('li a') as $item) {
                                    if (strpos($item->href, 'http://vietjack.com') === false && $item->href!='https://goo.gl/y3kTwA') {
                                        $href = str_replace('../', './', $item->href);
                                        $name = $item->plaintext;
                                        $cateByLink = Cate::where(['link_cawl' => $href])->first();
                                        if ($cateByLink) {
                                            //da tồn tại thì update parent
                                            $cateParent = $cateByLink->parents;
                                            $canAdd = true;
                                            foreach ($cateParent as $key=>$val){
                                                if($val['alias']==$cate->alias){
                                                    $canAdd = false;
                                                    break;
                                                }
                                            }
                                            if($canAdd) {
                                                $cateParent[] = [
                                                    'name' => $cate->name,
                                                    'alias' => $cate->alias,
                                                    'type' => $cate->type,
                                                    'object' => $cate->object,
                                                ];
                                                $_saveToCate = [
                                                    'parents' => $cateParent
                                                ];
                                                Debug::show($_saveToCate, $href, 'green');
                                                Cate::where(['_id' => $cateByLink->_id])->update($_saveToCate);
                                            }else{
                                                Debug::show('No add',$href,'pink');
                                            }

                                        } else {
                                            //chưa có thì thêm mới và cái gì thiếu thì sau này update sau
                                            $cateParent = [];
                                            $cateParent = [
                                                'name' => $cate->name,
                                                'alias' => $cate->alias,
                                                'type' => $cate->type,
                                                'object' => $cate->object,
                                            ];
                                            $namex = str_replace('C#', 'C sharp', $name);
                                            $namex = str_replace('C++', 'C plus plus', $namex);
                                            $saveToCate = [
                                                'name' => $name,
                                                'alias' => Helper::convertToAlias($namex),
                                                'status' => Cate::STATUS_ACTIVE,
                                                'type' => Cate::$cateTypeRegister['cate']['key'],
                                                'object' => Cate::$cateObjectRegister['subject']['key'],
                                                'link_cawl' => $href,
                                                'parents' => [
                                                    $cateParent
                                                ],
                                            ];
                                            if (Cate::getCateByAlias($saveToCate['alias'])) {
                                                Debug::show('ĐÃ TỒN TẠI');
                                            } else {
                                                Debug::show($saveToCate);
                                                Cate::insert($saveToCate);
                                            }


                                        }
                                    }else{
                                        Debug::show('NO ADD TO DB',$item->href,'violet');
                                    }
                                }

                            }
                        }
                    }
                } else {
                    Debug::show('MEO CRALW DC CUA NO');
                }
            }
            die();
        }
        if ($step == 2) {


            $string = '<ul class="nav nav-list primary left-menu">
<li class="heading">Mục lục bài học theo môn</li>
<li><a href="../series/soan-van.jsp" style="background-color: rgb(255, 165, 0);">Soạn văn</a></li>
<li><a href="../series/van-mau.jsp">Văn mẫu</a></li>
<li><a href="../series/mon-toan.jsp">Môn Toán</a></li>
<li><a href="../series/mon-tieng-anh.jsp">Môn Tiếng anh</a></li>
<li><a href="../series/mon-vat-li.jsp">Môn Vật Lí</a></li>
<li><a href="../series/mon-hoa-hoc.jsp">Môn Hoá học</a></li>
<li><a href="../series/mon-sinh-hoc.jsp">Môn Sinh học</a></li>
<li><a href="../series/mon-lich-su.jsp">Môn Lịch sử</a></li>
<li><a href="../series/mon-dia-li.jsp">Môn Địa Lí</a></li>
<li><a href="../series/mon-gdcd.jsp">Môn GDCD</a></li>
<li><a href="../series/mon-tin-hoc.jsp">Môn Tin học</a></li>
<li><a href="../series/mon-cong-nghe.jsp">Môn Công nghệ</a></li>
<li><a href="../series/it-lap-trinh.jsp">IT - Lập trình</a></li>

dsfsfasdfasdfsdf
</ul>';
            $html = str_get_html($string);
            foreach ($html->find('a') as $item) {
                $name = $item->plaintext;
                $alias = Helper::convertToAlias($name);
                $saveToMenu = [
                    'name' => $name,
                    'alias' => $alias,
                    'status' => Cate::STATUS_ACTIVE,
                    'type' => Cate::$cateTypeRegister['menu-subject']['key'],
                    'object' => Cate::$cateObjectRegister['subject']['key'],
                    'link_cawl' => $item->href
                ];
                Cate::insert($saveToMenu);
            }

            die();
        }
        if ($step == 1) {
            $string = '<ul class="nav navbar-nav">
                                <li class="level-1">
                                   <a href="./series/lop-3.jsp" class="">Lớp 3</a>
                                   <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./tieng-viet-lop-3/index.jsp">Soạn Tiếng Việt lớp 3</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-3/index.jsp">Giải Toán lớp 3</a> </li>
                                   </ul>
                                   </li>
                                <li class="level-1">
                                  <a href="./series/lop-4.jsp" class="">Lớp 4</a>
                                  <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./tieng-viet-lop-4/index.jsp">Soạn Tiếng Việt lớp 4</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-4/index.jsp">Giải Toán lớp 4</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-4/index.jsp">Đề kiểm tra Toán 4 (phần 1)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-lop-4/index.jsp">Đề kiểm tra Toán 4 (phần 2)</a> </li>
                                   
                                   </ul>
                                   </li>
                                <li class="level-1">
                                   <a href="./series/lop-5.jsp" class="">Lớp 5</a>
                                   <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./tieng-viet-lop-5/index.jsp">Soạn Tiếng Việt lớp 5</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-5/index.jsp">Giải Toán lớp 5</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-5/index.jsp">Đề kiểm tra Toán 5</a> </li>
                                        
                                   </ul>
                                   </li>
                                <li class="level-1">
                                  <a href="./series/lop-6.jsp" class="">Lớp 6</a>
                                  <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-6/index.jsp">Soạn Văn 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-6/index.jsp">Soạn Văn 6 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-6/index.jsp">Văn mẫu lớp 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-6/index.jsp">Giải Toán 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-toan-6/index.jsp">Giải SBT Toán 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-6/index.jsp">Đề kiểm tra Toán 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-6/index.jsp">Giải Vật Lí 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-vat-li-6/index.jsp">Giải SBT Vật Lí 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-vat-li-6/index.jsp">Đề kiểm tra Vật Lí 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-6/index.jsp">Giải Sinh 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-sinh-hoc-6/index.jsp">BT trắc nghiệm Sinh 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-sinh-hoc-6/index.jsp">Đề kiểm tra Sinh 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-6/index.jsp">Giải Địa Lí 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-tap-ban-do-va-bai-tap-thuc-hanh-dia-li-6/index.jsp">Tập bản đồ Địa Lí 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-6/index.jsp">Giải Tiếng Anh 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-6-moi/index.jsp">Giải Tiếng Anh 6 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-tieng-anh-6-moi/index.jsp">Giải SBT Tiếng Anh 6 mới</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-6/index.jsp">Giải Lịch sử 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-6/index.jsp">Giải Tin học 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-6/index.jsp">Giải GDCD 6</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-cong-nghe-6/index.jsp">Giải Công nghệ 6</a> </li>
                                        
                                    </ul>
                                    </li>
                                <li class="level-1">
                                    <a href="./series/lop-7.jsp" class="">Lớp 7</a>
                                    <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-7/index.jsp">Soạn Văn 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-7/index.jsp">Soạn Văn 7 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-7/index.jsp">Văn mẫu lớp 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-7/index.jsp">Giải Toán 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-toan-7/index.jsp">Giải SBT Toán 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-7/index.jsp">Giải Vật Lí 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-vat-li-7/index.jsp">Giải SBT Vật Lí 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-vat-li-7/index.jsp">BT trắc nghiệm Vật Lí 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-vat-li-7/index.jsp">Đề kiểm tra Vật Lí 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-7/index.jsp">Giải Sinh 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-sinh-hoc-7/index.jsp">Đề kiểm tra Sinh 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-7/index.jsp">Giải Địa Lí 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-tap-ban-do-va-bai-tap-thuc-hanh-dia-li-7/index.jsp">Tập bản đồ Địa Lí 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-7/index.jsp">Giải Tiếng Anh 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-tieng-anh-7/index.jsp">Giải SBT Tiếng Anh 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-7-moi/index.jsp">Giải Tiếng Anh 7 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-7/index.jsp">Giải Lịch sử 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-7/index.jsp">Giải Tin học 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-7/index.jsp">Giải GDCD 7</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-cong-nghe-7/index.jsp">Giải Công nghệ 7</a> </li>
                                        
                                    </ul>
                                    </li>
                                <li class="level-1">
                                    <a href="./series/lop-8.jsp" class="">Lớp 8</a>
                                     <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-8/index.jsp">Soạn Văn 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-8/index.jsp">Soạn Văn 8 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-8/index.jsp">Văn mẫu lớp 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-8/index.jsp">Giải Toán 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-toan-8/index.jsp">Giải SBT Toán 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-8/index.jsp">Đề kiểm tra Toán 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-8/index.jsp">Giải Vật Lí 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-vat-li-8/index.jsp">Giải SBT Vật Lí 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-vat-li-8/index.jsp">Đề kiểm tra Vật Lí 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-hoa-lop-8/index.jsp">Giải Hóa 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-hoa-8/index.jsp">Giải SBT Hóa 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-hoa-hoc-8/index.jsp">Đề kiểm tra Hóa 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-8/index.jsp">Giải Sinh 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-sinh-hoc-8/index.jsp">BT trắc nghiệm Sinh 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-8/index.jsp">Giải Địa Lí 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-tap-ban-do-va-bai-tap-thuc-hanh-dia-li-8/index.jsp">Tập bản đồ Địa Lí 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-8/index.jsp">Giải Tiếng Anh 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-8-moi/index.jsp">Giải Tiếng Anh 8 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-8/index.jsp">Giải Lịch sử 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-8/index.jsp">Giải Tin học 8</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-8/index.jsp">Giải GDCD 8</a> </li>
                                    </ul></li>
                                <li class="level-1">
                                   <a href="./series/lop-9.jsp" class="">Lớp 9</a>
                                   <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-9/index.jsp">Soạn Văn 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-9/index.jsp">Soạn Văn 9 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-9/index.jsp">Văn mẫu lớp 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-9/index.jsp">Giải Toán 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-toan-9/index.jsp">Giải SBT Toán 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./chuyen-de-toan-9/index.jsp">Chuyên đề Toán 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-9/index.jsp">Đề kiểm tra Toán 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-9/index.jsp">Giải Vật Lí 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-vat-li-9/index.jsp">Giải SBT Vật Lí 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-vat-li-9/index.jsp">Đề kiểm tra Vật Lí 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-hoa-lop-9/index.jsp">Giải Hóa 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-hoa-hoc-9/index.jsp">Đề kiểm tra Hóa 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-9/index.jsp">Giải Sinh 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./chuyen-de-sinh-hoc-9/index.jsp">Chuyên đề Sinh 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-9/index.jsp">Giải Địa Lí 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-tap-ban-do-va-bai-tap-thuc-hanh-dia-li-9/index.jsp">Tập bản đồ Địa Lí 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-9/index.jsp">Giải Tiếng Anh 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-sach-bai-tap-tieng-anh-9/index.jsp">Giải SBT Tiếng Anh 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-9-moi/index.jsp">Giải Tiếng Anh 9 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-9/index.jsp">Giải Lịch sử 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-9/index.jsp">Giải Tin học 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-9/index.jsp">Giải GDCD 9</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-cong-nghe-9/index.jsp">Giải Công nghệ 9</a> </li>
                                        
                                    </ul></li>
                                <li class="level-1">
                                    <a href="./series/lop-10.jsp" class="">Lớp 10</a>
                                    <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-10/index.jsp">Soạn Văn 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-10/index.jsp">Soạn Văn 10 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-10/index.jsp">Văn mẫu lớp 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-10/index.jsp">Giải Toán 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-10-nang-cao/index.jsp">Giải Toán 10 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-10/index.jsp">Giải Vật Lí 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-ly-10-nang-cao/index.jsp">Giải Vật Lí 10 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-hoa-lop-10/index.jsp">Giải Hóa 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-hoa-10-nang-cao/index.jsp">Giải Hóa 10 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-10/index.jsp">Giải Sinh 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-10/index.jsp">Giải Địa Lí 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-10/index.jsp">Giải Tiếng Anh 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-10-moi/index.jsp">Giải Tiếng Anh 10 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-10/index.jsp">Giải Lịch sử 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-10/index.jsp">Giải Tin học 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-10/index.jsp">Giải GDCD 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-cong-nghe-10/index.jsp">Giải Công nghệ 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-hinh-hoc-10/index.jsp">BT trắc nghiệm Hình 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-vat-li-10/index.jsp">BT trắc nghiệm Vật Lí 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-hoa-10/index.jsp">BT trắc nghiệm Hóa 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-sinh-hoc-10/index.jsp">BT trắc nghiệm Sinh 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-dia-li-10/index.jsp">BT trắc nghiệm Địa Lí 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-dia-li-10/index.jsp">Đề kiểm tra Địa Lí 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-lich-su-10/index.jsp">BT trắc nghiệm Lịch Sử 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-lich-su-10/index.jsp">Đề kiểm tra Lịch Sử 10</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-gdcd-10/index.jsp">BT trắc nghiệm GDCD 10</a> </li>
                                     
                                    </ul>
                                </li>
                                <li class="level-1">
                                <a href="./series/lop-11.jsp" class="">Lớp 11</a>
                                <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-11/index.jsp">Soạn Văn 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-11/index.jsp">Soạn Văn 11 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-11/index.jsp">Văn mẫu lớp 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-11/index.jsp">Giải Toán 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-11-nang-cao/index.jsp">Giải Toán 11 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-11/index.jsp">Giải Vật Lí 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-ly-11-nang-cao/index.jsp">Giải Vật Lí 11 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-hoa-lop-11/index.jsp">Giải Hóa 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-hoa-11-nang-cao/index.jsp">Giải Hóa 11 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-11/index.jsp">Giải Sinh 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-11/index.jsp">Giải Địa Lí 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-tap-ban-do-va-bai-tap-thuc-hanh-dia-li-11/index.jsp">Tập bản đồ Địa Lí 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-11/index.jsp">Giải Tiếng Anh 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-11-moi/index.jsp">Giải Tiếng Anh 11 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-11/index.jsp">Giải Lịch sử 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-11/index.jsp">Giải Tin học 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-11/index.jsp">Giải GDCD 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-dai-so-va-giai-tich-11/index.jsp">BT trắc nghiệm Giải tích 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-hinh-hoc-11/index.jsp">BT trắc nghiệm Hình 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-toan-11/index.jsp">Đề kiểm tra Toán lớp 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-vat-li-11/index.jsp">BT trắc nghiệm Vật Lí 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-hoa-11/index.jsp">BT trắc nghiệm Hóa 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-sinh-hoc-11/index.jsp">BT trắc nghiệm Sinh 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-dia-li-11/index.jsp">BT trắc nghiệm Địa Lí 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./de-kiem-tra-dia-li-11/index.jsp">Đề kiểm tra Địa Lí 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-lich-su-11/index.jsp">BT trắc nghiệm Lịch Sử 11</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-gdcd-11/index.jsp">BT trắc nghiệm GDCD 11</a> </li>
                                     
                                    </ul>
                                    </li>
                                <li class="level-1">
                                <a href="./series/lop-12.jsp" class="">Lớp 12</a>
                                   <ul class="menu-2 row">
                                        <li class="level-2 col-xs-6"><a href="./soan-van-lop-12/index.jsp">Soạn Văn 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./soan-van-12/index.jsp">Soạn Văn 12 (ngắn nhất)</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./van-mau-lop-12/index.jsp">Văn mẫu lớp 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-lop-12/index.jsp">Giải Toán 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-toan-12-nang-cao/index.jsp">Giải Toán 12 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-vat-ly-12/index.jsp">Giải Vật Lí 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-ly-12-nang-cao/index.jsp">Giải Vật Lí 12 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-hoa-lop-12/index.jsp">Giải Hóa 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-hoa-12-nang-cao/index.jsp">Giải Hóa 12 nâng cao</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-sinh-hoc-12/index.jsp">Giải Sinh 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./chuyen-de-sinh-hoc-12/index.jsp">Chuyên đề Sinh 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-dia-li-12/index.jsp">Giải Địa Lí 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-tap-ban-do-va-bai-tap-thuc-hanh-dia-li-12/index.jsp">Tập bản đồ Địa Lí 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-12/index.jsp">Giải Tiếng Anh 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./tieng-anh-12-moi/index.jsp">Giải Tiếng Anh 12 thí điểm</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-lich-su-12/index.jsp">Giải Lịch sử 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-tin-hoc-12/index.jsp">Giải Tin học 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./giai-bai-tap-giao-duc-cong-dan-12/index.jsp">Giải GDCD 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-giai-tich-12/index.jsp">BT trắc nghiệm Giải tích 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-hinh-hoc-12/index.jsp">BT trắc nghiệm Hình 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-vat-li-12/index.jsp">BT trắc nghiệm Vật Lí 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-hoa-12/index.jsp">BT trắc nghiệm Hóa 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./chuyen-de-sinh-hoc-12/index.jsp">Chuyên đề Sinh 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-dia-li-12/index.jsp">BT trắc nghiệm Địa Lí 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-lich-su-12/index.jsp">BT trắc nghiệm Lịch Sử 12</a> </li>
                                        <li class="level-2 col-xs-6"><a href="./bai-tap-trac-nghiem-gdcd-12/index.jsp">BT trắc nghiệm GDCD 12</a> </li>  
                                
                                    </ul>
                                    </li>
                                    
                                </li>
                            </ul>';

            $html = str_get_html($string);
            $number = 0;
            foreach ($html->find('li.level-1') as $item) {
                $number = 0;
                $saveParent = [];
                foreach ($item->find('a') as $x) {
                    $name = $x->plaintext;
                    $alias = Helper::convertToAlias($name);
                    $cateInDb = Cate::getCateByAlias($alias);
                    if ($cateInDb) {

                    } else {
                        if ($number == 0) {
                            $saveToMenu = [
                                'name' => $name,
                                'alias' => $alias,
                                'status' => Cate::STATUS_ACTIVE,
                                'type' => Cate::$cateTypeRegister['menu-class']['key'],
                                'object' => Cate::$cateObjectRegister['subject']['key'],
                                'link_cawl' => $x->href
                            ];
                            $saveParent = [
                                'name' => $name,
                                'alias' => $alias,
                                'type' => Cate::$cateTypeRegister['menu-class']['key'],
                                'object' => Cate::$cateObjectRegister['subject']['key'],
                            ];
                            Cate::insert($saveToMenu);
                        } else {
                            $saveToCate = [
                                'name' => $name,
                                'alias' => $alias,
                                'status' => Cate::STATUS_ACTIVE,
                                'type' => Cate::$cateTypeRegister['cate']['key'],
                                'object' => Cate::$cateObjectRegister['subject']['key'],
                                'link_cawl' => $x->href,
                                'parents' => [
                                    $saveParent
                                ],
                            ];
                            //Debug::show($saveToCate);
                            Cate::insert($saveToCate);
                            //$saveParent = [];
                        }
                    }
                    $number++;
                }
            }
        }


    }

    function getCate(){

    }
}
