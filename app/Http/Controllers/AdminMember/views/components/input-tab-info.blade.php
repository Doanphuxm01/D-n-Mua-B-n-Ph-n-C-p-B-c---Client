@if(!isset($allCity))
    @php
        $allCity = collect(\App\Http\Models\Location::getAllCity());
    @endphp
@endif
@php($allStaffDataList = \App\Http\Models\MetaData::getAllByObject('staff'))
@php($listNgoaiNgu = \App\Http\Models\MetaData::getAllByType(\App\Http\Models\MetaData::STAFF_NGOAI_NGU))

<style>
    .d-flex {
        display: flex;
    }

    .justify-content-center {
        align-items: center;
    }

    #upload-avatar {
        display: none;
    }

    #avatar-container:hover #upload-avatar {
        display: block;
        margin-left: auto;
        margin-right: auto;

    }

</style>
<form action="{{admin_link('/staff/_save_tab_info')}}" method="POST" id="postInputForm">
    <input type="hidden" name="_token" value="{{csrf_token()}}">

    <input type="hidden" name="id" id="id" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <input type="hidden" name="obj[_id]" value="{{isset($obj['_id'])?$obj['_id']:''}}"/>
    <div class="">
        <div class="col-md-12" style="padding-left: 5px;padding-right: 5px;">
            <div class="panel">
                <div class="panel-heading">
                    <h5 class="panel-title">
                        Thông tin cơ bản
                    </h5>
                </div>
                <div class="panel-body">
                    <div class="d-flex">
                        <div class="col-md-3 p-4">
                            <?php
                            $avatar = "";
                            if (!isset($obj['avatar_url']) || empty($obj['avatar_url'])) {
                                $avatar = '/images/no-avatar.png';
                            } else {
                                $avatar = \App\Http\Models\Media::getFileLink($obj['avatar_url']);
                            }
                            ?>
                            <div id="avatar-container" class="border-green "
                                 style="border:1px dashed ; height: 100px;height: 100%;background-position: center;background-repeat: no-repeat;background-size: contain ;display: flex;align-items: center ;background-image: url('{{$avatar}}')">
                                <div class="text-center" style="width: 100%;">
                                    <input type="hidden" value="{{@$obj['avatar_url']}}" id="input-avatar"
                                           name="obj[avatar_url]">
                                    <button title="Nhấn để thay ảnh"
                                            id="upload-avatar"
                                            class="btn btn-sm btn-info"
                                    >
                                        <i class="icon-image5"></i>
                                    </button>


                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">

                                <div class="col">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Mã nhân viên','key'=>'code'],
                                    'note'=>['label'=>'*','class'=>'text-danger']])
                                </div>
                                <div class="col">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Họ tên','key'=>'name'],
                                    'note'=>['label'=>'*','class'=>'text-danger']])
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="" class="control-label">Giới tính</label>
                                        <select name="obj[gender]" class="form-control" >
                                            <option value=" ">Chưa lựa chọn</option>
                                            <option @if(isset($obj['gender']) && $obj['gender']=="male") selected
                                                    @endif  value="male">Nam
                                            </option>
                                            <option @if(isset($obj['gender']) && $obj['gender']=="female") selected
                                                    @endif  value="female">Nữ
                                            </option>
                                            <option @if(isset($obj['gender']) && $obj['gender']=="other") selected
                                                    @endif  value="other">Khác
                                            </option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Ngày sinh','key'=>'date_of_birth',
                                     'value' => \App\Elibs\Helper::showMongoDate(@$obj['date_of_birth']), 'class' => 'datepicker']])
                                </div>
                                <div class="col">
                                    <label for="" class="control-label">Nơi sinh</label>
                                    <select style="width: 100%;height: 35px"
                                            {{--onchange="$('#nguyenquan').val($('#nguyenquan').val())"--}}
                                            class="select-search select-md" name="obj[noi_sinh][key]">
                                        <option value="">Chọn tỉnh thành</option>
                                        @foreach($allCity as $key=>$value)
                                            <option @if(isset($obj['noi_sinh']['key']) && $obj['noi_sinh']['key']==$value->slug) selected
                                                    @endif value="{{$value->slug}}">{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col">
                                    <label for="" class="control-label">Nguyên quán</label>
                                    <select style="width: 100%;height: 35px"
                                                {{--onchange="$('#nguyenquan').val($('#nguyenquan').val())"--}}
                                                class="select-search select-md" name="obj[nguyen_quan][key]">
                                            <option value="">Chọn tỉnh thành</option>
                                            @foreach($allCity as $key=>$value)
                                                <option @if(isset($obj['nguyen_quan']['key']) && $obj['nguyen_quan']['key']==$value->slug) selected
                                                        @endif value="{{$value->slug}}">{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="" class="control-label">Dân tộc</label>
                                        <select name="obj[dan_toc][id]" class="select-search select-md">
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_DAN_TOC;}) as $val)
                                                <option @if(isset($obj['dan_toc']['id']) && $obj['dan_toc']['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="" class="control-label">Tôn giáo</label>
                                        <select name="obj[ton_giao][id]" class="select-search select-md">
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_TON_GIAO;}) as $val)
                                                <option @if(isset($obj['ton_giao']['id']) && $obj['ton_giao']['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-group">
                                        <label for="" class="control-label">Quốc tịch</label>
                                        <select name="obj[quoc_tich][id]" class="select-search select-md"
                                                placeholder="Chọn quốc tịch">
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_QUOC_TICH;}) as $val)
                                                <option @if(isset($obj['quoc_tich']['id']) && $obj['quoc_tich']['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Số BHXH','key'=>'so_bhxh',
                                    'class' => 'input-type-number']])
                                </div>
                                <div class="col">
                                    @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Mã số thuế','key'=>'ma_so_thue',
                                    'class' => 'input-type-number']])
                                </div>
                                <div class="col">
                                    <label for="" class="control-label">Tình trạng hôn nhân</label>
                                    <select type="text" class="form-control"
                                            name="obj[tinh_trang_hon_nhan]">
                                        <option @if(isset($obj['tinh_trang_hon_nhan']) && $obj['tinh_trang_hon_nhan']=='Độc thân') selected
                                                @endif value="Độc thân">Độc thân
                                        </option>
                                        <option @if(isset($obj['tinh_trang_hon_nhan']) && $obj['tinh_trang_hon_nhan']=='Kết hôn') selected
                                                @endif value="Kết hôn">Kết hôn
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="" class="control-label">Tiền án / tiền sự</label>
                                        <select class="form-control" name="obj[tien_an_tien_su]">
                                            <option @if(isset($obj['tien_an_tien_su']) && $obj['tien_an_tien_su'] =='') selected
                                                    @endif
                                                    value="">Chưa lựa chọn
                                            </option>
                                            <option @if(isset($obj['tien_an_tien_su']) && $obj['tien_an_tien_su'] =='có') selected
                                                    @endif
                                                    value="có">Có
                                            </option>
                                            <option @if(isset($obj['tien_an_tien_su']) && $obj['tien_an_tien_su'] =='không') selected
                                                    @endif
                                                    value="không">Không
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label for="" class="control-label">Ngoại ngữ</label>
                                        <select name="obj[ngoai_ngu][id][]" class="select-search"
                                                placeholder="Nhập thông tin" multiple>
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($listNgoaiNgu as $val)
                                                <option @if(
                                                isset($obj['ngoai_ngu'])  && is_array($obj['ngoai_ngu']) &&
                                                collect($obj['ngoai_ngu'])->first(function($item)use($val){return isset($item['id']) && $item['id'] == @$val['_id'];})))
                                                        selected
                                                        @endif value="{{@$val['_id']}}">{{@$val['name']}}</option>

                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{--HỘ KHẨU THƯỜNG CHÚ--}}
                    <div class="d-flex">
                        <div class="col">
                            <div class="form-group ">
                                <label for="" class="control-label">Hộ khẩu thường trú</label>
                                <input name="obj[ho_khau_thuong_chu][chi_tiet]"
                                     @isset($obj['ho_khau_thuong_chu']['chi_tiet'])
                                     value="{{$obj['ho_khau_thuong_chu']['chi_tiet']}}"
                                     @endisset
                                     type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Tỉnh thành
                                </label>
                                <select style="width: 100%;height: 35px"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#ho_khau_thuong_chu_huyen','Chọn quận huyện')"
                                        class="select-search select-md" name="obj[ho_khau_thuong_chu][tinh][key]">
                                    <option value="">Chọn tỉnh thành</option>
                                    @foreach($allCity as $key=>$value)
                                        <option @if(isset($obj['ho_khau_thuong_chu']['tinh']['key']) && $obj['ho_khau_thuong_chu']['tinh']['key']==$value->slug) selected
                                                @endif value="{{$value->slug}}">{{$value->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Quận, huyện
                                </label>
                                <select style="width: 100%;height: 35px"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#ho_khau_thuong_chu_xa','Chọn xã phường')"
                                        id="ho_khau_thuong_chu_huyen" class="select-search"
                                        name="obj[ho_khau_thuong_chu][huyen][key]">
                                    <option value="">Chọn quận huyện</option>
                                    <?php $districtOfCity = []; ?>
                                    @if(isset($obj['ho_khau_thuong_chu']['tinh']['code']))
                                        <?php
                                        $districtOfCity = \App\Http\Models\Location::getAllLocationByParent($obj['ho_khau_thuong_chu']['tinh']['code']);
                                        ?>
                                    @endif
                                    @if(isset($districtOfCity) && $districtOfCity)
                                        @foreach($districtOfCity as $key=>$value)
                                            <option @if(isset($obj['ho_khau_thuong_chu']['huyen']['key']) && $obj['ho_khau_thuong_chu']['huyen']['key']==$value->slug) selected
                                                    @endif value="{{$value->slug}}">{{$value->name}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Xã, Phường
                                </label>
                                <select style="width: 100%;height: 35px"
                                        id="ho_khau_thuong_chu_xa" class="select-search"
                                        name="obj[ho_khau_thuong_chu][xa][key]">
                                    <option value="">Chọn xã phường</option>
                                    <?php $townOfDistrict = [];?>
                                    @if(isset($obj['ho_khau_thuong_chu']['huyen']['code']))
                                        <?php
                                        $townOfDistrict = \App\Http\Models\Location::getAllLocationByParent($obj['ho_khau_thuong_chu']['huyen']['code'])->toArray();
                                        ?>

                                    @endif
                                    @if(isset($townOfDistrict) && $townOfDistrict)
                                        @foreach($townOfDistrict as $key=>$value)
                                            <option
                                                    @if(isset($obj['ho_khau_thuong_chu']['xa']['key']) && $obj['ho_khau_thuong_chu']['xa']['key']==$value['slug'])
                                                    selected
                                                    @endif
                                                    value="{{$value['slug']}}">{{$value['name']}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    {{--END HỘ KHẨU THƯỜNG CHÚ--}}

                    {{--NƠI Ở HIỆN NAY--}}
                    <div class="d-flex">
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">Nơi ở hiện nay</label>
                                <input name="obj[noi_o_hien_nay][chi_tiet]"
                                       @isset($obj['noi_o_hien_nay']['chi_tiet'])
                                       value="{{$obj['noi_o_hien_nay']['chi_tiet']}}"
                                       @endisset
                                       type="text" class="form-control">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Tỉnh thành
                                </label>
                                <select style="width: 100%;height: 35px"
                                        onchange="return APPLICATION._changeCity(jQuery(this).val(),'#noi_o_hien_nay_huyen','Chọn quận huyện')"
                                        class="select-search select-md"
                                        name="obj[noi_o_hien_nay][tinh][key]">
                                    <option value="">Chọn tỉnh thành</option>
                                    @foreach($allCity as $key=>$value)
                                        <option @if(isset($obj['noi_o_hien_nay']['tinh']['key']) && $obj['noi_o_hien_nay']['tinh']['key']==$value->slug) selected
                                                @endif value="{{$value->slug}}">{{$value->name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="" class="control-label">
                                    Quận, huyện
                                </label>
                                <select style="width: 100%;height: 35px"
                                            id="noi_o_hien_nay_huyen" class="select-search"
                                            name="obj[noi_o_hien_nay][huyen][key]"
                                            onchange="return APPLICATION._changeCity(jQuery('#noi_o_hien_nay_huyen').val(),'#noi_o_hien_nay_xa','Chọn xã phường')">
                                        <option value="">Chọn quận huyện</option>
                                        <?php $districtOfCity = []; ?>

                                        @if(isset($obj['noi_o_hien_nay']['tinh']['code']))
                                            <?php
                                            $districtOfCity = \App\Http\Models\Location::getAllLocationByParent($obj['noi_o_hien_nay']['tinh']['code']);
                                            ?>
                                        @endif

                                        @if(isset($districtOfCity) && $districtOfCity)
                                            @foreach($districtOfCity as $key=>$value)
                                                <option @if(isset($obj['noi_o_hien_nay']['huyen']['key']) && $obj['noi_o_hien_nay']['huyen']['key']==$value->slug) selected
                                                        @endif value="{{$value->slug}}">{{$value->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                            </div>
                        </div>
                        <div class="col">
                                <div class="form-group">
                                    <label for="" class="control-label">
                                        Xã, Phường
                                    </label>
                                    <select style="width: 100%;height: 35px"
                                                id="noi_o_hien_nay_xa" class="select-search"
                                                name="obj[noi_o_hien_nay][xa][key]">
                                            <option value="">Chọn xã phường</option>
                                            <?php $townOfDistrict = [];?>
                                            @if(isset($obj['noi_o_hien_nay']['huyen']['code']))
                                                <?php
                                                $townOfDistrict = \App\Http\Models\Location::getAllLocationByParent($obj['noi_o_hien_nay']['huyen']['code']);
                                                ?>
                                            @endif
                                            @if(isset($townOfDistrict) && $townOfDistrict)
                                                @foreach($townOfDistrict as $key=>$value)
                                                    <option @if(isset($obj['noi_o_hien_nay']['xa']['key']) && $obj['noi_o_hien_nay']['xa']['key']==$value->slug) selected
                                                            @endif value="{{$value->slug}}">{{$value->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                </div>
                            </div>
                    </div>
                    {{--END NƠI Ở HIỆN NAY--}}
                    <div class="d-flex">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="" class="control-label">Email</label>
                                <input type="text" name="obj[emails]"
                                @if(isset($obj['emails']))
                                value="{{collect($obj['emails'])->map(function($item){return $item['value'];})->implode(',')}}"
                                data-value="{{collect($obj['emails'])->map(function($item){return ['id'=>$item['value'], 'text'=>$item['value']];})}}"
                                @endif
                                multiple tags
                                class="select2-createable" placeholder="Nhập thông tin....">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="" class="control-label">SĐT</label>
                                <input type="text" name="obj[phones]"
                                       @if(isset($obj['phones']))
                                       value="{{collect($obj['phones'])->map(function($item){return $item['value'];})->implode(',')}}"
                                       data-value="{{collect($obj['phones'])->map(function($item){return ['id'=>$item['value'], 'text'=>$item['value']];})}}"
                                       @endif
                                       multiple tags
                                       class="select2-createable" placeholder="Nhập thông tin....">
                            </div>
                        </div>
                        <div class="col-3">
                            @include('forms/input-group-text',['placeholder'=>'Nhập thông tin...','field'=>['label'=>'Nickname','key'=>'nickname']])
                        </div>
                    </div>

                    <div class="d-flex">
                        <div class="col-3">
                            <div class="form-group">
                                <label for="" class="control-label">Tình trạng công việc </label>
                                <select type="text" class="form-control"
                                        name="obj[tinh_trang_cong_viec]"
                                        id="tinh_trang_cong_viec">
                                    <option @if(isset($obj['tinh_trang_cong_viec']) && $obj['tinh_trang_cong_viec']  == "Đang công tác") selected
                                            @endif value="Đang công tác">Đang công tác
                                    </option>
                                    <option @if(isset($obj['tinh_trang_cong_viec']) && $obj['tinh_trang_cong_viec']  == "Đã nghỉ việc") selected
                                            @endif value="Đã nghỉ việc">Đã nghỉ việc
                                    </option>
                                    <option @if(isset($obj['tinh_trang_cong_viec']) && $obj['tinh_trang_cong_viec']  == "Tạm nghỉ")  selected
                                            @endif value="Tạm nghỉ">Tạm nghỉ
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="" class="control-label">Phòng ban hiện tại</label>
                                <select name="obj[department][id]"
                                        class="select-search select-md"
                                        onchange="return APPLICATION._changeDepartment(jQuery(this).val(),'#qua_trinh_cong_tac_{{@$key}}_position','Chọn chức vụ')">
                                    <option value="">Chưa lựa chọn</option>
                                    @isset($allDepartment)
                                        @foreach($allDepartment->sortBy('name') as $val)
                                            <option @if(isset($obj['department']['id']) && $obj['department']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}
                                                @if(isset($val['parent_dep']['name']))
                                                    ({{$val['parent_dep']['name']}})
                                                @endif
                                            </option>

                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                                <div class="form-group">
                                    <label for="" class="control-label">Chức vụ hiện tại</label>
                                    <select name="obj[position][id]"
                                            class="select-search select-md" id="qua_trinh_cong_tac_{{@$key}}_position">
                                        <option value="">Lựa chọn phòng ban trước...</option>
                                        @isset($allPosition)
                                            @if(@$obj)
                                                @foreach(collect($allPosition)->sortBy('name')->filter(function ($item) use($obj){
                                                    if(isset($obj['position']['id']) &&  $item['id']){
                                                       if( $obj['position']['id'] == $item['id']){
                                                        return true;
                                                       };
                                                    }
                                                    return @$item['department']['id'] == @$obj['department']['id'];

                                                }) as $val)
                                                    <option @if(isset($obj['position']['id']) && $obj['position']['id']==$val['_id'])
                                                            selected
                                                            @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                                @endforeach
                                            @endif
                                        @endisset
                                    </select>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex">
        <div class="col-md-8">
            <div class="panel panel-white">

                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th width="0">Giấy tờ tuỳ thân</th>
                        <th width="0">Số</th>
                        <th width="0">Ngày cấp</th>
                        <th width="0">Nơi cấp</th>
                        <th width="1" class="text-right"><a onclick="clone_giay_to()"
                                                            class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>
                    </tr>
                    </thead>
                    <tbody id="obj_giay_to">
                    @if(isset($obj['giay_to']))
                        @foreach(@$obj['giay_to'] as $key=>$value)
                            <tr>
                                <td>
                                    <div>
                                        <select name="obj[giay_to][{{$key}}][id]" class="select-search select-md"
                                                placeholder="Nhập giấy tờ..."
                                        >
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_GIAY_TO;}) as $val)
                                                <option @if(isset($value['id'])  && $value['id']==$val['_id']))
                                                        selected
                                                        @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                            @endforeach
                                        </select>

                                    </div>

                                </td>
                                <td>
                                    <input type="text" class="form-control input-type-number"
                                           name="obj[giay_to][{{$key}}][so]"
                                           value="{{@$value['so']}}"
                                           placeholder="Nhập số">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[giay_to][{{@$key}}][ngay_cap]"
                                           placeholder="Ngày cấp..."
                                           @if(isset($value['ngay_cap']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_cap'])}}"@endif
                                    >

                                </td>
                                <td>
                                    <select style="width: 100%;height: 35px"
                                            class="select-search select-md"
                                            name="obj[giay_to][{{$key}}][noi_cap]">
                                        <option value="">Chọn tỉnh thành</option>
                                        @foreach($allCity as $city)
                                            <option @if(isset($value['noi_cap']) && $value['noi_cap']==$city->slug) selected
                                                    @endif value="{{$city->slug}}">{{$city->name}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-right"><i class="icon-trash text-danger"
                                                          onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endif

                    </tbody>
                </table>


            </div>
            <div class="panel panel-white">
                {{--<div class="panel-heading">--}}
                {{--<h6 class="panel-title">Các tổ chức đoàn thể</h6>--}}
                {{--</div>--}}
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th width="100">Tổ chức đoàn thể</th>
                        <th width="100">Ngày gia nhập</th>
                        <th width="100">Chức vụ cao nhất</th>

                        <th width="1" class="text-right"><a onclick="clone_to_chuc_doan_the()"
                                                            class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>
                    </tr>
                    </thead>
                    <tbody id="obj_to_chuc_doan_the">
                    @isset($obj['to_chuc_doan_the'])
                        @foreach(@$obj['to_chuc_doan_the'] as $key=>$value)
                            <tr>
                                <td>
                                    <select name="obj[to_chuc_doan_the][{{$key}}][id]" class="select-search select-md"
                                            placeholder="Nhập giấy tờ..."
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_TO_CHUC_DOAN_THE;}) as $val)
                                            <option @if(isset($value['id'])  && $value['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker"
                                           name="obj[to_chuc_doan_the][{{$key}}][ngay_gia_nhap]"
                                           @if(isset($value['ngay_gia_nhap']))
                                           value="{{\App\Elibs\Helper::showMongoDate($value['ngay_gia_nhap'])}}" @endif
                                           placeholder="Chọn ngày...">

                                </td>
                                <td>
                                    <select name="obj[to_chuc_doan_the][{{$key}}][chuc_vu][id]"
                                            class="select-search select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUC_VU_DOAN_THE;}) as $val)
                                            <option @if(isset($value['chuc_vu']['id'])  && $value['chuc_vu']['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>
                                        @endforeach
                                    </select>

                                </td>
                                <td class="text-right"><i class="icon-trash text-danger"
                                                          onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endisset

                    </tbody>
                </table>


            </div>
        </div>
        <div class="col-md-4">
            {{--Tài khoản ngân hàng--}}
            <div class="panel panel-white">
                {{--<div class="panel-heading">--}}
                {{--<h6 class="panel-title">Giấy tờ</h6>--}}
                {{--<div class="heading-elements">--}}
                {{--<div class="heading-btn-group">--}}


                {{--</div>--}}

                {{--</div>--}}
                {{--</div>--}}

                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th width="300">Tài khoản ngân hàng</th>
                        <th width="200">Số</th>
                        <th width="1" class="text-right"><a onclick="clone_tk_ngan_hang()"
                                                            class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>
                    </tr>
                    </thead>
                    <tbody id="obj_tk_ngan_hang">
                    @isset($obj['tk_ngan_hang'])
                        @foreach(@$obj['tk_ngan_hang'] as $key=>$value)
                            <tr>
                                <td>
                                    <select name="obj[tk_ngan_hang][{{$key}}][id]" class="select-search select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NGAN_HANG;}) as $val)
                                            <option @if(isset($value['id'])  && $value['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control input-type-number"
                                           name="obj[tk_ngan_hang][{{$key}}][so]"
                                           value="{{@$value['so']}}"
                                           placeholder="Nhập số">

                                </td>
                                <td><i class="icon-trash text-danger"
                                       onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endisset

                    </tbody>
                </table>


            </div>
            {{--Liên hệ khác--}}
            <div class="panel panel-white">
                <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th width="300">Liên hệ khác</th>
                        <th width="200">Thông tin</th>
                        <th width="1" class="text-right"><a onclick="clone_lien_he_khac()"
                                                            class="btn btn-link text-primary">
                                <i class="icon-add-to-list"></i></a></th>
                    </tr>
                    </thead>
                    <tbody id="obj_lien_he_khac">
                    @isset($obj['lien_he_khac'])
                        @foreach(@$obj['lien_he_khac'] as $key=>$value)
                            <tr>
                                <td>
                                    <select name="obj[lien_he_khac][{{$key}}][id]" class="select-search select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_LIEN_HE_KHAC;}) as $val)
                                            <option @if(isset($value['id'])  && $value['id']==$val['_id']))
                                                    selected
                                                    @endif value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control"
                                           name="obj[lien_he_khac][{{$key}}][thong_tin]"
                                           value="{{@$value['thong_tin']}}"
                                           placeholder="Nhập thông tin...">

                                </td>
                                <td><i class="icon-trash text-danger"
                                       onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>

                        @endforeach
                    @endisset

                    </tbody>
                </table>
            </div>
            <input type="hidden" name="obj[files_thong_tin_co_ban]" value="">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">
                        <a class="" data-toggle="collapse" href="#gFile" aria-expanded="true">File đính kèm</a>
                    </h6>
                </div>
                <div id="gFile" class="panel-collapse collapse in" aria-expanded="true">
                    <div class="panel-body no-padding-bottom">
                        <div id="documentFileRegion">
                            @if(isset($obj['files_thong_tin_co_ban']) && $obj['files_thong_tin_co_ban'] && is_array($obj['files_thong_tin_co_ban']))
                                @foreach($obj['files_thong_tin_co_ban'] as $key=> $file)
                                    <div class="form-group js-document-container" id="file_{{$key}}">

                                        <div class="col-md-12">
                                            <div class="input-group">
                                                <div style="display: flex">
                                                    <input type="text" style="z-index: 0" class="form-control "
                                                           name="obj[files_thong_tin_co_ban][name][]"
                                                           @isset($file['name']) value="{{$file['name']}}" @endisset
                                                           placeholder="Tên file">
                                                    <input type="text" style="z-index: 0" readonly=""
                                                           class="form-control js-document-file"
                                                           name="obj[files_thong_tin_co_ban][path][]"
                                                           @isset($file['path']) value="{{$file['path']}}" @endisset

                                                           placeholder="File tài liệu">
                                                </div>

                                                <div class="input-group-btn">
                                                    <a target="_blank"
                                                       href="{{\App\Http\Models\Media::getFileLink($file['path'])}}"
                                                       class="btn btn-default js-document-link">Xem file</a>
                                                    <a onclick="_removeFile('#file_{{$key}}')" href="javascript:void(0)"
                                                       class="btn btn-default js-document-del"><i
                                                                class="icon-trash text-danger"></i> </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="col-md-11 p-3  text-right">
                                <button id="pickfiles" type="button" class="btn bg-primary btn-xs"><i
                                            class="fa fa-plus"></i>
                                    Thêm file
                                </button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>

        </div>
    </div>


    <div class="col-md-12 text-right">
        <button class="btn btn-primary" id="save-button"
                onclick="return MNG_POST.update('{{admin_link('/staff/_save_tab_info')}}','#postInputForm');">Lưu lại
        </button>
    </div>


</form>

<div style="display: none" id="uploadFileItemClone">
    <div class="form-group js-document-container">
        <div class="col-md-12">
            <div class="input-group"><span
                        style="position: absolute;display: none; z-index: 100; right: 200px; top: 8px;"
                        class="js-document-loading"><i
                            class="fa fa-spinner fa-spin"></i> Đang upload vui lòng đợi....</span>
                <div style="display: flex">
                    <input type="text" style="z-index: 0" class="form-control "
                           name="obj[files_thong_tin_co_ban][name][]" value="" placeholder="Tên file">
                    <input type="text" style="z-index: 0" readonly="" class="form-control js-document-file"
                           name="obj[files_thong_tin_co_ban][path][]" value="" placeholder="File tài liệu">

                </div>
                <div class="input-group-btn">
                    <a target="_blank" href="" class="btn btn-default js-document-link">Xem file</a>
                    <a href="javascript:void(0)" class="btn btn-default js-document-del"><i
                                class="icon-trash text-danger"></i> </a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    INPUT_NUMBER()

    function clone_giay_to() {
        let index = $('#obj_giay_to tr').length
        let temp_select_class = "select-seart-" + Number(new Date())
        let tmp = `<tr>
        <td>
            <select name="obj[giay_to][${index}][id]" class="select-search select-md ${temp_select_class} "
                                                placeholder="Nhập giấy tờ..."
                                        >
                                            <option value="">Chưa lựa chọn</option>
                                            @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_GIAY_TO;}) as $val)
            <option value="{{$val['_id']}}">{{$val['name']}}</option> @endforeach
            </select>

        </td>
        <td>
            <input type="text" class="form-control input-type-number" name="obj[giay_to][${index}][so]"

                                               placeholder="Nhập số">

                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker"
                                               name="obj[giay_to][${index}][ngay_cap]"
                                               placeholder="Ngày cấp..."


                                    </td>
                                    <td>
                                        <select style="width: 100%;height: 35px"
                                                class="${temp_select_class} select-md"
                                                name="obj[giay_to][${index}][noi_cap]">
                                            <option value="">Chọn tỉnh thành</option>
                                            @foreach($allCity as $city)
            <option city="{{$city->slug}}">{{$city->name}}</option>
                                            @endforeach
            </select>
        </td>
        <td class='text-right' onclick="confirm('Bạn có muốn xoá không?')&&$(this).parents('tr').remove()"><i class="icon-trash text-danger"></i></td>
    </tr>
`
        $('#obj_giay_to').append(tmp)

        $(`.${temp_select_class}`).select2()
        INPUT_NUMBER()
    }

    function clone_tk_ngan_hang() {
        let index = $('#obj_tk_ngan_hang tr').length
        let temp_select_class = "select-search-" + Number(new Date())
        let tmp = `<tr>
                                <td>
                                   <select name="obj[tk_ngan_hang][${index}][id]" class="${temp_select_class} select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_NGAN_HANG;}) as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control input-type-number" name="obj[tk_ngan_hang][${index}][so]"

                                           placeholder="Nhập số">

                                </td>
                                <td class='text-right'><i class="icon-trash text-danger"
                                       onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>
`
        $('#obj_tk_ngan_hang').append(tmp)
        DATE_PICKER_INIT()
        INPUT_NUMBER()
        $(`.${temp_select_class}`).select2()
    }

    function clone_lien_he_khac() {
        let index = $('#obj_lien_he_khac tr').length
        let temp_select_class = "select-search-" + Number(new Date())
        let tmp = `<tr>
                                <td>
                                   <select name="obj[lien_he_khac][${index}][id]" class="${temp_select_class} select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_LIEN_HE_KHAC;}) as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>

                                        @endforeach
            </select>
        </td>
        <td>
            <input type="text" class="form-control" name="obj[lien_he_khac][${index}][thong_tin]"

                                           placeholder="Nhập thông tin">

                                </td>
                                <td class='text-right'><i class="icon-trash text-danger"
                                       onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
                                </td>
                            </tr>
`
        $('#obj_lien_he_khac').append(tmp)
        DATE_PICKER_INIT()
        INPUT_NUMBER()
        $(`.${temp_select_class}`).select2()
    }

    function clone_to_chuc_doan_the() {
        let index = $('#obj_to_chuc_doan_the tr').length

        let temp_select_class = "select-seart-" + Number(new Date())
        let tmp = `<tr>
                            <td>
                            <select name="obj[to_chuc_doan_the][${index}][id]" class="${temp_select_class} select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_TO_CHUC_DOAN_THE;}) as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>
                                        @endforeach
            </td>
            <td>
                <input type="text" class="form-control datepicker"
                       name="obj[to_chuc_doan_the][${index}][ngay_gia_nhap]"

                                       placeholder="Chọn ngày...">

                            </td>
                            <td>
                                <select name="obj[to_chuc_doan_the][${index}][chuc_vu][id]" class="${temp_select_class} select-md"
                                    >
                                        <option value="">Chưa lựa chọn</option>
                                        @foreach($allStaffDataList->filter(function($item){return $item['type'] == \App\Http\Models\MetaData::STAFF_CHUC_VU_DOAN_THE;}) as $val)
            <option  value="{{$val['_id']}}">{{$val['name']}}</option>
                                        @endforeach

            </td>
            <td class="text-right"><i class="icon-trash text-danger"
                   onclick="confirm('Bạn có muốn xoá không? ') &&$(this).parents('tr').remove()"></i>
            </td>
        </tr>
`
        $('#obj_to_chuc_doan_the').append(tmp)
        //$('.datepicker').datepicker()
        DATE_PICKER_INIT()

        $(`.${temp_select_class}`).select2()
    }


    DATE_PICKER_INIT();
    SELECT2_CREATABLE();

    MNG_MEDIA.uploadInit({
        loading_element: '#loading_upload',
        input_element: '#document_file',
        link_element: '#document_file_link'
    });
    MNG_MEDIA.uploadAvatarInit();


    function _removeFile($element) {
        bootbox.confirm("File của bạn sẽ bị xóa.<br/>Bạn có chắc chắn muốn thực hiện hành động này?", function (result) {
            if (result) {
                $($element).remove();
            }
        });
    }

    $('#tinh_trang_cong_viec').on('change',
        function () {
            let $this = $(this)
            if($this.val() !=="Đang công tác"){
                if (confirm("Thay đổi trạng thái công tác sang 'Đã nghỉ việc' hoặc 'Tạm nghỉ' sẽ xoá tài khoản của nhân viên trên hệ thống. Bạn đồng ý thay đổi đổi trạng thái công tác?")) {

                }else{
                    $this.val("Đang công tác")
                }
            }
        }
    )
</script>
