@if(isset($field['field_label']))
    <div>
        <div class="form-group" data-id="filter-elem">
            <label data-id="filter-elem-check">
                <input name="qObj[{{$field['field_key']}}][query_check]"
                       data-id="filter-elem-checkbox"
                       value="1"
                       @if( isset($qObj[$field['field_key']]['query_check'])
                         && $qObj[$field['field_key']]['query_check'])
                       checked
                       @endif
                       type="checkbox" class="styled"/>
                {{$field['field_label']}}
            </label>
            <div data-id="filter-elem-box"
                 @if( isset($qObj[$field['field_key']]['query_check'])
                         && $qObj[$field['field_key']]['query_check'])
                 class=""
                 @else
                 class="hide"
                    @endif
            >
                @if(isset($field['type']))
                    @if($field['type'] == 'select')
                        <select
                                data-id="filter-elem-box-query"
                                class="select-search select-xs" name="qObj[{{$field['field_key']}}][query]">
                            @foreach( [
                                [ 'text'=>"Lựa chọn...",
                                  'id'=>""],
                                [ 'text'=>"Không có dữ liệu",
                                  'id'=>"not_exist"],
                                [ 'text'=>"Có dữ liệu",
                                  'id'=>"exist"],
                                [ 'text'=>"Có chứa",
                                  'id'=>"in"],
                                [ 'text'=>"Không chứa",
                                  'id'=>"not_in"],

                            ] as $item)
                                <option
                                        @if(
                                            isset($qObj[$field['field_key']]['query'])
                                            &&$qObj[$field['field_key']]['query'] == $item['id'])
                                        selected
                                        @endif
                                        value="{{$item['id']}}">{{$item['text']}}
                                </option>

                            @endforeach

                        </select>
                        @if(isset($field['metadata_type']))
                            @if(isset($qObj[$field['field_key']]['query_value']))
                                @php
                                    $temp = explode(',',$qObj[$field['field_key']]['query_value'] );
                                    if(!empty($temp)){
                                    $selectedChoice = \App\Http\Models\MetaData::whereIn('_id', $temp)->get();
                                    $selectedChoice = collect($selectedChoice->toArray())->map(function ($item){
                                        return [
                                            'id'=> strval(@$item['_id']),
                                            'name'=> strval(@$item['name']),
                                        ];
                                    })->toArray();
                                    }else{
                                    $selectedChoice =[];
                                    }

                                @endphp

                            @endif
                            <input
                                    data-id="filter-elem-box-value"
                                    @if(isset($field['metadata_type']))
                                    data-selectSource="{{admin_link('staff/metastaff?q_type='.$field['metadata_type'])}}"
                                    data-qType="{{$field['metadata_type']}}"
                                    @endif
                                    @if(isset($qObj[$field['field_key']]['query_value']))
                                    value="{{$qObj[$field['field_key']]['query_value']}}"
                                    @endif
                                    class="select-xs mt-1"
                                    name="qObj[{{$field['field_key']}}][query_value]"/>
                        @else
                            <select
                                    data-id="filter-elem-box-value"
                                    @if(isset($field['metadata_type']))
                                    data-selectSource="{{admin_link('staff')}}"
                                    data-qType="{{$field['metadata_type']}}"
                                    @endif
                                    multiple="true"
                                    class="select-search select-xs mt-1"
                                    name="qObj[{{$field['field_key']}}][query_value][]">
                                @if(isset($field['options']) && is_array($field['options']))
                                    @foreach($field['options'] as $item)
                                        <option
                                                @if(isset($qObj[$field['field_key']]['query_value'])
                                                    && in_array($item['id'],$qObj[$field['field_key']]['query_value'])
                                                )
                                                selected
                                                @endif
                                                value="{{$item['id']}}">{{$item['text']}}</option>

                                        </option>
                                    @endforeach

                                @endif
                            </select>


                        @endif

                    @endif
                    @if($field['type'] == 'text')
                        <select data-id="filter-elem-box-query"
                                class="select-search select-xs" name="qObj[{{$field['field_key']}}][query]">
                            @foreach( [
                                   [ 'text'=>"Lựa chọn...",
                                     'id'=>""],
                                   [ 'text'=>"Không có dữ liệu",
                                     'id'=>"not_exist"],
                                   [ 'text'=>"Có dữ liệu",
                                     'id'=>"exist"],
                                   [ 'text'=>"Giống",
                                     'id'=>"like"],

                               ] as $item)
                                <option
                                        @if(
                                            isset($qObj[$field['field_key']]['query'])
                                            &&$qObj[$field['field_key']]['query'] == $item['id'])
                                        selected
                                        @endif
                                        value="{{$item['id']}}">{{$item['text']}}
                                </option>

                            @endforeach
                        </select>
                        <input
                                data-id="filter-elem-box-value"
                                class="form-control mt-1" type="text"
                                @isset($qObj[$field['field_key']]['query_value']) value="{{$qObj[$field['field_key']]['query_value']}}"
                                @endisset
                                name="qObj[{{$field['field_key']}}][query_value]">

                    @endif
                    @if($field['type'] == 'date')
                        <select data-id="filter-elem-box-query"
                                class="select-search select-xs" name="qObj[{{$field['field_key']}}][query]">
                            @foreach( [
                                   [ 'text'=>"Trong khoảng",
                                     'id'=>"date-range"],

                               ] as $item)
                                <option
                                        @if(
                                            isset($qObj[$field['field_key']]['query'])
                                            &&$qObj[$field['field_key']]['query'] == $item['id'])
                                        selected
                                        @endif
                                        value="{{$item['id']}}">{{$item['text']}}
                                </option>

                            @endforeach
                        </select>
                        <input
                                data-id="filter-elem-box-value"
                                @isset($qObj[$field['field_key']]['query_value']) value="{{$qObj[$field['field_key']]['query_value']}}"
                                @endisset
                                class="form-control mt-1  daterange-basic-customer" type="text"
                                name="qObj[{{$field['field_key']}}][query_value]">
                    @endif
                @endif
            </div>

        </div>
    </div>
@endif
