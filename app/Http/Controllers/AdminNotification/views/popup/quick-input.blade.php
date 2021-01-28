<div class="modal-dialog modal-large">
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}


    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">
                @if(isset($id) && $id)
                    Sửa thông báo
                @else
                    Thêm thông báo
                @endif
            </h3>
        </div>
        <div class="modal-body pd-10">
            <form name="postInputFormModal"
                  onsubmit="return MNG_POST.update('{{admin_link('notification/save')}}','#postInputFormModal');"
                  id="postInputFormModal" class="form-horizontal" method="post">
                <input name="id" id="obj-id" value="{{isset($obj['_id'])?$obj['_id']:''}}" type="hidden">
                <input name="obj[content][id]" id="obj-id" value="{{isset($obj['_id'])?$obj['_id']:''}}" type="hidden">
                <div class="form-group">
                    <label class="control-label col-md-3">Tiêu đề</label>
                    <div class="col-md-9">
                        <input type="text" name="obj[content][title]" class="form-control"
                               value="{{@$obj['content']['title']}}"
                               placeholder="Tiêu đề ghi chú (nếu có)"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Tóm tắt</label>
                    <div class="col-md-9">
                        <textarea name="obj[content][brief]" style="min-height: 120px;"
                                  class="form-control">{{@$obj['content']['brief']}}</textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Người gửi</label>
                    <div class="col-md-9">
                        <input class="form-control" name="" type="text"
                               @if(isset($obj['sender']['name']))
                               value="{{$obj['sender']['name']}}"
                               @elseif(isset($currentMember))
                               value="{{$currentMember['name']}} ({{$currentMember['account']}})"
                               @endif
                               readonly/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Người nhận thông báo</label>
                    <div class="col-md-9">
                        <input type="text" id="select-remote-data-staff" name="obj[listReceiverId]"
                               @if(isset($listObj))data-selected="{{json_encode(collect($listObj)
                               ->map(function ($item){return $item['receiver'];})
                               ->map(function($item){return ['id'=>$item['id'], 'text'=>$item['name']];}))}}"
                               @endif
                               placeholder="Lựa chọn nhân viên">
                    </div>
                </div>


            </form>
        </div>
        <div class="modal-footer">
            @if(!isset($obj['_id']))
                <button type="button"
                        onclick="return MNG_POST.update('{{admin_link('notification/save')}}','#postInputFormModal');"
                        class="btn btn-primary">Gửi thông báo
                </button>
            @else
                <button type="button"
                        data-dismiss="modal"
                        class="btn btn-primary">Đóng lại
                </button>

            @endif
        </div>
    </div>
</div>
<script>
    _AUTO_COMPLETE_INIT('#select-remote-data-staff', '{{admin_link('staff/suggest')}}', 'Nhập tên nhân viên')

</script>
