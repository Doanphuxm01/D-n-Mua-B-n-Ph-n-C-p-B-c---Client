@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
@stop

@section('BREADCRUMB_REGION')

    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Mô tả danh sách các quyền hệ thống</span></h5>
        </div>

        <div class="heading-elements">
        </div>
    </div>

    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Home</a></li>
            <li class="active">Quyền hệ thống</li>
        </ul>
        <ul class="breadcrumb-elements">
            <li>
                <a href="{{admin_link('member/input')}}">
                    <b><i class="icon-file-plus2"></i></b> Thêm thành viên mới
                </a>
            </li>

        </ul>
    </div>

@stop
@section('CONTENT_REGION')
    <div class="row">
        <div name="postInputForm">
            <div class="col-md-12">
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gRole" aria-expanded="true">Mô tả các quyền trong hệ thống</a>
                        </h3>
                    </div>
                    <div id="gRole" class="panel-collapse collapse in" aria-expanded="true">
                        <table class="panel-body">
                            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                <tbody>
                                @foreach($listRole as $key=>$value)
                                    <tr class="rule-root">
                                        <th colSpan="20" class="header"><i class="fa fa-gavel"></i> <b>
                                                {{$value->name}}
                                            </b>
                                        </th>
                                    </tr>
                                    @if($value->role)
                                        @foreach($value->role as $ks=>$role)
                                            <tr class="rule">
                                                <td width="5%">
                                                    {{$ks}}
                                                </td>
                                                <td>
                                                    <label style="display: block;margin: 0;padding: 5px" for="{{$ks}}">{{$role->name}}</label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
