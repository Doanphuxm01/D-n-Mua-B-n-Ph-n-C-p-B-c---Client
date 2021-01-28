@if(!$preview)
    <textarea style="min-height: {{value_show(@$field['height'],68)}}px" name="obj[{{$field['key']}}]" class="form-control {{@$field['class']}} tinymce_editor" id="obj-{{@$field['id_prefix']}}{{$field['key']}}"
              placeholder="{{@$placeholder}}">{{@$obj[$field['key']]}}</textarea>
    @if(@$field['intro'])
        <small class="form-text text-muted">{{$field['intro']}}</small>
    @endif
@else
    <div class="text-primary" style="white-space: pre-line;">{!! value_show(@$obj[$field['key']],'Chưa cập nhật') !!}</div>
@endif