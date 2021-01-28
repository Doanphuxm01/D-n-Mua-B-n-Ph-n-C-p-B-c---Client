<div class="modal-dialog modal-full">
    <div class="modal-content">
        <div class="modal-header bg-teal-800">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Thông tin chi tiết bản Raw </h3>
        </div>

        <div class="modal-body pd-10" style="padding-bottom: 0">
            <div class="row">
                <div>
                    Dưới đây là chi tiết của đối tượng bị tác động thay đổi và người thay đổi
                </div>
                <pre>
                    {{trim(print_r($data->toArray()))}}
                </pre>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>