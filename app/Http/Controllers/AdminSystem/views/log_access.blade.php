@extends($THEME_EXTEND)
@section('JS_REGION')
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/selects/select2.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_select2.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/core/libraries/jquery_ui/datepicker.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/anytime.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/picker_date.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/pages/form_checkboxes_radios.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/ui/moment/moment.min.js') !!}

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/pickers/daterangepicker.js') !!}

@stop
@section('BREADCRUMB_REGION')
    <div class="page-header-content">
        <div class="page-title">
            <h5><i class="icon-newspaper position-left"></i> <span class="text-semibold">Quản lý logs, lịch sử thao tác ứng dụng, web</span></h5>
        </div>
        <div class="heading-elements">
        </div>
    </div>
    <div class="breadcrumb-line">
        <ul class="breadcrumb">
            <li><a href="{{admin_link('')}}"><i class="icon-home2 position-left"></i> Trang chủ</a></li>
            <li class=""><a href="{{admin_link('logs')}}">Quản lý log, lịch sử thao tác</a></li>
        </ul>
    </div>
@stop
@section('CONTENT_REGION')
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Lịch sử thao tác</strong></h3>
            (Tìm thấy : {{$listObj->total()}} bản ghi)
            <div class="heading-elements">
                <form class="" method="GET">
                    <div class="form-inline">
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
                                        <input type="text" name="q_time" class="form-control daterange-basic-customer" value="{{request('q_time')}}" placeholder="Thời gian">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_action" class="form-control">
                                    <option value="0">Tất cả hành động</option>
                                    <option @if(isset($q_action) && $q_action=='created') selected @endif value="created">Thêm dữ liệu</option>
                                    <option @if(isset($q_action) && $q_action=='updated') selected @endif value="updated">Sửa thông tin</option>
                                    <option @if(isset($q_action) && $q_action=='deleted') selected @endif value="deleted">Xóa</option>
                                    <option @if(isset($q_action) && $q_action=='login') selected @endif value="login">Login (đăng nhập)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group no-margin">
                            <div class="content-group">
                                <select name="q_object" class="form-control">
                                    <?php

                                    ?>
                                    <option value="0">Tất cả đối tượng</option>
                                        @foreach($lsObject as $key=>$val)
                                            <option @if(isset($q_object) && $q_object==$key) selected @endif value="{{$key}}">{{$val}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="input-group content-group">
                            <div class="has-feedback has-feedback-left">
                                <input name="q" value="{{app('request')->input('q')}}" type="text"
                                       class="form-control input-sm" placeholder="Tìm kiếm từ khóa">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-muted text-size-base"></i>
                                </div>
                            </div>

                            <div class="input-group-btn mr-3">
                                <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Lọc</button>
                            </div>

                            <div class="input-group-btn ">
                                <button type="submit" class="btn btn-default btn-sm ml-3" value="1" name="excel">Xuất excel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive" id="memberTbl">
            <table class="table table-striped  table-io">
                <thead>
                <tr>
                    <th width="10">STT</th>
                    <th>Note</th>
                    <th>Ip/Device</th>
                    <th>Object</th>
                    <th width="30" class="text-center"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$val)
                    <tr id="itemRow_{{$val->id}}">
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            @if(isset($val->created_by) && isset($val->created_by['account']))
                                {{$val->created_by['account']}}
                            @elseif(isset($val->created_by['phone']) && $val->created_by['phone'])
                                {{$val->created_by['phone']}}
                            @elseif(isset($val->created_by['email']) && $val->created_by['email'])
                                {{$val->created_by['email']}}
                            @elseif(isset($val->created_by['name']) && $val->created_by['name'])
                                {{$val->created_by['name']}}
                            @endif
                            <div class="">
                                {!! $val->note !!}
                            </div>

                        </td>
                        <td>
                            @if(isset($val->client_info['agent']) && $val->client_info['agent'])
                                <div style="white-space: nowrap;;max-width: 500px;overflow: hidden;text-overflow: ellipsis">
                                    Agent : <i>{{$val->client_info['agent']}}</i>
                                </div>
                            @endif
                            {{--@if(isset($val->client_info['referer']) && $val->client_info['referer'])
                                <div>
                                    Referer : <i>{{$val->client_info['referer']}}</i>
                                </div>
                            @endif--}}
                            @if(isset($val->client_info['ip']) && $val->client_info['ip'])
                                <div>
                                    <span class="text-warning-800"> Lúc: <i>{{\App\Elibs\Helper::showMongoDate($val['created_at'])}}</i></span> , IP : <i>{{$val->client_info['ip']}}</i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="margin-bottom: 5px;display: block"
                                 class="label @if($val->type=='created')
                                         label-success @elseif($val->type=='deleted') label-danger @elseif($val->type=='updated') label-warning @else label-info @endif ">{{$val->type}}
                            </div>
                            @if(isset($lsObject[$val->collection_name]))
                               {{$lsObject[$val->collection_name]}}
                            @endif

                        </td>
                        <td class="text-center">

                            <ul class="icons-list">
                                <li class="text-primary-600">
                                    <button class="btn btn-default btn-sm btn-xs"
                                            onclick="return _SHOW_FORM_REMOTE('{{admin_link('logs?id='.$val['_id'].'&token='.\App\Elibs\Helper::buildTokenString($val['_id']))}}')"
                                            title="Xem"
                                    > Xem
                                    </button>
                                </li>
                                {{-- <li class="text-danger-600">
                                     <a href="javascript:void(0);" data-popup="tooltip" title="Xóa"
                                        onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Logs::buildLinkDelete($val,'logs')}}','{{$val['_id']}}')">
                                         <i class="icon-trash"></i>
                                     </a>
                                 </li>--}}
                            </ul>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="panel-body">
            @if(!$listObj->count())
                <div class="alert alert-danger alert-styled-left alert-bordered">
                     Không tìm thấy dữ liệu nào ở trang này. (Hãy kiểm tra lại các điều kiện tìm kiếm hoặc
                    phân trang...)
                </div>
            @endif
            <div class="text-center pagination-rounded-all">{{ $listObj->render() }}</div>
        </div>
    </div>
    <script type="text/javascript">
    </script>

    <script type="text/javascript">

        function _DATERANGE_BASIC() {
            try {

                $('.daterange-basic-customer').daterangepicker({
                    applyClass: 'bg-slate-600',
                    cancelClass: 'btn-default',
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    autoEnd: true
                });
                $('.daterange-basic-customer').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                });

                $('.daterange-basic-customer').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            } catch (e) {
                console.log(e);
            }
        }
        _DATERANGE_BASIC();
    </script>
@stop
