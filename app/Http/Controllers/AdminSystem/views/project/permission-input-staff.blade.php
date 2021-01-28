<div class="modal-dialog modal-larger">
    <div class="modal-content">
        <div class="modal-header bg-brand">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Phân quyền dự án cho thành viên</h3>
        </div>
        <div class="modal-body no-padding" style="background: #e8e8e8">
            <form name="postInputFormModal" onsubmit="return MNG_POST.update('{{admin_link('project/permission-save-staff',TRUE)}}','#postInputFormModal');" id="postInputFormModal" class="form-horizontal" method="post">
                <input type="hidden" name="token" value="{{\App\Elibs\Helper::buildTokenString('ngannv'.date('d'))}}"/>
                <div>
                    <div class="panel panel-white">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <span>Thông tin cơ bản</span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-md-3">Dự án: </label>
                                <div class="col-md-9">
                                    @php
                                        $strSelected = '';
                                        $objSelected = [];
                                         if (isset($project) && $project) {
                                                $strSelected =$project['_id'];
                                                $objSelected[] = [
                                                    'text'=>$project['name'],
                                                    'id'=>$project['_id'],
                                                ];
                                            }
                                    @endphp
                                    <input type="text"
                                           value="{{$strSelected}}"
                                           data-selected='{!! json_encode($objSelected) !!}'
                                           class="select-xs" id="select-remote-data-project" name="obj[projects]">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3">Nhân viên: </label>
                                <div class="col-md-9">
                                    @php
                                        $strSelected = '';
                                        $objSelected = [];
                                         if (isset($member) && $member) {
                                                $strSelected =$member['_id'];
                                                $objSelected[] = [
                                                    'text'=>$member['name'],
                                                    'id'=>$member['_id'],
                                                ];
                                            }
                                    @endphp
                                    <input type="text"
                                           value="{{$strSelected}}"
                                           data-selected='{!! json_encode($objSelected) !!}'
                                           class="select-xs" id="select-remote-data-staff" name="obj[staffs]">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-white">
                    <div class="panel-heading" style="border-bottom: 0">
                        <h3 class="panel-title">
                            <a class="" data-toggle="collapse" href="#gRolePop" aria-expanded="true">Phân quyền</a>
                        </h3>
                    </div>
                    <div id="gRolePop" class="panel-collapse collapse in" aria-expanded="true">
                        <div class="panel-body no-padding">
                            <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                <tbody>
                                @foreach($listRoleProject as $key=>$value)
                                    <tr class="rule-root">
                                        <th colSpan="20" class="header"><i class="fa fa-gavel"></i> <b>
                                                {{$value['name']}}
                                            </b>
                                        </th>
                                    </tr>

                                    @if($value['role'])
                                        @foreach($value['role'] as $ks=>$role)
                                            <tr class="rule">
                                                <td width="2%">
                                                    <input
                                                            @if(isset($listRoleOfMember['permission_list']) && $listRoleOfMember['permission_list'])
                                                                    @foreach($listRoleOfMember['permission_list'] as $ks2=>$vs)
                                                                        @if($vs['key']==$value['key'] && $ks==$vs['value']) checked @break @endif
                                                                    @endforeach
                                                            @endif
                                                            name="roles[{{$value['key']}}]" value="{{$ks}}" id="pop_{{$value['name']}}{{$ks}}" type="radio"/>
                                                </td>
                                                <td>
                                                    <label style="display: block;margin: 0;padding: 5px" for="pop_{{$value['name']}}{{$ks}}">{{$role}}</label>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </form>

        </div>
        <div class="modal-footer">
            <button type="button" onclick="return MNG_POST.update('{{admin_link('project/permission-save-staff',TRUE)}}','#postInputFormModal');" class="btn btn-primary">Cập nhật</button>
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