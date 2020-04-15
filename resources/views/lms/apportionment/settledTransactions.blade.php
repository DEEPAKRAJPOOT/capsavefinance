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
            <small>Settled Transactions</small>
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
            <div class="row">
                @include('lms.apportionment.common.listSettledTransactions')
            </div>
        </div>
    </div>

</div>
@endsection

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('apport_settled_list') }}",
        user_id: "542",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection