@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@if($flag == 1)
@include('layouts.backend.partials.admin_customer_links',['active' => 'invoice'])
@endif
<div class="content-wrapper">
 <div class="col-md-12 ">
      
        <div class="row grid-margin">

            <div class="col-md-12 ">
                <div class="card">
                      <div class="card-body">
                    <div class="table-responsive ps ps--theme_default w-100">
                        <table class="table  table-td-right">
                              <tbody>
                                <tr>
                                    <td class="text-left" width="30%"><b>Business Name</b></td>
                                    <td> {{$userInfo->biz->biz_entity_name}}	</td> 
                                     <td class="text-left" width="30%"><b>Full Name</b></td>
                                    <td>{{$userInfo->f_name}} {{$userInfo->m_name}}	{{$userInfo->l_name}}</td> 
                                   
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%"><b>Email</b></td>
                                    <td>{{$userInfo->email}}	</td> 
                                     <td class="text-left" width="30%"><b>Mobile</b></td>
                                    <td>{{$userInfo->mobile_no}} </td> 
                                </tr>
                                
                                <tr>
                                    <td class="text-left" width="30%"><b>Total Limit</b></td>
                                    <td>{{ $userInfo->total_limit }} </td> 
                                    <td class="text-left" width="30%"><b>Available Limit</b></td>
                                    <td>{{  $userInfo->consume_limit }} </td> 
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%"><b>Utilize Limit</b></td>
                                    <td>{{ $userInfo->utilize_limit }} </td> 
                                    <td class="text-left" width="30%"><b>Sales Manager</b></td>
                                    <td>{{ (isset($userInfo->anchor->salesUser)) ? $userInfo->anchor->salesUser->f_name.' '.$userInfo->anchor->salesUser->m_name.' '.$userInfo->anchor->salesUser->l_name : '' }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>	
                        <div class="tab-content">

                            <div id="menu1" class=" active tab-pane "><br>
                             <span id="moveCase" class="text-success"></span>
                               <div class="row">
                                   <div class="col-md-9">
                                       
                                       <input type="hidden" name="user_id" value="{{($user_id) ? $user_id : ''}}">
                                   </div>
                                                 <div class="col-md-3">
                                                <select class="form-control form-control-sm changeBiz searchbtn" name="status_id">
                                                    <option value=""> Select Invoice Status</option>  
                                                        @foreach($status as $row)
                                                        <option value="{{{$row->id}}}">{{{$row->status_name}}} </option>
                                                        @endforeach
                                                </select>
                                            </div>
                                           
                                        </div>
                                        <div class="row">
                                            <div class="col-12 dataTables_wrapper mt-4">
                                                <div class="overflow">
                                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <table id="invoiceList" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                                    <thead>
                                                                        <tr role="row">
                                                                           
                                                                            <th>Inv. No.</th>
                                                                            <th>Anchor Detail</th>
                                                                            <th>Customer Detail</th>
                                                                            <th> Inv Detail</th>
                                                                            <th> Inv Amount</th>
                                                                            <th> Status</th>
                                                                            
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
        </div></div>




    <div class="modal align-middle" id="myModal6" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5>Upload Invoices</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body ">
                    <form id="signupForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtCreditPeriod">Anchor Name  <span class="error_message_label">*</span></label>
                                    <select readonly="readonly" class="form-control changeBulkAnchor" id="anchor_bulk_id"  name="anchor_bulk_id">

                                        <option value="">Select Anchor  </option>
                                        @foreach($anchor_list as $row)
                                        @php if(isset($row->anchor->anchor_id)) { @endphp
                                        <option value="{{{$row->anchor->anchor_id}}}">{{{$row->anchor->comp_name}}}  </option>
                                          @php } @endphp
                                        @endforeach
                                    </select>
                                    <span id="anc_limit"></span>

                                </div></div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtCreditPeriod">Product Program Name
                                        <span class="error_message_label">*</span>
                                    </label>
                                    <select readonly="readonly" class="form-control changeBulkSupplier" id="program_bulk_id" name="supplier_bulk_id">
                                    </select>
                                    <input type="hidden" id="pro_limit_hide" name="pro_limit_hide">
                                    <span id="pro_limit"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="txtCreditPeriod">Customer Name <span class="error_message_label">*</span></label>
                                    <select readonly="readonly" class="form-control" id="supplier_bulk_id" name="supplier_bulk_id">
                                    </select>
                                    <a href="{{url('backend/assets/invoice/invoice-template.csv')}}" class="mt-1 float-left"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Download Template</a>
                                </div>
                            </div>



                            <div class="clearfix">
                            </div>
                        </div>	
                        <h5 id="submitInvoiceMsg" class="text-success"></h5>
                        <button type="submit" id="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Upload</button> 
                        <button type="reset" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 	

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal show" id="myModal7" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h5>Confirm Invoice Approved Amount</h5>
                <button type="button" class="close close-btns" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body text-left">
                <form id="signupFormNew"  action="{{Route('update_invoice_amount')}}" method="post">
                    @csrf	
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Invoice Amount
                                    <span class="mandatory">*</span>
                                </label>
                                <input type="text" class="form-control" id="invoice_amount" value="" disabled="">
                                <input type="hidden" name="invoice_id" id="invoice_id">
                            </div>
                            <div class="form-group">
                                <label for="txtCreditPeriod">Invoice Approved Amount
                                    <span class="mandatory">*</span>
                                </label>
                                <input type="text" class="form-control" id="invoice_approve_amount" name="approve_invoice_amount" value="Enter Amount">

                            </div>

                            <div class="form-group">
                                <label for="txtCreditPeriod">Comment  <span class="error_message_label doc-error">*</span>

                                </label>
                                <textarea class="form-control" name="comment" id="comment" cols="4" rows="4"></textarea>

                            </div>
                        </div>



                    </div>
                    <span class="model7msg error"></span>			
                    <input type="submit" id="UpdateInvoiceAmount" class="btn btn-success float-right btn-sm mt-3" value="Submit"> 
                </form> 
            </div>
        </div>
    </div>
</div>

@endsection
@section('jscript')
<style>
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
        user_wise_invoice_list: "{{ URL::route('user_wise_invoice_list') }}",
        upload_invoice_csv: "{{ URL::route('upload_invoice_csv') }}",
        get_program_supplier: "{{ URL::route('get_program_supplier') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        front_program_list: "{{ URL::route('front_program_list') }}",
        front_supplier_list: "{{ URL::route('front_supplier_list') }}",
        update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
        invoice_document_save: "{{ URL::route('invoice_document_save') }}",
        update_bulk_invoice: "{{ URL::route('update_bulk_invoice') }}",
        token: "{{ csrf_token() }}",
    };


    $(document).ready(function () {
       
         document.getElementById('invoice_approve_amount').addEventListener('input', event =>
         event.target.value = (parseInt(event.target.value.replace(/[^\d]+/gi, '')) || 0).toLocaleString('en-US'));
         $("#program_bulk_id").append("<option value=''>No data found</option>");
         $("#program_bulk_id").append("<option value=''>No data found</option>");
    });


    //////////////// for checked & unchecked////////////////
    $(document).on('click', '#chkAll', function () {
        var isChecked = $("#chkAll").is(':checked');
        if (isChecked)
        {
            $('input:checkbox').attr('checked', 'checked');
        } else
        {
            $('input:checkbox').removeAttr('checked');
        }
    });

    ///////////////////////For Invoice Approve////////////////////////
    $(document).on('click', '.approveInv', function () {
        $("#moveCase").html('');
        if (confirm('Are you sure? You want to approve it.'))
        {
            var invoice_id = $(this).attr('data-id');
            var postData = ({'invoice_id': invoice_id, 'status': 8, '_token': messages.token});
            th = this;
            jQuery.ajax({
                url: messages.update_invoice_approve,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);
                },
                success: function (data) {
                    $("#moveCase").html('Invoice successfully sent to  approve ');
                    $(th).parent('td').parent('tr').remove();
                }
            });
        } else
        {
            return false;
        }
    });
    //////////////////// onchange anchor  id get data /////////////////

    $("#supplier_id").append("<option value=''>Select Customer</option>");
    $(document).on('change', '.changeAnchor', function () {
        var anchor_id = $(this).val();
        $("#supplier_id").empty();
        var postData = ({'anchor_id': anchor_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.get_program_supplier,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                alert(errorThrown);

            },
            success: function (data) {

                if (data.status == 1)
                {
                    var obj1 = data.userList;

                    ///////////////////// for suppllier array///////////////  

                    if (obj1.length > 0)
                    {
                        $("#supplier_id").append("<option value=''> Select Supplier </option>");
                        $(obj1).each(function (i, v) {

                            $("#supplier_id").append("<option value='" + v.user_id + "'>" + v.f_name + "</option>");

                        });
                    } else
                    {
                        $("#supplier_id").append("<option value=''>No data found</option>");

                    }


                }

            }
        });
    });

    function uploadInvoice()
    {
       $('.isloader').show();
       $("#submitInvoiceMsg").empty();
        var file  = $("#customFile")[0].files[0];
        var datafile = new FormData();
        var anchor_bulk_id  = $("#anchor_bulk_id").val();
        var program_bulk_id  = $("#program_bulk_id").val();
        var supplier_bulk_id  = $("#supplier_bulk_id").val();
        var pro_limit_hide  =  $("#pro_limit_hide").val();
        datafile.append('_token', messages.token );
        datafile.append('doc_file', file);
        datafile.append('anchor_bulk_id', anchor_bulk_id);
        datafile.append('program_bulk_id', program_bulk_id);
        datafile.append('supplier_bulk_id', supplier_bulk_id);
        datafile.append('pro_limit_hide', pro_limit_hide);
        $.ajax({
            headers: {'X-CSRF-TOKEN':  messages.token  },
            url : messages.upload_invoice_csv,
            type: "POST",
            data: datafile,
            processData: false,
            contentType: false,
            cache: false, // To unable request pages to be cached
            enctype: 'multipart/form-data',

            success: function(r){
                $(".isloader").hide();

                if(r.status==1)
                {
                     $("#submitInvoiceMsg").show();
                     $("#submitInvoiceMsg").text('Invoice Successfully uploaded');
                }
                else
                {
                     $("#submitInvoiceMsg").show();
                     $("#submitInvoiceMsg").text('Total Amount if invoice should not greater Program Limit');
                 } 
            }
        });
    }
 //////////////////// for upload invoice//////////////////////////////   
function uploadFile(app_id,id)
{
   $(".isloader").show(); 
   var file  = $("#file"+id)[0].files[0];
   var extension = file.name.split('.').pop().toLowerCase();
   var datafile = new FormData();
   datafile.append('_token', messages.token );
   datafile.append('app_id', app_id);
   datafile.append('doc_file', file);
   datafile.append('invoice_id', id);
    $.ajax({
        headers: {'X-CSRF-TOKEN':  messages.token  },
        url : messages.invoice_document_save,
        type: "POST",
        data: datafile,
        processData: false,
        contentType: false,
        cache: false, // To unable request pages to be cached
        enctype: 'multipart/form-data',
         success: function(r){
            $(".isloader").hide();
            location.reload();
        }
    });
}

    /////////// for pop up//////////////////


    //////////////////// onchange anchor  id get data /////////////////
    $(document).on('change', '.changeBulkAnchor', function () {

        var anchor_id = $(this).val();
        if (anchor_id == '')
        {
            $("#pro_limit").empty();
            $("#pro_limit_hide").empty();
        }
        $("#program_bulk_id").empty();
        $("#anc_limit").empty();
        var postData = ({'anchor_id': anchor_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.front_program_list,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                alert(errorThrown);

            },
            success: function (data) {
                if (data.status == 1)
                {

                    var obj1 = data.get_program;
                    var obj2 = data.limit;

                    $("#anc_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_limit + '');


                    $("#program_bulk_id").append("<option value=''>Please Select</option>");
                    $(obj1).each(function (i, v) {

                        $("#program_bulk_id").append("<option value='" + v.program.prgm_id + "'>" + v.program.prgm_name + "</option>");
                    });



                } else
                {

                    $("#program_bulk_id").append("<option value=''>No data found</option>");


                }

            }
        });
    });

    //////////////////// onchange anchor  id get data /////////////////
    $(document).on('change', '.changeBulkSupplier', function () {

        var program_id = $(this).val();
        $("#supplier_bulk_id").empty();
        $("#pro_limit").empty();
        $("#pro_limit_hide").empty();
        var postData = ({'program_id': program_id, '_token': messages.token});
        jQuery.ajax({
            url: messages.front_supplier_list,
            method: 'post',
            dataType: 'json',
            data: postData,
            error: function (xhr, status, errorThrown) {
                alert(errorThrown);

            },
            success: function (data) {
                if (data.status == 1)
                {

                    var obj1 = data.get_supplier;
                    var obj2 = data.limit;

                    $("#pro_limit").html('Limit : <span class="fa fa-inr"></span>  ' + obj2.anchor_limit + '');
                    $("#pro_limit_hide").val(obj2.anchor_limit);
                    $("#supplier_bulk_id").append("<option value=''>Please Select</option>");
                    $(obj1).each(function (i, v) {

                        $("#supplier_bulk_id").append("<option value='" + v.app.user.user_id + "'>" + v.app.user.f_name + "</option>");
                    });

                } else
                {

                    $("#supplier_bulk_id").append("<option value=''>No data found</option>");


                }

            }
        });
    });

//////////////////////////// for bulk approve invoice////////////////////


    $(document).on('click', '#bulkApprove', function () {
        $("#moveCase").html('');
        var arr = [];
        i = 0;
        th = this;
        $(".chkstatus:checked").each(function () {
            arr[i++] = $(this).val();
        });
        if (arr.length == 0) {
            alert('Please select atleast one checked');
            return false;
        }
        if (confirm('Are you sure? You want to approve it.'))
        {
            var status = $(this).attr('data-status');
            var postData = ({'invoice_id': arr, 'status': status, '_token': messages.token});
            jQuery.ajax({
                url: messages.update_bulk_invoice,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown);

                },
                success: function (data) {
                    if (data == 1)
                    {
                        
                        location.reload();
                    }

                }
            });
        } else
        {
            return false;
        }
    });

///////////////////////////////////////// change invoice amount////////////////
    $(document).on('click', '.changeInvoiceAmount', function () {

        var limit = $(this).attr('data-limit');
        var approveAmount = $(this).attr('data-approve').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        var amount = $(this).attr('data-amount').toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        var invoiceId = $(this).attr('data-id');
        $("#invoice_id").val(invoiceId);
        $("#invoice_amount").val(amount);
        $("#invoice_approve_amount").val(approveAmount);

    });

///////////////////////////////////////// change invoice amount////////////////
    $(document).on('click', '#UpdateInvoiceAmount', function () {

        var amount = parseFloat($("#invoice_amount").val().replace(/,/g, ''));
        var approveAmount = parseFloat($("#invoice_approve_amount").val().replace(/,/g, ''));
        if (approveAmount > amount)
        {
            $(".model7msg").show();
            $(".model7msg").html('Invoice Approve Amount should not greater amount');
            return false;
        } else
        {
            $(".model7msg").hide();
            return true;
        }
    });
</script>
<script src="{{ asset('backend/js/ajax-js/user_wise_invoice_list.js') }}"></script>

@endsection
