@extends('layouts.backend.admin-layout')
@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap.min.css">
<style>
    select[name='leadMaster_length']{
        height: calc(1.8125rem + 2px);
        margin: 0 10px 0 10px;
        width: 100px;
    }
    input[type='search']{
        height: calc(1.8125rem + 2px);
        display: inline;
        position: absolute;
        border: 1px solid rgba(0, 0, 0, 0.15);
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Fi/Rcu Application</h3>
            <small>Fi/Rcu Application List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Fi/Rcu Application</li>
                <li class="active">Fi/Rcu Application</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-md-4">

                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by App Id, Name',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>
                <div class="col-md-4">

                    {!!
                    Form::select('is_assign',
                    [''=>'Status', '1'=>'Assigned','0'=> 'Not Assigned'],
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                </div>
                <button type="button" id="searchbtn" class="btn btn-success btn-sm float-right">Search</button>
           </div>
           <div class="row">     
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="appList" class="table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ trans('frontend.app_list_head.app_id') }}</th>
                                                <th>{{ trans('frontend.app_list_head.business_name') }}</th>
                                                <th>{{ trans('frontend.app_list_head.user_name') }}</th>
                                                <th>{{ trans('frontend.app_list_head.user_email') }}</th>
                                                <th>{{ trans('frontend.app_list_head.user_phone') }}</th>
                                                <th>{{ trans('frontend.app_list_head.anchor') }}</th>
                                                <th>{{ trans('frontend.app_list_head.applied_loan_amount') }}</th>
                                                <th>{{ trans('frontend.app_list_head.created_date') }}</th>
                                                <th>{{ trans('frontend.app_list_head.status') }}</th>
                                                <!-- <th>{{ trans('frontend.app_list_head.action') }}</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div id="supplier-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('jscript')
<script>

    var messages = {
        get_agency_applications: "{{ URL::route('ajax_fircu_app_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('backend/js/ajax-js/fircu.js') }}" type="text/javascript"></script>
@endsection