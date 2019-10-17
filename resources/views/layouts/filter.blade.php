<div class="col-md-3">
    <div class="filter  mt-0">
        <div class="filter-heading  pb-2 p-0">
            Filter
        </div>
        <form class="  justify-content-center">
            <div class="sortby col-01 mb-3">
                <div class="title">Sort By</div>
                
                <div class="custom-control custom-checkbox">
                    <input data="Popular" @if(request()->rfilter == 1) checked @endif type="checkbox" value="1" class="custom-control-input common_selector sort_by"  id="check01">
                    <label class="custom-control-label" for="check01">Popular</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" value="2" data="Newest" class="custom-control-input common_selector sort_by" id="check03">
                    <label class="custom-control-label" for="check03">Newest</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" value="3" data="Featured" class="custom-control-input common_selector sort_by" id="check04">
                    <label class="custom-control-label" for="check04">Featured</label>
                </div>                
            </div>
            <div class="type-sec mb-3 col-01 ">
                <div class="title">Type</div>
                @php
                $types = Helpers::getRightTypeList()@endphp
                @foreach($types as $type)
                <div class="custom-control custom-checkbox">
                    <input type="checkbox"  value="{{$type->id}}" data="{{$type->title}}" class="custom-control-input common_selector type" id="t{{$type->id}}">
                    <label class="custom-control-label" for="t{{$type->id}}">{{$type->title}}</label>
                </div>
                @endforeach
            </div>

            <div class="group-div mb-3 col-01 language">
                <div class="title">Group</div>
                @php
                $skills = Helpers::getSkillsList()@endphp
                @foreach($skills as $skill)
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" value="{{$skill->id}}" data="{{$skill->title}}" class="custom-control-input common_selector group"  id="g{{$skill->id}}">
                    <label class="custom-control-label" for="g{{$skill->id}}">{{$skill->title}}</label>
                </div>
                @endforeach
            </div>
            <div class="col-md-12 group-div">
                <ul>
                    <li>
                        <div class="form-group clearfix row">
                            <div class="title">Current Price Range</div>
                            <div id="slider-container"></div>
                        </div>
                    </li>
                    <li class="clearfix row">
                        <div class="form-group clearfix row amount-box">
                            <div class="col-sm-6">
                                <label for="amount-from">Min: </label>
                                <input type="text" disabled="" class="form-control" id="amount-from" onkeypress="return isNumberKey(event)" value="596314">
                            </div>
                            <div class="col-sm-6">
                                <label for="amount-to" >Max: $</label>
                                <input type="tel" disabled="" class="form-control" id="amount-to" onkeypress="return isNumberKey(event)" value="33000000" style="padding-left:42px !important; width:82px;">
                                <input type="hidden" name="hidden_minimum_price" id="hidden_minimum_price" value="0" />
                                <input type="hidden" name="hidden_maximum_price" id="hidden_maximum_price" value="500000" />
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

        </form>
    </div>
</div>