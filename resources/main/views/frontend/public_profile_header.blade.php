<section class="profile-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="profile-img">
                    @if(isset($userData->user_photo) && !empty($userData->user_photo))
                    <img src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt($userData->user_photo)]) }}" alt="Profile image" />
                    @else
                    <img src="{{asset('frontend/outside/images/icon-user-default.png')}}" alt="Profile image" />
                    @endif
                </div>

                <div class="profile-head">
                                        
                    <h5>{{ucwords($userData->first_name.' '.$userData->last_name)}}</h5>
                    <h6 class="profile-location">
                        <!--<span class="country">Nationality:</span> <span class="city">{{ucwords(Helpers::getCountryById($userData->country_id)->country_name)}}</span>
                        <span class="line">|</span>-->
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <span class="city-name">{{ $userData->addr1 ? $userData->addr1 : ''}}
                            {{$userData->addr2 ?', '.$userData->addr2 : ''}}
                            {{$userData->area ?', '. $userData->area : ''}}
                            {{$userData->city ?', '. $userData->city : '' }}</span>
                    </h6>
                    <!--<p class="profile-email"><i class="fa fa-envelope-o" aria-hidden="true"></i><span>{{ $userData->email ? $userData->email : ''}}</span></p>
                    <p class="profile-email"><i class="fa fa-phone" aria-hidden="true"></i><span>{{ $userData->phone ? $userData->phone : 'NA'}}</span></p>-->

                </div>
            </div>
<!--            <div class="col-md-1 mb-4 add-right">
                <div class="add-img">
                    <a href="{{route('add_right')}}">
                        <img src="{{ asset('frontend/inside/images/add-right.png') }}" class="img-fluid">
                        <span>Add Rights</span>
                    </a>
                </div>

            </div>-->
        </div>
        <div class="row">
            <div class="col-10">

            </div>
            <div class="col-2"></div>
        </div>
    </div>
</section>