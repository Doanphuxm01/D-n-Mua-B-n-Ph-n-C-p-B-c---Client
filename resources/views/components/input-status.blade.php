<div class="form-group">

    <label class="control-label">Trạng thái: </label>
    <div>

        <select name="obj[status]" placeholder="Lựa chọn trạng thái"
                class="form-control">
            @if(isset($allProject) && $allProject)
                @foreach([
                \App\Http\Models\BaseModel::STATUS_ACTIVE=>"Hoạt động",
                \App\Http\Models\BaseModel::STATUS_DRATF =>"Nháp",
                ] as $key=>$val)
                    <option value="{{$key}}"
                            @if(isset($obj['status']) && $obj['status'] ==$key) selected @endif>{{$val}}</option>
                @endforeach
            @endif

        </select>
    </div>

</div>
