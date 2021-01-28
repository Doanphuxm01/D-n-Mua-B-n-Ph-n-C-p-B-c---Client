

    <div class="row">
        <form name="postInputForm" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm" class="form-horizontal" method="post">
            <input type="hidden" name="id" id="id" value="{{isset($obj['id'])?$obj['id']:0}}"/>
            <div class="col-md-6">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Thông tin cơ bản</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">Họ và tên</label>
                                <div class="col-md-9">
                                    <input name="obj[name]" id="obj-name" value="{{isset($obj['name'])?$obj['name']:''}}" type="text" class="form-control" placeholder="Họ tên">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Email</label>
                                <div class="col-md-9">
                                    <input name="obj[email]" id="obj-email" value="{{isset($obj['email'])?$obj['email']:''}}" type="text" class="form-control" placeholder="...">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">Số điện thoại</label>
                                <div class="col-md-9">
                                    <input name="obj[phone]" id="obj-phone" value="{{isset($obj['phone'])?$obj['phone']:''}}" type="text" class="form-control" placeholder="...">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">Địa chỉ</label>
                                <div class="col-md-9">
                                    <input name="obj[addr]" id="obj-addr" value="{{isset($obj['addr'])?$obj['addr']:''}}" type="text" class="form-control" placeholder="Địa chỉ liên hệ">
                                </div>
                            </div>
                            {{--//todo@tinhthanh gọi template trong form insert/update--}}
                            @include("components.input-locations",['obj'=>@$obj])

                            <div class="form-group">
                                <label class="control-label col-md-3">Trạng thái</label>
                                <div class="col-md-9">
                                    <select id="obj-status" name="obj[status]" class="select ">
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_ACTIVE) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_ACTIVE}}">Hoạt động</option>
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_PENDING) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_PENDING}}">Chờ duyệt</option>
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_DISABLE) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_DISABLE}}">Khóa lại</option>
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_NO_WOKING) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_NO_WOKING}}">Đã nghỉ việc</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gWorking" aria-expanded="true">Thông tin công việc</a>
                        </h3>
                    </div>
                    <div id="gWorking" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            @if(!isset($obj['departments']) || !$obj['departments'] || (isset($obj['departments']) && $obj['departments']))
                                <div class="form-group tooltip-1row dep-container">
                                    <label class="control-label col-md-3">Phòng ban
                                        <i onclick="_addMoreDep()" data-placement="right" data-popup="tooltip" title="Thêm phòng ban chức vụ" class="fa fa-plus-circle text-info"></i>
                                    </label>
                                    <div class="col-md-5 multi-select-full">
                                        <select onchange="return _getPosByDep(this)" name="dep[]" class="form-control">
                                            <option value="0">Chọn phòng ban</option>
                                            @if(isset($allDepartment) && $allDepartment)
                                                @foreach($allDepartment as $item)
                                                    <option @if(isset($obj['departments'][0]['id']) && $obj['departments'][0]['id']==$item['_id']) selected @endif value="{{$item['_id']}}">{{$item['name']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="pos[]" class="form-control js-pos">
                                            <option value="0">Chọn chức vụ</option>
                                            @if(isset($obj['departments'][0]['id']) && $obj['departments'][0]['id'])
                                                @if(isset($allPosition) && $allPosition)
                                                    @foreach($allPosition as $pos)
                                                        @if(isset($pos['department']['id']) && $pos['department']['id']==$obj['departments'][0]['id'])
                                                            <option @if(isset($obj['positions'][0]['id']) && $obj['positions'][0]['id']==$pos['id']) selected @endif  value="{{$pos['id']}}">{{$pos['name']}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-1 tooltip-1row text-center">
                                        <label style="margin-top: 5px">
                                            <input data-popup="tooltip" data-placement="left" title="Chịu trách nhiệm chính" type="radio" value="0" @if(isset($obj['departments'][0]['main']) && $obj['departments'][0]['main']==1) checked @endif name="dep_main"/>
                                        </label>
                                    </div>
                                </div>
                            @endif
                            <div id="lsDep">
                                @if(isset($obj['departments']) && $obj['departments'])
                                    @foreach($obj['departments'] as $key=> $item)
                                        @if(isset($item['id']) && $key>0)
                                            <div class="form-group dep-container">
                                                <label class="control-label col-md-3"></label>
                                                <div class="col-md-5 multi-select-full">
                                                    <select name="dep[]" class="form-control">
                                                        <option value="">Chọn phòng ban</option>
                                                        @if(isset($allDepartment) && $allDepartment)
                                                            @foreach($allDepartment as $dep)
                                                                <option @if(isset($dep['id']) && $dep['id']==$item['id']) selected @endif value="{{$dep['id']}}">{{$dep['name']}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="pos[]" class="form-control js-pos">
                                                        <option value="">Chọn chức vụ</option>
                                                        @if(isset($allPosition) && $allPosition)
                                                            @foreach($allPosition as $pos)
                                                                @if(isset($pos['department']['id']) && $pos['department']['id']==$item['id'])
                                                                    <option @if(isset($obj['positions'][$key]) && $obj['positions'][$key]['id']==$pos['id']) selected @endif  value="{{$pos['id']}}">{{$pos['name']}}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                <div class="col-md-1 tooltip-1row text-center">
                                                    <label style="margin-top: 5px">
                                                        <input data-popup="tooltip" data-placement="left" title="Chịu trách nhiệm chính" type="radio" @if(isset($item['main']) && $item['main']==1) checked @endif  value="{{$key}}" name="dep_main"/>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Mô tả công việc</label>
                                <div class="col-md-9">
                                    <textarea name="obj[working_note]" id="obj-working-note" class="form-control" placeholder="Mô tả công việc">{{isset($obj['working_note'])?$obj['working_note']:''}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Member::buildLinkDelete($obj,'staff')}}')"
                                        class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa
                                </button>

                            @endif
                            <button type="button" onclick="return MNG_POST.update('{{admin_link('staff/input')}}','#postInputForm','add_more');" class="btn btn-info bg-teal-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Lưu & Thêm mới
                            </button>
                            <button type="button" onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm');" class="btn btn-info bg-danger-800 pull-right mr-15"><i class=" icon-database-check position-left"></i>Lưu lại</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-6">

            </div>

        </form>
    </div>


@push('JS_BOTTOM_REGION')
    <script type="text/javascript">
        var allPosition = {!! $allPosition?json_encode($allPosition):json_encode([]) !!};
        var allDepartment = {!! $allDepartment?json_encode($allDepartment):json_encode([]) !!};

        function _getPosByDep(obj) {
            obj = $(obj);
            let posContainer = obj.parents('.dep-container').find('select.js-pos');
            let dep_id = obj.val();
            posContainer.html('<option value="">Chọn chức vụ</option>');
            for (let i in allPosition) {
                let pos = allPosition[i];
                if (typeof pos.department !== 'undefined') {
                    if (typeof pos.department.id !== 'undefined') {
                        if (pos.department.id == dep_id) {
                            let option = '<option value="' + pos._id + '">' + pos.name + '</select>'
                            posContainer.append(option)
                        }
                    }
                }
            }
        }

        function _addMoreDep() {
            let htmlOptionDep = '<option value="">Chọn phòng ban</option>';
            for (let i in allDepartment) {
                let dep = allDepartment[i];
                htmlOptionDep += '<option value="' + dep._id + '">' + dep.name + '</option>';
            }
            let new_index = eval($('.dep-container').length);
            let html = '<div class="form-group dep-container">' +
                '<label class="control-label col-md-3"></label>' +
                '<div class="col-md-5 multi-select-full">' +
                '<select onchange="_getPosByDep(this)" name="dep[]" class="form-control">' + htmlOptionDep +
                '</select>' +
                '</div>' +
                '<div class="col-md-3">' +
                '<select name="pos[]" class="form-control js-pos"><option value="">Chọn chức vụ</option> ' +
                '</select>' +
                '</div>' +
                '<div class="col-md-1 tooltip-1row text-center">' +
                '<label style="margin-top: 5px">' +
                '<input data-popup="tooltip"  data-placement="left" title="Chịu trách nhiệm chính" type="radio" value="' + new_index + '" name="dep_main"/>' +
                '</label>' +
                '</div>' +
                '' +
                '</div>'
            '</div>';
            $('#lsDep').append(html);
            $('[data-popup="tooltip"]').tooltip();
        }
    </script>
@endpush