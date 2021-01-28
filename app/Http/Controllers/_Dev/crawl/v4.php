<?php

namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\SearchHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Book;
use App\Http\Models\Post;
use Illuminate\Http\Request;
use App\Http\Models\Cate;
use App\Elibs\Pager;

require_once app_path('Elibs/simple_html_dom.php');

class v4 extends Controller
{
    function get_post()
    {
        $content = file_get_contents('/Volumes/Data/web/ebservice/app/Http/Controllers/_Dev/crawl/data/list.zing.html');
        $html = str_get_html($content);
        $contentDom = $html->find('article');
        $category = Cate::getCateByAlias('tu-van-du-hoc');
        foreach ($contentDom as $item) {
            $aDom = $item->find('header p.title a', 0);
            $imgDom = $item->find('div.cover a img', 0);
            $name = trim($aDom->plaintext);
            $alias = Helper::convertToAlias($name);
            if (Post::getPostByAlias($alias)) {
                Debug::show('Đã có item', '', 'green');
            } else {
                $save = [
                    'name' => $name,
                    'alias' => $alias,
                    'brief' => $item->find('header p.summary', 0)->plaintext,
                    'content' => '',
                    'categories' => [
                        [
                            'name' => $category->name,
                            'alias' => $category->alias,
                            'type' => $category->type,
                            'object' => $category->object,
                        ]
                    ],
                    'seo' => '{"TITLE":"","DES":"","KEYWORD":"","IMAGE":"","ROBOTS":"NOINDEX,FOLLOW"}',
                    'type' => Post::TYPE_IS_POST,
                    'object' => Post::$objectRegister['news-edu']['key'],
                    'avatar' => $imgDom->src,
                    'link_source' => 'https://news.zing.vn' . $aDom->href,
                    'status' => Post::STATUS_ACTIVE,
                    'created_at' => time(),
                    'updated_at' => time(),
                    'actived_at' => time(),
                ];
                Post::insert($save);
                Debug::show($save);
            }
        }
    }

    function get_content()
    {
        $listItems = Post::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        foreach ($listItems as $key => $value) {
            $link_source = $value->link_source;
            $html = Helper::getUrlContent($link_source);
            if ($html) {
                $html = str_get_html($html);
            } else {
                echo "\nBUG: " . $link_source;
            }

            if ($html) {
                $contentDom = $html->find('.the-article-body', 0);
                if ($contentDom) {
                    foreach ($contentDom->find('table.article') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($contentDom->find('table.picture') as $item) {
                        $strImage = '';
                        $imgDom = $item->find('td.pic');
                        //$imgCaptionDom = $item->find('td.caption');
                        foreach ($imgDom as $img) {
                            foreach ($img->find('img') as $x) {
                                $x->removeAttribute('width');
                                $x->removeAttribute('height');
                            }
                            $captionDom = $img->parent()->parent()->find('td.caption',0);
                            if($captionDom) {
                                $strImage .= '<div class="content-image">' . $img->innertext . '<div class="caption">' . $captionDom->innertext . '</div></div>';
                            }else{
                                $strImage .= '<div class="content-image">' . $img->innertext . '</div>';
                            }

                        }
                        $item->outertext = $strImage;
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
                    foreach ($contentDom->find('figure') as $item) {
                        $item->outertext = '<figure class="hoc1h-video">' . $item->innertext . '</figure>';
                    }
                    foreach ($contentDom->find('a') as $item) {
                        if (strpos($item->href, 'http') === false) {
                            $item->href = 'https://news.zing.vn' . $item->href;
                        }
                    }
                    $saveContent = [
                        'content' => $contentDom->innertext
                    ];
                    Post::where('_id', $value->id)->update($saveContent);
                    echo "\nDONE: " . $link_source;
                    //Debug::show($saveContent, $link_source);
                } else {
                    //remove cái này đi cho nhẹ nợ
                    Post::where('_id', $value->id)->delete();
                    echo "\nNO DOM CONTENT: " . $link_source;
                }
            } else {
                Post::where('_id', $value->id)->delete();
                echo "\nKHONG LAY DC CONTENT: " . $link_source;
            }
        }
        echo "\nPAGE: " . @$_GET['page'];
    }



}
