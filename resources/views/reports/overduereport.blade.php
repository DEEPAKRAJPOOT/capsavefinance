@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin_reports_links',['active'=>'overduereport'])
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Reports</h3>
            <small> Invoice Due Report</small>
        </div>
    </section>
    <div class="card">
        <div class="card-body">
            <div class="row">
                
                   <div class="row md-12">
                <div class="col-md-3">
                    {!!
                    Form::text('from_date',
                    null,
                    [
                    'class' => 'form-control',
                    'required' => 'required',
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
                    'required' => 'required',
                    'placeholder' => 'To Date',
                    'id'=>'to_date'
                    ])
                    !!} 
                </div>
               
                <div class="col-md-5" id="prefetch">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'required' => 'required',
                    'placeholder' => 'Search by Client ID/Name',
                    'id'=>'search_keyword',
                    'autocomplete'=>'off'
                    ])
                    !!}
                </div>
             
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
               
            </div>
                  <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="lmsSoaList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
                                                     <tr role="row">
                                                    <th>Batch No</th>
                                                    <th>Batch Date</th>
                                                    <th>Bills  No.</th>
                                                     <th>Bill  Date.</th>
                                                     <th>Due  Date.</th>
                                                     <th>Bill  Amount.</th>
                                                     <th>Approve  Amount.</th>
                                                     <th> Days OD </th>
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
<style>
    .dt-buttons
    {
        float:right !important;
    }
 </style>
<script src="{{ asset('backend\theme\assets\plugins\typeahead\handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend\theme\assets\plugins\bootstrap-tagsinput\typeahead.bundle.js') }}" type="text/javascript"></script>
<script>
 var messages = {
        get_customer: "{{ route('get_customer') }}",
        lms_get_invoice_over_due_list: "{{ URL::route('lms_get_invoice_over_due_list') }}",
        pdf_invoice_over_due_url:"{{ URL::route('pdf_invoice_over_due_url') }}",
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
<script src="{{ asset('backend/js/lms/overdue.js') }}" type="text/javascript"></script>
@endsection