@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
      <section class="content-header">
   <div class="header-icon">
      <i class="fa fa-clipboard" aria-hidden="true"></i>
   </div>
   <div class="header-title">
      <h3 class="mt-2">Repayment List</h3>
     
      <ol class="breadcrumb">
         <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
         <li class="active"> Repayment List</li>
      </ol>
   </div>
   <div class="clearfix"></div>
</section>
<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="form-fields">
                    <div class="active" id="details">
                        <div class="form-sections">
						<div class="row">
						<div class="col-md-2">
						<label class="float-left">Transaction Date
						</label> 
						<input type="text" name="date" class="form-control form-control-sm date_of_birth datepicker-dis-fdate" value="">
						</div>
							<div class="col-md-2">
						<label>Type</label>
						<select class="form-control form-control-sm" name="type">
                                                    <option value="">All</option>
                                                    <option value="1">Manual</option>
                                                    <option value="2">Excel Upload</option>
						</select>
						</div>
						<div class="col-md-2">
						<label>&nbsp;</label><br>
						<button type="button" class="btn btn-success btn-sm searchbtn">Search</button>
						</div>
						<div class="col-md-6 text-right">
						<label>&nbsp;</label><br>
						<a href="{{route('add_payment')}}" class="btn btn-primary btn-sm">Add Manual</a>
                                                <!--<a  data-toggle="modal" data-target="#modalUploadPayment" data-url ="{{route('excel_bulk_payment', [])}}" data-height="250px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Anchor Detail">Excel</a> -->
                                                <a href="{{route('excel_payment_list')}}" class="btn btn-primary btn-sm">Excel</a>
						</div>
						</div>
						       <div class="row">
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <input type="hidden" name="front" value="front">
                                    <table id="invoiceListTransaction" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                        <thead>
                                            <tr role="row">
                                              
                                                <th>Customer Detail</th>
                                                <th>Repayment Detail</th>
                                                <th>Payment Method</th>
                                                 <th> Comment</th>
                                                <th>Created Date</th> 
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
            </div>
        </div>
    </div>
</div>

  </div>
  {!!Helpers::makeIframePopup('modalUploadPayment','Upload Payment', 'modal-lg')!!}

    @endsection
    @section('jscript')
<script>

    var messages = {
            backend_get_bulk_transaction: "{{ URL::route('backend_get_bulk_transaction') }}",
             data_not_found: "{{ trans('error_messages.data_not_found') }}",
            token: "{{ csrf_token() }}",
 };
 
 
  $(document).ready(function () {
      ////here code ////////////////
}); 
  
  
</script>
<script src="{{ asset('backend/js/ajax-js/bulk_transaction.js') }}"></script>

@endsection
 