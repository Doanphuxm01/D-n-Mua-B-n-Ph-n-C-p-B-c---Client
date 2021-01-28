<div class="modal-dialog modal-large">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Có lỗi</h3>
        </div>
        <div class="modal-body pd-10">
            @if($msg)
                <h3>{{$msg}}</h3>
            @else
                Có lỗi
            @endif
        </div>
        <div class="modal-footer">
            <button data-dismiss="modal" class="btn btn-dager">Đóng lại</button>
        </div>
    </div>
</div>
