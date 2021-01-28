@extends('backend_gate')

@section('CONTENT_REGION')
    <div class="login-container">
        <form method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

        @if(isset($_MSG) && $_MSG)
                {!! $_MSG !!}
            @endif
            <div class="panel panel-body login-form">
                <div class="text-center">
                    <div class="icon-object text-slate-300" style="border-width: inherit;">
                        <a href="{{ public_link('/') }}">
                            <img src="{{asset('/images/logo.png')}}" style="height: auto;position: relative;width: 170px;left: 9px;">
                        </a>
                    </div>
                    <h5 class="content-group">Đăng nhập bằng tài khoản của bạn
                        <small class="display-block">Nhập tài khoản và mật khẩu vào ô phía dưới</small>
                    </h5>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[account]" value="{{isset($obj['account'])?$obj['account']:null}}" type="text" class="form-control" placeholder="Tài khoản hoặc Email hoặc Phone">
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <input name="obj[password]" type="password" value="" class="form-control" placeholder="Mật khẩu">
                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Đăng nhập <i class="icon-circle-right2 position-right"></i></button>
                </div>

                <div class="text-center">
                    <a href="{{ public_link('auth/register') }}">Đăng ký</a>
                </div>
            </div>
        </form>
        <!-- /simple login form -->
    </div>

    {{--<!-- Footer -->
    <div class="footer text-muted">
        &copy; {{date('Y')}}. <a href="/">Ứng dụng quản lý hệ thống</a> by <a href="{{ admin_link('/') }}">{{ config('app.cms_name') }}</a>
    </div>
    <!-- /footer -->--}}

@stop