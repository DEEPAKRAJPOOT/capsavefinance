@extends('layouts.app')
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
                   @php   if(count($getBulkInvoice)== 0) { @endphp
                            <!-- Modal body -->
                            <div class="modal-body ">

                                 <form id="signupForm" action="{{Route('frontend_save_bulk_invoice')}}" method="post" enctype='multipart/form-data'> 
                                @csrf
                                <div class="row">
                                          <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span> <!--<span id="anc_limit" class="error"></span> --> </label>
                                            <select readonly="readonly" name="anchor_name" class="form-control changeBulkAnchor" id="anchor_bulk_id" >
                                                <option value="">Select Anchor  </option>
                                                @foreach($anchor_list as $row)
                                                @php if(isset($row->anchorOne->anchor_id)) { @endphp
                                                <option value="{{{$row->anchorOne->anchor_id}}}">{{{$row->anchorOne->comp_name}}}</option>
                                                @php } @endphp
                                                @endforeach
                                            </select>
                                            <span id="anchor_bulk_id_msg" class="error"></span>

                                        </div></div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="txtCreditPeriod">Product Program Name
                                                <span class="error_message_label">*</span>  <!-- <span id="pro_limit" class="error"></span> -->
                                            </label>
                                            <select readonly="readonly"  name="program_name" class="form-control changeBulkSupplier" id="program_bulk_id" >
                                            </select>
                                            <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">

                                            <span id="program_bulk_id_msg" class="error"></span>
                                        </div>
                                    </div>
                                       <div class="col-md-6">
                                        <div class="form-group">
                                         <label for="txtCreditPeriod">Upload Invoice Copy
                                             <span class="error_message_label">*</span><span class="error">&nbsp;&nbsp;(zip file contains copy of invoice.)</span></label>
                                        <div class="custom-file  ">

                                            <input type="file"   class="custom-file-input fileUpload" id="customImageFile" data-id="1" name="file_image_id">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            <span id="customImageFile_msg" class="error"></span>
                                            <span id="msgImageFile" class="text-success"></span>
                                        </div>
                                      
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="txtCreditPeriod">Upload Invoice <span class="error_message_label">*&nbsp;&nbsp;(Only csv file format allowed.)</span></label>
                                        <div class="custom-file  ">

                                            <input type="file"   class="custom-file-input fileUpload" data-id="2" id="customFile" name="file_id">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            <span id="customFile_msg" class="error"></span>
                                            <span id="msgFile" class="text-success"></span>
                                        </div>
                                     <a href="{{url('frontend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
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
                              @php   } @endphp 
                                 <div class="row" id="setInvoiceCount" data-count="{{count($getBulkInvoice)}}">
                                    <div class="col-sm-12">
                                        <table  class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                            <thead>
                                                <tr role="row">

                                                    <th>Sr. No.</th>
                                                     <th>Customer  Detail</th>
                                                    <th>Anchor Detail</th>
                                                    <th>Invoice Detail</th>
                                                    <th>Invoice  Amount</th>
                                                    <th>Remark</th>
                                                    <th>Action </th>
                                                </tr>
                                            </thead>
                                           
                                            @php if(count($getBulkInvoice) > 0) { @endphp
                                            @foreach($getBulkInvoice as $key=>$val) 
                                           <tr id="deleteRow{{$val->invoice_bulk_upload_id}}"  @php if($val->status==2) { @endphp style="background-color: #ff00004d" @php } @endphp  class="getUploadBulkId"  data-id="{{$val->invoice_bulk_upload_id}}" data-status="{{$val->status}}"> 
                                            <td>{{$key+1}}</td>
                                            <td>
                                                    <span><b>Name:&nbsp;</b>{{$val->supplier->f_name}} {{$val->supplier->l_name}}</span><br>
                                                    <span><b>Customer Id :&nbsp;</b>{{$val->lms_user->customer_id}}</span>
                                            </td>
                                            <td>
                                                    <span><b>Name:&nbsp;</b>{{$val->anchor->comp_name}}</span><br>
                                                    <span><b>Program:&nbsp;</b>{{$val->program->prgm_name}}</span><br>
                                                    <span><b>Business Name:&nbsp;</b>{{$val->business->biz_entity_name}}</span>
                                            </td>
                                             <td>
                                                    <span><b>Date:&nbsp;</b>{{$val->invoice_date}}</span><br>
                                                    <span><b>Due Date:&nbsp;</b>{{$val->invoice_due_date}}</span><br>
                                                    <span><b>Tenor In Days:&nbsp;</b>{{$val->tenor}}</span>
                                             </td>
                                                <td>{{number_format($val->invoice_approve_amount)}}</td>
                                                 <td>
                                                     <span class="error">{{($val->limit_exceed==1) ? 'Limit exceeded' : ''}} </span></br>
                                                     {{$val->comm_txt}}
                                                     
                                                 </td>
                                                <td><button class="btn deleteTempInv" data-id="{{$val->invoice_bulk_upload_id}}"><i class="fa fa-trash"></i></button></td>
                                            </tr>
                                          @endforeach     
                                          @php } else { @endphp      
                                          <tr>
                                              <td colspan="6" class="error">No data found...</td>
                                          </tr>
                                          @php }@endphp       
                                        </table>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-8">
                                            <label class="error" id="tenorMsg"></label>
                                        </div>
                                        <span class="exceptionAppend"></span>
                                        <span id="final_submit_msg" class="error" style="display:none;">Total Amount  should not greater Program Limit</span>
                                        @php   if(count($getBulkInvoice) > 0) { @endphp
                                        <input type="submit" id="final_submit" class="btn btn-secondary btn-sm mt-3 float-right" value="Final Submit"> 	
                                       @php  } @endphp
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
<script>

    var messages = {
        backend_get_invoice_list: "{{ URL::route('backend_get_invoice_list') }}",
        upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
        get_program_supplier: "{{ URL::route('get_program_supplier') }}",
        get_tenor: "{{ URL::route('get_tenor') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        front_lms_program_list: "{{ URL::route('front_lms_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        delete_temp_invoice: "{{ URL::route('delete_temp_invoice') }}",

        token: "{{ csrf_token() }}",
    };

</script>
<script src="{{ asset('frontend/js/bulk_invoice.js') }}"></script>
@endsection
