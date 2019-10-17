<section class="profile-bg">
    <div class="container">
        <div class="row">
            <div class="col-md-10">
                <div class="profile-img">
                    @if(isset(Auth::user()->user_photo) && !empty(Auth::user()->user_photo))
                    <img src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt(Auth::user()->user_photo)]) }}" alt="Profile image" />
                    @else
                    <img src="{{asset('frontend/outside/images/icon-user-default.png')}}" alt="Profile image" />
                    @endif
                </div>

                <div class="profile-head">
                    <span class="edit-btn">
                        <a href="{{route('edit_profile')}}" style="color: #00edff;font-weight:bold;font-size:11px;"> Edit Your Profile</a> <i class="fa fa-pencil" aria-hidden="true"></i>
                        @if(Auth::user()->is_scout == 1)
                        @if(Auth::user()->repotation_score != NULL)
                        <div class="rep-score"><span> {{ Auth::user()->repotation_score }} </span>
                            <lable>Reputation Score</lable>
                        </div>
                        @endif
                        @endif
                    </span>
                    <h5>{{ucwords(Auth::user()->first_name.' '.Auth::user()->last_name)}}</h5>
                    <h6 class="profile-location">
                        <!--<span class="country">Nationality:</span> <span class="city">{{ucwords(Helpers::getCountryById(Auth::user()->country_id)->country_name)}}</span>
                        <span class="line">|</span>-->
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <span class="city-name">{{ Auth::user()->addr1 ? Auth::user()->addr1 : ''}}
                            {{Auth::user()->addr2 ?', '.Auth::user()->addr2 : ''}}
                            {{Auth::user()->area ?', '. Auth::user()->area : ''}}
                            {{Auth::user()->city ?', '. Auth::user()->city : '' }}</span>
                    </h6>
                    <p class="profile-email"><i class="fa fa-envelope-o" aria-hidden="true"></i><span>{{ Auth::user()->email ? Auth::user()->email : ''}}&nbsp;&nbsp;</span>
                        @if(Auth::user()->status == 1)
                        <span style="background: green;padding: 2px;border-radius: 14%;">
                            <i class="fa fa-check" aria-hidden="true"></i>Verified
                        </span>
                        @endif
                        @if(Auth::user()->status == 3)
                        <!--<span style="background: gray;padding: 2px;border-radius: 14%;color:white">
                                <i class="fa fa-times" aria-hidden="true"></i>Not Verified
                            </span>-->
                        @endif
                    </p>
                    <p class="profile-email"><i class="fa fa-phone" aria-hidden="true"></i><span>{{ Auth::user()->phone ? Auth::user()->phone : 'NA'}}</span></p>

                </div>
            </div>
            <!--            <div class="col-md-1 mb-4 add-right">
                <div class="add-img">
                    <a href="{{route('add_right')}}">
                        <img src="{{ asset('frontend/inside/images/add-right.png') }}" class="img-fluid">
                        <span>Add Right</span>
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
