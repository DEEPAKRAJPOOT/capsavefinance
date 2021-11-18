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
    @include('layouts.backend.partials.admin_customer_links',['active'=>'refundTrans'])
@endif
<div class="content-wrapper">
    @if(!$sanctionPageView)
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manual Apportionment</h3>
            <small>Refund Transactions</small>
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
                    </div>  @endif 
        @if(!$sanctionPageView)        
            @include('lms.apportionment.common.userDetails')
        @endif    
            <form id="unsettlementFrom" action="" method="post" onsubmit="return apport.validateMarkSettled(this)">
            @csrf	
            <div class="row">
                @include('lms.apportionment.common.listRefundTransactions')
            </div>
            <div class="row pull-right action-btn">
                <div class="col-md-12" >
                    @can('apport_mark_adjustment_confirmation')
                        <input type="submit" name="action" value="Adjustment" class="btn btn-success btn-sm">
                    @endcan
                    {{-- <input type="submit" name="action" value="Refund" class="btn btn-success btn-sm"> --}}
                </div>
            </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('apport_refund_list') }}",
        confirm_adjustment: "{{ URL::route('apport_mark_adjustment_confirmation',[ 'user_id' => $userDetails['user_id'] , 'sanctionPageView' => $sanctionPageView ]) }}",
        user_id: "{{$userDetails['user_id']}}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}?id={{ time() }}"></script>
@endsection