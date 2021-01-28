@if(@$field ['type']== 'img')
    <div style="width: 100%; height: 150px;display: block;text-align: center">
        <img src="#" alt="Ảnh thẻ" style="border: 1px dashed #0a001f; width: 150px; height: 100%;display: inline-block">
    </div>
@elseif(@$field ['type']== 'select')
    @include('views.components.fields.select', ['field'=> $field])
@elseif(@$field ['type']== 'date')
    @include('views.components.fields.date', ['field'=> $field])

@elseif(@$field ['type']== 'multi')
    @include('views.components.fields.multi', ['field'=> $field])

@elseif(@$field ['type']== 'table')
    @include('views.components.fields.table', ['field'=> $field])

@else
    @include('views.components.fields.default-input', ['field'=> $field])

@endif
