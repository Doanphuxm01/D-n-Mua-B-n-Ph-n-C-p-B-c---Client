<tbody>
@if(isset($listObj))
    @foreach($listObj as $obj)
        <tr>
            <td>
                @isset($obj['name']){{$obj['name']}} @endisset
            </td>
            <td>
                @isset($obj['account']){{$obj['account']}} @endisset
            </td>
            <td>
                @isset($obj['email']){{$obj['email']}} @endisset
            </td>
            <td>
                @isset($obj['phone']){{$obj['phone']}} @endisset
            </td>
            <td>
                @isset($obj['can_cuoc_cong_dan']){{$obj['can_cuoc_cong_dan']}} @endisset
            </td>
            <td>
                @isset($obj['chuc_danh']){{$obj['chuc_danh']}} @endisset
            </td>
            <td>
                @isset($obj['code']){{$obj['code']}} @endisset
            </td>
            <td>
                @isset($obj['ma_gioi_thieu']){{$obj['ma_gioi_thieu']}} @endisset
            </td>
        </tr>
    @endforeach
@endif
</tbody>

