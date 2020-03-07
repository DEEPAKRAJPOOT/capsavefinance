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
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_invoice') active @endif"  href="{{Route('backend_get_invoice')}}">Pending</a>
                            </li>
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_approve_invoice') active @endif"  href="{{Route('backend_get_approve_invoice')}}">Approved</a>
                            </li>
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice')}}">Disbursement Queue</a>
                            </li>

                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_sent_to_bank') active @endif" href="{{Route('backend_get_sent_to_bank')}}">Sent to Bank</a>
                            </li>
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_failed_disbursment') active @endif" href="{{Route('backend_get_failed_disbursment')}}">Failed Disbursement</a>
                            </li>
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_disbursed') active @endif" href="{{Route('backend_get_disbursed')}}">Disbursed</a>

                            </li>
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice')}}">Repaid</a>
                            </li>
                            <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_reject_invoice') active @endif" href="{{Route('backend_get_reject_invoice')}}">Reject</a>

                            </li>

 <li class="nav-item itemBackground">
                                <a class="itemBackgroundColor nav-link @if(Route::currentRouteName()=='backend_get_exception_cases') active @endif" href="{{Route('backend_get_exception_cases')}}">Exception Cases</a>

                            </li>
                        </ul>

                        <div class="tab-content">

                            <div id="menu1" class=" active tab-pane "><br>


                                <div class="card">
                                    <div class="card-body">
                                        <div class="row"><div class="col-md-6"></div>
                                            <div class="col-md-2">				 
                                                <input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
                                                <select class="form-control form-control-sm changeBiz searchbtn"  name="search_biz" id="search_biz">
                                                    <option value="">Select Application  </option>
                                                    @foreach($get_bus as $row)
                                                    <option value="{{{$row->business->biz_id}}}">{{{$row->business->biz_entity_name}}} </option>
                                                    @endforeach


                                                </select>
                                                <span id="anchorMsg" class="error"></span>

                                            </div>
                                            <div class="col-md-2">				 

                                                <select class="form-control form-control-sm changeAnchor searchbtn"  name="search_anchor">
                                                    <option value="">Select Anchor  </option>
                           @foreach($anchor_list as $row)
                            @php if(isset($row->anchorOne->anchor_id)) { @endphp
                           <option value="{{{$row->anchorOne->anchor_id}}}">{{{$row->anchorOne->comp_name}}}  </option>
                          @php } @endphp
                           @endforeach

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
                                                                <table id="invoiceListDisbursed" class="text-capitalize table white-space table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                                                    <thead>
                                                                        <tr role="row">
                                                                            <th>Invoice No</th> 
                                                                            <th>Anchor Name</th>
                                                                            <th>Customer Name</th>
                                                                            <th>Invoice Date</th>
                                                                            <th>Invoice Due Date</th>
                                                                            <th>Tenor</th>
                                                                            <th>Invoice  Amount</th>
                                                                            <th>Invoice Approve Amount</th>
                                                                            <th>Status</th>
                                                                            <th>Action</th>
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




    <div class="modal show" id="myModal5" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5>Repayment Details | Invoice Number : INV-112</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body text-left">
                    <div class="listing-modal repayment-form-modal mb-3">
                        <ul>
                            <li>
                                <div class="listing-modal-left"> Repay count: </div>
                                <div class="listing-modal-right"> <span id="repay_count">1</span></div>
                                <span id="overdue_percentage_raw" hidden="">16</span>
                                <span id="remaining_overdue_raw" hidden="">110000.1328125</span>
                            </li>
                            <li>
                                <!-- <span style="display:none" id="repay_count"></span> -->
                                <div class="listing-modal-left"> Invoice Approved Amount (₹): </div>
                                <div class="listing-modal-right"> <span id="invoice_approved_amount">₹60,000.00</span></div>
                            </li>

                            <li>
                                <div class="listing-modal-left"> Funded Amount (₹): </div>
                                <div class="listing-modal-right"> <span id="funded_amount">₹56,000.00</span></div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Final Funded Amount (₹): </div>
                                <div class="listing-modal-right"> <span id="final_funded_amount_repay">₹48146.00</span></div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Funded Date:  </div>
                                <div class="listing-modal-right"> <span id="funded_date_show">13-Dec-2019</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Tenor (in days): </div>
                                <div class="listing-modal-right"> <span id="term_days">90</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Payment Due Date: </div>
                                <div class="listing-modal-right"> <span id="payment_due_date" payment_due_date_raw="2019-10-17">14-March-2020</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Interest Per Annum (%): </div>
                                <div class="listing-modal-right"> <span id="interest_percentage">12</span><span> %</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Processing Fee (%): </div>
                                <div class="listing-modal-right"> <span id="processing_fee_repay">1</span><span> %</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Discount Type: </div>
                                <div class="listing-modal-right"> <span id="discount_type">front end</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Grace period (in days): </div>
                                <div class="listing-modal-right"> <span id="penal_grace">0</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Penal Interest Per Annum (%): </div>
                                <div class="listing-modal-right"> <span id="penal_interest">0</span><span> %</span>
                                </div>
                            </li>

                            <li>
                                <div class="listing-modal-left">Repayment Amount: </div>
                                <div class="listing-modal-right"> <span id="repayment_amount">₹0</span>

                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Total Amount Repaid: </div>
                                <div class="listing-modal-right"> <span id="already_repaid_amount">₹0</span></div>

                            </li>  
                            <li>
                                <div class="listing-modal-left">Penal days: </div>
                                <div class="listing-modal-right"> <span id="penal_days">41</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Penalty Amount: </div>
                                <div class="listing-modal-right"> <span id="penalty_amount">₹0</span>
                                </div>
                            </li>



                            <li>
                                <div class="listing-modal-left">Principal Amount: </div>
                                <div class="listing-modal-right"> <span id="remaining_overdue">₹60,000</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Total Amount to Repay: </div>
                                <div class="listing-modal-right"> <span id="remaining_repay_amount">₹0</span></div>
                            </li>



                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Repayment Date :</label>
                                <input type="date" class="form-control " value="">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Repayment Amount :</label>
                                <input type="date" class="form-control " value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Payment Type :</label>
                                <select class="form-control">
                                    <option value=""> Select Payment Type </option>
                                    <option value="1"> Online RTGS/NEFT </option>
                                    <option value="2"> Cheque</option>
                                    <option value="3"> Other </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Upload Documents :</label>
                                <input type="file" class="form-control " value="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="repaid_amount" class="form-control-label">Comment : </label>
                            <textarea class="form-control" cols="4" rows="4"></textarea>

                        </div>
                    </div>
                    <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Save</button> 
                    <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		

                </div>
            </div>
        </div>
    </div>
    {!!Helpers::makeIframePopup('modalInvoiceDisbursed','Repayment Details', 'modal-lg')!!}
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
            backend_get_invoice_list_disbursed: "{{ URL::route('backend_get_invoice_list_disbursed') }}",
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
            $("#program_bulk_id").append("<option value=''>No data found</option>");
            $("#program_bulk_id").append("<option value=''>No data found</option>");
            /////// jquery validate on submit button/////////////////////
            $('#submit').on('click', function (e) {

                if ($('form#signupForm').validate().form()) {
                    $("#anchor_bulk_id").rules("add", {
                        required: true,
                        messages: {
                            required: "Please enter Anchor name",
                        }
                    });

                    $("#supplier_id").rules("add", {
                        required: true,
                        messages: {
                            required: "Please Select Supplier Name",
                        }
                    });
                    $("#program_bulk_id").rules("add", {
                        required: true,
                        messages: {
                            required: "Please Select Product Program Name",
                        }
                    });

                    $("#customFile").rules("add", {
                        required: true,
                        messages: {
                            required: "Please upload Invoice Copy",
                        }
                    });


                } else {
                    alert();
                }
            });
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
        $(document).on('change', '.approveInv', function () {
            var status = $(this).val();
            if (status == 0)
            {
                return false;
            }
            if (confirm('Are you sujre? You want to approve it'))
            {
                th = this;
                var invoice_id = $(this).attr('data-id');
                var postData = ({'invoice_id': invoice_id, 'status': status, '_token': messages.token});
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
                        $(th).closest('tr').remove();
                    }
                });
            } else
            {
                return false;
            }
        });
        //////////////////// onchange anchor  id get data /////////////////

        $("#supplier_id").append("<option value=''>Select customer</option>");
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

                                $("#supplier_id").append("<option value='" + v.app.user.user_id + "'>" + v.app.user.f_name + "</option>");

                            });
                        } else
                        {
                            $("#supplier_id").append("<option value=''>No data found</option>");

                        }


                    }

                }
            });
        });


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


        function uploadInvoice()
        {
            $('.isloader').show();
            $("#submitInvoiceMsg").empty();
            var file = $("#customFile")[0].files[0];
            var datafile = new FormData();
            var anchor_bulk_id = $("#anchor_bulk_id").val();
            var program_bulk_id = $("#program_bulk_id").val();
            var supplier_bulk_id = $("#supplier_bulk_id").val();
            var pro_limit_hide = $("#pro_limit_hide").val();
            datafile.append('_token', messages.token);
            datafile.append('doc_file', file);
            datafile.append('anchor_bulk_id', anchor_bulk_id);
            datafile.append('program_bulk_id', program_bulk_id);
            datafile.append('supplier_bulk_id', supplier_bulk_id);
            datafile.append('pro_limit_hide', pro_limit_hide);
            $.ajax({
                headers: {'X-CSRF-TOKEN': messages.token},
                url: messages.upload_invoice_csv,
                type: "POST",
                data: datafile,
                processData: false,
                contentType: false,
                cache: false, // To unable request pages to be cached
                enctype: 'multipart/form-data',

                success: function (r) {
                    $(".isloader").hide();

                    if (r.status == 1)
                    {
                        $("#submitInvoiceMsg").show();
                        $("#submitInvoiceMsg").text('Invoice Successfully uploaded');
                    } else
                    {
                        $("#submitInvoiceMsg").show();
                        $("#submitInvoiceMsg").text('Total Amount if invoice should not greater Program Limit');
                    }
                }
            });
        }
        //////////////////// for upload invoice//////////////////////////////   
        function uploadFile(app_id, id)
        {
            $(".isloader").show();
            var file = $("#file" + id)[0].files[0];
            var extension = file.name.split('.').pop().toLowerCase();
            var datafile = new FormData();
            datafile.append('_token', messages.token);
            datafile.append('app_id', app_id);
            datafile.append('doc_file', file);
            datafile.append('invoice_id', id);
            $.ajax({
                headers: {'X-CSRF-TOKEN': messages.token},
                url: messages.invoice_document_save,
                type: "POST",
                data: datafile,
                processData: false,
                contentType: false,
                cache: false, // To unable request pages to be cached
                enctype: 'multipart/form-data',
                success: function (r) {
                    $(".isloader").hide();
                    location.reload();
                }
            });
        }

    //////////////////////////// for bulk approve invoice////////////////////


        $(document).on('click', '#bulkApprove', function () {
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
            if (confirm('Are you sujre? You want to approve it'))
            {
                var postData = ({'invoice_id': arr, 'status': 9, '_token': messages.token});
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
            var approveAmount = $(this).attr('data-approve');
            var amount = $(this).attr('data-amount');
            var invoiceId = $(this).attr('data-id');
            $("#invoice_id").val(invoiceId);
            $("#invoice_amount").val(amount);
            $("#invoice_approve_amount").val(approveAmount);

        });

    ///////////////////////////////////////// change invoice amount////////////////
        $(document).on('click', '#UpdateInvoiceAmount', function () {

            var amount = parseFloat($("#invoice_amount").val());
            var approveAmount = parseFloat($("#invoice_approve_amount").val());
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
    <script src="{{ asset('backend/js/ajax-js/invoice_list_disbursment.js') }}"></script>

    @endsection
