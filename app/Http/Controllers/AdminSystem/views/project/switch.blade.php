<div class="modal-dialog modal-large">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Chọn dự án làm việc</h3>
        </div>
        <div class="modal-body pd-10">
            @if(!isset($allProjectByMe) || !$allProjectByMe)
                <h3 class="text-center mt-0">Không tìm thấy dự án nào!</h3>
                <div>
                    <small>Có thể bạn chưa được phân quyền vào bất kỳ dự án nào. Hãy liên hệ với quản trị viên để được hỗ trợ!</small>
                </div>
            @else
                <ul class="app-list app-list-fanpage">
                    @foreach($allProjectByMe as $key=>$value)
                        <li class="list-media-small">
                            <div class="listImg"><i class="fa fa-university text-warning-600"> </i></div>
                            <div class="desc">
                                <div class="title"><a title="Quản lý dự án" href="javascript:void(0)" onclick="return MNG_POST.update('{{admin_link('project/show_switch?popup=true&id='.$value['_id'].'&token='.\App\Elibs\Helper::buildTokenString($value['_id']).'')}}')" >
                                        {{$value['name']}}
                                    </a></div>
                                <small>@if($value['brief']) {{$value['brief']}} @else Cập nhật lúc: {{\App\Elibs\Helper::showMongoDate($value['updated_at'],'H:i:s d/m/Y')}}@endif</small>
                            </div>
                            <div class="actions">
                                <a onclick="return MNG_POST.update('{{admin_link('project/show_switch?popup=true&id='.$value['_id'].'&token='.\App\Elibs\Helper::buildTokenString($value['_id']).'')}}')" class="btn btn-outline-success btn-xs" href="javascript:void(0)"><i class="fa fa-location-arrow"></i> Truy cập</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="panel-footer">
            @if(!\App\Http\Models\Project::$curentProject)
                <div>Bạn chưa chọn dự án làm việc</div>
                @else
                <div><i class="fa fa-info-circle"></i> Bạn đang làm việc với dự án: {{\App\Http\Models\Project::$curentProject['name']}}</div>
                @endif
        </div>
    </div>
</div>