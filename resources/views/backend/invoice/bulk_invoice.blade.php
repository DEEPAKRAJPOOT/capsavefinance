@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
<div class="content-wrapper">
    <div class="col-md-12 ">
          @if(Session::has('multiVali'))
          @php $multiVali = Session::get('multiVali');  @endphp
        <div class=" alert-danger alert" role="alert">
       <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
      @foreach ($multiVali as $key=>$val)
      {{($key!='status') ? $val : ''}}</br>
      @endforeach
    </div>
     @endif
       <span id="storeSuccessMsg"></span>
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
                           <form id="signupImageForm" action="{{Route('upload_bulk_csv_Invoice')}}" method="post" enctype='multipart/form-data'> 
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                        <div class="form-group">
                                <label for="txtCreditPeriod">Anchor Business Name  <span class="error_message_label">*</span> <!--<span id="anc_limit" class="error"></span> --> </label>
                                            <select readonly="readonly" class="form-control changeBulkAnchor changeAnchor" id="anchor_bulk_id" name="anchor_name">


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
                                            <select readonly="readonly" class="form-control changeBulkSupplier" id="program_bulk_id" name="program_name">
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
                                    <div class="col-md-6 check_upload_inv">
                                        <div class="form-group">
                                         <label for="txtCreditPeriod">Upload Invoice Copy
                                             <span class="error_message_label customFile_astrik"></span><span class="error">&nbsp;&nbsp;(zip file contains copy of invoice.)</span></label>
                                        <div class="custom-file  ">

                                            <input type="file"   class="custom-file-input fileUpload" id="customImageFile" data-id="1" name="file_image_id">
                                            <label class="custom-file-label" for="customFile">Choose file</label>
                                            <span id="customImageFile_msg" class="error"></span>
                                            <span id="msgImageFile" class="text-success"></span>
                                            <input type="hidden" name="customImageFileval" id="customImageFileval" val="0"></span>
                                            
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
   <a href="{{url('backend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
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
                                        @can('download_bulk_invoice')
                                        @if(count($getBulkInvoice) > 0)
                                        <a href="{{ route('download_bulk_invoice') }}" title="Download Bulk Invoice" class="btn btn-success btn-sm float-right ml-3" style="margin: 0px 0 10px 0;"><i class="fa fa-download" aria-hidden="true"></i> Download</a>
                                        @endif
                                        @endcan
                                        <table  class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                            <thead>
                                                <tr role="row">

                                                    <th>Sr. No.</th>
                                                     <th>Invoice number </th>
                                                     <th>Customer  Detail</th>
                                                    <th>Anchor Detail</th>
                                                    <th>Invoice Detail</th>
                                                    <th>Invoice  Amount</th>
                                                     <th>Uploaded By</th>
                                                    <th>Remark</th>
                                                    <th>Action </th>
                                                </tr>
                                            </thead>
                                           
                                            @php if(count($getBulkInvoice) > 0) { @endphp
                                            @foreach($getBulkInvoice as $key=>$val) 
                                           <tr id="deleteRow{{$val->invoice_bulk_upload_id}}"  @php if($val->status==2) { @endphp style="background-color: #ff00004d" @php } @endphp  class="getUploadBulkId"  data-id="{{$val->invoice_bulk_upload_id}}" data-status="{{$val->status}}"> 
                                            <td>{{$key+1}}</td>
                                             
                                                  <td>{{$val->invoice_no}}</td>
                                            <td>
                                                    <span><b>Name:&nbsp;</b>{{$val->supplier->f_name}} {{$val->supplier->l_name}}</span><br>
                                                    <span><b>Customer Id :&nbsp;</b>{{$val->lms_user->customer_id}}</span><br/>
                                                    <span><b>Business Name:&nbsp;</b>{{$val->business->biz_entity_name}}</span>
                                            </td>
                                            <td>
                                                    <span><b>Name:&nbsp;</b>{{$val->anchor->comp_name}}</span><br>
                                                    <span><b>Program:&nbsp;</b>{{$val->program->prgm_name}}</span>
                                            </td>
                                             <td>
                                                    <span><b>Date:&nbsp;</b>{{\Carbon\Carbon::parse($val->invoice_date)->format('d-m-Y')}}</span><br>
                                                    <span><b>Due Date:&nbsp;</b>{{\Carbon\Carbon::parse($val->invoice_due_date)->format('d-m-Y')}}</span><br>
                                                    <span><b>Tenor In Days:&nbsp;</b>{{$val->tenor}}</span>
                                             </td>
                                             <td>
                                                <span><b>Inv. Appr. Amt.:&nbsp;</b>{{number_format($val->invoice_approve_amount)}}</span><br>
                                                <span><b>Upfront Int. Amt.:&nbsp;</b>{{$val->upfront_interest}}</span><br>
                                            </td>
                                              <td>
                                                    <span><b>Name:&nbsp;</b>{{$val->user->f_name}} {{$val->user->l_name}}</span><br>
                                                    <span><b>Date:&nbsp;</b>{{date('d-m-Y',strtotime($val->created_at))}}</span>
                                            </td>
                                                
                                                 <td>
                                                     <span class="error">{{($val->limit_exceed==1) ? 'Limit exceeded' : ''}} </span></br>
                                                     {{$val->comm_txt}}
                                                     
                                                 </td>
                                         
                                                <td><button class="btn deleteTempInv" data-id="{{$val->invoice_bulk_upload_id}}"><i class="fa fa-trash"></i></button></td>
                                            </tr>
                                          @endforeach     
                                          @php } else { @endphp      
                                          <tr>
                                              <td colspan="9" class="error" align="center">No data found...</td>
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
        front_program_list: "{{ URL::route('front_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        delete_temp_invoice: "{{ URL::route('delete_temp_invoice') }}",
        chk_anchor_blk_inv_req: "{{ URL::route('chk_anchor_blk_inv_req') }}",
        token: "{{ csrf_token() }}",
    };

    // $(".check_upload_inv").hide();
    $(document).ready(function () {
      $(document).on('change blur keyup', '.changeAnchor', function(){
            var anchorID = $(this).val();
            // alert("hh");
            $.ajax({
               url: messages.chk_anchor_blk_inv_req,
               type: 'POST',
               data: {
                     'anchorID' : anchorID,
                     '_token' : messages.token,
               },
               success: function(response){
                  if(response['status'] === '1') {
                    //  $(".check_upload_inv").show();
                    $(".customFile_astrik").html('*');
                    $("#customImageFileval").val('1');
                  } else {
                    //  $(".check_upload_inv").hide();
                    $(".customFile_astrik").html('');
                    $("#customImageFileval").val('0');
                  }
               }
            });
         });      
   })   
</script>
<script src="{{ asset('backend/js/bulk_invoice.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/invoice_list.js') }}"></script>

@endsection
