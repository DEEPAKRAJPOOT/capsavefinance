@extends('layouts.backend.admin-layout')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Sanction Cases </h3>
            <small>Customer List</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">Manage Customers</li>
                <li class="active">My Customers</li>
            </ol>
        </div>
    </section>


    <div class="card">
        <div class="card-body">       
            <div class="row">
                <div class="col-md-5">
                    {!!
                    Form::text('search_keyword',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Entity Name, Email and UCIC Code',
                    'id'=>'search_keyword'
                    ])
                    !!}
                </div>
                <div class="col-md-4">
                    {!!
                    Form::text('customer_id',
                    null,
                    [
                    'class' => 'form-control',
                    'placeholder' => 'Search by Customer Id',
                    'id'=>'customer_id'
                    ])
                    !!} 
                </div>
                <button id="searchB" type="button" class="btn  btn-success btn-sm float-right">Search</button>
                
                <div class="col-12 dataTables_wrapper mt-4">
                    <div class="overflow">
                        <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive ps ps--theme_default" data-ps-id="0b57d57f-c517-e65f-5cf6-304e01f86376">
	                              		<table id="customerList" class="table table-striped cell-border dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
	                                        <thead>
	                                        	<tr role="row">                                                   
                                                    <th >Cust ID</th>       
                                                    <th >UCIC Code</th>
                                                    <th >App ID</th>       
		                                     		<th >Virtual ID</th>		
		                                     		<th >Customer Detail</th>
													<th >Product Limit</th>
													<th >Utilize Limit</th>
													<th >Available Limit</th>
                                                    <th >Anchor Detail</th>
													<th >Status</th>
												</tr>
	                                        </thead>
	                                        <tbody>

	                                        </tbody>
                                    	</table>
							  		</div>
                            		<div id="customerList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('editCustomer','Edit Customer Detail', 'modal-md')!!}

@endsection

@section('jscript')
<script>

    var messages = {
        get_customer: "{{ URL::route('lms_get_customer') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/lms/customer.js') }}" type="text/javascript"></script>
@endsection




