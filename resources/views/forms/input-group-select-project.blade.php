@if(!isset($no_label))
    <div class="form-group {{@$group['class']}}">
        <label>{{@$field['label']}}</label>
        @endif
        @if(!$preview)
            <select id="obj_{{value_show(@$field['key'],'projects')}}" @if(isset($field['name'])) name="{{$field['name']}}" @else name="obj[{{value_show(@$field['key'],'projects')}}][]" @endif class="form-control select2-multiple"
                    @if(isset($field['disabled']) && $field['disabled']) disabled @endif @if(isset($field['multiple']) && $field['multiple'])  multiple="{{$field['multiple']}}" @endif
                    data-toggle="select2">
                <option value="">Chọn dự án</option>
                @foreach(all_project() as $key=>$value)
                    <option
                            @if(isset($obj['projects']) && $obj['projects'])
                                @foreach($obj['projects'] as $ks=>$vs)
                                    @if(@$vs['id']==$value['_id'])
                                        selected
                                    @endif
                                @endforeach
                            @endif
                            value="{{$value['_id']}}">{{$value['name']}}</option>
                @endforeach
            </select>
        @else
            <div class="text-primary">
                <?php
                $_haveProject = 0;
                ?>
                @foreach(all_project() as $key=>$value)
                        @if(isset($obj['projects']) && $obj['projects'])
                            @foreach($obj['projects'] as $ks=>$vs)
                                @if(@$vs['id']==$value['_id'])
                                    <?php $_haveProject = 1; ?>
                                    {{$value['name']}}
                                @endif
                            @endforeach
                        @endif
                @endforeach
                @if(!$_haveProject)
                    Chưa cập nhật
                @endif
            </div>
        @endif

        @if(!isset($no_label))
    </div>
@endif

{{--
@include('forms/input-group-select-project',['field'=>['label'=>'Chọn dự án:','key'=>'project_id','value'=>@$obj['project_id'],'multiple'=>false]])
--}}
