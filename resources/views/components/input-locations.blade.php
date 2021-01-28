@if(!isset($allCity))
    @php
        $allCity = \App\Http\Models\Location::getAllCity();
    @endphp
@endif
<div class="form-group">
    <label class="control-label col-md-2">Tỉnh thành</label>
    <div class="col-md-10 pr-0 pl-0">
        <div class="col-md-4">
            <select style="width: 100%;height: 35px"
                    onchange="return APPLICATION._changeCity(jQuery(this).val(),'#location-district','Chọn quận huyện')"
                    class="select-search" name="obj[locations][city]">
                <option value="">Chọn tỉnh thành</option>
                @foreach($allCity as $key=>$value)
                    <option @if(isset($obj['locations']['province']['key']) && $obj['locations']['province']['key']==$value->slug) selected
                            @endif value="{{$value->slug}}">{{$value->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select style="width: 100%;height: 35px"
                    onchange="return APPLICATION._changeCity(jQuery(this).val(),'#location-town','Chọn xã phường')"
                    id="location-district" class="select-search"
                    name="obj[locations][district]">
                <option value="">Chọn quận huyện</option>
                @if(isset($districtOfCity) && $districtOfCity)
                    @foreach($districtOfCity as $key=>$value)
                        <option @if(isset($obj['locations']['district']['key']) && $obj['locations']['district']['key']==$value->slug) selected
                                @endif value="{{$value->slug}}">{{$value->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-4">
            <select style="width: 100%;height: 35px" id="location-town"
                    class="select-search" name="obj[locations][town]">
                <option value="">Xã phường</option>
                @if(isset($townOfDistrict) && $townOfDistrict)
                    @foreach($townOfDistrict as $key=>$value)
                        <option @if(isset($obj['locations']['town']['key']) && $obj['locations']['town']['key']==$value->slug) selected
                                @endif value="{{$value->slug}}">{{$value->name}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>
