@if(!$preview)
    <select @if(isset($field['name'])) name="{{$field['name']}}" @else name="members[{{$field['key']}}][]" @endif class="form-control select2-multiple"
            @if(isset($field['disabled']) && $field['disabled']) disabled @endif @if(isset($field['multiple']) && $field['multiple'])  multiple="{{$field['multiple']}}" @endif
            data-toggle="select2-with-title"
            data-placeholder="{{$field['label']}}...">
        <option value="">Chọn {{$field['label']}}</option>
        {!! @$_lsMemberSelectOption['select_option_html'][$field['key']] !!}
    </select>
@else
    <div class="text-primary">
        {!! value_show(@implode(',',@$_lsMemberSelectOption['data'][$field['key']]),'---') !!}
    </div>
@endif