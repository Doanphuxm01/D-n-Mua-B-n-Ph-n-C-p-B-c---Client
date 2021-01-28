<div class="cart-form border">
    <div class="cart-form-top">
        <h4>Nhập địa chỉ nhận hàng</h4>
        <p>Vui lòng nhập địa chỉ nhận hàng</p>
    </div>
    <div class="info-custommer">
        <form id="address-info">
            <label for="full_name">Họ và tên</label>
            <input type="text" name="order[full_name]" value="{{ @$info['full_name'] }}" id="full_name" class="form-control" placeholder="Nhập họ tên">

            <label for="telephone">Số điện thoại</label>
            <input type="text" name="order[telephone]" value="{{ @$info['telephone'] }}" id="telephone" class="form-control" placeholder="Số điện thoại">

            <label for="email">Email</label>
            <input type="email" name="order[email]" value="{{ @$info['email'] }}" id="email" class="form-control" placeholder="Email">


            <div class="group-khu-vuc">
                <label for="">Tỉnh/Thành phố</label>
                <select class="form-control city" name="order[city]" id="city">
                    <option value="">Chưa lựa chọn</option>
                    @if(@$info['email']) <option selected value="{{ @$info['city']['id'] }}">{{ @$info['city']['name'] }}</option> @endif
                </select>

                <label for="">Quận/Huyện</label>
                <select class="form-control district" name="order[district]" id="district">
                    <option value="">Chưa lựa chọn</option>
                    @if(@$info['district']) <option selected value="{{ @$info['district']['id'] }}">{{ @$info['district']['name'] }}</option> @endif
                </select>
                <label for="">Phường/Xã</label>
                <select class="form-control town" name="order[town]" id="town">
                    <option value="">Chưa lựa chọn</option>
                    @if(@$info['town']) <option  selected value="{{ @$info['town']['id'] }}">{{ @$info['town']['name'] }}</option> @endif
                </select>
            </div>
            <input type="hidden" name="ref" value="shipping">
            <label for="street">Địa chỉ</label>
            <input type="text" class="form-control" value="{{ @$info['street'] }}" name="order[street]" id="street" placeholder="Vd: số nhà, số ngõ">

            <label for="">Ghi chú thêm (Không bắt buộc)</label>
            <textarea class="form-control" name="order[note]" value="{{ @$info['note'] }}" id="note" cols="30" rows="5" placeholder="Nhập thêm thông tin ghi chú"></textarea>

            <button type="button" class="btn btn-order mx-auto d-block rounded-0">Giao đến địa chỉ này</button>
        </form>
    </div>
</div>

@push('JS_REGION')
    <script>
        $(document).ready(function () {
            select_tinh_thanh_quan_huyen_fx_bot($('.group-khu-vuc'), '/public_api/location/');
            $('.btn-order').click(function () {
                _POST_FORM('#address-info', '/checkout/save')
            })
        });
    </script>
@endpush