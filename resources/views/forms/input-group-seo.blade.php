<div class="card">
    <div class="card-body">
        <h4 class="mb-3 header-title">Thông tin seo</h4>

        @include('forms/input-group-text',['placeholder'=>'','field'=>['label'=>'Tiêu đề','key'=>'seo_title']])
        @include('forms/input-group-text',['placeholder'=>'','field'=>['label'=>'Mô tả','key'=>'seo_description']])
        @include('forms/input-group-text',['placeholder'=>'','field'=>['label'=>'Giá trị thẻ H1','key'=>'seo_h1']])

        <div class="form-group ">
            <label>Seo robot</label>
            @if(!$preview)
                <select name="obj[seo_robot]" class="form-control" data-toggle="select2">
                    <option @if(@$obj['seo_robot']=='index,follow') selected @endif value="index,follow">Index, Follow</option>
                    <option @if(@$obj['seo_robot']=='noindex,follow') selected @endif value="noindex,follow">No Index, Follow</option>
                    <option @if(@$obj['seo_robot']=='noindex,nofollow') selected @endif value="noindex,nofollow">No Index, No Follow</option>
                </select>

            @else
                <div class="text-primary">
                    {{value_show(@$obj['seo_robot'],'Chưa cập nhật')}}
                </div>

            @endif
        </div>
        @include('forms/input-group-files',['containerKey'=>'containerKey_input-form-seo','field'=>['label'=>'Đính kèm hình ảnh','key'=>'seo_images']])

    </div>
</div>