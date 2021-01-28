<?php
namespace App\Elibs;
/**
 * User: Ngannv
 * Date: 11/12/2014
 * Time: 20:12
 */
class eCalendar
{
    private static $instance = FALSE;
    public         $TUAN     = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
    public         $THANG    = ["Giêng", "Hai", "Ba", "Tư", "Năm", "Sáu", "Bảy", "Tám", "Chín", "Mười", "Một", "Chạp"];
    public         $CAN      = ["Giáp", "Ất", "Bính", "Đinh", "Mậu", "Kỷ", "Canh", "Tân", "Nhâm", "Quý"];
    public         $CHI      = ["Tý", "Sửu", "Dần", "Mão", "Thìn", "Tỵ", "Ngọ", "Mùi", "Thân", "Dậu", "Tuất", "Hợi"];
    public         $GIO_HD   = ["110100101100", "001101001011", "110011010010", "101100110100", "001011001101", "010010110011"];
    public         $TIETKHI  = ["Xuân phân", "Thanh minh", "Cốc vũ", "Lập hạ", "Tiểu mãn", "Mang chủng", "Hạ chí", "Tiểu thử", "Đại thử", "Lập thu", "Xử thử", "Bạch lộ", "Thu phân", "Hàn lộ", "Sương giáng", "Lập đông", "Tiểu tuyết", "Đại tuyết", "Đông chí", "Tiểu hàn", "Đại hàn", "Lập xuân", "Vũ Thủy", "Kinh trập"];


    public $daySelected   = 0;
    public $dateSelected  = 0;
    public $monthSelected = 0;
    public $yearSelected  = 0;
    public $isSolar       = true;

    function __construct()
    {
        self::$instance =& $this;

    }

    static function &getInstance()
    {

        if (!self::$instance) {
            new self();
        }

        return self::$instance;
    }

    /***
     * @param $name : Tên của biến GET hoặc POST hoặc REQUEST nói chung
     * @param string $default : Giá trị mặc định của biến
     * @return string
     */
    private function _get($name, $default = false)
    {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        } elseif (isset($_POST[$name])) {
            return $_POST[$name];
        } elseif (isset($_GET[$name])) {
            return $_GET[$name];
        } else {
            return $default;
        }
    }

    function getTimeSelected()
    {

        $this->yearSelected  = abs((int)$this->_get('year', date('Y')));
        $this->monthSelected = abs((int)$this->_get('month', date('m')));
        //$this->monthSelected = ($this->monthSelected > 12 ? $this->monthSelected : 12);
        $this->dateSelected = abs((int)$this->_get('date', date('d')));

        if ($this->dateSelected > 31) {

        }

        if (isset($_GET['cal']) && strtolower($_GET['cal']) == 'am-lich') {
            $this->isSolar = true;
            // $this->convertLunar2Solar($this->dateSelected,$this->monthSelected,$this->yearSelected);
        } else {
            $this->isSolar = false;
        }
    }

    private function _getWeekInMoth($month = NULL, $year = NULL, $totalDayInMonth = NULL)
    {
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $month = date('m');
        }
        if (!$totalDayInMonth) {
            $totalDayInMonth = $this->_getDayInMonth($month, $year);//Số ngày trong tháng
        }
        $startDayInMonth = date('N', mktime(0, 0, 0, $month, 1, $year));//Ngày bắt đầu của tháng vào thứ mấy, thứ 2=1
        $endDayInMonth   = date('N', mktime(0, 0, 0, $month, $totalDayInMonth, $year));//Ngày kết thúc của tháng vao thứ mấy
        $numWeekOfMonth  = ($totalDayInMonth % 7 == 0 ? 0 : 1) + (int)($totalDayInMonth / 7);//Nếu
        //Vậy khi nào thì tháng có 6 tuần (khi có 5 tuần và có thứ bắt đâu của tháng > thứ kết thúc của tháng
        if ($startDayInMonth > $endDayInMonth) {
            $numWeekOfMonth++;
        }
        $data['startDayInMonth'] = $startDayInMonth;
        $data['endDayInMonth']   = $endDayInMonth;
        $data['numWeekOfMonth']  = $numWeekOfMonth;

        return $data;

    }

    /**
     * @param null $month
     * @param null $year
     * Tính tháng X của năm Y xem có bao nhiêu ngày?
     */
    private function _getDayInMonth($month = NULL, $year = NULL)
    {
        if (!$year) {
            $year = (int)date('Y');
        }
        if (!$month) {
            $month = (int)date('m');
        }

        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }

    private function _convertDayToText($day)
    {
        $dayObject = [
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy',
            7 => 'Chủ Nhật',
        ];
        if (isset($dayObject[$day])) {
            return $dayObject[$day];
        }

        return FALSE;
    }

    /**
     * @param $year
     * @return string
     * Tính can chi năm
     */
    public function convertYearSolar2Lunar($year)
    {
        return $this->CAN[($year + 6) % 10] . " " . $this->CHI[($year + 8) % 12];
    }

    /**
     * @param $date
     * @param $month
     * @param $year
     * @return string
     * Tính can chi ngày
     */
    private function _convertDateSolar2Lunar($date = NULL, $month = NULL, $year = NULL, $jd = NULL)
    {
        if (!$jd) {
            $jd = $this->jdFromDate($date, $month, $year);
        }

        return $this->CAN[($jd + 9) % 10] . " " . $this->CHI[($jd + 1) % 12];
    }

    private function _getCanChiThang($lunarMonth, $lunarYear)
    {
        return $this->CAN[($lunarYear * 12 + $lunarMonth + 3) % 10] . " " . $this->CHI[($lunarMonth + 1) % 12];
    }

    /***
     * @param null $date
     * @param null $month
     * @param null $year
     * @return array
     * require run getTimeSelected
     */
    public function getMonthCalendar($date = NULL, $month = NULL, $year = NULL)
    {
        if (!$year) {
            $year = $this->yearSelected;
        }
        if (!$month) {
            $month = $this->monthSelected;
        }
        if (!$date) {
            $date = $this->dateSelected;
        }

        $calendar    = [];
        $weekOfMonth = $this->_getWeekInMoth($month, $year);

        $calendar['startDayInMonth'] = $weekOfMonth['startDayInMonth'];
        $calendar['endDayInMonth']   = $weekOfMonth['endDayInMonth'];
        $calendar['totalDayInMonth'] = $this->_getDayInMonth($month, $year);
        $calendar['dayOfWeek']       = date('N', mktime(0, 0, 0, $month, $date, $year));
        $calendar['dayOfWeekText']   = $this->_convertDayToText($calendar['dayOfWeek']);

        $calendar['solar']['date']      = $date;
        $calendar['solar']['month']     = $month;
        $calendar['solar']['year']      = $year;
        $jd                             = $this->jdFromDate($date, $month, $year);
        $lunarBySolar                   = $this->convertSolar2Lunar($date, $month, $year);
        $calendar['lunar']['date']      = $lunarBySolar[0];
        $calendar['lunar']['dateText']  = $this->_convertDateSolar2Lunar($date, $month, $year, $jd);
        $calendar['lunar']['month']     = $lunarBySolar[1];
        $calendar['lunar']['monthText'] = $this->_getCanChiThang($lunarBySolar[1], $lunarBySolar[2]);
        $calendar['lunar']['year']      = $lunarBySolar[2];
        $calendar['lunar']['yearText']  = $this->convertYearSolar2Lunar($lunarBySolar[2]);
        $calendar['lunar']['hour']      = $this->getGioHoangDao($date, $month, $year, $jd);
        $weekOfMonth                    = $weekOfMonth['numWeekOfMonth'];

        #region Tháng trước tháng hiện tại
        $backMonth = $month - 1;//tháng trước = tháng hiện tại - 1 nếu là lớn hơn 1 = nếu = 0 thì thì tháng hiện tại là tháng 1 tháng trước là tháng 12 của năm trước
        $backYear  = $year;
        if ($backMonth == 0) {
            $backYear = $backYear - 1;
        }

        $backDate = $this->_getDayInMonth($backMonth, $backYear);


        #endregion Tháng trước tháng hiện tại

        #region Tháng kế tiếp tháng hiện tại
        $nextMonth = $month + 1;
        $nextYear  = $year;
        if ($nextMonth > 12) {
            $nextYear = $nextYear + 1;
        }
        $nextDate = 1;//chắc chắn là ngày 1 dương lịch
        #endregion Tháng kế tiếp tháng hiện tại
        $_date = 0;
        $today = date('Y') . (int)date('m') . (int)(date('d'));
        for ($i = 1; $i <= $weekOfMonth; $i++) {
            for ($j = 1; $j <= 7; $j++) {

                if (($i == 1 && $calendar['startDayInMonth'] > $j) || ($i == $weekOfMonth && $j > $calendar['endDayInMonth'])) {
                    if ($i == 1 && $calendar['startDayInMonth'] > $j) {
                        //tháng trước
                        //$backDate = $backDate - $calendar['startDayInMonth'];
                        $xDate  = $backDate - ($calendar['startDayInMonth'] - $j - 1);
                        $xMonth = $backMonth;
                        $xYear  = $backYear;

                        //$backDate--;
                    }
                    if ($i == $weekOfMonth && $j > $calendar['endDayInMonth']) {
                        $xDate  = $nextDate;
                        $xMonth = $nextMonth;
                        $xYear  = $nextYear;
                        $nextDate++;
                    }
                    $out_month = true;

                } else {
                    $out_month = false;
                    $_date++;
                    $xDate  = $_date;
                    $xMonth = $month;
                    $xYear  = $year;
                }
                $solar        = $xDate;
                $lunarBySolar = $this->convertSolar2Lunar($solar, $xMonth, $xYear);
                $lunarInt     = $lunarBySolar[0];
                $lunar        = $lunarBySolar[0];
                if ($lunarBySolar[0] == 1) {
                    //đầu tháng âm lịch
                    $lunar .= '/' . $lunarBySolar[1];
                }
                if ($lunarBySolar[1] != $month) {
                    if ($_date == 1 || $_date == $calendar['totalDayInMonth']) {
                        $lunar .= '/' . $lunarBySolar[1];
                    }
                }
                if ($lunarBySolar[2] != $year) {
                    //$lunar .= '/' . $lunarBySolar[2];
                }
                $timeStamp = $year . $month . $solar;//mktime(0, 0, 0, $month, $_date, $year);
                $htmlTitle = 'Click xem thông tin chi tiết ' . $this->_convertDayToText(date('N', $timeStamp)) . ', Dương lịch: ngày ' . $solar . '/' . $month . '/' . $year . ', Âm lịch: ngày ' . $lunarBySolar[0] . '/' . $lunarBySolar[1] . ' năm ' . $this->convertYearSolar2Lunar($lunarBySolar[2]);

                if ($timeStamp == $today) {
                    $isToday = 1;
                    if ($htmlTitle) {
                        $htmlTitle .= ' [Hôm nay]';
                    }
                } else {
                    $isToday = 0;
                }
                //MyHelper::debug($today);
                $calendar['week'][$i][$j]['lunar']     = $lunar;
                $calendar['week'][$i][$j]['lunarInt']  = $lunarInt;
                $calendar['week'][$i][$j]['solar']     = $solar;
                $calendar['week'][$i][$j]['hoang_dao'] = 0;
                $calendar['week'][$i][$j]['hac_dao']   = 0;
                $calendar['week'][$i][$j]['htmlTitle'] = $htmlTitle;
                $calendar['week'][$i][$j]['timeStamp'] = $timeStamp;
                $calendar['week'][$i][$j]['isToday']   = $isToday;
                $calendar['week'][$i][$j]['out_month'] = $out_month;
                //$calendar['week'][$i][$j]['link']      = Url::build('index', array('date' => $solar, 'month' => $month, 'year' => $year));
                $calendar['week'][$i][$j]['link'] = '';
            }
        }

        //System::debug($calendar);

        // die();

        return ($calendar);
    }


    function jdFromDate($dd, $mm, $yy)
    {
        $a  = floor((14 - $mm) / 12);
        $y  = $yy + 4800 - $a;
        $m  = $mm + 12 * $a - 3;
        $jd = $dd + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - floor($y / 100) + floor($y / 400) - 32045;
        if ($jd < 2299161) {
            $jd = $dd + floor((153 * $m + 2) / 5) + 365 * $y + floor($y / 4) - 32083;
        }

        return $jd;
    }

    function jdToDate($jd)
    {
        if ($jd > 2299160) { // After 5/10/1582, Gregorian calendar
            $a = $jd + 32044;
            $b = floor((4 * $a + 3) / 146097);
            $c = $a - floor(($b * 146097) / 4);
        } else {
            $b = 0;
            $c = $jd + 32082;
        }
        $d     = floor((4 * $c + 3) / 1461);
        $e     = $c - floor((1461 * $d) / 4);
        $m     = floor((5 * $e + 2) / 153);
        $day   = $e - floor((153 * $m + 2) / 5) + 1;
        $month = $m + 3 - 12 * floor($m / 10);
        $year  = $b * 100 + $d - 4800 + floor($m / 10);

        //echo "day = $day, month = $month, year = $year\n";
        return [$day, $month, $year];
    }

    function getNewMoonDay($k, $timeZone)
    {
        $T   = $k / 1236.85; // Time in Julian centuries from 1900 January 0.5
        $T2  = $T * $T;
        $T3  = $T2 * $T;
        $dr  = M_PI / 180;
        $Jd1 = 2415020.75933 + 29.53058868 * $k + 0.0001178 * $T2 - 0.000000155 * $T3;
        $Jd1 = $Jd1 + 0.00033 * sin((166.56 + 132.87 * $T - 0.009173 * $T2) * $dr); // Mean new moon
        $M   = 359.2242 + 29.10535608 * $k - 0.0000333 * $T2 - 0.00000347 * $T3; // Sun's mean anomaly
        $Mpr = 306.0253 + 385.81691806 * $k + 0.0107306 * $T2 + 0.00001236 * $T3; // Moon's mean anomaly
        $F   = 21.2964 + 390.67050646 * $k - 0.0016528 * $T2 - 0.00000239 * $T3; // Moon's argument of latitude
        $C1  = (0.1734 - 0.000393 * $T) * sin($M * $dr) + 0.0021 * sin(2 * $dr * $M);
        $C1  = $C1 - 0.4068 * sin($Mpr * $dr) + 0.0161 * sin($dr * 2 * $Mpr);
        $C1  = $C1 - 0.0004 * sin($dr * 3 * $Mpr);
        $C1  = $C1 + 0.0104 * sin($dr * 2 * $F) - 0.0051 * sin($dr * ($M + $Mpr));
        $C1  = $C1 - 0.0074 * sin($dr * ($M - $Mpr)) + 0.0004 * sin($dr * (2 * $F + $M));
        $C1  = $C1 - 0.0004 * sin($dr * (2 * $F - $M)) - 0.0006 * sin($dr * (2 * $F + $Mpr));
        $C1  = $C1 + 0.0010 * sin($dr * (2 * $F - $Mpr)) + 0.0005 * sin($dr * (2 * $Mpr + $M));
        if ($T < -11) {
            $deltat = 0.001 + 0.000839 * $T + 0.0002261 * $T2 - 0.00000845 * $T3 - 0.000000081 * $T * $T3;
        } else {
            $deltat = -0.000278 + 0.000265 * $T + 0.000262 * $T2;
        };
        $JdNew = $Jd1 + $C1 - $deltat;

        //echo "JdNew = $JdNew\n";
        return floor($JdNew + 0.5 + $timeZone / 24);
    }

    function getSunLongitude($jdn, $timeZone)
    {
        $T  = ($jdn - 2451545.5 - $timeZone / 24) / 36525; // Time in Julian centuries from 2000-01-01 12:00:00 GMT
        $T2 = $T * $T;
        $dr = M_PI / 180; // degree to radian
        $M  = 357.52910 + 35999.05030 * $T - 0.0001559 * $T2 - 0.00000048 * $T * $T2; // mean anomaly, degree
        $L0 = 280.46645 + 36000.76983 * $T + 0.0003032 * $T2; // mean longitude, degree
        $DL = (1.914600 - 0.004817 * $T - 0.000014 * $T2) * sin($dr * $M);
        $DL = $DL + (0.019993 - 0.000101 * $T) * sin($dr * 2 * $M) + 0.000290 * sin($dr * 3 * $M);
        $L  = $L0 + $DL; // true longitude, degree
        //echo "\ndr = $dr, M = $M, T = $T, DL = $DL, L = $L, L0 = $L0\n";
        // obtain apparent longitude by correcting for nutation and aberration
        $omega = 125.04 - 1934.136 * $T;
        $L     = $L - 0.00569 - 0.00478 * sin($omega * $dr);
        $L     = $L * $dr;

        $L = $L - M_PI * 2 * (floor($L / (M_PI * 2))); // Normalize to (0, 2*PI)
        return floor($L / M_PI * 6);
    }

    function getLunarMonth11($yy, $timeZone)
    {
        $off     = $this->jdFromDate(31, 12, $yy) - 2415021;
        $k       = floor($off / 29.530588853);
        $nm      = $this->getNewMoonDay($k, $timeZone);
        $sunLong = $this->getSunLongitude($nm, $timeZone); // sun longitude at local midnight
        if ($sunLong >= 9) {
            $nm = $this->getNewMoonDay($k - 1, $timeZone);
        }

        return $nm;
    }

    function getLeapMonthOffset($a11, $timeZone = 7.0)
    {
        $k = floor(($a11 - 2415021.076998695) / 29.530588853 + 0.5);

        $i   = 1; // We start with the month following lunar month 11
        $arc = $this->getSunLongitude($this->getNewMoonDay($k + $i, $timeZone), $timeZone);
        do {
            $last = $arc;
            $i    = $i + 1;
            $arc  = $this->getSunLongitude($this->getNewMoonDay($k + $i, $timeZone), $timeZone);
        } while ($arc != $last && $i < 14);

        return $i - 1;
    }

    /* Comvert solar date dd/mm/yyyy to the corresponding lunar date */
    function convertSolar2Lunar($dd, $mm, $yy, $timeZone = 7.0)
    {
        $dayNumber  = $this->jdFromDate($dd, $mm, $yy);
        $k          = floor(($dayNumber - 2415021.076998695) / 29.530588853);
        $monthStart = $this->getNewMoonDay($k + 1, $timeZone);
        if ($monthStart > $dayNumber) {
            $monthStart = $this->getNewMoonDay($k, $timeZone);
        }
        $a11 = $this->getLunarMonth11($yy, $timeZone);
        $b11 = $a11;
        if ($a11 >= $monthStart) {
            $lunarYear = $yy;
            $a11       = $this->getLunarMonth11($yy - 1, $timeZone);
        } else {
            $lunarYear = $yy + 1;
            $b11       = $this->getLunarMonth11($yy + 1, $timeZone);
        }
        $lunarDay   = $dayNumber - $monthStart + 1;
        $diff       = floor(($monthStart - $a11) / 29);
        $lunarLeap  = 0;
        $lunarMonth = $diff + 11;
        if ($b11 - $a11 > 365) {
            $leapMonthDiff = $this->getLeapMonthOffset($a11, $timeZone);
            if ($diff >= $leapMonthDiff) {
                $lunarMonth = $diff + 10;
                if ($diff == $leapMonthDiff) {
                    $lunarLeap = 1;
                }
            }
        }
        if ($lunarMonth > 12) {
            $lunarMonth = $lunarMonth - 12;
        }
        if ($lunarMonth >= 11 && $diff < 4) {
            $lunarYear -= 1;
        }

        return [$lunarDay, $lunarMonth, $lunarYear, $lunarLeap];
    }

    /* Convert a lunar date to the corresponding solar date */
    function convertLunar2Solar($lunarDay, $lunarMonth, $lunarYear, $lunarLeap, $timeZone = 7.0)
    {
        if ($lunarMonth < 11) {
            $a11 = $this->getLunarMonth11($lunarYear - 1, $timeZone);
            $b11 = $this->getLunarMonth11($lunarYear, $timeZone);
        } else {
            $a11 = $this->getLunarMonth11($lunarYear, $timeZone);
            $b11 = $this->getLunarMonth11($lunarYear + 1, $timeZone);
        }
        $k   = floor(0.5 + ($a11 - 2415021.076998695) / 29.530588853);
        $off = $lunarMonth - 11;
        if ($off < 0) {
            $off += 12;
        }
        if ($b11 - $a11 > 365) {
            $leapOff   = $this->getLeapMonthOffset($a11, $timeZone);
            $leapMonth = $leapOff - 2;
            if ($leapMonth < 0) {
                $leapMonth += 12;
            }
            if ($lunarLeap != 0 && $lunarMonth != $leapMonth) {
                return [0, 0, 0];
            } else if ($lunarLeap != 0 || $off >= $leapOff) {
                $off += 1;
            }
        }
        $monthStart = $this->getNewMoonDay($k + $off, $timeZone);

        return $this->jdToDate($lunarDay, $monthStart);
    }

    function getGioHoangDao($date, $month, $year, $jd = NULL)
    {
        if (!$jd) {
            $jd = $this->jdFromDate($date, $month, $year);
        }
        $chiOfDay = ($jd + 1) % 12;
        $gioHD    = $this->GIO_HD[$chiOfDay % 6]; // same values for Ty' (1) and Ngo. (6), for Suu and Mui etc.
        $hours    = [];
        $x        = $y = 0;
        for ($i = 0; $i < 12; $i++) {
            //if ($gioHD. . charAt($i)chara == '1') {
            $hour_name   = $this->CHI[$i];
            $hour_number = ($i * 2 + 23) % 24 . 'h - ' . ($i * 2 + 1) % 24 . 'h';
            if ($gioHD{$i} == 1) {
                $x++;
                $hours['hoang_dao'][$x]['name']   = $hour_name;
                $hours['hoang_dao'][$x]['number'] = $hour_number;
            } else {
                $y++;
                $hours['hac_dao'][$y]['name']   = $hour_name;
                $hours['hac_dao'][$y]['number'] = $hour_number;
            }
        }

        return $hours;
    }

    public function buildMonthCalendarByCountry($date = NULL, $month = NULL, $year = NULL, $country = null)
    {

        $calendar    = [];
        $weekOfMonth = $this->_getWeekInMoth($month, $year);

        $calendar['startDayInMonth'] = $weekOfMonth['startDayInMonth'];
        $calendar['endDayInMonth']   = $weekOfMonth['endDayInMonth'];
        $calendar['totalDayInMonth'] = $this->_getDayInMonth($month, $year);
        $calendar['dayOfWeek']       = date('N', mktime(0, 0, 0, $month, $date, $year));
        $calendar['dayOfWeekText']   = $this->_convertDayToText($calendar['dayOfWeek']);

        $calendar['solar']['date']  = $date;
        $calendar['solar']['month'] = $month;
        $calendar['solar']['year']  = $year;

        $weekOfMonth = $weekOfMonth['numWeekOfMonth'];

        #region Tháng trước tháng hiện tại
        $backMonth = $month - 1;//tháng trước = tháng hiện tại - 1 nếu là lớn hơn 1 = nếu = 0 thì thì tháng hiện tại là tháng 1 tháng trước là tháng 12 của năm trước
        $backYear  = $year;
        if ($backMonth == 0) {
            $backYear = $backYear - 1;
        }

        $backDate = $this->_getDayInMonth($backMonth, $backYear);


        #endregion Tháng trước tháng hiện tại

        #region Tháng kế tiếp tháng hiện tại
        $nextMonth = $month + 1;
        $nextYear  = $year;
        if ($nextMonth > 12) {
            $nextYear = $nextYear + 1;
        }
        $nextDate = 1;//chắc chắn là ngày 1 dương lịch
        #endregion Tháng kế tiếp tháng hiện tại
        $_date = 0;
        $today = date('Y') . (int)date('m') . (int)(date('d'));

        $tableCalendar = '<table class="cal"><thead>
                            <tr>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                                <th>Sun</th>
                            </tr>
                            </thead>
                            <tbody>';

        for ($i = 1; $i <= $weekOfMonth; $i++) {
            $tableCalendar .='<tr>';
            for ($j = 1; $j <= 7; $j++) {
                if (($i == 1 && $calendar['startDayInMonth'] > $j) || ($i == $weekOfMonth && $j > $calendar['endDayInMonth'])) {
                    if ($i == 1 && $calendar['startDayInMonth'] > $j) {
                        //tháng trước
                        //$backDate = $backDate - $calendar['startDayInMonth'];
                        $xDate  = $backDate - ($calendar['startDayInMonth'] - $j - 1);
                        $xMonth = $backMonth;
                        $xYear  = $backYear;

                        //$backDate--;
                    }
                    if ($i == $weekOfMonth && $j > $calendar['endDayInMonth']) {
                        $xDate  = $nextDate;
                        $xMonth = $nextMonth;
                        $xYear  = $nextYear;
                        $nextDate++;
                    }
                    $out_month = true;

                } else {
                    $out_month = false;
                    $_date++;
                    $xDate  = $_date;
                    $xMonth = $month;
                    $xYear  = $year;
                }
                $solar = $xDate;

                $timeStamp = $year . $month . $solar;//mktime(0, 0, 0, $month, $_date, $year);
                //$htmlTitle = 'View info ' . $this->_convertDayToText(date('N', $timeStamp)) . ', Dương lịch: ngày ' . $solar . '/' . $month . '/' . $year . ', Âm lịch: ngày ' . $lunarBySolar[0] . '/' . $lunarBySolar[1] . ' năm ' . $this->convertYearSolar2Lunar($lunarBySolar[2]);
                $htmlTitle = '';
                if ($timeStamp == $today) {
                    $isToday = 1;
                    if ($htmlTitle) {
                        $htmlTitle .= ' [Today]';
                    }
                } else {
                    $isToday = 0;
                }
                //MyHelper::debug($today);

                $tableCalendar.='<td';
                if($out_month){
                    $tableCalendar.=' class="off"';
                }
                $class = '';
                $j==7? $class=' class="sun" ':null;
                $tableCalendar.='><a '.$class.' href="javascript:void(0)">'.$solar.'</a>';
                $tableCalendar.='</td>';

                /*$calendar['week'][$i][$j]['solar']     = $solar;
                $calendar['week'][$i][$j]['htmlTitle'] = $htmlTitle;
                $calendar['week'][$i][$j]['timeStamp'] = $timeStamp;
                $calendar['week'][$i][$j]['isToday']   = $isToday;
                $calendar['week'][$i][$j]['out_month'] = $out_month;
                $calendar['week'][$i][$j]['link']      = '';*/
            }
            $tableCalendar .='</tr>';
        }
        if($weekOfMonth==5){
            $tableCalendar .='<tr><td class="ext" colspan="1000"><span></span></td> </tr>';
        }
        $tableCalendar .= '</tbody></table>';
        //System::debug($calendar);

        // die();

        //return ($calendar);
        return ($tableCalendar);
    }

    function rangeMonth($datestr, $utc = true)
    {
        $dt = strtotime($datestr);
        if($utc){
            return [
                new \MongoDB\BSON\UTCDateTime(strtotime('first day of this month', $dt) * 1000),
                new \MongoDB\BSON\UTCDateTime(strtotime('last day of this month', $dt) * 1000),
            ];
        }
        return [
            "start" => date('Y-m-d', strtotime('first day of this month', $dt)),
            "end"   => date('Y-m-d', strtotime('last day of this month', $dt))
        ];
    }

    function rangeWeek($datestr, $utc = true)
    {
        $dt = strtotime($datestr);
        if($utc){
            return [
                new \MongoDB\BSON\UTCDateTime(strtotime('last monday', $dt) * 1000),
                new \MongoDB\BSON\UTCDateTime(strtotime('next sunday', $dt) * 1000),
            ];
        }
        return [
            "start" => date('N', $dt) == 1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt)),
            "end"   => date('N', $dt) == 7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt))
        ];
    }


}
/***
 * Các bước build lịch table của tháng
 * Bước 1: Tính tháng X của năm Y xem có bao nhiêu ngày
 * Bước 2: Tính tháng X của năm Y xem có bao nhiêu tuần
 * Bước 3: Tính ngày bắt đầu của tháng là vào thứ mấy?
 * Bước 4=> tháng đó có bao nhiêu tuần 4<=TUẦN<=6
 */