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

/**
 * Class vnews
 * @package App\Http\Controllers\_Dev
 * Cralw trang thông tin của vnexpress
 */
class vnews extends Controller
{
    function showMsg($msg)
    {
        echo "\n<br/>" . $msg;
    }

    function get_list_item()
    {
        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/tuyen-sinh';
        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/tuyen-sinh/page/2.html';
        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/tuyen-sinh/page/3.html';
        $cate_alias = ['thong-tin-tuyen-sinh'];


        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/du-hoc';
        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/du-hoc/page/2.html';
        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/du-hoc/page/3.html';
        $linkCrawl = 'https://vnexpress.net/tin-tuc/giao-duc/du-hoc/page/4.html';
        $cate_alias = ['tu-van-du-hoc'];


        $listCate = [];
        foreach ($cate_alias as $alias) {
            $_cate = Cate::getCateByAlias($alias);
            if ($_cate) {
                $listCate[] = [
                    'name' => $_cate->name,
                    'alias' => $_cate->alias,
                    'type' => $_cate->type,
                    'object' => $_cate->object,
                ];
            }
        }
        $content = Helper::getUrlContent($linkCrawl);
        if ($content) {
            $html = str_get_html($content);
            if ($html) {
                $domListItem = $html->find('.sidebar_1 article.list_news');
                if ($domListItem) {
                    foreach ($domListItem as $item) {
                        $domLink = $item->find('h3.title_news a', 0);
                        $name = trim($domLink->plaintext);
                        $link = $domLink->href;

                        $domAvatar = $item->find('div.thumb_art img', 0);
                        $object = 'data-original';
                        $avatar = $domAvatar->$object;
                        if (!$avatar) {
                            $avatar = $domAvatar->src;//replace _180x108
                        }
                        $avatar = str_replace('_180x108', '', $avatar);

                        $domBrief = $item->find('h4.description', 0);
                        $brief = trim($domBrief->plaintext);

                        $where = [
                            'link_source' => $link
                        ];
                        if (Post::where($where)->first()) {
                            //todo: da ton tai bai viet
                            $this->showMsg("Đã tồn tại tin theo link: " . $link);
                        } else {
                            $alias = Helper::convertToAlias($name);
                            if (Post::getPostByAlias($alias)) {
                                //todo đã tồn tại
                                $this->showMsg("Đã tồn tại tin theo alias: " . $alias);
                            } else {
                                $save = [
                                    'name' => $name,
                                    'alias' => $alias,
                                    'brief' => $brief,
                                    'content' => '',
                                    'categories' => $listCate,
                                    'seo' => '{"TITLE":"","DES":"","KEYWORD":"","IMAGE":"","ROBOTS":"NOINDEX,FOLLOW"}',
                                    'type' => Post::TYPE_IS_POST,
                                    'object' => Post::$objectRegister['news-edu']['key'],
                                    'avatar' => $avatar,
                                    'link_source' => $link,
                                    'status' => Post::STATUS_ACTIVE,
                                    //'created_at' => time(),//cái này cần theo thông tin
                                    'updated_at' => time(),
                                    'actived_at' => time(),
                                ];
                                Post::insert($save);
                                $this->showMsg("Ok con dê: " . $link);
                            }
                        }

                    }
                }
            } else {
                $this->showMsg("KHONG LAY DC HTML");
            }
        } else {
            $this->showMsg("KHONG LAY DC CONTENT");
        }

    }

    function get_content()
    {
        $listItems = Post::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        foreach ($listItems as $key => $value) {
            $linkCrawl = $value->link_source;
            $content = Helper::getUrlContent($linkCrawl);
            if ($content) {
                $html = str_get_html($content);
                if ($html) {
                    $domContainer = $html->find('.sidebar_1', 0);
                    $domBrief = $domContainer->find('h2.description', 0);
                    $saveUpdate = [];
                    if ($domBrief) {
                        $saveUpdate['brief'] = trim($domBrief->plaintext);
                    }

                    $domTime = $domContainer->find('.time', 0);
                    if ($domTime) {
                        $created_at = $domTime->plaintext;
                        $_time = explode('|', $created_at);
                        $_time = explode(',', $_time[0]);
                        if (isset($_time[1])) {
                            $_time = trim($_time[1]);
                            $saveUpdate['created_at'] = Helper::convertTimeToInt($_time);
                        }
                    }

                    $domContent = $domContainer->find('article.content_detail', 0);

                    foreach ($domContent->find('script') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('style') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('ins') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('iframe') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('applet') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('link') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('embed') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('input') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('marquee') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('replace') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('button') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('textarea') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('select') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('object') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('frame') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('layer') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('bgsound') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('meta') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('noscript') as $item) {
                        $item->outertext = '';
                    }
                    foreach ($domContent->find('div#box_shop_detail_v2') as $item) {
                        $item->outertext = '';
                    }

                    foreach ($domContent->find('table.tplCaption') as $item) {
                        $strImage = '';
                        $imgDom = $item->find('td');
                        //$imgCaptionDom = $item->find('td.caption');
                        $haveImage = false;
                        foreach ($imgDom as $img) {
                            $image = $img->find('img');
                            if ($image) {
                                foreach ($image as $x) {
                                    $haveImage = true;
                                    $x->removeAttribute('width');
                                    $x->removeAttribute('height');
                                    $x->removeAttribute('data-natural-width');
                                }
                                $captionDom = $img->parent()->parent()->find('td p.Image', 0);
                                if ($captionDom) {
                                    $strImage .= '<figure>' . $img->innertext . '<figcaption>' . $captionDom->innertext . '</figcaption></figure>';
                                    $captionDom->outertext = '';

                                } else {
                                    $strImage .= '<figure>' . $img->innertext . '</figure>';
                                }
                            }
                        }
                        if ($haveImage) {
                            $item->outertext = $strImage;
                        }
                    }

                    foreach ($domContent->find('a') as $item) {
                        $plaintext = $item->plaintext;
                        $item->rel = "nofollow";
                        $item->target = "_blank";
                        if (!in_array(Helper::convertToAlias($plaintext), ['chi-tiet', 'xem-chi-tiet'])) {
                            if (strpos($item->href, 'https://vnexpress.net/tin-tuc/giao-duc') !== false) {
                                $item->outertext = '<span class="tl">' . $item->innertext . '</span>';
                            }
                        }
                    }

                    $output = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $domContent->innertext);
                    $output = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $output);

                    $saveUpdate['content'] = $output;
                    //echo $output;
                    $this->showMsg("DONE: ");
                    Post::where(['_id' => $value->_id])->update($saveUpdate);

                } else {
                    $this->showMsg("KHONG LAY DC HTML");
                }
            } else {
                $this->showMsg("KHONG LAY DC CONTENT");
            }
            $this->showMsg("PAGE = " . @$_GET['page']);
        }
    }

    /**
     * Global
     */
    function download_avatar()
    {

        $listItems = Post::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        $this->showMsg("PAGE = " . @$_GET['page']);
        foreach ($listItems as $key => $value) {
            //avatar
            $linkImage = $value->avatar;
            if ($linkImage && strpos($linkImage, 'http') !== false) {
                $content = Helper::getUrlContent($linkImage);
                if ($content) {
                    $folder = 'pictures/';//learn, news,...
                    if ($value->categories && is_array($value->categories)) {
                        foreach ($value->categories as $category) {
                            if ($category['type'] == 'cate') {
                                $folder .= $category['alias'] . '/';
                                break;
                            }
                        }
                        //$folder .= substr(Helper::convertToAlias($value->categories[0]['name']), 0, 30) . '/';
                    }
                    $folder .= (Helper::getNumberOnlyInString($value->_id) % 10) . '/';
                    $alias = Helper::convertToAlias($value->name);
                    $file = $folder . substr($alias, 0, 60) . Helper::getFileExtension($linkImage);
                    $root = public_path('/media/');
                    //echo $root;
                    if (!file_exists(($root . $folder))) {
                        @mkdir($root . $folder, 0777, true);
                    }
                    @file_put_contents($root . $file, $content);

                    $saveToPost = [
                        'avatar' => $file
                    ];
                    $this->showMsg("DONE: " . $linkImage . ' -> ' . $file);
                    Post::where(['_id' => $value->_id])->update($saveToPost);
                    //Debug::show($saveToPost);
                } else {
                    $this->showMsg("404:" . $linkImage);
                }
            }
        }
    }

    /**
     * Global
     */
    function download_image_in_content()
    {
        $listItems = Post::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        foreach ($listItems as $key => $value) {
            $contentBook = $value->content;
            $html = str_get_html($contentBook);
            $image = $html->find('img');
            if ($image) {
                $numberImage = 0;
                foreach ($image as $item) {
                    $linkImage = $item->src;
                    if (strpos($item->src, 'https://media.hoc1h.com') === false) {
                        $numberImage++;

                        if (!$item->alt) {
                            $alt = $value->name;
                            if ($value->categories && is_array($value->categories)) {
                                $x = $value->categories;
                                shuffle($x);
                                $alt .= ' - ' . $x[0]['name'];
                            }
                            $item->alt = $alt . ' - hình số ' . $numberImage;
                        }
                        $item->removeAttribute('width');
                        $item->removeAttribute('height');
                        $content = Helper::getUrlContent($linkImage);
                        if ($content) {
                            $folder = 'pictures/';//learn, news,...
                            if ($value->categories && is_array($value->categories)) {
                                foreach ($value->categories as $category) {
                                    if ($category['type'] == 'cate') {
                                        $folder .= $category['alias'] . '/';
                                        break;
                                    }
                                }
                            }
                            $folder .= (Helper::getNumberOnlyInString($value->_id) % 10) . '/';
                            $imageObject = explode('/', $linkImage);
                            //$folder = $folder . ($numberInId % 10) . '/';
                            $file = $folder . end($imageObject);
                            //$file = $folder . substr($alias, 0, 60) . Helper::getFileExtension($linkImage);
                            $root = public_path('/media/');
                            //echo $root;
                            if (!file_exists(($root . $folder))) {
                                @mkdir($root . $folder, 0777, true);
                            }
                            if (file_exists($root . $file)) {
                                $this->showMsg("===============================TON TAI FILE==================================");
                            }
                            if (@file_put_contents($root . $file, $content)) {
                                $item->src = 'https://media.hoc1h.com/' . $file;
                            };
                            $this->showMsg("DONE: " . $linkImage . ' -> ' . $file);
                        }
                    } else {
                        $this->showMsg("IMAGE LOCAL: " . $linkImage);
                    }
                }
                $saveToPost = [
                    'content' => $html->innertext
                ];
                //echo $html->innertext;
                $this->showMsg("DONE: " . $value->name);
                // Debug::show($saveToPost);
                Post::where(['_id' => $value->_id])->update($saveToPost);
            } else {
                echo "\n+++++++++NO IMAGE++++++++++";
            }
        }
    }
}
