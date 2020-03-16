@extends('layouts.backend.admin-layout')

@section('content')

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
            <div class="row">
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
                <div class="col-md-3" id="prefetch">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Customer ID/Name',
                    'id'=>'search_keyword',
                    'autocomplete'=>'off'
                    ])
                    !!}
                </div>
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
                
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
                                                    {{-- <th>Customer Name</th> --}}
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
                                                    
                                                    {{-- <th>Virtual Account Id</th> --}}
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

<script src="https://twitter.github.io/typeahead.js/js/handlebars.js"></script>
<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>

<script>

    var messages = {
        lms_get_soa_list: "{{ URL::route('lms_get_soa_list') }}",
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
    
    var path = "{{ route('get_customer') }}";

    $(document).ready(function(){
      var sample_data = new Bloodhound({
       datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
       queryTokenizer: Bloodhound.tokenizers.whitespace,
       prefetch:path,
       remote:{
        url:path+'?query=%QUERY',
        wildcard:'%QUERY'
       }
      });
      
    
      $('#prefetch .form-control').typeahead(null, {
       name: 'sample_data',
       display: 'customer_id',
       source:sample_data,
       limit:10,
       templates:{
        suggestion:Handlebars.compile(' <div class="row"> <div class="col-md-12" style="padding-right:5px; padding-left:5px;">@{{customer}} <small>( @{{customer_id}} )</small></div> </div>') }
      });
    });
</script>

<script src="{{ asset('backend/js/lms/soa.js') }}" type="text/javascript"></script>
@endsection




