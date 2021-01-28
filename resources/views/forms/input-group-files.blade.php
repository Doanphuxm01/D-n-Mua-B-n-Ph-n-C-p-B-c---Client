@if(!isset($containerKey))
    <?php
    $containerKey = 'fileGlobalContainer';
    ?>

@endif

<div id="{{$containerKey}}">
    @if(isset($obj['files']) && is_array($obj['files']))
        @php($tmpFiles =collect(@$obj['files'] )->filter(function($file){ return @$file  && in_array(strtolower(  \App\Elibs\Helper::getFileExtension(@$file['src'])), ['.png', '.jpg', '.jepg']); }))
            <p class="imglist" @if(count($tmpFiles)==0) style="display:none"  @endif>

                @foreach( $tmpFiles as $key=>$val)
                    <a target="_blank"
                       data-fancybox="images"
                       @if($key > 2)
                       style="display: none"
                       id="fancy-file____{{$containerKey.$key}}"
                       @endif
                       href="{!! \App\Http\Models\Media::getFileLink(@$val['src']) !!}" class="sp-line-1">
                        <img src="{!! \App\Http\Models\Media::getFileLink(@$val['src']) !!}" alt="">
                    </a>
                @endforeach
                @if(count($obj['files'])>3)
                <a class='show-all-img' href="#" onclick="return show_fancy_box()"
                   style="background-color: grey;color: white;font-size: larger;text-align: center; line-height: 120px">
                    Xem tất cả
                </a>
                    @endif
            </p>
        @if($preview)
                        <div class="alert alert-info" role="alert">
                            <i class="mdi mdi-alert-circle-outline mr-2"></i> Có {{count($obj['files'])}} file đính kèm!
                        </div>@endif
        @foreach(@$obj['files'] as $key=>$val)

            @if(isset($val['src']))
                @if($preview)
                    <div>
                        Tên file: {{value_show(@$val['name'],'Không có tên')}}
                        <br/>
                        <a target="_blank" href="{!! \App\Http\Models\Media::getFileLink(@$val['src']) !!}"
                           class="sp-line-1">
                            {{$val['src']}}
                        </a>
                        <hr class="mt-1 mb-0"/>
                    </div>
                @else
                    <div class="form-group mb-1 js-document-container" id="file____{{$containerKey.$key}}">
                        <div class="input-group ">
                            <input type="text" style="z-index: 0" class="form-control form-control-sm js-document-name"
                                   name="files_name[]" value="{{@$val['name']}}"
                                   placeholder="Nhập tên file (nếu cần...)">
                            <input type="text" style="z-index: 0" readonly=""
                                   class="form-control form-control-sm js-document-file" name="files[]"
                                   value="{{@$val['src']}}" placeholder="File tài liệu">
                            <div class="input-group-append">
                                <a target="_blank" href="{!! \App\Http\Models\Media::getFileLink(@$val['src']) !!}"
                                   class="btn btn-secondary btn-sm waves-effect form-control-sm  js-document-link">Xem
                                    file</a>
                                <a onclick="return _confirmRemoveFile('file____{{$containerKey.$key}}')"
                                   href="javascript:void(0)"
                                   class="btn btn-danger btn-sm waves-effect  form-control-sm waves-light js-document-del"><i
                                            class="icon-trash "></i> </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
    @else
        @if($preview)
            <div class="text-muted">Không có file đính kèm</div>
        @endif
    @endif
</div>
@if(!$preview)
    <div class="form-group mt-3">
        <button id="pickFileGlobal_{{$containerKey}}" class="btn btn-light btn-sm" style="width: 100%"><i
                    class="fe-paperclip"></i> Đính kèm file
        </button>
    </div>
    <script type="text/javascript">
        lsFormUpload.push('{{$containerKey}}');

        function initUploadInputForm(containerKey) {
            _UPLOAD_INIT('pickFileGlobal_' + containerKey, '#' + containerKey,(file)=>{
                {{--let count = $('.imglist a').length;--}}
                {{--$('.imglist').append(`--}}
                {{-- <a target="_blank"--}}
                {{--       data-fancybox="images"--}}
                {{--     ${count > 2 ? 'style="display: none"' : ""}--}}
                {{--    id="fancy-file____{{$containerKey}}${count}"--}}
                {{--    href="${file['src']}">--}}
                {{--        <img src="${file['src']}" alt="">--}}
                {{--    </a>--}}

                {{--`)--}}
            });
        }


        function _confirmRemoveFile(id) {
            Swal.fire(
                {
                    title: "Bạn có chắc chắn muốn xóa file này?",
                    text: "Lưu ý: dữ liệu bị xóa sẽ không thể phục hồi lại được!",
                    type: "warning",
                    showCancelButton: !0,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonClass: "btn btn-success mt-2 btn-sm",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2 btn-sm",
                    buttonsStyling: !1,
                    confirmButtonText: "Vâng, Tôi muốn xóa!"
                }).then(function (t) {
                if (t.value) {
                    haveChangeData = true
                    $('#' + id).remove();
                    $('#fancy-' + id).remove();
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function (event) {
        if(('.imglist a').length >=4){
            $('.show-all-img').show()
        }else{
            $('.show-all-img').hide()
        }
        });
    </script>
@endif
