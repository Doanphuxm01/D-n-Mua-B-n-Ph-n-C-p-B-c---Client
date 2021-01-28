<?php

namespace App\Http\Controllers\AdminContent;

use App\Elibs\Debug;
use App\Elibs\eView;
use App\Elibs\Helper;
use App\Elibs\HtmlHelper;
use App\Elibs\Pager;
use App\Http\Controllers\Controller;
use App\Http\Models\Media;
use App\Http\Models\Department;
use App\Http\Models\Member;
use App\Http\Models\MetaData;
use App\Http\Models\Contract;
use App\Http\Models\Role;
use App\Http\Requests;
use App\Http\Models\BaseModel;
use App\Http\Models\Logs;
use App\Http\Models\Menu;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use function PHPSTORM_META\type;


class MngMedia extends Controller
{

    public function index($action = '')
    {

        //Điều hướng chức năng qua cái nầy
        $action = str_replace('-', '_', $action);
        if (method_exists($this, $action)) {
            return $this->$action();
        } else {
            return $this->_list();
        }

    }

    /***
     * @name:_list
     * @url : /admin/media/list hoặc /admin/media
     * @note: Quản lý danh sách các file, hình ảnh
     * @return \Illuminate\Support\Facades\View
     */
    private function _list()
    {
        $isAllow = Role::isAllowTo(Role::$ACTION_LIST_OF_ME . Role::$KAYN_MEDIA)
            || Role::isAllowTo(Role::$ACTION_LIST_OF_NOT_ME . Role::$KAYN_MEDIA);
        if (!$isAllow) {
            return eView::getInstance()->cannnotAccess(['msg' => 'Bạn không có quyền thực hiện hành động này']);
        }
        HtmlHelper::getInstance()->setTitle('Quản lý file, hình ảnh');
        $tpl = array();

        $where = [];

        $listObj = Media::where($where);

        $itemPerPage = Request::capture()->input('row', 50);
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, $itemPerPage, 'all');
        $tpl['listObj'] = $listObj;
        $tpl['allDepartments'] = Department::getDepartment();
        $tpl['allProject'] = Project::getAllProject();
        $tpl['dataGroup'] = MetaData::getAllByType();
        $tpl['allContract'] = Contract::getAllContract();

        return eView::getInstance()->setViewBackEnd(__DIR__, 'mng-media/list', $tpl);
    }

    function input()
    {
        if (!empty($_POST)) {
            return $this->_save();
        }
        HtmlHelper::getInstance()->setTitle('Cập nhật ảnh');
        $tpl = array();

        $id = Request::capture()->input('id');
        if ($id) {
            $obj = Media::find($id);
            if ($obj) {
                $tpl['obj'] = $obj;
            }
        }
        $tpl['allDepartments'] = Department::getDepartment();
        $tpl['dataGroup'] = MetaData::getAllByType();
        return eView::getInstance()->setViewBackEnd(__DIR__, 'mng-media/input', $tpl);
    }

    public
    function _save()
    {
        $id = Request::capture()->input('id', 0);
        $obj = Request::capture()->input('obj', []);
        $type = Request::capture()->input('project_type', []);
        $project_category = Request::capture()->input('project_category', []);

        $obj['project_type'] = (isset($type) && is_array($type)) ? $type : [];
        $obj['project_category'] = (isset($project_category) && is_array($project_category)) ? $project_category : [];

        if ($id) {
            if (!Member::haveRole(Member::mng_media_update)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
            }
            $curentObj = Media::find($id);
            if (!$curentObj) {
                return eView::getInstance()->getJsonError('Không tìm thấy đối tượng. Vui lòng kiểm tra lại');
            }
        } else {
            if (!Member::haveRole(Member::mng_media_ad)) {
                return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện chức năng này');
            }
        }
        if (!isset($obj['created_photo']) || !$obj['created_photo']) {
            return eView::getInstance()->getJsonError('Bạn chưa chọn Ngày chụp');
        }
        if (!isset($obj['project_category']) || !$obj['project_category']) {
            return eView::getInstance()->getJsonError('Bạn chưa chọn Hạng mục Dự án');
        }
        if (!isset($obj['project_type']) || !$obj['project_type']) {
            return eView::getInstance()->getJsonError('Bạn chưa chọn Loại hình');
        }
        if (!isset($obj['department']) || !$obj['department']) {
            return eView::getInstance()->getJsonError('Bạn chưa chọn Phòng ban');
        }

        $obj['created_photo'] = (isset($obj['created_photo']) && $obj['created_photo']) ? Helper::getMongoDateTime($obj['created_photo'], 'd/m/Y') : '';

        //xử lý mảng trước khi insert
        //phòng ban
        $objDept = MetaData::find($obj['department']);
        $obj['department'] = array(
            'id' => $objDept['_id'],
            'name' => $objDept['name'],
        );

        //dự án
        $objPro = Project::find($obj['project']);
        $obj['project'] = array(
            'id' => $objPro['_id'],
            'name' => $objPro['name'],
        );

        $dataGroup = MetaData::getAllByType();

        $_saveProjectModelToDb = [];
        $_saveProjectTypeToDb = [];
        foreach ($dataGroup as $item) {
            //hạng mục dự án
            if ($item['type'] == MetaData::PROJECT_MODEL) {
                if (isset($item['_id']) && in_array($item['_id'], $obj['project_category'])) {
                    $_saveProjectModelToDb[] = [
                        'id' => $item['_id'],
                        'name' => $item['name'],
                    ];
                }
            }
            //loại hình dự án
            if ($item['type'] == MetaData::PROJECT_TYPE) {
                if (isset($item['_id']) && in_array($item['_id'], $obj['project_type'])) {
                    $_saveProjectTypeToDb[] = [
                        'id' => $item['_id'],
                        'name' => $item['name'],
                    ];
                }
            }
        }
        $obj['project_category'] = $_saveProjectModelToDb;
        $obj['project_type'] = $_saveProjectTypeToDb;

        if ($id) {
            $obj['updated_by'] = Member::$currentMember['_id'];
            $obj['updated_at'] = Helper::getMongoDate();
            Media::where('_id', $id)->update($obj);
            Logs::createLog([
                'type' => Logs::TYPE_UPDATED,
                'object_id' => $id,
                'data_object' => $obj,
                'note' => "Ảnh được sửa bởi" . Member::getCurentAccount(),
            ], Logs::OBJECT_MEDIA);

        } else {
            $obj['removed'] = BaseModel::REMOVED_NO;
            $obj['created_at'] = Helper::getMongoDate();
            $obj['created_by'] = Member::getCreatedByToSaveDb();
            $id = Media::insertGetId($obj);
            Logs::createLog([
                'type' => Logs::TYPE_CREATE,
                'object_id' => (string)$id,
                'data_object' => $obj,
                'note' => "Ảnh được thêm bởi" . Member::getCurentAccount(),
            ], Logs::OBJECT_MEDIA);
        }

        $return['link'] = Menu::buildLinkAdmin('media/input?id=' . $id);

        return eView::getInstance()->getJsonSuccess('Cập nhật thông tin thành công', $return);
    }

    /***
     * @name      :_showFormUpload
     * @url       : /admin/product/_showFormUpload
     * @note      : Hiển thị form thêm, sửa
     * @localParam: setting, curent
     * @return String
     */
    public
    function _showFormUpload()
    {
        $action_name = Request::capture()->input('action_name');
        $setting = Request::capture()->input('setting');
        $curent = Request::capture()->input('curent');
        $loadListOnly = Request::capture()->input('only-list', 0);
        if ($loadListOnly) {
            return eView::getInstance()->getJsonSuccess('Load list done!', $this->_getListMedia());
        }

        $tpl['listObj'] = $this->_getListMedia();
        $tpl['curent'] = Media::getImageSrc($curent);
        $tpl['action_name'] = $action_name;

        return eView::getInstance()->setViewBackEnd(__DIR__, 'mng-media/upload-form', $tpl);
    }

    public
    function _getListMedia($type = Media::TYPE_IS_IMAGE)
    {

        $where = [
            'type' => $type,
        ];
        $listObj = Media::where($where);
        $listObj = $listObj->orderBy('_id', 'desc');
        $listObj = Pager::getInstance()->getPager($listObj, 80, 'all');
        if ($listObj) {
            $listObj = $listObj->toArray();

        }

        return $listObj;
    }

    /***
     * @name:_input_save
     * @url : /admin/product/input-save
     * @note: Submit: (thực hiện bởi ajax post) Lưu đối tượng khi cập nhật sửa hoặc thêm mới
     */
    public
    function _input_save()
    {
        //todo: action ở đây
    }

    /***
     * @name:_doUpload
     * @url : /admin/product/_doUpload
     * @note: Submit: (thực hiện bởi ajax post) upload image/video(media) vào thư viện
     */
    public
    function _doUpload()
    {
        //Debug::show($_POST);
        $isAllow = Role::isAllowTo(Role::$ACTION_EDIT_OF_ME . Role::$KAYN_MEDIA) || Role::isAllowTo(Role::$ACTION_EDIT_OF_NOT_ME . Role::$KAYN_MEDIA);
        if (!$isAllow) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện hành động này');
        }
        $file = Input::file('file');
        // dd($file);
        if ($file) {
            $subFolder = date('Y/md/');
            $destinationPath = Media::getUploadPath($subFolder); // upload path
            $_file_name = $file->getClientOriginalName();//$file['name']; // renameing image
            $nameFileWithOutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_file_name);
            $extension = Helper::getFileExtension($_file_name);
            $fileNew = Helper::convertToAlias($nameFileWithOutExt) . $extension;
            $file->move($destinationPath, $fileNew);


            $fType = Media::TYPE_IS_DOC;
            if (in_array(strtolower($extension), ['.png', '.jpg', '.jepg'])) {
                $fType = Media::TYPE_IS_IMAGE;
            }
            $saveMedia = [
                'name' => $nameFileWithOutExt,
                'type' => $fType,
                'created_by' => Member::getCreatedByToSaveDb(),
                'brief' => '',
                'created_at' => Helper::getMongoDate(),
                'src' => Media::getUploadFolder($subFolder) . $fileNew,
            ];
            $id = Media::insertGetId($saveMedia);
            if (is_object($id)) {
                $id = $id->__toString();
            }
            $dataReturn = [
                'id' => $id,
                'full_size_link' => Media::getFileLink($saveMedia['src']),
                'relative_link' => $saveMedia['src'],
                'name' => $saveMedia['name'],
                'brief' => $saveMedia['brief'],
            ];

            return eView::getInstance()->getJsonSuccess('upload thành công', $dataReturn);
        } else {
            return eView::getInstance()->getJsonSuccess('Có lỗi trong quá trình upload', []);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * Cho mobile
     */
    public
    function do_upload()
    {
        $isAllow = Role::isAllowTo(Role::$ACTION_EDIT_OF_ME . Role::$KAYN_MEDIA) || Role::isAllowTo(Role::$ACTION_EDIT_OF_NOT_ME . Role::$KAYN_MEDIA);
        if (!$isAllow) {
            return eView::getInstance()->getJsonError('Bạn không có quyền thực hiện hành động này');
        }
        $file = Input::file('image');
        // dd($file);
        if ($file) {
            $subFolder = date('Y/md/');
            $destinationPath = Media::getUploadPath($subFolder); // upload path
            $_file_name = $file->getClientOriginalName();//$file['name']; // renameing image
            $nameFileWithOutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_file_name);
            $extension = Helper::getFileExtension($_file_name);
            $fileNew = Helper::convertToAlias($nameFileWithOutExt) . $extension;
            $file->move($destinationPath, $fileNew);

            $saveMedia = [
                'name' => $nameFileWithOutExt,
                'type' => Media::TYPE_IS_IMAGE,
                'created_at' => Helper::getMongoDate(),
                'src' => Media::getUploadFolder($subFolder) . $fileNew,
            ];
            Media::insertGetId($saveMedia);
            $dataReturn = [
                'link' => Media::getImageSrc($saveMedia['src']),
                'path' => $saveMedia['src'],
            ];

            return eView::getInstance()->getJsonSuccess('upload thành công', $dataReturn);
        } else {
            return eView::getInstance()->getJsonError('Bạn cần gửi image', []);
        }
    }

    /***
     * @name:_input_save
     * @url : /admin/product/update
     * @note: Submit: (thực hiện bởi ajax post) Update lại trạng thái hoặc xóa đối tượng , thực hiện ở list
     */
    public
    function _upload_file()
    {
        $file = Input::file('files');
        Debug::show($file);
    }


}
