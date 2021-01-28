<div class="form-group">

    <label class="control-label ">Phòng ban</label>
    <div>

        <select id="obj-departments" name="obj[departments][]" data-placeholder="Chọn phòng ban..." multiple="multiple"
                class="select select-xs select-multi-small">
            @if(isset($allDepartment) && $allDepartment)
                @php
                    $dep = collect(@$obj['departments'])->map(function($item){return $item['id'];})->all();
                @endphp
                @foreach(collect($allDepartment)->filter(function ($item){
                    if(!isset($item['department_type'])){
                    return true;
                    }else {
                        if($item['department_type'] === \App\Http\Models\MetaData::DEPARTMENT_LEVEL['level_1']['id']){
                            return true;
                        }

                        if($item['department_type'] === \App\Http\Models\MetaData::DEPARTMENT_LEVEL['level_2']['id']){
                            return false;
                        }
                    }
                }) as $item)
                    <option @if(isset($obj['departments'])  && is_array($dep) && in_array($item['id'],$dep)) selected
                            @endif  value="{{$item['id']}}">{{$item['name']}}</option>

                    @foreach(collect($allDepartment)->filter(function ($child)use($item){
                        return isset($child['parent_dep']['id']) && $child['parent_dep']['id']== $item['id'];
                    }) as $child)
                        <option @if(isset($obj['departments'])  && is_array($dep) && in_array($child['id'],$dep)) selected
                                @endif  value="{{$child['id']}}"> &nbsp;&nbsp; {{$child['name']}}({{ $item['name']}})</option>


                    @endforeach

                @endforeach
            @endif
        </select>

        <script>
            $('#obj-departments').select2()
        </script>
    </div>

</div>
