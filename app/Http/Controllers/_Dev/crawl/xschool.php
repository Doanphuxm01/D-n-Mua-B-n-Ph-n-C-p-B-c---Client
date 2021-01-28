<?php

namespace App\Http\Controllers\_Dev;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\SearchHelper;
use App\Http\Controllers\Controller;
use App\Http\Models\MetaData;
use App\Http\Models\School;
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
class xschool extends Controller
{
    function showMsg($msg)
    {
        echo "\n<br/>" . $msg;
    }

    function get_name_school()
    {
        $cate_alias = ['truong-dai-hoc'];
        $file = 'truong-dai-hoc.txt';
        $level = 'dai-hoc';

        $cate_alias = ['hoc-vien'];
        $file = 'hoc-vien.txt';
        $level = 'dai-hoc';


        $cate_alias = ['ngoai-cong-lap'];
        $file = 'ngoai-cong-lap.txt';
        $level = 'dai-hoc';


        $cate_alias = ['truong-quan-su-cong-an'];
        $file = 'quan-su.txt';
        $level = 'dai-hoc';

        $cate_alias = ['dai-hoc-cap-vung-dia-phuong'];
        $file = 'dai-hoc-cap-vung-dia-phuong.txt';
        $level = 'dai-hoc';

        $cate_alias = ['truong-du-bi-dai-hoc-dan-toc'];
        $file = 'truong-du-bi-dai-hoc-dan-toc.txt';
        $level = 'dai-hoc';

        $cate_alias = ['cao-dang-chuyen-nghiep'];
        $file = 'cao-dang-chuyen-nghiep.txt';
        $level = 'cao-dang';


        $cate_alias = ['cao-dang-chuyen-nghiep', 'ngoai-cong-lap'];
        $file = 'cao-dang-ngoai-cong-lap.txt';
        $level = 'cao-dang';


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
        $content = file_get_contents('/Volumes/Data/web/ebservice.com/app/Http/Controllers/_Dev/crawl/data/' . $file);
        $html = str_get_html($content);
        $location = [
            [
                'name' => 'Việt Nam',
                'alias' => Helper::convertToAlias('viet-nam'),
                'type' => 'country'
            ]
        ];
        if ($html) {
            foreach ($html->find('li a') as $item) {
                $name = $item->plaintext;
                $alias = Helper::convertToAlias($name);
                $inDb = School::getByAlias($alias);
                if ($inDb) {
                    //todo: xem xets vieecj update cate
                    $listCateInDb = $inDb->categories;
                    if ($listCateInDb) {
                        foreach ($listCateInDb as $cate) {
                            //Debug::show($cate);
                            $listCate[] = $cate;
                        }
                    }
                    $cateSave = Helper::unique_multidim_array($listCate, 'alias');
                    School::where('_id', $inDb->_id)->update([
                        'categories' => $cateSave,
                        'level' => $level,
                        'location' => $location,
                    ]);
                    $this->showMsg("UPDATE CATE DONE: " . $inDb->name);
                } else {
                    $saveToDb = [
                        'name' => $name,
                        'alias' => $alias,
                        'categories' => $listCate,
                        'level' => $level,
                        'location' => $location,
                        'link_source' => 'https://vi.wikipedia.org' . $item->href,
                    ];
                    // Debug::show($saveToDb);
                    School::insert($saveToDb);
                    $this->showMsg("ADD NEW DONE: " . $name);
                }

            }
        } else {
            $this->showMsg("KHONG LAY DC HTML");
        }

    }

    function xget_name_school()
    {
        $cate_alias = ['truong-dai-hoc'];
        $file = 'truong-dai-hoc.txt';
        $level = 'dai-hoc';

        $cate_alias = ['hoc-vien'];
        $file = 'hoc-vien.txt';
        $level = 'dai-hoc';


        $cate_alias = ['ngoai-cong-lap'];
        $file = 'ngoai-cong-lap.txt';
        $level = 'dai-hoc';


        $cate_alias = ['truong-quan-su-cong-an'];
        $file = 'quan-su.txt';
        $level = 'dai-hoc';

        $cate_alias = ['dai-hoc-cap-vung-dia-phuong'];
        $file = 'dai-hoc-cap-vung-dia-phuong.txt';
        $level = 'dai-hoc';

        $cate_alias = ['truong-du-bi-dai-hoc-dan-toc'];
        $file = 'truong-du-bi-dai-hoc-dan-toc.txt';
        $level = 'dai-hoc';

        $cate_alias = ['cao-dang-nghe'];
        $file = 'cao-dang-nghe.txt';
        $level = 'cao-dang';


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
        $content = file_get_contents('/Volumes/Data/web/ebservice.com/app/Http/Controllers/_Dev/crawl/data/' . $file);
        $html = str_get_html($content);
        if ($html) {
            foreach ($html->find('dl') as $dl) {
                $domLocation = $dl->find('dt', 0);
                $location = [
                    [
                        'name' => $domLocation->plaintext,
                        'alias' => Helper::convertToAlias($domLocation->plaintext),
                        'type' => 'region'
                    ],
                    [
                        'name' => 'Việt Nam',
                        'alias' => Helper::convertToAlias('viet-nam'),
                        'type' => 'country'
                    ]
                ];
                $domItem = $dl->find('li a');
                foreach ($domItem as $item) {
                    $name = $item->plaintext;
                    $alias = Helper::convertToAlias($name);
                    $inDb = School::getByAlias($alias);
                    if ($inDb) {
                        //todo: xem xets vieecj update cate
                        $listCateInDb = $inDb->categories;
                        if ($listCateInDb) {
                            foreach ($listCateInDb as $cate) {
                                //Debug::show($cate);
                                $listCate[] = $cate;
                            }
                        }
                        $cateSave = Helper::unique_multidim_array($listCate, 'alias');
                        School::where('_id', $inDb->_id)->update([
                            'categories' => $cateSave,
                            'level' => $level,
                        ]);
                        $this->showMsg("UPDATE CATE DONE: " . $inDb->name);
                    } else {
                        $saveToDb = [
                            'name' => $name,
                            'alias' => $alias,
                            'categories' => $listCate,
                            'level' => $level,
                            'location' => $location,
                            'link_source' => 'https://vi.wikipedia.org' . $item->href,
                        ];
                        Debug::show($saveToDb);
                        School::insert($saveToDb);
                        $this->showMsg("ADD NEW DONE: " . $name);
                    }
                }
            }
        } else {
            $this->showMsg("KHONG LAY DC HTML");
        }

    }

    function get_info_wiki()
    {
        $this->showMsg('------PAGE: ' . @$_GET['page']);
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 20);
        foreach ($items as $key => $value) {
            if (!$value->content) {
                $link_source = $value->link_source;
                if (strpos($link_source, 'redlink=1') !== false) {
                    $saveToDb = [
                        'link_source' => ''
                    ];
                    School::where('_id', $value->_id)->update($saveToDb);
                    $this->showMsg('REMOVE LINK SOURCE NO CONTENT: ' . $link_source);

                } else if ($link_source) {
                    $content = Helper::getUrlContent($link_source);
                    if ($content) {
                        $html = str_get_html($content);
                        if ($html) {
                            $saveToDb = [];

                            #region box info
                            $domInfoBox = $html->find('table.infobox', 0);
                            if ($domInfoBox) {
                                $domAvatar = $domInfoBox->find('img', 0);
                                if ($domAvatar) {
                                    $saveToDb['avatar'] = 'https:' . $domAvatar->src;
                                }
                                $saveInfoExtra = [];
                                foreach ($domInfoBox->find('tr') as $tr) {
                                    $domKey = $tr->find('th', 0);
                                    $domValue = $tr->find('td', 0);
                                    if ($domKey && $domValue) {
                                        $_key = $domKey->plaintext;
                                        $_value = $domValue->plaintext;
                                        if (in_array(Helper::convertToAlias($_key), ['website', 'trang-web'])) {
                                            $profile_online = [
                                                'website' => $_value,
                                                'fanpage' => '',
                                            ];
                                            $saveToDb['profile_online'] = json_encode($profile_online);
                                        }
                                        $saveInfoExtra[] = [
                                            'key' => $_key,
                                            'value' => $_value,
                                        ];
                                    }
                                }
                                $saveToDb['info'] = $saveInfoExtra;
                                //Debug::show($saveInfoExtra);
                            }
                            #endregion box info

                            $domContent = $html->find('.mw-parser-output', 0);
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
                            foreach ($domContent->find('.toc') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.infobox') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.reference') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.mw-editsection') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.magnify') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.plainlinks') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.Z3988') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.reflist') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.navbox') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.noprint') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('.dablink') as $item) {
                                $item->outertext = '';
                            }
                            foreach ($domContent->find('#stub') as $item) {
                                $item->outertext = '';
                            }

                            foreach ($domContent->find('h2 span') as $item) {
                                $_id = $item->id;
                                $_id = Helper::convertToAlias($_id);
                                if (in_array($_id, ['chu-thich', 'lien-ket-ngoai',
                                    'xem-them',
                                    'tham-khao',
                                ])) {
                                    $_parent = $item->parent();
                                    $_parent->next_sibling()->outertext = '';
                                    $_parent->outertext = '';

                                }
                            }
                            if (1 == 1) {
                                foreach ($domContent->find('.thumb') as $item) {
                                    // $strImage = '';
                                    foreach ($domContent->find('img') as $img) {
                                        if (strpos($img->src, 'Flag_of_the_People') === false) {
                                            if (strpos($img->src, '//') === 0) {
                                                $img->src = 'https:' . $img->src;
                                            }
                                            if (1 == 1) {
                                                $img->src = str_replace('/300px-', '/600px-', $img->src);
                                                $img->src = str_replace('/400px-', '/600px-', $img->src);
                                                $img->src = str_replace('/120px-', '/600px-', $img->src);
                                                $img->src = str_replace('/220px-', '/600px-', $img->src);
                                                $img->src = str_replace('/277px-', '/600px-', $img->src);
                                            }
                                            $_parent = $img->parent()->parent();
                                            $captionDom = $_parent->find('div.thumbcaption', 0);
                                            if ($captionDom) {
                                                $strImage = '<figure><img src="' . $img->src . '" class="wiki-image"/><figcaption>' . $captionDom->plaintext . '</figcaption></figure>';
                                                $captionDom->outertext = '';

                                            } else {
                                                $strImage = '<figure><img src="' . $img->src . '" class="wiki-image"/></figure>';
                                            }
                                            $_parent->outertext = $strImage;

                                        }
                                        //
                                        // $img->outertext = '<figure><img src="' . $img->src . '" class="wiki-image"/></figure>';
                                    }
                                    //$item->outertext = $strImage;
                                }
                            }

                            foreach ($domContent->find('a') as $item) {
                                $_href = $item->href;
                                $item->target = "_blank";
                                $item->rel = "nofollow";
                                if (strpos($_href, 'redlink=1') !== false) {
                                    //todo: remove link này
                                    $item->outertext = '' . $item->plaintext . '';
                                }
                                if (strpos($_href, '/wiki/') !== false) {
                                    //todo: remove link này
                                    $item->outertext = '' . $item->plaintext . '';
                                }
                                if (strpos($_href, '#') === 0) {
                                    $item->outertext = '';
                                }

                            }

                            $content = $domContent->innertext;
                            $content = preg_replace('/<!--(.*)-->/Uis', '', $content);
                            $content = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
                            //$content = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $content);
                            $saveToDb['content'] = $content;

                            School::where('_id', $value->_id)->update($saveToDb);

                            $this->showMsg("LAY DC HTML: " . $link_source);
                        } else {
                            $this->showMsg("KHONG LAY DC HTML: " . $link_source);
                        }

                    } else {
                        $this->showMsg("KHONG LAY DC NOI DUNG: " . $link_source);
                    }
                } else {
                    $this->showMsg("KHONG TON TAI LINK SOURCE: " . $link_source);
                }
            } else {
                $this->showMsg("DA CO CONTENT: " . $value->name);
            }
        }
    }

    function convert_brief()
    {
        die();
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);
        foreach ($items as $key => $value) {
            if (isset($value->profile_online)) {
                $profile_online = json_decode($value->profile_online);
                $profile_online->phone = '';
                $profile_online->email = '';
                $saveToDb = [
                    'profile_online' => $profile_online
                ];
                Debug::show($saveToDb);
                School::where('_id', $value->_id)->update($saveToDb);
            }
        }
        die();
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);
        foreach ($items as $key => $value) {
            Debug::show($value->toArray());
            $dt = $value->toArray();
            unset($dt['_id']);
            School::where('_id', $value->_id)->delete();
            School::tmp()->insert($dt);
        }
        die();
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);
        foreach ($items as $key => $value) {
            Debug::show($value->toArray());
            $dt = $value->toArray();
            $location = [];
            foreach ($value->location as $ks => $vs) {
                $location[$ks] = $vs;
                if (!isset($vs['object'])) {
                    $location[$ks]['object'] = 'school';
                    $location[$ks]['type'] = 'location_' . $vs['type'];
                }
            }
            $meta_data = array_merge($value->categories, $location);
            $dt['meta_data'] = $meta_data;
            unset($dt['categories']);
            unset($dt['location']);
            Debug::show($meta_data);
            School::where('_id', $value->_id)->delete();
            School::insert($dt);
        }
        die();
        $items = MetaData::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);
        foreach ($items as $key => $value) {
            if ($value->object == 'location') {
                $saveToDb = [
                    'object' => 'school',
                    'type' => 'location_' . $value->type,
                ];
                MetaData::where('_id', $value->_id)->update($saveToDb);
            }
        }

        die();
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);
        foreach ($items as $key => $value) {
            $saveToDb = [];
            $meta_data = array_merge($value->categories, $value->location);
            $saveToDb['meta_data'] = $meta_data;
            School::where('_id', $value->_id)->update($saveToDb);
        }
        die();
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);
        $location = [
            [
                'name' => 'Việt Nam',
                'alias' => Helper::convertToAlias('viet-nam'),
                'type' => 'country'
            ]
        ];
        foreach ($items as $key => $value) {
            if (!$value->location) {
                $saveToDb = [];
                $saveToDb['location'] = $location;
                School::where('_id', $value->_id)->update($saveToDb);
            }
        }

        die();
        $saveToDb['status'] = School::STATUS_ACTIVE;
        School::where(1)->update($saveToDb);
        die();
        $items = School::select();
        $items = Pager::getInstance()->getPagerSimple($items, 700);

        foreach ($items as $key => $value) {
            if ($value->content) {
                $html = str_get_html($value->content);
                $domBrief = $html->find('p', 0);
                $saveToDb = [];
                $saveToDb['brief'] = $domBrief->plaintext;
                School::where('_id', $value->_id)->update($saveToDb);
            }
        }
    }


}
