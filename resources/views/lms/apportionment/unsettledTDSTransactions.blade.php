@extends('layouts.backend.admin-layout')
@section('additional_css')
<style>
    .Lh-3{
        line-height:2.5;
    }
</style>
@endsection

@section('content')
@if($sanctionPageView == true)
    @include('layouts.backend.partials.admin_customer_links',['active'=>'unsettledTrans'])
                
@endif
<div class="content-wrapper">
    @if(!$sanctionPageView == true)
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manual TDS Apportionment</h3>
            <small>Unsettled TDS Transactions</small>
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
            <form id="unsettlementFrom" action="" method="post" onsubmit="return apport.validateMarkSettledTDS(this)">
             @csrf	
            <div class="row">
                @include('lms.apportionment.common.listUnsettledTransactions')
            </div>
            <div class="row pull-right action-btn">
                <div class="col-md-12" >
                    @if($paymentId) 
                        @can('apport_mark_settle_confirmation')
                            @if($paymentAppor)
                                <input type="button" value="Mark Settled" class="btn btn-success btn-sm" onclick="javascript:alert('You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.')">
                            @else
                                <input id="mark_settle_btn" type="submit" name="action" value="Mark Settled" class="btn btn-success btn-sm" disabled="true">
                            @endif
                        @endcan
                    @endif
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
        url: "{{ URL::route('apport_settledunsettled_tds_list') }}",
        confirm_settle: "{{ URL::route('apport_mark_settle_confirmation_tds',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'settlement' => 'TDS' ]) }}",
        user_id: "{{$userId}}",
        payment_id: "{{$paymentId}}",
        payment_amt: "{{ $payment_amt }}",
        payment: "{{ $payment['action_type'] }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        old_data: {!! json_encode($oldData) !!},
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection