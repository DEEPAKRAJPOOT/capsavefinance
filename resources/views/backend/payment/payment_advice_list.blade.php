@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
<div class="col-md-12 ">
   <section class="content-header">
   <div class="header-icon">
      <i class="fa fa-clipboard" aria-hidden="true"></i>
   </div>
   <div class="header-title">
      <h3 class="mt-2">Payment Advice</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active">Payment Advice</li>
      </ol>
   </div>

   <div class="clearfix"></div>
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
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button><br>

                <span id="upload_msg" class="error" style="display: none;"></span>
                <a href="{{route('payment_advice_excel')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>

                
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="payment_advice"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">
													<th>Cust ID</th>
													<th>Customer Name</th>
													<th>Tran Date</th>
													<th>Value Date</th>
													<th>Credite</th>
													<th>Action</th>
                                                    
                                                    <!-- <th>Virtual Account Id</th>
													<th>Currency</th> -->
												</tr>
	                                        </thead>
	                                        <tbody>

	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="payment_advice_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row grid-margin">



</div></div>
</div>
    @endsection
    @section('jscript')
<script>

    var messages = {
            backend_get_payment_advice: "{{ URL::route('backend_get_payment_advice') }}",
            save_excel_payment: "{{ URL::route('save_excel_payment') }}", 
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
<script src="{{ asset('backend/js/ajax-js/payment_advice.js') }}"></script>

@endsection
 