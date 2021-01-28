<div class="modal-dialog modal-large">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">
                Phân quyền cho nhân viên
            </h3>
        </div>
        <div class="modal-body pd-10">
            <form name="postInputFormModal"
                  onsubmit="return MNG_POST.update('{{admin_link('project/save_member_role')}}','#postInputFormModal');"
                  id="postInputFormModal" class="form-horizontal" method="post">
                <input name="id" id="obj-id" value="{{isset($obj['_id'])?$obj['_id']:''}}" type="hidden">
                <div class="form-group">
                    <label class="control-label col-md-3">Nhân viên</label>
                    <div class="col-md-9">
                        @if(isset($curAccount))
                            <input class="form-control" name="obj[account_id]" type="hidden"
                                   value="{{$curAccount['_id']}}"/>
                            <input class="form-control" name="" type="text"
                                   value="{{@$curAccount['name']}} ({{@$curAccount['account']}})" readonly/>
                        @endif


                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-3">Dự án</label>
                    <div class="col-md-9">
                        @if(isset($curProject))
                            <input class="form-control" name="obj[project_id]" type="hidden"
                                   value="{{$curProject['_id']}}"/>
                            <input class="form-control" name="" type="text" value="{{@$curProject['name']}}" readonly/>
                        @endif

                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-md-3">Phân nhóm quyền</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <select
                                    name="obj[role_group]"
                                    class="select-search-role-group">
                                <option value="">Chưa lựa chọn</option>
                                @if(isset($allRoleGroup))
                                    @foreach($allRoleGroup as $item)
                                        <option
                                                @if(isset($obj['role_group'])  && $obj['role_group'] ==$item['key'])
                                                checked
                                                @endif
                                                value="{{$item['key']}}">{{$item['name']}}</option>
                                    @endforeach
                                @endif

                            </select>
                            <a href="{{admin_link('roles')}}" target="_blank" class="input-group-addon"><i
                                        class="icon-info3"></i></a

                        </div>


                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button"
                    onclick="return MNG_POST.update('{{admin_link('project/save_member_role')}}','#postInputFormModal');"
                    class="btn btn-primary">Ghi lại
            </button>
        </div>
    </div>
</div>
<script>
    $('.select-search-role-group').select2({})
</script>
