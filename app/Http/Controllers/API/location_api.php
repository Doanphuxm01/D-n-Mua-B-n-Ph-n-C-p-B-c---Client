<?php

namespace App\Http\Controllers\API;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Http\Controllers\Controller;
use App\Http\Models\Location;
use Illuminate\Http\Request;


class location_api extends AppApi
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {

        }
    }

    public function get_all_city()
    {
        $allCity = Location::getAllCity();
        return $this->outputDone($allCity, 'Danh sách tỉnh thành');
    }

    public function get_sub_location()
    {
        $parentKey = Request::capture()->input('parent_key', 0);
        $cityByKey = Location::getBySlug($parentKey);
        $allCity = [];
        if ($cityByKey) {
            $allCity = Location::getAllLocationByParent($cityByKey['code']);
        }

        return $this->outputDone($allCity, 'Danh sách');
    }
}
