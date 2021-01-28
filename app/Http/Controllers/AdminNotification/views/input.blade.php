@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
@stop
@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Cập nhật thông thông báo gửi cho app </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('notification')}}">Quản lý thông báo</a></li>
            @if(isset($obj['_id']) && $obj['_id'])
                <li class="active">Cập nhật thông tin</li>
            @else
                <li class="active">Thêm thông báo mới</li>
            @endif
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('notification/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm thông báo mới
                </a>
            </li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <form name="postInputForm" autocomplete="off" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm" class="form-horizontal " method="post">
            <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:0}}"/>
            <div class="col-md-6">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Bước 1: Nhập nội dung thông báo</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">Tiêu đề</label>
                                <div class="col-md-9">
                                    <input name="obj[title]" id="obj-title" value="{{isset($obj['title'])?$obj['title']:''}}" type="text" class="form-control" placeholder="Tiêu đề thông báo">
                                </div>
                            </div>

                            {{-- <div class="form-group">
                                 <label class="control-label col-md-3">Mô tả ngắn</label>
                                 <div class="col-md-9">
                                     <textarea name="obj[brief]" id="obj-brief" class="form-control" placeholder="">{{isset($obj['brief'])?$obj['brief']:''}}</textarea>
                                 </div>
                             </div>--}}
                            <div class="form-group">
                                <label class="control-label col-md-3">Nội dung </label>
                                <div class="col-md-9">
                                    <textarea name="obj[brief]" id="obj-brief" class="form-control" placeholder="">{{isset($obj['brief'])?$obj['brief']:''}}</textarea>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer p-3">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Notification::buildLinkDelete($obj,'notification')}}','{{$obj['_id']}}')"
                                        class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa
                                </button>

                            @endif
                            <button type="button" onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm');" class="btn  bg-primary-800 pull-right">
                                <i class=" icon-database-check position-left"></i>Lưu lại
                            </button>
                        </div>
                    </div>
                </div>
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gAccount" aria-expanded="true">Hướng dẫn</a>
                        </h3>
                    </div>
                    <div id="gAccount" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <ul>
                                <li>Bước 1: nhập thông tin, nội dung thông báo</li>
                                <li>Bước 2: Chọn tiêu chí khách hàng & kiểm tra</li>
                                <li><i>Bạn có thể lưu thông tin đã sửa, chọn bằng cách click nút lưu lại</i></li>
                                <li>Bước 3: Gửi thông báo <i class="text-warning-800">*Trước khi gửi cần lưu lại</i></li>
                                <li><i class="text-warning">Tin nhắn sẽ chưa được gửi đi nếu bạn không thao tác bước 3</i></li>

                            </ul>

                        </div>
                        <div class="panel-footer p-3">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.sendNotification('{{url('/app/notification/send')}}','#postInputForm');" class="btn btn-info bg-teal-800 pull-right">
                                    <i class=" icon-connection position-left"></i>Bước 3: Gửi thông báo
                                </button>
                            @else
                                <button type="button" onclick="alert('Bạn cần lưu thông báo trước khi thực hiện gửi')" disabled class="btn btn-default pull-right">
                                    <i class=" icon-connection position-left"></i>Bước 3: Bạn cần lưu lại trước khi gửi
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-6">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gAccount" aria-expanded="true">Bước 2: Chọn tiêu chí người nhận</a>
                        </h3>
                    </div>
                    <div id="gAccount" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">Có Email?</label>
                                <div class="col-md-9">
                                    <select name="obj[filter][have_email]" class="select">
                                        <option value="">Mặc định</option>
                                        <option value="co-email" @if(isset($obj['filter']['have_email']) && $obj['filter']['have_email']=='co-email')selected @endif>Có email</option>
                                        <option value="co-email-da-kich-hoat" @if(isset($obj['filter']['have_email']) && $obj['filter']['have_email']=='co-email-da-kich-hoat')selected @endif>Có email & đã kích hoạt</option>
                                        <option value="co-email-chua-kich-hoat" @if(isset($obj['filter']['have_email']) && $obj['filter']['have_email']=='co-email-chua-kich-hoat')selected @endif>Có email & chưa kích hoạt</option>
                                        <option value="no-email" @if(isset($obj['filter']['have_email']) && $obj['filter']['have_email']=='no-email')selected @endif>Không có email</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Có Phone?</label>
                                <div class="col-md-9">
                                    <select name="obj[filter][have_phone]" class="select">
                                        <option value="">Mặc định</option>
                                        <option value="co-phone-da-kich-hoat" @if(isset($obj['filter']['have_phone']) && $obj['filter']['have_phone']=='co-phone-da-kich-hoat')selected @endif>Đã kích hoạt</option>
                                        <option value="co-phone-chua-kich-hoat" @if(isset($obj['filter']['have_phone']) && $obj['filter']['have_phone']=='co-phone-chua-kich-hoat')selected @endif>Chưa kích hoạt</option>
                                    </select>
                                </div>
                            </div>
                            {{--<div class="form-group">
                                <label class="control-label col-md-3">Có Xe?</label>
                                <div class="col-md-9">
                                    <select name="obj[filter][have_car]" class="select">
                                        <option>Mặc định</option>
                                        <option value="no-car" @if(isset($obj['filter']['have_phone']) && $obj['filter']['have_phone']=='co-phone-da-kich-hoat')selected @endif>Chưa có xe nào</option>
                                        <option value="">Có xe</option>
                                        <option>Chỉ gửi cho member cho xe oto</option>
                                        <option>Chỉ gửi cho member có xe máy</option>
                                    </select>
                                </div>
                            </div>--}}
                            <div class="form-group">
                                <label class="control-label col-md-3">Hạn sử dụng?</label>
                                <div class="col-md-9">
                                    <select name="obj[filter][end_at]" class="select">
                                        <optgroup label="Sắp hết hạn">
                                            <option value="">Mặc định</option>
                                            <option value="in-3day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='in-3day') selected @endif>Còn thời hạn <=3 ngày</option>
                                            <option value="in-7day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='in-7day') selected @endif>Còn thời hạn <=1 tuần</option>
                                            <option value="in-14day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='in-14day') selected @endif>Còn thời hạn <=2 tuần</option>
                                            <option value="in-30day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='in-30day') selected @endif>Còn thời hạn <=1 tháng</option>
                                        </optgroup>
                                        <optgroup label="Đã hết hạn">
                                            <option value="out-3day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='out-3day') selected @endif>Đã hết hạn cách đây <=3 ngày</option>
                                            <option value="out-7day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='out-7day') selected @endif>Đã hết hạn cách đây <=1 tuần</option>
                                            <option value="out-14day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='out-14day') selected @endif>Đã hết hạn cách đây <=2 tuần</option>
                                            <option value="out-30day" @if(isset($obj['filter']['end_at']) && $obj['filter']['end_at']=='out-30day') selected @endif>Đã hết hạn cách đây <=1 tháng</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">Ngày sinh nhật?</label>
                                <div class="col-md-5">
                                    <input name="obj[filter][birthday][start]" id="obj-filter-brith-start" value="{{isset($obj['filter']['birthday']['start'])?$obj['filter']['birthday']['start']:''}}" type="text" class="form-control datepicker" placeholder="Sinh nhật từ ngày">

                                </div>
                                <div class="col-md-4">
                                    <input name="obj[filter][birthday][end]" id="obj-filter-brith-end" value="{{isset($obj['filter']['birthday']['end'])?$obj['filter']['birthday']['end']:''}}" type="text" class="form-control datepicker" placeholder="Sinh nhật đến ngày">

                                </div>
                            </div>
                            <fieldset>
                                <legend>Hoặc chỉ gửi trong danh sách sau:</legend>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Có số điện thoại trong danh sách này</label>
                                    <div class="col-md-9">
                                        <textarea name="obj[filter][phone]" class="form-control" placeholder="Nhập danh sách số phone, cách nhau bởi dấu phẩy">{{isset($obj['filter']['phone'])?$obj['filter']['phone']:''}}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3">Có email trong danh sách này</label>
                                    <div class="col-md-9">
                                        <textarea name="obj[filter][email]" class="form-control" placeholder="Nhập danh sách email, cách nhau bởi dấu phẩy">{{isset($obj['filter']['email'])?$obj['filter']['email']:''}}</textarea>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Hoặc theo điều kiện sau:</legend>
                                <div class="form-group">
                                    <label class="control-label col-md-3">Tất cả khách hàng?</label>
                                    <div class="col-md-9">
                                        <select name="obj[filter][all]" class="select">
                                            <option value="">Mặc định</option>
                                            <option value="all" @if(isset($obj['filter']['all']) && $obj['filter']['all']=='all') selected @endif>Toàn bộ khách hàng</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>

                        </div>
                        <div class="panel-footer p-3">
                            <button type="button" onclick="return MNG_POST.update('{{admin_link('notification/filter-member')}}','#postInputForm');" class="btn btn-info pull-right">
                                <i class=" icon-search4 position-left"></i>Kiểm tra
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    @if(isset($listObj) && !$listObj->isEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-flat ">
                    <div class="panel-heading">
                        <h3 class="panel-title"><strong>Danh sách xe của khách</strong></h3>
                        (Tìm thấy : {{$listObj->total()}} xe - Theo số điện thoại của khách ({{$obj['phone']}}))
                    </div>
                    <div class="-table-responsive" id="domainTbl">
                        <table class="table table-striped  table-io">
                            <thead>
                            <tr>
                                <th class="text-center" width="10">STT</th>
                                <th width="160">
                                    Ảnh
                                </th>
                                <th>
                                    Tên Xe/Biển số
                                </th>
                                <th>
                                    Mã thiết bị
                                </th>

                                <th width="230">
                                    Ngày tạo/Cập nhật
                                </th>

                                <th class="text-center" width="115">Chức năng</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($listObj as $key=>$val)
                                <?php
                                $attrs = $val->attrs;
                                ?>
                                <tr id="itemRow_{{$val['id']}}">
                                    <td class="text-center">
                                        {{$key+1}}
                                    </td>
                                    <td>
                                        @if($val->image)
                                            <a href="{{\App\Http\Models\Media::getImageSrc($val->image)}}" data-popup="lightbox">
                                                <img src="{{\App\Http\Models\Media::getImageSrc($val->image)}}"
                                                     style="width: 60px;height: 48px"/>
                                            </a>
                                        @else
                                            <img src="{{url('/images/no-image.jpg')}}"
                                                 style="width: 60px;height: 48px"/>
                                        @endif
                                        @if($val->image_number)
                                            <a href="{{\App\Http\Models\Media::getImageSrc($val->image_number)}}" data-popup="lightbox">
                                                <img src="{{\App\Http\Models\Media::getImageSrc($val->image_number)}}"
                                                     style="width: 60px;height: 48px"/>
                                            </a>
                                        @else
                                            <img src="{{url('/images/no-image.jpg')}}"
                                                 style="width: 60px;height: 48px"/>
                                        @endif
                                    </td>
                                    <td>
                                        <b>{{$val['name']}}</b>
                                        <div>
                                            Biển: <b class="text-primary"> {{$val['number']}}</b>

                                        </div>
                                    </td>
                                    <td>
                                        @if($val['device_key']) <b class="text-success-700" title="Mã thiết bị">Mã: {{$val['device_key']}}</b>
                                        @else
                                            <b class="text-danger-400" title="Mã thiết bị">[Chưa gắn thiết bị]</b>
                                        @endif
                                        <div>
                                            @if(is_array($attrs))
                                                @foreach($attrs as $ks=>$vs)
                                                    @if($vs['label'])
                                                        {{$vs['type']}}: {{$vs['label']}}
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        Thêm: {{$val['created_at']}}
                                        <div>
                                            Cập nhật: {{$val['updated_at']}}
                                            {{--  {{date("d/m/Y",$val['updated_at'])}}--}}
                                        </div>
                                    </td>
                                    <td class="text-center">
                             <span style="margin-bottom: 5px"
                                   class="label label-{{ \App\Http\Models\Post::getStatus($val['status'])['style'] }}">{{ \App\Http\Models\Post::getStatus($val['status'])['text'] }}
                            </span>
                                        <ul class="icons-list">
                                            <li class="text-primary-600">
                                                <a href="{{admin_link('car/input?id='.$val['_id'])}}" title="Sửa"
                                                ><i class="icon-pencil7"></i>
                                                </a>
                                            </li>
                                            <li class="text-danger-600">
                                                <a href="javascript:void(0);" data-popup="tooltip" title="Xóa"
                                                   onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Car::buildLinkDelete($val)}}','{{$val['_id']}}')">
                                                    <i class="icon-trash"></i>
                                                </a>
                                            </li>
                                        </ul>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    @endif

    <script type="text/javascript">
        jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
            if (jqxhr.status == 200) {
                $(document).unbind('click.fb-start');
                $('[data-popup="lightbox"]').fancybox({
                    padding: 3
                });
            }
        });
    </script>
@stop
