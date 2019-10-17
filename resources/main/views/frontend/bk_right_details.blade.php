@extends('layouts.app')

@section('content')
<section>
    <div class="banner-section">
        <div class="search-box">
            <!--Grid column-->
            @include('layouts.global_search')
            <div class="col-md-1 mb-4 add-right">
                <div class="add-img">
                    <a href="{{route('add_right')}}">
                        <img src="{{ asset('frontend/inside/images/add-right.png') }}" alt="add-right" class="img-fluid">
                        <span>Add Rights</span>
                    </a>
                </div>
            </div>
            <!--Grid column-->
        </div>
    </div>
</section>
<section>
    @php $isPutOnStack = Helpers::getStackByID(Auth::user()->id, $rightDetail->id) @endphp

    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-md-9">
                <div class="panel-default mb-3 box-shadow-div pt-3">
                    <ul class="holder-history">
                        <li>
                            <div class="row">
                                <div class="holder-img col-md-8">
                                    <h2>{{ ucfirst($rightDetail->title) }}</h2>
                                    @if(!file_exists(storage_path("app/appDocs/rightsThumbnails/".$rightDetail->user_id."/"."715X135_".$rightDetail->thumbnail)))
                                    <img src="{{ asset('/frontend/inside/images/home/code-editor.png') }}">
                                    @elseif(!empty($rightDetail->thumbnail))
                                    <div class = "cropimg">
                                        <img src="{{ route('view_inside_register_profile',['folder'=> 'rightsThumbnails/'.$rightDetail->user_id, 'file' => encrypt("715X135_".$rightDetail->thumbnail)]) }}">
                                    </div>
                                    @else
                                    <img src="{{ asset('frontend/inside/images/home/code-editor.png') }}">
                                    @endif
                                </div>
                                <div class="col-md-4 hover-effect">

                                    <div class="recommended recommended-home">
                                        <div class="holder-user-bg mb-3">

                                            <div class="comment-left">
                                                @php $isPutOnStack = Helpers::getStackByID(Auth::user()->id, $rightDetail->id) @endphp

                                                @if(isset($rightDetail->user_photo) && !empty($rightDetail->user_photo))
                                                <img src="{{ route('view_inside_register_profile',['folder'=> 'profile', 'file' => encrypt($rightDetail->user_photo)]) }}" alt="Profile image" />
                                                @else
                                                <img src="{{asset('frontend/outside/images/icon-user-default.png')}}" alt="Profile image" />
                                                @endif
                                            </div>
                                            <div class="comment-right mt-3">
                                                <h4 class="font-italic">By: <a href="{{route('public_profile',['user_id' => $rightDetail->user_id])}}">{{ ucfirst($rightDetail->first_name.' '.$rightDetail->last_name) }}</a></h4>
                                                <p class="date font-italic">{{ Helpers::getDateByFormat($rightDetail->created_at) }}
                                                    <span>

                                                        {{ $rightDetail->role }}

                                                    </span>
                                                </p> 
                                                @php $bounty = Helpers::getBounty($rightDetail->id); @endphp
                                                @if($bounty > 0 && ((int) Auth::user()->is_scout == 1))

                                                <p class="card-value">Bounty: ${{$bounty}} </p>
                                                @endif  

                                            </div>

                                        </div>

                                        @php $scoutcountPositive = Helpers::ifscountPositive($rightDetail->valid_scout_score,$rightDetail->invalid_scout_score); @endphp

                                        <div class=" Similar " style="overflow: hidden;width: 100%;">
                                            <div class="card-header"><!--Validated-->
                                                <div class="recommended-footer-right">
                                                    @if($rightDetail->ready_to_sale == 1 
                                                    && $rightDetail->valid_scout_score > 0
                                                    && $rightDetail->invalid_scout_score == 0)
                                                    <div class="icon-tick"></div>
                                                    <div class='spiralContainer icon-tick'>
                                                        <div class='spiral'><img src="{{ asset('frontend/inside/images/home/right-icon.png') }}"></div>
                                                    </div>
                                                    @endif
                                                    <ul class="pr-3" style="display: inline-flex;width: 100%;">
                                                        @if($rightDetail->ready_to_sale == 1)
                                                        <li style="display: flex;"><i class="fa fa-caret-up" aria-hidden="true"></i> {{$rightDetail->valid_scout_score}}</li>
                                                        <li><i class="fa fa-caret-down" aria-hidden="true"></i>{{$rightDetail->invalid_scout_score}}</li>
                                                        @endif
                                                        <li class="add-star doLikeUnlike" id="{{ $rightDetail->id.'~'.$rightDetail->user_id}}" style="display: flex;"><i class="fa fa-thumbs-up  @if($rightDetail->total_likes > 0) active @endif" aria-hidden="true"></i><span id="rcmd_{{ $rightDetail->id }}">{{$rightDetail->total_likes}}</span></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            @php $bounty = Helpers::getBounty($rightDetail->id); @endphp
                                            @if($bounty > 0 && ((int) Auth::user()->is_scout == 1))

                                            <!--                                    <div class="card-body  p-0">
                                                                                    <ul class="scouted">
                                                                                        <li>
                                                                                            <div class="scouted-left card-value">Bounty</div>
                                                                                            <div class="scouted-right">${{$bounty}}</div>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>-->
                                            @endif



                                            <div class="   rightdetailprice row" >

                                                <div class="col-md-6 p-0">
                                                    @if($rightDetail->ready_to_sale == 1)
                                                    @if($rightDetail->is_exclusive_purchase == 1 && $rightDetail->is_non_exclusive_purchase == 1)
                                                    <a  class="btn btn-x-sm btn-success   mb-1 rightpricesell" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$rightDetail->id])}}" data-height="600px" data-width="100%" data-placement="top">Buy<br> <span class="tooltip-2">| ${{ $rightDetail->non_exclusive_purchase_price}} <i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span> <span class="tooltip-2"> ${{ $rightDetail->exclusive_purchase_price}} <i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span> </a>
                                                    @elseif($rightDetail->is_exclusive_purchase == 1)
                                                    <a  class="btn btn-x-sm btn-success mb-1 rightpricesell" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$rightDetail->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                        Buy | ${{ $rightDetail->exclusive_purchase_price}} <span class="tooltip-2"> <i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span></a>
                                                    @elseif($rightDetail->is_non_exclusive_purchase == 1)
                                                    <a  class="btn btn-x-sm btn-success mb-1 rightpricesell" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$rightDetail->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                        Buy | ${{ $rightDetail->non_exclusive_purchase_price}} <span class="tooltip-2"> <i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span></a>
                                                    @endif

                                                    @endif <br>
                                                    <a href="" class="btn btn-info btn-x-sm  ownrship-trail-btn" data-target=".animate1" data-toggle="modal" data-ui-class="a-zoom"> Ownership Trail <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>

                                                </div>

                                                <div class="col-md-6 p-0">
                                                    
                                                    @if( (Auth::user()->is_scout == 1) && (Auth::user()->is_admin_approved == 1) &&  (Auth::user()->id!=$rightDetail->user_id))
                                                    @if($isPutOnStack == false && $validFlag == 1)
                                                    <a class="stake-btn btn btn-success mb-1 btn-x-sm pull-right" data-toggle="modal" data-target="#putStakePopup" data-url =" {{route('term_condition',['user_id' => $rightDetail->user_id, 'right_id'=>$rightDetail->id])}}" data-height="140px" data-width="100%" data-placement="top" title="Once you stake $1, then you will be allowed to evaluate this right">Stake It</a>
                                                    @endif
                                                    @if($validFlag == 0 && ($rightDetail->valid_scout_score >= $rightDetail->invalid_scout_score))
                                                    <!--<a  data-toggle="modal" data-target="#ReportPopup" data-url =" {{route('open_report_popup',['user_id' => $rightDetail->user_id, 'right_id'=>$rightDetail->id])}}" data-height="500px" data-width="100%" data-placement="top" class="btn btn-warning btn-sm  report-btn btn-x-sm">Report It</a>-->
                                                    @endif
                                                    @endif<br>
                                                    <a href="" class="btn   btn-x-sm pull-right" data-toggle="modal" data-target=".animate2" data-ui-class="a-zoom" style="background-color: #17a2b8;"> Validation Trail <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>

                                                </div>
                                            </div>
                                        </div>


                                        <!--                                    <div class="card-footer text-right rightdetailprice" >
                                                                                 
                                                                                   
                                                                                   
                                                                                  
                                                                            
                                                                                
                                                                            <div class="comment-right mt-3 ">
                                                                               
                                                                            </div>
                                                                            
                                                                            </div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="holder-user-bg mt-3">
                            </div>
                            </div>

                        </li>
                        <li>
                            <h3>Description:</h3>
                            <div class="comment more">
                                {{ $rightDetail->description }}  
                            </div>
                        </li>
                        <li>
                            <h3>Tags:</h3>
                            @php 
                            $tags = explode(',' , $rightDetail->keywords)
                            @endphp
                            @foreach ($tags as $item)
                            <span class="badge badge-default"> {{$item}} </span>
                            @endforeach
                        </li>
                        <li>
                            <h3>Attachments:</h3>
                            @foreach ($rightDetailAttachments as $rightDetailAttachment)
                            <div class="thumb"><img src="{{ asset('frontend/inside/images/home/thumb-3.png') }}" alt="">
                            </div>
                            @endforeach
                        </li>
                        <li>
                            <h3 class="mt-2">Other Url</h3>
                            <p>{{$rightDetail->url}}</p>
                        </li>
                    </ul>
                </div>

                <div class="panel-default mb-3">
                    @if( ($isPutOnStack != false) || ($rightDetail->user_id == Auth::user()->id))    
                    <ul class="holder-history">
                        <li>
                            <div class="project-tab">
                                <nav>
                                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active" id="nav-ip-tab" data-toggle="tab" href="#nav-ip" role="tab" aria-controls="nav-ip" aria-selected="true">Files containing details for this Right</a>
                                        <!--                                        <a class="nav-item nav-link" id="nav-holder-tab" data-toggle="tab" href="#nav-holder" role="tab" aria-controls="nav-holder" aria-selected="false">Quick Portfolio</a>-->
                                    </div>
                                </nav>
                                <div class="tab-content" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="nav-ip" role="tabpanel" aria-labelledby="nav-ip-tab">
                                        <table class="table" cellspacing="0">

                                            {{-- @if(count($fileList)>0)
                                               @php $fileList =  array_slice($fileList,0, -2)@endphp
                                               --}} 
                                            @if(count($onlyfile)== 0)   
                                            <tbody>
                                                <!--                                                @foreach($fileList as $file)
                                                                                                <tr>
                                                                                                    <td><img src="{{ asset('frontend/inside/images/icon/file-icon.svg') }}">{{$file}}</td>
                                                                                                    <td>
                                                                                                        <div class="recommended-footer-right width-auto pull-left">
                                                                                                             <ul class="pr-3">
                                                                                                                <a href="{{ route('view_inside_register_profile',['folder'=> 'rightsAttachments/'.$rightDetail->user_id.'/'.$rightDetail->id.'/ExtractZipFiles/'.$myFolder, 'file' => encrypt($file)]) }}" download = "{{$file}}"> <i class="fa fa-download" aria-hidden="true"></i></a>
                                                                                                             </ul>
                                                                                                        </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                                @endforeach-->

                                                <tr>
                                                    <td><img src="{{ asset('frontend/inside/images/icon/file-icon.svg') }}">{{$at}}</td>
                                                    <td>
                                                        <div class="recommended-footer-right width-auto pull-left">
                                                            <ul class="pr-3">
                                                                <a href="{{ route('download_zip_file', ['user_id'=> $rightDetail->user_id, 'right_id'=>$rightDetail->id, 'file' => $rightDetail->attachment])}}"> <i class="fa fa-download" aria-hidden="true"></i></a>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr> 

                                            </tbody>
                                            @endif

                                            @if(count($onlyfile)>0)
                                            <tbody>

                                                <!--                                                @foreach($onlyfile as $key => $files)
                                                                                                    <tr>
                                                                                                        <td><img src="{{ asset('frontend/inside/images/icon/file-icon.svg') }}">{{$files->attachment}}</td>
                                                                                                        <td>
                                                                                                            <div class="recommended-footer-right width-auto pull-left">
                                                                                                                 <ul class="pr-3">
                                                                                                                <a href="{{ route('view_inside_register_profile',['folder'=> 'rightsAttachments/'.$rightDetail->user_id.'/'.$rightDetail->id, 'file' => encrypt($files->attachment)]) }}" download = "{{$files->attachment}}"> <i class="fa fa-download" aria-hidden="true"></i></a>
                                                                                                             </ul>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach    -->

                                                <tr>
                                                    <td><img src="{{ asset('frontend/inside/images/icon/file-icon.svg') }}">{{$at}}</td>
                                                    <td>
                                                        <div class="recommended-footer-right width-auto pull-left">
                                                            <ul class="pr-3">
                                                                <a href="{{ route('download_zip_file', ['user_id'=> $rightDetail->user_id, 'right_id'=>$rightDetail->id, 'file' => $rightDetail->attachment])}}"> <i class="fa fa-download" aria-hidden="true"></i></a>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr> 

                                            </tbody>
                                            @endif
                                        </table>
                                    </div>
                                    <div class="tab-pane fade" id="nav-holder" role="tabpanel" aria-labelledby="nav-holder-tab">
                                        <table class="table" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td><img src="{{ asset('frontend/inside/images/icon/file-icon.svg') }}">Hyperledger Extension Based Wallet</td>
                                                    <td>
                                                        <div class="recommended-footer-right width-auto pull-left">
                                                            <ul>
                                                                <li><i class="fa fa-caret-up" aria-hidden="true"></i> 12</li>
                                                                <li><i class="fa fa-caret-down" aria-hidden="true"></i> 0</li>
                                                                <li class="add-star"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span>0</span></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td>2 days ago</td>
                                                </tr>
                                                <tr>
                                                    <td><img src="{{ asset('frontend/inside/images/icon/file-icon.svg') }}">WordPress plugin for PayPal integration</td>
                                                    <td>
                                                        <div class="recommended-footer-right width-auto pull-left">
                                                            <ul>
                                                                <li><i class="fa fa-caret-up" aria-hidden="true"></i> 12</li>
                                                                <li><i class="fa fa-caret-down" aria-hidden="true"></i> 0</li>
                                                                <li class="add-star"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span>0</span></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td>5 days ago</td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </li>
                    </ul>
                    @endif
                    <ul class="comment-box" id="main-cbox">
                        <li>

                            @if($isPutOnStack != false)
                            @if($isPutOnStack->is_validate == 0 && $validFlag == 1)
                            <a  class="btn btn-warning btn-sm" data-toggle="modal" data-target="#validClaimPopup" data-url =" {{route('scout_valid_claim',['user_id' => $rightDetail->user_id, 'right_id'=>$rightDetail->id])}}" data-height="500px" data-width="100%" data-placement="top">Evaluate It</a>
                            <!--                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal-comment">Invalidate &amp; Claim Bounty with Incentive</button>-->
                            @endif
                            @endif    

                        </li>
                    </ul>
                </div>

                <div class="panel-default mb-3">

                    <ul class="comment-box">
                        <li>
                            <form method="post" id="comment_form">
                                <button type="submit" value="submit" class="send-file"><img src="{{ asset('frontend/inside/images/icon/send-icon.svg') }}"></button>
                                <textarea rows="1" name="right_comment" id="right_comment" placeholder="Write Comment"></textarea>
                                <input type="hidden" id="right_id" name="right_id" value="{{$rightDetail->id}}">
                                <input type="hidden" name="right_user_id" value="{{$rightDetail->user_id}}">
                                <input type="hidden" id="comment_id" name="comment_id" value="">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                    <span id="comment_message"></span>
                    <ul class="comment" id="display_comment"></ul>
                </div>

            </div>
            <div class="col-md-3">

                <!--                <div class="card Similar box-shadow-div">
                                    <div class="card-header">Validated
                                        <div class="recommended-footer-right">
                                            <div class="icon-tick"></div>
                                            <div class='spiralContainer icon-tick'>
                                                <div class='spiral'><img src="{{ asset('frontend/inside/images/home/right-icon.png') }}"></div>
                                            </div>
                                            <ul class="pr-3">
                                                <li><i class="fa fa-caret-up" aria-hidden="true"></i> {{$rightDetail->valid_scout_score}}</li>
                                                <li><i class="fa fa-caret-down" aria-hidden="true"></i>{{$rightDetail->invalid_scout_score}}</li>
                                                <li class="add-star doLikeUnlike" id="{{ $rightDetail->id.'~'.$rightDetail->user_id}}"><i class="fa fa-thumbs-up @if($rightDetail->total_likes > 0) active @endif" aria-hidden="true"></i><span id="rcmd_{{ $rightDetail->id }}">{{$rightDetail->total_likes}}</span></li>
                                            </ul>
                                        </div>
                
                                    </div>
                                    <div class="card-body  p-0">
                                        <ul class="scouted">
                                            <li><span class="">Price:</span> <span class="font-lg">$750</span></li>
                                            <li>
                                                <div class="scouted-left">Validator Incentive</div>
                                                <div class="scouted-right">$1490</div>
                                            </li>
                
                                            @php $bounty = Helpers::getBounty($rightDetail->id); @endphp
                                             @if($bounty > 0 && ((int) Auth::user()->is_scout == 1))
                                            <li>
                                                <div class="scouted-left">Bounty</div>
                                                <div class="scouted-right">${{$bounty}}</div>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                       <div class="card-footer text-right">
                                       @if($rightDetail->ready_to_sale == 1)
                                        @if($rightDetail->is_exclusive_purchase == 1)
                                       <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$rightDetail->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                           Buy | ${{ $rightDetail->exclusive_purchase_price}}</a>
                                       @elseif($rightDetail->is_non_exclusive_purchase == 1)
                                       <a href="" class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#myModal">
                                           Buy | ${{ $rightDetail->non_exclusive_purchase_price}}</a>
                                       @endif
                                       @endif
                
                
                                       @php $arrRightTransactions = Helpers::getRightTransactionHistory($rightDetail->id); @endphp
                                       @if(count($arrRightTransactions) > 0)
                                        <a href="" class="btn btn-default btn-x-sm" data-toggle="modal" data-target=".animate1" data-ui-class="a-zoom"> Validatings <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>
                                        <a href="" class="btn btn-info btn-x-sm" data-target=".animate1" data-toggle="modal" data-ui-class="a-zoom"> Ownership Trail <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>
                                       @endif
                                        
                                    </div>
                                </div>-->


                <div class="card box-shadow-div Similar-sec">
                    <div class="card-header">
                        Similar Rights
                        <span class="tooltip-1" data-placement="bottom"><i class="fa fa-info-circle" aria-hidden="true"></i>
                            <p>Based on previous uploads</p></span>
                    </div>
                    <div class="card-body  pb-0 pt-0">
                        <div class="owl-carousel owl-theme">


                            @php  $similarRightCount = count($similarRights); @endphp
                            @for ($i = 1; $i <= $similarRightCount; $i++)
                            <div class="list-slider">
                                @php $counter = 1; @endphp
                                @if(count($similarRights) > 0)
                                @foreach($similarRights as $similarRight)
                                @php $scoutcountPositive = Helpers::ifscountPositive($similarRight->valid_scout_score,$similarRight->invalid_scout_score); @endphp
                                <div class="recommended recommended-home">
                                        <!--<div class="link-hover"><a href="holder-history.php"><i class="fa fa-link" aria-hidden="true"></i></a></div>-->
                                    <div class="icon-tick"></div>
                                    @if($scoutcountPositive)
                                    <div class='spiralContainer icon-tick'>
                                        <div class='spiral'><img src="{{ asset('frontend/inside/images/home/right-icon.png') }}"></div>
                                    </div>
                                    @endif

                                    <div class="recommended-header">
                                        <div class="recmd"><a href="holder-history.php">
                                                <div class="recommended-header-left">
                                                    <a href="{{route('right_details',
['user_id' => $similarRight->user_id, 'right_id' => $similarRight->id])}}">

                                                        @if(!file_exists(storage_path("app/appDocs/rightsThumbnails/".$similarRight->user_id."/"."60X60_".$similarRight->thumbnail)))
                                                        <img src="{{ asset('/frontend/inside/images/home/thumb-1.png') }}">
                                                        @elseif ($similarRight->thumbnail!= NULL)
                                                        <img src="{{ route('view_inside_register_profile',['folder'=> 'rightsThumbnails/'.$similarRight->user_id, 'file' => encrypt("60X60_".$similarRight->thumbnail)]) }}" width='50'>
                                                        @else
                                                        <img src="{{ asset('frontend/inside/images/home/thumb-1.png') }}">
                                                        @endif
                                                    </a>



                                                </div>
                                                <div class="recommended-header-right">
                                                    <h5 class="card-title"> <a href="{{route('right_details',
                                ['user_id' => $similarRight->user_id, 'right_id' => $similarRight->id])}}">{{$similarRight->title}}</a> </h5>
                                                    <p class="card-text">By:<a href="{{route('public_profile',['user_id' => $similarRight->user_id])}}">{{$similarRight->first_name}} {{$similarRight->last_name}}</a></p>
                                                    <p class="card-date">{{Helpers::getDateByFormat($similarRight->created_at,
                                'Y-m-d H:i:s', 'd F Y H:i:s')}}</p>
                                                </div>
                                            </a></div>
                                    </div>
                                    <div class="recommended-body">
                                        <p>{{substr($similarRight->description,0,100)}}

                                        </p>
                                    </div>
                                    <div class="recommended-footer">
                                        @if($similarRight->ready_to_sale == 1)

                                        @if($similarRight->is_exclusive_purchase == 1 && $similarRight->is_non_exclusive_purchase == 1)
                                        <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$similarRight->id])}}" data-height="600px" data-width="100%" data-placement="top">Buy<br> <span class="tooltip-2">| ${{ $similarRight->non_exclusive_purchase_price}}<i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span> <span class="tooltip-2"> ${{ $similarRight->exclusive_purchase_price}} <i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span> </a>
                                        @elseif($similarRight->is_exclusive_purchase == 1)
                                        <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$similarRight->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                            Buy | ${{ $similarRight->exclusive_purchase_price}} <span class="tooltip-2"><i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span></a>
                                        @elseif($similarRight->is_non_exclusive_purchase == 1)
                                        <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$similarRight->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                            Buy | ${{ $similarRight->non_exclusive_purchase_price}} <span class="tooltip-2"><i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span></a>
                                        @endif
                                        @endif
                                        <div class="recommended-footer-right">
                                            <ul>
                                                @if($similarRight->ready_to_sale == 1)
                                                <li><i class="fa fa-caret-up" aria-hidden="true"></i> {{$similarRight->valid_scout_score}}</li>
                                                <li><i class="fa fa-caret-down" aria-hidden="true"></i> {{$similarRight->invalid_scout_score}}</li>
                                                @endif
                                                <li class="add-star doLikeUnlike" id="{{ $similarRight->id.'~'.$similarRight->user_id}}"><i class="fa fa-thumbs-up @if($similarRight->total_likes > 0) active @endif" aria-hidden="true"></i><span id="rcmd_{{ $similarRight->id }}">{{$similarRight->total_likes}}</span></li>


                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                @php $counter = $counter++; @endphp
                                @endforeach
                                @endif




                            </div>
                            @endfor
                            <!-- Item -->





                        </div>
                    </div>
                </div>

                <!--                <div class="card Similar-sec">
                                    <div class="card-header">Recently Viewed</div>
                                    <div class="card-body pt-0  ">
                                        <div class="recommended recommended-home">
                
                                            <div class="recommended-header">
                                                <div class="recmd"><a href="holder-history.php">
                                                        <div class="recommended-header-left"><img src="{{ asset('frontend/inside/images/home/thumb-1.png') }}"></div>
                                                        <div class="recommended-header-right">
                                                            <h5 class="card-title"> ERC-20 Token Smart-contract in Hyperledger Sawtooth </h5>
                                                            <p class="card-text">By: Waterlex Organization</p>
                                                            <p class="card-date">Dec 4, 2018</p>
                                                        </div>
                                                    </a></div>
                                            </div>
                                            <div class="recommended-body">
                                                <p>Its is an implementation of the popular token standard- ERC20, originally developed for Ethereum, in Hyperledger Sawtooth...
                
                                                </p>
                                            </div>
                                            <div class="recommended-footer">
                                                <a href="" class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#myModal">Buy | $214 </a>
                                                <div class="recommended-footer-right">
                                                    <ul>
                                                        <li><i class="fa fa-caret-up" aria-hidden="true"></i> 12</li>
                                                        <li><i class="fa fa-caret-down" aria-hidden="true"></i> 0</li>
                                                        <li class="add-star"><i class="fa fa-thumbs-up" aria-hidden="true"></i><span>0</span></li>
                
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer text-right">
                                        <a href="" class="btn btn-default btn-sm"> View All <i class="fa fa-long-arrow-right" aria-hidden="true"></i> </a>
                                    </div>
                                </div>-->
            </div>
        </div>
    </div>

    <!-- The Modal -->



    @php $arrRightTransactions = Helpers::getRightBuyHistory($rightDetail->id, $rightDetail->address); @endphp

    <div class="modal transaction-bl animate1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"> </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body mt--5">

                    <h2>Ownership from Blockchain</h2>
                    <div class="table-responsive table-custom">
                        <table class="table table-bordered">
                            @if(count($arrRightTransactions) > 0)
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th width="18%">TXN Timestamp</th>
                                    <th>User Address</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($arrRightTransactions as $arrRightTransaction)

                                <tr>
                                    <td>{{$arrRightTransaction->txnNumber}}</td>
                                    <td>{{$arrRightTransaction->reportTime}}</td>
                                    <td>{{$arrRightTransaction->address}}</td>
                                    <td>Right Owner</td>
                                </tr>
                                @endforeach


                            </tbody>
                            @else
                            <tbody>
                                <tr>
                                    <td colspan="4">No Ownership found</td>
                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @php $arrRightScouts = Helpers::getRightScoutHistory($rightDetail->id, $rightDetail->address); @endphp

    <div class="modal transaction-bl animate2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"> </h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body mt--5">

                    <h2>Validation Trail on Blockchain</h2>
                    <div class="table-responsive table-custom">
                        <table class="table table-bordered">
                            @if(count($arrRightScouts) > 0)
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th width="18%">TXN Timestamp</th>
                                    <th>User Address</th>
                                    <th width="10%">Pseudo Name</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($arrRightScouts as $arrRightScout)
                                @php $arrSudoname = Helpers::getRightScoutSudoname($arrRightScout->userPublicKey); @endphp

                                <tr>
                                    <td>{{$arrRightScout->txnNumber}}</td>
                                    <td>{{$arrRightScout->reportTime}}</td>
                                    <td>{{$arrRightScout->address}}</td>
                                    <td>{{$arrSudoname->sudoname}}</td>
                                </tr>
                                @endforeach


                            </tbody>
                            @else
                            <tbody>
                                <tr>
                                    <td colspan="3">No Validation Trail found</td>
                                </tr>
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <div class="modal" id="myModal-comment">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Write Comment</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <ul class="comment-box">
                        <li>
                            <div class="file-browse">
                                <span class="button button-browse">
                                    <img src="{{ asset('frontend/inside/images/icon/attachment-icon.svg') }}"> <input type="file" />
                                </span>
                                <input type="text" class="file-name" readonly>
                            </div>
                            <button class="send-file"><img src="{{ asset('frontend/inside/images/icon/send-icon.svg') }}"></button>
                            <textarea rows="1" placeholder="Write Comment"></textarea>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection

@section('iframe')
<div class="modal myframe" id="putStakePopup">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Terms and Condition of Stake</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div class=" pb-2">
                    To validate this Right, you need to stake One dollar.

                </div>
                <div class="term-condition">
                    <h3>Software Evaluation Agreement</h3>
                    <div class="important-note">
                        <h4>IMPORTANT: PLEASE READ THIS AGREEMENT CAREFULLY
                            BEFORE INSTALLING OR USING THE EVALUATION SOFTWARE.
                            YOU ACCEPT AND AGREE TO BE BOUND BY THE TERMS AND
                            CONDITIONS IN THIS EVALUATION END USER LICENSE
                            AGREEMENT (“EVALUATION AGREEMENT”). {RIGHTS
                            HOLDER/SOFTWARE OWNER} GRANTS YOU ACCESS TO THE
                            EVALUATION SOFTWARE ONLY IF YOU ACCEPT THE TERMS AND
                            CONDITIONS OF THIS EVALUATION AGREEMENT. IF YOU ARE
                            ENTERING INTO THIS EVALUATION AGREEMENT ON BEHALF OF
                            A COMPANY OR OTHER LEGAL ENTITY, YOU REPRESENT THAT
                            YOU HAVE THE AUTHORITY TO BIND SUCH ENTITY TO THIS
                            EVALUATION AGREEMENT, IN WHICH CASE THE TERMS “YOU,”
                            OR “YOUR” SHALL REFER TO SUCH ENTITY. IF YOU DO NOT
                            HAVE SUCH AUTHORITY, OR IF YOU DO NOT AGREE WITH THESE
                            TERMS AND CONDITIONS, YOU MAY NOT ACCESS OR USE THE
                            EVALUATION SOFTWARE.
                        </h4>
                        <p>This Evaluation Agreement is an agreement to license Evaluation
                            Software between you (the individual and any entity for which you are
                            acting, and your permitted successors and assigns) and {RIGHTS
                            HOLDER/SOFTWARE OWNER}. In the event you have separately
                            procured Evaluation Software from {RIGHTS HOLDER/SOFTWARE
                            OWNER} you agree that notwithstanding any other signed or accepted
                            licensing terms previously agreed to, these terms govern for purposes
                            of evaluating the Software and Documentation.
                        </p>
                    </div>
                    <div class="grant-software">
                        <h5>A. GRANT AND USE RIGHTS FOR EVALUATION SOFTWARE</h5>
                        <p><strong>1. Evaluation License Grant.</strong> Subject to your compliance with this
                            Evaluation Agreement, {RIGHTS HOLDER/SOFTWARE OWNER}
                            grants you a non-exclusive, non-transferable, non-sublicensable,
                            temporary and limited license to use Software and Documentation
                            solely for non-production, internal, test and demonstration purposes,
                            during the Evaluation Term, in the Territory, solely as limited herein
                            (including limitations of time and resources).
                        </p>
                        <p><strong>2. License Restrictions.</strong> You must not, and must not allow any third
                            party to use the Evaluation Software. You must not disclose to any third
                            party the results of any benchmark testing, comparative or competitive
                            analyses, Intellectual property valuation, or any other type of evaluation
                            of the Evaluation Software, except the permitted disclosures to
                            Inventrust, which may be disclosed on the Inventrust Platform. You
                            must not use the Evaluation Software in conflict with this Evaluation
                            Agreement or, except as permitted by applicable mandatory law or third
                            party license, copy, modify, translate, enhance, or create derivative
                            works from the Evaluation Software, or reverse assemble or
                            disassemble, reverse engineer, decompile or otherwise attempt to
                            derive source code from the Evaluation Software. Further, You must
                            not remove any copyright or other proprietary notices on or in the
                            Evaluation Software, or violate or circumvent any technological
                            restrictions within the Evaluation Software, or as otherwise specified in
                            this Evaluation Agreement.
                        </p>
                    </div>
                    <div class="ownr-softwre">
                        <h5>B. OWNERSHIP OF EVALUATION SOFTWARE.</h5>
                        <p>{RIGHTS HOLDER/SOFTWARE OWNER} owns and retains all right,
                            title and interest in and to the Evaluation Software, including all
                            improvements, enhancements, modifications and derivative works
                            thereof, all Intellectual Property Rights therein, and all rights not
                            expressly granted to you in this Evaluation Agreement. Your rights to
                            use the Evaluation Software are limited to those expressly granted in
                            this Evaluation Agreement. No other rights with respect to the Software
                            or any related Intellectual Property Rights are implied.
                        </p>
                    </div>
                    <div class="ur-content">
                        <h5>C. YOUR CONTENT</h5>
                        <p><strong>1. Access to your Content.</strong> You are solely responsible for your
                            Content. The Evaluation Software may perform operations on, and
                            distribute, your Content. You and your authorized Users retain all of
                            your respective rights, title and interest in and to your Content. {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust’s rights to access and
                            use your Content are limited to those expressly granted in this
                            Evaluation Agreement. You are responsible for installation,
                            configuration and secure connectivity to the Evaluation Software.
                        </p>
                        <p><strong>2. Security.</strong> You are responsible for protecting the security of your
                            Content, including any access you might provide to your Content by
                            your employees, customers or other third parties. You will properly
                            configure and use the Evaluation Software so that it is suitable for your
                            use. You will take and maintain appropriate security, protection and
                            backup for your Content, which may include the use of encryption
                            technology to protect Content from unauthorized access. You will
                            protect the privacy of any of your Users’ data (including by
                            implementation of a privacy policy that complies with applicable law),
                            provide any necessary notices to your Users, and obtain any legally-
                            required consents from your Users concerning your use of the
                            Evaluation Software. You are responsible for complying with any laws or
                            regulations applicable to your Content and you understand that the
                            Evaluation Software is not intended for data regulated by the Health
                            Insurance Portability and Accountability Act (“HIPAA”). You are solely
                            responsible for any consequences if your Content is inadvertently
                            exposed or lost, whether or not you have encrypted, backed up or
                            otherwise taken steps required by the relevant laws or regulations to
                            protect your Content.
                        </p>
                    </div>
                    <div class="accpt-use">
                        <h5>D. ACCEPTABLE USE</h5>
                        <ul>
                            <li>
                                <strong>1. Content Restrictions.</strong> You will not, and you will take steps to ensure
                                that your authorized Users do not, post Content that:
                                <ul>
                                    <li>a. may create a risk of harm, loss, physical or mental injury,
                                        emotional distress, death, disability, disfigurement, or physical
                                        or mental illness to an authorized User, or any other person or
                                        entity;
                                    </li>
                                    <li>b.  may create a risk of any other loss or damage to any person or
                                        property, including viruses and similar elements;
                                    </li>
                                    <li>c.  may constitute a violation of the Terms of this Evaluation
                                        Agreement;
                                    </li>
                                    <li>d.  may constitute or contribute to a crime or tort;</li>
                                    <li>e.  contains any information or content that is unlawful, harmful,
                                        abusive, racially or ethnically offensive, defamatory, infringing,
                                        invasive of personal privacy or publicity rights, harassing,
                                        humiliating to other people (publicly or otherwise), libelous,
                                        threatening, or otherwise objectionable;
                                    </li>
                                    <li>f.  contains any information or content that is illegal, or in violation
                                        of applicable data privacy and protection laws, rules and
                                        regulations; or
                                    </li>
                                    <li>g.  contains any information or content that you do not have a right
                                        to make available under any law or under contractual or
                                        fiduciary relationships.
                                    </li>
                                </ul>
                            </li>
                            <li>2. You represent and warrant that the Content does not and will not
                                violate third-party rights of any kind, including without limitation any
                                Intellectual Property Rights, and rights of publicity and privacy. You shall
                                ensure that your use of the Evaluation Software complies at all times
                                with your privacy policies and all applicable local, state, federal and
                                international laws and regulations, including any encryption
                                requirements.
                            </li>
                            <li><strong>3. Violations of Acceptable Use. </strong> If you become aware that any of
                                your Content or your User’s use of your Content violates this Evaluation
                                Agreement, you shall immediately suspend or remove the applicable
                                Content and/or suspend the User’s access. If you fail to do so, {RIGHTS
                                HOLDER/SOFTWARE OWNER} or Inventrust may require you to do so.
                                If you fail to comply with such request, Inventrust may suspend your
                                account or disable the applicable Content until you comply with our
                                request.
                            </li>
                        </ul>
                    </div>
                    <div class="support">
                        <h5>E. SUPPORT SERVICES</h5>
                        <p>{RIGHTS HOLDER/SOFTWARE OWNER} may, upon request, provide
                            support services in connection with the Evaluation Software.
                        </p>
                    </div>
                    <div class="term-suspensn">
                        <h5>F. TERM AND TERMINATION; SUSPENSION</h5>
                        <ul>
                            <li><strong>1. Term. </strong>This Evaluation Agreement and your access to the
                                Evaluation Software will commence when you click the “I Accept” button
                                or check box presented with these terms or, if earlier, when you use any
                                of the Evaluation Software (the “Effective Date”), and will be effective
                                through the Evaluation Term, unless terminated earlier as permitted
                                under this Section 7.
                            </li>
                            <li><strong>2. Termination for Convenience.</strong> This Evaluation Agreement may be
                                terminated effective immediately {RIGHTS HOLDER/SOFTWARE
                                OWNER}, Inventrust, or You for any or no reason and at any time by
                                providing notice of termination to the other party.
                            </li>
                            <li><strong>3. Survival.</strong> All provisions of this Evaluation Agreement will survive
                                any termination or expiration if by its nature and context it is intended to
                                survive.
                            </li>
                            <li>
                                <strong>4.Suspension</strong>
                                <ul>
                                    <li>
                                        <strong>a. General.</strong> {RIGHTS HOLDER/SOFTWARE OWNER} or
                                        Inventrust may temporarily suspend your use of the Evaluation
                                        Software if they determine at their discretion:
                                        <ul>
                                            <li>i. you or your use of the Evaluation Software is in breach of
                                                this Evaluation Agreement, including but not limited to any
                                                violation of the acceptable use requirements in Section D;
                                            </li>
                                            <li>ii. your use of the Evaluation Software poses a security risk to
                                                the Evaluation Software, or others using the Evaluation
                                                Software, or interferes with, disrupts, damages, or accesses
                                                in an unauthorized manner the servers, networks, or other
                                                properties or services of any other party including without
                                                limitation {RIGHTS HOLDER/SOFTWARE OWNER} or
                                                Inventrust; or
                                            </li>
                                            <li>iii. suspension is required pursuant to our receipt of a
                                                subpoena, court order or other request by a law
                                                enforcement agency.
                                            </li>
                                        </ul>
                                    </li>
                                    <li> <strong>b. Effect of Termination or Suspension.</strong> Upon the termination or
                                        suspension of this Evaluation Agreement for any reason: (a) all
                                        rights and licenses granted to you under this Evaluation
                                        Agreement, including your ability to access any of your
                                        Content, will immediately terminate; and (b) you must promptly
                                        discontinue all access or use of the Evaluation Software and
                                        delete or destroy any of our Confidential Information, including
                                        by removing the Evaluation Software.
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="idemni">
                        <h5>G. INDEMNIFICATION.</h5>
                        <p>You agree to indemnify, defend, release, and hold {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust, its parents,
                            subsidiaries, affiliates, officers, directors, agents, partners, employees
                            and licensors harmless from any Claims, liabilities, demands, losses,
                            damages, costs, expenses and fees (including reasonable attorneys’
                            fees) that such parties may incur as a result of or arising from: (1) your
                            access to or use of the Evaluation Software or violation or breach of this
                            Evaluation Agreement; (2) your Content; (3) any infringement or
                            misappropriation of any Intellectual Property Rights by you, your
                            customers or your suppliers; (4) violation of laws, rules, regulations, or
                            breach of this Evaluation Agreement by you; or (5) your use of any third
                            party content. {RIGHTS HOLDER/SOFTWARE OWNER} and/or
                            Inventrust will (i) provide you with notice of such Claim within a
                            reasonable period of time after learning of the Claim; and (ii) reasonably
                            cooperate in response to your requests for assistance (subject to your
                            reimbursement of {RIGHTS HOLDER/SOFTWARE OWNER} and/or
                            Inventrust’s costs and expenses). Inventrust reserves the right to
                            assume the exclusive defense and control of any matter otherwise
                            subject to indemnification by you and, in such case, you agree to
                            cooperate with Invntrust’s defense of such Claim, and in no event may
                            you agree to any settlement affecting Inventrust without Inventrust’s
                            written consent.
                        </p>
                    </div>
                    <div class="disclaim">
                        <h5>H. DISCLAIMER OF WARRANTY.</h5>
                        <p>TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW,
                            {RIGHTS HOLDER/SOFTWARE OWNER} PROVIDES THE
                            EVALUATION SOFTWARE AS-IS WITHOUT ANY WARRANTIES OF
                            ANY KIND, EXPRESS, IMPLIED, STATUTORY, OR IN ANY OTHER
                            PROVISION OF THIS EVALUATION AGREEMENT OR
                            COMMUNICATION WITH YOU, AND {RIGHTS HOLDER/SOFTWARE
                            OWNER} SPECIFICALLY DISCLAIM ANY IMPLIED WARRANTIES OR
                            CONDITIONS OF MERCHANTABILITY, FITNESS FOR A
                            PARTICULAR PURPOSE, NON-INFRINGEMENT, TITLE, AND ANY
                            WARRANTIES ARISING FROM COURSE OF DEALING OR COURSE
                            OF PERFORMANCE REGARDING OR RELATING TO THE
                            EVALUATION SOFTWARE, THE DOCUMENTATION, OR ANY
                            MATERIALS FURNISHED OR PROVIDED TO YOU UNDER THIS
                            EVALUATION AGREEMENT. {RIGHTS HOLDER/SOFTWARE
                            OWNER} DOES NOT WARRANT THAT THE EVALUATION
                            SOFTWARE WILL OPERATE UNINTERRUPTED, OR THAT IT WILL
                            BE FREE FROM DEFECTS OR THAT THE EVALUATION SOFTWARE
                            WILL MEET (OR IS DESIGNED TO MEET) YOUR BUSINESS
                            REQUIREMENTS.
                        </p>
                    </div>
                    <div class="limit-liablty">
                        <h5>I. LIMITATION OF LIABILITY.</h5>
                        <p>TO THE MAXIMUM EXTENT MANDATED BY LAW, IN NO EVENT
                            SHALL {RIGHTS HOLDER/SOFTWARE OWNER} OR ITS
                            DISTRIBUTORS BE LIABLE FOR ANY LOST PROFITS OR
                            BUSINESS OPPORTUNITIES, LOSS OF USE, LOSS OF REVENUE,
                            LOSS OF GOODWILL, BUSINESS INTERRUPTION, LOSS OF DATA,
                            INABILITY TO USE THE EVALUATION SOFTWARE, INCLUDING AS
                            A RESULT OF ANY TERMINATION/SUSPENSION/DISCONTINUATION/MODIFICATION
                            OR SUSPENSION OF THIS EVALUATION AGREEMENT, OR ANY
                            OTHER INDIRECT, SPECIAL, INCIDENTAL, OR CONSEQUENTIAL
                            DAMAGES UNDER ANY THEORY OF LIABILITY, WHETHER BASED
                            IN CONTRACT, TORT, NEGLIGENCE, PRODUCT LIABILITY OR
                            OTHERWISE. {RIGHTS HOLDER/SOFTWARE OWNER} LIABILITY
                            UNDER THIS EVALUATION AGREEMENT SHALL NOT, IN ANY
                            EVENT, EXCEED USD $100. THE FOREGOING LIMITATIONS SHALL
                            APPLY REGARDLESS OF WHETHER {RIGHTS
                            HOLDER/SOFTWARE OWNER} HAS BEEN ADVISED OF THE
                            POSSIBILITY OF SUCH DAMAGES AND REGARDLESS OF
                            WHETHER ANY REMEDY FAILS OF ITS ESSENTIAL PURPOSE.
                            YOU MAY NOT BRING A CLAIM UNDER THIS EVALUATION
                            AGREEMENT MORE THAN EIGHTEEN (18) MONTHS AFTER (i) THE
                            END OF THE EVALUATION TERM, OR (ii) THE DATE THE CLAIM
                            FIRST ARISES, WHICHEVER IS EARLIEST.
                        </p>
                    </div>
                    <div class="confidential">
                        <h5>J. CONFIDENTIALITY. </h5>
                        <p>You may (a) use Confidential Information only for exercising rights and
                            performing obligations in connection with this Evaluation Agreement;
                            and (b) protect from disclosure any Confidential Information disclosed
                            by {RIGHTS HOLDER/SOFTWARE OWNER} or Inventrust for a period
                            commencing upon the disclosure date until 3 years thereafter.
                            Notwithstanding the foregoing, either party may disclose Confidential
                            Information: (i) to an Affiliate to fulfill its obligations or exercise its rights
                            under this Evaluation Agreement so long as such Affiliate agrees to
                            comply with these restrictions in writing; and (ii) if required by law or
                            regulatory authorities, provided that if you are required to disclose
                            Confidential Information by applicable law, regulatory authorities or
                            court order, you shall notify {RIGHTS HOLDER/SOFTWARE OWNER}
                            and Inventrust of the required disclosure promptly in writing and shall
                            cooperate with {RIGHTS HOLDER/SOFTWARE OWNER} and
                            Inventrust in any lawful action to contest or limit the scope of the
                            required disclosure before disclosing any Confidential Information.
                            {RIGHTS HOLDER/SOFTWARE OWNER} and Inventrust shall not be
                            responsible for unauthorized disclosure of your data stored within
                            Evaluation Software arising from a data security breach. You are solely
                            responsible for all obligations to comply with laws applicable to your
                            access or use of Evaluation Software, including without limitation any
                            personal data processing. {RIGHTS HOLDER/SOFTWARE OWNER}
                            and Inventrust may collect, use, store and transmit technical and related
                            information about your use of Evaluation Software, including server
                            internet protocol address, hardware identification, operating system,
                            application software, peripheral hardware, and Evaluation Software
                            usage statistics, to facilitate the provisioning of updates, support,
                            invoicing, and online services. You are responsible for obtaining all
                            consents required to enable {RIGHTS HOLDER/SOFTWARE OWNER}
                            and Inventrust to exercise its confidentiality rights, in compliance with
                            applicable law.
                        </p>
                    </div>
                    <div class="exp-trde">
                        <h5> K. EXPORT AND TRADE COMPLIANCE. </h5>
                        <p>The Evaluation Software and any technology delivered in connection
                            therewith may be subject to governmental restrictions on exports from
                            the U.S., restrictions on exports from other countries in which such
                            technology may be provided or located, disclosures of technology to
                            foreign persons, exports from abroad of derivative products thereof, and
                            the importation and/or use of such technology included therein outside
                            of the United States (collectively, “Export Laws”). Diversion contrary to
                            Export Laws is expressly prohibited. You shall, at your sole expense,
                            comply with all Export Laws, including without limitation all licensing,
                            authorization, documentation and reporting requirements and export
                            policies made available to you by {RIGHTS HOLDER/SOFTWARE
                            OWNER} and Inventrust. You represent that you are not a restricted
                            party, which shall be deemed to include any person or entity: (a) located
                            in or a national of Cuba, Iran, North Korea, Sudan, Syria, Crimea, or any
                            other countries that may, from time to time, become subject to sanctions
                            or with which U.S. persons are generally prohibited from engaging in
                            financial transactions; (b) on any restricted party or entity list maintained
                            by any U.S. governmental agency; or (c) any person or entity involved in
                            an activity restricted by any U.S. government agency. Certain
                            information or technology may be subject to the International Traffic in
                            Arms Regulations and shall only be exported, transferred or released to
                            foreign nationals inside or outside the United States in compliance with
                            such regulations.
                        </p>
                    </div>
                    <div class="gernl">
                        <h5>L. GENERAL. </h5>
                        <p>This Evaluation Agreement is governed by New York law. You hereby
                            expressly consents to the personal jurisdiction of either the New York
                            courts or the United States District Courts located in the State of New
                            York and agree that any action relating to or arising out of this
                            Evaluation Agreement must be instituted and prosecuted only in a New
                            York Court or the United States District Court located in New York. The
                            U.N. Convention on Contracts for the International Sale of Goods does
                            not apply. You shall comply with all applicable laws and regulations and
                            diversion contrary to such laws is expressly prohibited. Except to the
                            extent expressly set forth to the contrary in this Evaluation Agreement,
                            this Evaluation Agreement is not intended to confer upon any person
                            other than the parties hereto any rights or remedies. The parties are
                            independent contractors. In the event that {RIGHTS
                            HOLDER/SOFTWARE OWNER} or Inventrust is prevented from
                            performing or is unable to perform any of its obligations under this
                            Evaluation Agreement due to any Act of God, fire, casualty, flood,
                            earthquake, war, strike, lockout, epidemic, destruction of production
                            facilities, riot, insurrection, material unavailability, unavailability or
                            interruption of telecommunications equipment or networks, or any other
                            cause beyond the reasonable control of {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust, {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust shall give written notice
                            to you, {RIGHTS HOLDER/SOFTWARE OWNER} and Inventrust
                            performance shall be excused, and the time for the performance shall
                            be extended for the period of delay or inability to perform due to such
                            occurrences. This Evaluation Agreement is the complete statement of
                            the parties’ agreement with regard to the subject matter hereof and may
                            be modified only by written agreement. You shall not assign or transfer
                            any rights under this Evaluation Agreement or delegate any of your
                            duties hereunder, by operation of law or otherwise, without {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust prior written consent,
                            and any such action in violation of this provision, is null and void, and of
                            no force, and a breach of this Evaluation Agreement. {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust may assign or transfer
                            this Evaluation Agreement to any successors-in-interest to all or
                            substantially all of the business or assets of {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust whether by merger,
                            reorganization, asset sale or otherwise, or to any {RIGHTS
                            HOLDER/SOFTWARE OWNER} and Inventrust Affiliates, and this
                            Evaluation Agreement shall inure to the benefit of and be binding upon
                            the respective permitted successors and assigns. If any part of this
                            Evaluation Agreement or an Order is held unenforceable, the validity of
                            the remaining provisions shall not be affected. In the event of conflict or
                            inconsistency among the Evaluation Agreement or the Order, the
                            Evaluation Agreement shall control solely to the extent of the conflict or
                            inconsistency.
                        </p>
                    </div>
                    <div class="define">
                        <h5>M. DEFINITIONS.</h5>
                        <ul>
                            <li>1.<span> “Affiliate”</span> means a legal entity controlled by, controls, or is under
                                common control of Pivotal or you, with “control” meaning more than fifty
                                (50%) of the voting power or ownership interests then outstanding of
                                that entity.
                            </li>
                            <li>2.<span> “Claim(s)”</span> means any third party claim, notice, demand, action,
                                proceeding, litigation, investigation or judgment. With respect to
                                Evaluation Software, such Claim must be related to your access to or
                                use of the Evaluation Software during the Evaluation Term.
                            </li>
                            <li>3.<span> “Confidential Information” </span>means the terms of this Evaluation
                                Agreement, Software, and all confidential and proprietary information of
                                Pivotal or you, including without limitation, all business plans, product
                                plans, financial information, software, designs, and technical, business
                                and financial data of any nature whatsoever, provided that such
                                information is marked or designated in writing as “confidential,”
                                “proprietary,” or with a similar term or designation. Confidential
                                Information does not include information that is (a) rightfully in the
                                receiving party’s possession without prior obligation of confidentiality
                                from the disclosing party; (b) a matter of public knowledge (or becomes
                                a matter of public knowledge other than through breach of confidentiality
                                by the other party); (c)rightfully furnished to the receiving party by a third
                                party without confidentiality restriction; or (d) independently developed
                                by the receiving party without reference to the disclosing party’s
                                Confidential Information.
                            </li>
                            <li>4. <span>“Content”</span> means any and all applications, files, information,
                                materials, data, software, or other content uploaded to, stored,
                                published or displayed by You through the Evaluation Software.
                            </li>
                            <li>5.<span> “Distributor”</span> means a reseller, distributor, system integrator,
                                service provider, independent software vendor, value-added reseller,
                                OEM or other partner authorized by Pivotal to license Evaluation
                                Software to end users, and any third party duly authorized by a
                                Distributor to license Evaluation Software to end users.
                            </li>
                            <li>6. <span>“Documentation”</span> means documentation provided to you by Pivotal
                                with Evaluation Software, as revised by Pivotal from time to time.
                            </li>
                            <li>7.<span> “Evaluation License”</span> means (a) access to Evaluation Software
                                and Documentation subject to the licensing terms and restrictions set
                                forth in this Evaluation Agreement, and the Order, all during the
                                Evaluation Term.
                            </li>
                            <li>8.<span> “Evaluation Term”</span> means the period starting on the Effective Date
                                and continues for a period of 90 days.
                            </li>
                            <li>9.<span> “Evaluation Software”</span> means Pivotal computer programs that you
                                procure from the Service Offering or you upload to your Service Offering
                                account pursuant to this Evaluation License.
                            </li>
                            <li>10.<span> “Intellectual Property Rights”</span> means all worldwide intellectual
                                property rights, including, without limitation,patents, utility models, rights
                                to inventions, copyright and related rights, trademarks and service
                                marks, trade names and domain names, rights in get-up, goodwill and
                                the right to sue for passing off or unfair competition, rights in designs,
                                rights in computer software, database rights, rights to preserve the
                                confidentiality of information (including know-how and trade secrets)
                                and any other intellectual property rights, including all applications for
                                (and rights to apply for and be granted), renewals or extensions of, and
                                rights to claim priority from, such rights and all similar or equivalent
                                rights or forms of protection which subsist or will subsist, now or in the
                                future, in any part of the world.
                            </li>
                            <li>11.<span> “Open Source Software” or “OSS”</span> means software components
                                licensed under a license approved by the Open Source Initiative or
                                similar open source or freeware license and included in, embedded in,
                                utilized by, provided or distributed with Evaluation Software.
                            </li><li>12.<span> “Order”</span> means the Service Offering ordering document (including
                                a registration webpage) pursuant to which you obtain access to the
                                Evaluation Software.
                            </li>
                            <li>13.<span> “Service Offering”</span> means the cloud computing infrastructure
                                hosted by the web service provider where the Evaluation Software is
                                provided to you.
                            </li>
                            <li>14.<span> “Service Offering Terms”</span> means any terms and conditions of your
                                Order and subscription to the Service Offering, including any related
                                website and service terms.
                            </li>
                            <li>15.<span> “Taxes”</span> means any sales, use, gross receipts, business and
                                occupation, and other taxes (other than taxes on Pivotal’s NET income),
                                export and import fees, customs duties and similar charges imposed by
                                any government or other authority.
                            </li>
                            <li>16.<span> “Territory”</span> means the country or countries in which you have been
                                invoiced by the Service Provider.
                            </li>
                            <li>17.<span> “User(s)”</span> means the individual and/or the entity that uses or
                                accesses the Evaluation Software.
                            </li>
                        </ul>
                    </div>
                </div>
                <iframe id ="frameset"  frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="validClaimPopup">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Evaluate It</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <iframe id ="frameset"  frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="rightpurchase">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Buy Rights</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" style="padding: 0.5rem;">
                <iframe id ="frameset"  frameborder="0"></iframe>
            </div>


        </div>
    </div>
</div>



<div class="modal" id="ReportPopup">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Report </h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <iframe id ="frameset"  frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
<!-- open myaccount pupop-->



@section('pageTitle')
Rights Details
@endsection


@section('additional_css')
<style>
    a.morelink {
        color:black;
        font-weight: bold;
    }

    a.morelink {
        text-decoration:none;
        outline: none;
    }
    .morecontent span {
        display: none;
    }
    .comment {
        width: 49em;
        margin: 10px;
    }

</style>
@endsection
@section('jscript')
<script>
    var messages = {
        save_comments: "{{route('save_comments')}}",
        fetch_comments: "{{route('fetch_comments')}}",
        csrf_token: "{{csrf_token()}}",
        req_right_number: "{{ trans('error_messages.req_right_number') }}",
        req_right_inventor: "{{ trans('error_messages.req_right_inventor') }}",
        req_right_assignee: "{{ trans('error_messages.req_right_assignee') }}",
        req_right_cluster: "{{ trans('error_messages.req_right_cluster') }}",
        req_right_cluster_numeric: "{{ trans('error_messages.req_right_cluster_numeric') }}",
        req_right_date: "{{ trans('error_messages.req_right_date') }}",
        req_right_description: "{{ trans('error_messages.req_right_description') }}"
    };
</script>
<script src="{{ asset('frontend/inside/js/rights_details.js')}}"></script>
<script type="text/javascript" src="{{ asset('frontend/inside/js/dashboard.js') }}"></script>
<script>
    $(document).ready(function () {
        var showChar = 250;
        var ellipsestext = "...";
        var moretext = "Read More";
        var lesstext = "Read Less";
        $('.more').each(function () {
            var content = $(this).html();

            if (content.length > showChar) {

                var c = content.substr(0, showChar);
                var h = content.substr(showChar - 1, content.length - showChar);

                var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

                $(this).html(html);
            }

        });

        $(".morelink").click(function () {
            if ($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });
    });
</script>
@endsection
