<?php

namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\SearchHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\Book;
use App\Http\Models\Logs;
use App\Http\Models\Post;
use Illuminate\Http\Request;
use App\Http\Models\Cate;
use App\Elibs\Pager;

require_once app_path('Elibs/simple_html_dom.php');

class v3 extends Controller
{
    function gen_alias()
    {

        $listItems = Book::select();
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 500);
        foreach ($listItems as $key => $value) {
            $alias = substr(Helper::convertToAlias($value->name), 0, 80);
            $postInDb = Book::getPostByAlias($alias);
            if ($postInDb) {
                if ($postInDb->id != $value->id) {
                    $alias = $alias . '-1';
                } else {
                    //là chính nó thì thôi k update nữa
                    $alias = '';
                }
            }
            $updateData = [];
            if ($alias) {
                $updateData['alias'] = $alias;
            } else {
                echo "\nNO UPDATE";
            }
            //update category đối với case post k có category môn học
            if ($value->categories) {
                $_saveCate = $value->categories;
                foreach ($value->categories as $item) {
                    $cate = Cate::getCateByAlias($item['alias']);
                    if ($cate) {
                        if ($cate->parents) {
                            foreach ($cate->parents as $p) {
                                $_saveCate[] = $p;
                            }
                        }
                    }
                }
                $_saveCate = Helper::unique_multidim_array($_saveCate, 'alias');
                $updateData['categories'] = $_saveCate;
            }
            if (isset($updateData) && $updateData) {
                Book::where(['_id' => $value->id])->update($updateData);
                echo "\nUPDATE DONE: " . $alias;
                //Debug::show($updateData);
            }

        }
        echo "\n=====PAGE: " . @$_GET['page'];
    }

    function get_image()
    {

        //Book::where(1)->update(['status'=>Book::STATUS_ACTIVE]);
        die();
        $listItems = Book::select();
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
                        $opts = array('http' => array('header' => "User-Agent:Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)\r\nCookie: wkdth_code=eden\r\n"));
                        $context = stream_context_create($opts);
                        $content = @file_get_contents(str_replace(" ", '%20', $linkImage), false, $context);
                        if ($content) {
                            $folder = 'pictures/';//learn, news,...
                            if ($value->categories && is_array($value->categories)) {
                                foreach ($value->categories as $category) {
                                    if ($category['type'] == 'cate') {
                                        $folder .= $category['alias'] . '/';
                                        break;
                                    }
                                }

                                $folder .= substr(Helper::convertToAlias($value->categories[0]['name']), 0, 30) . '/';
                            }
                            $numberInId = Helper::getNumberOnlyInString($value->_id);
                            //$alias = Helper::convertToAlias($value->name);
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
                                echo "\n===============================TON TAI FILE==================================";
                            }
                            if (@file_put_contents($root . $file, $content)) {
                                $item->src = 'https://media.hoc1h.com/' . $file;
                            };
                            echo "\n DONE: " . $linkImage . ' -> ' . $file;

                        }
                    } else {
                        echo "\n IMAGE LOCAL: : " . $linkImage;
                    }
                }
                $saveToPost = [
                    'content' => $html->innertext
                ];
                echo "\n DONE POST: " . $value->name;
                // Debug::show($saveToPost);
                Book::where(['_id' => $value->_id])->update($saveToPost);
            } else {
                echo "\n+++++++++NO IMAGE++++++++++";
            }
        }
    }

    function get_audio()
    {

        die();
        $listItems = Logs::getTableDocCrawl()->get();
        $content_file = '';
        foreach ($listItems as $key => $value) {
            $to = '/Volumes/Data/web/ebservice/public/media/' . $value['dest'];
            $_path = explode('/', $value['dest']);
            unset($_path[count($_path) - 1]);
            $path = implode('/', $_path);

            $content_file .= "\n";
            $_folder = '';
            foreach ($_path as $folder) {
                $_folder .= '/' . $folder;
                $content_file .= "\n";
                $content_file .= "mkdir /Volumes/Data/web/ebservice/public/media" . $_folder;
            }
            $content_file .= "\n";
            $content_file .= "wget -O " . $to . ' "' . $value['source'] . '"';
        }
        file_put_contents('/Volumes/Data/web/ebservice/_data/dl.sh', $content_file);

        die();
        $listItems = Book::select()->where('content', 'LIKE', '%img.loigiai%');
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 10);
        foreach ($listItems as $key => $value) {
            $contentBook = $value->content;
            $html = str_get_html($contentBook);
            $image = $html->find('audio');
            if ($image) {
                $numberItem = 0;
                foreach ($image as $item) {
                    $linkImage = $item->src;
                    if (strpos($item->src, 'https://media.hoc1h.com') === false) {
                        $numberItem++;

                        // $opts = array('http' => array('header' => "User-Agent:Mozilla/5.0 (Linux; Android 6.0.1; Nexus 5X Build/MMB29P) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.96 Mobile Safari/537.36 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)\r\nCookie: wkdth_code=eden\r\n"));
                        // $context = stream_context_create($opts);
                        //$content = @file_get_contents(str_replace(" ", '%20', $linkImage), false, $context);
                        //if ($content) {
                        $folder = 'audio/';//learn, news,...
                        if ($value->categories && is_array($value->categories)) {
                            foreach ($value->categories as $category) {
                                if ($category['type'] == 'cate') {
                                    $folder .= $category['alias'] . '/';
                                    break;
                                }
                            }

                            $folder .= substr(Helper::convertToAlias($value->categories[0]['name']), 0, 30) . '/';
                        }

                        $imageObject = explode('/', $linkImage);
                        //$folder = $folder . ($numberInId % 10) . '/';
                        $file = $folder . end($imageObject);
                        $newSrc = 'https://media.hoc1h.com/' . $file;
                        //todo: $item->src = đường dẫn mới ($file)
                        $item->src = $newSrc;
                        $saveFile = [
                            'source' => $linkImage,
                            'dest' => $file,
                            'alias' => $value->alias
                        ];
                        Logs::getTableDocCrawl()->insert($saveFile);
                        echo "\n DONE: " . $linkImage . ' -> ' . $file;

                        //}
                    } else {
                        echo "\n IMAGE LOCAL: : " . $linkImage;
                    }
                }
                $saveToPost = [
                    'content' => $html->innertext
                ];
                echo "\n DONE POST: " . $value->name;
                // Debug::show($saveToPost);
                Book::where(['_id' => $value->_id])->update($saveToPost);
            } else {
                echo "\n+++++++++NO IMAGE++++++++++";
            }
        }
        if ($listItems->isEmpty()) {
            echo "\n+++++++++NO ITEM++++++++++";
        }
        echo "\n+++++++++PAGE=" . @$_GET['page'] . "++++++++++";
    }

    function showMsg($msg)
    {
        echo "\n<br/>" . $msg;
    }

    function process_image_in_book()
    {
        die();
        $listItems = Book::select()->where('content', 'LIKE', '%%20%');
        //echo $listItems;
        //die($listItems);
        $listItems = Pager::getInstance()->getPagerSimple($listItems, 1000);
        foreach ($listItems as $key => $value) {
            $contentBook = $value->content;
            $html = str_get_html($contentBook);
            foreach ($html->find('img') as $item) {
                if (strpos($item->src, '%20') !== false) {
                    $link_related = str_replace('https://media.hoc1h.com/', '', $item->src);

                    $link_local = '/Volumes/Data/web/ebservice.com/public/media/' . $link_related;
                    $files = explode('/', $link_related);
                    $fileName = end($files);
                    $ext = Helper::getFileExtension($fileName);
                    $alias = str_ireplace($ext, '', $fileName);
                    // $this->showMsg($fileName.'-'.$ext);
                    $alias = Helper::convertToAlias($alias);
                    $link_related_new = str_ireplace($fileName, $alias . $ext, $link_related);
                   // $this->showMsg("OLD:" . $link_related);
                   // $this->showMsg("NEWS:" . $link_related_new);
                    $item->src = 'https://media.hoc1h.com/' . $link_related_new;
                    $link_local_new = '/Volumes/Data/web/ebservice.com/public/media/' . $link_related_new;
                    echo "<br/>mv '" . $link_local . "' " . $link_local_new;
                }
            }
            $saveToPost = [
                'content' => $html->innertext
            ];
           // echo "\n DONE POST: " . $value->alias;
            // Debug::show($saveToPost);
            Book::where(['_id' => $value->_id])->update($saveToPost);
        }
    }

}
