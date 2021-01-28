<?php

namespace App\Http\Controllers\API;

use App\Elibs\BeaconsHelper;
use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\BaseModel;
use App\Http\Models\Logs;
use App\Http\Models\Member;
use App\Http\Models\Notes;
use App\Http\Models\Staff;

class global_api extends Controller
{
    public function index($action = '')
    {
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {

        }
    }

    public function get_notes()
    {
        //loadmore danh sách ghi chú
    }

    public function save_note()
    {
        $object_id = request('object_id');
        $table_name = request('table_name');
        $token = request('token');
        if (!Helper::validateToken($token, $object_id . $table_name)) {
            return eView::getInstance()->getJsonNotifError('Dữ liệu không hợp lệ. Vui lòng kiểm tra lại');
        }

        if (!Helper::isMongoId($object_id)) {
            return eView::getInstance()->getJsonNotifError('Dữ liệu không hợp lệ. Vui lòng kiểm tra lại!');
        }

        $description = trim((string)request('description'));
        if (!isset($description[3])) {
            return eView::getInstance()->getJsonNotifError('Nội dung ghi chú quá ngắn (tối thiểu 3 ký tự). Vui lòng <a href="#note-description">Xem lại</a>');
        }

        $files = request('files');
        $files_name = request('files_name');


        Notes::create([
            'object_id'   => $object_id,
            'table'       => $table_name,
            'description' => $description,
            'created_by'  => Staff::getCreatedByToSaveDb(),
            'created_at'  => Helper::getMongoDateTime(),
            'files'       => BeaconsHelper::processFilesToSave(),
        ]);

        return eView::getInstance()->getJsonSuccess('Thêm ghi chú thành công', ['reload' => true]);

    }

    /**
     * Xem so sánh dữ liệu thay đổi
     * form-preview-history
     */
    public function form_preview_history()
    {
        $id = request('id');

        $obj = Logs::find($id);
        if ($obj) {
            $obj = $obj->toArray();
        }
        $tpl['obj'] = $obj;

        return eView::getInstance()->setView('', 'components/preview-history', $tpl);


    }

}
