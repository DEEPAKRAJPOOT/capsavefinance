@extends('layouts.backend.admin-layout')

@section('additional_css')
<style>
    .Lh-3{
        line-height:2.5;
    }
</style>
@endsection

@section('content')

<div class="content-wrapper">

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

    <div class="card">
        <div class="card-body">       
            @include('lms.apportionment.common.userDetails')
            @include('lms.apportionment.common.paymentDetails')
            <form action="{{ route('apport_mark_settle_confirmation',[ 'user_id' => $userId , 'payment_id' => $paymentId]) }}" method="post" onsubmit="return apport.validateMarkSettled(this)">
             @csrf	
            <div class="row">
                @include('lms.apportionment.common.listUnsettledTransactions')
            </div>
            <div class="row pull-right">
                <div class="col-md-12" >
                    <input type="submit" value="Mark Settled" class="btn btn-success btn-sm">
                    <input type="button" value="Wave Off" class="btn btn-success btn-sm">
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
        url: "{{ URL::route('apport_unsettled_list') }}",
        user_id: "{{$userId}}",
        payment_id: "{{$paymentId}}",
        payment_amt: "{{$payment['payment_amt']}}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection