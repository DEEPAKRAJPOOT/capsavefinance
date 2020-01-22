@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Disbursal List </h3>
            <small>Disbursal List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li class="active">Disbursal List</li>
            </ol>
        </div>
    </section>


<div class="card">
        <div class="card-body">       
            <div class="row">
                
                <div class="col-md-2">
                    {!!
                    Form::text('from_date',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'From Date',
                    'id'=>'from_date'
                    ])
                    !!} 
                </div>
                 <div class="col-md-2">
                    {!!
                    Form::text('to_date',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'To Date',
                    'id'=>'to_date'
                    ])
                    !!} 
                </div>
                
                
                
                <div class="col-md-2">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Inv. Ref. No.',
                    'id'=>'by_email'
                    ])
                    !!}
                </div>
                <div class="col-md-2">

                    {!!
                    Form::select('is_status',
                    $getAppStatus,
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                </div>
                <button id="searchB" type="button" class="btn  btn-success btn-sm float-right mr-4">Search</button>
                 <button id="reset" type="button" class="btn  btn-danger btn-sm float-right">Reset</button>

                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="disbursalList"  class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">
													<th>Disb. Date</th>
													<th>Inv. Ref. No.</th>
													<th>Due Date</th>
													<th>Inv. Amount</th>
													<th>disbursed Amount</th>
													<th>Disb. Status</th>
													<th>actual funded Amount</th>
													<th>Collection Date</th>
													<th>Collection Amount</th>
													<th>Accured Intrest</th>
													<th>Surplus Amount</th>
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
@endsection

@section('jscript')
<script>

    var messages = {
        lms_get_disbursal_list: "{{ URL::route('lms_get_disbursal_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="{{ asset('backend/js/lms/disbursal.js') }}" type="text/javascript"></script>
@endsection




