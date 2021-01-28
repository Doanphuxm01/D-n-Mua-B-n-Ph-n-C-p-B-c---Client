@if(!isset($preview))
    @php($preview = false)
@endif
<div class="form-group {{@$group['class']}}">
    <label>{{@$field['label']}} @if(@$note['label'])
            <small class="form-text {{$note['class']}}">{{$note['label']}}</small>
        @endif</label>
    @if(!$preview)
        @if(is_array(@$obj[$field['key']]))
            <input
                    @if(@$field['name'])
                    name="{{@$field['name']}}"
                    @else name="obj[{{$field['key']}}]" @endif

        {{@$field['disabled']}}
            type="{{@$field['type']?@$field['type']:'text'}}"
                    class="form-control {{@$field['class']}}" id="obj-{{$field['key']}}"
                    value=""
                    placeholder="{{@$placeholder}}">

        @else

            <input   @if(@$field['name'])
                     name="{{@$field['name']}}"
                     @else name="obj[{{$field['key']}}]" @endif
                     type="{{@$field['type']?@$field['type']:'text'}}"

                   {{@$field['disabled']}}
                   class="form-control {{@$field['class']}}" id="obj-{{@$field['id_prefix']}}{{$field['key']}}"
                   value="{{isset($field['value']) ?$field['value']:@$obj[$field['key']]}}"
                   placeholder="{{@$placeholder}}">
        @endif

    @else
        <div class="text-primary"  @if(@$field['name'])
        name="{{@$field['name']}}"
             @else name="obj[{{$field['key']}}]" @endif>
            @if(isset($field['value_preview']))
                {!! $field['value_preview'] !!}
            @else
                {{isset($field['value'])?value_show($field['value'],'Chưa cập nhật'):value_show(@$obj[$field['key']],'Chưa cập nhật')}}
            @endif
        </div>

    @endif
</div>