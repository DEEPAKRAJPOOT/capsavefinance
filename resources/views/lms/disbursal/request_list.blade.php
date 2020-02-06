@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Disbursal Request </h3>
            <small>Disbursal Request</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Disbursal Request</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">       
            <div class="row">
                <div class="col-md-4">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Customer Code',
                    'id'=>'search_keyword'
                    ])
                    !!}
                </div>
                <button id="searchbtn" type="button" class="btn  btn-success btn-sm float-right">Search</button>
                
                <div class="col-md-3 ml-auto text-right">

                    <a data-toggle="modal" data-target="#disbueseInvoices" data-url ="{{route('confirm_disburse', ['disburse_type' => 1]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" id="openDisbueseInvoices" >Send To Bank</a>
                    <a data-toggle="modal" data-target="#disbueseInvoices" data-url ="{{route('confirm_disburse', ['disburse_type' => 2]) }}" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" id="openDisbueseInvoices" >Disburse Manually</a>
                </div>
                <input type="hidden" value="" name="invoice_ids" id="invoice_ids">  
                <input type="hidden" value="" name="user_ids" id="user_ids">  

                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="disbursalCustomerList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">
                                                    <th></th>
                                                    <th>Customer Code</th>
													<th>Ben Name</th>
													<th>Ben Bank Name</th>
													<th>Ben IFSC</th>
													<th>Ben Account No.</th>
													<th>Total Invoice Amt.</th>
													<th>Total Disburse Amt.</th>
													<th>Total Actual Funded Amt.</th>
													<th>Total Invoice </th>
                                                    <th>Status</th>
													<th>Action</th>
												</tr>
	                                        </thead>
	                                        <tbody>

	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="disbursalCustomerList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('viewDisbursalCustomerInvoice','View Disbursal Customer Invoice', 'modal-lg')!!}
{!!Helpers::makeIframePopup('disbueseInvoices','Disbuse Invoices', 'modal-md')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        lms_get_disbursal_customer: "{{ URL::route('lms_get_disbursal_customer') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>

<script src="{{ asset('backend/js/lms/disbursal.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function(){
    $(document).on('change', '.user_id', function() {

        let current_user_ids = $('#user_ids').val();
        let current_id = $(this).val();
        if($(this).is(':checked')){
            $('#user_ids').val(current_user_ids+','+current_id);
        }else{
            $('#user_ids').val(current_user_ids.replace(new RegExp(current_id, 'g'), ''));
        }
    });
    
});
</script>
@endsection




