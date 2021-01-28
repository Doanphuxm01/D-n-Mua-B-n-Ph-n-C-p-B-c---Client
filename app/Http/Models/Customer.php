<?php

namespace App\Http\Models;

use App\Elibs\Debug;
use App\Elibs\eCache;
use App\Elibs\Helper;
use Illuminate\Support\Facades\DB;

class Customer extends Member
{
    public $timestamps = false;
    const table_name = 'io_customers';
    protected $table = self::table_name;
    static $unguarded = true;
    static $basicFiledsForList = '*';
    protected $dates = [];
    protected $dateFormat = 'd/m/Y';
    const floor = 5;

    static function getByPhone($alias)
    {
        $where = [
            'phone' => $alias
        ];
        return self::where($where)->first();
    }

    static function getByEmail($alias)
    {
        $where = [
            'email' => $alias
        ];
        return self::where($where)->first();
    }

    static function buildTree(array &$menu_data, $parent_id = '0', $selected = [], $loop = 0) {
        $data = [];
        foreach ($menu_data as $k => &$item) {
            if ($item['parent_id'] == $parent_id) {
                $children = self::buildTree($menu_data, $item['ma_gioi_thieu']??$item['account']);
                if ($children) {
                    $item['children'] = $children;
                }
                $data[@$item['account']] = $item;
                unset($menu_data[$k]);
            }
        }
        return $data;
    }

    public static function buildTreeNguoc($cur_id,&$data,$account = '',$dept = 1, &$temp = 0)
    {
        if($temp < $dept)
            $cur_item = Customer::select('account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email')->where(!$cur_id ? 'account' : 'ma_gioi_thieu', !$cur_id ? $account : $cur_id)->first();
        if(!empty($cur_item)) {
            $cur_item = $cur_item->toArray();
            $p_item = Customer::select('account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email')->where('ma_gioi_thieu', $cur_item['parent_id'])->first();
            if(!empty($p_item)) {
                $p_item = $p_item->toArray();
                $temp++;
                $data[] = $p_item;
                self::buildTreeNguoc($p_item['ma_gioi_thieu'], $data,$p_item['account'],$dept,$temp);
            }
        }
        return $data;
    }

    // hàm build cây bao gồm cả account gốc
    public static function buildTreeNguocBaoGomCaGoc($cur_id,&$data,$account = '',$dept = 1, &$temp = 0)
    {
        if($temp < $dept)
            $cur_item = Customer::select('account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email')->where(!$cur_id ? 'account' : 'ma_gioi_thieu', !$cur_id ? $account : $cur_id)->first();
        if(!empty($cur_item)) {
            $cur_item = $cur_item->toArray();
            $p_item = Customer::select('account', '_id', 'parent_id', 'ma_gioi_thieu', 'status', 'name', 'verified', 'phone', 'email')->where('ma_gioi_thieu', $cur_item['parent_id'])->first();
            if(!empty($p_item)) {
                $p_item = $p_item->toArray();
                $temp++;
                $data[] = $cur_item;
                self::buildTreeNguoc($cur_item['ma_gioi_thieu'], $data,$cur_item['account'],$dept,$temp);
            }
        }
        return $data;
    }

    public static function checkF($tai_khoan_nguon, $tai_khoan_nhan)
    {
        $temp= [];
        self::buildTreeNguoc('', $temp, $tai_khoan_nguon, self::floor);
        if(!empty($temp)) {
            foreach ($temp as $f => $t) {
                if($t['account'] == $tai_khoan_nhan) {
                    return ++$f;
                }
            }
        }
        return 0;
    }

    static function getTaiKhoanToSaveDb($customer)
    {
        return [
            'id'      => (string)@$customer['_id']??$customer['id'],
            'name'    => @$customer['name'],
            'account' => $customer['account'],
            'email' => @$customer['email'],
            'phone' => @$customer['phone'],
            'verified' => @$customer['verified'],
        ];
    }

    static function buildLinkMaGioiThieu($ma_gioi_thieu) {
        if(!$ma_gioi_thieu) {
            return 'javascript:void(0);';
        }
        return tv_admin_link('auth/register?ma_gioi_thieu=' . $ma_gioi_thieu . '&token=' . Helper::buildTokenString($ma_gioi_thieu));
    }

}
