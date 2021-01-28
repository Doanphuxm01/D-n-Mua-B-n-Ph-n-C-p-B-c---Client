<div class="table-responsive" id="memberTbl">
    <table class="table table-striped  table-io">
        <thead>
        <tr>
            <th width="10">STT</th>
            <th>Tài khoản mua hàng</th>
            <th>Điểm cần mua</th>
            <th>Phone/Email</th>
            <th>Cập nhật</th>
            <th>Kích hoạt</th>
            <th width="150" class="text-right">Chức năng</th>
        </tr>
        </thead>
        <tbody>
        @foreach($listObj as $key=>$val)
            <tr id="itemRow_{{$val->id}}">
                <td>
                    {{$key+1}}
                </td>
                <td>
                    Tài khoản nhận: <span class="text-success text-bold">{{@$val['tai_khoan_nhan']['account']}}</span>

                    @if(@$listObjCustomersInCustomer[@$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_INACTIVE)
                        <a class="text-danger" href="{{ admin_link('customer/input?id='.@$val['tai_khoan_nhan']['id']) }}" title="Chưa xác thực, click để xác thực tài khoản">[<i class="icon-check"></i>]</a>
                    @elseif(@$listObjCustomersInCustomer[@$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_ACTIVE)
                        <span class="text-success" title="Đã xác thực">[<i class="icon-check"></i>]</span>
                    @elseif(@$listObjCustomersInCustomer[@$val['tai_khoan_nhan']['account']]['status'] == \App\Http\Models\Orders::STATUS_DISABLE)
                        <span class="text-danger" title="Đã khoá">[<i class="icon-lock2"></i>]</span>
                    @endif
                    <div>
                        Họ Tên KH: {{@$val['tai_khoan_nhan']['name']}}
                    </div>
                </td>
                <td>
                    Số điểm cần mua: <b class="text-danger">{{\App\Elibs\Helper::formatMoney($val->so_diem_can_mua)}}</b>
                    @if(isset($val['debt']))
                        <div>
                            Trạng thái nợ: {{ \App\Http\Models\Orders::getStatus(@$val['debt'])['text'] }}
                            @if(@$val['debt'] == \App\Http\Models\Orders::DEBT_YES && @$val['status'] == \App\Http\Models\Orders::STATUS_NO_PROCESS)
                                <div>
                                    Số điểm nợ: <b class="text-danger editable-text cursor-pointer" title="Click để ghi công nợ" data-type="text"
                                                   data-url="{{ admin_link('orders-mpg/_update_cong_no?token='.\App\Elibs\Helper::buildTokenString(@$val['_id'])) }}" data-pk="{{ @$val['_id'] }}"
                                                   data-title="{{\App\Elibs\Helper::formatMoney($val->cong_no)}}"></b>
                                </div>
                                <div>
                                    Số điểm được nhận: <b class="text-danger">{{\App\Elibs\Helper::formatMoney($val->so_diem_duoc_nhan)}}</b>
                                </div>
                            @endif
                        </div>
                    @endif
                </td>
                <td>
                    Phone: <b>{{@$val['tai_khoan_nhan']['phone']??'Chưa cập nhật'}}</b>
                    @if(isset($val->verified['phone']) && $val->verified['phone']=='true')
                        <span class="text-success"><i class="icon-check"></i></span>
                    @else
                        <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                    @endif
                    <div>Email: @if(@$val['tai_khoan_nhan']['email'])<i>{{@$val['tai_khoan_nhan']['email']??'Chưa cập nhật'}}</i>
                        @if(isset($val->verified['email']) && $val->verified['email']=='true')
                            <span class="text-success"><i class="icon-check"></i></span>
                        @else
                            <span class="text-danger" title="Chưa xác thực">[<i class="icon-check"></i>]</span>
                        @endif
                        @else <span class="text-danger-800">Chưa có email</span> @endif</div>
                </td>
                <td>
                    Thêm: <i>{{\App\Elibs\Helper::showMongoDate(@$val['created_at'])}}</i>
                    <div>
                        Cập nhật: <i>{{\App\Elibs\Helper::showMongoDate(@$val['updated_at'])}}</i>
                    </div>
                </td>
                <td>
                    @if(@$val['status'] != \App\Http\Models\Orders::STATUS_DELETED)
                        Kích hoạt: <i>{{\App\Elibs\Helper::showMongoDate(@$val['actived_at'])??'Chưa cập nhật'}}</i>
                        <div>
                            Người cập nhật: <i>{{@$val['updated_by']['name']??'Chưa cập nhật'}}</i>
                        </div>
                    @else
                        Đã xóa: <i>{{\App\Elibs\Helper::showMongoDate(@$val['deleted_at'])??'Chưa cập nhật'}}</i>
                        <div>
                            Người xóa: <i>{{@$val['deleted_by']['name']??'Chưa cập nhật'}}</i>
                        </div>
                    @endif
                </td>
                @if(@$val['status'] == \App\Http\Models\Orders::STATUS_NO_PROCESS)
                    <td class="text-right">
                        <span style="margin-bottom: 10px;display: inline-block; cursor: pointer" title="{{ \App\Http\Models\Orders::getStatus(@$val['status'])['text'] }}"
                              class="text-{{ \App\Http\Models\Orders::getStatus(@$val['status'])['style'] }}">
                            {{ \App\Http\Models\Orders::getStatus(@$val['status'])['text'] }}
                        </span>
                        {{--@todo @kayn Cần thêm xem chi tiết lịch sử đơn hàng--}}
                        <ul class="icons-list">
                            <li class="text-danger-600">
                                <a href="javascript:void(0);" data-popup="tooltip" title="Xóa" data-placement="left"
                                   onclick="return MNG_POST.deleteItem('{{\App\Http\Models\Customer::buildLinkDelete($val,'orders-mpg')}}','{{@$val['_id']}}')">
                                    <i class="icon-trash"></i>
                                </a>
                            </li>
                        </ul>
                    </td>
                @else
                    <td class="text-right">
                            <span style="margin-bottom: 10px;display: inline-block; cursor: pointer"
                                  class="text-bold text-{{ \App\Http\Models\Orders::getStatus(@$val['status'])['style'] }}">{{ \App\Http\Models\Orders::getStatus(@$val['status'])['text'] }}
                            </span>
                    </td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>
</div>