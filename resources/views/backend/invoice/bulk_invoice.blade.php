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
                <h3 class="mt-2">Upload Bulk Invoice</h3>

                <ol class="breadcrumb">
                    <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                    <li class="active">Upload Bulk Invoice</li>
                </ol>
            </div>
            <div class="clearfix"></div>
        </section>
        <div class="row grid-margin">

            <div class="col-md-12 ">
                <div class="card">
                    <div class="card-body">
                        <div class="modal-content">
                            <!-- Modal Header -->

                            <!-- Modal body -->
                            <div class="modal-body ">
                           <form id="signupImageForm" action="{{Route('upload_bulk_csv_Invoice')}}" method="post" enctype='multipart/form-data'> 
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span> <!--<span id="anc_limit" class="error"></span> --> </label>
                                            <select readonly="readonly" class="form-control changeBulkAnchor" id="anchor_bulk_id" >


                                                @if(count($anchor_list) > 0)
                                                @if($anchor==11)
                                                @foreach($anchor_list as $row) 
                                                @php if(isset($row->anchorOne->anchor_id)) {  
                                                if($id==$row->anchorOne->anchor_id) { @endphp
                                                <option value="{{{$row->anchorOne->anchor_id}}}">{{{$row->anchorOne->comp_name}}}</option>
                                                @php } } @endphp
                                                @endforeach
                                                @else    
                                                <option value="">Select Anchor  </option>
                                                @foreach($anchor_list as $row) 
                                                @php if(isset($row->anchorOne->anchor_id)) { @endphp
                                                <option value="{{{$row->anchorOne->anchor_id}}}">{{{$row->anchorOne->comp_name}}}</option>
                                                @php } @endphp
                                                @endforeach
                                                @endif  
                                                @endif
                                            </select>
                                            <span id="anchor_bulk_id_msg" class="error"></span>

                                        </div></div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Product Program Name
                                                <span class="error_message_label">*</span>  <!-- <span id="pro_limit" class="error"></span> -->
                                            </label>
                                            <select readonly="readonly" class="form-control changeBulkSupplier" id="program_bulk_id" >
                                                @if($anchor==11)
                                                <option value="">Please Select</option>
                                                @if($get_program)
                                                @foreach($get_program as $row1) 
                                                <option value="{{{$row1->program->prgm_id}}},{{{$row1->app_prgm_limit_id}}}">{{{$row1->program->prgm_name}}}</option>

                                                @endforeach
                                                @endif
                                                @endif          

                                            </select>
                                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">

                                            <span id="program_bulk_id_msg" class="error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                         <label for="txtCreditPeriod">Upload Invoice Copy
                                             <span class="error_message_label">*</span><span class="error">&nbsp;&nbsp;(Please upload zip file*)</span></label>
                                        <div class="custom-file  ">

                                            <input type="file"   class="custom-file-input fileUpload" id="customImageFile" name="file_image_id">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            <span id="customImageFile_msg" class="error"></span>
                                            <span id="msgImageFile" class="text-success"></span>
                                        </div>
                                         <a href="{{url('backend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="txtCreditPeriod">Upload Invoice <span class="error_message_label">*</span></label>
                                        <div class="custom-file  ">

                                            <input type="file"   class="custom-file-input fileUpload" id="customFile" name="file_id">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            <span id="customFile_msg" class="error"></span>
                                            <span id="msgFile" class="text-success"></span>
                                        </div>

                                    </div>

                                    <div class="col-md-2">
                                        <label for="txtCreditPeriod"> <span class="error_message_label"></span></label>
                                        <div class="custom-file  ">
                                            <input type="submit"  id="submit" class="btn btn-success float-right btn-sm mt-3 ml-2" value="Upload">
                                        </div>

                                    </div>							

                                    <div class="clearfix">
                                    </div>
                                </div>
                             </form>
                                </div>
                            <form id="signupForm" action="{{Route('backend_save_bulk_invoice')}}" method="post"> 
                                @csrf
                                <div class="row">

                                    <div class="col-sm-12">
                                        <table  class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                            <thead>
                                                <tr role="row">

                                                    <th>Sr. No.</th>
                                                    <th>Invoice No</th>
                                                    <th>Invoice Date</th>
                                                    <th>Invoice Due Date</th>
                                                    <th>Invoice  Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>



                                            <tbody  class="invoiceAppendData">

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-8">
                                            <label class="error" id="tenorMsg"></label>
                                        </div>
                                        <span class="exceptionAppend"></span>
                                        <span id="final_submit_msg" class="error" style="display:none;">Total Amount  should not greater Program Limit</span>
                                        <input type="hidden" value="" id="tenor" name="tenor">
                                        <input type="hidden" value="" id="tenor_old_invoice" name="tenor_old_invoice"> 
                                        <input type="hidden" value="" id="prgm_offer_id" name="prgm_offer_id">
                                        <input type="submit" id="final_submit" class="btn btn-secondary btn-sm mt-3 float-right finalButton" value="Final Submit"> 	
                                    </div> 

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div></div>




</div>
@endsection
@section('jscript')

<script>

    var messages = {
        backend_get_invoice_list: "{{ URL::route('backend_get_invoice_list') }}",
        upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
        get_program_supplier: "{{ URL::route('get_program_supplier') }}",
        get_tenor: "{{ URL::route('get_tenor') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        front_program_list: "{{ URL::route('front_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        delete_temp_invoice: "{{ URL::route('delete_temp_invoice') }}",

        token: "{{ csrf_token() }}",
    };

   
</script>
<script src="{{ asset('backend/js/bulk_invoice.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/invoice_list.js') }}"></script>

@endsection
