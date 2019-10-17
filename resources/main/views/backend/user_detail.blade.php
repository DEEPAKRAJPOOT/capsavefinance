@extends('layouts.admin')

@section('content')

<div class="prtm-content">
    <div class="prtm-page-bar">
         <h3>User Detail</h3> </li>
    </div>
    <div class="order-detail">
        <div class="prtm-block">
           
            <div class="prtm-block-content prtm-block-no-gutter">
               
                <div class="tab-content pad-all-lg">
                    <div id="order" class="tab-pane fade in active">
                        <div class="row">
                            
                                    <div class="prtm-block-title mrgn-b-md">
                                        <div class="caption">
                                            <h3 class="text-capitalize">Basic Details</h3> 
                                        <hr></div>
                                        
                                    </div>
                           
                                    <div class="prtm-block-content">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-9">
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>First name:</strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['first_name']) ? $userData['first_name'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Last name : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['last_name']) ? $userData['last_name'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Email: </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['email']) ? $userData['email'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Phone: </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['phone']) ? $userData['phone'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Address 1: </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['addr1']) ? $userData['addr1'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Address 2 : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span>{{isset($userData['addr2']) ? $userData['addr2'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Area : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span>{{isset($userData['area']) ? $userData['area'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>City : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['city']) ? $userData['city'] : '' }}</span> </div>
                                                </div>
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Country : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($userData['country_name']) ? $userData['country_name'] : '' }}</span> </div>
                                                </div>
                                                
                                                
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Zip code : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span >{{isset($userData['zip_code']) ? $userData['zip_code'] : '' }}</span> </div>
                                                </div>
                                                
                                                <div class="row mrgn-b-md">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Created Date : </strong></span> </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"> <span >{{isset($userData['created_at']) ? $userData['created_at'] : '' }}</span> </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 hidden-sm text-center"> <span class="icon"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> </div>
                                        </div>
                                    </div>
                                </div>
                               
                    </div>
                    
                   
                </div>
            </div>

            <div class="prtm-block-content prtm-block-no-gutter">

                <div class="tab-content pad-all-lg">
                    <div id="order" class="tab-pane fade in active">
                        <div class="row">

                            <div class="prtm-block-title mrgn-b-md">
                                <div class="caption">
                                    <h3 class="text-capitalize">Education Details</h3> 
                                    <hr></div>

                            </div>
                            @php
                            $i = 1
                            @endphp
                            
                            @if(count($educations) > 0)
                              @foreach ($educations as $education)
                              <div class="caption">
                                  <h4 class="text-capitalize"><i>Education Details {{ $i }}</i></h4> 
                            <hr></div>
                            <div class="prtm-block-content">
                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>University Name:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($education['university']) ? $education['university'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Course Name : </strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($education['course']) ? $education['course'] : '' }} </span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Dates Attended From Year : </strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value"></span> {{isset($education['date_attended_from_year']) ? $education['date_attended_from_year'] : '' }}</div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Dates Attended To Year : </strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($education['date_attended_to_year']) ? $education['date_attended_to_year'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Additional information: </strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value"> {{isset($education['remarks']) ? $education['remarks'] : '' }}</span> </div>
                                        </div>

                                    </div>
                                    <div class="col-md-3 hidden-sm text-center"> <span class="icon"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> </div>
                                </div>
                            </div>
                              @php
                               $i++
                              @endphp
                              @endforeach
                              @endif
                        </div>

                    </div>

                </div>
            </div>
            
            <div class="prtm-block-content prtm-block-no-gutter">

                <div class="tab-content pad-all-lg">
                    <div id="order" class="tab-pane fade in active">
                        <div class="row">

                            <div class="prtm-block-title mrgn-b-md">
                                <div class="caption">
                                    <h3 class="text-capitalize">Skill Details</h3> 
                                    <hr></div>

                            </div>
                            @php
                            $i = 1
                            @endphp
                            
                            @if(count($skills) > 0)
                              @foreach ($skills as $skills)
                              <div class="caption">
                                  <h4 class="text-capitalize"><i>Skill Details {{ $i }}</i></h4> 
                            <hr></div>
                            <div class="prtm-block-content">
                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong >Skill Name:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span  class="value">{{Helpers::getSkillName($skills['skill_id'])}}</span> </div>
                                        </div>
                                       
                                        <div class="mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong style="margin-left: -1em">Skill Level : </strong></span> </div>
                                            @if($skills['level'] == 1)
                                               @php  $ii = 'Beginner'@endphp
                                            @endif
                                            @if($skills['level'] == 2)
                                              @php   $ii = 'Intermideate'@endphp
                                            @endif
                                            @if($skills['level'] == 3)
                                               @php  $ii = 'Professional'@endphp
                                            @endif
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{$ii}} </span> </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 hidden-sm text-center"> <span class="icon"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> </div>
                                </div>
                            </div>
                              @php
                               $i++
                              @endphp
                              @endforeach
                              @endif
                        </div>

                    </div>

                </div>
            </div>
            <div class="prtm-block-content prtm-block-no-gutter">

                <div class="tab-content pad-all-lg">
                    <div id="order" class="tab-pane fade in active">
                        <div class="row">

                            <div class="prtm-block-title mrgn-b-md">
                                <div class="caption">
                                    <h3 class="text-capitalize">Research Publication</h3> 
                                    <hr></div>

                            </div>
                            <div class="prtm-block-content">
                            @php
                            $r = 1
                            @endphp
                            
                            @if(count($researches) > 0)
                              @foreach ($researches as $researche)
                              
                              
                              <div class="caption">
                                    <h4 class="text-capitalize">Research Publication {{ $r }}</h4> 
                            <hr></div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Title:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($researche['title']) ? $researche['title'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Journal/Magazine:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($researche['journal_magazine']) ? $researche['journal_magazine'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Month of Publication:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($researche['publication_month']) ? $researche['publication_month'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Year of Publication:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($researche['publication_year']) ? $researche['publication_year'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Attachment:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($researche['attachment']) ? $researche['attachment'] : '' }}</span> </div>
                                        </div>
                                        
                                         
                                        
                                    </div>
                                    <div class="col-md-3 hidden-sm text-center"> <span class="icon"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> </div>
                                </div>
                              @php
                               $r++
                              @endphp
                              @endforeach
                              @endif
                            </div>
                        </div>

                    </div>


                </div>
            </div>
            <div class="prtm-block-content prtm-block-no-gutter">

                <div class="tab-content pad-all-lg">
                    <div id="order" class="tab-pane fade in active">
                        <div class="row">

                            <div class="prtm-block-title mrgn-b-md">
                                <div class="caption">
                                    <h3 class="text-capitalize">Awards & Honors</h3> 
                                    <hr></div>

                            </div>
                            @php
                            $a = 1
                            @endphp
                            
                            @if(count($awards) > 0)
                              @foreach ($awards as $award)
                              <div class="caption">
                                    <h4 class="text-capitalize">Awards & Honors {{ $a }}</h4> 
                            <hr></div>
                            <div class="prtm-block-content">
                                <div class="row">
                                    <div class="col-sm-12 col-md-9">
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Title:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($award['title']) ? $award['title'] : '' }}</span> </div>
                                        </div>
                                        <div class="row mrgn-b-md">
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span><strong>Brief Description:</strong></span> </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6"> <span class="value">{{isset($award['title']) ? $award['title'] : '' }}</span> </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 hidden-sm text-center"> <span class="icon"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span> </div>
                                </div>
                            </div>
                              @php
                               $a++
                              @endphp
                              @endforeach
                              @endif
                        </div>
                            
                       @if($is_scout == 1)
                      
                        <form action="{{route('admin_approved')}}" id="appForm" method="post">
                            <input type = "hidden" value = "{{$userData['id']}}" name = "user_id">
                            <input type = "hidden" value = "{{$userData['email']}}" name = "email_id">
                            <input @if($userData['is_admin_approved'] == 1) checked='checked'  @endif  type="radio"  value ="1" name = "is_approved">Approved &nbsp;&nbsp;
                            <input type="hidden" name="_token" value="{{ csrf_token()}}">
                            <input type="hidden" name="is_approved_stack" value="{{ $userData['is_admin_approved']}}">
                            <input @if($userData['is_admin_approved'] == 2) checked='checked'  @endif type="radio" value ="2" name = "is_approved">DisApproved <br>
                            <textarea class ="form-control" placeholder="Reason" name = "reason">{{$userData['reason']}}</textarea>
                            <input class ="btn btn-primary text-center" type = "submit" name = "submit" value = "Set">
                        </form>
                       
                    @endif
                     
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@section('pageTitle')
User Detail
@endsection

