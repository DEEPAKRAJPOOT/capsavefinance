@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="col-md-12 ">
        <section class="content-header">
            <div class="header-icon">
                <i class="fa fa-clipboard" aria-hidden="true"></i>
            </div>
            <div class="header-title">
                <h3 class="mt-2">Manage Invoice</h3>

                <ol class="breadcrumb">
                    <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                    <li class="active">Manage Invoice</li>
                </ol>
            </div>
            <div class="clearfix"></div>
        </section>
        <div class="row grid-margin">

            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                          <ul class="nav nav-tabs" role="tablist">
           <li class="nav-item ">
      <a class="nav-link @if(Route::currentRouteName()=='get_invoice') active @endif"  href="{{Route('get_invoice')}}">Pending</a>
    </li>
    <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_approve_invoice') active @endif"  href="{{Route('get_approve_invoice')}}">Approved</a>
    </li>
  <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_disbursed_que_invoice') active @endif"  href="{{Route('get_disbursed_que_invoice')}}">Disbursement Queue</a>
    </li>
   <li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName()=='get_sent_to_bank') active @endif" href="{{Route('get_sent_to_bank')}}">Sent to Bank</a>
    </li>
	<li class="nav-item">
            <a class="nav-link @if(Route::currentRouteName()=='get_failed_disbursed_invoice') active @endif" href="{{Route('get_failed_disbursed_invoice')}}">Failed Disbursement</a>
    </li>
   <li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_disbursed_invoice') active @endif"  href="{{Route('get_disbursed_invoice')}}">Disbursed</a>
    </li>
	<li class="nav-item">
         <a class="nav-link @if(Route::currentRouteName()=='get_repaid_invoice') active @endif" href="{{Route('get_repaid_invoice')}}">Repaid</a>
    </li>
   <li class="nav-item">
      <a class="nav-link @if(Route::currentRouteName()=='get_reject_invoice') active @endif" href="{{Route('get_reject_invoice')}}">Reject</a>

    </li>
  
   
  </ul>
                        <div class="tab-content">

                            <div id="menu1" class=" active tab-pane "><br>


                                <div class="card">
                                    <div class="card-body">
                                        <div class="row"><div class="col-md-4"></div>
                                            <div class="col-md-3">				 
                                                <input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
                                                <select class="form-control form-control-sm changeBiz searchbtn"  name="search_biz" id="search_biz">
                                                    <option value="">Select Business Name </option>
                                                    @foreach($get_bus as $row)
                                                    @php if(isset($row->business->biz_id)) { @endphp
                                                    <option value="{{{$row->business->biz_id}}}">{{{$row->business->biz_entity_name}}} </option>
                                                    @php } @endphp
                                                    @endforeach


                                                </select>
                                                <span id="anchorMsg" class="error"></span>

                                            </div>
                                            <div class="col-md-2">				 

                                                <select class="form-control form-control-sm changeAnchor searchbtn" id="changeAnchor"  name="search_anchor">

                                                </select>
                                            </div>
                                            <div class="col-md-2">		    

                                                <select readonly="readonly" class="form-control form-control-sm searchbtn" id="supplier_id" name="search_supplier">

                                                </select>
                                            </div>    


                                        </div>
                                        <div class="row">
                                            <div class="col-12 dataTables_wrapper mt-4">
                                                <div class="overflow">
                                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <table id="invoiceListBank" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                                    <thead>
                                                                        <tr role="row">
                                                                            <th>Inv. No.</th>
                                                                            <th>Anchor Detail</th>
                                                                            <th>Customer Detail</th>
                                                                            <th> Inv Detail</th>
                                                                            <th> Inv Amount</th>
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
        </div></div>

</div>

    @endsection
    @section('jscript')
<style>
    .image-upload > input
   {
      display: none;
   }

   .image-upload i
   {
      width: 80px;
      cursor: pointer;
   }
    .itemBackground 
    { 
      border: 2px solid blanchedalmond;  
      background-color:#5c9742;
    }
     .itemBackgroundColor 
    { 
      color:white;
    }
</style>    
<script>  
       var messages = {
        frontend_get_invoice_list_bank: "{{ URL::route('frontend_get_invoice_list_bank') }}",
        upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
        get_user_program_supplier: "{{ URL::route('get_user_program_supplier') }}",
        get_user_biz_anchor: "{{ URL::route('get_user_biz_anchor') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        front_program_list: "{{ URL::route('front_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
        invoice_document_save: "{{ URL::route('invoice_document_save') }}",
        update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
        token: "{{ csrf_token() }}",
    };</script>
<script src="{{ asset('frontend/js/ajax-js/invoiceAjax.js') }}"></script>
<script src="{{ asset('frontend/js/ajax-js/invoice_list_send_to_bank.js') }}"></script>

@endsection
 