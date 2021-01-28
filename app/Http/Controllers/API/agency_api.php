<?php

namespace App\Http\Controllers\API;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Http\Controllers\Controller;
use App\Http\Models\Agency;
use App\Http\Models\Customer;
use App\Http\Models\Location;
use App\Http\Models\Member;
use Illuminate\Http\Request;


class agency_api extends AppApi
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {

        }
    }

    public function get_all_agency_location()
    {

        $city = Request::capture()->input('parent_key', '');
        $district = Request::capture()->input('pdistrict', '');

        $allAgency = Agency::getListDaiLyTraHang($city, $district);
        return $this->outputDone($allAgency, 'Danh sách đại lý trả hàng');

    }

    public function get_sub_location()
    {

    }
}
