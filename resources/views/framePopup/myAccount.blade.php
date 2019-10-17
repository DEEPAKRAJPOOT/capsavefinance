@extends('layouts.app')

@section('content')
<div class="col-lg-12 account-detail  m-auto pt-4">
    <div class="container  ">
        <div class="row">
            <div class="col-lg-12">
                <div class="title-section center pb-60">
                    <h2><span>My Account</span></h2>
                </div>
            </div>
            <section class=" col-md-12">
                <div class="container white-bg">
                    <div class="row">
                        <div class="col-md-6 ">
                            <p><strong>Recent Transactions History </strong> </p>
                        </div>
                        <div class="col-md-6  ml-auto text-right">
                            @if(Auth::user()->is_scout == 1)
                            @php $Incentive = Helpers::getIncentiveByUserID(Auth::user()->id); @endphp
                            <p><strong>Total Balance :</strong> <span>${{number_format($Incentive, 2)}}</span></p>
                            @else
                            @php $soldRightAmount = Helpers::getsoldRightAmountByUserID(Auth::user()->id); @endphp
                            <p><strong>Total Balance :</strong> <span>${{number_format($soldRightAmount, 2)}}</span></p>
                            @endif

                        </div>
                    </div>
                    <hr>

                    @if(Auth::user()->is_scout == 1)
                    @php $purchaseArray = Helpers::getPurchaseByUserID(Auth::user()->id); @endphp
                    @php $soldArray = Helpers::getsoldRightByUserID(Auth::user()->id); @endphp
                    @php $IncentiveRights = Helpers::getIncentiveRightByUserID(Auth::user()->id); @endphp
                    <div class="inen-acc">
                        <div class="row">
                            <div class="col-md-6 ">
                                <p><strong>Incentive :</strong> <span>${{number_format($Incentive, 2)}}</span></p>
                            </div>
                            @if(count($IncentiveRights) > 2)
                            <div class="col-md-6  ml-auto text-right view_all">
                                <button class="btn" data-toggle="modal" data-target="#IncentiveModal">View All</button>
                            </div>
                            @endif
                        </div>
                        <div class="table-design">
                            <table>
                                <tr>
                                    @if(count($IncentiveRights) > 0)
                                    @php $u3=0; @endphp
                                    @foreach($IncentiveRights as $IncentiveRight)
                                    @php $u3++; @endphp
                                    @if($u3 <= 2)
                                    <td width="50%">
                                        <table>
                                            <tr>
                                                <td>Right Name:</td>
                                                <td> {{ucfirst($IncentiveRight->title)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Incentive:</td>
                                                <td><span class="text-success">${{number_format($IncentiveRight->to_scout, 2)}} </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transaction Date: </td>
                                                <td width="70%">{{ Helpers::getDateByFormat($IncentiveRight->updated_at)}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    @endif
                                    @endforeach
                                    @else
                                    <td><p class="text-danger">No Transaction Found</p></td>
                                    <td></td>
                                @endif
                                </tr></table></div>
                    </div>
                    <hr>
                    <div class="inen-acc">
                        <div class="row">
                            <div class="col-md-6 ">
                                <p><strong>Purchase Items </strong> </p>
                            </div>
                            @if(count($purchaseArray) > 2)
                            <div class="col-md-6  ml-auto text-right view_all">
                                <button class="btn" data-toggle="modal" data-target="#PurchaseModal">View All</button>
                            </div>
                            @endif
                        </div>
                        <div class="table-design">
                            <table>
                                <tr>
                                    @if(count($purchaseArray) > 0)
                                    @php $u2=0; @endphp
                                    @foreach($purchaseArray as $purchase)
                                    @php $rightArray = Helpers::getRightByRightID($purchase->right_id);@endphp
                                    @if($purchase->is_payment_success == 0)  @php @$paymentstatus ='Payment Fail'; @$classstatus='text-danger';@endphp @else @php @$paymentstatus ='Payment Completed'; @$classstatus='text-success';@endphp @endif
                                    @if(isset($rightArray) && !empty($rightArray->title))
                                    @php $u2++;@endphp
                                    @if($u2 <= 2)
                                    <td width="50%">
                                        <table>
                                            <tr>
                                                <td width="20%">Rights : </td>
                                                <td>{{ucfirst($rightArray->title)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Price : </td>
                                                <td> ${{$purchase->right_price}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status : </td>
                                                <td><span class="{{ @$classstatus }}">{{@$paymentstatus}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transaction : </td>
                                                <td width="70%"><div class="text-overflow">{{ $purchase->trasation_id }}<div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%">Transaction Date : </td>
                                                <td width="70%">{{ Helpers::getDateByFormat($purchase->updated_at)}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @endif
                                    @endif
                                    @endforeach
                                    @else
                                    
                                    <td><p class="text-danger">No Transaction Found</p></td>
                                    <td></td>
                                
                                
                                @endif
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="inen-acc">
                        <div class="row">
                            <div class="col-md-6 ">
                                <p><strong>Selling Items </strong> </p>
                            </div>
                            @if(count($soldArray) > 2)
                            <div class="col-md-6  ml-auto text-right view_all">
                                <button class="btn" data-toggle="modal" data-target="#SellingModal">View All</button>
                            </div>
                            @endif
                        </div>
                        @if(count($soldArray) > 0)
                        <div class="table-design">
                            <table>
                                <tr>
                                    @php $u1=0; @endphp
                                    @foreach($soldArray as $sold)
                                    @php $rightArray = Helpers::getRightByRightID($sold->right_id);  @endphp
                                    @if($sold->is_payment_success == 0)  @php @$paymentstatus ='Payment Fail'; @$classstatus ='text-danger'; @endphp @else @php @$paymentstatus ='Payment Completed';@$classstatus ='text-success'; @endphp @endif
                                    @if(isset($rightArray) && !empty($rightArray->title))
                                    @php $u1++; @endphp
                                    @if($u1 <= 2)
                                    <td width="50%">
                                        <table>
                                            <tr>
                                                <td width="20%">Rights : </td>
                                                <td>{{ucfirst($rightArray->title)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Price : </td>
                                                <td> ${{$sold->right_price}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status : </td>
                                                <td><span class="{{ @$classstatus }}">{{@$paymentstatus}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transaction : </td>
                                                <td width="70%"><div class="text-overflow">{{ $sold->trasation_id }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%">Transaction Date : </td>
                                                <td width="70%">{{ Helpers::getDateByFormat($sold->updated_at)}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @endif
                                    @endif
                                    @endforeach
                                </tr>
                            </table>
                        </div>
                        @else
                            <td><p class="text-danger">No Transaction Found</p></td>
                            <td></td>
                        @endif
                                            </tr></table></div>
                    @else
                    @php $purchaseArray = Helpers::getPurchaseByUserID(Auth::user()->id); @endphp
                    @php $soldArray     = Helpers::getsoldRightByUserID(Auth::user()->id); @endphp
                    <div class="inen-acc">
                        <div class="row">
                            <div class="col-md-6 ">
                                <p><strong>Purchase Items </strong> </p>
                            </div>
                            @if(count($purchaseArray) > 2)
                            <div class="col-md-6  ml-auto text-right view_all">
                                <button class="btn" data-toggle="modal" data-target="#PurchaseModal">View All</button>
                            </div>
                            @endif
                        </div>
                        <div class="table-design">
                            <table>
                                <tr>
                                    @if(count($purchaseArray) > 0)
                                    @php $u4=0; @endphp
                                    @foreach($purchaseArray as $purchase)
                                    @php $rightArray = Helpers::getRightByRightID($purchase->right_id); @endphp
                                    @if($purchase->is_payment_success == 0)  @php @$paymentstatus ='Payment Fail';@$classstatus ='text-danger'; @endphp @else @php @$paymentstatus ='Payment Completed';@$classstatus ='text-success'; @endphp @endif
                                    @if(isset($rightArray) && !empty($rightArray->title))
                                    @php $u4++;@endphp
                                    @if($u4 <= 2)
                                    <td width="50%">
                                        <table>
                                            <tr>
                                                <td width="20%">Rights : </td>
                                                <td>{{ucfirst($rightArray->title)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Price : </td>
                                                <td> ${{$purchase->right_price}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status : </td>
                                                <td><span class="{{ @$classstatus }}">{{@$paymentstatus}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transaction : </td>
                                                <td width="70%"><div class="text-overflow">{{ $purchase->trasation_id }}</div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%">Transaction Date : </td>
                                                <td width="70%">{{ Helpers::getDateByFormat($purchase->updated_at)}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @endif
                                    @endif
                                    @endforeach
                                
                        @else
                                    <td><p class="text-danger">No Transaction Found</p></td>
                                    <td></td>
                        @endif
                        </tr></table></div>
                    </div>
                    <hr>
                    <div class="inen-acc">
                        <div class="row">
                            <div class="col-md-6 ">
                                <p><strong>Selling Items :</strong> <span>${{number_format($soldRightAmount, 2)}}</span>
                                </p>
                            </div>
                            @if(count($soldArray) > 2)
                            <div class="col-md-6  ml-auto text-right view_all">
                                <button class="btn" data-toggle="modal" data-target="#SellingModal">View All</button>
                            </div>
                            @endif
                        </div>
                        <div class="table-design">
                            <table>
                                <tr>
                                    @if(count($soldArray) > 0)
                                    @php $u5=0; @endphp
                                    @foreach($soldArray as $sold)
                                    @php $rightArray = Helpers::getRightByRightID($sold->right_id);@endphp
                                    @if($sold->is_payment_success == 0)  @php @$paymentstatus ='Payment Fail';@$classstatus ='text-danger'; @endphp @else @php @$paymentstatus ='Payment Completed'; @$classstatus ='text-success';@endphp @endif
                                    @if(isset($rightArray) && !empty($rightArray->title))
                                    @php $u5++;@endphp
                                    @if($u5 <= 2)
                                    <td width="50%">
                                        <table>
                                            <tr>
                                                <td width="20%">Rights : </td>
                                                <td>{{ucfirst($rightArray->title)}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Price : </td>
                                                <td> ${{$sold->right_price}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status : </td>
                                                <td><span class="{{ @$classstatus }}">{{@$paymentstatus}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Transaction : </td>
                                                <td width="70%"><div class="text-overflow">{{ $sold->trasation_id }}<div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40%">Transaction Date : </td>
                                                <td width="70%">{{ Helpers::getDateByFormat($sold->updated_at)}}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    @endif
                                    @endif
                                    @endforeach
                                    @else
                                    <td><p class="text-danger">No Transaction Found</p></td>
                                    <td></td>
                                @endif
                                
                                </tr>
                            </table>
</div>

                        <!-- Selling Rights of user End ---->


                        @endif

                        @if(Auth::user()->is_scout == 1)
                        <!--<div class="card">
                         <div class="card-header p-2" id="heading-1-2">
                           <h5 class="mb-0 text-center">
                             <a class="collapsed" role="button" data-toggle="collapse" href="#collapse-1-4" aria-expanded="false" aria-controls="collapse-1-4">
                                 <span>$66</span>
                                 <span>Bounty Gained</span>
                             </a>
                           </h5>
                         </div>
                         <div id="collapse-1-4" class="collapse" data-parent="#accordion-1" aria-labelledby="heading-1-2">
                           <div class="card-body">
                               <p>Transaction Id:<span>JL201904316293670364776 </span></p>
                               <p>Transaction Date: <span>04 April ,02:22:25PM</span></p>
    
                           </div>
                         </div>
                       </div>-->
                        @endif
                    </div>      
                </div>
            </section>
        </div>
    </div>
</div>
<div class="modal fade" id="SellingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Selling Order List </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="inen-acc">

                    <div class="table-design">
                        <table class="table table-bordered">
                            <thead>
                                <tr>

                                    <td width="20%">Rights : </td>
                                    <td width="20%">Price : </td>
                                    <td width="20%">Status : </td>
                                    <td width="20%">Transaction : </td>
                                    <td width="20%">Transaction Date : </td>

                                </tr>
                            </thead>
                            <tbody>
                                @if(count($soldArray) > 0)
                                @foreach($soldArray as $sold)
                                @php $rightArray = Helpers::getRightByRightID($sold->right_id); @endphp
                                @if($sold->is_payment_success == 0)  @php @$paymentstatus ='Payment Fail';@$classstatus ='text-danger'; @endphp @else @php @$paymentstatus ='Payment Completed';@$classstatus ='text-success'; @endphp @endif
                                @if(isset($rightArray) && !empty($rightArray->title))
                                <tr>
                                    <td>{{ucfirst($rightArray->title)}}</td>
                                    <td>${{$sold->right_price}}</td>
                                    <td><span class="{{ @$classstatus }}">{{@$paymentstatus}} </span></td>
                                    <td><div class="text-overflow2">{{ $sold->trasation_id }}</div></td>
                                    <td>{{ Helpers::getDateByFormat($sold->updated_at)}}</td>

                                </tr> 
                                @endif
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="PurchaseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Purchase Order List </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="inen-acc">

                    <div class="table-design">
                        <table class="table table-bordered">
                            <thead>
                                <tr>

                                    <td width="20%">Rights : </td>
                                    <td width="20%">Price : </td>
                                    <td width="20%">Status : </td>
                                    <td width="20%">Transaction : </td>
                                    <td width="20%">Transaction Date : </td>

                                </tr>
                            </thead>
                            <tbody>
                                @if(count($purchaseArray) > 0)
                                @foreach($purchaseArray as $purchase)
                                @php $rightArray = Helpers::getRightByRightID($purchase->right_id); @endphp
                                @if($purchase->is_payment_success == 0)  @php @$paymentstatus ='Payment Fail'; @$classstatus ='text-danger'; @endphp @else @php @$paymentstatus ='Payment Completed';@$classstatus ='text-success'; @endphp @endif
                                @if(isset($rightArray) && !empty($rightArray->title))
                                <tr>
                                    <td>{{ucfirst($rightArray->title)}}</td>
                                    <td>${{$purchase->right_price}}</td>
                                    <td><span class="{{ @$classstatus }}">{{@$paymentstatus}} </span></td>
                                    <td><div class="text-overflow2">{{ $purchase->trasation_id }}</div></td>
                                    <td>{{ Helpers::getDateByFormat($purchase->updated_at)}}</td>
                                </tr> 
                                @endif
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->is_scout == 1)
@php $IncentiveRights = Helpers::getIncentiveRightByUserID(Auth::user()->id); @endphp
<div class="modal fade" id="IncentiveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Incentive List </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="inen-acc">
                    <div class="table-design">
                        <table class="table table-bordered">
                            <thead>
                                <tr>

                                    <td width="20%">Rights : </td>
                                    <td width="20%">Price : </td>
                                    <td width="20%">Transaction Date : </td>

                                </tr>
                            </thead>
                            <tbody>
                                @if(count($IncentiveRights) > 0)
                                @foreach($IncentiveRights as $IncentiveRight)
                                <tr>
                                    <td>{{ucfirst($IncentiveRight->title)}}</td>
                                    <td>${{number_format($IncentiveRight->to_scout, 2)}}</td>
                                    <td>{{ Helpers::getDateByFormat($IncentiveRight->updated_at)}}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>

                        </table>
                    </div>
                    </div>
                </div>
            </div>
    </div>
</div>
@endif
@endsection
@section('pageTitle')
My-Account
@endsection
@section('addtional_css')

@endsection
@section('jscript')
@endsection