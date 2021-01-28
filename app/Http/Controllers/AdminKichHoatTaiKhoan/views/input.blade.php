@extends('backend')

@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/wizards/stepy.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/validation/validate.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/wizard_stepy.js') !!}


@stop
@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Kích hoạt thành viên </span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li><a href="{{admin_link('kich-hoat-thanh-vien')}}">Kích hoạt thành viên</a></li>
        </ul>
    </div>

@stop

@section('CONTENT_REGION')
    <form class="form-horizontal" action="#">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-flat">
                    <div class="panel-heading">
                        <h5 class="panel-title">Kích hoạt thành viên</h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                                <li><a data-action="reload"></a></li>
                                <li><a data-action="close"></a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div class="form-group">
                            <label class="col-lg-3 control-label">Mã tài khoản kích hoạt</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="ma_tai_khoan_nhan_kich_hoat" placeholder="Nhập mã tài khoản của người cần kích hoạt">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Thông tin tài khoản</label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" placeholder="Your strong password">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Select your state:</label>
                            <div class="col-lg-9">
                                <select data-placeholder="Select your state" class="select">
                                    <option></option>
                                    <optgroup label="Alaskan/Hawaiian Time Zone">
                                        <option value="AK">Alaska</option>
                                        <option value="HI">Hawaii</option>
                                    </optgroup>
                                    <optgroup label="Pacific Time Zone">
                                        <option value="CA">California</option>
                                        <option value="NV">Nevada</option>
                                        <option value="WA">Washington</option>
                                    </optgroup>
                                    <optgroup label="Mountain Time Zone">
                                        <option value="AZ">Arizona</option>
                                        <option value="CO">Colorado</option>
                                        <option value="ID">Idaho</option>
                                    </optgroup>
                                    <optgroup label="Central Time Zone">
                                        <option value="AL">Alabama</option>
                                        <option value="IA">Iowa</option>
                                        <option value="KS">Kansas</option>
                                    </optgroup>
                                    <optgroup label="Eastern Time Zone">
                                        <option value="CT">Connecticut</option>
                                        <option value="DE">Delaware</option>
                                        <option value="WV">West Virginia</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Attach screenshot:</label>
                            <div class="col-lg-9">
                                <input type="file" class="file-styled">
                                <span class="help-block">Accepted formats: gif, png, jpg. Max file size 2Mb</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-3 control-label">Your message:</label>
                            <div class="col-lg-9">
                                <textarea rows="5" cols="5" class="form-control" placeholder="Enter your message here"></textarea>
                            </div>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">Submit form <i class="icon-arrow-right14 position-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop
