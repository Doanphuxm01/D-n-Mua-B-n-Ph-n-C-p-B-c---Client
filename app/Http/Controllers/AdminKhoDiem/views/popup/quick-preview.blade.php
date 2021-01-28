
<div class="col-8 m-auto">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title" id="myCenterModalLabel">Hóa đơn</h4>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div id="invoice-content">

            <div class="card-box">
                <div class="row p-5">
                    <div class="col-md-6">
                        <div class="mt-3">
                            <p><b>Xin chào, {{ $obj->fullname }}</b></p>
                            <p class="text-muted">Cảm ơn rất nhiều vì bạn tiếp tục mua sản phẩm của chúng tôi.
                                Cửa hàng chúng tôi hứa sẽ cung cấp các sản phẩm chất lượng cao cho bạn cũng như dịch vụ khách hàng xuất sắc cho mọi giao dịch. </p>
                        </div>

                    </div><!-- end col -->
                    <div class="col-md-5 offset-md-1">
                        <div class="mt-3 float-right">
                            <p class="m-b-10"><strong>Ngày đặt hàng : </strong> <span class="float-right">{{ \App\Elibs\Helper::showMongoDate(@$obj['created_at'], 'd/m/Y - H:i') }}</span></p>
                            @php($arrSta = \App\Http\Models\PurchaseOrder::getListStatus(FALSE, @$obj->status))
                            <p class="m-b-10"><strong>Tình trạng đặt hàng : </strong> <span class="float-right"><span class="badge badge-{{ $arrSta['style'] }}">{{ $arrSta['text'] }}</span></span></p>
                            <p class="m-b-10"><strong>Mã đơn hàng : </strong> <span class="float-right font-weight-bold text-warning">&nbsp; #{{ @$obj['code'] }} </span></p>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row mt-3">
                    <div class="col-sm-6">
                        {{--<h6>Phương thức thanh toán</h6>
                        <b class="text-warning d-block">{{ \App\Http\Models\PurchaseOrder::paymentType(FALSE, @$obj->payment_type)['text'] }}</b>
                        @if(@$obj->payment_type == 2)
                            <b>Tên Ngân Hàng:</b> {{ @$bank->bank }}<br>
                            <b>Số tài khoản:</b> {{ @$bank->account }}<br>
                            <b>Tên Chủ tài khoản:</b> {{ @$bank->name }}<br>
                            <b>Chi nhánh:</b> {{  @$bank->branch }}
                        @endif--}}
                    </div> <!-- end col -->

                    <div class="col-sm-6">
                        <b>Địa chỉ giao hàng</b>
                        <address>
                            Tỉnh / Thành Phố: {{ @$obj['city']['name'] }}<br>
                            Quận/Huyện: {{ @$obj['district']['name'] }}<br>
                            Phường/Xã: {{ @$obj['town']['name'] }}<br>
                            Địa chỉ: {{ @$obj['street']?:'Chưa cập nhật' }}<br>
                            Ghi chú: {{ @$obj['note']?:'Chưa cập nhật' }}<br>
                            <abbr title="Phone">P:</abbr> {{ @$obj['phone'] }}
                        </address>
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table mt-4 table-centered">
                                <thead>
                                <style>
                                    th {
                                        font-weight: bold;
                                    }
                                </style>
                                <tr><th>#</th>
                                    <th>Sản phẩm</th>
                                    <th style="width: 20%">Số lượng</th>
                                    <th style="width: 20%" class="text-right">Tiền</th>
                                </tr></thead>
                                <tbody>
                                @foreach ($obj['details'] as $item)
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <b>{{ @$item['name']}}</b> <br/>
                                        </td>
                                        <td>{{ @$item['amount']?:0 }}</td>
                                        <td class="text-right">{{ \App\Elibs\Helper::formatMoney(@$item['finalPrice']?:0) }}</td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-sm-6">
                        <div class="clearfix pt-5">
                            {{-- <h6 class="text-muted">Notes:</h6>

                            <small class="text-muted">
                                All accounts are to be paid within 7 days from receipt of
                                invoice. To be paid by cheque or credit card or direct payment
                                online. If account is not paid within 7 days the credits details
                                supplied as confirmation of work undertaken will be charged the
                                agreed quoted fee noted above.
                            </small> --}}
                        </div>
                    </div> <!-- end col -->
                    <div class="col-sm-6">
                        <div class="text-right">
                            {{-- <p><b>Phí vận chuyển:</b> <span class="float-right">{{ \Lib::priceFormat(@$obj->transport_fee?:0) }}</span></p> --}}
                            {{--<p><b>Mã giảm giá:</b> <span class="float-right"> &nbsp;&nbsp;&nbsp; {{ \StringLib::getStrVal(@$obj->coupon_code?:"Chưa áp dụng") }}</span></p>--}}
                            <h3>Tổng tiền: <span class="text-danger">{{ \App\Elibs\Helper::formatMoney(@$obj['grandTotal']?:0) }}</span></h3>
                        </div>
                        <div class="clearfix"></div>
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div> <!-- end card-box -->
        </div>
        <div class="modal-footer">
            <div class="text-right d-print-none">
                <a href="javascript:void(0);" id="invoice-print" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Bỏ qua</button>

            </div>
        </div>
    </div><!-- /.modal-content -->
</div>
<script>
    document.getElementById("invoice-print").onclick = function () {
        printElement(document.getElementById("invoice-content"));
    };

    function printElement(elem) {
        var domClone = elem.cloneNode(true);

        var $printSection = document.getElementById("printSection");

        if (!$printSection) {
            var $printSection = document.createElement("div");
            $printSection.id = "printSection";
            document.body.appendChild($printSection);
        }

        $printSection.innerHTML = "";
        $printSection.appendChild(domClone);
        window.print();
    }
</script>
<style>
    @media screen {
        #printSection {
            display: none;
        }
    }

    @media print {
        body * {
            visibility:hidden;
        }
        #printSection, #printSection * {
            visibility:visible;
        }
        #printSection {
            position:absolute;
            left:0;
            top:0;
        }
    }
</style>