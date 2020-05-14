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

								<a href="{{route('generate_write_off',[ 'user_id' => $userInfo->user_id ])}}" >
									<button class="btn  btn-success btn-sm float-right mb-3" type="button">

									<i class="fa fa-plus"></i> Generate Wtite Off
									</button>
								</a>

							</div>
						</div>
						<div class="col-sm-12">
							<table id="WriteOffList" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
								<thead>
									<tr role="row">
										<th width="90px">Customer Id </th>
										<th>Full Name</th>
										<th>Amount</th>
										<th width="105px">RCU Status</th>
										<th width="105px">Status</th>
										<th>Action</th>
									</tr>

								</thead>
								<tbody>
								</tbody>
							</table>
							<div id="write-off-listing_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
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
	data_not_found: "{{ trans('error_messages.data_not_found') }}",
	token: "{{ csrf_token() }}",
	user_id:"{{ $userInfo->user_id }}",
	};
</script>
@endsection