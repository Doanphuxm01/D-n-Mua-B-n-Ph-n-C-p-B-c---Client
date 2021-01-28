@if(!isset($preview))
    @php($preview = false)
@endif
<div class="form-group {{@$group['class']}}">

<label for="obj-description">{{$field['label']}}</label>
    @if(!$preview)
        <textarea style="min-height: {{value_show(@$field['height'],68)}}px"
                  @if(!@$field['name'])
                  name="obj[{{$field['key']}}]"
                  @else
                          name="{{$field['name']}}"
                  @endif
                  class="form-control {{@$field['class']}}" id="obj-{{@$field['id_prefix']}}{{$field['key']}}"
                  {{-- placeholder="{{@$placeholder}}">{{@$obj[$field['key']]}}</textarea> --}}
                  placeholder="{{@$placeholder}}">{{(@$obj[$field['key']]) ? @$obj[$field['key']] :  @$field['textarea']}}</textarea>
        @if(@$field['intro'])
            <small class="form-text text-muted">{{$field['intro']}}</small>
        @endif
    @else
        <div class="text-primary" style="white-space: pre-line;">{!! isset($field['value'])?value_show(\App\Elibs\Helper::joinAreaContent(trim($field['value']),'<br/>'),'Chưa cập nhật nội dung'):value_show(@$obj[$field['key']],'Chưa cập nhật') !!}</div>
    @endif
</div>