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
      <h3 class="mt-2">Settled Payments</h3>

      <ol class="breadcrumb">
        <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
        <li class="active">Payments List</li>
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
                  <div class="col-md-4">
                    <label class="float-left">Search By Business Name / Customer Id </label> 
                    <input type="text" name="search_bus" id="search_bus" class="form-control form-control-sm searchBusiness" placeholder="Type your Business Name">
                    <span id="search_bus_error" class="error"></span>
                    <input type="hidden" name="customer_id" id="customer_id" class="form-control form-control-sm">
                    <input type="hidden" name="user_id" id="user_id" class="form-control form-control-sm">
                  </div>
                  <div class="col-md-2">
                    <label>&nbsp;</label><br>
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm searchbtn">Search</button>
                  </div>
                  <div class="col-md-6 text-right">
                      <label>&nbsp;</label><br>
                      <!-- <button type="button" id="move_to_settle" class="btn btn-success btn-sm move_to_settle">Move to Settle</button> -->
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                      <div id="payments_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                          <div class="col-sm-12">
                            <table id="payments_txns" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="payments_listing_info" style="width: 100%;">
                              <thead>
                                <tr role="row">
                                  <th>Customer Id</th>
                                  <th>Customer Name</th>
                                  <th>Business Name</th>
                                  <th>Amount</th>
                                  <th>Txn Type</th>
                                  <th>Updated By</th>
                                  <th>Action</th> 
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                            </table>
                            <div id="payments_listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
{!!Helpers::makeIframePopup('paymentRefundInvoice','Payment Refund Invoice', 'modal-lg')!!}
@endsection
@section('jscript')
<style>
 .business_list {
   background-color:aliceblue;  
   border: 2px solid #f7f7f7;
} 
.business_list_li {
  background-color:#f9f9f9;
  border: 1px solid antiquewhite;  
  cursor: pointer;
}

</style>
<script src="{{ asset('backend\theme\assets\plugins\typeahead\handlebars.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('backend\theme\assets\plugins\bootstrap-tagsinput\typeahead.bundle.js') }}" type="text/javascript"></script>
<script>
    var messages = {
      search_business: "{{URL::route('search_business')}}",
      get_customer: "{{ route('get_customer') }}",
      get_to_settle_payments: "{{ route('get_settled_payments') }}",
      data_not_found: "{{ trans('error_messages.data_not_found') }}",
      token: "{{ csrf_token() }}",
    };
</script>
<script src="{{ asset('backend/js/ajax-js/payments.js') }}"></script>
@endsection
