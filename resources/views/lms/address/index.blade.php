@extends('layouts.backend.admin-layout')

@section('content')

@include('layouts.backend.partials.admin_customer_links',['active'=>'address'])


<div class="content-wrapper">
	<div class="row grid-margin mt-3">
		<div class="  col-md-12  ">
			
			<div class="card">
                               <div class="card-body">
                    <div class="table-responsive ps ps--theme_default w-100">
                     @include('lms.customer.limit_details')
                    </div>
                </div>	
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="head-sec">
                                                            @can('add_addr')
								<a data-toggle="modal" data-target="#addAddressFrame" id="register" data-url ="{{route('add_addr',[ 'user_id' => $userInfo->user_id ])}}" data-height="450px" data-width="100%" data-placement="top" >
									<button class="btn  btn-success btn-sm float-right mb-3" type="button">

									<i class="fa fa-plus"></i> Add Address
									</button>
								</a>
                                                            @endcan
							</div>
						</div>
						<div class="col-sm-12">
							<table id="AddressList" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
								<thead>
									<tr role="row">
										<th width="90px">Customer Id </th>
										<th>Address</th>
										<th>City</th>
										<th>State</th>
										<th>Pincode</th>
										<th width="105px">FI Status</th>
										<th width="105px">Status</th>
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

{!!Helpers::makeIframePopup('addAddressFrame','Add Address', 'modal-md')!!}
{!!Helpers::makeIframePopup('editAddressFrame','Edit Address Detail', 'modal-md')!!}
@endsection


@section('additional_css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endsection
@section('jscript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<script>


var messages = {
	get_address_list: "{{ URL::route('get_ajax_address_list') }}",       
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}",
	user_id:"{{ $userInfo->user_id }}",
	set_default_address : "{{ URL::route('set_default_address') }}",
	};
</script>

<script src="{{ asset('backend/js/ajax-js/userAddress.js') }}"></script>


@endsection