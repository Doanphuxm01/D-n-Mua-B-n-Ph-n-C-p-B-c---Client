<div class="form-group">

    <label class="control-label">Dự án: </label>
    <div>
        <select id="obj-projects" name="obj[projects][]" data-placeholder="Chọn dự án..." multiple="multiple"
                class="select select-xs select-multi-small">
            @if(isset($allProject) && $allProject)
                <?php
                    if(isset($obj['projects']) && $obj['projects']){
                    $lsProjectId = array_column($obj['projects'],'id');
                    }else{
                        $lsProjectId = [];
                    }
                ?>
                @foreach($allProject as $item)
                    <option @if(isset($obj['projects'])  && is_array($obj['projects']) && in_array($item['_id'],$lsProjectId)) selected
                            @endif  value="{{$item['_id']}}">{{$item['name']}}</option>
                @endforeach
            @endif

        </select>
    </div>
    <script>
        $('#obj-projects').select2()
    </script>
</div>
