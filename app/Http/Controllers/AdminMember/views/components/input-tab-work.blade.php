@if(!isset($allCity))
    @php
        $allCity = collect(\App\Http\Models\Location::getAllCity());
    @endphp
@endif
@php($allStaffDataList = \App\Http\Models\MetaData::getAllByObject('staff'))
<style>
    .d-flex {
        display: flex;
    }

    .justify-content-center {
        align-items: center;
    }
</style>
<script>
    $.datepicker.setDefaults({
        dateFormat: 'dd/mm/yy'
    });
</script>
<form action="{{admin_link('/staff/_save_tab_work')}}" id="postInputForm">
    <input type="hidden" name="_token" value="{{csrf_token()}}">

    <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <input type="hidden" name="obj[_id]" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    Thông tin hợp đồng lao động
                </h6>
            </div>
            <div class="" style="overflow: scroll">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>TT</th>
                        <th>Tình trạng công việc</th>
                        <th>Loại hợp đồng lao động</th>
                        <th>Ngày bắt đầu hợp đồng</th>
                        <th>Ngày kết thúc hợp đồng</th>
                        <th width="1" class="text-right">
                            <a onclick="clone_thong_tin_hop_dong_lao_dong()"
                               class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>
                    </tr>

                    </thead>
                    <tbody id="obj_thong_tin_hop_dong_lao_dong">
                    @isset($obj['thong_tin_hop_dong_lao_dong'])
                        @foreach($obj['thong_tin_hop_dong_lao_dong'] as $key=>$value)
                            <tr>
                                <td>{{$key +1 }}</td>
                                <td>
                                    <select type="text" class="form-control"
                                            name="obj[thong_tin_hop_dong_lao_dong][{{$key}}][tinh_trang]"
                                    >
                                        <option @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['tinh_trang']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['tinh_trang']  == "Đang công tác") selected
                                                @endif value="Đang công tác">Đang công tác
                                        </option>
                                        <option @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['tinh_trang']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['tinh_trang']  == "Đã nghỉ việc") selected
                                                @endif value="Đã nghỉ việc">Đã nghỉ việc
                                        </option>
                                        <option @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['tinh_trang']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['tinh_trang']  == "Tạm nghỉ")  selected
                                                @endif value="Tạm nghỉ">Tạm nghỉ
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select type="text" class="form-control"
                                            name="obj[thong_tin_hop_dong_lao_dong][{{$key}}][loai_hop_dong]">
                                        <option
                                                @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']  == "Cộng tác viên") selected
                                                @endif
                                                value="Cộng tác viên">Cộng tác viên
                                        </option>
                                        <option
                                                @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']  == "Ngắn hạn") selected
                                                @endif
                                                value="Ngắn hạn">Ngắn hạn
                                        </option>
                                        <option
                                                @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']  == "Có thời hạn") selected
                                                @endif
                                                value="Có thời hạn">Có thời hạn
                                        </option>
                                        <option
                                                @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']  == "Không xác định") selected
                                                @endif
                                                value="Không xác định">Không xác định
                                        </option>
                                        <option
                                                @if(isset($obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']) && $obj['thong_tin_hop_dong_lao_dong'][$key]['loai_hop_dong']  == "Chưa ký Hợp đồng") selected
                                                @endif
                                                value="Chưa ký Hợp đồng">Chưa ký Hợp đồng
                                        </option>
                                    </select>

                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[thong_tin_hop_dong_lao_dong][{{$key}}][ngay_bat_dau]"
                                           @if(isset($value['ngay_bat_dau']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_bat_dau'])}}" @endif
                                           placeholder="Chọn ngày...">

                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[thong_tin_hop_dong_lao_dong][{{$key}}][ngay_ket_thuc]"
                                           placeholder="Ngày cấp..."
                                           @if(isset($value['ngay_ket_thuc']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_ket_thuc'])}}" @endif
                                    >

                                </td>
                                <td class="text-right"><i class="icon-trash text-danger"
                                                          onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endisset

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    Quá trình công tác
                </h6>
            </div>
            <div class="" style="overflow: scroll">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>TT</th>
                        <th>Phòng ban</th>
                        <th>Vai trò đảm nhiệm</th>
                        <th>Từ ngày</th>
                        <th>Đến ngày</th>
                        <th>Tại dự án</th>
                        <th width="1" class="text-right">
                            <a onclick="clone_qua_trinh_cong_tac()"
                               class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>

                    </tr>

                    </thead>
                    <tbody id="obj_qua_trinh_cong_tac">
                    @isset($obj['qua_trinh_cong_tac'])
                        @foreach(@$obj['qua_trinh_cong_tac'] as $key=>$value)
                            <tr>
                                <td>{{$key + 1}}</td>

                                <td>
                                    <select name="obj[qua_trinh_cong_tac][{{@$key}}][department][id]"
                                            class="select-search select-md"
                                            onchange="return APPLICATION._changeDepartment(jQuery(this).val(),'#qua_trinh_cong_tac_{{@$key}}_position','Chọn chức vụ')"

                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @isset($allDepartment)
                                            @foreach($allDepartment->sortBy('name') as $val)
                                                <option @if(isset($obj['qua_trinh_cong_tac'][$key]['department']['id']) && $obj['qua_trinh_cong_tac'][$key]['department']['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}
                                                    @if(isset($val['parent_dep']['name']))
                                                        ({{$val['parent_dep']['name']}})
                                                    @endif
                                                </option>

                                            @endforeach
                                        @endisset
                                    </select>

                                </td>
                                <td>
                                    <select name="obj[qua_trinh_cong_tac][{{@$key}}][position][id]"
                                            class="select-search select-md" id="qua_trinh_cong_tac_{{@$key}}_position"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @isset($allPosition)
                                            @foreach(collect($allPosition)->filter(function ($item)use($obj,$key){
                                                if(isset($obj['qua_trinh_cong_tac'][$key]['position']['department']['id']) &&  $item['department']['id']){
                                                   return  $obj['qua_trinh_cong_tac'][$key]['position']['department']['id'] == $item['department']['id'];
                                                }else{
                                                return false;
                                                }
                                            }) as $val)
                                                <option @if(isset($obj['qua_trinh_cong_tac'][$key]['position']['id']) && $obj['qua_trinh_cong_tac'][$key]['position']['id']==$val['_id'])
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                            @endforeach
                                        @endisset
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[qua_trinh_cong_tac][{{@$key}}][ngay_bat_dau]"
                                           placeholder="Ngày bắt đầu..."
                                           @if(isset($value['ngay_bat_dau']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_bat_dau'])}}" @endif>

                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[qua_trinh_cong_tac][{{@$key}}][ngay_ket_thuc]"
                                           placeholder="Ngày kết thúc..."
                                           @if(isset($value['ngay_ket_thuc']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_ket_thuc'])}}" @endif>

                                </td>
                                <td>

                                    <select name="obj[qua_trinh_cong_tac][{{@$key}}][project][id]"
                                            class="select-search select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @isset($allProject)
                                            @foreach($allProject as $val)
                                                <option @if(isset($obj['qua_trinh_cong_tac'][$key]['project']['id']) && $obj['qua_trinh_cong_tac'][$key]['project']['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                            @endforeach
                                        @endisset
                                    </select>
                                </td>
                                <td class="text-right"><i class="icon-trash text-danger"
                                                          onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endisset

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" name="obj[files_thong_tin_cong_viec]" value="">

    <div class="col-md-6">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <a class="" data-toggle="collapse" href="#gFile" aria-expanded="true">File đính kèm</a>
                </h6>
            </div>
            <div id="gFile" class="panel-collapse collapse in" aria-expanded="true">
                <div class="panel-body no-padding-bottom">
                    <div id="documentFileRegion">
                        @if(isset($obj['files_thong_tin_cong_viec']) && $obj['files_thong_tin_cong_viec'] && is_array($obj['files_thong_tin_cong_viec']))
                            @foreach($obj['files_thong_tin_cong_viec'] as $key=> $file)
                                <div class="form-group js-document-container" id="file_{{$key}}">

                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <div style="display: flex">
                                                <input type="text" style="z-index: 0" class="form-control "
                                                       name="obj[files_thong_tin_cong_viec][name][]"
                                                       @isset($file['name']) value="{{$file['name']}}" @endisset
                                                       placeholder="Tên file">
                                                <input type="text" style="z-index: 0" readonly=""
                                                       class="form-control js-document-file"
                                                       name="obj[files_thong_tin_cong_viec][path][]"
                                                       @isset($file['path']) value="{{$file['path']}}" @endisset

                                                       placeholder="File tài liệu">
                                            </div>

                                            <div class="input-group-btn">
                                                <a target="_blank"
                                                   href="{{\App\Http\Models\Media::getFileLink($file['path'])}}"
                                                   class="btn btn-default js-document-link">Xem file</a>
                                                <a onclick="_removeFile('#file_{{$key}}')" href="javascript:void(0)"
                                                   class="btn btn-default js-document-del"><i
                                                            class="icon-trash text-danger"></i> </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="col-md-11 p-3  text-right">
                            <button id="pickfiles" type="button" class="btn bg-primary btn-xs"><i
                                        class="fa fa-plus"></i>
                                Thêm file
                            </button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="col-md-6 text-right">
        <button class="btn btn-primary" id="save-button"
                onclick="return MNG_POST.update('{{admin_link('/staff/_save_tab_work')}}','#postInputForm');">
            Lưu lại
        </button>
    </div>

</form>
<div style="display: none" id="uploadFileItemClone">
    <div class="form-group js-document-container">
        <div class="col-md-12">
            <div class="input-group"><span
                        style="position: absolute;display: none; z-index: 100; right: 200px; top: 8px;"
                        class="js-document-loading"><i
                            class="fa fa-spinner fa-spin"></i> Đang upload vui lòng đợi....</span>
                <div style="display: flex">
                    <input type="text" style="z-index: 0" class="form-control "
                           name="obj[files_thong_tin_cong_viec][name][]" value="" placeholder="Tên file">
                    <input type="text" style="z-index: 0" readonly="" class="form-control js-document-file"
                           name="obj[files_thong_tin_cong_viec][path][]" value="" placeholder="File tài liệu">

                </div>
                <div class="input-group-btn">
                    <a target="_blank" href="" class="btn btn-default js-document-link">Xem file</a>
                    <a href="javascript:void(0)" class="btn btn-default js-document-del"><i
                                class="icon-trash text-danger"></i> </a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    INPUT_NUMBER()

    function clone_thong_tin_hop_dong_lao_dong() {
        let temp_select_class = "select-seart-" + Number(new Date())
        let tt = $('#obj_thong_tin_hop_dong_lao_dong tr').length;
        let tmp = `
<tr>
        <td>${tt + 1}</td>
        <td>

            <select type="text" class="form-control"
                    name="obj[thong_tin_hop_dong_lao_dong][${tt}][tinh_trang]"
            >
                <option value="">Chưa lựa chọn</option>
                <option value="Đang công tác">Đang công tác</option>
                <option value="Đã nghỉ việc">Đã nghỉ việc</option>
                <option value="Tạm nghỉ">Tạm nghỉ</option>
            </select>
        </td>
        <td>
            <select type="text" class="form-control"
                    name="obj[thong_tin_hop_dong_lao_dong][${tt}][loai_hop_dong]">
                <option value="">Chưa lựa chọn</option>
                <option value="Cộng tác viên">Cộng tác viên</option>
                <option value="Ngắn hạn">Ngắn hạn</option>
                <option value="Có thời hạn">Có thời hạn</option>
                <option value="Không xác định">Không xác định</option>
                <option value="Chưa ký Hợp đồng">Chưa ký Hợp đồng</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control datepicker"
                   name="obj[thong_tin_hop_dong_lao_dong][${tt}][ngay_bat_dau]"

                   placeholder="Chọn ngày...">

        </td>
        <td>
            <input type="text" class="form-control datepicker"
                   name="obj[thong_tin_hop_dong_lao_dong][${tt}][ngay_ket_thuc]"
                   placeholder="Ngày cấp..."

            >

        </td>
        <td class="text-right"><i class="icon-trash text-danger"
                                  onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
        </td>
    </tr>
`
        $('#obj_thong_tin_hop_dong_lao_dong').append(tmp)
        DATE_PICKER_INIT()

        $(`.${temp_select_class}`).select2()
    }

    function clone_qua_trinh_cong_tac() {
        let temp_select_class = "select-seart-" + Number(new Date())
        let tt = $('#obj_qua_trinh_cong_tac tr').length;
        let tmp = `
<tr>
    <td>${tt + 1}</td>

        <td>
            <select name="obj[qua_trinh_cong_tac][${tt}][department][id]"
                class="${temp_select_class} select-md"
                onchange="return APPLICATION._changeDepartment(jQuery(this).val(),'#qua_trinh_cong_tac_${tt + 1}_position','Chọn chức vụ')"

        >
            <option value="">Chưa lựa chọn</option>
            @isset($allDepartment)
                @foreach($allDepartment->sortBy('name') as $val)
            <option value="{{$val['_id']}}">{{$val['name']}}</option>

                @endforeach
                @endisset
            </select>

        </td>
        <td>
        <select name="obj[qua_trinh_cong_tac][${tt}][position][id]"
                class="${temp_select_class} select-md"
                id="qua_trinh_cong_tac_${tt + 1}_position"
        >
            <option value="">Chưa lựa chọn</option>
            @isset($allPosition)
                @foreach($allPosition as $val)
            <option value="{{$val['_id']}}">{{$val['name']}}</option>

                @endforeach
                @endisset
            </select>
        </td>
        <td>
            <input type="text" class="form-control datepicker"
                   name="obj[qua_trinh_cong_tac][${tt}][ngay_bat_dau]"
                   placeholder="Ngày bắt đầu..."

            >

        </td>
        <td>
            <input type="text" class="form-control datepicker"
                   name="obj[qua_trinh_cong_tac][${tt}][ngay_ket_thuc]"
                   placeholder="Ngày kết thúc..."
            >

        </td>
        <td>
            <select name="obj[qua_trinh_cong_tac][${tt}][project][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @isset($allProject)
                @foreach($allProject as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>

                @endforeach
                @endisset
            </select>

        </td>
        <td class="text-right"><i class="icon-trash text-danger"
                                  onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
        </td>
    </tr>
`
        $('#obj_qua_trinh_cong_tac').append(tmp)
        DATE_PICKER_INIT()

        $(`.${temp_select_class}`).select2()
    }

    MNG_MEDIA.uploadInit({
        loading_element: '#loading_upload',
        input_element: '#document_file',
        link_element: '#document_file_link'
    });

    function _removeFile($element) {
        bootbox.confirm("File của bạn sẽ bị xóa.<br/>Bạn có chắc chắn muốn thực hiện hành động này?", function (result) {
            if (result) {
                $($element).remove();
            }
        });
    }

</script>
