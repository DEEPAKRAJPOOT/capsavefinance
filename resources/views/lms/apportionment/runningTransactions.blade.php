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
    @include('layouts.backend.partials.admin_customer_links',['active'=>'runningTrans'])
                
@endif
<div class="content-wrapper">
    @if(!$sanctionPageView == true)
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manual Apportionment</h3>
            <small>Running Unsettled Transactions</small>
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
            <form action="{{ route('apport_running_save',[ 'user_id' => $userId ,  'sanctionPageView' => $sanctionPageView ]) }}" method="post" onsubmit="return apport.validateRunningPosted(this)">
             @csrf	
            <div class="row">
                @include('lms.apportionment.common.listRunningTransactions')
            </div>
            <div class="row pull-right action-btn">
                <div class="col-md-12" >
                  @can('apport_running_save')
                    <input type="submit" value="Mark Posted" class="btn btn-success btn-sm" >
                  @endcan
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
        url: "{{ URL::route('apport_running_list') }}",
        user_id: "{{$userId}}",
        payment_amt :"{{ $unsettledAmt }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection