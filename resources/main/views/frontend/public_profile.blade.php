@extends('layouts.app')

@section('content')
<!-- SECTION -->
@include('frontend.public_profile_header')
<!-- SECTION -->
<section>
    <div class="container mt--5 public-profile">
        <div class="row">
            <div class="col-md-8 profile-border">
                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-followers1-tab" data-toggle="tab" href="#nav-followers1" role="tab" aria-controls="nav-followers1" aria-selected="false">About Us</a>
                         <a class="nav-item nav-link " id="nav-owned-tab" data-toggle="tab" href="#nav-owned" role="tab" aria-controls="nav-owned" aria-selected="false">Owned Rights</a>
                        <a class="nav-item nav-link" id="nav-connections-tab" data-toggle="tab" href="#nav-connections" role="tab" aria-controls="nav-connections" aria-selected="false">Connections</a>
                        <!--<a class="nav-item nav-link" id="nav-followers-tab" data-toggle="tab" href="#nav-followers" role="tab" aria-controls="nav-followers" aria-selected="false">Followers</a>-->
                    </div>
                </nav>
                <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                    <div class="tab-pane fade  show active" id="nav-followers1" role="tabpanel" aria-labelledby="nav-followers1-tab">
                        
                        <div id="accordion">
                        <div class="card">

                            <div class="card-header">
                                <a class="card-link" data-toggle="collapse" href="#collapsethree">
                                    Education Details</a>
                            </div>
                             <div id="collapsethree" class="collapse" data-parent="#accordion">
                            <div class="card-body  pb-0">
                               @if(count($educationData)>0)
                              @foreach($educationData as $eduData)
                                <div ng-repeat="edu in  educationdetail" class="ng-scope">
                                        <div class="body-text ">{{$eduData->university}}</div>
                                        <div class="body-text deg">{{$eduData->course}}</div>
                                        <div class="body-text "><i class="ng-binding"> {{$eduData->date_attended_from_year}} -  {{$eduData->date_attended_to_year}}</i></div>

                                        <br>
                                        </div>
                              @endforeach
                                @else
                            <div ng-repeat="edu in  educationdetail" class="ng-scope">
                            No Data Present
                            </div>
                            @endif
                            </div>

                            </div>

                        </div>
                            
                        <div class="card">

                            <div class="card-header">
                                <a class="card-link" data-toggle="collapse" href="#collapseOne">
       Skills
      </a>
                                
                            </div>
                            <div id="collapseOne" class="collapse " data-parent="#accordion">
                            <div class="card-body  pb-0">
                                @if(count($skillData)>0)
                                @foreach($skillData as $skillDataVal)
                                @php $arrOtheSkill = Helpers::getSkillOtherName($skillDataVal->id) @endphp
                                <p>
                                {{Helpers::getSkillName($skillDataVal->skill_id)}}
                                {{($arrOtheSkill) ? '('.$arrOtheSkill.')' : ''}}  :
                                @if($skillDataVal->level == 1)
                                Beginner
                                @elseif($skillDataVal->level == 2)
                                Intermediate
                                @elseif($skillDataVal->level == 3)
                                Pofessional
                                @endif
                               </p>
                                
                               <br>


                                @endforeach
                                    @else
                                    <p>No Data Present</p>
                                @endif
                                </div>

                           </div>

                        </div>
                        
                           
                        <div class="card">

                            <div class="card-header">
                                <a class="card-link" data-toggle="collapse" href="#collapsetwo">
                                    Research Publication</a>
                            </div>
                            <div id="collapsetwo" class="collapse" data-parent="#accordion">
                            <div class="card-body  pb-0">
                                
                                @if(count($researchData)>0)
                                @foreach($researchData as $researchDataVal)
                                        <div ng-repeat="job in jobdetail" class="ng-scope">
                                        <div class="body-text ">{{$researchDataVal->title}}</div>
                                        <div class="body-text deg">Journal/Magazine:{{$researchDataVal->journal_magazine}}</div>
                                        <div class="body-text "><i class="ng-binding">Year of Publication{{$researchDataVal->publication_year}}</i></div>
                                        @if($researchDataVal->attachment != NULL) 
                                        <div class="body-text "><a href="{{url('download-research-file/'.$researchDataVal->user_id.'/'.$researchDataVal->attachment)}}"><i class="fa fa-download" aria-hidden="true"></i></a></div>
                                        @endif
                                        <br>
                                            </div>
                                    @endforeach
                                    @else
                                    <div ng-repeat="job in jobdetail" class="ng-scope">
                                    No Data Present
                                        </div>
                                    @endif
                               
                            </div>

                           </div>

                        </div>
                        
                         <div class="card">

                            <div class="card-header">
                                <a class="card-link" data-toggle="collapse" href="#collapsefour">
                                    Awards & Honors </a>
                            </div>
                             <div id="collapsefour" class="collapse " data-parent="#accordion">
                            @if(count($awardsData)> 0)
                            @foreach($awardsData as $adData)
                            <div class="card-body  pb-4">

                            
                           <div ng-repeat="edu in  educationdetail" class="ng-scope">
                                <div class="body-text ">{{$adData->title}}</div>
                                <div class="body-text deg">{{$adData->description}}</div>
                            <br>
                            </div>
                            </div>
                            @endforeach
                            @else
                            <div class="card-body  pb-4">

                            No Data Present
                            </div>
                            @endif





                            </div>

                        </div>
                    </div>
                    </div>
                    <div class="tab-pane fade " id="nav-owned" role="tabpanel" aria-labelledby="nav-owned-tab">

                        <div class="card">

                            <div class="card-header">
                                Owned Rights
                            </div>

                                <div class="card-body  pb-0">
                                @if(count($ownedRights) > 0)
                                @foreach($ownedRights as $patent)
                                <div class="recommended">
                                    <div class="recommended-header">
                                        <div class="recommended-header-left">

                                             @if ($patent->thumbnail!= NULL)
                                                <img src="{{ route('view_inside_register_profile',['folder'=> 'rightsThumbnails/'.$patent->user_id, 'file' => encrypt("60X60_".$patent->thumbnail)])}}" width='60'>
                                                @else
                                                <img src="{{ asset('/frontend/inside/images/home/thumb-1.png') }}">
                                                @endif


                                            <!--<img src="{{ asset('/frontend/inside/images/home/thumb-1.png') }}">-->

                                        </div>
                                        <div class="recommended-header-right">
                                        @if($patent->ready_to_sale == 1 
                                                    && $patent->valid_scout_score > 0
                                                    && $patent->invalid_scout_score == 0)
                                            <div class="icon-tick"></div>
                                            <div class='spiralContainer icon-tick'>
                                                <div class='spiral'>
                                                    <img src="{{ asset('frontend/inside/images/home/right-icon.png') }}"></div>
                                            </div>
                                                @endif
                                            <h5 class="card-title">
                                                <a href="{{route('right_details',['user_id' => $patent->user_id, 'right_id' => $patent->id])}}">{{$patent->title}}</a></h5>
                                            <p class="card-text">{{ ucwords($userData->first_name.' '.$userData->last_name) }}</p>
                                            <p class="card-date">{{ Helpers::getDateByFormat($patent->created_at) }}</p>
                                        </div>
                                    </div>
                                    <div class="recommended-body">
                                        <p>{{$patent->description}}</p>
                                    </div>
                                    <div class="recommended-footer">
                                        <div class="recommended-footer-right">
                                            <!--<ul>
                                                <li><i class="fa fa-caret-up" aria-hidden="true"></i> 12</li>
                                                <li><i class="fa fa-caret-down" aria-hidden="true"></i> 0</li>
                                                <li><i class="fa fa-thumbs-up" aria-hidden="true"></i> 12</li>
                                                <li><img src="{{ asset('/frontend/inside/images/home/right-icon.png') }}"></li>
                                                <li>Published on: December 4, 2018</li>
                                            </ul>-->


                                            <ul class="pr-3" style="display: inline-flex;width: 100%;">
                                                @if($patent->ready_to_sale == 1)
                                                <li style="display: flex;"><i class="fa fa-caret-up" aria-hidden="true"></i> {{$patent->valid_scout_score}}</li>
                                                <li><i class="fa fa-caret-down" aria-hidden="true"></i>{{$patent->invalid_scout_score}}</li>
                                                @endif
                                                <li class="add-star doLikeUnlike" id="{{ $patent->id.'~'.$patent->user_id}}" style="display: flex;"><i class="fa fa-thumbs-up  @if($patent->total_likes > 0) active @endif" aria-hidden="true"></i><span id="rcmd_{{ $patent->id }}">{{$patent->total_likes}}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                No Record Found
                                @endif<!-- Item -->
                            </div>

                           

                            <!--<div class="card-footer text-center">
                                <a href="" class="btn btn-default btn-sm"> View All <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>
                            </div>-->

                        </div>


                    </div>


                    <div class="tab-pane fade" id="nav-connections" role="tabpanel" aria-labelledby="nav-connections-tab">
                        <div class="card">

                            <div class="card-header">
                                Connections
                            </div>
                            <div class="card-body  pb-0">
<!--                                @if(count($connectionDatas) > 0)
                                    @foreach($connectionDatas as $connectionData)
                                        @if($connectionData->from_user_id == $userId)
                                            @php $userDetailId = $connectionData->to_user_id; $requester = true;@endphp
                                        @elseif($connectionData->to_user_id == $userId)
                                            @php $userDetailId = $connectionData->from_user_id; $requester = false; @endphp
                                        @endif
                                @php
                                $users = Helpers::getUserDetail($userDetailId);
                                @endphp

                                <div class="recommended">
                                    <div class="recommended-header">
                                        <a href="{{route('public_profile',['user_id' => $users->id])}}" title="{{ ucwords($users->first_name.' '.$users->last_name) }}">
                                        <div class="recommended-header-left">
                                        @if(isset($users->user_photo) && !empty($users->user_photo))
                                        <img src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt($users->user_photo)]) }}" class="img-xs1 rounded-circle" title="{{ ucwords($users->first_name.' '.$users->last_name) }}" />
                                        @else
                                        <img src="{{asset('frontend/outside/images/icon-user-default.png')}}" class="img-xs1 rounded-circle" id="wizardPicturePreview" title="{{ ucwords($users->first_name.' '.$users->last_name) }}" />
                                        @endif
                                        </div>
                                        </a>
                                        <div class="recommended-header-right">
                                            <p class="card-text"><a href="{{route('public_profile',['user_id' => $users->id])}}" title="{{ ucwords($users->first_name.' '.$users->last_name) }}">{{ ucwords($users->first_name.' '.$users->last_name) }}</a></p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                
                                @endif-->

                               <!-- Approve Connection-->
                                
                               
                                @if(count($approveConnection) > 0)
                                    @foreach($approveConnection as $connectionData)
                                        @if($connectionData->from_user_id == $userId)
                                            @php $userDetailId = $connectionData->to_user_id; $requester = true;@endphp
                                        @elseif($connectionData->to_user_id == $userId)
                                            @php $userDetailId = $connectionData->from_user_id; $requester = false; @endphp
                                        @endif
                                @php
                                $users = Helpers::getUserDetail($userDetailId);
                                @endphp

                                <div class="recommended">
                                    <div class="recommended-header">
                                        <a href="{{route('public_profile',['user_id' => $users->id])}}" title="{{ ucwords($users->first_name.' '.$users->last_name) }}">
                                        <div class="recommended-header-left">
                                        @if(isset($users->user_photo) && !empty($users->user_photo))
                                        <img src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt($users->user_photo)]) }}" class="img-xs1 rounded-circle" title="{{ ucwords($users->first_name.' '.$users->last_name) }}" />
                                        @else
                                        <img src="{{asset('frontend/outside/images/icon-user-default.png')}}" class="img-xs1 rounded-circle" id="wizardPicturePreview" title="{{ ucwords($users->first_name.' '.$users->last_name) }}" />
                                        @endif
                                        </div>
                                        </a>
                                        <div class="recommended-header-right">
                                            <p class="card-text"><a href="{{route('public_profile',['user_id' => $users->id])}}" title="{{ ucwords($users->first_name.' '.$users->last_name) }}">{{ ucwords($users->first_name.' '.$users->last_name) }}</a></p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                
                                @endif<!-- Item -->

                        @if(count($connectionDatas) == 0 && count($approveConnection) == 0)
                            No Connection Found
                        @endif

                            </div>

                            <!--<div class="card-footer text-center">
                                <a href="" class="btn btn-default btn-sm"> View All <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>
                            </div>-->

                        </div>
                    </div>
                    <!--<div class="tab-pane fade" id="nav-followers" role="tabpanel" aria-labelledby="nav-followers-tab">
                        <div class="card">

                            <div class="card-header">
                                Followers
                            </div>
                            <div class="card-body  pb-0">

                            </div>

                            <div class="card-footer text-center">
                                <a href="" class="btn btn-default btn-sm"> View All <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>
                            </div>

                        </div>
                    </div>-->


                            

                </div>

            </div>

            <div class="col-md-4">
                <div class="card Similar box-shadow-div">
                    <div class="card-body  p-0">
                        <ul class="scouted mt-2">
                            <li>
                                <div class="scouted-left"> Connections</div>
                                <div class="scouted-right">{{$totalConnection}}</div>
                            </li>
                            <!--<li class="pb-0">
                                <div class="scouted-left">Followers</div>
                                <div class="scouted-right">50</div>
                            </li>-->
                        </ul>
                    </div>
                    <hr>
                  
                </div>
                <div class="card Feeds ">
                    
                    <div class="card-body pt-4">
                       
                        <form action="">
                          @php $condata = helpers::getConnectionData($userId, Auth::user()->id)@endphp
                        
                         
                                <div class="form-group text-center">
                                <!--<button type="submit" class="btn btn-primary">Message</button>
                                <button type="submit" class="btn btn-info">Follow</button>-->
                               @if( count($condata) > 0 && ($condata['is_seen'] == '0' || $condata['is_seen'] == '1') && $condata['status'] == 0)
                                <button type="button" class="btn btn-info removeconn" id="removeconnection_request">Notification Sent </button>
                               @endif
                               @if(count($condata) > 0 && $condata['is_seen'] == '1' && $condata['status'] == '1' )
                                <button type="button"  class="btn btn-info accveconn" id="accconnection_request">Notification Accept </button>
                               @endif
                                @if($condata == '0')
                                <button type="button" class="btn btn-info conn" id="connection_request">Connect <i style = "display:none" class=" refres fa fa-refresh" aria-hidden="true"></i></button>
                               @endif
                                <button type="button" style ="display:none" class="btn btn-info removeconn" id="removeconnection_request">Notification Sent </button>
                                <input type="hidden" name="from_user_id" id="from_user_id" value="{{$userId}}">
                            </div>
                        </form>

                    </div>
                </div>

                <!--<div class="card Recommended-conect ligh-grey">
                    <div class="card-header">
                        Mutual Connection

                    </div>

                    <div class="card-body ">
                        <div class="Recommended-info">
                            <div class="recommended">
                                <div class="recommended-header mb-3">
                                    <div class="recommended-header-left"><img src="assets/images/user01.png"></div>
                                    <div class="recommended-header-right">
                                        <h5 class="card-title">Malte Janduda
                                        </h5>
                                        <p class="card-date">Inventor</p>
                                        <p class="card-text"> 0 Followers</p>

                                        <ul class="list-unstyled user-info">
                                            <li><a href="#"><i class="fa fa-add-user"></i></a></li>
                                            <li><a href="#"><i class="fa fa-msg"></i></a></li>
                                        </ul>


                                    </div>
                                </div>
                                <div class="recommended-header mb-3">
                                    <div class="recommended-header-left"><img src="assets/images/user01.png"></div>
                                    <div class="recommended-header-right">
                                        <h5 class="card-title">Malte Janduda
                                        </h5>
                                        <p class="card-date">Inventor</p>
                                        <p class="card-text"> 0 Followers</p>

                                        <ul class="list-unstyled user-info">
                                            <li><a href="#"><i class="fa fa-add-user"></i></a></li>
                                            <li><a href="#"><i class="fa fa-msg"></i></a></li>
                                        </ul>


                                    </div>
                                </div>
                                <div class="recommended-header mb-3">
                                    <div class="recommended-header-left"><img src="assets/images/user01.png"></div>
                                    <div class="recommended-header-right">
                                        <h5 class="card-title">Malte Janduda
                                        </h5>
                                        <p class="card-date">Inventor</p>
                                        <p class="card-text"> 0 Followers</p>

                                        <ul class="list-unstyled user-info">
                                            <li><a href="#"><i class="fa fa-add-user"></i></a></li>
                                            <li><a href="#"><i class="fa fa-msg"></i></a></li>
                                        </ul>


                                    </div>
                                </div>


                            </div>

                        </div>



                    </div>
                </div> -->


                



            </div>
        </div>
    </div>

    <section>
        <!-- / END SECTION -->
<!-- / END SECTION -->
@endsection
@section('pageTitle')
Profile
@endsection

@section('jscript')
<script type="text/javascript" src="{{ asset('frontend/inside/js/dashboard.js') }}"></script>
<script>
var messages = {
    set_connection: "{{ route('send_connection') }}",
    _token: "{{ csrf_token() }}",
};
</script>
@endsection
