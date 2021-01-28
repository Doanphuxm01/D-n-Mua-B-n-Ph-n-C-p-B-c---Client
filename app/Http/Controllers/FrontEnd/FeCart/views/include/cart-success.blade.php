<div class="oder-success">
    <div class="oder-success-title">
        <img src="/images/icon-disc.png" alt="">
        <h3>Đặt hàng thành công <span>Cảm ơn bạn đã mua hàng của chúng tôi</span></h3>
    </div>
    <div class="order-infomations">
        <p> Mã số đơn hàng: ABVZC14 </p>
        <p> Để theo dõi tình trạng đơn hàng vui lòng vào phần <a href="javascript:void(0)">Quản lý đơn hàng</a> </p>
        <p> Thông tin đơn hàng đã được gửi đến địa chỉ email: datnguyenhai@gmail.com </p>
        <p> Nếu không tìm thấy email này vui lòng kiểm tra trong Spam hoặc Junk folder. </p>
    </div>
    <div class="check-infomations">
        <h4>Kiểm tra lại thông tin</h4>
        <div class="info-content">
            <p class="tit">Địa chỉ nhận hàng</p>
            <p> <strong>Họ và tên: </strong> {{ @$info['fullname'] }}</p>
            <p> <strong>Số điện thoại:</strong>  {{ @$info['telephone'] }}</p>
            <p> <strong> Địa chỉ nhận hàng:</strong>  {{ @$info['street'] }}, {{ @$info['town'] }}, {{ @$info['district'] }}, {{ @$info['city'] }}</p>
            <p> <strong>Ghi chú:</strong>  {{ @$info['note']?:'Không' }}</p>
            <p class="payments">Hình thức thanh toán</p>
            <p class="color--text">Thanh toán khi nhận hàng</p>
        </div>
    </div>
    <div class="btn-actions">
        <button>tiếp tục mua sắm</button>
        <button class="btn-order">Quản lý đơn hàng</button>
    </div>
</div>