@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin_customer_links',['active'=>'charges'])
<div class="content-wrapper">
    <div class="card">
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
                                    <td class="text-left" width="30%"><b>Product Limit</b></td>
                                    <td>{{ $userInfo->total_limit }} </td> 
                                    <td class="text-left" width="30%"><b>Utilize Product Limit</b></td>
                                    <td>{{  $userInfo->consume_limit }} </td> 
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%"><b>Remaining Product Limit</b></td>
                                    <td>{{ $userInfo->utilize_limit }} </td> 
                                    <td class="text-left" width="30%"><b>Sales Manager</b></td>
                                    <td>{{ (isset($userInfo->anchor->salesUser)) ? $userInfo->anchor->salesUser->f_name.' '.$userInfo->anchor->salesUser->m_name.' '.$userInfo->anchor->salesUser->l_name : '' }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
        <div class="card-body">
             <div class="row">
                   <div class="col-md-5">
                     
                        <input type="hidden" name="user_id" value="{{($user_id)}}">
                      
                        </div>
                        <div class="col-md-2">
                            <label class="float-left">From Date
                        </label> 
                        <input type="text" name="from_date" readonly="readonly" class="form-control form-control-sm date_of_birth datepicker-dis-fdate" value="">
                        </div>
                           <div class="col-md-2">
                            <label class="float-left">To Date
                        </label> 
                               <input type="text" name="to_date" readonly="readonly" class="form-control form-control-sm date_of_birth datepicker-dis-fdate" value="">
                        </div>     
                        <div class="col-md-1">
                        <label>&nbsp;</label><br>
                        <button type="button" class="btn btn-success btn-sm searchbtn" id="searchbtn">Search</button>
                        </div>
                        <div class="col-md-2">
                             <label>&nbsp;</label><br>
                        @can('list_lms_charges')
                            @if($paymentAppor)
                                <a class="btn btn-success btn-sm" href="javascript:void(0)" onclick="javascript:alert('You cannot perform this action as you have not uploaded  the unsettled payment apportionment CSV file.')"><i class="fa fa-plus"></i>Add Manual Charge</a>
                           @else
                                <a data-toggle="modal" class="btn  btn-success btn-sm" data-target="#addChargesLmsFrame" data-url ="{{route('list_lms_charges',['user_id' => request()->get('user_id')])}}" data-height="500px" data-width="100%" data-placement="top" >
                                <i class="fa fa-plus"></i>Add Manual Charge</a>
                           @endif
                        @endcan
                      </div>
            </div>
           
            <div class="row">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="chargesList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                            <thead>
                                <tr role="row">
                                 
                                    <th>Charge Name</th>
                                    <th>Charge Type</th>
                                    <th>Charge Calculation Amount</th>
                                    <th>GST Applicable</th>
                                     <th>Charge(%)</th>
                                    <th>Charge Applicable On</th>
                                    <th>Effective Date</th>
                                    <th>Applicability</th>
                                    <th>Description</th>
                                    <th>Date Time</th>
                                   
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
{!!Helpers::makeIframePopup('addChargesLmsFrame','Add Manual Charge', 'modal-lg')!!}
{!!Helpers::makeIframePopup('editChargesLmsFrame','Edit Charges Detail', 'modal-lg')!!}
@endsection

@section('jscript')
<script>

var messages = {
    get_lms_charges_list: "{{ URL::route('get_lms_charges_list') }}", 
    get_lms_charges_edit: "{{ URL::route('get_lms_charges_edit') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    };
    
    
    
</script>

<script src="{{ asset('backend/js/ajax-js/lms/charges_list.js') }}"></script>

@endsection


