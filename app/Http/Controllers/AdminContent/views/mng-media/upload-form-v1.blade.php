<style type="text/css">
    #MEDIA-CONTAINER .media-item {
        padding-left: 5px;
        padding-right: 5px;
    }

    #MEDIA-CONTAINER .media-item .thumbnail {
        margin-bottom: 10px;
        border: 1px solid #ccc;
        height: 110px;
        overflow: hidden;
    }

    #MEDIA-CONTAINER .media-item .thumbnail .thumb {
        height: 100%;
        overflow: hidden;
    }

    #noMedia {
        cursor: pointer;
        vertical-align: middle;
        text-align: center;
        font-size: 18px;
        border: 3px dashed #ccc;
        margin: 15px 30px;
        padding: 50px;
        background: #fafafa;
    }

    #noMedia:hover {
        border: 3px dashed #474aa5;
    }

    #noMedia i {
        font-size: 50px;
        color: #999;
    }

    .pop-media-list, .pop-media-preview {
        height: 500px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .pop-media-preview {
        height: 500px;
        padding: 15px 10px 10px;
        background: #f6f6f6;
    }

    .pop-media-preview .pop-preview-image {
        height: 200px;
        overflow: hidden;
        display: table-cell;
        vertical-align: middle;
        background: #ccc;
    }

    .img-selected-in-list {
        width: 50px;
        height: 40px;
        float: left;
        overflow: hidden;
        margin-right: 5px;
        border: 1px solid #ccc;
    }

    .img-selected-in-list img {
        max-width: 100%;
    }

    .img-selected {
        background: blue;
    }

    .upload-error {
        border-color: red;
        border-style: solid;
        border-width: 5px;
    }

</style>
<script type="text/javascript" src="{{url('backend-ui/assets/js/plugins/uploaders/plupload/plupload.full.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}"></script>


<div class="modal-dialog modal-full">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Thư viện hình ảnh</h3>
        </div>

        <div class="modal-body pd-10" style="padding-bottom: 0">
            <div class="row">
                <div class="col-md-9 {{--no-padding--}}">
                    {{--<div class="pop-media-search-tool mb-10">
                        <div class="row">
                            <div class="col-md-2">

                            </div>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="text" class="form-control bg-slate-300" placeholder="Tìm kiếm">
                                    <span class="input-group-addon bg-slate-700"><i class="icon-search4"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>--}}
                    <div class="clearfix"></div>
                    <div class="pop-media-list" id="pop-media-list">
                        <div class="" id="MEDIA-CONTAINER">
                            @if(isset($listObj['data']) && $listObj['data'])
                                @foreach($listObj['data'] as $key=>$val)
                                    <div class="col-md-2 col-sm-6 media-item">
                                        <div class="thumbnail" data-id="MEDIA" id="MEDIA-{{$val['_id']}}">
                                            <div class="thumb">
                                                <img rel="data-container" onerror="this.src='{{url('backend-ui/assets/images/no-image-available.jpg')}}';this.onerror = null"
                                                     data-id="{{$val['_id']}}"
                                                     data-full-size-link="{{\App\Http\Models\Media::getImageSrc($val['src'])}}"
                                                     data-relative-link="{{$val['src']}}"
                                                     data-thumb-size-link="{{\App\Http\Models\Media::getImageSrc($val['src'])}}"
                                                     data-name="{{$val['name']}}"
                                                     data-brief="{{@$val['brief']}}"
                                                     src="{{\App\Http\Models\Media::getImageSrc($val['src'])}}">
                                                <div onclick="return MNG_MEDIA.imageSelected('{{$val['_id']}}')" class="caption-overflow">
                                        <span>
                                            <a href="{{\App\Http\Models\Media::getImageSrc($val['src'])}}" data-popup="lightbox" rel="gallery"
                                               class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class=" icon-eye4"></i></a>
                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="noMedia">
                                    <div class="no-text">
                                        Không tìm thấy hình ảnh/video nào trong thư viện.
                                        <div>
                                            <em>Click để upload thêm vào thư viện</em>
                                        </div>
                                    </div>
                                    <i class="icon-nbsp"></i>
                                </div>
                                <div id="preview">

                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-3 no-padding">
                    <div class="pop-media-preview form-horizontal" id="pop-media-preview">
                        <div id="IMAGE_SELECTED">
                            {{--chứa các hidden input với value là chuỗi json của 1 image--}}
                        </div>
                        <div class="pop-preview-image">
                            <img rel="preview" style="width: 100%" src="{{isset($curent) && $curent && $curent!='undefined'?$curent:url('backend-ui/assets/images/no-image-available.jpg')}}" alt="">
                        </div>
                        <div class="form-group mt-15">
                            <label class="control-label col-md-3"> Ảnh gốc </label>
                            <div class="col-md-9">
                                <input rel="full-link" type="text" class="form-control" readonly="readonly" value="{{isset($curent) && $curent?$curent:''}}">
                            </div>
                        </div>
                        <div class="form-group mt-5">
                            <label class="control-label col-md-3"> Tiêu đề </label>
                            <div class="col-md-9">
                                <input rel="name" type="text" class="form-control" placeholder="Tiêu đề/Tên media...">
                            </div>
                        </div>
                        <div class="form-group mt-5">
                            <label class="control-label col-md-3"> Mô tả </label>
                            <div class="col-md-9">
                                <input rel="brief" type="text" class="form-control" placeholder="Mô tả ngắn...">
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group mt-5">
                            <label class="control-label col-md-3"> Tiêu đề </label>
                            <div class="col-md-9">
                                <input rel="name-draft" type="text" class="form-control" placeholder="Tiêu đề thay thế...">
                            </div>
                        </div>
                        <div class="form-group mt-5">
                            <label class="control-label col-md-3"> Mô tả </label>
                            <div class="col-md-9">
                                <input rel="brief-draft" type="text" class="form-control" placeholder="Mô tả thay thế...">
                            </div>
                        </div>
                        <div class="form-group mt-5">
                            <label class="control-label col-md-3">Size </label>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <select rel="size" class="select">
                                        <option value="*x*">Full Size</option>
                                        <option value="680x*">680x*</option>
                                        <option value="550x*">550x*</option>
                                        <option value="480x*">480x*</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="pop-selected-list">
                <button id="pickfiles" type="button" class="btn btn-primary btn-labeled btn-labeled-left" style="z-index: 99999;float:left"><b><i class=" icon-nbsp"></i></b> Upload thêm</button>
            </div>
            <button onclick="return MNG_MEDIA_BUTTON.click('{{$action_name}}')" type="button" class="btn btn-primary btn-labeled btn-labeled-right"><b><i class="icon-circle-right2"></i></b> Sử dụng ảnh đã chọn</button>
        </div>
    </div>
</div>
<div id="JS-SELECTED-IMAGE-HIDDEN">

</div>
<div style="display: none">
    <div class="JS-THUMB-TEMPLATE">
        <div class="col-lg-2">
            <div class="thumbnail clone-item col-md-2 col-sm-6 media-item" data-id="MEDIA">
                <div class="thumb">
                    <img rel="data-container"
                         data-id=""
                         data-full-size-link=""
                         data-relative-link=""
                         data-thumb-size-link=""
                         data-name=""
                         data-brief=""
                         src="{{url('backend-ui/assets/images/loading.gif')}}" alt="">
                    <div data-id="MEDIAx" onclick="return MNG_MEDIA.imageSelected(jQuery(this).attr('data-id'))" class="caption-overflow">
                                        <span>
                                            <a href="{{url('/backend-ui/assets/images/placeholder.jpg')}}" data-popup="lightbox" rel="gallery"
                                               class="btn border-white text-white btn-flat btn-icon btn-rounded"><i class=" icon-eye4"></i></a>
                                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="upload-container-fake"></div>
</div>

<script type="text/javascript">
    jQuery('#pop-media-preview').find('[rel="size"]').select2({minimumResultsForSearch: -1});
    /***
     * @note: mỗi khi chọn 1 ảnh sẽ có những việc sau
     * 1: hiển thị preview ảnh
     * 2: đưa data của anh vào trong chỗ nào đó "" để save lại dùng sau này
     * @param id
     */
    MNG_MEDIA.imageSelected = function (id) {
        var $image = jQuery('#MEDIA-' + id).find('[rel="data-container"]');
        if ($image.length > 0) {
            jQuery('.img-selected').removeClass('img-selected');
            var dt = $image.data();
            var $previewContainer = jQuery('#pop-media-preview');
            $previewContainer.find('[rel="name"]').attr('value', dt.name).val(dt.name);
            $previewContainer.find('[rel="brief"]').attr('value', dt.brief);
            //$previewContainer.find('[rel="preview"]').attr('src', dt.thumbSizeLink);
            $previewContainer.find('[rel="preview"]').attr('src', dt.fullSizeLink);
            $previewContainer.find('[rel="full-link"]').attr('value', dt.fullSizeLink);
            jQuery('#MEDIA-' + id).addClass('img-selected');
            if (MNG_MEDIA.SELECTED_MULTI) {
                MNG_MEDIA.SELECTED.push(dt);
            } else {
                MNG_MEDIA.SELECTED = [];
                MNG_MEDIA.SELECTED.push(dt);
            }
        } else {
            alert("Không tìm thấy đối tượng. hoặc có lỗi trong quá trình xử lý, vui lòng liên hệ kỹ thuật để được hỗ trợ")
        }
    };
    function fancyboxInit() {
        $(document).unbind('click.fb-start');
        $('[data-popup="lightbox"]').fancybox({
            padding: 3
        });
    }
    jQuery.getScript("{{url('backend-ui/assets/js/plugins/media/fancybox.min.js')}}?v={{\App\Elibs\HtmlHelper::$clientVersion}}", function (data, textStatus, jqxhr) {
        if (jqxhr.status == 200) {
            fancyboxInit();
        }
    });
    jQuery('#noMedia').click(function () {

        jQuery('#pickfiles').trigger('click');

    });


    var MNG_MEDIA_BUTTON = {
        click: function (function_name) {
            if (function_name == undefined && MNG_MEDIA.setting.BUTTON_ACTION != '') {
                function_name = MNG_MEDIA.setting.BUTTON_ACTION;
            }
            if (MNG_MEDIA_BUTTON[function_name] != undefined) {
                eval(MNG_MEDIA_BUTTON[function_name]());
            } else {
                alert("Không tìm thấy func: " + function_name + "\nVui lòng liên hệ kỹ thuật để được hỗ trợ");
            }
        },
        /***
         * Setting image cho trường image seo trong các chức năng SEO
         * @require:
         * - Yêu cầu id của textbox chứa image phải là "
         */
        setSeoImage: function () {
            if (MNG_MEDIA.SELECTED.length > 0) {
                var link = [];
                for (var i in MNG_MEDIA.SELECTED) {
                    //console.log(MNG_MEDIA.SELECTED);
                    link.push(MNG_MEDIA.SELECTED[i]['relativeLink'])
                }
                jQuery('#seo-image').val(link.join(';'))
                jQuery(document).find('[data-dismiss="modal"]').trigger('click');
            } else {
                alert('Không có ảnh nào được chọn.' + "\nVui lòng kiểm tra lại")
            }

        }, /***
         * Insert Image to Editor
         * @require:
         */
        insertImageToEditor: function () {
            if (MNG_MEDIA.SELECTED.length > 0) {
                for (var i in MNG_MEDIA.SELECTED) {
                    _EDITOR.insertContent('&nbsp;<img src="' + MNG_MEDIA.SELECTED[i]['fullSizeLink'] + '" alt="' + MNG_MEDIA.SELECTED[i]['name'] + '" />&nbsp;');
                }
                jQuery(document).find('[data-dismiss="modal"]').trigger('click');
            } else {
                alert('Không có ảnh nào được chọn.' + "\nVui lòng kiểm tra lại")
            }

        }, /***
         * Insert Image to Editor
         * @require:
         */
        setPostImage: function () {
            if (MNG_MEDIA.SELECTED.length > 0) {
                //chỉ support 1 ảnh đại diện
                var link = [];
                var relativeLink = [];
                for (var i in MNG_MEDIA.SELECTED) {
                    link.push(MNG_MEDIA.SELECTED[i]['fullSizeLink']);
                    if (i > 0) {
                        relativeLink.push(MNG_MEDIA.SELECTED[i]['relativeLink'])
                    }
                }
                jQuery('#thumbnail').val(link.join(';'));
                jQuery('#holder').attr("src", link);
                jQuery('#obj-avatar').val(MNG_MEDIA.SELECTED[0]['relativeLink']);
                jQuery('#obj-post-image-multi').val(relativeLink.join(';'));
                jQuery('#obj-post-image-preview').html('<img src="' + MNG_MEDIA.SELECTED[0]['fullSizeLink'] + '"/>');
                jQuery(document).find('[data-dismiss="modal"]').trigger('click');
            } else {
                alert('Không có ảnh nào được chọn.' + "\nVui lòng kiểm tra lại")
            }

        }
    }

    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',

        browse_button: 'pickfiles', // you can pass in id...
        container: document.getElementById('upload-container-fake'),
        url: '{{admin_link('media/_doUpload')}}',
        filters: {
            max_file_size: '10mb',
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png"},
            ]
        },
        multipart_params: {
            "_token": "{{csrf_token()}}"
        },
        flash_swf_url: 'plupload/js/Moxie.swf',
        init: {
            PostInit: function () {
            },

            FilesAdded: function (up, files) {
                uploader.start();
            },


            FileUploaded: function (up, file, response) {
                uploader.removeFile(file);
                var imageUrl = response.response;
                var response = JSON.parse(response.response);
                console.log(response);
                console.log(response.data.id);
                if (imageUrl) {
                    var thumb = $(".media-item")[0];
                    thumb = $(thumb).clone();
                    $(thumb).addClass('clone-x');
                    $(thumb).find(".thumb img").attr("data-id", response.data.id);
                    $(thumb).find(".thumbnail").attr("id", "MEDIA-" + response.data.id);
                    $(thumb).find(".thumb img").attr("data-full-size-link", response.data.full_size_link);
                    $(thumb).find(".thumb img").attr("data-thumb-size-link", response.data.thumb_size_link);
                    $(thumb).find(".thumb img").attr("src", response.data.full_size_link);
                    $(thumb).find(".thumb div").attr("onclick", "return MNG_MEDIA.imageSelected('" + response.data.id + "')");
                    $("#MEDIA-CONTAINER").prepend(thumb);
                }
            },
            UploadComplete: function (up, files) {

            }
        }
    });
    uploader.init();
    uploader.bind('FilesAdded', function (up, files) {
        $.each(files, function () {

            var img = new mOxie.Image();

            img.onload = function () {
                this.embed($('#preview').get(0), {
                    width: 100,
                    height: 100,
                    crop: true
                });
            };

            img.onembedded = function () {
                this.destroy();
            };

            img.onerror = function () {
                this.destroy();
            };

            img.load(this.getSource());

        });
    });


</script>
