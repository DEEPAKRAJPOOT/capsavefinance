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
                                    <td><h6>{{ $userInfo->total_limit }}</h6> </td> 
                                   <td class="text-left" width="30%"><b>Sales Manager</b></td>
                                    <td>{{ (isset($userInfo->anchor->salesUser)) ? $userInfo->anchor->salesUser->f_name.' '.$userInfo->anchor->salesUser->m_name.' '.$userInfo->anchor->salesUser->l_name : '' }} </td>
                               
                                    
                                </tr>
                               
                            </tbody>
                        </table>
                    </div>
                </div>	
			
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-sm-12">

							<table class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
								<thead>
									<tr role="row">
                                                                                <th>Product Name </th>
										<th>Anchor Name </th>
										<th>Program Name</th>
										<th> Product Limit</th>
										<th>Utilize Product Limit </th>
										<th>Remaining Product Limit</th>
										
									</tr>
								</thead>
								<tbody>
								<tr role="row">
                                                                                <td>Supply chain</td>
										 <td>Maruti anchor</td>
										<td>John Test Program</td>
										 <td>100000</td>
										 <td>60000</td>
										 <td>40000</td>
										
									</tr>	
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
