<div class="form-group">
    <label class="control-label">Chuyên đề: </label>
    <div>
        <select name="obj[topic_id]" placeholder="Lựa chọn trạng thái"
                class="form-control">
            <option value="">Lựa chọn chuyên đề</option>
            @if(isset($allTopic))
                <?php
                $selected_topic_id = app('request')->input('topic_id', '');
                if (isset($obj)) {
                    $selected_topic_id = $obj['topic_id'];
                }
                ?>
                @foreach($allTopic as $item)
                    <option value="{{$item->_id}}"
                            @if($selected_topic_id && $selected_topic_id == strval($item->id)) selected @endif>{{$item->name}}</option>
                @endforeach
            @endif

        </select>
    </div>

</div>
