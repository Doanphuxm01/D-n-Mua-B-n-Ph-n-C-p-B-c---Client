<div class="modal-dialog modal-large">
    {!! \App\Elibs\HtmlHelper::getInstance()->setLinkJs('backend-ui/assets/js/plugins/forms/styling/uniform.min.js') !!}
    <div class="modal-content">
        <div class="modal-header bg-brand">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Xuất file excel tất cả các kết quả tìm được </h3>
        </div>
        <div class="modal-body no-padding">
            <form action="{{admin_link('staff')}}" method="GET" id="popupInputForm">
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
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="code"
                                          checked disabled
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('code')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="name"
                                          checked disabled
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('name')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="account"
                                          checked disabled
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('account')}}
                            </label></td>
                    </tr>

                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="role_group"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('role_group')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="department"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('department')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="position"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('position')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="gender"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('gender')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="date_of_birth"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('date_of_birth')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="noi_sinh"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('noi_sinh')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="nguyen_quan"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('nguyen_quan')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="dan_toc"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('dan_toc')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="ton_giao"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('ton_giao')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="quoc_tich"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('quoc_tich')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="so_bhxh"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('so_bhxh')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="ma_so_thue"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('ma_so_thue')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="tinh_trang_hon_nhan"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('tinh_trang_hon_nhan')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="tien_an_tien_su"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('tien_an_tien_su')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="ngoai_ngu"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('ngoai_ngu')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="ho_khau_thuong_chu"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('ho_khau_thuong_chu')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="emails"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('emails')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="phones"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('phones')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="tinh_trang_cong_viec"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('tinh_trang_cong_viec')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="department"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('department')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="chuc_vu_hien_tai"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('chuc_vu_hien_tai')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="giay_to"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('giay_to')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="tk_ngan_hang"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('tk_ngan_hang')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="to_chuc_doan_the"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('to_chuc_doan_the')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="lien_he_khac"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('lien_he_khac')}}
                            </label></td>
                    </tr>
                    <tr>
                        <td><label><input type="checkbox" class="styled ml-4 mr-1" value="files_thong_tin_co_ban"
                                          name="select_field[]">{{\App\Http\Models\Member::getFieldName('files_thong_tin_co_ban')}}
                            </label></td>
                    </tr>

                    <tr>
                        <td><label class="text-bold"><input type="radio" name="output" value="work-contract"> Thông tin
                                công việc(hợp
                                đồng)</label></td>
                    </tr>
                    <tr>
                        <td><label class="text-bold"><input type="radio" name="output" value="work-process"> Thông tin
                                công việc(quá trình
                                công tác)</label></td>
                    </tr>
                    <tr>
                        <td><label class="text-bold"><input type="radio" name="output" value="family"> Thông tin gia
                                đình
                            </label></td>
                    </tr>
                    <tr>
                        <td><label class="text-bold"><input type="radio" name="output" value="edu-bang-cap"> Thông tin
                                Đào tạo(bằng
                                cấp)</label></td>
                    </tr>
                    <tr>
                        <td><label class="text-bold"><input type="radio" name="output" value="edu-chung-chi"> Thông tin
                                Đào tạo(chứng
                                chỉ)</label></td>
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
        let link = `{{admin_link('staff')}}?${popupdata}&${filterdata}`
        location.href = link;
    }
</script>
