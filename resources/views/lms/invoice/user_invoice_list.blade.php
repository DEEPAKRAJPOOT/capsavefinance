@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'userInvoice'])
<div class="content-wrapper">

  <div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive ps ps--theme_default w-100" style="margin-bottom: 20px;">
           @include('lms.customer.limit_details')
          </div>
          <div class="form-fields">
            <div class="active" id="details">
              <div class="form-sections">
                <div class="row">
                  <div class="col-md-2">
                    <label class="float-left">From Date</label> 
                    <input type="text" name="from_date" id="from_date" class="form-control form-control-sm" placeholder="Select From date">
                    <span id="from_date_error" class="error"></span>
                  </div>
                  <div class="col-md-2">
                    <label class="float-left">To Date</label> 
                    <input type="text" name="to_date" id="to_date" class="form-control form-control-sm" placeholder="Select to date">
                    <span id="to_date_error" class="error"></span>
                  </div>
                  <div class="col-md-3">
                    <label class="float-left">Invoice No</label> 
                    <input type="text" name="invoice_no" id="invoice_no" class="form-control form-control-sm" placeholder="Search From Invoice No">
                    <span id="invoice_no_error" class="error"></span>
                  </div>
                  <div class="col-md-3">
                    <label>&nbsp;</label><br>
                    <button type="button" id="searchbtn" class="btn btn-success btn-sm searchbtn">Search</button>
                  </div>
                  <div class="col-md-2 text-right">
                      <label>&nbsp;</label><br>
                       @can('create_user_invoice')
                        <a href="{{ route('create_user_invoice', [ 'user_id' => $user_id ] ) }}" >
                            <button class="btn  btn-success btn-sm" type="button"><i class="fa fa-plus"></i> Create Invoice</button>
                        </a>
                        @endcan
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                      <div id="invoices_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="row">
                          <div class="col-sm-12">
                            <table id="invoices_list" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoices_list_info" style="width: 100%;">
                              <thead>
                                <tr role="row">
                                  <th>Reference No</th>
                                  <th>Int/Charge Name</th>
                                  <th>Business GST No</th>
                                  <th>Invoice No</th>
                                  <th>Invoice Date</th>
                                  <th>Due Date</th>
                                  <th>Place of Supply</th>
                                  <th>Action</th> 
                                </tr>
                              </thead>
                              <tbody>

                              </tbody>
                            </table>
                            <div id="invoices_list_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
@endsection
@section('jscript')
<script>
    var messages = {
      get_user_invoice_list: "{{ URL::route('get_user_invoice_list') }}",
      search_business: "{{URL::route('search_business')}}",
      get_customer: "{{ route('get_customer') }}",
      get_to_settle_payments: "{{ route('get_to_settle_payments') }}",
      data_not_found: "{{ trans('error_messages.data_not_found') }}",
      token: "{{ csrf_token() }}",
      user_id:"{{ $user_id }}",
    };

    $("#from_date").datetimepicker({
       setDate : new Date(),
       format: 'dd/mm/yyyy',
       autoclose: true,
       minView : 2,
    });

    $("#to_date").datetimepicker({
       setDate : new Date(),
       format: 'dd/mm/yyyy',
       autoclose: true,
       minView : 2,
    });
</script>

<script src="{{ asset('backend/js/ajax-js/lms/userInvoice.js') }}"></script>

@endsection
