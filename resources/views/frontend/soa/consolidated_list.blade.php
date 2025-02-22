@extends('layouts.app')
@section('additional_css')
<style>
    .table td {
        border: inherit !important; 
    }
    div.dt-buttons {
        position: relative;
        float: right;
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
            <h3>SOA List</h3>
            <small>SOA List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">SOA List</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">    
            <div class="table-responsive ps ps--theme_default w-100">
                @include('frontend.soa.limit_details')
            </div>
            <div class="row" id="client_details"></div>   
            <div class="row mt-4">
                <div class="col-md-3">
                    {!!
                    Form::text('from_date',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'From Date',
                    'id'=>'from_date'
                    ])
                    !!} 
                </div>
                 <div class="col-md-3">
                    {!!
                    Form::text('to_date',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'To Date',
                    'id'=>'to_date'
                    ])
                    !!} 
                </div>
                <div class="col-md-3">
                {!! Form::select('trans_entry_type', $transTypes, Null, [
                        'class' => 'form-control',
                        'placeholder' => 'Select Transaction Type',
                        'id'=>'to_date'
                        ]) !!}
                </div>
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
                {!! Form::hidden('biz_id', 
                    isset($user['biz_id'])?$user['biz_id']:null, 
                    [ 'id'=>'biz_id']) 
                !!}

                {!! Form::hidden('user_id', 
                    isset($user['user_id'])?$user['user_id']:null, 
                    [ 'id'=>'user_id' ]) 
                !!}

                {!! Form::hidden('customer_id',  
                    isset($user['customer_id'])?$user['customer_id']:null, 
                    [ 'id'=>'customer_id' ])
                !!}
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="lmsSoaList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
                                            <tr role="row">
                                                <th>Customer ID</th>
                                                <th>Tran Date</th>
                                                <th>Value Date</th>
                                                <th>Tran Type</th>
                                                <th>UTR No</th>
                                                <th>Batch No</th>
                                                <th>Invoice No</th>
                                                <th>Capsave Invoice No</th>
                                                <th>Narration</th>
                                                <th>Currency</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Balance</th>
							                </tr>
	                                        </thead>
	                                        <tbody>
	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="lmsSoaList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
        lms_get_soa_list: "{{ URL::route('front_ajax_user_soa_consolidated_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        excel_soa_url: "{{ URL::route('front_soa_excel_download',['user_id' => $user['user_id'] ?? '','customer_id' => $user['customer_id'] ?? '', 'soaType' => 'customerSoa']) }}",
    };
    $('#from_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2
    });
    $('#to_date').datetimepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        minView: 2
    });
</script>

<script src="{{ asset('frontend/js/soa.js') }}" type="text/javascript"></script>
@endsection