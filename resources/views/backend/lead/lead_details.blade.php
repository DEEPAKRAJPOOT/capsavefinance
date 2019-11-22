@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa  fa-list"></i>
        </div>
        <div class="header-title">
            <h3>Manage Leads</h3>
            <small>Supplier List</small>
            <ol class="breadcrumb">
                <li><a href="#"><i class="mdi mdi-home"></i> Home</a></li>
                <li class="active">Manage Leads</li>
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
                                    <h5 class="card-title form-head-h5">Manage Leads Details</h5>
                                </div>
                                <div class="col-sm-6">
                                  <div class="head-sec">
                                     <div class="pull-right" style="margin-bottom: 10px;">
                                        <a href="{{route('create_application',['user_id' => request()->get('user_id')])}}">
                                        <button class="btn  btn-success btn-sm" type="button">+ Create Application</button>
                                        </a>
                                     </div>
                                  </div>
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
                                            <td class="text-left" width="30%"><b>Entity Name</b></td>
                                            <td>{{$userInfo->biz_name}}</td> 
                                        </tr>


                                        <tr>
                                            <td class="text-left" width="30%"><b>Email</b></td>
                                            <td>{{$userInfo->email}}	</td> 
                                            <td class="text-left" width="30%"><b>Mobile</b></td>
                                            <td>{{$userInfo->mobile_no}} </td> 
                                        </tr>


                                        <!--
                                                                                              <tr>
                                            <td class="text-left" width="30%"><b>PAN Number</b></td>
                                            <td>	27377XDTYASYS6</td> 
                                             <td class="text-left" width="30%"><b>Pin Code </b></td>
                                            <td>	203245</td> 
                                             <td class="text-left" width="30%"><b>GST Number</b></td>
                                            <td>	xyz Singh</td> 
                                        </tr>
                                        
                                        <tr>
                                            <td class="text-left" width="30%"><b>Industry </b></td>
                                            <td>	Industry</td> 
                                            <td class="text-left" width="30%"><b>Nature of Business </b></td>
                                            <td>	Nature of Business</td> 
                                            
                                        </tr>
                                        
                                        
                                         <tr>
                                            <td class="text-left" width="30%"><b>Business Constitution </b></td>
                                            <td>{{$userInfo->f_name}} {{$userInfo->m_name}} {{$userInfo->l_name}}	</td> 
                                            <td class="text-left" width="30%"><b>Business Turnover </b></td>
                                            <td>	400000.00</td> 
                                         </tr>                                             
                                        
                                         <tr>
                                            <td class="text-left" width="30%"><b>Address </b></td>
                                            <td>	xyx hdhhd jhdyh</td> 
                                            <td class="text-left" width="30%"><b>City </b> </td>
                                            <td>	Noida</td> 
                                        </tr>-->
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
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(count($application)>0)
                                        @foreach ($application AS $app)
                                        <tr>
                                            <td class="text-left">{{$app['app_id']}}</td>
                                            <td>
                                                @if($app['status'] == 1)
                                                <button type="button" class="btn btn-success btn-sm">Complete</button>
                                                @else
                                                <button type="button" class="btn btn-info btn-sm">Not Complete</button>
                                                @endif 
                                            </td>
                                            <td><div class="d-flex inline-action-btn justify-content-center"><a title="Add App Note" href="{{route('cam_overview',['user_id'=>$app['user_id'], 'app_id'=>$app['app_id']])}}" class="btn btn-action-btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
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

@section('jscript')
<script>

    var messages = {
        get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
</script>
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
@endsection