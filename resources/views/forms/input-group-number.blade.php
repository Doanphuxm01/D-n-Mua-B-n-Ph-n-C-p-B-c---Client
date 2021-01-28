<div class="form-group {{@$group['class']}}">

    <label>{{@$field['label']}}</label>
    @if(!$preview)
        <input @if(@$field['name'])
               name="{{@$field['name']}}"
               @else name="obj[{{$field['key']}}]" @endif
               type="text"
               {{@$field['disabled']}}
               {{@$field['readonly']}}
               class="form-control autonumber {{@$field['class']}}" id="obj-{{@$field['id_prefix']}}{{$field['key']}}"
               value="{{isset($field['value']) ?
                 \App\Elibs\Helper::numberFormat(@$field['value'],value_show(@$data_sep,','))
                 :value_show(\App\Elibs\Helper::numberFormat(@$obj[$field['key']],value_show(@$data_sep,',')))}}"
               data-a-sep="{{value_show(@$data_sep,',')}}" data-a-sign="{{value_show(@$data_prefix,'')}}"
               placeholder="{{@$placeholder}}">

    @else
        <div class="text-primary" @if(@$field['name'])
        name="{{@$field['name']}}"
             @else name="obj[{{$field['key']}}]" @endif>
            @if(isset($field['value_preview']))
                {!! $field['value_preview'] !!}
            @else
                {{isset($field['value'])?value_show(\App\Elibs\Helper::numberFormat(@$field['value'],value_show(@$data_sep,',')),'Chưa cập nhật'):value_show(\App\Elibs\Helper::numberFormat(@$obj[$field['key']],value_show(@$data_sep,',')),'Chưa cập nhật')}}
            @endif
        </div>

    @endif
</div>