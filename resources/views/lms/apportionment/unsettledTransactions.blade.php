@extends('layouts.backend.admin-layout')

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
            <div class="row">
                @include('lms.apportionment.common.listUnsettledTransactions')
            </div>
        </div>
    </div>

</div>
@endsection

@section('jscript')
<script>

    var messages = {
        url: "{{ URL::route('apport_unsettled_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/apportionment.js') }}"></script>
@endsection




