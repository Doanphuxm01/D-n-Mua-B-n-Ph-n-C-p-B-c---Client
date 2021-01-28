
<div class="col-8 m-auto">
    <div class="modal-content">
        <!-- Invoice template -->
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title">Chi tiết đơn</h6>
                <div class="heading-elements">
                    <button type="button" class="ml-5 pt-2 close" data-dismiss="modal" aria-hidden="true"> x</button>
                </div>
            </div>

            <div class="panel-body">
                <div class="row invoice-payment" id="invoice-content">
                    <div class="col-sm-5">
                        <div class="content-group">
                            <h6>Đơn mua điểm của {{ $obj['tai_khoan_nguon']['account'] }}</h6>
                            <div class="table-responsive no-border">
                                @if(@$obj)
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th>Số điểm cần mua:</th>
                                            <td class="text-right">{{ \App\Elibs\Helper::formatMoney($obj['so_diem_can_mua']) }}</td>
                                        </tr>
                                        @if(isset($obj['debt']))
                                            <tr>
                                                <th>Công nợ: </th>
                                                <td class="text-right">
                                                    <div>
                                                        {{\App\Elibs\Helper::formatMoney($obj['cong_no'])}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Số điểm được nhận:</th>
                                            <td class="text-right text-primary"><h5 class="text-semibold">{{ \App\Elibs\Helper::formatMoney($obj['so_diem_duoc_nhan']??$obj['so_diem_can_mua']) }}</h5></td>
                                        </tr>
                                        <tr>
                                            <th>Số điểm được ví chiết khấu:</th>
                                            <td class="text-right text-primary"><h5 class="text-semibold">{{ \App\Elibs\Helper::formatMoney($obj['so_diem_vi_chiet_khau']) }}</h5></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-primary btn-labeled" id="invoice-print"><b><i class="icon-printer"></i></b> Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /invoice template -->
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Bỏ qua</button>
        </div>
    </div><!-- /.modal-content -->
</div>
<script>
    document.getElementById("invoice-print").onclick = function () {
        printElement(document.getElementById("invoice-content"));
    };

    function printElement(elem) {
        var domClone = elem.cloneNode(true);

        var $printSection = document.getElementById("printSection");

        if (!$printSection) {
            var $printSection = document.createElement("div");
            $printSection.id = "printSection";
            document.body.appendChild($printSection);
        }

        $printSection.innerHTML = "";
        $printSection.appendChild(domClone);
        window.print();
    }
</script>
<style>
    @media screen {
        #printSection {
            display: none;
        }
    }

    @media print {
        body * {
            visibility:hidden;
        }
        #printSection, #printSection * {
            visibility:visible;
        }
        #printSection {
            position:absolute;
            left:0;
            top:0;
        }
    }
</style>