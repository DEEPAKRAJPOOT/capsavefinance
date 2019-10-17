<div class="card Recommended-conect ligh-grey  Recommended Connection ">
        <div class="card-header">Recommended Connections</div>
        <div class="card-body ">
            <div class="Recommended-info">
                <div class="recommended">
                    @foreach($recommendedConnection as $list)
                            <div class="recommended-header mb-3">
                                <div class="recommended-header-left">
                                @if(isset($list->user_photo) && !empty($list->user_photo))
                                <img src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt($list->user_photo)]) }}"  alt="{{$list->first_name}} {{$list->last_name}}" />
                                @else
                                <img src="{{asset('assets/images/user01.png')}}"  id="wizardPicturePreview" alt="{{$list->first_name}} {{$list->last_name}}" />
                                @endif
                                </div>
                                <div class="recommended-header-right">
                                    <h5 class="card-title"><a href="{{route('public_profile',['user_id' => $list->id])}}">{{$list->first_name}} {{$list->last_name}} </a></h5>
                                    <p class="card-date">{{$list->first_name}}</p>
                                    <!--<p class="card-text"> 0 Followers</p>-->
                                    <!--<ul class="list-unstyled user-info">
                                        <li><a href="#"><i class="fa fa-add-user"></i></a></li>
                                        <li><a href="#"><i class="fa fa-msg"></i></a></li>
                                    </ul>-->
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>