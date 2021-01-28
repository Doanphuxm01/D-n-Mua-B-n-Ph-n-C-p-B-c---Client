@if(!isset($preview))
        @php($preview = false)
@endif
<div class="form-group {{@$group['class']}}">
        <label
        @if(@$field['label-title'])
                title="{{@$field['label-title']}}"
                @endif
        >{{$field['label']}}</label>
    @if(!$preview)
        <select
                @if(@$field['data-toggle'])
                {{@$field['data-toggle']}}
                @else
                data-toggle="select2"
                @endif
                {{@$field['multiple']}}
                @if(@$field['name'])
                @if(@$field['multiple'])
                name="{{@$field['name'].'[id][]'}}"
                @else
                name="{{@$field['name'].'[id]'}}"
                @endif
                @else name="obj[{{$field['key']}}][id]" @endif
                @if(@$field['disabled'])
                {{@$field['disabled']}}
                @endif
                class="form-control">

            <option value="">Chưa lựa chọn</option>
            @if(isset($data) && is_array($data))
                @if(!isset($field['value']))
                    @php($field['value']= @$obj[$field['key']])
                @endif
                @foreach($data as $item)
                        @php($tempId= @$item['_id']?:@$item['id'] )
                    <option
                            @if(@$item['id']  &&(@$field['value']['id']  == $tempId))
                            selected
                            @elseif($tempId && (@$field['value']['id']  == $tempId))
                            selected
                            @elseif($tempId == @$field['value'])
                            selected
                            @endif
                            value="{{$tempId}}">{{@$item['name']}}</option>
                @endforeach
            @endif
        </select>

    @else
        <div class="text-primary" @if(@$field['name'])
        name="{{@$field['name']}}"
             @else name="obj[{{$field['key']}}][id]" @endif
        >
            @if(isset($field['value_preview']))
                {!! $field['value_preview'] !!}
            @else
                {{isset($field['value'])?value_show($field['value'],'Chưa cập nhật'):value_show(@$obj[$field['key']],'Chưa cập nhật')}}
            @endif
        </div>

    @endif
</div>