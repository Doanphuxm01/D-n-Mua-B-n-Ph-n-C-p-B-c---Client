<div class="col">
    <div class="table-responsive" id="lsObj">
        <table class="table table-centered table-bordered  table-hover" id="products-datatable">
            <thead>
            <tr>
                <th style="width: 10px;" align="center">
                    <div class="custom-control custom-checkbox ml-1">
                        <input type="checkbox" class="custom-control-input" id="checkAll">
                        <label class="custom-control-label" for="checkAll">&nbsp;</label>
                    </div>
                </th>
                @foreach([
                    "Đơn hàng",
                    "Khách hàng",
                    "Chi tiết đơn hàng",
                    "Phương thức thanh toán",
                    "Tình trạng đơn hàng",
                    "Ngày tạo"
                ] as $k=> $label)
                    <th title="{{$label}}">
                        <div class="sp-line-1">
                            {{$label}}
                        </div>
                        @if($k==0)
                            <div><span class="text-danger">({{@$listObj->total()}}) bản ghi</span></div>
                        @endif
                    </th>
                @endforeach

                <th style="width: 20px;text-align: right">
                    <a href="javascript:void(0);" class="text-danger delete-all-checked" style="display: none;">Xóa</a>
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($listObj as $k => $value)
                <tr>
                    <td class="">
                        <div class="custom-control custom-checkbox ml-1">
                            <input type="checkbox" value="{{ @$value->code }}" data-id="{{ @$value['_id'].'&token=' . \App\Elibs\Helper::buildTokenString($value['_id']) }}" class="custom-control-input item-check" id="customCheck{{$k}}">
                            <label class="custom-control-label" for="customCheck{{$k}}">&nbsp;</label>
                        </div>
                    </td>
                    <td style="width:100px">
                        <a href="javascript:void(0);" onclick="_SHOW_FORM_REMOTE('{{admin_link('danh-sach-don-tra-hang/quick-form?id='.$value['_id'])}}')" title="Đơn hàng của {{ @$value->fullname }}" class="cursor-pointer font-weight-bold d-block">
                            {{ @$value->code }}
                        </a>
                    </td>

                    <td>
                    <span class="">
                        <strong>Họ tên:</strong> <span class="text-success">{{ @$value['fullname'] }}</span>
                    </span><br>
                    <span class="">
                        <strong>Acc:</strong> <span class="text-success">{{ @$value['created_by']['account'] }}</span>
                    </span><br>
                    <span class="">
                        <strong>Email:</strong> {{ @$value['email'] }}
                    </span><br>
                        <span class="">
                        <strong>Phone:</strong> {{ @$value['phone'] }}
                    </span>
                    </td>
                    <td>
                    <span>
                        Số sản phẩm: <strong class="text-danger">{{ \App\Elibs\Helper::numberFormat(@$value['number']) }}</strong>
                    </span><br>

                    <span>
                        Tổng tiền: <strong class="text-danger">{{ \App\Elibs\Helper::formatMoney(@$value['grandTotal']) }}</strong>
                    </span>
                        <br>
                        <span>
                            Đại lý trả hàng: <strong class="text-danger">{{ @$value['agency']['name'] }}</strong>
                        </span><br>
                    </td>
                    <td style="width:150px">
                        {{ \App\Http\Models\PurchaseOrder::paymentType(FALSE, @$value['payment_type'])['text'] }}
                    </td>
                    @php($arrSta = \App\Http\Models\PurchaseOrder::getListStatus(FALSE, @$value['status']))
                    <td style="width:110px">
                        <span class="badge badge-{{ $arrSta['style'] }}">{{ $arrSta['text'] }}</span>
                    </td>

                    <td style="width:100px">
                        {{ \App\Elibs\Helper::showMongoDate(@$value['created_at'], 'd/m/Y - H:i') }}
                    </td>

                    @php($lsStatus = \App\Http\Models\PurchaseOrder::getListStatus())
                    @php($groupAction = \App\Http\Models\PurchaseOrder::getListStatus(FALSE, @$value['status']))

                    <td class="text-right">
                        @foreach($groupAction['group-action'] as $kf => $fail)
                            <a title="Click để đưa đơn sang trạng thái {{ $lsStatus[$fail]['text-action'] }}"
                               @click="_save({{ json_encode($lsStatus[$fail]) }}, '{{ $value['_id'] }}')"
                               data-plugin="tippy" data-tippy-animation="shift-away" data-tippy-arrow="true"  class="action-icon cursor-pointer text-{{ $lsStatus[$fail]['style'] }}">
                                <i class="{{ $lsStatus[$fail]['icon'] }}"></i>
                            </a>
                        @endforeach
                        <a title="Xem lịch sử đơn hàng" href=""
                           data-plugin="tippy" data-tippy-animation="shift-away" data-tippy-arrow="true"  class="action-icon cursor-pointer text-info">
                            <i class="mdi mdi-history"></i>
                        </a>

                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('JS_BOTTOM_REGION')
    <script>
        $(document).ready(function() {
            $('#checkAll').click(function() {
                $('.item-check').prop('checked', this.checked);
                if($('.item-check').filter(":checked").length) {
                    $('.delete-all-checked').show();
                }else {
                    $('.delete-all-checked').hide();
                }
            });

            $('.item-check').change(function () {
                var check = ($('.item-check').filter(":checked").length == $('.item-check').length);
                $('#checkAll').prop("checked", check);
                if($('.item-check').filter(":checked").length) {
                    $('.delete-all-checked').show();
                }else {
                    $('.delete-all-checked').hide();
                }
            });

            $('.delete-all-checked').click(function () {
                var order_ids = [], data = [], codes = [];
                $.each($("input.item-check[type='checkbox']:checked"), function(){
                    order_ids.push($(this).data('id'));
                    codes.push($(this).val());
                });
                if(order_ids) {
                    data.push({'name': 'ids', 'value': order_ids});
                    data.push({'name': 'codes', 'value': codes});
                }
                swal({
                    title: 'Xóa đơn hàng',
                    text: 'Xác nhận chuyển tất cả đơn đã chọn sang trạng thái Đã xóa',
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((t) => {
                        if (t) {
                            var _token = jQuery('meta[name=_token]').attr("content");
                            if (_token) {
                                data.push({'name': '_token', 'value': _token});
                            }
                            jQuery.ajax({
                                url: 'danh-sach-don-hang/_delete_multi',
                                type: "POST",
                                data: data,
                                dataType: 'json',
                                success: function (json) {
                                    if(json.status == 1) {
                                        swal({
                                            title: 'Cập nhật thành công',
                                            text: json.msg,
                                            icon: "success",
                                            buttons: true,
                                            dangerMode: false,
                                        })
                                        window.location.href = json.data.link;
                                    }else{
                                        swal({
                                            title: 'Oops!',
                                            text: json.msg,
                                            icon: "warning",
                                            buttons: true,
                                            dangerMode: false,
                                        })
                                    }
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    alert(thrownError);
                                }
                            });
                            return true;
                        }
                    });
            })
            /*$("input.placement").maxlength({
                alwaysShow: !0,
                placement: "top-left",
                warningClass: "badge badge-success",
                limitReachedClass: "badge badge-danger"
            });
            jQuery(".range-datepicker").daterangepicker({
                autoUpdateInput: false,
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'DD/MM/YYYY hh:mm A',
                    cancelLabel: 'Clear',
                }

            });
            $('.range-datepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY hh:mm A') + ' - ' + picker.endDate.format('DD/MM/YYYY hh:mm A'));
            });
            $('.range-datepicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });*/
        });
        function SHOW_POPUP(id, bidx) {
            shop.ajax_popup('order/popup-preview', 'POST', {
                    id: id
                }, function (response) {
                    if (response.error == 1) {
                        Swal.fire({
                            title: 'Oops!',
                            text: response.msg,
                            type: "warning",
                            showCancelButton: !0,
                            showConfirmButton: 0,
                            cancelButtonColor: "#d33",
                            cancelButtonClass: "btn btn-danger ml-2 mt-2 btn-sm",
                            buttonsStyling: !1,
                        });
                    }else {
                        $('.preview').empty().html(response);
                        $('.bs-example-modal-center').modal({backdrop: 'static'});
                        $('.switchery-popup').each(function (idx, obj) {
                            new Switchery($(this)[0], $(this).data());
                        });
                    }
                },
                'html');
        }
        <?php
            echo "var lsOrders ='" . json_encode(@$listObj['data']) . "';";
            ?>
            lsOrders = JSON.parse(lsOrders);
    </script>

    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('vue.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('sweetalert.min.js') !!}
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui\public\order\order.js') !!}
@endpush
