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
                                    <td>{{ number_format($userlimit->tot_limit_amt) }} </td> 
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
                                                 @foreach($offerlimit as $limit)   
                                                       @php $inv=0 @endphp      @foreach($limit->invoice as $invoice)    @php $inv+=$invoice->invoice_approve_amount; @endphp        @endforeach
							<table class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
							   
                                                            <thead>
									<tr role="row">
                                                                                 <th>Product Name </th>
                                                                                <th>Anchor Name </th>
										<th>Program Name</th>
										<th>Program Offer Limit </th>
										
										
									</tr>
								</thead>
								<tbody>
                                                                @php $sum=0 @endphp    
                                                                @foreach($limit->offer as $val) @php $sum+=$val->prgm_limit_amt; @endphp        
								<tr role="row">
                                                                    <td> {{$limit->product->product_name}}</td>
										 <td>{{ $val->anchor->comp_name}}</td>
										 <td>{{ $val->program->prgm_name}}</td>
										<td>{{ number_format($val->prgm_limit_amt)}}</td>
										
										
									</tr>
                                                                   @endforeach      
								</tbody>
							</table>
                                                    
                                                            
                                                 
                                                 @endforeach
						</div>
                                               <div class="col-sm-12">
                                                           <div class="col-sm-8"></div>
                                                           <div class="col-sm-4 pull-right" style="margin-right: 193px;">
                                                                <table cellspacing="5"  border="0"  width="100%" class= no-footer overview-table> 
                                                        <tr>
                                                             <th>Total Approve </th> <th>:</th> <td>{{number_format($sum)}}</td> </tr><tr>
                                                             <th>Product Limit</th><th>:</th>
                                                             <td>{{ number_format($limit->limit_amt)}}</td></tr> <tr>
                                                              <th> Utilize Product Limit</th><th>:</th>
                                                             <td>{{number_format($inv)}}</td></tr> <tr>
                                                              <th> Remaining Limit</th><th>:</th>
                                                             <td>{{number_format($sum-$inv)}}</td>
                                                        </tr>
                                                     
                                                    </table>
                                                               
                                                               
                                                               
                                                           </div>
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
