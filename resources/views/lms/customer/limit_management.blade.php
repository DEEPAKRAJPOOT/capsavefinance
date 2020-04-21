@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'customer'])

<div class="content-wrapper">
	<div class="row grid-margin mt-3">
		<div class="  col-md-12  ">
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
			<div class="row">
				<div class="col-sm-12">
					<div class="head-sec">
						@can('add_bank_account')
						<a data-toggle="modal" 
						   title="Add Bank" 
						   data-height="450px" 
						   data-width="100%" 
						   data-target="#add_bank_account"
						   id="register" 
						   data-url="{{ route('add_bank_account', ['user_id' => request()->get('user_id')]) }}" >
							<button class="btn  btn-success btn-sm float-right mb-3" type="button">
								+ Add Bank
							</button>
						</a>
						@endcan
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">

							<table class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
								<thead>
									<tr role="row">
										<th>Acc. Holder Name </th>
										<th>Acc. Number</th>
										<th>Bank Name</th>
										<th>IFSC Code</th>
										<th>Branch Name </th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div></div>
	</div>
</div>
@endsection

@section('additional_css')

@section('jscript')
