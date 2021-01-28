<?php

namespace App\Elibs;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

/**
 * Created by PhpStorm.
 * User: ngannv
 * Date: 8/16/15
 * Time: 7:58 PM
 */
class Helper
{
    /***
     * @param $email
     *
     * @return bool
     * @note: validate xem có phải là email hay không?
     */
    static function isEmail($email)
    {
        return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $email)) ? FALSE : TRUE;
    }

    static function genCorlorRand($string)
    {
        return '#' . substr(md5($string), 0, 6);
    }

    /***
     * @param $number
     *
     * @return int
     * @note: validate số điện thoại di động
     */
    static function isMobileNumber($number)
    {
        return preg_match("/^\+?\d{9,12}$/i", trim($number));
    }

    public static function formatMoney($stringNumber, $sep = '.', $₫ = ' ₫') {
        $stringNumber = (double)$stringNumber;
        if(!$stringNumber){
            return $stringNumber;
        }
        return number_format($stringNumber, 0, ',', $sep).$₫;
    }

    public static function calcDiscount($finalPrice, $regularPrice, $format = false) {
        $finalPrice = (int)$finalPrice;
        $regularPrice = (int)$regularPrice;
        if(!$finalPrice || !$regularPrice){
            return 0;
        }
        if($format) {
            return self::formatMoney($regularPrice - $finalPrice);
        }
        return round(100 - ($finalPrice/$regularPrice*100));
    }

    /***
     * @param $number
     *
     * @return int
     * @note: validate số điện thoại cố định
     */
    static function isPhoneNumber($number)
    {
        return self::isMobileNumber($number);
    }

    /***
     * @param string $string
     *
     * @return int
     * @note: validate tài khoản
     */
    static function isAccount($string)
    {
        return preg_match('/^[A-z0-9]+[._]?[A-z0-9]+$/', $string);
    }

    /***
     * @param string $string
     *
     * @return int
     * @note: validate tài khoản
     */
    static function isCanCuocCongDan($string)
    {
        return preg_match('/^\(\d{3}[0-9]{6}\)|(^\d{1,2}[0-9]{7})|(^\d{9})|(^\d{12})$/', $string);
    }

    /**
     * Validate chuỗi ngày tháng có đúng định dạng hay ko
     * @param $str
     * @param string $format
     * @return bool
     */
    public static function isDatetime($str, $format = 'd/m/Y')
    {
        try {
            $d = Carbon::createFromFormat($format, $str);
        } catch (\InvalidArgumentException $e) {
            return FALSE;
        }

        return $d && $d->format($format) == $str;
    }

    static function getFileType($file_name)
    {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        // if()
    }

    /***
     * @param string $time : ví dụ dd/mm/yyyy
     * @param string $split : ví dụ '/' hoặc '-'
     *
     * @return int
     */
    static function convertTimeToInt($time, $split = '/', $hour = '')
    {
        if (!$time) {
            return FALSE;
        }
        $t = explode($split, trim($time));
        if ($hour) {
            $h = explode(':', trim($hour));
        }


        return mktime(isset($h[0]) ? $h[0] : 0, isset($h[1]) ? $h[1] : 0, isset($h[2]) ? $h[2] : 0, @$t[1], @$t[0], @$t[2]);
    }

    /***
     * @param       $number
     * @param array $point
     *
     * @return mixed
     * @note: Convert số dạng format sang số thường ví dụ: 1.000 => 1000 hoặc 1,000 =>1000
     * @call: Helper::getInstance()->convertStringToNumber(1.000);
     */
    public static function convertStringToNumber($number, $point = [',', '.', ' '])
    {
        //todo: có thể bổ sung thêm việc xóa các ký tự khác "Không phải số"
        return str_replace($point, '', $number);
    }

    /***
     * @param text $text
     *
     * @return mixed
     * @note: Sử dụng trước khi đưa một text vào db
     * @call: Helper::getInstance()->replaceMQ('abc');
     */
    public static function replaceMQ($text)
    {
        $text = str_replace("\'", "'", $text);
        $text = str_replace("'", "''", $text);

        return $text;
    }

    public static function convertDateTime($strDate = "", $strTime = "")
    {
        //Break string and create array date time
        $strDate = str_replace("/", "-", $strDate);
        $strDateArray = explode("-", $strDate);
        $countDateArr = count($strDateArray);
        $strTime = str_replace("-", ":", $strTime);
        $strTimeArray = explode(":", $strTime);
        $countTimeArr = count($strTimeArray);
        //Get Current date time
        $today = getdate();
        $day = $today["mday"];
        $mon = $today["mon"];
        $year = $today["year"];
        $hour = $today["hours"];
        $min = $today["minutes"];
        $sec = $today["seconds"];
        //Get date array
        switch ($countDateArr) {
            case 2:
                $day = intval($strDateArray[0]);
                $mon = intval($strDateArray[1]);
                break;
            case $countDateArr >= 3:
                $day = intval($strDateArray[0]);
                $mon = intval($strDateArray[1]);
                $year = intval($strDateArray[2]);
                break;
        }
        //Get time array
        switch ($countTimeArr) {
            case 2:
                $hour = intval($strTimeArray[0]);
                $min = intval($strTimeArray[1]);
                break;
            case $countTimeArr >= 3:
                $hour = intval($strTimeArray[0]);
                $min = intval($strTimeArray[1]);
                $sec = intval($strTimeArray[2]);
                break;
        }
        //Return date time integer
        if (@mktime($hour, $min, $sec, $mon, $day, $year) == -1) return $today[0];
        else return mktime($hour, $min, $sec, $mon, $day, $year);
    }


    /**
     * [removeAccent Function xoa dau tieng viet]
     *
     * @param  [type] $mystring [description]
     * @return mixed [type]     [description]
     */
    public static function removeAccent($mystring)
    {
        $marTViet = [
            // Chữ thường
            "à", "á", "ạ", "ả", "ã", "â", "ầ", "ấ", "ậ", "ẩ", "ẫ", "ă", "ằ", "ắ", "ặ", "ẳ", "ẵ",
            "è", "é", "ẹ", "ẻ", "ẽ", "ê", "ề", "ế", "ệ", "ể", "ễ",
            "ì", "í", "ị", "ỉ", "ĩ",
            "ò", "ó", "ọ", "ỏ", "õ", "ô", "ồ", "ố", "ộ", "ổ", "ỗ", "ơ", "ờ", "ớ", "ợ", "ở", "ỡ",
            "ù", "ú", "ụ", "ủ", "ũ", "ư", "ừ", "ứ", "ự", "ử", "ữ",
            "ỳ", "ý", "ỵ", "ỷ", "ỹ",
            "đ", "Đ", "'",
            // Chữ hoa
            "À", "Á", "Ạ", "Ả", "Ã", "Â", "Ầ", "Ấ", "Ậ", "Ẩ", "Ẫ", "Ă", "Ằ", "Ắ", "Ặ", "Ẳ", "Ẵ",
            "È", "É", "Ẹ", "Ẻ", "Ẽ", "Ê", "Ề", "Ế", "Ệ", "Ể", "Ễ",
            "Ì", "Í", "Ị", "Ỉ", "Ĩ",
            "Ò", "Ó", "Ọ", "Ỏ", "Õ", "Ô", "Ồ", "Ố", "Ộ", "Ổ", "Ỗ", "Ơ", "Ờ", "Ớ", "Ợ", "Ở", "Ỡ",
            "Ù", "Ú", "Ụ", "Ủ", "Ũ", "Ư", "Ừ", "Ứ", "Ự", "Ử", "Ữ",
            "Ỳ", "Ý", "Ỵ", "Ỷ", "Ỹ",
            "Đ", "Đ", "'", ".",
        ];
        $marKoDau = [
            /// Chữ thường
            "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a",
            "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e",
            "i", "i", "i", "i", "i",
            "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o",
            "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u",
            "y", "y", "y", "y", "y",
            "d", "D", "",
            //Chữ hoa
            "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A",
            "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E",
            "I", "I", "I", "I", "I",
            "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O",
            "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U",
            "Y", "Y", "Y", "Y", "Y",
            "D", "D", "", "-",
        ];

        return str_replace($marTViet, $marKoDau, $mystring);
    }

    /**
     * [convertToAlias Function tao alias]
     *
     * @param string $replace
     * @return mixed|string [type]           [description]
     */
    public static function convertToAlias($str, $replace = "-")
    {

        //$str = strtolower(self::removeAccent($str));
        $str = self::removeAccent($str);
        $str = self::url_slug($str,['delimiter'=>$replace]);

        return $str;
        //return self::slugify($str);

        $str = trim($str);

        $str = str_replace("   ", " ", $str);
        $str = str_replace("ū", "u", $str);
        $str = str_replace("沐", " ", $str);
        $str = str_replace("轶", " ", $str);
        $str = str_replace("沐轶", " ", $str);
//        $str = preg_replace('/\P{Han}+/', '', $str);
//        $str = preg_replace('/[^\u4E00-\u9FFF]+/', '', $str);
        $str = str_replace("  ", " ", $str);
        $str = str_replace(" ", $replace, $str);

        return $str;
    }

    static function url_slug($str, $options = [])
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        // $str = strtolower($str);
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());

        $defaults = [
            'delimiter'     => '-',
            'limit'         => NULL,
            'lowercase'     => TRUE,
            'replacements'  => [],
            'transliterate' => TRUE,
        ];

        // Merge options
        $options = array_merge($defaults, $options);

        $char_map = [
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',
            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
            'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
            'š' => 's', 'ū' => 'u', 'ž' => 'z',
        ];

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);

        }
        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);

        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }

    static public function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        return $text;
    }

    /***
     * @param $string
     *
     * @return string
     */
    public static function safeText($string)
    {
        return strip_tags($string);
    }

    public static function trimAllSpace($string)
    {
        $string = str_replace('&nbsp;', '', $string);
        $string = str_replace(' ', '', $string);
        $string = preg_replace('/\s+/', '', $string);

        return $string;
    }

    public static function randomString($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function randomStringWithoutNumber($length = 8)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function getFileExtension($file)
    {
        $pos = strrpos($file, '.');
        if (!$pos) {
            return FALSE;
        }
        $str = substr($file, $pos, strlen($file));

        return strtolower($str);
    }

    public static function numberFormat($stringNumber)
    {
        return number_format($stringNumber, 0, '', '.');
    }

    /***
     * @param $link str
     *
     * @note Lay domain cua 1 duong link link yêu cầu phải có http hoặc https đàng hoàng
     * @return string
     */
    static function getDomainByLink($link)
    {

        $rex = '/^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/';
        if (preg_match($rex, $link, $matches)) {
            return rtrim($matches[0], '/');
        } else {
            return FALSE;
        }
    }

    /***
     * @param $link có http
     *
     * @return mixed true/ false
     * @note: Check 1 đối tượng xem có phải link hay không?
     */
    static public function isLink($link)
    {
        return filter_var($link, FILTER_VALIDATE_URL);
    }

    /***
     * @param        $content
     * @param string $string_split
     *
     * @return mixed
     * @note: join các dòng của text area lại thành 1 dòng
     */
    static public function joinAreaContent($content, $string_split = ',')
    {
        $content = preg_split('/\r\n|[\r\n]/', $content);

        return implode($string_split, $content);
    }

    /***
     * @param        $content
     * @param string $string_split
     *
     * @return mixed
     * @note: split các dòng của text area ra thành nhiều dòng
     */
    static public function splitAreaContent($content, $string_split = ',')
    {
        return preg_split('/\r\n|[\r\n]/', $content);
    }

    /***
     * @param $key
     * @param $val
     *
     * @note: sử dụng session theo cách thông thường hoặc dùng của laravel
     */
    static function setSession($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    /***
     * @param        $key
     * @param string $default
     *
     * @return string
     */
    static function getSession($key, $default = '')
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        } else {
            return $default;
        }
    }

    /***
     * @param $key
     */
    static function delSession($key)
    {
        unset($_SESSION[$key]);
    }

    /***
     * @param $key
     * @param $val
     *
     * @note: sử dụng session theo cách thông thường hoặc dùng của laravel
     */
    static function setCookie($name, $value, $minutes = 840000)
    {
        Cookie::queue($name, $value, $minutes);
    }

    /***
     * @param        $key
     * @param string $default
     *
     * @return string
     */
    static function getCookie($key, $default = '')
    {
        return Cookie::get($key);
    }

    /***
     * @param $key
     */
    static function delCookie($key)
    {
        Cookie::forget($key);
    }

    /***
     * @param     $message
     * @param int $start
     * @param     $length
     * @return string
     */
    static function subString($message, $start = 0, $length)
    {
        $length = (int)$length;
        if (isset($message[$length + 1])) {
            return mb_substr($message, $start, $length) . "...";
        } else {
            return $message;
        }
    }

    /**
     * @param      $time
     * @param bool $toString
     * @return string|array
     */
    static function formatTimestamp($time, $toString = FALSE)
    {
        if (!$time) {
            return $toString ? "" : [];
        }
        $diff = ['days' => 0, 'hours' => 0, 'minutes' => 0];
        $diffTime = TIME_NOW - $time;
        $days = intval($diffTime / (24 * 60 * 60));
        if ($days > 1) {
            if ($toString) {
                if ($days == 2) {
                    return date('H:i', $time) . " | Hôm qua";
                }

                return date('H:i:s d/m/Y', $time);
            }
            $diff['days'] = $days;
        } else {
            if ($toString) {
                return date('H:i', $time) . " | Hôm nay";
            }
            $hours = intval($diffTime / (60 * 60));
            if ($hours > 0) {
                $diff['hours'] = $hours;
            } else {
                $minutes = intval($diffTime / 60);
                $diff['minutes'] = $minutes;
            }
        }

        return $diff;
    }

    static function durationTime($_time)
    {
        $time = time() - $_time;

        if ($time > 0) {
            if ($time < 4 * 86400) {
                /*if($time>(365*86400)){
                    return floor($time/(365*86400)).' năm trước';
                }

                if($time>(30*86400)){
                    return floor($time/(30*86400)).' tháng trước';
                }
                */
                if ($time > (7 * 86400)) {
                    return floor($time / (7 * 86400)) . ' tuần trước';
                }
                if ($time > 86400) {
                    return floor($time / (86400)) . ' ngày trước';
                }

                if ($time > 3600) {
                    return floor($time / (3600)) . ' giờ trước';
                }

                if ($time > 60) {
                    return floor($time / (60)) . ' phút trước';
                }
            } else {
                return date('d/m/Y', $_time);
            }
        }

        return ' vài giây trước';
    }


    static function isMobile()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            return TRUE;
        }

        return FALSE;
    }

    static function mkdir($path)
    {
        @mkdir($path, 0777, TRUE);
    }

    static function buildTokenString($id)
    {
        return $id . '-' . sha1($id . 'sakura' . $id . 'sakura');
    }

    static function validateToken($token, $id = '')
    {
        $obj = explode('-', $token);
        if (!isset($obj[1])) {
            return FALSE;
        }
        if (!$id) {
            $id = $obj[0];
        }
        if (self::buildTokenString($id) == $token) {
            return $id;
        }

        return FALSE;
    }

    static function validateNganHang($token, $id = '')
    {
        $obj = explode('-', $token);
        if (!isset($obj[1])) {
            return FALSE;
        }
        if (!$id) {
            $id = $obj[0];
        }
        if (self::buildTokenString($id) == $token) {
            return $id;
        }

        return FALSE;
    }

    static function buildLinkVoVan($domain, $param)
    {
        return $domain . "?" . http_build_query($param);
    }

    static function getUrlContent($url,$cookie='ci_session=a%3A5%3A%7Bs%3A10%3A%22session_id%22%3Bs%3A32%3A%220132fd4e6d33e7f75942fbff1699fb10%22%3Bs%3A10%3A%22ip_address%22%3Bs%3A13%3A%22116.96.251.34%22%3Bs%3A10%3A%22user_agent%22%3Bs%3A115%3A%22Mozilla%2F5.0+%28Windows+NT+10.0%3B+Win64%3B+x64%29+AppleWebKit%2F537.36+%28KHTML%2C+like+Gecko%29+Chrome%2F61.0.3163.100+Safari%2F537.36%22%3Bs%3A13%3A%22last_activity%22%3Bi%3A1509558243%3Bs%3A9%3A%22user_data%22%3Bs%3A0%3A%22%22%3B%7D9a2ee928772e318fcc00198b3d23a3bb')
    {
        $agent = [
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.87 Safari/537.36",
            "Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0",
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)',
            "Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:42.0) Gecko/20100101 Firefox/42.0",
            "Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1",
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Googlebot/2.1 (+http://www.google.com/bot.html)',
        ];
        //shuffle($agent);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent[0]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        //curl_setopt($ch, CURLOPT_COOKIE, 'D0N=8cf8745f0cfb55a7fa0b7507a6414781; _ga=GA1.2.1717978598.1529813496; _gid=GA1.2.379641568.1529813496; __asc=7c37a0ea1642ffd71c5337234d9; __auc=7c37a0ea1642ffd71c5337234d9; JSESSIONID=C2dkbvfLngpB5CQyLXvrXTy00pNjL3TgnpGRGMGw5vdj0GkhhJr4!-611282167; DEV_PORTAL=11.1+en-us+us+AMERICA+6F5C6FD2B544576EE050A8C0BF0933CD+8C436B9D4A126F3844A8D2AE35146256B46BF25A35C276998157DA58A91B4A297DB064DB414EA53E646BEDBEB91D98948A4CF371F92FCA5D64FBD11CD157FE1B6CAD969FE0516ABFD2C0BE0385E6FC78D45710CE99CED953; _gat=1; _gali=_search');

        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        //echo $httpcode;
        return ($httpcode >= 200 && $httpcode < 300) ? $data : FALSE;
    }

    static function showDate($date, $format = 'd/m/Y')
    {
        return self::showMongoDate($date, $format);
    }

    static function showMongoDate($date, $format = 'd/m/Y')
    {
        if (!$date) {
            return NULL;
        }

        if (is_object($date)) {

            return $date->toDateTime()->setTimezone(new \DateTimeZone(config('app.timezone')))->format($format);
            //return $date->toDateTime()->format($format);
        } else {
            return NULL;
        }

    }
    static function validateDateTime($date, $format = 'd/m/Y H:i')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
    /**
     * @param $date_time_string ví dụ : 24/05/2018 08:20
     * @param string $format 'd/m/Y H:i'
     * @return \MongoDB\BSON\UTCDateTime
     *
     */
    static function getMongoDateTime($date_time_string = FALSE, $format = 'd/m/Y H:i')
    {
        if (!$date_time_string) {
            return new \MongoDB\BSON\UTCDateTime(strtotime('now') * 1000);
        }
        $date = strtotime(Carbon::createFromFormat($format, $date_time_string)->toDateTimeString());
        return new \MongoDB\BSON\UTCDateTime($date * 1000);
    }
    static function randMongoDateTime($from='1 January 2018',$to = '30 May 2018'){
        $date_start = strtotime($from);
        $date_end = strtotime($to);
        $rand_date = rand($date_start, $date_end);
        return new \MongoDB\BSON\UTCDateTime($rand_date * 1000);
    }

    static function getMongoDate($date = NULL, $dimiter = '/', $start = TRUE)
    {
        if ($date) {
            $time = explode($dimiter, $date);
            if (!isset($time[1])) {
                return new \MongoDB\BSON\UTCDateTime(strtotime($date) * 1000);
            }
            if ($start) {
                $time = mktime(0, 0, 0, (int)$time[1], (int)$time[0], (int)$time[2]);
            } else {
                $time = mktime(23, 59, 59, (int)$time[1], (int)$time[0], (int)$time[2]);
            }

            return new \MongoDB\BSON\UTCDateTime($time * 1000);
        }

        return new \MongoDB\BSON\UTCDateTime(strtotime('now') * 1000);
    }
    static function convertMktimeToMongoTime($mktime){
        return new \MongoDB\BSON\UTCDateTime($mktime * 1000);

    }

    static function uploadImageFromUrl($url, $to = '')
    {
        $linkParse = parse_url($url);
        $path = $linkParse['path'];
        $path = explode('/', $path);
        $fileName = end($path);
        array_pop($path);

        if (!$to) {
            $to = public_path("media/tutorial") . implode('/', $path);
            $dest = $linkParse['path'];
        } else {
            $dest = $to . $fileName;
            $to = public_path("media/tutorial/") . $to;
        }
        if (file_exists(public_path("media/tutorial") . $linkParse['path'])) {
            return '/tutorial' . $linkParse['path'];
        }
        Helper::mkdir($to);
        if (file_put_contents(public_path("media/tutorial") . $dest, file_get_contents($url))) {
            return '/tutorial' . $dest;
        }

        return NULL;

    }

    static function getNumberOnlyInString($str)
    {
        preg_match_all('!\d+!', $str, $matches);

        return implode('', $matches[0]);
        //return filter_var($str, FILTER_SANITIZE_NUMBER_INT);// cái này nó trả về cả số âm
    }

    /**
     * @param $array
     * @param $key
     * @return array
     */
    static function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }


    static function isMongoId($id)
    {
        if ($id instanceof \MongoDB\BSON\ObjectID
            || preg_match('/^[a-f\d]{24}$/i', $id)
        ) {
            return true;
        }
        return false;
    }
    static function getMongoId($id){
        return new  \MongoDB\BSON\ObjectID($id);
    }

    static function BsonDocumentToArray($item)
    {
        return \MongoDB\BSON\toPHP(\MongoDB\BSON\fromPHP($item), ['root' => 'array', 'document' => 'array']);
    }
    static function showContent($string) {
        if (!$string) {
            return 'Chưa cập nhật';
        }
        return $string;
    }

    public static function formatPercent($number) {
        return $number *100;
    }
}
