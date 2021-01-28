<div class="modal-dialog modal-larger">
    <div class="modal-content">
        <div class="modal-header bg-brand">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">
                Phân quyền hệ thống cho chức vụ {{@$obj['name']}}
            </h4>
        </div>
        <div class="modal-body no-padding" style="background: #e8e8e8">
            <div class="row mx-0">
                <div class="panel panel-white">
                    <form name="postInputForm" autocomplete="off" method="POST"
                      onsubmit="return MNG_POST.update('{{url()->current()}}','#postInputForm');" id="postInputForm">
                    <div>
                        <input type="hidden" name="position_id" value="{{@$obj['_id']}}">
                        <div class="form-group">
                            <table class="table table-hover table-bordered table-io" border="1" width="100%">
                                <tbody>
                                @php ($roleGroupBy = \App\Http\Models\Role::$KAYN_ROLE_GROUP_DETAILS)
                                @foreach(\App\Http\Models\Role::$KAYN_ROLE_GROUP as $keyGroup=> $group)
                                    <tr>
                                        <th colspan="3"><span class="text-bold">{{$group['label']}}</span></th>
                                    </tr>
                                    @foreach(@$roleGroupBy[$group['key']] as $key=>$item)
                                        <tr>
                                            <td>{{$keyGroup+1}}.{{$key+1}}</td>
                                            <td>
                                                <label for="role-{{$item['key']}}">{{$item['text']}}</label>
                                            </td>


                                            <td><input type="checkbox" name="obj[roles][]"
                                                       value="{{$item['key']}}"
                                                       @if(is_array(@$obj['roles']) && in_array($item['key'], $obj['roles']))
                                                       checked
                                                       @endif
                                                       id="role-{{$item['key']}}"></td>
                                        </tr>
                                    @endforeach

                                @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary" type="button" style="width: 150px"
                                    onclick="return MNG_POST.update('{{url()->current()}}','#postInputForm');">
                                Phân quyền
                            </button>
                        </div>
                    </div>

                </form>
                </div>
            </div>
        </div>

    </div>
</div>
