<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header bg-brand">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Phân quyền dự án</h3>
        </div>
        <div class="modal-body no-padding" style="background: #e8e8e8">
            <form name="postInputFormModal" onsubmit="return MNG_POST.update('{{admin_link('project/permission-save',TRUE)}}','#postInputFormModal');" id="postInputFormModal" class="form-horizontal" method="post">
                <div style="display: flex;background-color: #fff">
                    <div style="width: 250px;border-right: 1px solid #ccc">
                        <div>

                        </div>
                        <div class="lsProject">
                            @if (isset($project) && $project) {
                            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                <tbody>
                                @foreach($project as $key=>$value)
                                    <tr class="rule">
                                        <td>
                                            {{$value['text']}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                    <div style="flex:1">
                        <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                            <tbody>
                            @foreach($listRoleProject as $key=>$value)
                                <tr class="rule-root">
                                    <th colSpan="20" class="header"><i class="fa fa-gavel"></i> <b>
                                            {{$value->name}}
                                        </b>
                                    </th>
                                </tr>

                                @if($value->role)
                                    @foreach($value->role as $ks=>$role)
                                        <tr class="rule">
                                            <td width="2%">
                                                <input @if(isset($listRoleOfMember['permission_list']) && is_array($listRoleOfMember['permission_list']) && in_array($ks,$listRoleOfMember['permission_list'])) checked @endif name="roles[{{$ks}}]" value="{{$ks}}" id="pop_{{$ks}}" type="checkbox"/>
                                            </td>
                                            <td>
                                                <label style="display: block;margin: 0;padding: 5px" for="pop_{{$ks}}">{{$role->name}}</label>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>

        </div>
        <div class="modal-footer">
            <button type="button" onclick="return MNG_POST.update('{{admin_link('project/permission-save',TRUE)}}','#postInputFormModal');" class="btn btn-primary">Cập nhật</button>
        </div>

    </div>
</div>
<script type="text/javascript">
    $('select.select').select2();
</script>
<script type="text/javascript">
    $(function () {
        _AUTO_COMPLETE_INIT('#select-remote-data-project', '{{admin_link('project/suggest')}}', 'Nhập tên dự án')
        _AUTO_COMPLETE_INIT('#select-remote-data-staff', '{{admin_link('staff/suggest')}}', 'Nhập tên nhân viên')
    });
</script>