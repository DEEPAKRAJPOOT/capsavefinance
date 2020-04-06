@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@if($flag == 1)
@include('layouts.backend.partials.admin_customer_links',['active' => 'invoice'])
@endif
<div class="content-wrapper">
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

                    @include('layouts.backend.invoice_status_links')

                    <div class="tab-content">

                        <div id="menu1" class=" active tab-pane "><br>


                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" name="route" value="{{Route::currentRouteName()}}">                                
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control form-control-sm"  name="search_biz"  placeholder="Search by business name, Invoice number ">
                                        </div> 
                                        <div class="col-md-1">
                                            <button  type="button" id="search_biz" class="btn  btn-success btn-sm float-right">Search</button>
                                        </div>  
                                        <!-- <div class="col-md-3">				 
                                            
                                           <select class="form-control form-control-sm changeBiz searchbtn"  name="search_biz" id="search_biz">
                                                 <option value="">Select Business Name  </option>
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
                                         </div>  -->   

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
                                                                        <th> Inv. No. </th>
                                                                        <th> Batch ID </th> 
                                                                        <th> Anchor Detail </th>
                                                                        <th> Customer Detail </th>
                                                                        <th> Inv Detail </th>
                                                                        <th> Inv Amount </th>
                                                                        <th> Updated By</th>
                                                                        <th> Action </th>
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

    {!!Helpers::makeIframePopup('modalInvoiceDisbursed','Repayment Details', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('viewDisbursalCustomerInvoice','View Disbursal Customer Invoice', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('viewInterestAccrual','Interest Accrual', 'modal-lg')!!}
    @endsection
    @section('jscript')
    <style>
        .itemBackground 
        { 
            border: 2px solid blanchedalmond;  
            background-color:#138864;
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
            get_biz_anchor: "{{ URL::route('get_biz_anchor') }}",
            front_program_list: "{{ URL::route('front_program_list') }}",
            front_supplier_list: "{{ URL::route('front_supplier_list') }}",
            update_invoice_approve: "{{ URL::route('update_invoice_approve') }}",
            lms_get_disbursal_list: "{{ URL::route('lms_get_disbursal_list') }}",
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


        //////////////////// onchange Business  id get Anchor /////////////////

        $("#changeAnchor").append("<option value=''>Select Anchor</option>");
        $(document).on('change', '.changeBiz', function () {
            var biz_id = $(this).val();
            $("#changeAnchor").empty();
            var postData = ({'status_id': 12, 'biz_id': biz_id, '_token': messages.token});
            jQuery.ajax({
                url: messages.get_biz_anchor,
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
                            $("#changeAnchor").append("<option value=''> Select Anchor </option>");
                            $(obj1).each(function (i, v) {

                                $("#changeAnchor").append("<option value='" + v.anchor.anchor_id + "'>" + v.anchor.comp_name + "</option>");

                            });
                        } else
                        {
                            $("#changeAnchor").append("<option value=''>No data found</option>");

                        }


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
