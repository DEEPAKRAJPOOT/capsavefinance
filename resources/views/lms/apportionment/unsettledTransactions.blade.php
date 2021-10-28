@extends('layouts.backend.admin-layout')
@section('additional_css')
<style>
    .Lh-3{
        line-height:2.5;
    }
    .table-wrapper-scroll-y {
        display: block;
    }
    .my-custom-scrollbar {
        position: relative;
        height: 50vh;
        overflow: auto;
    }
    .ps--active-x .ps__scrollbar-x-rail {
        display: none !important;
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
            <h3>Manual Apportionment</h3>
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
            <form id="unsettlementFrom" action="" method="post" onsubmit="return apport.validateMarkSettled(this)">
             @csrf	
            <div class="row">
                @include('lms.apportionment.common.listUnsettledTransactions')
            </div>
            <div class="row pull-right action-btn mt-2">
                <div class="col-md-12" >
                    @if($paymentId) 
                        @can('apport_mark_settle_confirmation')
                            <input type="submit" name="action" value="Mark Settled" class="btn btn-success btn-sm">
                        @endcan
                    @endif
                    @if($sanctionPageView) 
                        @can('apport_trans_waiveoff')
                        <input type="button" value="Waived Off" class="btn btn-success btn-sm" onclick="apport.onWaveOff()">
                        @endcan
                        @if($userDetails['status_id'] == 41 && in_array($userDetails['wo_status_id'],[config('lms.WRITE_OFF_STATUS.APPROVED'),config('lms.WRITE_OFF_STATUS.TRANSACTION_SETTLED')]))
                            <input type="submit" name="action" value="Write Off" class="btn btn-success btn-sm">
                        @endif
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
        url: "{{ URL::route('apport_unsettled_list') }}",
        confirm_writeoff: "{{ URL::route('apport_mark_writeOff_confirmation',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView ]) }}",
        confirm_settle: "{{ URL::route('apport_mark_settle_confirmation',[ 'user_id' => $userId , 'payment_id' => $paymentId, 'sanctionPageView' => $sanctionPageView ]) }}",
        trans_waiveoff_url: "{{ URL::route('apport_trans_waiveoff',['sanctionPageView' => $sanctionPageView]) }}",
        user_id: "{{$userId}}",
        payment_id: "{{$paymentId}}",
        payment_amt: "{{ $payment_amt }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        old_data: {!! json_encode($oldData) !!},
        token: "{{ csrf_token() }}",
    };

    jQuery(document).ready(function ($) {
        setTimeout(() => {
            $('.table-responsive').scrollLeft($('.table-responsive').scrollLeft() + 20);
        }, 1000);
    }); 
    
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection