@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Refund </h3>
            <small>Manage Refund</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Manage Refund</li>
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

                    <a data-toggle="modal" data-target="#disbueseInvoices" data-url ="{{route('confirm_refund', ['refund_type' => 1]) }}" data-height="150px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2 disabled" id="openDisbueseInvoices" >Refund by Bank</a>
                    <a data-toggle="modal" data-target="#disbueseInvoices" data-url ="{{route('confirm_refund', ['refund_type' => 2]) }}" data-height="330px" data-width="100%" data-placement="top" class="btn btn-success btn-sm ml-2" id="openDisbueseInvoices" >Refund Manually</a>
                </div>
                <input type="hidden" value="" name="disbursal_ids" id="disbursal_ids">  

                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="refundCustomerList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">
                                                    <th></th>
                                                    <th>Customer Code</th>
													<th>Ben Name</th>
													<th>Ben Bank Name</th>
													<th>Ben IFSC</th>
													<th>Ben Account No.</th>
													<th>Refund Amt.</th>
                                                    <th>Status</th>
												</tr>
	                                        </thead>
	                                        <tbody>

	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="refundCustomerList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('disbueseInvoices','refund Invoices', 'modal-md')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        lms_get_refund_customer: "{{ URL::route('lms_get_refund_customer') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>

<script src="{{ asset('backend/js/lms/refund.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function(){
    $(document).on('change', '.disbursal_id', function() {

        let current_disbursal_ids = $('#disbursal_ids').val();
        let current_id = $(this).val();
        if($(this).is(':checked')){
            $('#disbursal_ids').val(current_disbursal_ids+','+current_id);
        }else{
            $('#disbursal_ids').val(current_disbursal_ids.replace(new RegExp(current_id, 'g'), ''));
        }
    });
    
});
</script>
@endsection




