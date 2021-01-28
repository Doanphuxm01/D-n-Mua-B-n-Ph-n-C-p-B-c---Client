@extends($THEME_EXTEND)


@section('CONTENT_REGION')
    <div class="row">
        <div class="row col-md-6 col-lg-offset-3 col-sm-12">
            <div class="panel panel-white">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gAccount" aria-expanded="true">Chọn dự án để làm việc</a>
                    </h3>
                    <small>Bạn có thể chọn 1 dự án để làm việc</small>
                </div>
                <div id="gAccount" class="panel-collapse collapse in" aria-expanded="true">
                    <div class="panel-body">
                        @if(!isset($allProjectByMe) || !$allProjectByMe)
                            <h3 class="text-center mt-0">Không tìm thấy dự án nào!</h3>
                            <div class="text-center">
                                <small>Có thể bạn chưa được phân quyền vào bất kỳ dự án nào. Hãy liên hệ với quản trị viên để được hỗ trợ!</small>
                            </div>

                        @else
                            <ul class="app-list app-list-fanpage">
                                @foreach($allProjectByMe as $key=>$value)
                                    <li class="list-media-small">
                                        <div class="listImg"><i class="fa fa-university text-warning-600"> </i></div>
                                        <div class="desc">
                                            <div class="title"><a onclick="return MNG_POST.update('{{admin_link('project/show_switch?popup=static&id='.$value['_id'].'&token='.\App\Elibs\Helper::buildTokenString($value['_id']).'')}}')" title="Quản lý dự án" href="javascript:void(0)">
                                                    {{$value['name']}}
                                                </a></div>
                                            <small>@if($value['brief']) {{$value['brief']}} @else Cập nhật lúc: {{\App\Elibs\Helper::showMongoDate($value['updated_at'],'H:i:s d/m/Y')}}@endif</small>
                                        </div>
                                        <div class="actions">
                                            <a onclick="return MNG_POST.update('{{admin_link('project/show_switch?popup=static&id='.$value['_id'].'&token='.\App\Elibs\Helper::buildTokenString($value['_id']).'')}}')" class="btn btn-outline-success btn-xs" href="javascript:void(0)"><i class="fa fa-location-arrow"></i> Truy cập</a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    @if(isset($allProjectByMe) )
                        <div class="panel-footer">
                            @if($allProjectByMe)
                                @if(!\App\Http\Models\Project::$curentProject)
                                    <span><i class="fa fa-info-circle"></i> Bạn chưa chọn dự án làm việc. Hãy chọn 1 trong các dự án ở danh sách bên trên</span>
                                @else
                                    <i class="fa fa-info-circle"></i> Bạn đang làm việc với dự án: {{\App\Http\Models\Project::$curentProject['name']}}
                                @endif
                            @endif
                            @if(\App\Http\Models\Member::haveRole(\App\Http\Models\Member::mng_project))
                                <button onclick="return  _SHOW_FORM_REMOTE('{{admin_link('project/input')}}');" class="btn btn-outline-danger btn-sm btn-xs" style="float: right;">Tạo dự án mới</button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop