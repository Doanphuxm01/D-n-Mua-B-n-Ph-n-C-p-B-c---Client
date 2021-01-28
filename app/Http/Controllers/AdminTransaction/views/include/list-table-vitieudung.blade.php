<div class="table-responsive" id="memberTbl">
    <table class="table table-striped  table-io">
        <thead>
        <tr>
            <th width="10">STT</th>
            <th>Tài khoản nhận</th>
            <th>Tài khoản nguồn</th>
            <th>MPG được nhận</th>
            <th>Cập nhật</th>
        </tr>
        </thead>
        <tbody>
        @foreach($listObj as $key=>$val)
            <tr id="itemRow_{{$val->id}}">
                <td>
                    {{$key+1}}
                </td>
                <td>
                    Tài khoản nhận: <span class="text-success text-bold">{{$val['tai_khoan_nhan']['account']}}</span>

                    @if($listObjCustomersInCustomer[$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_INACTIVE)
                        <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                    @elseif($listObjCustomersInCustomer[$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_ACTIVE)
                        <span class="text-success" title="Đã xác thực">[<i class="icon-check"></i>]</span>
                    @elseif($listObjCustomersInCustomer[$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_DISABLE)
                        <span class="text-danger" title="Đã khoá">[<i class="icon-lock2"></i>]</span>
                    @endif
                    <div>
                        Họ Tên KH: {{$val['tai_khoan_nhan']['name']}}
                    </div>
                </td>
                <td>
                    Tài khoản nguồn: <span class="text-success text-bold">{{$val['tai_khoan_nguon']['account']}}</span>

                    @if($listObjCustomersInCustomer[$val['tai_khoan_nguon']['id']]['status'] == \App\Http\Models\Orders::STATUS_INACTIVE)
                        <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                    @elseif($listObjCustomersInCustomer[$val['tai_khoan_nguon']['id']]['status'] == \App\Http\Models\Orders::STATUS_ACTIVE)
                        <span class="text-success" title="Đã xác thực">[<i class="icon-check"></i>]</span>
                    @elseif($listObjCustomersInCustomer[$val['tai_khoan_nguon']['id']]['status'] == \App\Http\Models\Orders::STATUS_DISABLE)
                        <span class="text-danger" title="Đã khoá">[<i class="icon-lock2"></i>]</span>
                    @endif
                    <div>
                        Họ Tên KH: {{$listObjCustomersInCustomer[$val['tai_khoan_nguon']['id']]['name']}}
                    </div>
                    <div>
                        @php($giaPha = \App\Http\Models\Customer::checkF($val['tai_khoan_nguon']['account'], $val['tai_khoan_nhan']['account']))
                        <b class="text-success">F{{$giaPha}}</b>
                    </div>
                </td>
                <td>
                    Số điểm: <b class="text-danger">{{\App\Elibs\Helper::formatMoney($val->diem_da_nhan)}}</b>
                    @if(isset($val['debt']))
                        <div>
                            Trạng thái nợ: {{ \App\Http\Models\Orders::getStatus($val['debt'])['text'] }}
                            @if($val['debt'] == \App\Http\Models\Orders::DEBT_YES && $val['status'] == \App\Http\Models\Orders::STATUS_NO_PROCESS)
                                <span title="Nhấn để ghi công nợ cho {{$val['tai_khoan_nhan']['account']}}" class="text-primary cursor-pointer">
                                        <i class="icon-link" aria-hidden="true"></i></span>
                            @endif
                        </div>
                    @endif
                </td>
                <td>
                    Thêm: <i>{{\App\Elibs\Helper::showMongoDate($val['created_at'], 'd-m-Y H:i:s')}}</i>
                    {{--@todo @kayn thêm trường loại giao dịch type_giaodich --}}
                    <div>
                        Loại: {{ \App\Http\Models\Transaction::$objectRegister[$val['type_giaodich']]['name'] }}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>