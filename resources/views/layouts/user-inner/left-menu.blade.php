<!-- SIDEBAR - START -->
<?php

$userKycId= $benifinary['user_kyc_id'];  
$corpUserId=  $benifinary['corp_user_id'];
$isBycompany=$benifinary['is_by_company'];


?>
<div class="list-section">
                    <div class="kyc">
                        <h2>{{trans('master.personalProfile.kyc')}}</h2>
                        <p class="marT15 marB15">{{trans('master.personalProfile.text')}}</p>
                        @if($corpUserId==0)
                        <ul class="menu-left">
                            <li><a class="" href="{{url('/')}}/dashboard">DashBoard</a></li>
                            <li><a class="" href="{{route('changepassword')}}">Change Password</a></li>

                        </ul>
                        @endif
                        <ul class="menu-left">
                            <li>
                            @if(Route::currentRouteName()=="family_information")
                          
                                <a class="" href="{{route('profile')}}">Personal Information</a>
                                <a class="active marL15" href="{{route('family_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Family Information</a>
                                <a class="marL15" href="{{route('residential_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Residential Information</a>
                            
                            @elseif(Route::currentRouteName()=="residential_information")
                            
                                <a class="" href="{{route('profile')}}">Personal Information</a>
                                <a class="marL15" href="{{route('family_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Family Information</a>
                                <a class="active marL15" href="{{route('family_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Residential Information</a>
                            
                            @elseif(Route::currentRouteName()=="profile")
                            
                                <a class="active" href="{{route('profile',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Personal Information</a>
                          
                            @else
                            
                                <a class="" href="{{route('profile',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Personal Information</a>
                            
                            @endif
                            </li>
                            <li>
                            @if(Route::currentRouteName()=="professional_information")
                                 <a class="active" href="{{route('professional_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Professional Information</a>
                            @elseif(Route::currentRouteName()=="commercial_information")
                               <a class="" href="{{route('professional_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Professional Information</a>
                               <a class="active marL15" href="{{route('commercial_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">For Sole Proprietorship/ Self Employed</a>
                            @else
                            
                            <a href="{{route('professional_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Professional Information</a>
                            @endif
                            </li>
                            <li>
                            @if(Route::currentRouteName()=="financial_information")
                                <a class="active" href="{{route('financial_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Financial Information</a>
                            @else
                            
                            <a class="" href="{{route('financial_information',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Financial Information</a>
                            @endif
                            </li>
                           
                            <li>
                            @if(Route::currentRouteName()=="upload_document")

                               <li><a class="active" href="{{route('upload_document',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Documents</a></li>
                            
                            
                            @else
                            
                             <li><a href="{{route('upload_document',['user_kyc_id'=>$userKycId,'corp_user_id'=>$corpUserId,'is_by_company'=>$isBycompany])}}">Documents</a></li>
                            @endif
                            </li>
                           
                        </ul>
                        @if($corpUserId==0)
                        <ul class="menu-left">
                        <li>
                            <a  href="{{ route('frontend_logout') }}" onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            Sign Out
                            </a>
                            <form id="logout-form" action="{{ route('frontend_logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>

                        </ul>
                        @endif


                    </div>
                </div>
<!--  SIDEBAR - END -->