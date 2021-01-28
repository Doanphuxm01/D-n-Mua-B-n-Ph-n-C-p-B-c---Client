const lsObj = new Vue({
    el: '#lsObj',
    data() {
        return {
            lsOrders: Object.assign({}, lsOrders),
            isShowDeleteAll: false
        }
    },
    methods: {
        _save(step = [], id = 0) {
            step['order_id'] = id
            swal({
                title: 'Cập nhật tình trạng đơn',
                text: 'Xác nhận chuyển đơn sang trạng thái ' + step.text,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((t) => {
                    if (t) {
                        var _token = jQuery('meta[name=_token]').attr("content");
                        if (_token) {
                            step['_token'] = _token;
                        }
                        jQuery.ajax({
                            url: 'danh-sach-don-tra-hang/_save',
                            type: "POST",
                            data: step,
                            dataType: 'json',
                            success: function (json) {
                                if(json.status == 1) {
                                    swal({
                                        title: 'Cập nhật thành công',
                                        text: 'Xác nhận chuyển đơn sang trạng thái ' + step.text,
                                        icon: "success",
                                        buttons: true,
                                        dangerMode: false,
                                    })
                                    window.location.href = json.data.link;
                                }else{
                                    swal({
                                        title: 'Oops!',
                                        text: json.msg,
                                        icon: "warning",
                                        buttons: true,
                                        dangerMode: false,
                                    })
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(thrownError);
                            }
                        });
                        return true;
                    }
                });

        },
        _delele() {
            $('.delete-all-checked').click(function () {
                var order_ids = [];
                $.each($("input.item-check[type='checkbox']:checked"), function(){
                    order_ids.push($(this).data('id'));
                });
                Swal.fire({
                title: 'Xóa đơn hàng',
                text: 'Xác nhận chuyển tất cả đơn đã chọn sang trạng thái Đã xóa',
                type: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonClass: "btn btn-success mt-2 btn-sm",
                cancelButtonClass: "btn btn-danger ml-2 mt-2 btn-sm",
                buttonsStyling: !1,
                confirmButtonText: "Vâng, tôi muốn"
                }).then(function (t) {
                    if (t.value) {
                        shop.ajax_popup('order/delete', 'POST', {ids: order_ids}, function(json){
                            if(json.error == 0) {
                                Swal.fire({
                                    title: 'Cập nhật thành công',
                                    type: "success",
                                    showCancelButton: 0,
                                    showConfirmButton: !0,
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonClass: "btn btn-success mt-2 btn-sm",
                                    buttonsStyling: !1,
                                });
                                shop.reload();
                            }else{
                                Swal.fire({
                                    title: 'Oops!',
                                    text: json.msg,
                                    type: "warning",
                                    showCancelButton: !0,
                                    showConfirmButton: 0,
                                    cancelButtonColor: "#d33",
                                    cancelButtonClass: "btn btn-danger ml-2 mt-2 btn-sm",
                                    buttonsStyling: !1,
                                });
                            }
                        });
                    }
                });
            });
        }
    }
});