<div class="sidebar sidebar-secondary sidebar-default">
    <div class="sidebar-content">
        <div class="tab-content">

            <!-- Sidebar tabs -->
            <div class="tab-pane no-padding active" style="width: 240px" id="forms-tab">
                <form id="form-filter" action="">
                    <!-- Sidebar search -->
                    <div class="sidebar-category">
                        <div class="category-title">
                            <span>Tìm kiếm</span>
                            <ul class="icons-list">
                                <li><a href="#" data-action="collapse"></a></li>
                            </ul>
                        </div>

                        <div class="category-content">
                            <div class="has-feedback has-feedback-left">
                                <input name="q" type="search" value="{{app('request')->input('q')}}"
                                       class="form-control" placeholder="Từ khoá,...">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-size-base text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /sidebar search -->


                    <!-- Right checkbox group -->
                    <div class="sidebar-category">
                        <div class="category-title">
                            <span>Danh mục</span>
                            <ul class="icons-list">
                                <li><a href="#" data-action="collapse"></a></li>
                            </ul>
                        </div>

                        <div class="category-content @if(!app('request')->input('q_cate')) collapse @endif">
                            @if(isset($allCateNews['items']) && $allCateNews['items'])
                                @foreach($allCateNews['items'] as $item)
                                    <div class="form-group">
                                        <div class="checkbox checkbox-right">
                                            <label onclick="$('#form-filter').submit()">
                                                <input type="checkbox" class="styled" name="q_cate[]"
                                                       value="{{$item['alias']}}"
                                                       @if(is_array(app('request')->input('q_cate')) &&in_array($item['alias'],app('request')->input('q_cate') )) checked="checked" @endif

                                                >
                                                {{$item['name']}}
                                            </label>
                                        </div>

                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <!-- /right checkbox group -->
                    <div class="sidebar-category">
                        <div class="category-title">
                            <span>Trạng thái</span>
                            <ul class="icons-list">
                                <li><a href="#" data-action="collapse"></a></li>
                            </ul>
                        </div>
                        <div class="category-content ">
                            <select name="q_status" id="" class="form-control">
                                <option value="0">Tất cả trạng thái</option>
                                <option value="{{\App\Http\Models\Post::STATUS_ACTIVE}}"
                                        @if(isset($q_status) && $q_status==\App\Http\Models\Post::STATUS_ACTIVE) selected @endif>
                                    Hiển thị
                                </option>
                                <option value="{{\App\Http\Models\Post::STATUS_DRATF}}"
                                        @if(isset($q_status) && $q_status==\App\Http\Models\Post::STATUS_DRATF) selected @endif>
                                    Lưu nháp
                                </option>
                                <option value="{{\App\Http\Models\Post::STATUS_DELETED}}"
                                        @if(isset($q_status) && $q_status==\App\Http\Models\Post::STATUS_DELETED) selected @endif>
                                    Xóa
                                </option>

                            </select>
                        </div>
                    </div>
                    <div class="sidebar-category">
                        <div class="category-content text-center">
                            <div class="row">
                                <div class="col-xs-6">
                                    <a href="/news" onclick="return confirm('Bạn muốn reset bộ lọc?')">
                                        <button type="button" class="btn btn-danger btn-block">Reset</button>
                                    </a>
                                </div>
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-info btn-block">Lọc</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

        </div>


    </div>
</div>
