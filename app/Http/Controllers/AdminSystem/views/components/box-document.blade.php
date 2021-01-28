<div class="panel panel-white">
    <div class="panel-heading panel-heading-tabx">

        <h3 class="panel-title">
            <a class="" data-toggle="collapse" href="#gDocument" aria-expanded="true">
                <i class="icon-book text-primary"> </i>
                Văn bản mới
            </a>
        </h3>
        <div class="heading-elements">
            <ul class="icons-list">
                <li>
                    <a title="Xem tất cả" href="{{admin_link('/document')}}"> <i class="icon-redo2"></i></a>
                </li>
            </ul>

        </div>

    </div>
    <div id="gDocument" class="panel-collapse collapse in" aria-expanded="true">
        <div class="panel-body no-padding">
            <div class="tabbable">
                <ul class="nav nav-tabs nav-tabs-bottom mb-0" style="border-bottom: 0">
                    <li class="active"><a href="#tab-van-ban-den" data-toggle="tab" aria-expanded="true">Văn bản đến</a>
                        @if(isset($lsDocumentFromCount) && $lsDocumentFromCount )
                                <span class="badge bg-danger-400 badge-count">{{$lsDocumentFromCount}}</span>
                        @endif
                    </li>
                    <li><a href="#tab-van-ban-di" data-toggle="tab" aria-expanded="true">Văn bản đi</a>
                        @if(isset($lsDocumentToCount) && $lsDocumentToCount )
                            <span class="badge bg-danger-400 badge-count">{{$lsDocumentToCount}}</span>
                        @endif
                    </li>
                    <li><a href="#tab-van-ban-product" data-toggle="tab" aria-expanded="true">Kiểm soát sản phẩm</a>
                        @if(isset($lsDocumentProCount) && $lsDocumentProCount )
                            <span class="badge bg-danger-400 badge-count">{{$lsDocumentProCount}}</span>
                        @endif
                    </li>
                </ul>
                <div class="tab-content pb-10" style="max-height: 500px;overflow: auto">
                    <div class="tab-pane active" id="tab-van-ban-den">
                        @if(isset($lsDocumentFrom) && $lsDocumentFrom)
                            @if(!$lsDocumentFrom->isEmpty())
                                <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                    <tbody>
                                    @foreach($lsDocumentFrom as $ks=>$val)
                                        @include('views.components.document-row')
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    </div>
                    <div class="tab-pane" id="tab-van-ban-di">
                        @if(isset($lsDocumentTo) && $lsDocumentTo)
                            @if(!$lsDocumentTo->isEmpty())
                                <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                    <tbody>
                                    @foreach($lsDocumentTo as $ks=>$val)
                                        @include('views.components.document-row')
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    </div>
                    <div class="tab-pane" id="tab-van-ban-product">
                        @if(isset($lsDocumentPro) && $lsDocumentPro)
                            @if(!$lsDocumentPro->isEmpty())
                                <table class="table-1-row table table-hover table-striped table-bordered1 table-advanced c2-table">
                                    <tbody>
                                    @foreach($lsDocumentPro as $ks=>$val)
                                        @include('views.components.document-row')
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>