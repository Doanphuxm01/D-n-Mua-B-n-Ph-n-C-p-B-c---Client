@extends($THEME_EXTEND)


@section('CONTENT_REGION')
    <div class="row">
        <div class="col-md-8 col-lg-offset-2">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gAccount" aria-expanded="true">Thông tin liên hệ</a>
                    </h3>
                </div>
                <div id="gAccount" class="panel-collapse collapse in" aria-expanded="true">
                    <div class="panel-body">
                        Mọi chi tiết liên quan đến kỹ thuật cần hỗ trợ vui lòng gọi: <a href="tel:0915155644">0915155644 </a>(Hoàng Đức Vũ)
                        <br/>
                        Hoặc Email <a href="mailto:hoangducvu@texo.com.vn">hoangducvu@texo.com.vn</a>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop