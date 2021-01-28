<div class="modal-dialog modal-large">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Cập nhật Hạng mục/ Kiểu/ Loại</h3>
        </div>
        <div class="modal-body pd-10">
            <form name="postInputForm" onsubmit="return MNG_POST.update('{{admin_link('category/_save')}}','#postInputForm');" id="postInputForm" class="form-horizontal" method="post">

                {{--thông tin cần có với form meta-data cơ bản--}}
                <input name="id" value="{{isset($obj['_id'])?$obj['_id']:''}}" type="hidden">

                <div class="form-group">
                    <label class="control-label col-md-3">Tên</label>
                    <div class="col-md-9">
                        <textarea
                                @if(!isset($obj['_id'])  || !$obj['_id'])
                                data-popup="tooltip" data-placement="top" title="Có thể thêm nhiều cùng lúc, mỗi tên nằm trên một hàng"
                                @endif
                                name="obj[name]" placeholder="Tên...(với công cụ tiện ích nhập theo cấu trúc: Tên | link) " class="form-control" style="height: 200px">{{isset($obj['name'])?$obj['name']:''}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Thuộc</label>
                    <div class="col-md-9">
                        <select name="obj[type]" class="form-control">
                            <option value="0">--Tất cả--</option>
                            @foreach(App\Http\Models\MetaData::$typeRegister as $val)
                                @if(($val['key'] != \App\Http\Models\MetaData::POSITION) && ($val['key'] != \App\Http\Models\MetaData::DEPARTMENT)
                                && ($val['key'] != \App\Http\Models\MetaData::LOCATION_REGION)&& ($val['key'] != \App\Http\Models\MetaData::LOCATION_COUNTRY))
                                    <option @if(isset($obj['type']) && $val['key'] == $obj['type']) selected @endif value="{{ $val['key'] }}">{{ $val['name'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

        </div>
        <div class="modal-footer">
            <button type="button" onclick="return MNG_POST.update('{{admin_link('category/_save')}}','#postInputForm');" class="btn btn-primary">Cập nhật</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    $('[data-popup="tooltip"]').tooltip();
</script>