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
        </div>
    </div>
</section>
<section class="mt-4">
    <div class="container ">
        <div class="col-md-10 card-header  pb-3 pt-0">
                            Recommended Rights
                        </div>
        <div class="d-flex">
            {{--@include('layouts.filter')--}}
            <div class=" col-md-12">
                <div class="card box-shadow-div view-all-rights-sec mb-5">
                    <div class="card-body p-0   overflow-v hover-effect">
                        
                        <div class="col-md-12">
                            <div class="row">
                             @if(count($recommendedRights) > 0)
                            @foreach($recommendedRights as $recommendRights)
                            @php $bounty = Helpers::getBounty($recommendRights->id); @endphp
                                <div class="col-md-3 mb-3">
                                    <div class="recommended recommended-home">
                                        <div class="recommended-header">
                                            <div class="recmd">
                                        <a href="{{route('right_details',
                                ['user_id' => $recommendRights->user_id, 'right_id' => $recommendRights->id])}}">
                                            <div class="recommended-header-left">
                                               @if ($recommendRights->thumbnail!= NULL)
                                                <img src="{{ route('view_inside_register_profile',['folder'=> 'rightsThumbnails/'.$recommendRights->user_id, 'file' => encrypt("60X60_".$recommendRights->thumbnail)])}}" width='60'>
                                                 @else
                                                 <img src="{{ asset('frontend/inside/images/home/thumb-1.png') }}">
                                              @endif
                                            </div>
                                            <div class="recommended-header-right">
                                                @if($recommendRights->ready_to_sale == 1
                                                    && $recommendRights->valid_scout_score > 0
                                                    && $recommendRights->invalid_scout_score == 0)

                                            <div class="icon-tick"></div>
                                            <div class='spiralContainer icon-tick'>
                                                <div class='spiral'><img src="{{ asset('frontend/inside/images/home/right-icon.png') }}"></div>
                                            </div>
                                                @endif
                                                <h5 class="card-title"> {{$recommendRights->title}}</h5>
                                                <p class="card-text">By: <a href="{{route('public_profile',['user_id' => $recommendRights->user_id])}}">{{ucwords($recommendRights->first_name.' '.$recommendRights->last_name)}}</a></p>
                                                <p class="card-date">{{Helpers::getDateByFormat($recommendRights->created_at,
                                'Y-m-d H:i:s', 'd F Y')}}</p>
                                                @if($bounty > 0)
                                                <p class="card-value">Bounty: ${{$bounty}}</p>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                        </div>
                                        <div class="recommended-body">
                                            <p>{{substr($recommendRights->description,0, 100).'...'}}</p>
                                        </div>
                                        <div class="recommended-footer">

                                            @if($recommendRights->user_id != (int) Auth::user()->id)
                                        @if($recommendRights->ready_to_sale == 1)
                                        @if($recommendRights->is_exclusive_purchase == 1)
                                            <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$recommendRights->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                Buy | ${{ $recommendRights->exclusive_purchase_price}}</a>
                                            @elseif($recommendRights->is_non_exclusive_purchase == 1)
                                            <a class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$recommendRights->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                Buy | ${{ $recommendRights->non_exclusive_purchase_price}}</a>
                                            @endif
                                         @endif
                                     @endif
                                            <div class="recommended-footer-right">
                                                <ul>
                                            @if($recommendRights->ready_to_sale == 1)
                                            <li><i class="fa fa-caret-up" aria-hidden="true"></i> {{$recommendRights->valid_scout_score}}</li>
                                            <li><i class="fa fa-caret-down" aria-hidden="true"></i>{{$recommendRights->invalid_scout_score}}</li>
                                            @endif
                                            <li class="add-star doLikeUnlike" id="{{ $recommendRights->id.'~'.$recommendRights->user_id}}"><i class="fa fa-thumbs-up @if($recommendRights->total_likes > 0) active @endif" aria-hidden="true"></i><span id="rcmd_{{ $recommendRights->id }}">{{$recommendRights->total_likes}}</span></li>
                                        </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                            @else
                            <span>Recommended Rights Not Available</span>
                            @endif



                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


@section('iframe')
 <div class="modal" id="rightpurchase">
        <div class="modal-dialog modal-lg ">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Buy Right</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" style="padding: 0.5rem;">
              <iframe id ="frameset"  frameborder="0"></iframe>
            </div>
          </div>
        </div>
    </div>

@endsection



@section('pageTitle')
All Rights
@endsection
@section('additional_css')

@endsection
@section('jscript') 
@endsection
