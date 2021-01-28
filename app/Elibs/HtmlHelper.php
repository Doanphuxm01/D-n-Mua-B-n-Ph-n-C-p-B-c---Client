<?php
namespace App\Elibs;
/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 16/12/2014
 * Time: 14:37
 */
class HtmlHelper
{
    private static $instance = FALSE;
    private $seoMeta = array('title' => '',
        'des' => '',
        'keywords' => '',
        'image' => '',
        'images' => [],
        'robots' => 'INDEX,FOLLOW,ARCHIVE',);
    static $clientVersion = '8686';

    public function __construct()
    {
        //self::$clientVersion = rand(1, 100000);
        self::$instance =& $this;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            new self();
        }

        return self::$instance;
    }

    public function setTitle($title = '')
    {
        $this->seoMeta['title'] = $title;
        return $this;
    }

    public function appendTitle($sub_title = '')
    {
        $this->seoMeta['title'] .= $sub_title;
        return $this;
    }

    public function getTitle()
    {
        return str_replace(array('"', "'"), '', $this->seoMeta['title']);
    }

    public function setSiteDes($content = '')
    {
        $this->seoMeta['des'] = $content;
        return $this;
    }

    public function appendSiteDes($subContent = '')
    {
        $this->seoMeta['des'] .= $subContent;
        return $this;
    }

    public function getDes()
    {
        return $this->seoMeta['des'];
    }

    public function setKeyWords($keyword = '')
    {
        $this->seoMeta['keywords'] = $keyword;
        return $this;
    }

    public function getKeyWords()
    {
        return $this->seoMeta['keywords'];
    }

    public function setRobots($robots = true)
    {
        if ($robots) {
            $this->seoMeta['robots'] = 'INDEX,FOLLOW,ARCHIVE';
        } else {
            $this->seoMeta['robots'] = 'NOINDEX,NOFOLLOW,NOARCHIVE';
        }
        return $this;
    }

    public function getRobots()
    {
        return $this->seoMeta['robots'];
    }

    public function getSeoMeta()
    {
        return $this->seoMeta;
    }

    public function getSeoSetting()
    {
        if (isset($_POST['SEO'])) {
            if ($_POST['SEO']) {
                $arrayKey = ['TITLE', 'DES', 'IMAGE', 'ROBOTS', 'KEYWORD','H1'];
                foreach ($_POST['SEO'] as $key => $val) {
                    if (!in_array($key, $arrayKey)) {
                        unset($_POST['SEO'][$key]);
                    } else {
                        $_POST['SEO'][$key] = strip_tags($val);
                    }
                }
                return json_encode($_POST['SEO']);
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function setSeoSetting($setting, $build = false)
    {
        if ($setting) {
            $setting = json_decode($setting, true);
            if (isset($setting['TITLE']) && $title = strip_tags($setting['TITLE'])) {
                if ($build) {
                    $this->setTitle($title);
                }
                //todo: code tiếp phần này return luôn những thẻ meta html kèm theo các thông tin seo tương ứng
            }
            return $setting;
        }
    }

    /***
     * @param $content
     *
     * @return array
     * @note; lấy toàn bộ link trong nội dung (link và anchol text)
     */
    public function getAllLinkInContent($content)
    {
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
        if (preg_match_all("/$regexp/siU", $content, $matches)) {
            if (isset($matches[2]) && isset($matches[3])) {
                foreach ($matches[2] as $key => $val) {
                    if (isset($matches[3][$key])) {
                        $_link[] = [
                            'text' => $matches[3][$key],
                            'link' => $val,
                        ];
                    }
                }
            }
            return isset($_link) ? $_link : [];
        }
        return [];
    }

    /***
     * @param $content
     *
     * @return array
     * @note; lấy toàn bộ src img trong nội dung (link và anchol text)
     */
    public function getAllImageInContent($content)
    {
        preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $content, $matches);
        Debug::show($matches);
    }


    function getXmlRpc($domain, $cms = "wp")
    {
        if (!class_exists('IXR_Client')) {
            require_once app_path('Elibs/IXR_Library.php');
        }
        if ($cms == 'wp') {
            $xmlrpc = $domain . '/xmlrpc.php';
        }
        $client = new \IXR_Client($xmlrpc);
        return $client;
    }

    function getHtmlDom($link)
    {
        if (!function_exists('file_get_html')) {
            require_once app_path('Elibs/simple_html_dom.php');
        }

        $html = file_get_html($link);
        return $html;
    }

    function setCssLink($link)
    {
        return '<link href="' . url($link) . '?v=' . static::$clientVersion . '" rel="stylesheet">';
    }
    function setPreLoadCssLink($link)
    {
        return '<link rel="preload" href="'.url($link).'" as="style" onload="this.rel=\'stylesheet\'">
    <noscript><link rel="stylesheet" href="'.url($link).'"></noscript>';
       // return '<link href="' . url($link) . '?v=' . static::$clientVersion . '" rel="stylesheet">';
    }

    function setLinkJs($link)
    {
        return '<script type="text/javascript" src="' . url($link) . '?v=' . static::$clientVersion . '"></script>';
    }

    function setLinkJsAsync($link)
    {
        return '<script type="text/javascript" async src="' . url($link) . '?v=' . static::$clientVersion . '"></script>';
    }
    /**
     *
     */
}
HtmlHelper::$clientVersion = time();