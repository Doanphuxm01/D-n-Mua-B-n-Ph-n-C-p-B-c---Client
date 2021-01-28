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
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Cập nhật thông tin khách hàng </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('customer')}}">Quản lý khách hàng</a></li>
            @if(isset($obj['_id']) && $obj['_id'])
                <li class="active">Cập nhật thông tin</li>
            @else
                <li class="active">Thêm khách hàng mới</li>
            @endif
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('customer/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm khách hàng mới
                </a>
            </li>
        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <form name="postInputForm" autocomplete="off" onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm" class="w-100 d-flex form-horizontal " method="post">
            <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:0}}"/>
            <div class="col-md-6">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gInfo" aria-expanded="true">Thông tin khách hàng</a>
                        </h3>
                    </div>
                    <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="col">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Họ tên','key'=>'name'],
                                    'note'=>['label'=>'*','class'=>'text-danger']])

                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Số điện thoại','key'=>'phone'],
                                        'note'=>['label'=>'*','class'=>'text-danger']])
                            </div>

                            {{--<div class="form-group">
                                <label class="control-label col-md-3">Phone (dùng để login)</label>
                                <div class="col-md-6">
                                    <input name="obj[phone]" id="obj-phone" value="{{isset($obj['phone'])?$obj['phone']:''}}" type="text" class="form-control" placeholder="Số điện thoại dùng để đăng nhập">
                                </div>
                                <div class="col-md-3">
                                    <select id="obj-verified_phone" name="obj[verified][phone]" class="select">
                                        <option value=false>Chưa xác thực</option>
                                        <option @if(isset($obj->verified['phone']) && $obj->verified['phone']=='true') selected="selected" @endif value=true>Đã xác thực</option>
                                    </select>
                                </div>
                            </div>--}}
                            <div class="col d-flex pr-0">
                                @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Email','key'=> 'email'], 'group'=>['class'=>'col-md-9 mr-0 pl-0']])
                                <div class="col-md-3 pr-0">
                                    <label for="">Trạng thái xác thực email</label>
                                    <select id="obj-verified_email" name="obj[verified][email]" class="select">
                                        <option value=false>Chưa xác thực</option>
                                        <option @if(isset($obj->verified['email']) && $obj->verified['email']=='true') selected="selected" @endif value=true>Đã xác thực</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Địa chỉ</label>
                                <div class="col-md-9">
                                    <input name="obj[addr]" id="obj-addr" value="{{isset($obj['addr'])?$obj['addr']:''}}" type="text" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Ngày sinh</label>
                                <div class="col-md-9">
                                    <input name="obj[birthday]" id="obj-birthday"
                                           value="@if(isset($obj['birthday'])){{\App\Elibs\Helper::showMongoDate($obj['birthday'],'d/m/Y')}}@endif"
                                           type="text" class="form-control datepicker" placeholder="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3">Giới tính</label>
                                <div class="col-md-9">
                                    <select id="obj-gender" name="obj[gender]" class="select">
                                        <option value="">Chọn giới tính</option>
                                        <option @if(isset($obj['gender']) && $obj['gender']=='male') selected="selected" @endif value="male">Nam giới</option>
                                        <option @if(isset($obj['gender']) && $obj['gender']=='female') selected="selected" @endif value="male">Nữ giới</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Trạng thái</label>
                                <div class="col-md-9">
                                    <select id="obj-status" name="obj[status]" class="select">
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_ACTIVE) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_ACTIVE}}">Hoạt động</option>
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_PENDING) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_PENDING}}">Chờ duyệt</option>
                                        <option @if(isset($obj['status']) && $obj['status']==\App\Http\Models\Member::STATUS_DISABLE) selected="selected" @endif value="{{\App\Http\Models\Member::STATUS_DISABLE}}">Khóa</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="panel-footer p-3">
                            @if(isset($obj['_id']))
                                <button type="button" onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Customer::buildLinkDelete($obj,'customer')}}','{{$obj['_id']}}')"
                                        class="btn btn-info bg-danger-800 pull-left mr-15"><i class=" icon-database-check position-left"></i>Xóa
                                </button>

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
            <div class="col-md-6">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gImage" aria-expanded="true">Hình ảnh</a>
                            {!!  isset($image_avatar) ? ' <a style="float: right;" href="'.$image_avatar.'" data-popup="lightbox">[Xem]</a>':''!!}

                        </h3>
                    </div>
                    <div id="gImage" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="row">
                                <div style="width: 100%;max-height: 150px;border: 2px dashed #ccc;cursor: pointer;overflow: hidden" onclick="return MNG_MEDIA.openUploadForm('setImage',jQuery('#obj-image').val(),{releativeSelector:'#obj-image',absoluteSelector:'#holder_image'})">
                                    <input type="hidden" name="obj[image]" id="obj-image"
                                           value="{{isset($obj->image) && $obj->image ?$obj->image:''}}"/>
                                    <img onerror="this.src='{{url('/images/no-image.jpg')}}';this.onerror=null;" id="holder_image" src="{{ isset($image_avatar) ? $image_avatar : url('/images/no-image.jpg')}}"
                                         style="max-height:100px;max-width:100%">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gAccount" aria-expanded="true">Tài khoản</a>
                        </h3>
                    </div>
                    <div id="gAccount" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">Mật khẩu</label>
                                <div class="col-md-9">
                                    <input name="obj[password]" id="obj-password" value="" type="text" class="form-control" autocomplete="off" placeholder="Mật khẩu đăng nhập">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Nhập lại mật khẩu</label>
                                <div class="col-md-9">
                                    <input name="obj[password]" id="obj-password" value="" type="text" class="form-control" autocomplete="off" placeholder="Mật khẩu đăng nhập">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Ngày kích hoạt</label>
                                <div class="col-md-9">
                                    <input name="obj[actived_at]" id="obj-actived_at" value="@if(isset($obj['actived_at'])){{\App\Elibs\Helper::showMongoDate($obj['actived_at'],'d/m/Y')}}@endif" type="text" class="form-control datepicker" autocomplete="off" placeholder="Nhập ngày kích hoạt">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Ngày gia hạn</label>
                                <div class="col-md-9">
                                    <input name="obj[end_at]" id="obj-end_at" value="@if(isset($obj['end_at'])){{\App\Elibs\Helper::showMongoDate($obj['end_at'],'d/m/Y')}}@endif" type="text" class="form-control datepicker" autocomplete="off" placeholder="Nhập ngày kết thúc, cần gia hạn tài khoản">
                                </div>
                            </div>

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
                                                   onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Car::buildLinkDelete($val,'car')}}','{{$val['_id']}}')">
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
