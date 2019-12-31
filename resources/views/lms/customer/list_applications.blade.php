@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Sanction Cases Applications</h3>
            <small>Applications List</small>
            <ol class="breadcrumb">
                <li><a href="#"><i class="mdi mdi-home"></i> Home</a></li>
                <li >Manage Sanction Cases</li>
                <li class="active">Manage Applications</li>
            </ol>
        </div>
    </section>
    <div class="row ">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class=" form-fields w-100">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-title form-head-h5">Manage Applications</h5>
                                </div>
                           </div>
                        </div>

                        <div class="col-md-12">   
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
                                    </tbody>
                                </table>
                            </div> 
                            <div class="table-responsive ps ps--theme_default w-100">
                                <table class="table text-center  table-hover">
                                    <thead class="thead-primary">
                                        <tr>
                                            <td class="sub-title-bg" colspan="3">Applications</td>
                                        </tr>
                                        <tr>
                                            <th class="text-left">Application Id.</th>
                                            <th class="text-left">Biz Entity Name</th>
                                            <th class="text-left">Status</th>
                                            <th class="text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(count($application)>0)
                                        @foreach ($application AS $app)
                                        <tr>
                                            <td class="text-left">
                                            	<a href="">{{$app['app_id']}}</a>
                                            </td>
                                            <td class="text-left">{{$app['business']['biz_entity_name']}}</td>
                                            <td class="text-left">
                                                @if($app['status'] == 1)
                                                <button type="button" class="btn btn-success btn-sm">Complete</button>
                                                @else
                                                <button type="button" class="btn btn-info btn-sm">Not Complete</button>
                                                @endif 
                                            </td>
                                            <td class="text-left">
	                                            <div class="d-flex inline-action-btn">
											  	 	<a title="View Application Details" href="company-details.php" class="btn btn-action-btn btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
												   
											     	<a title="View Invoice" href="manage-invoice.php" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a>
			                                   </div>	           
                                        	</td>  
                                        </tr>	

                                        @endforeach
                                        @else
                                        <tr>
                                            <td  colspan = "3"> No Application Found:</td>
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