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
                            Selling Rights
                        </div>
        <div class="d-flex">
            {{--@include('layouts.filter')--}}
            <div class=" col-md-12">
                <div class="card box-shadow-div view-all-rights-sec mb-5">
                    <div class="card-body p-0   overflow-v hover-effect">
                        
                        <div class="col-md-12">
                            <div class="row">
                            @if(count($trendingRights)>0)
                            @foreach($trendingRights as $trendRights)
                            @php $bounty = Helpers::getBounty($trendRights->id); @endphp
                                <div class="col-md-3 mb-3">
                                    <div class="recommended recommended-home"><div class="recommended-header">
                                            <div class="recmd"><a href="{{route('right_details',['user_id' => $trendRights->user_id, 'right_id' => $trendRights->id])}}">
                                                    <div class="recommended-header-left">
                                                        @if ($trendRights->thumbnail!= NULL)
                                                <img src="{{ route('view_inside_register_profile',['folder'=> 'rightsThumbnails/'.$trendRights->user_id, 'file' => encrypt("60X60_".$trendRights->thumbnail)])}}" width='60'>
                                                @else
                                                <img src="{{ asset('frontend/inside/images/home/thumb-1.png') }}">
                                                @endif
                                                    </div></a>
                                                    <div class="recommended-header-right">
                                                        <h5 class="card-title"><a href="{{route('right_details',['user_id' => $trendRights->user_id, 'right_id' => $trendRights->id])}}">{{$trendRights->title}}</a></h5>
                                                        <p class="card-text">By:  <a href="{{route('public_profile',['user_id' => $trendRights->user_id])}}">{{ucwords($trendRights->first_name.' '.$trendRights->last_name)}}</a></p>
                                                        <p class="card-date">{{Helpers::getDateByFormat($trendRights->created_at,
                                'Y-m-d H:i:s', 'd F Y')}}</p>
                                                        @if($bounty > 0 && ((int) Auth::user()->is_scout == 1))
                                                <p class="card-value">Bounty: ${{$bounty}} </p>
                                                @endif
                </div>
                                                </div>
                                        </div>
                                        <div class="recommended-body">
                                            <p>{{substr($trendRights->description,0, 100).'...'}}</p>
                                        </div>
                                        <div class="recommended-footer">

                                            @if($trendRights->user_id != (int) Auth::user()->id)
                                            @if($trendRights->ready_to_sale == 1)
                                            @if($trendRights->is_exclusive_purchase == 1 && $trendRights->is_non_exclusive_purchase == 1)
                                            <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$trendRights->id])}}" data-height="600px" data-width="100%" data-placement="top">Buy<br> <span class="tooltip-2">| ${{ $trendRights->non_exclusive_purchase_price}}<i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span> <span class="tooltip-2"> ${{ $trendRights->exclusive_purchase_price}} <i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span> </a>
                                            @elseif($trendRights->is_exclusive_purchase == 1)
                                            <a  class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$trendRights->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                Buy | ${{ $trendRights->exclusive_purchase_price}} <span class="tooltip-2"><i class="fa fa-info-circle" aria-hidden="true"></i><p>Exclusive</p></span></a>
                                            @elseif($trendRights->is_non_exclusive_purchase == 1)
                                            <a class="btn btn-x-sm btn-success pull-left" data-toggle="modal" data-target="#rightpurchase" data-url =" {{route('buy_right_popup',['right_id'=>$trendRights->id])}}" data-height="600px" data-width="100%" data-placement="top">
                                                Buy | ${{ $trendRights->non_exclusive_purchase_price}} <span class="tooltip-2"><i class="fa fa-info-circle" aria-hidden="true"></i><p>Non Exclusive</p></span></a>
                                            @endif
                                            @endif
                                        @endif
                                            <div class="recommended-footer-right">
                                                <ul>
                                            <li><i class="fa fa-caret-up" aria-hidden="true"></i> {{$trendRights->valid_scout_score}}</li>
                                            <li><i class="fa fa-caret-down" aria-hidden="true"></i> {{$trendRights->invalid_scout_score}}</li>
                                            <li class="add-star doLikeUnlike" id="{{ $trendRights->id.'~'.$trendRights->user_id}}"><i class="fa fa-thumbs-up active" aria-hidden="true"></i><span id="tred_{{ $trendRights->id }}">{{$trendRights->total_likes}}</span></li>
                                        </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                            @else
                            <span>Selling Rights Not Available</span>
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
