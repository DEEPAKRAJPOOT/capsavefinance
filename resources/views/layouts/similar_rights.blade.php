<div class="col-md-4">
    <div class="card box-shadow-div Similar-sec">
        <div class="card-header">
            <strong>Similar Rights</strong>
        </div>
        <div class="card-body  pb-0 pt-0">
            <div class="owl-carousel owl-theme">

                 @php  $similarRightCount = count($similarRights);@endphp
                    @for ($i = 1; $i <= $similarRightCount; $i++)
                            <div class="list-slider">
                                @php $counter = 1; @endphp
                                 @if(count($similarRights) > 0)
                                @foreach($similarRights as $similarRight)


                                <div class="recommended recommended-home">
                                        <!--<div class="link-hover"><a href="holder-history.php"><i class="fa fa-link" aria-hidden="true"></i></a></div>-->
                                    <div class="recommended-header">
                                        @if($similarRight->ready_to_sale == 1 
                                            && $similarRight->valid_scout_score > 0
                                            && $similarRight->invalid_scout_score == 0)
                                            <div class="icon-tick"></div>
                                            <div class='spiralContainer icon-tick'>
                                                <div class='spiral'><img src="{{ asset('frontend/inside/images/home/right-icon.png') }}"></div>
                                            </div>
                                        @endif
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
                                            <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$similarRight->id])}}" data-height="600px" data-width="100%" data-placement="top">Buy<br> <span class="tooltip-2">| ${{ $similarRight->non_exclusive_purchase_price}} <i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span> <span class="tooltip-2"> ${{ $similarRight->exclusive_purchase_price}} <i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span> </a>
                                            @elseif($similarRight->is_exclusive_purchase == 1)
                                            <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$similarRight->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                Buy | ${{ $similarRight->exclusive_purchase_price}} <span class="tooltip-2"> <i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span></a>
                                            @elseif($similarRight->is_non_exclusive_purchase == 1)
                                            <a href="" class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#myModal">
                                                Buy | ${{ $similarRight->non_exclusive_purchase_price}} <span class="tooltip-2"> <i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span></a>
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
                            @endfor<!-- Item -->
               

                

            </div>
        </div>
    </div>
</div>