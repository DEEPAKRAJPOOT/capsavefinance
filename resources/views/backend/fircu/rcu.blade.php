@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')

@section('additional_css')
<style type="text/css">
    a:hover {
        text-decoration: none !important;
    }
    
</style>
@endsection
    <!-- partial -->
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        @can('backend_fi')
        <li>
            <a href="{{ route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => request()->get('user_id')]) }}">FI Residence</a>
        </li>
        @endcan
        @can('backend_rcu')
        <li>
            <a href="{{ route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => request()->get('user_id')]) }}" class="active">RCU Document</a>
        </li>
        @endcan
        @can('backend_inspection')
        <li>
            {{-- <a href="{{ route('backend_inspection', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Inspection</a> --}}
        </li>
        @endcan
        @can('pd_notes_list')
        <li>
            <a href="{{ route('pd_notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => request()->get('user_id')]) }}"> Personal Discussion </a>
        </li>
        @endcan
    </ul>


<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive" id="rcu_list" style="overflow: visible !important;">
                            <table id="rcuList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>RCU ID</th>
                                        <th>Document Type</th>
                                        <th>Agency Name</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $i= 1;
                                    @endphp
                                    @foreach ($data as $key => $value) 
                                    
                                        <tr role="row" class="odd">
                                            <td class="sorting_1"><input type="checkbox" class="document_id" value="{{ $value->rcuDoc->id }}">{{ $value->rcuDoc->id }}.</td>
                                            <td>{{ $value->rcuDoc->doc_name }}</td> 
                                            <td>{{ (isset($value->current_agency->comp_name)) ? $value->current_agency->comp_name : '' }}</td>                                      
                                            <td>
                                                <div class="btn-group"><label class="badge badge-warning">{{(isset($value->cm_status)) ? ($value->cm_status): 'Pending' }}</label></div>
                                            </td>
                                            <td>
                                                @if($value['agencies']->count())
                                                <div class="btn-group ml-2 mb-1">
                                                    @if(Auth::user()->agency_id == null)
                                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                    </button>
                                                    @can('change_cm_rcu_status')
                                                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;"  data-rcu_doc_id="{{ $value->current_rcu->rcu_doc_id}}">
                                                        <a class="dropdown-item change-cm-status" href="javascript:void(0);" value="1">Pending</a>
                                                        <a class="dropdown-item change-cm-status" href="javascript:void(0);" value="2">Inprogress</a>
                                                        <a class="dropdown-item change-cm-status" href="javascript:void(0);" value="3">Positive</a>
                                                        <a class="dropdown-item change-cm-status" href="javascript:void(0);" value="4">Negative</a>
                                                        <a class="dropdown-item change-cm-status" href="javascript:void(0);" value="5">Cancelled</a>
                                                    </div>
                                                    @endcan
                                                    @endif
                                                </div>
                                                @endif
                                            </td> 
                                            <td align="right"><span class="trigger minus"></span></td> 
                                        </tr>
                                        
                                        <tr class="dpr" style="display: table-row;">
                                            <td colspan="7" class="p-0">
                                               <table class="overview-table remove-tr-bg" cellpadding="0" cellspacing="0" border="0" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td width="25%"><b>File Name</b></td>
                                                        <td width="25%"><b>Upload On </b></td>
                                                        <td width="25%"><b>Action</b></td>
                                                        <td width="25%"><b>View Response</b></td>
                                                     </tr>
                                                    @foreach ($value->documents as $key1 => $document) 
                                                     <tr>
                                                      <td width="25%">{{ $document->userFile->file_name }}</td>
                                                      <td width="25%">{{\Carbon\Carbon::parse($document->created_at)->format('d/m/Y h:i A')}}</td>
                                                    <td width="25%">
                                                        @can('download_storage_file')
                                                        <a class="btn-sm" title="Download Document" href="{{ route('download_storage_file', ['file_id' => $document->userFile->file_id ]) }}" >
                                                            <button class="btn-upload btn-sm" type="button"> <i class="fa fa-download"></i>
                                                            </button>
                                                        </a>
                                                        @endcan
                                                    </td>
                                                    <td width="25%">
                                                        @if($document->doc_id == 31)
                                                        @can('show_dl_data')
                                                        <a class="btn-sm" data-toggle="modal"  data-target="#modalPromoter" data-height="400" data-width="100%" data-url="{{route('show_dl_data',['type'=>'5','ownerid' => $document->biz_owner_id ])}}" style="display: inline">  
                                                            <button class="btn-upload btn-sm" type="button" title="View Details (Pan Card)" data-id="{{isset($document->first_name) ? $i : '1'}}" data-type="5" > <i class="fa fa-eye"></i>
                                                            </button>
                                                        </a>
                                                        @endcan
                                                        @endif

                                                        @if($document->doc_id == 30)
                                                        @can('show_voter_data')
                                                        <a class="btn-sm" data-toggle="modal"  data-target="#modalPromoter" data-height="400" data-width="100%" data-url="{{route('show_voter_data',['type'=>'4','ownerid' => $document->biz_owner_id ])}}" style="display: inline">  
                                                            <button class="btn-upload btn-sm" type="button" title="View Details (Pan Card)" data-id="{{isset($document->first_name) ? $i : '1'}}" data-type="5" > <i class="fa fa-eye"></i>
                                                            </button>
                                                        </a>
                                                        @endcan
                                                        @endif

                                                        @if($document->doc_id == 32)
                                                        @can('show_pass_data')
                                                        <a class="btn-sm" data-toggle="modal"  data-target="#modalPromoter" data-height="400" data-width="100%" data-url="{{route('show_pass_data',['type'=>'6','ownerid' => $document->biz_owner_id ])}}" style="display: inline">  
                                                            <button class="btn-upload btn-sm" type="button" title="View Details (Pan Card)" data-id="{{isset($document->first_name) ? $i : '1'}}" data-type="5" > <i class="fa fa-eye"></i>
                                                            </button>
                                                        </a>
                                                        @endcan
                                                        @endif

                                                        @if($document->doc_id == 2)
                                                        @can('show_pan_data')
                                                        <a class="btn-sm" data-toggle="modal"  data-target="#modalPromoter" data-height="400" data-width="100%" data-url="{{route('show_pan_data',['type'=>'3','ownerid' => $document->biz_owner_id ])}}" style="display: inline">  
                                                            <button class="btn-upload btn-sm" type="button" title="View Details (Pan Card)" data-id="{{isset($document->first_name) ? $i : '1'}}" data-type="5" > <i class="fa fa-eye"></i>
                                                            </button>
                                                        </a>
                                                        @endcan
                                                        @endif
                                                    </td>
                                                     </tr>
                                                     @endforeach
                                                  </tbody>
                                               </table>
                                               <table class="overview-table remove-tr-bg" cellpadding="0" cellspacing="0" border="0" width="100%">
                                                  <tbody>
                                                     <tr>
                                                        <td width="20%"><b>Agency Name</b></td>
                                                        <td width="20%"><b>User Name</b></td>
                                                        <td width="15%"><b>Created At</b></td>
                                                        <td width="15%"><b>Updated On</b></td>
                                                        <td align="center" width="15%" style="border-right: 1px solid #e9ecef;"><b>Status</b></td>
                                                        <td width="15%"><b>Action</b></td>
                                                     </tr>
                                                    @forelse($value['agencies'] as $value2)
                                                    <tr>
                                                       <td width="20%">{{$value2->agency->comp_name}}</td>
                                                       <td width="20%">{{ucwords($value2->user->f_name.' '.$value2->user->l_name)}}</td>
                                                       <td width="15%">{{\Helpers::convertDateTimeFormat($value2->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d/m/Y h:i A') }}</td>
                                                       <td width="15%">{{($value2->rcu_status_updatetime)? \Helpers::convertDateTimeFormat($value2->rcu_status_updatetime, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d/m/Y h:i A') : ''}}</td>
                                                       <td align="center" width="15%" style="border-right: 1px solid #e9ecef;">{{$value2->status->status_name}}</td>
                                                       <td width="15%">
                                                           
                                                        @if(isset($value2->userFile->file_path))
                                                        @can('download_storage_file')
                                                        <a title="Download Report Document" href="{{ route('download_storage_file', ['file_id' => $value2->userFile->file_id ]) }}"><i class="fa fa-download"></i></a>
                                                        @endcan
                                                        @endif                                                        
                                                        
                                                        @if(isset($value2->status->id) && $value2->status->id != 2)
                                                        @else
                                                        @if($value2->is_active)
                                                        <button class="btn-upload btn-sm trigger-for-rcu-doc" style="padding: 1px 8px;" type="button" data-rcu_doc_id="{{$value2->rcu_doc_id}}"> <i class="fa fa-upload"></i></button>
                                                        @can('change_agent_rcu_status')
                                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>                                                                                                                                                                        
                                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" data-rcu_doc_id="{{$value2->rcu_doc_id}}" data-address_id="{{ $value->rcuDoc->id }}" data-app_id="{{request()->get('app_id')}}" data-biz_id="{{request()->get('biz_id')}}">
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="1" @if(isset($value2->userFile->file_path)) data-is_uploaded="1" @else data-is_uploaded="0" @endif >Pending</a>
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="2" @if(isset($value2->userFile->file_path)) data-is_uploaded="1" @else data-is_uploaded="0" @endif >Inprogress</a>
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="3" @if(isset($value2->userFile->file_path)) data-is_uploaded="1" @else data-is_uploaded="0" @endif >Positive</a>
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="4" @if(isset($value2->userFile->file_path)) data-is_uploaded="1" @else data-is_uploaded="0" @endif >Negative</a>
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="5" @if(isset($value2->userFile->file_path)) data-is_uploaded="1" @else data-is_uploaded="0" @endif >Cancelled</a>
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="6" @if(isset($value2->userFile->file_path)) data-is_uploaded="1" @else data-is_uploaded="0" @endif >Refer to Credit</a>
                                                        </div>
                                                        @endcan
                                                        @endif
                                                        @endif
                                                       </td>
                                                    </tr>
                                                    @empty
                                                    <tr style="text-align: center;">
                                                       <td width="100%" colspan="5">No data found</td>
                                                    </tr>
                                                    @endforelse
                                                  </tbody>
                                               </table>
                                            </td>
                                        </tr>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="rcuList_processing" class="dataTables_processing card" style="display: none;">Processing...</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                            @can('show_assign_rcu')
                            <button class="btn btn-success btn-sm" id="trigger-for-rcu">Trigger for RCU</button>
                            @endcan
                            <a data-toggle="modal" data-target="#assignRcuFrame" data-url ="{{route('show_assign_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="300px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openRcuModal" style="display: none;"><i class="fa fa-plus"></i>Assign RCU</a>
                            <a data-toggle="modal" data-target="#uploadRcuDocFrame" data-url ="{{route('rcu_upload', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" data-height="150px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openRcuDocModal" style="display: none;"><i class="fa fa-plus"></i>Upload Report</a>
                            <input type="hidden" id="rcuDId" value="">
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{!!Helpers::makeIframePopup('assignRcuFrame','Assign RCU', 'modal-lg')!!}
{!!Helpers::makeIframePopup('uploadRcuDocFrame','Upload Rcu Document', 'modal-md')!!}
{!!Helpers::makeIframePopup('modalPromoter','View PAN Card Detail', 'modal-lg')!!}
@endsection

@section('jscript')
<script>
$(document).ready(function(){
    $('#trigger-for-rcu').on('click', function(){
        if($('.document_id').is(':checked')){
            $('#openRcuModal').trigger('click');
        }else{
            alert('First check at least one checkbox.');
        }
    });
    
    $(document).on('click', '.trigger-for-rcu-doc', function(){
        $('#rcuDId').val($(this).data('rcu_doc_id'));
        $('#openRcuDocModal').trigger('click');
    });
    
    $('.change-status').on('click', function(){
        let address_id = $(this).parent('div').data('address_id');
        let status = $(this).attr('value');
        //hit ajax to save data to log table and update status of fi address and status in biz_addr table
    });
});

$(document).on('click', '.change-agent-status', function(){
    let rcu_doc_id = $(this).parent('div').data('rcu_doc_id');
    let address_id = $(this).parent('div').data('address_id');
    let app_id = $(this).parent('div').data('app_id');
    let biz_id = $(this).parent('div').data('biz_id'); 
    let status = $(this).attr('value');
    let token = '{{ csrf_token() }}';
    if ($(this).data('is_uploaded') == '0') {
        alert('You cannot update the RCU status until the document is uploaded.')
        return false;
    }    
    $('.isloader').hide();

    $.ajax({
        url: "{{route('change_agent_rcu_status')}}",
        type: "POST",
        data: {"rcu_doc_id": rcu_doc_id, "address_id":address_id, "app_id":app_id, "biz_id":biz_id, "status": status, "_token":token},
        //dataType:'json',
        error:function (xhr, status, errorThrown) {
            $('.isloader').hide();
            alert(errorThrown);
        },
        success: function(res){
            if(res.status == 1){
                $('#rcu_list').load(' #rcu_list');
            }else{
                alert(res.message);
            }
            $('.isloader').hide();
          }
    });
    /*------------------------------------------------------*/
    //hit ajax to save data to log table and update status of fi address and status in biz_addr table
});

$(document).on('click', '.change-cm-status', function(){
    let rcu_doc_id = $(this).parent('div').data('rcu_doc_id');
    let status = $(this).attr('value');
    let token = '{{ csrf_token() }}';
    $('.isloader').show();

    $.ajax({
        url: "{{route('change_cm_rcu_status')}}",
        type: "POST",
        data: {"rcu_doc_id": rcu_doc_id, "status": status, "_token":token},
        //dataType:'json',
        error:function (xhr, status, errorThrown) {
            $('.isloader').hide();
            alert(errorThrown);
        },
        success: function(res){
            if(res.status == 1){
                $('#rcu_list').load(' #rcu_list');
            }else{
                alert(res.message);
            }
            $('.isloader').hide();
          }
    });
    /*------------------------------------------------------*/
    //hit ajax to save data to log table and update status of fi address and status in biz_addr table
});
</script>
@endsection
