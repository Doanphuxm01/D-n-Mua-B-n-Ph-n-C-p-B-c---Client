@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/pickadate/picker.time.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/notifications/bootbox.min.js') !!}

@stop
@section('BREADCRUMB_REGION')

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('media/list')}}">Danh sách ảnh</a></li>
            <li class="active">Cập nhật ảnh</li>
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('media/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm ảnh
                </a>
            </li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')

    <div class="row">
        <form name="postInputForm" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm" class="form-horizontal " method="post">
            <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:0}}"/>
            <div class="col-md-12">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Thông tin Ảnh</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="control-label col-md-2">Ngày chụp</label>
                                <div class="col-md-4">
                                    <input name="obj[created_photo]"
                                           value="@if(isset($obj['created_photo']) && $obj['created_photo'] != ""){{\App\Elibs\Helper::showMongoDate($obj['created_photo'], "d/m/Y")}}@endif"
                                           type="text" class="form-control datepicker" placeholder="Ngày chụp" title="Ngày chụp">
                                </div>

                                <label class="control-label col-md-2" style="text-align: right">Thuộc Phòng ban</label>
                                <div class="col-md-4">
                                    <select name="obj[department]" class="select-search select-xs" placeholder="Chọn Phòng ban">
                                        <option value="0">Chọn Phòng ban</option>
                                        @if(isset($allDepartments) && $allDepartments)
                                            @foreach($allDepartments as $item)
                                                <option @if(isset($obj['department']) && in_array($item['_id'], $obj['department'])) selected @endif  value="{{$item['_id']}}">{{$item['name']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Hạng mục Dự án</label>
                                <div class="col-md-4">
                                    <select name="project_category[]" multiple="multiple" class="select-search select-multi-small select-xs" placeholder="Chọn Hạng mục Dự án">
                                        @php
                                            $pro_cate = @array_column(@$obj['project_category'],'id');
                                        @endphp
                                        @foreach($dataGroup as $val)
                                            @if($val['type'] == \App\Http\Models\MetaData::PROJECT_MODEL)
                                                <option @if(isset($obj['project_category']) && is_array($pro_cate) && in_array($val['_id'],$pro_cate)) selected="selected" @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <label class="control-label col-md-2" style="text-align: right">Loại hình</label>
                                <div class="col-md-4">
                                    <select name="project_type[]"  multiple="multiple" class="select-search select-multi-small select-xs" placeholder="Chọn Loại hình Dự án">
                                        @php
                                            $pro_type = @array_column(@$obj['project_type'],'id');
                                        @endphp
                                        @foreach($dataGroup as $val)
                                            @if($val['type'] == \App\Http\Models\MetaData::PROJECT_TYPE)
                                                <option @if(isset($obj['project_type']) && is_array($pro_type) && in_array($val['_id'],$pro_type)) selected="selected" @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="control-label col-md-2">Thuộc Hợp đồng</label>
                                <div class="col-md-4">
                                    <select name="obj[contract]" class="select-search select-xs">
                                        <option value="0">Chọn Hợp đồng</option>
                                        @foreach($allContract as $val)
                                            @if($val['removed'] == \App\Http\Models\BaseModel::REMOVED_NO)
                                                <option @if(isset($obj['contract']) && $obj['contract'] && $obj['contract'] == $val['_id'])) selected="selected" @endif value="{{$val['_id']}}">[{{$val['number']}}]-{{$val['contract_name']}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <label class="control-label col-md-2" style="text-align: right">Thuộc dự án</label>
                                <div class="col-md-4">
                                    <select name="obj[project]" class="select-search select-xs">
                                        <option value="0">--Chọn dự án--</option>
                                        @foreach($allProject as $val)
                                            <option @if(isset($obj['project']) && is_array($obj['project']) && in_array($val['_id'],$obj['project']))  selected="selected" @endif value="{{ $val['_id'] }}">@if(isset($val['full_name'])){{ $val['full_name'] }}@else {{ $val['name'] }} @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-2">Mô tả thêm</label>
                                <div class="col-md-10">
                                    <textarea name="obj[description]" style="min-height: 50px;" class="form-control">{{isset($obj['description'])?$obj['description']:''}}</textarea>
                                </div>
                            </div>

                            <div id="documentFileRegion">
                                @if(isset($obj['files']) && $obj['files'] && is_array($obj['files']))
                                    @foreach($obj['files'] as $key=> $file)
                                        <div class="form-group js-document-container" id="file_{{$key}}">
                                            <label class="control-label col-md-2"></label>
                                            <div class="col-md-10">
                                                <div class="input-group">
                                                    <input type="text"  style="z-index: 0" readonly="" class="form-control js-document-file" name="obj[files][]" value="{{$file}}" placeholder="File tài liệu">
                                                    <div class="input-group-btn">
                                                        <a target="_blank" href="{{\App\Http\Models\Media::getFileLink($file)}}" class="btn btn-default js-document-link">Xem file</a>
                                                        <a onclick="_removeFile('#file_{{$key}}')" href="javascript:void(0)" class="btn btn-default js-document-del"><i class="icon-trash text-danger"></i> </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="col-md-12  text-right">
                                    <button id="pickfiles" type="button" class="btn bg-primary btn-xs"><i class="fa fa-plus"></i> Thêm ảnh</button>
                                </div>
                            </div>

                            @if(isset($obj['_id']))
                                @if(\App\Http\Models\Member::haveRole(\App\Http\Models\Member::mng_media_delete))
                                    <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\BaseModel::buildLinkDelete($obj,'media')}}','{{$obj['_id']}}')"
                                            class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa Ảnh
                                    </button>
                                @else
                                    <button type="button" disabled
                                            class="btn btn-default pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa Ảnh
                                    </button>
                                @endif
                            @endif

                            <button type="button" onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm','add-new');" class="btn btn-info bg-teal-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Lưu & Thêm mới
                            </button>
                            <button type="button" onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm');" class="btn btn-info bg-danger-800 pull-right mr-15">
                                <i class=" icon-database-check position-left"></i>Lưu lại
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </form>
    </div>
    <div style="display: none" id="uploadFileItemClone">
        <div class="form-group js-document-container">
            <label class="control-label col-md-3"></label>
            <div class="col-md-9">
                <div class="input-group"><span style="position: absolute;display: none; z-index: 100; right: 200px; top: 8px;" class="js-document-loading"><i class="fa fa-spinner fa-spin"></i> Đang upload vui lòng đợi....</span>
                    <input type="text"  style="z-index: 0" readonly="" class="form-control js-document-file" name="obj[files][]" value="" placeholder="File tài liệu">
                    <div class="input-group-btn">
                        <a target="_blank" href="" class="btn btn-default js-document-link">Xem file</a>
                        <a href="javascript:void(0)" class="btn btn-default js-document-del"><i class="icon-trash text-danger"></i> </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        MNG_MEDIA.uploadInit({loading_element:'#loading_upload',input_element:'#document_file',link_element:'#document_file_link'});

        function  _removeFile($element) {
            bootbox.confirm("File của bạn sẽ bị xóa.<br/>Bạn có chắc chắn muốn thực hiện hành động này?", function(result) {
                if(result){
                    $($element).remove();
                }
            });
        }

    </script>

@stop
