<div class="modal-dialog modal-larger">
    <div class="modal-content">
        <div class="modal-header bg-brand">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">
                Phân quyền hệ thống cho từng chức vụ
            </h3>
        </div>
        <div class="modal-body no-padding" style="background: #e8e8e8">
            <?php
            $allRoles = \App\Http\Models\Role::getListRole();
            $allRoleGroup = \App\Http\Models\Role::getListGroup();
            ?>
            <div class="row mx-0">
                <form name="postInputForm" autocomplete="off"
                      onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm"
                      class="form-horizontal " method="post">
                    <div class="col-md-12 px-0">
                        <div class="panel panel-white">
                            <div id="gInfo" class="panel-collapse collapse in" aria-expanded="true">
                                <table id="table-role" cellspacing="0" cellpadding="0"
                                       class="table table-striped table-bordered table-io" border="1" width="100%">
                                    <thead>
                                    <tr class="heading">
                                        <td rowspan="2" colspan="3" class="text-center text-bold">Nội dung</td>
                                        <td rowspan="2" colspan="1" class="text-center text-bold">

                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($allRoles as $key=>$item)
                                        <tr>
                                            <td class="text-center text-bold">{{$key+1}}</td>
                                            <td class="text-left text-bold" rowspan="1" colspan="200000">{{$item['name']}}</td>
                                        </tr>

                                        @if(isset($item['role']) && $item['role'])
                                            <?php
                                            $_index_sub = 1;
                                            ?>
                                            @foreach($item['role'] as $ks=>$vs)

                                                <tr>
                                                    <td></td>
                                                    <td>{{$key+1}}.{{$_index_sub}}</td>
                                                    <td>{{$vs}}</td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="obj[role][]"
                                                               value="{{$item['key'].'_'.$ks}}"
                                                               @if(is_array(@$obj['role']) && in_array($item['key'].'_'.$ks, $obj['role']))
                                                               checked
                                                               @endif
                                                               id="role-{{$item['key'].'_'.$ks}}">
                                                    </td>
                                                </tr>
                                                <?php
                                                $_index_sub++;
                                                ?>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="4" style="text-align: right;padding: 0;">
                                            <button type="button" onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm');"
                                                     class="btn btn-danger">Lưu lại
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        {{--<div class="modal-footer">--}}
        {{--<button type="button"--}}
        {{--onclick="return MNG_POST.update('{{admin_link('project/permission-save',TRUE)}}','#postInputFormModal');"--}}
        {{--class="btn btn-primary">Cập nhật--}}
        {{--</button>--}}
        {{--</div>--}}

    </div>
</div>
<script type="text/javascript">
    //$('select.select').select2();
</script>
<script type="text/javascript">
    var listRoleProject = {!! json_encode($listRoleProject) !!};

    function _selectProjectToAdd(obj) {
        let lsProjectId = $(obj).val();
        if (lsProjectId !== '') {
            lsProjectId = lsProjectId.split(',');
        } else {
            lsProjectId = [];
        }
        if (lsProjectId.length > 0) {
            $('#btnAddProject').addClass('btn-primary');
        } else {
            $('#btnAddProject').removeClass('btn-primary');
        }

        console.log(lsProjectId)
    }

    function _addProjectSelected() {
        MNG_POST.update('{{admin_link('project/assign_project_dep')}}', '#add-dep')
    }

    function _removeProjectSelected() {
        MNG_POST.update('{{admin_link('project/assign_project_dep')}}', '#add-dep')
    }

    $(function () {
        $('#obj-select-project').select2()
    });
</script>