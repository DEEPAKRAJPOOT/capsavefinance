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
            <h3>Co-lender Application</h3>
            <small>Co-lender Application List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Co-lender</li>
                <li class="active">Co-lender Application</li>
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
                    'placeholder' => 'Search by Customer Name, Eamil',
                    'id'=>'by_name'
                    ])
                    !!}
                </div>
                <div class="col-md-4">

                    {!!
                    Form::text('customer_id',null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Customer Id',
                    'id'=>'customer_id'
                    ])
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
                                                    <th >Cust ID</th>       
                                                    <th >App ID</th>       
                                                    <th >Virtual ID</th>        
                                                    <th >Customer Detail</th>
                                                    <th >Product Limit</th>
                                                    <th >Utilize Limit</th>
                                                    <th >Available Limit</th>
                                                    <th >Anchor Detail</th>
                                                    <th >Status</th>
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
        get_colender_applications: "{{ URL::route('ajax_colender_app_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('backend/js/ajax-js/colender.js') }}" type="text/javascript"></script>
@endsection