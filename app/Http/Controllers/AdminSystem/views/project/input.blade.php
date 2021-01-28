{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
{!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
<div class="modal-dialog modal-large">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Cập nhật thông tin dự án</h3>
        </div>
        <div class="modal-body pd-10">
            <form name="postInputForm" onsubmit="return MNG_POST.update('{{admin_link('project/_save')}}','#postInputForm');" id="postInputForm" class="form-horizontal" method="post">

                {{--thông tin cần có với form meta-data cơ bản--}}
                <input name="id" id="obj-id" value="{{isset($obj['_id'])?$obj['_id']:''}}" type="hidden">

                <div class="form-group">
                    <label class="control-label col-md-3">Tên dự án <i class="text-danger">*</i> </label>
                    <div class="col-md-9">
                        <input name="obj[name]" id="obj-name" placeholder="Tên dự án..." value="{{isset($obj['name'])?$obj['name']:''}}" type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Tên đầy đủ <i class="text-danger">*</i> </label>
                    <div class="col-md-9">
                        <input name="obj[full_name]" id="obj-full_name" placeholder="Tên đầy đủ của dự án..." value="{{isset($obj['full_name'])?$obj['full_name']:''}}" type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Mô tả ngắn</label>
                    <div class="col-md-9">
                        <input name="obj[brief]" id="obj-brief" placeholder="Mô tả ngắn..." value="{{isset($obj['brief'])?$obj['brief']:''}}" type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Mô tả chi tiết (nếu có)</label>
                    <div class="col-md-9">
                        <textarea name="obj[content]" id="obj-content" placeholder="Mô tả ngắn..." class="form-control">{{isset($obj['content'])?$obj['content']:''}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Trạng thái</label>
                    <div class="col-md-9">
                        <select id="obj-status" name="obj[removed]" class="select form-control">
                            <option @if(isset($obj['removed']) && $obj['removed']==\App\Http\Models\BaseModel::REMOVED_NO) selected="selected" @endif value="{{\App\Http\Models\BaseModel::REMOVED_NO}}">Hoạt động</option>
                            <option @if(isset($obj['removed']) && $obj['removed']==\App\Http\Models\BaseModel::REMOVED_YES) selected="selected" @endif value="{{\App\Http\Models\BaseModel::REMOVED_YES}}">Xóa</option>
                        </select>
                    </div>
                </div>

            </form>

        </div>
        <div class="modal-footer">
            <button type="button" onclick="return  MNG_POST.update('{{admin_link('project/_save')}}','#postInputForm');" class="btn btn-primary">Cập nhật</button>
        </div>
    </div>
</div>