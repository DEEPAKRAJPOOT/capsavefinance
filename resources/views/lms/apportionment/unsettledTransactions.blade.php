@extends('layouts.backend.admin-layout')
@section('additional_css')
<style>
    .Lh-3{
        line-height:2.5;
    }
</style>
@endsection

@section('content')
@if($sanctionPageView)
    @include('layouts.backend.partials.admin_customer_links',['active'=>'unsettledTrans'])
                
@endif
<div class="content-wrapper">
    @if(!$sanctionPageView)
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manual Aapprotionment</h3>
            <small>Unsettled Transactions</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Payment</li>
                <li class="active">Manage Repayment</li>
            </ol>
        </div>
    </section>
    @endif
    <div class="card">
        <div class="card-body"> 
            @if($sanctionPageView)
             <div class="table-responsive ps ps--theme_default w-100">
                      @include('lms.customer.limit_details')
                    </div>
             @endif     	
        @if(!$sanctionPageView)      
            @include('lms.apportionment.common.userDetails')
            @if($paymentId)
            @include('lms.apportionment.common.paymentDetails')
            @endif
        @endif
            <form action="{{ route('apport_mark_settle_confirmation',[ 'user_id' => $userId , 'payment_id' => $paymentId]) }}" method="post" onsubmit="return apport.validateMarkSettled(this)">
             @csrf	
            <div class="row">
                @include('lms.apportionment.common.listUnsettledTransactions')
            </div>
            <div class="row pull-right">
                <div class="col-md-12" >
                    @if($paymentId) <input type="submit" value="Mark Settled" class="btn btn-success btn-sm"> @endif
                    <input type="button" value="Wave Off" class="btn btn-success btn-sm" onclick="apport.onWaveOff()">
                </div>
            </div>
            </form>
        </div>
    </div>
     <a data-toggle="modal" data-target="#viewDetailFrame" data-url="" data-height="400px" data-width="100%" data-placement="top" class="view_detail_transaction"></a>
</div>
{!!Helpers::makeIframePopup('viewDetailFrame','Transaction Detail', 'modal-md')!!}
</div>
@endsection

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('apport_unsettled_list') }}",
        trans_waiveoff_url: "{{ URL::route('apport_trans_waiveoff') }}",
        user_id: "{{$userId}}",
        payment_id: "{{$paymentId}}",
        payment_amt: "{{$payment['payment_amt']}}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        old_data: {!! json_encode($oldData) !!},
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection