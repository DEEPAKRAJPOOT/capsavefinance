@extends('layouts.backend.admin-layout')

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
@if(request()->get('sanctionPageView'))
    @include('layouts.backend.partials.admin_customer_links',['active'=>'custSoa'])
@endif
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>SOA List for all </h3>
            <small>SOA List for all</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">SOA List for all</li>
            </ol>
        </div>
    </section>
    <div class="card">
        <div class="card-body">    
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
													<th>Batch No</th>
													<th>Invoice No</th>
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
<script src="{{ asset('backend\theme\assets\plugins\typeahead\handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend\theme\assets\plugins\bootstrap-tagsinput\typeahead.bundle.js') }}" type="text/javascript"></script>

<script>

    var messages = {
        get_colender_soa_list: "{{ URL::route('get_colender_soa_list') }}",
        get_soa_client_details:"{{ URL::route('get_soa_client_details') }}",
        pdf_soa_url:"{{ URL::route('soa_pdf_download',['user_id'=>$user['user_id'],'customer_id'=>$user['customer_id']]) }}",
        excel_soa_url:"{{ URL::route('soa_excel_download',['user_id'=>$user['user_id'],'customer_id'=>$user['customer_id']]) }}",
        get_customer: "{{ route('get_customer') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
    };
    $('#from_date').datetimepicker({
        format: 'dd/mm/yyyy',
        //  startDate: new Date(),
        autoclose: true,
        minView: 2, });
    $('#to_date').datetimepicker({
        format: 'dd/mm/yyyy',
        //  startDate: new Date(),
        autoclose: true,
        minView: 2, });
    

    $(document).ready(function(){
      var sample_data = new Bloodhound({
       datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
       queryTokenizer: Bloodhound.tokenizers.whitespace,
       prefetch:messages.get_customer,
       remote:{
        url:messages.get_customer+'?query=%QUERY',
        wildcard:'%QUERY'
       }
      });
      
    
    $('#prefetch .form-control').typeahead(null, {
        name: 'sample_data',
        display: 'customer_id',
        source:sample_data,
        limit: 'Infinity',
        templates:{
            suggestion:Handlebars.compile(' <div class="row"> <div class="col-md-12" style="padding-right:5px; padding-left:5px;">@{{biz_entity_name}} <small>( @{{customer_id}} )</small></div> </div>') 
        },
    }).bind('typeahead:select', function(ev, suggestion) {
        setClientDetails(suggestion)
    }).bind('typeahead:change', function(ev, suggestion) {
        var customer_id = $.trim($("#customer_id").val());
        if(customer_id != suggestion)
        setClientDetails(suggestion)
    }).bind('typeahead:cursorchange', function(ev, suggestion) {
        setClientDetails(suggestion)
    });
});

function setClientDetails(data){
    $("#biz_id").val(data.biz_id);
    $("#user_id").val(data.user_id);
    $("#customer_id").val(data.customer_id);
}
</script>

<script src="{{ asset('backend/js/lms/colenderSoa.js') }}" type="text/javascript"></script>
@endsection




