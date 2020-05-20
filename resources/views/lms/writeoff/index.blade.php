@extends('layouts.backend.admin-layout')

@section('content')
    @include('layouts.backend.partials.admin_customer_links',['active'=>'writeOff'])
    <div class="content-wrapper">
        <div class="row grid-margin mt-3">
            <div class="col-md-12">
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
                                    @if (count($woData) < 1)
                                        <a href="{{route('generate_write_off',[ 'user_id' => $userInfo->user_id ])}}" >
                                            <button class="btn  btn-success btn-sm float-right mb-3" type="button">
                                            <i class="fa fa-plus"></i> Generate Wtite Off
                                            </button>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table id="WriteOffList" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                    <thead>
                                        <tr role="row">
                                            <th width="90px">Customer Id </th>
                                            <th>Customer Name</th>
                                            <th>Amount</th>
                                            <th width="105px">Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($woData) > 0)
                                            @foreach($woData as $wOff)
                                                <tr>
                                                    <td>{{$wOff->lmsUser->customer_id}}</td>
                                                    <td>{{$userInfo->f_name}} {{$userInfo->m_name}}	{{$userInfo->l_name}}</td>
                                                    <td>{{ number_format($wOff->amount,2) }}</td>
                                                    <td>{{$wOff->status_name}}</td>
                                                    <td>

                                                        @if(in_array($wOff->status_id, [config('lms.WRITE_OFF_STATUS.IN_PROCESS')]) && in_array($role_id, [8]))
                                                            <a data-toggle="modal"  data-height="250px" 
                                                            data-width="100%" data-target="#apprDisapprFrame"
                                                            data-url="{{route('wo_approve_dissapprove', ['user_id' => $wOff->user_id, 'wo_req_id' => $wOff->wo_req_id, 'action_type' => '2', 'status_id'=>$wOff->status_id])}}"  
                                                            data-placement="top" class="btn btn-action-btn btn-sm" title="Moved to Back Stage"><i class="fa fa-reply" aria-hidden="true"></i></a>
                                                        @endif

                                                        @php
                                                            $nxtBtnFlag = false;
                                                            if($role_id == 6 && in_array($wOff->status_id, [config('lms.WRITE_OFF_STATUS.NEW'),config('lms.WRITE_OFF_STATUS.REVERT_BACK')]))
                                                                $nxtBtnFlag = true;
                                                            elseif($role_id == 8 && !in_array($wOff->status_id, [config('lms.WRITE_OFF_STATUS.NEW'),config('lms.WRITE_OFF_STATUS.REVERT_BACK')]))
                                                                $nxtBtnFlag = true;
                                                            elseif($role_id == 1)
                                                                $nxtBtnFlag = true;
                                                        @endphp


                                                        @if(!in_array($wOff->status_id, [config('lms.WRITE_OFF_STATUS.COMPLETED'),config('lms.WRITE_OFF_STATUS.APPROVED')]) && in_array($role_id, [6,8,1]) && $nxtBtnFlag)
                                                            <a data-toggle="modal"  data-height="250px" 
                                                            data-width="100%" data-target="#apprDisapprFrame"
                                                            data-url="{{route('wo_approve_dissapprove', ['user_id' => $wOff->user_id, 'wo_req_id' => $wOff->wo_req_id, 'action_type' => '1', 'status_id'=>$wOff->status_id])}}"  
                                                            data-placement="top" class="btn btn-action-btn btn-sm" title="Moved to Next Stage"><i class="fa fa-share" aria-hidden="true"></i></a>
                                                        
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6">No data found</td>
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
    {!!Helpers::makeIframePopup('apprDisapprFrame','Send next stage', 'modal-md')!!}
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