@php
    $tab = request('tab');
    $tab= $tab ? $tab : 'tab-info';
@endphp
<form id="popupform">
    <div class="modal-dialog modal-large modal-md">
        <div class="modal-content">
            <div class="modal-header bg-teal-800">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    Đổi mật khẩu
                </h3>
            </div>
            <div class="modal-body ">

                <div class="form-group row">
                    <label class="control-label col-md-3">Tài khoản</label>
                    <div class="col-md-9">
                        <input disabled id="obj-account" value="{{@$obj['account']}}" type="text"
                               class="form-control text-bold" placeholder="...">

                    </div>
                </div>
                <input type="hidden" name="id" value="{{$obj['_id']}}">
                <div class="form-group row">
                    <label class="control-label col-md-3">Mật khẩu cũ</label>
                    <div class="col-md-9">
                        <input type="password" name="password"
                               class="form-control text-bold" placeholder="...">

                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-md-3">Mật khẩu mới</label>
                    <div class="col-md-9">
                        <input type="password" name="new-password"
                               class="form-control text-bold" placeholder="...">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="control-label col-md-3">Nhập lại mật khẩu mới</label>
                    <div class="col-md-9">
                        <input type="password" name="re-new-password"
                               class="form-control text-bold" placeholder="...">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class=" btn btn-primary"
                        onclick="return MNG_POST.update('{{admin_link('/staff/update-change-password')}}','#popupform');"
                >Cập nhật password
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">Đóng lại</button>
            </div>
        </div>

    </div>
</form>