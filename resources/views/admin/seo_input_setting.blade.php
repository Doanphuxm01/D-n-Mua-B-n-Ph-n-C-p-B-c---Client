<?php
/**
 * Created by PhpStorm.
 * User: Sakura
 * Date: 4/29/2016
 * Time: 2:09 PM
 */
/*
 * mảng mẫu
    $seo['TITLE'] = "Tiêu đề bài viết ở đâd";
    $seo['DES'] = "Mô tả cho bài viết ở đây";
    $seo['ROBOTS'] = "Mô tả cho bài viết ở đây";
    $seo['IMAGE'] = "Mô tả cho bài viết ở đây";
    $seo['KEYWORD'] = "Mô tả cho bài viết ở đây";
$tpl['SEO_INPUT_SETTING'] = $seo;
*/
?>
<style type="text/css">
    [contenteditable=true]:empty:before {
        content: attr(placeholder);
        display: block; /* For Firefox */
    }

    div[contenteditable=true] {
        border: 1px dashed #AAA;
        display: inline-block;
        border-radius: 5px;
        padding: 0 5px;
    }

    .seo-region {
        color: #333;
    }

    .seo-region .seo-title {
        font-size: 18px;
        color: #0000d0;
    }

    .seo-region .seo-link {
        color: #009613;
    }

    ::-webkit-input-placeholder { /* Chrome/Opera/Safari */
        color: pink;
    }

    ::-moz-placeholder { /* Firefox 19+ */
        color: pink;
    }

    :-ms-input-placeholder { /* IE 10+ */
        color: pink;
    }

    :-moz-placeholder { /* Firefox 18- */
        color: pink;
    }

    .seo-region {
    }
</style>
<div class="alert alert-primary alert-styled-left alert-arrow-left alert-component">
    <div class="seo-region">
        <div style="margin-right: 15px;font-style: italic"> Tiêu đề (<i id="seo-title-length">0</i>/70)</div>
        <div placeholder="Tiêu đề seo ở đây, nên có từ khóa" class="seo-title" id="seo-title" contenteditable="true">{!! isset($SEO_INPUT_SETTING['TITLE']) && $SEO_INPUT_SETTING['TITLE'] ?$SEO_INPUT_SETTING['TITLE']:'' !!}</div>
        <input type="hidden" name="SEO[TITLE]" value="{{isset($SEO_INPUT_SETTING['TITLE'])?$SEO_INPUT_SETTING['TITLE']:'' }}" id="hidden-seo-title"/>
        <div style="clear: both;"></div>
        <div style="margin-right: 15px;font-style: italic">Mô tả: (<i id="seo-des-length">0</i>/160)</div>
        <div placeholder="Mô tả ngắn nhập ở đây, nên có chứa từ khóa" class="seo-des" id="seo-des" contenteditable="true">{!! isset($SEO_INPUT_SETTING['DES'])&& $SEO_INPUT_SETTING['DES']?$SEO_INPUT_SETTING['DES']:'' !!}</div>
        <input type="hidden" name="SEO[DES]" value="{{isset($SEO_INPUT_SETTING['DES'])?$SEO_INPUT_SETTING['DES']:'' }}" id="hidden-seo-des"/>
        <div style="clear: both;"></div>
        <div class="seo-link" id="seo-link">{{url('link-demo-tren-google-hien-thi-nhu-the-nay.html')}}</div>

    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-2">Từ khóa khác</label>
    <div class="col-md-10">
        <input name="SEO[KEYWORD]" value="{{isset($SEO_INPUT_SETTING['KEYWORD'])?$SEO_INPUT_SETTING['KEYWORD']:'' }}" type="text" class="form-control" placeholder="Thời trang nữ, thời trang nữ đẹp, đồ nữ đẹp">
    </div>
</div>
<div class="form-group">
    <label class="control-label col-md-2">Hình ảnh</label>
    <div class="col-md-10">
        <div class="input-group">
            <input id="seo-image" name="SEO[IMAGE]" type="text" value="{{isset($SEO_INPUT_SETTING['IMAGE'])?$SEO_INPUT_SETTING['IMAGE']:'' }}" class="form-control" placeholder="Nhập link ảnh hoặc chọn hình ảnh từ thư viện">
            <a href="javascript:void(0)" onclick="return MNG_MEDIA.openUploadForm('setSeoImage',jQuery('#seo-image').val())" title="Click để chọn hình ảnh" class="input-group-addon"><i class=" icon-image4"></i></a>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-2">Robots</label>
    <div class="col-md-10">
        <select class="select-search" name="SEO[ROBOTS]" id="seo-robots">
            <option @if(isset($SEO_INPUT_SETTING['ROBOTS']) && $SEO_INPUT_SETTING['ROBOTS']=='INDEX,FOLLOW') selected="selected" @endif value="INDEX,FOLLOW">INDEX,FOLLOW</option>
            <option @if(isset($SEO_INPUT_SETTING['ROBOTS']) && $SEO_INPUT_SETTING['ROBOTS']=='INDEX,NOFOLLOW') selected="selected" @endif  value="INDEX,NOFOLLOW">INDEX,NOFOLLOW</option>
            <option @if(isset($SEO_INPUT_SETTING['ROBOTS']) && $SEO_INPUT_SETTING['ROBOTS']=='NOINDEX,NOFOLLOW') selected="selected" @endif value="NOINDEX,NOFOLLOW">NOINDEX,NOFOLLOW</option>
            <option @if(isset($SEO_INPUT_SETTING['ROBOTS']) && $SEO_INPUT_SETTING['ROBOTS']=='NOINDEX,FOLLOW') selected="selected" @endif value="NOINDEX,FOLLOW">NOINDEX,FOLLOW</option>
        </select>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-md-2">Thẻ H1
        <small>(nếu có)</small>
    </label>
    <div class="col-md-10">
        <input name="SEO[H1]" value="{{isset($SEO_INPUT_SETTING['H1'])?$SEO_INPUT_SETTING['H1']:'' }}" type="text" class="form-control" placeholder="Nội dung thẻ H1: ví dụ Thời trang nữ">
    </div>
</div>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function (event) {
        document.getElementById('seo-title-length').innerHTML = document.getElementById('hidden-seo-title').value.length;
        document.getElementById('seo-des-length').innerHTML = document.getElementById('hidden-seo-des').value.length;
    });

    function _contentEditAbleInit(editElement, hiddenElement, countElement) {
        document.getElementById(editElement).addEventListener("input", function () {
            document.getElementById(hiddenElement).value = document.getElementById(editElement).innerHTML.trim();
            document.getElementById(countElement).innerHTML = document.getElementById(hiddenElement).value.length;
        }, false);
    }

    _contentEditAbleInit('seo-title', 'hidden-seo-title', 'seo-title-length');
    _contentEditAbleInit('seo-des', 'hidden-seo-des', 'seo-des-length');
</script>
