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
    .select2-choice{
        height: auto;
    }
    .select2-choice .select2-chosen {
        margin-right: 28px;
        padding-left: 1px;
        display: block;
        overflow: hidden;
        white-space: normal;
        text-overflow: inherit;
        float: none;
        width: auto;
    }
    .select2-chosen {
        max-width: unset !important;
    }

</style>
<script>
    $.datepicker.setDefaults({
        dateFormat: 'dd/mm/yy'
    });
</script>
<form action="{{admin_link('/staff/save_tab_edu')}}" id="postInputForm">
    <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <input type="hidden" name="obj[_id]" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>

    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    Thông tin bằng cấp
                </h6>
            </div>
            <div class="" style="overflow: scroll">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th width="1">TT</th>
                        <th width="10">Bằng cấp</th>
                        <th width="50">Chuyên môn</th>
                        <th width="100">Chuyên ngành</th>
                        <th width="10">Ngày cấp</th>
                        <th width="100">Nơi cấp bằng</th>
                        <th width="1" class="text-right">
                            <a onclick="clone_bang_cap()"
                               class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>
                    </tr>

                    </thead>
                    <tbody id="obj_bang_cap">
                    @isset($obj['bang_cap'])
                        @foreach(@$obj['bang_cap'] as $key=>$value)
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>

                                    <select name="obj[bang_cap][{{$key}}][loai_bang_cap][id]"
                                            class="select-search narrow wrap select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_BANG_CAP;}) as $val)

                                            <option @if(isset($obj['bang_cap'][$key]['loai_bang_cap']['id']) && $obj['bang_cap'][$key]['loai_bang_cap']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>

                                </td>
                                <td>
                                    <select name="obj[bang_cap][{{$key}}][chuyen_mon][id]"
                                            class="select-search narrow wrap narrow wrap select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUYEN_MON;}) as $val)

                                            <option @if(isset($obj['bang_cap'][$key]['chuyen_mon']['id']) && $obj['bang_cap'][$key]['chuyen_mon']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>

                                </td>
                                <td>
                                    <select name="obj[bang_cap][{{$key}}][chuyen_nganh][id]"
                                            class="select-search narrow wrap narrow wrap select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUYEN_NGANH;}) as $val)

                                            <option @if(isset($obj['bang_cap'][$key]['chuyen_nganh']['id']) && $obj['bang_cap'][$key]['chuyen_nganh']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[bang_cap][{{$key}}][ngay_cap]"
                                           placeholder="Ngày cấp..."
                                           @if(isset($value['ngay_cap']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_cap'])}}" @endif>

                                </td>
                                <td>
                                    <select name="obj[bang_cap][{{$key}}][noi_cap][id]"
                                            class="select-search narrow wrap narrow wrap select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NOI_CAP_BANG_CAP;}) as $val)

                                            <option @if(isset($obj['bang_cap'][$key]['noi_cap']['id']) && $obj['bang_cap'][$key]['noi_cap']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
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
    <div class="col-md-7">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    Thông tin chứng chỉ đào tạo
                </h6>
            </div>
            <div class="" style="overflow: scroll">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>TT</th>
                        <th>Loại chứng chỉ</th>
                        <th>Hạng chứng chỉ</th>

                        <th>Ngày cấp</th>
                        <th>Nơi cấp chứng chỉ</th>
                        <th width="1" class="text-right">
                            <a onclick="clone_chung_chi_dao_tao()"
                               class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>

                    </tr>

                    </thead>
                    <tbody id="obj_chung_chi_dao_tao">
                    @isset($obj['chung_chi_dao_tao'])
                        @foreach(@$obj['chung_chi_dao_tao'] as $key=>$value)
                            <tr>
                                <td></td>
                                <td>
                                    <select name="obj[chung_chi_dao_tao][{{$key}}][loai_chung_chi][id]"
                                            class="select-search narrow wrap select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUNG_CHI;}) as $val)

                                            <option @if(isset($obj['chung_chi_dao_tao'][$key]['loai_chung_chi']['id']) && $obj['chung_chi_dao_tao'][$key]['loai_chung_chi']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control "
                                           name="obj[chung_chi_dao_tao][{{@$key}}][hang_chung_chi]"
                                           placeholder="Hạng chứng chỉ"
                                           @if(isset($value['hang_chung_chi']))
                                           value="{{$value['hang_chung_chi']}}" @endif>

                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[chung_chi_dao_tao][{{@$key}}][ngay_cap]"
                                           placeholder="Ngày cấp..."
                                           @if(isset($value['ngay_cap']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_cap'])}}" @endif>


                                </td>
                                <td>
                                    <select name="obj[chung_chi_dao_tao][{{$key}}][noi_cap][id]"
                                            class="select-search narrow wrap select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NOI_CAP_BANG_CAP;}) as $val)

                                            <option @if(isset($obj['chung_chi_dao_tao'][$key]['noi_cap']['id']) && $obj['chung_chi_dao_tao'][$key]['noi_cap']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
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
    <div class="col-md-5">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <a class="" data-toggle="collapse" href="#gFile" aria-expanded="true">File đính kèm</a>
                </h6>
            </div>
            <input type="hidden" name="obj[files_thong_tin_dao_tao]" value="">

            <div id="gFile" class="panel-collapse collapse in" aria-expanded="true">
                <div class="panel-body no-padding-bottom">
                    <div id="documentFileRegion">
                        @if(isset($obj['files_thong_tin_dao_tao']) && $obj['files_thong_tin_dao_tao'] && is_array($obj['files_thong_tin_dao_tao']))
                            @foreach($obj['files_thong_tin_dao_tao'] as $key=> $file)
                                <div class="form-group js-document-container" id="file_{{$key}}">

                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <div style="display: flex">
                                                <input type="text" style="z-index: 0" class="form-control "
                                                       name="obj[files_thong_tin_dao_tao][name][]"
                                                       @isset($file['name']) value="{{$file['name']}}" @endisset
                                                       placeholder="Tên file">
                                                <input type="text" style="z-index: 0" readonly=""
                                                       class="form-control js-document-file"
                                                       name="obj[files_thong_tin_dao_tao][path][]"
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

    <div class="col-md-12 text-right">
        <button class="btn btn-primary" id="save-button"
                onclick="return MNG_POST.update('{{admin_link('/staff/_save_tab_edu')}}','#postInputForm');">
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
                           name="obj[files_thong_tin_dao_tao][name][]" value="" placeholder="Tên file">
                    <input type="text" style="z-index: 0" readonly="" class="form-control js-document-file"
                           name="obj[files_thong_tin_dao_tao][path][]" value="" placeholder="File tài liệu">

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

    function clone_bang_cap() {
        let temp_select_class = "select-seart-" + Number(new Date())
        let tt = $('#obj_bang_cap tr').length;
        let tmp = `
 <tr>
    <td>${tt + 1}</td>
    <td>
        <select name="obj[bang_cap][${tt + 1}][loai_bang_cap][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_BANG_CAP;}) as $val)

            <option value="{{$val['_id']}}">{{$val['name']}}</option>

            @endforeach
            </select>

        </td>
        <td>
            <select name="obj[bang_cap][${tt + 1}][chuyen_mon][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUYEN_MON;}) as $val)

            <option value="{{$val['_id']}}">{{$val['name']}}</option>

            @endforeach
            </select>

        </td>
        <td>
            <select name="obj[bang_cap][${tt + 1}][chuyen_nganh][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUYEN_NGANH;}) as $val)

            <option value="{{$val['_id']}}">{{$val['name']}}</option>

            @endforeach
            </select>

        </td>
        <td>
            <input type="text" class="form-control datepicker"
                   name="obj[bang_cap][${tt + 1}][ngay_cap]"
               placeholder="Ngày cấp..."
        >

    </td>
    <td>
        <select name="obj[bang_cap][${tt + 1}][noi_cap][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NOI_CAP_BANG_CAP;}) as $val)

            <option value="{{$val['_id']}}">{{$val['name']}}</option>

            @endforeach
            </select>


        </td>
        <td class="text-right"><i class="icon-trash text-danger"
                                  onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
        </td>
    </tr>
`
        $('#obj_bang_cap').append(tmp)
        DATE_PICKER_INIT()

        $(`.${temp_select_class}`).select2()
    }

    function clone_chung_chi_dao_tao() {
        let temp_select_class = "select-seart-" + Number(new Date())
        let tt = $('#obj_chung_chi_dao_tao tr').length;
        let tmp = `
        <tr>
    <td>${tt + 1}</td>
    <td>
        <select name="obj[chung_chi_dao_tao][${tt + 1}][loai_chung_chi][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUNG_CHI;}) as $val)

            <option value="{{$val['_id']}}">{{$val['name']}}</option>

            @endforeach
            </select>
        </td>
          <td>
                                    <input type="text" class="form-control "
                                           name="obj[chung_chi_dao_tao][${tt + 1}][hang_chung_chi]"
                                           placeholder="Hạng chứng chỉ"
                                     >

                                </td>
        <td>
            <input type="text" class="form-control datepicker"
                   name="obj[chung_chi_dao_tao][${tt + 1}][ngay_cap]"
                   placeholder="Ngày cấp..."
            >
        </td>
        <td>
            <select name="obj[chung_chi_dao_tao][${tt + 1}][noi_cap][id]"
                class="${temp_select_class} select-md"
        >
            <option value="">Chưa lựa chọn</option>
            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NOI_CAP_BANG_CAP;}) as $val)
            <option value="{{$val['_id']}}">{{$val['name']}}</option>
            @endforeach
            </select>

        </td>
        <td class="text-right"><i class="icon-trash text-danger"
                                  onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
        </td>
    </tr>
`
        $('#obj_chung_chi_dao_tao').append(tmp)
        DATE_PICKER_INIT()

        $(`.${temp_select_class}`).select2({
        })
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


