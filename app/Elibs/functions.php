<?php
/**
 * Created by PhpStorm.
 * User: sakura
 * Date: 4/23/18
 * Time: 3:34 PM
 */

function admin_link($router = '')
{
    return url(str_replace('//', '/', 'admin/' . $router));
}

function public_link($router = '', $withoutProject = FALSE)
{
    return url(str_replace('//', '/', '/' . $router));
}
function tv_admin_link($router = '',$withoutProject = FALSE)
{
    $host = 'http://minhphucgroup.com.vn/'.str_replace('//', '/', '/' . $router);
    return $host;
}
function date_time_show($date, $format = 'd/m/Y', $msg_if_null = '')
{
    if ($date) {
        return \App\Elibs\Helper::showMongoDate($date, $format);
    } else {
        return $msg_if_null;
    }
}
function date_time_range_show($start,$end, $format = 'd/m/Y', $msg_if_null = '')
{
    if ($start && $end) {
        $t_start =  \App\Elibs\Helper::showMongoDate($start, $format);
        $t_end =  \App\Elibs\Helper::showMongoDate($end, $format);
        return $t_start.'-'.$t_end;
    } else {
        return $msg_if_null;
    }
}

function value_show($value, $default = '')
{
    if (is_string($value) || is_numeric($value)) {
        if(empty($value)){
            return $default;
        }
        return $value;
    }
    if(is_array($value)){
        if(isset($value['name']) && is_string($value['name'])){
            return $value['name'];
        }else if(isset($value['value']) && is_string($value['value'])){
            return $value['value'];
        }else{
            return $default;
        }
    }

    return $default;
}

/**
 * @param $array1
 * @param $array2
 * @return array
 * So sánh 2 mảng
 */
function array_diff_assoc_recursive($array1, $array2)
{
    $difference = [];
    foreach ($array1 as $key => $value) {
        if (is_array($value)) {
            if (!isset($array2[$key]) || !is_array($array2[$key])) {
                $difference[$key] = $value;
            } else {
                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
                if (!empty($new_diff))
                    $difference[$key] = $new_diff;
            }
        } else if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
            $difference[$key] = $value;
        }
    }
    return $difference;
}

function build_token($string)
{
    return \App\Elibs\Helper::buildTokenString($string);
}

function is_deleted($obj,$force_root=true)
{
    return \App\Http\Models\BaseModel::isDeleted($obj,$force_root);
}

function all_staff_basic()
{
    return \App\Http\Models\Staff::getAllStaffBasic();
}
function all_project($key_by_id=false)
{
    return \App\Http\Models\Project::getAllProject($key_by_id);
}

function ebug($var,$label=''){
    return \App\Elibs\eBug::show($var,$label);
}

function link_detail($item)
{
    if (!isset($item['_id'])) {
        return '';
    }
    if (!isset($item['alias'])) {
        $alias = \App\Elibs\Helper::convertToAlias($item['name']);
    } else {
        $alias = $item['alias'];
    }

    return route('FeProductDetail', ['alias' => $alias, 'id' => $item['_id']]);
}