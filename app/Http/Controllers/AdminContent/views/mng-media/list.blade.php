@extends($THEME_EXTEND)
@section('CSS_REGION')

@stop
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_checkboxes_radios.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/media/fancybox.min.js') !!}

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/daterangepicker.js') !!}

@stop
@section('BREADCRUMB_REGION')
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li><a href="javascript:void(0)">Quản lý nội dung</a></li>
            <li class="active">Quản lý thư viện media</li>
        </ul>

        <ul class="breadcrumb-elements">
            <li><a href="{{admin_link('media/input')}}"><i class="icon-plus3 position-left"></i> Thêm file,ảnh mới</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title">
                <a href="javascript:void(0)">Danh sách Hồ sơ</a>
            </h3>
            (Tổng số : {{$listObj->total()}} kết quả)
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_department" class="select-search  select-xs" style="width: 180px">
                                    <option value="0">--Tất cả Phòng ban--</option>
                                    @foreach($allDepartments as $k=>$val)
                                        <option @if(isset($q_department) && $val['_id'] == $q_department) selected="selected" @endif  value="{{ $val['_id'] }}">{{ $val['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="project_category" class="select-search  select-xs @if(request('project_category')) text-danger text-bold @endif">
                                    <option value="0">Hạng mục Dự án</option>
                                    @foreach($dataGroup as $val)
                                        @if($val['type'] == \App\Http\Models\MetaData::PROJECT_MODEL)
                                            <option @if(request('project_category') == $val['_id']) selected @endif  value="{{$val['_id']}}">{{$val['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="contract" class="select-search  select-xs @if(request('project')) text-danger text-bold @endif" style="width: 160px">
                                    <option value="0">Chọn Dự án</option>
                                    @foreach($allProject as $val)
                                        @if($val['removed'] == \App\Http\Models\BaseModel::REMOVED_NO)
                                            <option @if(isset($obj['project']) && $obj['project'] == $val['_id'])) selected="selected" @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="contract" class="select-search  select-xs @if(request('contract')) text-danger text-bold @endif" style="width: 220px">
                                    <option value="0">Chọn Hợp đồng</option>
                                    @foreach($allContract as $val)
                                        @if($val['removed'] == \App\Http\Models\BaseModel::REMOVED_NO)
                                            <option @if(isset($obj['contract']) && $obj['contract'] == $val['_id'])) selected="selected" @endif value="{{$val['_id']}}">[{{$val['number']}}]-{{$val['contract_name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input-group content-group">
                            <div class="has-feedback has-feedback-left">
                                <input type="text" value="{{request('q')}}" name="q" class="form-control" placeholder="Từ khóa..." style="width: 175px"/>
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>

                            <div class="input-group-btn">
                                <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Tìm</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <div class="panel-body">
            <div class="pop-media-list" id="pop-media-list">
                <div class="" id="MEDIA-CONTAINER">
                    @if(isset($listObj) && $listObj)
                        @foreach($listObj as $key=>$val)
                            <div class="col-md-2 col-sm-6 media-item">
                                <div class="thumbnail" data-id="MEDIA" id="MEDIA-{{$val['_id']}}">
                                    <div class="thumb">
                                        <img rel="data-container" onerror="this.src='{{url('backend-ui/assets/images/no-image-available.jpg')}}';this.onerror = null"
                                             src="{{\App\Http\Models\Media::getImageSrc($val['src'])}}">
                                        <div onclick="return MNG_MEDIA.imageSelected('{{$val['_id']}}')" class="caption-overflow">
                                        <span>
                                            <a href="{{\App\Http\Models\Media::getImageSrc($val['src'])}}" data-popup="lightbox" rel="gallery"
                                               class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class=" icon-eye4"></i></a>
                                        </span>
                                        </div>
                                    </div>

                                    <div class="caption">
                                        <h7 class="no-margin">
                                            <a href="#" class="text-default">
                                                {{\App\Elibs\Helper::showMongoDate($val['created_photo'], "d/m/Y")}}
                                            </a>
                                            <br>{{$val['project']['name']}}
                                            <div style="float: right">
                                                <a href="{{admin_link('media/input?id='.$val['_id'])}}" title="Sửa">Sửa</a> |
                                                <a href="javascript:void(0);" data-popup="tooltip" title="Xóa"
                                                   onclick="return MNG_POST.deleteItem('{{\App\Http\Models\BaseModel::buildLinkDelete($val,'media')}}','{{$val['_id']}}')">Xóa</a>
                                                {{--<ul class="icons-list">
                                                    <li class="text-primary-300">
                                                        <a href="{{admin_link('media/input?id='.$val['_id'])}}" title="Sửa"><i class="icon-pencil7"></i></a>
                                                    </li>
                                                    <li class="text-danger-300">
                                                        <a href="javascript:void(0);" data-popup="tooltip" title="Xóa"
                                                           onclick="return MNG_POST.deleteItem('{{\App\Http\Models\BaseModel::buildLinkDelete($val,'media')}}','{{$val['_id']}}')">
                                                            <i class="icon-trash"></i>
                                                        </a>
                                                    </li>
                                                </ul>--}}
                                            </div>

                                        </h7>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div id="noMedia">
                            <div class="no-text">
                                Không tìm thấy hình ảnh/video nào trong thư viện.
                            </div>
                            <i class="icon-nbsp"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

@stop

@section('JS_REGION')
    <script type="text/javascript">
        function fancyboxInit() {

            $(document).unbind('click.fb-start');
            $('[data-popup="lightbox"]').fancybox({
                padding: 3
            });
        }
       $(document).ready(function(){
           fancyboxInit();
       });
    </script>
@stop