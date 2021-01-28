<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;

class Media extends BaseModel
{
    public $timestamps = FALSE;
    const table_name = 'io_media';
    protected $table     = self::table_name;
    static    $unguarded = TRUE;

    private static $mediaUpload = [
        'folder' => 'upload/',
        'path'   => '',
    ];


    /***
     * Định nghĩa kiểu của đối tượng: value của trường type
     */
    const TYPE_IS_IMAGE = 'image';
    const TYPE_IS_DOC = 'doc';
    const TYPE_IS_VIDEO = 'video';

    /***
     * @return mixed
     */
    static function getUploadPath($folder = '')
    {
        return public_path(config('filesystems.media_folder') . "/upload/") . $folder;
    }

    /***
     * @return mixed
     */
    static function getUploadFolder($folder, $extra = TRUE)
    {
        if ($extra) {
            return self::$mediaUpload['folder'] . $folder;
        } else {
            return $folder;
        }
    }

    /***
     * @param $src
     * @param $no_image
     *
     * @return mixed
     */
    static function buildImageLink($src, $no_image = '')
    {
        if (!$src) {
            if (!$no_image) {
                return url('images/no-image.jpg?ver=2580524613');
            } else {
                return $no_image;
            }
        }
        if (Helper::isLink($src)) {
            return $src;
        }

        return config('filesystems.media_domain') . $src . '?ver=' . HtmlHelper::$clientVersion;
    }

    /***
     * @param $src
     * @param $width
     * @param $height
     * @param $type
     *
     * @note: todo: tạm thời gen ảnh thumb tại đây
     */
    static function getImageSrc($src, $width = 0, $height = 0, $type = '')
    {

        if (!$src) {
            return url('/images/no-image.jpg') . '?ver=888';
        }
        if (Helper::isLink($src)) {
            return $src;
        }
        if ($width == 0 && $height == 0) {
            return self::buildImageLink($src);
        }
        $_src = explode('/', $src);
        $_src[0] = 'thumbs/' . $width . 'x' . $height;//replace thư mục images gốc thành thư mục thumbs/size
        $src = implode('/', $_src);

        return config('filesystems.media_domain') . $src;
    }

    static function getFileLink($link)
    {
        if (Helper::isLink($link)) {
            return $link;
        }
        return url('/data/' . $link);
    }


}
