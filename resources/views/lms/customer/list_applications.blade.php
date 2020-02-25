@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'summary'])
<div class="content-wrapper">
	<div class="row ">
            
           
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive ps ps--theme_default w-100">
						<table class="table  table-td-right">
							<tbody>
								<tr>
									<td class="text-left" width="30%"><b>Full Name</b></td>
									<td>{{$userInfo->f_name}} {{$userInfo->m_name}}	{{$userInfo->l_name}}</td> 
									<td class="text-left" width="30%"><b>Email</b></td>
									<td>{{$userInfo->email}}	</td> 
								</tr>
								<tr>
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
				<div class="card-body">
					<table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
					   	<thead>
						   	<tr>
								<td class="sub-title-bg" colspan="5">Associate Anchor</td>
							</tr>
					  		<tr role="row">
								<th>Anchor Id</th>
								<th>Anchor Name </th>
								<th>Program </th>
								<th>Program Limit</th>
								<th>Avialable Limit</th>
						  	</tr>
					   </thead>
					   
					   <tbody>
					   		@if($anchors->count() >0)
							@foreach ($anchors AS $anchor)
							@if(!empty($anchor->anchor))
							<tr role="row" class="odd">
								<td class="sorting_1">{{ $anchor->anchor->anchor_id }}</td>
								<td>{{ $anchor->anchor->comp_name }}</td>
								<td>{{ $anchor->program->prgm_name }}</td>
								<td><i class="fa fa-inr"></i> {{ $anchor->limit_amt }}</td>
								<td><i class="fa fa-inr"></i> {{ (isset($anchor->offer->prgm_limit_amt)) ?? $anchor->offer->prgm_limit_amt - (isset($anchor->offer->loan_amount)) ?? $anchor->offer->loan_amount }}</td>
							</tr>
							@endif
						   	@endforeach
							@else
							<tr>
								<td  colspan = "5"> No Anchor Found:</td>
							</tr>
							@endif
					   </tbody>
					</table>
		 		</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">   
							
							<div class="table-responsive ps ps--theme_default w-100">
								<table class="table text-center overview-table table-hover">
									<thead class="thead-primary">
										<tr>
											<td class="sub-title-bg" colspan="4">Applications</td>
										</tr>
										<tr>
											<th class="text-left">Application Id.</th>
											<th class="text-left">Biz Entity Name</th>
											<th class="text-left">Status</th>
											<th class="text-left">Action</th>
										</tr>
									</thead>
									<tbody>

										@if($application->count() >0)
										@foreach ($application AS $app)
										<tr>
											<td class="text-left">{{$app->app_id}}</td>
											<td class="text-left">{{$app->business->biz_entity_name}}</td>
											<td class="text-left">
												@if($app->status == 1)
												<button type="button" class="btn btn-success btn-sm">Complete</button>
												@else
												<button type="button" class="btn btn-info btn-sm">Not Complete</button>
												@endif 
											</td>
											<td class="text-left">
												<div class="d-flex inline-action-btn">
													<a title="View Application Details" href="{{ route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id ]) }}" class="btn btn-action-btn btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
												   
											   </div>	           
											</td>  
										</tr>	

										@endforeach
										@else
										<tr>
											<td  colspan = "4"> No Application Found:</td>
										</tr>
										@endif
									</tbody>
								</table>
							</div>  
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection