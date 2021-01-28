<style>
    .category-content {
        padding: 10px
    }
</style>
<div class="sidebar sidebar-secondary sidebar-default">
    <form action="" id="form-filter">

    <div class="sidebar-content">
        <div class="tab-content">

            <!-- Sidebar tabs -->
            <div class="tab-pane no-padding active" style="min-width: 240px" id="forms-tab">



                    <!-- Sidebar search -->
                    <div class="sidebar-category">
                        <div class="category-title">
                            <span>Tìm kiếm</span>
                            <ul class="icons-list">
                                <li><a href="#" data-action="collapse"></a></li>
                            </ul>
                        </div>

                        <div class="category-content">
                            <div class="has-feedback has-feedback-left">
                                <input name="q" type="search" value="{{app('request')->input('q')}}"
                                       class="form-control" placeholder="Từ khoá,...">
                                <div class="form-control-feedback">
                                    <i class="icon-search4 text-size-base text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /sidebar search -->


                    <!-- Right checkbox group -->
                    <div class="sidebar-category">
                        <div class="category-title">
                            <span>Phòng ban</span>
                            <ul class="icons-list">
                                <li><a href="#" data-action="collapse"></a></li>
                            </ul>
                        </div>

                        <div class="category-content">

                            <div>
                                <select id="obj-departments" name="q_departments[]"
                                        data-placeholder="Chọn phòng ban..." multiple="multiple"
                                        class="select select-xs select-multi-small">
                                    @if(isset($allDepartment) && $allDepartment)
                                        @php
                                            $allDepGroup = collect($allDepartment)->reduce(function ($ret, $cur) {
                                                if(@$cur['group']){
                                                    $ret[$cur['group']] = 1;
                                                }

                                                return $ret;
                                            }, []);
                                        @endphp

                                        @foreach($allDepGroup as $depGroupKey =>$depGroupVal)
                                            @php $tempLabel= "";
                                            if($depGroupKey =="DEP_GROUP_QUAN_LY"){ $tempLabel ='Nhóm quản lý';}
                                            else if ($depGroupKey =="DEP_GROUP_SAN_XUAT"){$tempLabel ='Nhóm sản xuất';}
                                            elseif ($depGroupKey  =='DEP_GROUP_GIAM_DOC'){$tempLabel ='Nhóm giám đốc';}
                                            @endphp
                                            <optgroup label="{{$tempLabel}}">
                                                @foreach(collect($allDepartment)->filter(function($dep)use($depGroupKey){return $dep['group'] ==$depGroupKey; })->sortBy('name') as $item)
                                                    <option
                                                            @if(isset($qObj['department'])  && is_array($qObj['department']) && in_array($item['_id'],$qObj['department'])) selected
                                                            @endif
                                                            @if(in_array($item['_id'], request()->input('q_departments', [])))
                                                            selected
                                                            @endif
                                                            value="{{$item['_id']}}">{{$item['name']}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                        </div>
                    </div>
                    <!-- /right checkbox group -->
                    @foreach(collect(\App\Http\Models\MetaData::$member_data_filter)->sortBy('group_name')->groupBy('group_name') as $label=>  $group)

                        <div class="sidebar-category">
                            <div class="category-title">
                                @php
                                    $label = $label  ? $label :"Thông tin cơ bản";
                                @endphp
                                <span>  @if($label)
                                        <b>{{$label}}</b>
                                    @endif</span>
                                <ul class="icons-list">
                                    <li><a href="#" data-action="collapse"
                                           onclick="$.cookie('{{$label}}', $.cookie('{{$label}}')=='true'? 'false':'true', {expires:10})"></a>
                                    </li>
                                </ul>
                            </div>

                            <div class="category-content text-left" data-id="cookie-toggle" data-value="{{$label}}">

                                <div class="row">


                                    @foreach($group as $field)
                                        <div class="col-md-12">
                                            @include("views.components.filter-elem", ["field"=>$field])
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="sidebar-category">
                        <div class="category-content text-center">
                            <div class="row">
                                <div class="col-xs-6">
                                    <a href="/staff" onclick="return confirm('Bạn muốn reset bộ lọc?')">
                                        <button type="button" class="btn btn-danger btn-block">Reset</button>
                                    </a>
                                </div>
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-info btn-block">Lọc</button>
                                </div>
                            </div>
                        </div>
                    </div>


            </div>

        </div>


    </div>
    </form>
</div>

<script>
    $(function () {

        function DATERANGE_BASIC() {
            try {

                $('.daterange-basic-customer').daterangepicker({
                    applyClass: 'bg-slate-600',
                    cancelClass: 'btn-default',
                    autoUpdateInput: false,
                    locale: {
                        format: 'DD/MM/YYYY'
                    },
                    autoEnd: true
                });
                $('.daterange-basic-customer').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                });

                $('.daterange-basic-customer').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
            } catch (e) {
                console.log(e);
            }
        }

        DATERANGE_BASIC();

    });
            {{--_AUTO_COMPLETE_INIT('#select-remote-data-staff', '{{admin_link('staff/suggest')}}', 'Nhập tên nhân viên')--}}
    const filter_mng_member = {
            toggle_check(e) {
                let current = $(e.target)

                let formGroupContainer = current.parents('[data-id=filter-elem]')
                let filterBox = formGroupContainer.find('[data-id=filter-elem-box]')
                if (current.prop('checked')) {
                    filterBox.removeClass('hide')
                    filterBox.find('input').removeAttr('disabled')
                } else {
                    filterBox.addClass('hide')
                    filterBox.find('input').attr('disabled', 'disabled')
                }

            },
            change_query(e) {
                let current = $(e.target);
                let formGroupContainer = current.parents('[data-id=filter-elem]')
                let filterBox = formGroupContainer.find('[data-id=filter-elem-box]')
                let filterBoxValue = formGroupContainer.find('[data-id=filter-elem-box-value]')
                if (['exist', 'not_exist', '', null, undefined].includes(e.target.value)) {
                    filterBoxValue.attr('disabled', 'disabled')
                } else {
                    filterBoxValue.removeAttr('disabled')
                }
            },


        }


    function init_filter() {
        $('[data-id=filter-elem-checkbox]').on('click', filter_mng_member.toggle_check)
        $('[data-id=filter-elem-box-value][data-selectSource]').each(function () {
            let toAuto = $(this)
            let q_source = toAuto.attr('data-selectSource');
            // toAuto.select2({})
            _AUTO_COMPLETE_INIT_FILTER(toAuto, q_source, 'Nhập để tìm kiếm', true, {data: []})
        });
        $('[data-id=filter-elem-box-query]').on('change', filter_mng_member.change_query)
        $('[data-id=filter-elem-box-query]').trigger('change')


    }

    $('[data-id=cookie-toggle]').each(function () {
        if ($.cookie($(this).data('value')) != 'false') {
            $(this).css('display', 'none')
        }
    })

    init_filter()

</script>
