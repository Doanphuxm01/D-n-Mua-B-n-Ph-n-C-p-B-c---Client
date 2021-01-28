@if(!isset($allCity))
    @php
        $allCity = collect(\App\Http\Models\Location::getAllCity());
    @endphp
@endif
@php($allStaffDataList = \App\Http\Models\MetaData::getAllByObject('staff'))

<style>
    .d-flex {
        display: flex;
    }

    .justify-content-center {
        align-items: center;
    }
</style>
<script>
    $.datepicker.setDefaults({
        dateFormat: 'dd/mm/yy'
    });
</script>
<form action="{{admin_link('/staff/save_tab_info')}}" id="postInputForm">

    <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <input type="hidden" name="obj[_id]" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    Thông tin gia đình
                </h6>
            </div>
            <div class="" style="overflow: scroll">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>TT</th>
                        <th>Họ tên

                        <th>Ngày sinh</th>
                        <th>Mối quan hệ</th>
                        <th>Tình trạng</th>
                        <th>Nghề nghiệp</th>
                        <th>Nơi ở hiện nay</th>
                        <th width="1" class="text-right">
                            <a onclick="clone_thong_tin_gia_dinh()"
                               class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>

                    </tr>

                    </thead>
                    <tbody id="obj_thong_tin_gia_dinh">
                    @isset($obj['thong_tin_gia_dinh'])
                        @foreach(@$obj['thong_tin_gia_dinh'] as $key=>$value)
                            <tr>
                                <td>{{$key +1}}</td>
                                <td>
                                    <input type="text" class="form-control"
                                           name="obj[thong_tin_gia_dinh][{{$key}}][ho_ten]"
                                           value="{{@$value['ho_ten']}}"
                                           placeholder="Họ tên....">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[thong_tin_gia_dinh][{{@$key}}][ngay_sinh]"
                                           placeholder="Ngày sinh..."
                                           @if(isset($value['ngay_sinh']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_sinh'])}}"
                                           @endif
                                </td>
                                <td>
                                    <div class="col-md-9">
                                        <select name="obj[thong_tin_gia_dinh][{{@$key}}][moi_quan_he_gia_dinh]"
                                                class="select-search select-md"
                                        >
                                            <option value="">Chưa lựa chọn</option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Anh trai") selected
                                                    @endif value="Anh trai">Anh trai
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Em trai") selected
                                                    @endif value="Em trai">Em trai
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Chị gái") selected
                                                    @endif value="Chị gái">Chị gái
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Em gái") selected
                                                    @endif value="Em gái">Em gái
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Bố đẻ") selected
                                                    @endif value="Bố đẻ">Bố đẻ
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Bố dượng") selected
                                                    @endif value="Bố dượng">Bố dượng"
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Mẹ đẻ") selected
                                                    @endif value="Mẹ đẻ">Mẹ đẻ
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Mẹ kế") selected
                                                    @endif value="Mẹ kế">Mẹ kế
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Con gái") selected
                                                    @endif value="Con gái">Con gái
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Con trai") selected
                                                    @endif value="Con trai">Con trai
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Con nuôi") selected
                                                    @endif value="Con nuôi">Con nuôi
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Vợ") selected
                                                    @endif value="Vợ">Vợ
                                            </option>
                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']) && $obj['thong_tin_gia_dinh'][$key]['moi_quan_he_gia_dinh']=="Chồng") selected
                                                    @endif value="Chồng">Chồng
                                            </option>
                                        </select>
                                    </div>

                                </td>
                                <td>
                                    <select class="form-control select-md"
                                            name="obj[thong_tin_gia_dinh][{{$key}}][tinh_trang]">
                                        <option value="">Chưa lựa chọn</option>
                                        <option @if(isset($obj['thong_tin_gia_dinh'][$key]['tinh_trang']) && $obj['thong_tin_gia_dinh'][$key]['tinh_trang'] =='còn sống') selected
                                                @endif value="còn sống">
                                            Còn sống
                                        </option>
                                        <option value="Đã mất"
                                                @if(isset($obj['thong_tin_gia_dinh'][$key]['tinh_trang']) && $obj['thong_tin_gia_dinh'][$key]['tinh_trang'] =='Đã mất') selected @endif>
                                            Đã mất
                                        </option>
                                    </select>
                                </td>
                                <td>
                                    <select name="obj[thong_tin_gia_dinh][{{@$key}}][nghe_nghiep][id]"
                                            class="select-search select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NGHE_NGHIEP;}) as $val)

                                            <option @if(isset($obj['thong_tin_gia_dinh'][$key]['nghe_nghiep']['id']) && $obj['thong_tin_gia_dinh'][$key]['nghe_nghiep']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control "
                                           name="obj[thong_tin_gia_dinh][{{@$key}}][noi_o_hien_nay]"
                                           placeholder="Nơi ở hiện nay.."
                                           value="{{@$value['noi_o_hien_nay']}}">

                                </td>
                                <td class="text-right"><i class="icon-trash text-danger"
                                                          onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endisset

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <a class="" data-toggle="collapse" href="#gFile" aria-expanded="true">File đính kèm</a>
                </h6>
            </div>
            <input type="hidden" name="obj[files_thong_tin_gia_dinh]" value="">

            <div id="gFile" class="panel-collapse collapse in" aria-expanded="true">
                <div class="panel-body no-padding-bottom">
                    <div id="documentFileRegion">
                        @if(isset($obj['files_thong_tin_gia_dinh']) && $obj['files_thong_tin_gia_dinh'] && is_array($obj['files_thong_tin_gia_dinh']))
                            @foreach($obj['files_thong_tin_gia_dinh'] as $key=> $file)
                                <div class="form-group js-document-container" id="file_{{$key}}">

                                    <div class="col-md-12">
                                        <div class="input-group">
                                            <div style="display: flex">
                                                <input type="text" style="z-index: 0" class="form-control "
                                                       name="obj[files_thong_tin_gia_dinh][name][]"
                                                       @isset($file['name']) value="{{$file['name']}}" @endisset
                                                       placeholder="Tên file">
                                                <input type="text" style="z-index: 0" readonly=""
                                                       class="form-control js-document-file"
                                                       name="obj[files_thong_tin_gia_dinh][path][]"
                                                       @isset($file['path']) value="{{$file['path']}}" @endisset

                                                       placeholder="File tài liệu">
                                            </div>

                                            <div class="input-group-btn">
                                                <a target="_blank"
                                                   href="{{\App\Http\Models\Media::getFileLink($file['path'])}}"
                                                   class="btn btn-default js-document-link">Xem file</a>
                                                <a onclick="_removeFile('#file_{{$key}}')" href="javascript:void(0)"
                                                   class="btn btn-default js-document-del"><i
                                                            class="icon-trash text-danger"></i> </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="form-group">
                        <div class="col-md-11 p-3  text-right">
                            <button id="pickfiles" type="button" class="btn bg-primary btn-xs"><i
                                        class="fa fa-plus"></i>
                                Thêm file
                            </button>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="col-md-6 text-right">
        <button class="btn btn-primary " id="save-button"
                onclick="return MNG_POST.update('{{admin_link('/staff/_save_tab_family')}}','#postInputForm');">
            Lưu lại
        </button>
    </div>

</form>


<div style="display: none" id="uploadFileItemClone">
    <div class="form-group js-document-container">
        <div class="col-md-12">
            <div class="input-group"><span
                        style="position: absolute;display: none; z-index: 100; right: 200px; top: 8px;"
                        class="js-document-loading"><i
                            class="fa fa-spinner fa-spin"></i> Đang upload vui lòng đợi....</span>
                <div style="display: flex">
                    <input type="text" style="z-index: 0" class="form-control "
                           name="obj[files_thong_tin_gia_dinh][name][]" value="" placeholder="Tên file">
                    <input type="text" style="z-index: 0" readonly="" class="form-control js-document-file"
                           name="obj[files_thong_tin_gia_dinh][path][]" value="" placeholder="File tài liệu">

                </div>
                <div class="input-group-btn">
                    <a target="_blank" href="" class="btn btn-default js-document-link">Xem file</a>
                    <a href="javascript:void(0)" class="btn btn-default js-document-del"><i
                                class="icon-trash text-danger"></i> </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    INPUT_NUMBER();

    function clone_thong_tin_gia_dinh() {
        let temp_select_class = "select-seart-" + Number(new Date());
        let tt = $('#obj_thong_tin_gia_dinh tr').length;
        let tmp = `
           <tr>
                                <td>${tt + 1}</td>
                                <td>
                                    <input type="text" class="form-control"
                                           name="obj[thong_tin_gia_dinh][${tt}][ho_ten]"

                                           placeholder="Họ tên....">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[thong_tin_gia_dinh][${tt}][ngay_sinh]"
                                           placeholder="Ngày sinh..."


                                </td>
                                <td>
                                        <select name="obj[thong_tin_gia_dinh][${tt}][moi_quan_he_gia_dinh]"
                                                class="select-search ${temp_select_class} select-md"
                                        >
                                            <option value="">Chưa lựa chọn</option>
                                            <option value="Anh trai">Anh trai</option>
                                            <option value="Em trai">Em trai</option>
                                            <option value="Chị gái">Chị gái</option>
                                            <option value="Em gái">Em gái</option>
                                            <option value="Bố đẻ">Bố đẻ</option>
                                            <option value="Bố dượng">Bố dượng"</option>
                                            <option value="Mẹ đẻ">Mẹ đẻ</option>
                                            <option value="Mẹ kế">Mẹ kế</option>
                                            <option value="Con gái">Con gái</option>
                                            <option value="Con trai">Con trai</option>
                                            <option value="Con nuôi">Con nuôi</option>
                                            <option value="Vợ">Vợ</option>
                                            <option value="Chồng">Chồng</option>
                                        </select>
                                </td>
                                <td>
                                    <select class="form-control "
                                           name="obj[thong_tin_gia_dinh][${tt}][tinh_trang]"     >
                                        <option value="">Chưa lựa chọn</option>
                                        <option value="còn sống">Còn sống</option>
                                        <option value="Đã mất">Đã mất</option>
                                    </select>

                                </td>
                                <td>
                                          <select name="obj[thong_tin_gia_dinh][${tt}][nghe_nghiep][id]"
                                            class="select-search select-md ${temp_select_class}" >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NGHE_NGHIEP;}) as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>
                                        @endforeach
            </select>

        </td><td>
            <input type="text" class="form-control "
                   name="obj[thong_tin_gia_dinh][${tt}][noi_o_hien_nay]"
                                           placeholder="Nơi ở hiện nay.."
                                         >

                                </td>
                                <td class="text-right"><i class="icon-trash text-danger"
                                                          onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>
`
        ;
        $('#obj_thong_tin_gia_dinh').append(tmp);
        DATE_PICKER_INIT()

        $(`.${temp_select_class}`).select2()
    }


    MNG_MEDIA.uploadInit({
        loading_element: '#loading_upload',
        input_element: '#document_file',
        link_element: '#document_file_link'
    });
    function _removeFile($element) {
        bootbox.confirm("File của bạn sẽ bị xóa.<br/>Bạn có chắc chắn muốn thực hiện hành động này?", function (result) {
            if (result) {
                $($element).remove();
            }
        });
    }

</script>
