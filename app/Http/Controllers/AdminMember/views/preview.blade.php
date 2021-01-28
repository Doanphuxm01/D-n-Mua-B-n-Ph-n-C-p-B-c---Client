@php
    $tab = request('tab');
    $tab= $tab ? $tab : 'tab-info';
@endphp
<div class="modal-dialog modal-large modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">
                Thông tin chi tiết
            </h3>
        </div>
        <div class="modal-body no-padding">

            <ul class="nav nav-tabs mt-2">
                <li class="ml-3 @if($tab==='tab-info') active @endif"><a data-toggle="tab" href="#tab-info">
                        Thông tin cơ bản</a>
                </li>
                <li class="@if($tab==='tab-work') active @endif"><a data-toggle="tab" href="#tab-work">
                        Thông tin công việc</a>
                </li>
                <li class="@if($tab==='tab-edu') active @endif"><a data-toggle="tab" href="#tab-edu">
                        Thông tin đào tạo</a>
                <li class="@if($tab==='tab-family') active @endif "><a data-toggle="tab" href="#tab-family">
                        Thông tin gia đình</a>


            </ul>
            <div class="tab-content">
                <div class="tab-pane @if($tab==='tab-info') active @endif" id="tab-info">
                    <table class="table-1-row table table-hover table-striped table-bordered table-advanced c2-table"
                           style="border-bottom: 0;border-top: 0">
                        <thead>
                        <tr>
                            <th style="border: none" width="150">Thông tin</th>
                            <th style="border: none" width="300">Chi tiết</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{--@foreach($obj->toArray() as $key=>$value)--}}
                        {{--<tr>--}}
                        {{--<td>{{$key}}--}}
                        {{--</td>--}}
                        {{--<td>{{json_encode($value)}}--}}
                        {{--</td>--}}
                        {{--</tr>--}}

                        {{--@endforeach--}}
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('code')}}</td>
                            <td>
                                @isset($obj['code']){{$obj['code']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('name')}}</td>
                            <td>
                                @isset($obj['name']){{$obj['name']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('account')}}</td>
                            <td>
                                @isset($obj['account']){{$obj['account']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('gender')}}</td>
                            <td>
                                @isset($obj['gender'])
                                    @if($obj['gender'] === 'female')
                                        Nữ
                                    @elseif($obj['gender'] === 'male')
                                        Nam
                                    @else
                                        <i class="text-grey">Chưa có dữ liệu</i>
                                    @endif
                                @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('date_of_birth')}}</td>
                            <td>
                                @isset($obj['date_of_birth']){{\App\Elibs\Helper::showMongoDate($obj['date_of_birth'])}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('noi_sinh')}}</td>
                            <td>
                                @isset($obj['noi_sinh']['name']){{$obj['noi_sinh']['name']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('nguyen_quan')}}</td>
                            <td>
                                @isset($obj['nguyen_quan']['key']){{\App\Http\Models\Location::getBySlug($obj['nguyen_quan']['key'])['name_with_type']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('dan_toc')}}</td>
                            <td>
                                @isset($obj['dan_toc']['name']){{$obj['dan_toc']['name']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('ton_giao')}}</td>
                            <td>
                                @isset($obj['ton_giao']['name']){{$obj['ton_giao']['name']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('quoc_tich')}}</td>
                            <td>
                                @isset($obj['quoc_tich']['name']){{$obj['quoc_tich']['name']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('so_bhxh')}}</td>
                            <td>
                                @isset($obj['so_bhxh']){{$obj['so_bhxh']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('ma_so_thue')}}</td>
                            <td>
                                @isset($obj['ma_so_thue']){{$obj['ma_so_thue']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('tinh_trang_hon_nhan')}}</td>
                            <td>
                                @isset($obj['tinh_trang_hon_nhan']){{$obj['tinh_trang_hon_nhan']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('tien_an_tien_su')}}</td>
                            <td>
                                @isset($obj['tien_an_tien_su']){{$obj['tien_an_tien_su']}} @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('ngoai_ngu')}}</td>
                            <td>
                                @isset($obj['ngoai_ngu'])
                                    {{collect($obj['ngoai_ngu'])->map(function ($item){return $item['name'];})->implode(',')}}
                                @endisset
                            </td>
                        </tr>

                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('ho_khau_thuong_chu')}}</td>
                            <td>
                                @isset($obj['ho_khau_thuong_chu'])
                                    @if(isset($obj['ho_khau_thuong_chu']['chi_tiet'])){{$obj['ho_khau_thuong_chu']['chi_tiet']}}
                                    , @endif
                                    @if(isset($obj['ho_khau_thuong_chu']['huyen']['name_with_type'])){{$obj['ho_khau_thuong_chu']['huyen']['name_with_type']}}
                                    , @endif
                                    @if(isset($obj['ho_khau_thuong_chu']['tinh']['name_with_type'])){{$obj['ho_khau_thuong_chu']['tinh']['name_with_type']}}@endif
                                @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('ho_khau_thuong_chu')}}</td>
                            <td>
                                @isset($obj['noi_o_hien_nay'])
                                    @if(isset($obj['noi_o_hien_nay']['chi_tiet'])){{$obj['noi_o_hien_nay']['chi_tiet']}}
                                    , @endif
                                    @if(isset($obj['noi_o_hien_nay']['huyen']['name_with_type'])){{$obj['noi_o_hien_nay']['huyen']['name_with_type']}}
                                    , @endif
                                    @if(isset($obj['noi_o_hien_nay']['tinh']['name_with_type'])){{$obj['noi_o_hien_nay']['tinh']['name_with_type']}}@endif
                                @endisset
                            </td>
                        </tr>

                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('emails')}}</td>
                            <td>
                                @isset($obj['emails'])
                                    @foreach($obj['emails'] as $item)
                                        @if(isset($item['value'])) {{$item['value']}} @endif
                                    @endforeach
                                @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('phones')}}</td>
                            <td>
                                @isset($obj['phones'])
                                    @foreach($obj['phones'] as $item)
                                        @if(isset($item['value'])) {{$item['value']}} @endif
                                    @endforeach
                                @endisset
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('giay_to')}}</td>
                            <td>
                                @if(isset($obj['giay_to']) && is_array($obj['giay_to']))
                                    @foreach($obj['giay_to'] as $item)
                                        @if(isset($item['name']) && isset($item['so']))
                                            <div>{{$item['name']}} - {{$item['so']}}</div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('tk_ngan_hang')}}</td>
                            <td>
                                @if(isset($obj['tk_ngan_hang']) && is_array($obj['tk_ngan_hang']))
                                    @foreach($obj['tk_ngan_hang'] as $item)
                                        @if(isset($item['name']) && isset($item['so']))
                                            <div>{{$item['name']}} - {{$item['so']}}</div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('to_chuc_doan_the')}}</td>
                            <td>
                                @if(isset($obj['to_chuc_doan_the']) && is_array($obj['to_chuc_doan_the']))
                                    @foreach($obj['to_chuc_doan_the'] as $item)
                                        @if(isset($item['name']) && isset($item['chuc_vu']['name']))
                                            <div>{{$item['name']}}
                                                - {{$item['chuc_vu']['name']}} @if(isset($item['ngay_gia_nhap']))
                                                    ({{\App\Elibs\Helper::showMongoDate($item['ngay_gia_nhap'])}}
                                                    )@endif</div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('lien_he_khac')}}</td>
                            <td>
                                @if(isset($obj['lien_he_khac']) && is_array($obj['lien_he_khac']))
                                    @foreach($obj['lien_he_khac'] as $item)
                                        @if(isset($item['name']) && isset($item['thong_tin']))
                                            <div>{{$item['name']}} - {{$item['thong_tin']}}</div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('files_thong_tin_co_ban')}}</td>
                            <td>
                                @if(isset($obj['files_thong_tin_co_ban']) && is_array($obj['files_thong_tin_co_ban']))
                                    @foreach($obj['files_thong_tin_co_ban'] as $item)
                                        @if(isset($item['name']) && isset($item['path']))
                                            <div>{{$item['name']}} - <a target="_blank"
                                                                        href="{{\App\Http\Models\Media::getFileLink($item['path'])}}">Xem</a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>

                        {{--<tr>--}}
                        {{--<td>{{\App\Http\Models\Member::getFieldName('code')}}</td>--}}
                        {{--<td>--}}
                        {{--@isset($obj['code']){{$obj['code']}} @endisset--}}
                        {{--</td>--}}
                        {{--</tr>--}}

                        </tbody>
                    </table>
                </div>
                <div class="tab-pane @if($tab==='tab-family') active @endif" id="tab-family">
                    <table class="table-1-row table table-hover table-striped table-bordered table-advanced c2-table"
                           style="border-bottom: 0;border-top: 0">
                        <thead>
                        <tr>
                            <th style="border: none" width="">Tên/Mối quan hệ</th>
                            <th style="border: none" width="">Ngày sinh/Tình trạng</th>
                            <th style="border: none" width="">Nghề nghiệp/Nơi ở</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($obj['thong_tin_gia_dinh'])
                            @foreach($obj['thong_tin_gia_dinh'] as $key=>$value)
                                <tr>
                                    <td>
                                        @if(isset($value['ho_ten'])) {{$value['ho_ten']}} <br>@endif
                                        @if(isset($value['moi_quan_he_gia_dinh'])) {{$value['moi_quan_he_gia_dinh']}} @endif
                                    </td>
                                    <td>
                                        @if(isset($value['ngay_sinh'])) {{\App\Elibs\Helper::showMongoDate($value['ngay_sinh'])}}
                                        <br>@endif
                                        @if(isset($value['tinh_trang'])) {{$value['tinh_trang']}} @endif
                                    </td>
                                    <td>
                                        @if(isset($value['nghe_nghiep']['name'])) {{$value['nghe_nghiep']['name']}}
                                        <br>@endif
                                        @if(isset($value['noi_o_hien_nay'])) {{$value['noi_o_hien_nay']}} @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                        <thead>
                        <tr>
                            <th style="border: none">Thông tin khác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('files_thong_tin_gia_dinh')}}</td>
                            <td colspan="2">
                                @if(isset($obj['files_thong_tin_gia_dinh']) && is_array($obj['files_thong_tin_gia_dinh']))
                                    @foreach($obj['files_thong_tin_gia_dinh'] as $item)
                                        @if(isset($item['name']) && isset($item['path']))
                                            <div>{{$item['name']}} - <a target="_blank"
                                                                        href="{{\App\Http\Models\Media::getFileLink($item['path'])}}">Xem</a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane @if($tab==='tab-edu') active @endif" id="tab-edu">
                    <table class="table-1-row table table-hover table-striped table-bordered table-advanced c2-table"
                           style="border-bottom: 0;border-top: 0">
                        <thead>
                        <tr>
                            <th style="border: none" width="">Loại bằng cấp</th>
                            <th style="border: none" width="">Chuyên môn</th>
                            <th style="border: none" width="">Chuyên ngành</th>
                            <th style="border: none" width="">Nơi cấp</th>
                            <th style="border: none" width="">Ngày cấp</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($obj['bang_cap'])

                            @foreach($obj['bang_cap'] as $key=>$value)
                                <tr>
                                    <td>@if(isset($value["loai_bang_cap"]["name"])) {{$value["loai_bang_cap"]["name"]}}@endif</td>
                                    <td>@if(isset($value["chuyen_mon"]["name"])) {{$value["chuyen_mon"]["name"]}}@endif</td>
                                    <td>@if(isset($value["chuyen_nganh"]["name"])) {{$value["chuyen_nganh"]["name"]}}@endif</td>
                                    <td>@if(isset($value["noi_cap"]["name"])) {{$value["noi_cap"]["name"]}}@endif</td>
                                    <td>@if(isset($value["ngay_cap"])) {{\App\Elibs\Helper::showMongoDate($value["ngay_cap"])}}@endif</td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                        <thead>
                        </thead>
                        <tbody>
                        <thead>
                        <tr>
                            <th colspan="3" style="border: none" width="">Loại chứng chỉ</th>
                            <th style="border: none" width="">Nơi cấp</th>
                            <th style="border: none" width="">Ngày cấp</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($obj['chung_chi_dao_tao'])

                            @foreach($obj['chung_chi_dao_tao'] as $key=>$value)
                                <tr>
                                    <td colspan="3">@if(isset($value["loai_chung_chi"]["name"])) {{$value["loai_chung_chi"]["name"]}}@endif</td>
                                    <td>@if(isset($value["noi_cap"]["name"])) {{$value["noi_cap"]["name"]}}@endif</td>
                                    <td>@if(isset($value["ngay_cap"])) {{\App\Elibs\Helper::showMongoDate($value["ngay_cap"])}}@endif</td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                        <thead>
                        <tr>
                            <th style="border: none">Thông tin khác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('files_thong_tin_dao_tao')}}</td>
                            <td colspan="4">
                                @if(isset($obj['files_thong_tin_dao_tao']) && is_array($obj['files_thong_tin_dao_tao']))
                                    @foreach($obj['files_thong_tin_dao_tao'] as $item)
                                        @if(isset($item['name']) && isset($item['path']))
                                            <div>{{$item['name']}} - <a target="_blank"
                                                                        href="{{\App\Http\Models\Media::getFileLink($item['path'])}}">Xem</a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
                <div class="tab-pane @if($tab==='tab-work') active @endif" id="tab-work">
                    <table class="table-1-row table table-hover table-striped table-bordered table-advanced c2-table"
                           style="border-bottom: 0;border-top: 0">
                        <thead>
                        <tr>
                            <th style="border: none" width="">Tình trạng hợp đồng</th>
                            <th style="border: none" colspan="2" width="">Loại hợp đồng</th>
                            <th style="border: none" width="">Ngày bắt đầu</th>
                            <th style="border: none" width="">Ngày kết thúc</th>
                        </tr>
                        </thead>
                        <tbody>
                        @isset($obj['thong_tin_hop_dong_lao_dong'])
                            @foreach($obj['thong_tin_hop_dong_lao_dong'] as $key=>$value)
                                <tr>
                                    <td>@if(isset($value["tinh_trang"])) {{$value["tinh_trang"]}}@endif</td>
                                    <td colspan="2">@if(isset($value["loai_hop_dong"])) {{$value["loai_hop_dong"]}}@endif</td>
                                    <td>@if(isset($value["ngay_bat_dau"])) {{\App\Elibs\Helper::showMongoDate($value["ngay_bat_dau"])}}@endif</td>
                                    <td>@if(isset($value["ngay_ket_thuc"])) {{\App\Elibs\Helper::showMongoDate($value["ngay_ket_thuc"])}}@endif</td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                        <thead>
                        <tr>
                            <th colspan="" style="border: none" width="">Vị trí</th>
                            <th style="border: none" width="">Phòng ban</th>
                            <th style="border: none" width="">Dự án</th>
                            <th style="border: none" width="">Ngày bắt đầu</th>
                            <th style="border: none" width="">Ngày kết thúc</th>

                        </tr>
                        </thead>
                        <tbody>
                        @isset($obj['qua_trinh_cong_tac'])
                            @foreach($obj['qua_trinh_cong_tac'] as $key=>$value)
                                <tr>
                                    <td colspan="">@if(isset($value["position"]["name"])) {{$value["position"]["name"]}}@endif</td>
                                    <td colspan="">@if(isset($value["department"]["name"])) {{$value["department"]["name"]}}@endif</td>
                                    <td colspan="">@if(isset($value["project"]["name"])) {{$value["project"]["name"]}}@endif</td>
                                    <td>@if(isset($value["ngay_bat_dau"])) {{\App\Elibs\Helper::showMongoDate($value["ngay_bat_dau"])}}@endif</td>
                                    <td>@if(isset($value["ngay_ket_thuc"])) {{\App\Elibs\Helper::showMongoDate($value["ngay_ket_thuc"])}}@endif</td>
                                </tr>
                            @endforeach
                        @endisset
                        </tbody>
                        <thead>
                        <tr>
                            <th style="border: none">Thông tin khác</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{\App\Http\Models\Member::getFieldName('files_thong_tin_cong_viec')}}</td>
                            <td colspan="4">
                                @if(isset($obj['files_thong_tin_cong_viec']) && is_array($obj['files_thong_tin_cong_viec']))
                                    @foreach($obj['files_thong_tin_cong_viec'] as $item)
                                        @if(isset($item['name']) && isset($item['path']))
                                            <div>{{$item['name']}} - <a target="_blank"
                                                                        href="{{\App\Http\Models\Media::getFileLink($item['path'])}}">Xem</a>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
        <div class="modal-footer">
            <a target="_blank" href="{{admin_link('staff/input?id='.@$obj['_id'].'&tab=info')}}">
                <button class=" btn btn-primary">Chỉnh sửa</button>
            </a>
            <button class="btn btn-danger" data-dismiss="modal">Đóng lại</button>
        </div>
    </div>

</div>
