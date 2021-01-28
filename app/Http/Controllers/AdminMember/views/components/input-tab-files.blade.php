@if(!isset($allCity))
    @php
        $allCity = collect(\App\Http\Models\Location::getAllCity());
    @endphp
@endif
@php($allStaffDataList = \App\Http\Models\MetaData::getAllByObject('staff'))

<style>
    .d-flex {
        display: flex;
    }

    .justify-content-center {
        align-items: center;
    }
</style>
<script>
    $.datepicker.setDefaults({
        dateFormat: 'dd/mm/yy'
    });
</script>
<div class="col-md-12">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h3 class="panel-title"><strong>Các file đã được tải lên bởi {{@$obj['name']}}</strong></h3>
            (Tìm thấy : {{$listObj->total()}} tài khoản)
            <div class="heading-elements">
                <form class="" method="GET">
                    {{--<div class="form-inline dep-container">--}}
                    {{--<div class="form-group no-margin">--}}
                    {{--<div class="content-group">--}}
                    {{--<select name="q_status" class="form-control">--}}
                    {{--<option value="0">Tất cả trạng thái</option>--}}
                    {{--@foreach(App\Http\Models\BaseModel::getListStatus($q_status) as $status)--}}
                    {{--<option @if(isset($status['checked'])) selected="selected"--}}
                    {{--@endif value="{{ $status['id'] }}">{{ $status['text'] }}</option>--}}
                    {{--@endforeach--}}
                    {{--</select>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group no-margin">--}}
                    {{--<div class="content-group">--}}
                    {{--<select name="q_dep" onchange="_getPosByDep(this)" class="form-control">--}}
                    {{--<option value="0">Tất cả phòng ban</option>--}}
                    {{--@foreach($allDepartment as $key=>$item)--}}
                    {{--<option @if($item['_id']==request('q_dep')) selected="selected"--}}
                    {{--@endif value="{{ $item['_id'] }}">{{ $item['name'] }}</option>--}}
                    {{--@endforeach--}}
                    {{--</select>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="form-group no-margin">--}}
                    {{--<div class="content-group">--}}
                    {{--<select name="q_pos" class="form-control js-pos">--}}
                    {{--<option value="0">Tất cả chức vụ</option>--}}
                    {{--@foreach($allPosition as $key=>$item)--}}
                    {{--@if(isset($item['department']['id']) && $item['department']['id']==request('q_dep'))--}}
                    {{--<option @if($item['_id']==request('q_pos')) selected="selected"--}}
                    {{--@endif value="{{ $item['_id'] }}">{{ $item['name'] }}</option>--}}
                    {{--@endif--}}
                    {{--@endforeach--}}
                    {{--</select>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="input-group content-group">--}}
                    {{--<select class="form-control" name="q-job-status" id="">--}}
                    {{--<option @if(request('q-job-status')=='active') selected @endif value="active">--}}
                    {{--Đang công tác--}}
                    {{--</option>--}}
                    {{--<option @if(request('q-job-status')=='deactive' ) selected--}}
                    {{--@endif value="deactive">Đã nghỉ việc--}}
                    {{--</option>--}}
                    {{--<option @if(request('q-job-status')=='temp-deactive' )selected--}}
                    {{--@endif value="temp-deactive">--}}
                    {{--Tạm nghỉ--}}
                    {{--</option>--}}
                    {{--</select>--}}
                    {{--</div>--}}
                    <div class="input-group content-group" style="width: 360px">

                        <div class="has-feedback has-feedback-left">
                            <input name="q" value="{{app('request')->input('q')}}" type="text"
                                   class="form-control input-sm" placeholder="Tìm kiếm từ khóa">
                            <div class="form-control-feedback">
                                <i class="icon-search4 text-muted text-size-base"></i>
                            </div>
                        </div>

                        <div class="input-group-btn">
                            <button type="submit" class="btn btn-primary bg-teal-800 btn-sm">Tìm kiếm
                            </button>

                            <button type="button" class="btn btn-danger btn-sm"><a class="text-white"
                                                                                   href="{{admin_link('/staff/input?id='.request('id').'&tab='.request('tab'))}}">Reset</a>
                            </button>
                        </div>

                        <input type="hidden" name="tab" value="{{request('tab')}}">
                        <input type="hidden" name="id" value="{{request('id')}}">
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive" id="memberTbl" style="overflow-x: visible">
            <table class="table table-striped table-bordered table-io">
                <thead>
                <tr style="border-top: 1px solid #ccc">
                    <th>Stt</th>
                    <th>
                        Tên file
                    </th>
                    <th>Kiểu dữ liệu</th>
                    <th>Đường dẫn</th>
                    <th>Ngày tạo</th>
                    <th width="68"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listObj as $key=>$item)

                    <tr>
                        <td>{{$key + 1}}</td>
                        <td>{{@$item['name']}}</td>
                        <td>{{@$item['type']}}</td>
                        <td>{{admin_link($item['src'])}}</td>
                        <td>{{\App\Elibs\Helper::showDate(@$item['created_at'])}}</td>
                        <td class="text-right">
                            <a href="{{admin_link($item['src'])}}" target="_blank">
                                <i class="icon-download"></i>
                            </a>
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
</div>
