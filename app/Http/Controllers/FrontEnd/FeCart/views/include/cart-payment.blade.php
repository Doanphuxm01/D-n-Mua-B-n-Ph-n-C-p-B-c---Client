<form id="form-payment">
    <div class="cart-pay">
        <div class="cart-address-info">
            <div class="address-info-top">
                <h4>Địa chỉ nhận hàng</h4>
                <a href="{{ public_link('checkout/shipping') }}">Thay đổi địa chỉ</a>
            </div>
            <input type="hidden" name="ref" value="payment">
            <div class="address-info-content">
                <p> <strong>Họ và tên: </strong> {{ @$info['full_name'] }}</p>
                <p> <strong>Số điện thoại:</strong>  {{ @$info['telephone'] }}</p>
                <p> <strong> Địa chỉ nhận hàng:</strong>  {{ @$info['street'] }}, {{ @$info['town']['name'] }}, {{ @$info['district']['name'] }}, {{ @$info['city']['name'] }}</p>
                <p> <strong>Ghi chú:</strong>  {{ @$info['note']?:'Không' }}</p>
            </div>
        </div>
        <div class="cart-pay-method">
            <h4 class="method-title">CHỌN HÌNH THỨC THANH TOÁN</h4>
            <hr>
            @foreach($payments as $kpay => $pay)
                <div class="method-item" id="{{ $kpay }}">
                    <div class="icon-check">
                        <input type="radio" {{ $loop->first ? 'checked' : '' }} value="{{ $kpay }}" name="pay-method">
                        <span></span>
                    </div>
                    <div class="method-item-content">
                        <b>{{ @$pay['text'] }}</b>
                        <p>{{ @$pay['description'] }}</p>
                    </div>
                    @php($bank_transfer_payment = \App\Http\Models\Order::BANK_TRANSFER_PAYMENT)
                    @if($kpay === $bank_transfer_payment)
                        <div class="bengbeng d-none">
                            <p>Chọn ngân hàng anh/chị muốn thanh toán:</p>
                            <div class="bank-item">
                                <input type="radio" name="bank" value="VP_bank" checked>
                                <picture>
                                    <img src="{{ public_link('html-viettech/images/bank/VP_Bank.png') }}" alt="VP_bank">
                                </picture>
                            </div>
                            <div class="bank-item">
                                <input type="radio" name="bank" value="Bidv">
                                <picture>
                                    <img src="{{ public_link('html-viettech/images/bank/bidv.png') }}" alt="Bidv">
                                </picture>
                            </div>
                            <div class="bank-item">
                                <input type="radio" name="bank" value="Techcombank">
                                <picture>
                                    <img src="{{ public_link('html-viettech/images/bank/techcombank.png') }}" alt="Techcombank">
                                </picture>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
            <hr>

            <button type="button" class="btn btn-order mx-auto d-block rounded-0 text-uppercase">Đặt mua</button>
        </div>
    </div>
</form>
@push('JS_REGION')
    <script type="text/javascript">
        <?php
            echo "var bank = ".json_encode($bank_transfer_payment).";";
        ?>
        $(function () {
            $('.btn-order').click(function () {
                _POST_FORM('#form-payment', '/checkout/save')
            })
            $('input[name="pay-method"]').click(function () {
                let val = $(this).val();
                if(val == bank) {
                    $('.bengbeng').addClass('bank-list').removeClass('d-none')
                }else {
                    $('.bengbeng').removeClass('bank-list').addClass('d-none')
                }
            })
        })
    </script>
@endpush