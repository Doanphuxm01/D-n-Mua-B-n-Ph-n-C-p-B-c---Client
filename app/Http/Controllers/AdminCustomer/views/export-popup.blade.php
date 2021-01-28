<div class="modal-dialog modal-large">
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    <div class="modal-content">
        <div class="modal-header bg-brand">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Xuất file excel tất cả các kết quả tìm được </h3>
        </div>
        <div class="modal-body no-padding">
            <form action="{{admin_link('customer')}}" method="GET" id="popupInputForm">
                <input type="hidden" name="action" value="export_excel">
                <table class="table">
                    <tr>
                        <td>
                            <i class="icon-info3"></i>
                            <span class="text-primary text-bold text-center text-size-large">
                                Lựa chọn cấu hình
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>
                                <input type="radio" name="output" checked value="basic" class="styled1"> <b>
                                    Thông tin cơ bản
                                </b>
                            </label>
                        </td>

                    </tr>

                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="name"
                                          checked disabled
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName(' tên thành viên')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="account"
                                          checked disabled
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('tên đăng nhập')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="email"
                                          checked disabled
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName(' email')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="phone"
                            checked disabled name="select_field[]">{{\App\Http\Models\Member::getFieldName(' số điện thoại')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="can_cuoc_cong_dan"
                            checked disabled name="select_field[]">{{\App\Http\Models\Member::getFieldName(' chứng minh thư nhân dân')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="chuc_danh"
                            checked disabled name="select_field[]">{{\App\Http\Models\Member::getFieldName(' vai trò')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="code"
                            checked disabled name="select_field[]">{{\App\Http\Models\Member::getFieldName(' mã code')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="ma_gioi_thieu"
                            checked disabled name="select_field[]">{{\App\Http\Models\Member::getFieldName(' mã giới thiệu')}}
                            </label></td>
                    </tr>
                </table>

            </form>
        </div>
        <div class="modal-footer">
            <button onclick="submitFormExport()"
                    type="button"
                    class="btn btn-primary"

            >
                Xem file và tải
            </button>


        </div>
    </div>
</div>
<script type="text/javascript">
    function submitFormExport() {
        let popupdata = $('#popupInputForm').serialize();
        let filterdata = $('#form-filter').serialize();
        let link = `{{admin_link('customer')}}?${popupdata}&${filterdata}`
        location.href = link;
    }
</script>
