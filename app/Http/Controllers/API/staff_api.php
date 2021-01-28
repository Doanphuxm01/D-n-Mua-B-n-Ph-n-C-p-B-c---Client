<?php

namespace App\Http\Controllers\API;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Http\Controllers\Controller;
use App\Http\Models\Location;
use App\Http\Models\MetaData;
use Illuminate\Http\Request;


class staff_api extends AppApi
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {

        }
    }

    function get_position_list()
    {
        $department_id = Request::capture()->input('department_id', 0);
        $positionList = MetaData::getPositionStaff(['department.id' => $department_id]);

        return $this->outputDone($positionList, 'Danh sách');
    }

    function getSelectOption(){

    }

}
