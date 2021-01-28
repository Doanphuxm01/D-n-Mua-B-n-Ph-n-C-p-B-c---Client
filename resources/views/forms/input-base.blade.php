@if(@$field['type'] == 'text')
    @if(@$field['textarea'])
        @include('forms/input-group-textarea',['group'=>['class'=>@$field['groupClass']],'placeholder'=>@$field['label'].'...','field'=>$field])
    @else
        @include('forms/input-group-text',['group'=>['class'=>@$field['groupClass']],'placeholder'=>@$field['label'].'...','field'=>$field])
    @endif
@elseif(@$field['type'] =='select')
    @if($field['dataSource'] !=='table')
        @if(@$field['dataOptions'])
            @include('forms/input-group-select',[
               'data'=>@$field['dataOptions'],
              'group'=>['class'=>@$field['groupClass']],'placeholder'=>@$field['label'].'...','field'=>$field])
        @else
            @include('forms/input-group-select',[
         'data'=>\App\Http\Models\Contract::getMetaDataSelect($field, $metadata),
        'group'=>['class'=>@$field['groupClass']],'placeholder'=>@$field['label'].'...','field'=>$field])
        @endif
    @else ($field['dataSource'] ==='table')
        @include('forms/input-group-select',[
        'data'=> \App\Http\Models\Contract::getTableDataSelect($field, @$tableData),
        'group'=>['class'=>@$field['groupClass']],'placeholder'=>@$field['label'].'...','field'=>$field])
    @endif
@elseif(@$field['type'] =='number')
    @include('forms/input-group-number',['group'=>['class'=>@$field['groupClass']],'placeholder'=>@$field['label'].'...','field'=>$field])
@elseif(@$field['type'] =='date')
    @include('forms/input-group-text',['group'=>['class'=>@$field['groupClass']],'field'=>[
    'name'=>$field['name'],
    'disabled'=>@$field['disabled'],
    'label'=>@$field['label'],'key'=>$field['key'],'class'=>'date-datepicker',
    'value'=>\App\Elibs\Helper::showMongoDate($field['value'])]])
@endif