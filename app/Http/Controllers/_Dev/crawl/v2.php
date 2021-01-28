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

class v2 extends Controller
{


    /**
     * Bước 1 chạy create cate
     * Bước 2: chạy lấy index (chỉ mục của các danh mục
     */
    function createCate()
    {
        $step = 2;

        if ($step == 1) {

            $string = '<ul class="nav">
                        <li class="clearfix"><a href="/toan-hoc-c40.html"><span>Toán học</span></a><ul class="sub-nav2"><li><a href="/toan-lop-12-c47.html">Toán lớp 12</a></li><li><a href="/toan-lop-12-nang-cao-c201.html">Toán lớp 12 Nâng cao</a></li><li><a href="/toan-lop-11-c46.html">Toán lớp 11</a></li><li><a href="/toan-lop-11-nang-cao-c202.html">Toán lớp 11 Nâng cao</a></li><li><a href="/toan-lop-10-c45.html">Toán lớp 10</a></li><li><a href="/toan-lop-10-nang-cao-c204.html">Toán lớp 10 Nâng cao</a></li><li><a href="/toan-lop-9-c44.html">Toán lớp 9</a></li><li><a href="/toan-lop-8-c43.html">Toán lớp 8</a></li><li><a href="/toan-lop-7-c42.html">Toán lớp 7</a></li><li><a href="/toan-lop-6-c41.html">Toán lớp 6</a></li><li><a href="/toan-lop-5-c109.html">Toán lớp 5</a></li><li><a href="/toan-lop-4-c112.html">Toán lớp 4</a></li><li><a href="/toan-lop-3-c113.html">Toán lớp 3</a></li><li><a href="/toan-lop-2-c114.html">Toán lớp 2</a></li><li><a href="/toan-lop-1-c111.html">Toán lớp 1</a></li></ul></li><li class="clearfix"><a href="/ngu-van-c29.html"><span>Ngữ văn</span></a><ul class="sub-nav2"><li><a href="/luyen-dang-doc-hieu-c122.html">Luyện dạng đọc hiểu</a></li><li><a href="/ngu-van-lop-12-c30.html">Ngữ Văn lớp 12</a></li><li><a href="/ngu-van-lop-11-c38.html">Ngữ văn lớp 11</a></li><li><a href="/ngu-van-lop-10-c37.html">Ngữ văn lớp 10</a></li><li><a href="/ngu-van-lop-9-c36.html">Ngữ văn lớp 9</a></li><li><a href="/ngu-van-lop-8-c35.html">Ngữ văn lớp 8</a></li><li><a href="/ngu-van-lop-7-c34.html">Ngữ văn lớp 7</a></li><li><a href="/ngu-van-lop-6-c33.html">Ngữ văn lớp 6</a></li><li><a href="/tieng-viet-lop-5-c117.html">Tiếng Việt lớp 5</a></li><li><a href="/tieng-viet-lop-4-c118.html">Tiếng Việt lớp 4</a></li><li><a href="/tieng-viet-lop-3-c119.html">Tiếng Việt lớp 3</a></li><li><a href="/tieng-viet-lop-2-c120.html">Tiếng Việt lớp 2</a></li></ul></li><li class="clearfix"><a href="/anh-van-c72.html"><span>Tiếng Anh</span></a><ul class="sub-nav2"><li><a href="/ngu-phap-tieng-anh-c131.html">Ngữ pháp Tiếng Anh</a></li><li><a href="/tieng-anh-lop-12-c79.html">Tiếng Anh lớp 12</a></li><li><a href="/tieng-anh-lop-11-c78.html">Tiếng Anh lớp 11</a></li><li><a href="/tieng-anh-lop-10-c77.html">Tiếng Anh lớp 10</a></li><li><a href="/tieng-anh-lop-9-c76.html">Tiếng Anh lớp 9</a></li><li><a href="/tieng-anh-lop-8-c75.html">Tiếng Anh lớp 8</a></li><li><a href="/tieng-anh-lop-7-c74.html">Tiếng Anh lớp 7</a></li><li><a href="/tieng-anh-lop-6-c73.html">Tiếng Anh lớp 6</a></li><li><a href="/tieng-anh-lop-3-moi-c133.html">Tiếng Anh lớp 3 Mới</a></li><li><a href="/tieng-anh-lop-4-moi-c136.html">Tiếng Anh lớp 4 Mới</a></li><li><a href="/tieng-anh-lop-5-moi-c140.html">Tiếng Anh lớp 5 Mới</a></li><li><a href="/tieng-anh-lop-6-moi-c134.html">Tiếng Anh lớp 6 Mới</a></li><li><a href="/tieng-anh-lop-7-moi-c139.html">Tiếng Anh lớp 7 Mới</a></li><li><a href="/tieng-anh-lop-8-moi-c138.html">Tiếng Anh lớp 8 Mới</a></li><li><a href="/tieng-anh-lop-9-moi-c141.html">Tiếng Anh lớp 9 Mới</a></li><li><a href="/tieng-anh-lop-10-moi-c137.html">Tiếng Anh lớp 10 Mới</a></li><li><a href="/tieng-anh-lop-11-moi-c142.html">Tiếng Anh lớp 11 Mới</a></li><li><a href="/tieng-anh-lop-12-moi-c143.html">Tiếng Anh lớp 12 Mới</a></li></ul></li><li class="clearfix"><a href="/vat-ly-c56.html"><span>Vật lý</span></a><ul class="sub-nav2"><li><a href="/vat-ly-lop-12-c63.html">Vật lý lớp 12</a></li><li><a href="/vat-li-lop-12-nang-cao-c208.html">Vật lí lớp 12 Nâng cao</a></li><li><a href="/vat-ly-lop-11-c62.html">Vật lý lớp 11</a></li><li><a href="/vat-ly-lop-11-nang-cao-c209.html">Vật lý lớp 11 Nâng cao</a></li><li><a href="/vat-ly-lop-10-c61.html">Vật lý lớp 10</a></li><li><a href="/vat-ly-lop-10-nang-cao-c210.html">Vật lý lớp 10 Nâng cao</a></li><li><a href="/vat-ly-lop-9-c60.html">Vật lý lớp 9</a></li><li><a href="/vat-ly-lop-8-c59.html">Vật lý lớp 8</a></li><li><a href="/vat-ly-lop-7-c58.html">Vật lý lớp 7</a></li><li><a href="/vat-ly-lop-6-c57.html">Vật lý lớp 6</a></li></ul></li><li class="clearfix"><a href="/hoa-hoc-c50.html"><span>Hóa học</span></a><ul class="sub-nav2"><li><a href="/hoa-lop-12-c55.html">Hóa lớp 12</a></li><li><a href="/hoa-hoc-lop-12-nang-cao-c211.html">Hóa học lớp 12 Nâng cao</a></li><li><a href="/hoa-lop-11-c54.html">Hóa lớp 11</a></li><li><a href="/hoa-hoc-lop-11-nang-cao-c212.html">Hóa học lớp 11 Nâng cao</a></li><li><a href="/hoa-lop-10-c53.html">Hóa lớp 10</a></li><li><a href="/hoa-hoc-lop-10-nang-cao-c213.html">Hóa học lớp 10 Nâng cao</a></li><li><a href="/hoa-lop-9-c52.html">Hóa lớp 9</a></li><li><a href="/hoa-lop-8-c51.html">Hóa lớp 8</a></li></ul></li><li class="clearfix"><a href="/sinh-hoc-c64.html"><span>Sinh học</span></a><ul class="sub-nav2"><li><a href="/sinh-lop-12-c71.html">Sinh lớp 12</a></li><li><a href="/sinh-lop-11-c70.html">Sinh lớp 11</a></li><li><a href="/sinh-lop-10-c69.html">Sinh lớp 10</a></li><li><a href="/sinh-lop-9-c68.html">Sinh lớp 9</a></li><li><a href="/sinh-lop-8-c67.html">Sinh lớp 8</a></li><li><a href="/sinh-lop-7-c66.html">Sinh lớp 7</a></li><li><a href="/sinh-lop-6-c65.html">Sinh lớp 6</a></li></ul></li><li class="clearfix"><a href="/lich-su-c80.html"><span>Lịch sử</span></a><ul class="sub-nav2"><li><a href="/lich-su-lop-12-c87.html">Lịch sử lớp 12</a></li><li><a href="/lich-su-lop-11-c86.html">Lịch sử lớp 11</a></li><li><a href="/lich-su-lop-10-c85.html">Lịch sử lớp 10</a></li><li><a href="/lich-su-lop-9-c84.html">Lịch sử lớp 9</a></li><li><a href="/lich-su-lop-8-c83.html">Lịch sử lớp 8</a></li><li><a href="/lich-su-lop-7-c82.html">Lịch sử lớp 7</a></li><li><a href="/lich-su-lop-6-c81.html">Lịch sử lớp 6</a></li><li><a href="/lich-su-lop-5-c149.html">Lịch sử lớp 5</a></li><li><a href="/lich-su-lop-4-c150.html">Lịch sử lớp 4</a></li></ul></li><li class="clearfix"><a href="/dia-li-c88.html"><span>Địa lí</span></a><ul class="sub-nav2"><li><a href="/dia-li-lop-12-c95.html">Địa lí lớp 12</a></li><li><a href="/dia-li-lop-11-c94.html">Địa lí lớp 11</a></li><li><a href="/dia-li-lop-10-c93.html">Địa lí lớp 10</a></li><li><a href="/dia-li-lop-9-c92.html">Địa lí lớp 9</a></li><li><a href="/dia-li-lop-8-c91.html">Địa lí lớp 8</a></li><li><a href="/dia-li-lop-7-c90.html">Địa lí lớp 7</a></li><li><a href="/dia-li-lop-6-c89.html">Địa lí lớp 6</a></li><li><a href="/dia-li-lop-5-c151.html">Địa lí lớp 5</a></li><li><a href="/dia-li-lop-4-c152.html">Địa lí lớp 4</a></li></ul></li><li class="clearfix"><a href="/giao-duc-cong-dan-c144.html"><span>GDCD</span></a><ul class="sub-nav2"><li><a href="/gdcd-lop-12-c163.html">GDCD lớp 12</a></li><li><a href="/gdcd-lop-11-c164.html">GDCD lớp 11</a></li><li><a href="/gdcd-lop-10-c165.html">GDCD lớp 10</a></li><li><a href="/gdcd-lop-9-c148.html">GDCD lớp 9</a></li><li><a href="/gdcd-lop-8-c147.html">GDCD lớp 8</a></li><li><a href="/gdcd-lop-7-c146.html">GDCD lớp 7</a></li><li><a href="/gdcd-lop-6-c145.html">GDCD lớp 6</a></li></ul></li><li class="clearfix"><a href="/tin-hoc-c153.html"><span>Tin học</span></a><ul class="sub-nav2"><li><a href="/tin-hoc-lop-12-c154.html">Tin học lớp 12</a></li><li><a href="/tin-hoc-lop-11-c155.html">Tin học lớp 11</a></li><li><a href="/tin-hoc-lop-10-c156.html">Tin học lớp 10</a></li><li><a href="/tin-hoc-lop-9-c157.html">Tin học lớp 9</a></li><li><a href="/tin-hoc-lop-8-c158.html">Tin học lớp 8</a></li><li><a href="/tin-hoc-lop-7-c159.html">Tin học lớp 7</a></li><li><a href="/tin-hoc-lop-6-c160.html">Tin học lớp 6</a></li></ul></li><li class="clearfix"><a href="/cong-nghe-c166.html"><span>Công nghệ</span></a><ul class="sub-nav2"><li><a href="/cong-nghe-12-c167.html">Công nghệ 12</a></li><li><a href="/cong-nghe-11-c168.html">Công nghệ 11</a></li><li><a href="/cong-nghe-10-c169.html">Công nghệ 10</a></li><li><a href="/cong-nghe-9-c170.html">Công nghệ 9</a></li><li><a href="/cong-nghe-8-c171.html">Công nghệ 8</a></li><li><a href="/cong-nghe-7-c172.html">Công nghệ 7</a></li><li><a href="/cong-nghe-6-c173.html">Công nghệ 6</a></li></ul></li><li class="clearfix"><a href="/khoa-hoc-c175.html"><span>Khoa học</span></a><ul class="sub-nav2"><li><a href="/khoa-hoc-lop-5-c177.html">Khoa học lớp 5</a></li><li><a href="/khoa-hoc-lop-4-c176.html">Khoa học lớp 4</a></li></ul></li><li class="clearfix"><a href="javascript:void(0)">Môn khác</a><ul class="sub-nav2-1col"><li><a target="_blank" href="http://loigiaihay.com/truyen-co-tich-e3163.html"><span>Truyện cổ tích</span></a></li><li><a href="/mon-dai-cuong-c123.html"><span>Môn Đại Cương</span></a></li></ul></li>
                    </ul>';
            $html = str_get_html($string);

            foreach ($html->find('ul.nav li.clearfix') as $item) {
                $aDom = $item->find('a', 0);

                $nameMenu = $aDom->plaintext;
                $aliasMenu = Helper::convertToAlias($nameMenu);
                $saveToMenu = [
                    'name' => $nameMenu,
                    'alias' => $aliasMenu,
                    'status' => Cate::STATUS_ACTIVE,
                    'type' => Cate::$cateTypeRegister['menu-subject']['key'],
                    'object' => Cate::$cateObjectRegister['subject']['key'],
                    'link_source' => $aDom->href
                ];
                Debug::show($saveToMenu, 'SAVE MENU SUBJECT', 'green');
                Cate::insert($saveToMenu);


                foreach ($item->find('ul.sub-nav2 li a') as $aDom) {
                    $name = $aDom->plaintext;
                    $alias = Helper::convertToAlias($name);
                    $parents = [
                        [
                            'name' => $nameMenu,
                            'alias' => $aliasMenu,
                            'type' => Cate::$cateTypeRegister['menu-subject']['key'],
                            'object' => Cate::$cateObjectRegister['subject']['key'],
                        ]
                    ];
                    $numberClass = Helper::getNumberOnlyInString($alias);
                    if ($numberClass) {
                        if (!Cate::getCateByAlias('lop-' . $numberClass)) {
                            $saveMenuClass = [
                                'name' => 'Lớp ' . $numberClass,
                                'alias' => 'lop-' . $numberClass,
                                'status' => Cate::STATUS_ACTIVE,
                                'type' => Cate::$cateTypeRegister['menu-class']['key'],
                                'object' => Cate::$cateObjectRegister['subject']['key'],
                            ];
                            Debug::show($saveMenuClass, 'SAVE MENU CLASS', 'pink');
                            Cate::insert($saveMenuClass);
                        }
                        $parents[] = [
                            'name' => 'Lớp ' . $numberClass,
                            'alias' => 'lop-' . $numberClass,
                            'type' => Cate::$cateTypeRegister['menu-class']['key'],
                            'object' => Cate::$cateObjectRegister['subject']['key'],
                        ];
                    }
                    $saveToCate = [
                        'name' => $name,
                        'alias' => $alias,
                        'status' => Cate::STATUS_ACTIVE,
                        'type' => Cate::$cateTypeRegister['cate']['key'],
                        'object' => Cate::$cateObjectRegister['subject']['key'],
                        'link_source' => $aDom->href,
                        'parents' => $parents
                    ];
                    Debug::show($saveToCate);
                    Cate::insert($saveToCate);
                }

            }

            die();

        }
    }


    function get_index()
    {
        //cralw mục lục
        $allCate = Cate::where([
                'type' => Cate::$cateTypeRegister['cate']['key'],
                'object' => Cate::$cateObjectRegister['subject']['key'],
            ]
        );
        echo "\nPAGE:".@$_GET['page'];
        $allCate = Pager::getInstance()->getPagerSimple($allCate, 1);
        foreach ($allCate as $key => $value) {
            //$link_source = 'http://loigiaihay.com' . $value->link_source;
            $link_source = 'http://loigiaihay.com/ngu-van-lop-12-c30.html';
            //Debug::show($link_source);
            $html = Helper::getUrlContent($link_source);
            if ($html) {
                $html = str_get_html($html);
            }
            if ($html) {
                /*foreach ($html->find('div.box div.subject ul.magL20 li a') as $item){
                    $item->outertext='<level4 class="level4">'.$item->innertext.'</level4>';
                }foreach ($html->find('div.box div.subject ul.magL20 li') as $item){
                    $item->outertext='<level3 class="level3">'.$item->innertext.'</level3>';
                }*/
                foreach ($html->find('div.box div.subject') as $item) {

                    $h1IndexDom = $item->find('h3.s14 a', 0);//level 1
                    if ($h1IndexDom) {
                        $name = $h1IndexDom->plaintext;
                        $alias = Helper::convertToAlias($name . '-' . Helper::getNumberOnlyInString($h1IndexDom->href));
                        $cateInDb = Cate::getCateByAlias($alias);
                        $saveH1 = true;
                        if ($cateInDb) {
                            if ($cateInDb->type == Cate::$cateTypeRegister['main-index']['key'] && $cateInDb->object = Cate::$cateObjectRegister['index']['key']) {
                                //đã tồn tại rồi thì thôi dùng cái này luôn
                                $saveH1 = false;
                                $updateParents = [
                                    'parents' => [
                                        [
                                            'name' => $value->name,
                                            'alias' => $value->alias,
                                            'type' => $value->type,
                                            'object' => $value->object,
                                        ]
                                    ]
                                ];
                                Cate::where(['_id' => $cateInDb->id])->where($updateParents);
                                echo "\nUPDATE CATE OK";
                            } else {
                                //tức là trùng với cái khác đang có thì thêm cái này nhưng biến tấu alias đi
                                $alias = $alias . '-1';
                            }
                        }
                        if ($saveH1) {
                            // Lưu h1 vào dn
                            $saveH1ToDb = [
                                'name' => $name,
                                'alias' => $alias,
                                'status' => Cate::STATUS_ACTIVE,
                                'type' => Cate::$cateTypeRegister['main-index']['key'],
                                'object' => Cate::$cateObjectRegister['index']['key'],
                                'parents' => [
                                    [
                                        'name' => $value->name,
                                        'alias' => $value->alias,
                                        'type' => $value->type,
                                        'object' => $value->object,
                                    ]
                                ],
                                'link_source' => $h1IndexDom->href
                            ];
                            Debug::show($saveH1ToDb, 'LEVEL 1', 'green');
                            Cate::insert($saveH1ToDb);

                        }
                        $parentCateOfH2 = [
                            [
                                'name' => $name,
                                'alias' => $alias,
                                'type' => Cate::$cateTypeRegister['main-index']['key'],
                                'object' => Cate::$cateObjectRegister['index']['key'],
                            ],//cate sach
                            [
                                'name' => $value->name,
                                'alias' => $value->alias,
                                'type' => $value->type,
                                'object' => $value->object,
                            ],//cate h1
                        ];

                        $h2IndexDom = $item->find('ul.magL10 li ul.magL20');
                        if ($h2IndexDom) {
                            $number = 0;
                            foreach ($h2IndexDom as $ulDom) {

                                //Debug::show($ulDom->parent()->find('a',0)->innertext);
                                $aDom = $ulDom->parent()->find('a', 0);
                                $name = $aDom->plaintext;
                                //Debug::show($name);
                                $alias = Helper::convertToAlias($name . '-' . Helper::getNumberOnlyInString($aDom->href));
                                $cateInDb = Cate::getCateByAlias($alias);
                                $saveH2 = true;
                                if ($cateInDb) {
                                    if ($cateInDb->type == Cate::$cateTypeRegister['main-index']['key'] && $cateInDb->object = Cate::$cateObjectRegister['index']['key']) {
                                        //đã tồn tại rồi thì thôi dùng cái này luôn
                                        //đã tồn tại thì cần update cate root
                                        $updateParent = true;
                                        //Debug::show($cateInDb->toArray());
                                        if (isset($cateInDb->parents) && $cateInDb->parents) {
                                            foreach ($cateInDb->parents as $_p) {
                                                //Debug::show($_p);
                                                if ($_p['alias'] == $value->alias) {
                                                    $updateParent = false;
                                                }
                                            }
                                        }
                                        if ($updateParent) {
                                            $parents = $cateInDb->parents;
                                            $parents[] = [
                                                'name' => $value->name,
                                                'alias' => $value->alias,
                                                'type' => $value->type,
                                                'object' => $value->object,
                                            ];
                                            $updateH2InDb = [
                                                'parents' => $parents,
                                            ];
                                            Cate::where('_id', $cateInDb->_id)->update($updateH2InDb);
                                            Debug::show($updateH2InDb, 'UPDATE h2', 'blue');
                                        }
                                        $saveH2 = false;
                                    } else {
                                        //tức là trùng với cái khác đang có thì thêm cái này nhưng biến tấu alias đi
                                        $alias = $alias . '-2';
                                    }
                                }

                                if ($saveH2) {
                                    $saveH2ToDb = [
                                        'name' => $name,
                                        'alias' => $alias,
                                        'status' => Cate::STATUS_ACTIVE,
                                        'parents' => $parentCateOfH2,
                                        'type' => Cate::$cateTypeRegister['main-index']['key'],
                                        'object' => Cate::$cateObjectRegister['index']['key'],
                                        'link_source' => $aDom->href
                                    ];
                                    Debug::show($saveH2ToDb, 'LEVEL 2 ' . $number, 'violet');
                                    Cate::insert($saveH2ToDb);
                                }
                                ///h3
                                $parentCateOfH3 = [
                                    [
                                        'name' => $name,
                                        'alias' => $alias,
                                        'type' => Cate::$cateTypeRegister['main-index']['key'],
                                        'object' => Cate::$cateObjectRegister['index']['key'],
                                    ],//cate sach
                                    [
                                        'name' => $value->name,
                                        'alias' => $value->alias,
                                        'type' => $value->type,
                                        'object' => $value->object,
                                    ],//cate h2
                                ];

                                //process level 3
                                foreach ($ulDom->find('li a') as $aDom) {
                                    $name = $aDom->plaintext;
                                    $alias = Helper::convertToAlias($name . '-' . Helper::getNumberOnlyInString($aDom->href));
                                    $cateInDb = Cate::getCateByAlias($alias);

                                    $saveH3 = true;
                                    if ($cateInDb) {
                                        if ($cateInDb->type == Cate::$cateTypeRegister['main-index']['key'] && $cateInDb->object = Cate::$cateObjectRegister['index']['key']) {
                                            //đã tồn tại rồi thì thôi dùng cái này luôn
                                            //đã tồn tại thì cần update cate root
                                            $updateParent = true;
                                            if (isset($cateInDb->parents) && $cateInDb->parents) {
                                                foreach ($cateInDb->parents as $_p) {
                                                    if ($_p['alias'] == $value->alias) {
                                                        $updateParent = false;
                                                    }
                                                }
                                            }
                                            if ($updateParent) {
                                                $parents = $cateInDb->parents;
                                                $parents[] = [
                                                    'name' => $value->name,
                                                    'alias' => $value->alias,
                                                    'type' => $value->type,
                                                    'object' => $value->object,
                                                ];
                                                $updateToDb = [
                                                    'parents' => $parents,
                                                ];
                                                Cate::where('_id', $cateInDb->_id)->update($updateToDb);
                                                Debug::show($updateToDb, 'UPDATE H3', 'blue');
                                            }
                                            $saveH3 = false;
                                        } else {
                                            //tức là trùng với cái khác đang có thì thêm cái này nhưng biến tấu alias đi
                                            $alias = $alias . '-2';
                                        }
                                    }

                                    if ($saveH3) {

                                        $saveH3ToDb = [
                                            'name' => $name,
                                            'alias' => $alias,
                                            'status' => Cate::STATUS_ACTIVE,
                                            'type' => Cate::$cateTypeRegister['main-index']['key'],
                                            'object' => Cate::$cateObjectRegister['index']['key'],
                                            'parents' => $parentCateOfH3,
                                            'link_source' => $aDom->href
                                        ];
                                        Debug::show($saveH3ToDb, 'LEVEL 3', 'red');
                                        Cate::insert($saveH3ToDb);
                                    }
                                }


                            }


                        }

                    }

                }
            } else {
                Debug::show("KHONG LAY DC HTML");
            }
            //echo "\n$_GET['page']";
        }

    }

    function x()
    {



        echo Cate::where(['object' => Cate::$cateObjectRegister['subject']['key']])->count();
        for ($i = 1; $i <= 15; $i++) {
            echo "curl http://ebservice.com/_crawl/v2/get_index?page=" . $i . "<br/>";
        }
    }

    function get_post()
    {
        $fromMain = false;//false = from io_cate_tmp
        $_type = Book::TYPE_IS_LY_THUYET;


        $whereCate = [
            'type' => Cate::$cateTypeRegister['main-index']['key'],
            'object' => Cate::$cateObjectRegister['index']['key']
        ];
        if ($fromMain) {
            $listCate = Cate::where($whereCate);
            $listCate = Pager::getInstance()->getPagerSimple($listCate, 7);
        } else {
            $listCate = Cate::getTableTmp()->select();
            $listCate = Pager::getInstance()->getPagerSimple($listCate, 10);
        }
        if (!$listCate) {
            echo "\nHET HANG\n";
        }
        echo "\nPAGE:" . @$_GET['page'];
        foreach ($listCate as $key => $cate) {

            if (!$fromMain) {
                $cate = (object)$cate;
            }

            echo "\n=================\n";
            $cate->link_source = str_replace('http://loigiaihay.com', '', $cate->link_source);
            if ($_type == Book::TYPE_IS_BAI_TAP) {
                $link_source = 'http://loigiaihay.com' . $cate->link_source;
            } else {
                $link_source = 'http://loigiaihay.com' . $cate->link_source . '?t=1';
            }
//Debug::show($cate->name);
//Debug::show($link_source);
            $html = Helper::getUrlContent($link_source);
            if ($html) {
                $html = str_get_html($html);
                if ($html) {
                    /**
                     * todo:
                     * 1: đọc link cate với điều kiện link đó k phải link javascript:void(0)
                     * 2: trong link cần phân loại link cate level 1 và link root
                     */
                    //
                    //check xem có phải là trang cuối hay k
                    $isChapterRoot = $html->find('ul.list2 li', 0);
                    if ($isChapterRoot) {
                        echo "\nCATE ROOT:" . $link_source;
                        //Debug::show($cate->toArray(), 'Chapter root: ' . $link_source, 'red');
                    } else {
                        //Debug::show($cate->toArray(), 'Chapter sub: ' . $link_source, 'green');
                        //lấy dữ liệu bài viết
                        //todo xử lý parent ơ đây
                        // todo: lấy toàn bộ parent của các các cate cho đến tận menu môn học và cấp học để insert vào trường categories của post và đưa vào cate_tmp luôn
                        if ($fromMain) {
                            $categories = $cate->parents;
                            if (is_array($cate->parents) && $fromMain) {
                                foreach ($cate->parents as $k => $v) {
                                    $_catex = Cate::getCateByAlias($v['alias']);
                                    if (is_array($_catex->parents)) {
                                        /* foreach ($_catex->parents as $xxx) {
                                             $_xalias = Helper::convertToAlias($xxx['name']);
                                             if ($xxx['alias'] != $_xalias) {
                                                 Debug::show($_catex, 'XXXXXXXX', 'yellow');
                                             }
                                         }*/
                                        //Debug::show($v,'XXXX','blue');
                                        $categories = array_merge($categories, $_catex->parents);
                                    }
                                }
                                $categories = array_values($this->unique_multidim_array($categories, 'alias'));
                            }
                        } else {
                            $categories = $cate->categories;
                        }
                        //check xem có trang không, nếu có trang thì đếm xem bao nhiêu trang/ đưa thêm link vào mục io cate_link để cralw tiếp sau này và đây mặc định là phần bài tập
                        if ($fromMain) {
                            $havePage = $html->find('ul.paging li a');
                            if ($havePage) {
                                $numberLink = 0;
                                foreach ($havePage as $aDom) {
                                    if ($numberLink > 0) {
                                        // nhiều hơn 1 page thì mới lấy dữ liệu
                                        // trong trường hợp lấy link từ bảng này để crawl thì k insert cái này nữa
                                        if (!Cate::getTableTmp()->where('link_source', $aDom->href)->first()) {
                                            $saveToCateLink = $cate->toArray();
                                            unset($saveToCateLink['_id']);
                                            $saveToCateLink['link_source'] = $aDom->href;
                                            $saveToCateLink['categories'] = $categories;
                                            Cate::getTableTmp()->insert($saveToCateLink);
                                            echo "\nINSERT TO CATE TMP: " . $aDom->href;
                                        }
                                    }
                                    $numberLink++;
                                }
                            }
                        }
                        #region đọc lấy danh sách tin
                        $contentDom = $html->find('ul[class=list]', 1);
                        if ($contentDom) {

                            foreach ($contentDom->find('li') as $item) {
                                $aDom = $item->find('h3 a', 0);
                                if ($aDom) {
                                    if (!Book::where(['link_source' => $aDom->href])->first()) {
                                        $saveToPost = [];

                                        $saveToPost['name'] = $aDom->plaintext;
                                        $saveToPost['link_source'] = $aDom->href;

                                        $imageDom = $item->find('img', 0);
                                        //http://img.loigiaihay.com/picture/article/2014/0826/resize_365678731409019132_small.jpg
                                        //http://img.loigiaihay.com/picture/article/2014/0826/365678731409019132_small.jpg
                                        $saveToPost['image'] = $imageDom->src;

                                        $briefDom = $item->find('p.des_news', 0);
                                        $saveToPost['brief'] = $briefDom->plaintext;

                                        $saveToPost['object'] = Book::OBJECT_IS_SUBJECT;

                                        $saveToPost['type'] = $_type;

                                        $saveToPost['status'] = Book::STATUS_DRATF;

                                        // $saveToPost['categories'] = $this->unique_multidim_array($categories,'alias');
                                        $saveToPost['categories'] = $categories;
                                        $saveToPost['categories'][] = [
                                            'name' => $cate->name,
                                            'alias' => $cate->alias,
                                            'object' => $cate->object,
                                            'type' => $cate->type,
                                        ];
                                        //Debug::show($saveToPost,'$saveToPost','violet');
                                        Book::insert($saveToPost);
                                        echo "\nAdd POST DONE: " . $saveToPost['name'];
                                    } else {
                                        echo "\nDA TON TAI: " . $aDom->href;
                                        //nếu vô tình vớ được thằng này thì tính sao?
                                        //=> còn tính sao nữa: check parent rồi update thêm parent thôi
                                        //todo; tạm thời không làm gì cả kệ nó
                                    }
                                } else {
                                    echo "\nKHONG TIM THAY BAI VIET NAO: " . $link_source;
                                }
                            }
                        } else {
                            echo "\nKHONG CO LIST ITEM: " . $link_source;
                            //Debug::show($link_source, 'KHONG CO LIST ITEM', 'pink');
                        }
                        #endregion đọc lấy danh sách tin
                    }


                } else {
                    echo "\nKHONG DOM DC HTML: " . $link_source;
                    //Debug::show('KHONG DOM DC HTML:' . $link_source);
                }
            } else {
                echo "\nKHONG LAY DC HTML: " . $link_source;
                //Debug::show('KHONG LAY DC HTML:' . $link_source);
            }
        }
    }

    function download_image_avatar()
    {
        $listItems = Book::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        foreach ($listItems as $key => $value) {
            $linkImage = str_replace('/resize_', '/', $value->image);
            if ($linkImage) {
                $opts = array('http' => array('header' => "User-Agent:Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)\r\nCookie: wkdth_code=eden\r\n"));
                $context = stream_context_create($opts);
                $content = @file_get_contents(str_replace(" ", '%20', $linkImage), false, $context);
                if ($content) {
                    $folder = 'images/';//learn, news,...
                    if ($value->categories && is_array($value->categories)) {
                        foreach ($value->categories as $category) {
                            if ($category['type'] == 'cate') {
                                $folder .= $category['alias'] . '/';
                                break;
                            }
                        }
                        $folder .= substr(Helper::convertToAlias($value->categories[0]['name']), 0, 30) . '/';
                    }
                    $alias = Helper::convertToAlias($value->name);
                    $file = $folder . substr($alias, 0, 60) . Helper::getFileExtension($linkImage);
                    $root = public_path('/media/');
                    //echo $root;
                    if (!file_exists(($root . $folder))) {
                        @mkdir($root . $folder, 0777, true);
                    }
                    @file_put_contents($root . $file, $content);
                    echo "\n DONE: " . $linkImage . ' -> ' . $file;
                    $saveToPost = [
                        'avatar' => $file
                    ];
                    Book::where(['_id' => $value->_id])->update($saveToPost);
                    //Debug::show($saveToPost);
                } else {
                    echo "\n404==============40144040404040404040404========================";
                }
            } else {

            }

        }
        echo "\n=========DONE PAGE: " . @$_GET['page'] . "=============";


    }

    function get_content()
    {
        die($this->download_image_avatar());
        $listItems = Book::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        foreach ($listItems as $key => $post) {
            $link_source = $post->link_source;
            //Debug::show($link_source);
            $link_source = str_replace('http://loigiaihay.com', '', $link_source);
            $link_source = 'http://loigiaihay.com' . $link_source;
            $html = Helper::getUrlContent($link_source);
            if ($html) {
                $html = str_get_html($html);
                if ($html) {
                    $contentDom = $html->find('.detail_new', 0);
                    if ($contentDom) {
                        foreach ($contentDom->find('.box_gray') as $item) {
                            $item->outertext = '';
                        }

                        foreach ($contentDom->find('p') as $item) {
                            if (strtolower(trim($item->plaintext)) == 'loigiaihay.com') {
                                $item->outertext = '';
                            }
                            //$item->outertext = '';
                        }
                        foreach ($contentDom->find('div#fb_like_fb_new') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('script') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('style') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('ins') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('iframe') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('applet') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('link') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('embed') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('input') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('marquee') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('replace') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('button') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('textarea') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('select') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('object') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('frame') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('layer') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('bgsound') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('meta') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('noscript') as $item) {
                            $item->outertext = '';
                        }
                        foreach ($contentDom->find('a') as $item) {
                            $item->outertext = '<span>' . $item->innertext . '</span>';
                        }

                        $output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $contentDom->innertext);
                        $output = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $output);
                        $saveContent = [
                            'content' => $output,
                            'status' => Book::STATUS_CRALLW
                        ];
                        Book::where(['_id' => $post->_id])->update($saveContent);
                        echo "\nDone: " . $link_source;
                    } else {
                        echo "\nKHONG DOM DC CONTENT: " . $link_source;
                    }
                    // echo $output;
                }
            }
        }
        echo "\n=========DONE PAGE: " . @$_GET['page'] . "=============";
    }
}
