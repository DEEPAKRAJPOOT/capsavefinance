@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
    <!-- partial -->
    <div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        @can('backend_fi')
        <li>
            <a href="{{ route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">FI Residence</a>
        </li>
        @endcan
        @can('backend_rcu')
        <li>
            <a href="{{ route('backend_rcu', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">RCU Document</a>
        </li>
        @endcan
        @can('backend_inspection')
        <li>
            <a href="{{ route('backend_inspection', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Inspection</a>
        </li>
        @endcan
        @can('pd_notes_list')
        <li>
            <a href="{{ route('pd_notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}"> Personal Discussion </a>
        </li>
        @endcan
    </ul>


<div class="row grid-margin mt-3">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive" id="fi_list">
                            <table id="fiList" class="table white-space table-striped cell-border no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="supplier-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">
                                        <th>Residence ID</th>
                                        <th>Address Type</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($fiLists as $key=>$fiList)
                                    <tr role="row" class="odd">
                                        <td><input type="checkbox" value="{{$fiList->biz_addr_id}}" class="address_id">{{$fiList->biz_addr_id}}</td>
                                        <td>{{$addrType[$fiList->address_type]}}</td>
                                        <td>{{($fiList->biz_owner_id)? $fiList->owner->first_name: $fiList->business->biz_entity_name}}</td>                                      
                                        <td>{{$fiList->addr_1.' '.$fiList->city_name.' '.(isset($fiList->state->name)? $fiList->state->name: '').' '.$fiList->pin_code}}</td>                                      
                                        <td>
                                          <div class="btn-group"><label class="badge badge-warning">{{($fiList->cmFiStatus)? $fiList->cmFiStatus->cmStatus->status_name: 'Pending'}}&nbsp; &nbsp;</label></div>
                                        </td>
                                        <td>
                                            <div class="btn-group ml-2 mb-1">
                                                @if($fiList->fiAddress->count() && Auth::user()->agency_id == null)
                                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" data-address_id="{{$fiList->biz_addr_id}}">
                                                    @foreach($status_lists as $status_id => $status_name)
                                                        <a class="dropdown-item change-cm-status" href="javascript:void(0);" value="{{$status_id}}">{{$status_name}}</a>
                                                    @endforeach
                                                </div>
                                                @endif
                                            </div>
                                        </td> 
                                        <td align="right"><span class="trigger minus"></span></td> 
                                    </tr>
                                    <tr class="dpr" style="display: table-row;">
                                        <td colspan="8" class="p-0">
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
                                                 @forelse($fiList->fiAddress as $fiAdd)
                                                 @if(Auth::user()->agency_id == null || $fiAdd->agency_id == Auth::user()->agency_id)
                                                 <tr>
                                                    <td width="20%">{{$fiAdd->agency->comp_name}}</td>
                                                    <td width="20%">{{ucwords($fiAdd->user->f_name.' '.$fiAdd->user->l_name)}}</td>
                                                    <td width="15%">{{\Carbon\Carbon::parse($fiAdd->created_at)->format('d/m/Y h:i A')}}</td>
                                                    <td width="15%">{{($fiAdd->fi_status_updatetime)? \Carbon\Carbon::parse($fiAdd->fi_status_updatetime)->format('d/m/Y h:i A'): ''}}</td>
                                                    <td align="center" width="15%" style="border-right: 1px solid #e9ecef;">{{$fiAdd->status->status_name}}</td>
                                                    <td width="15%">

                                                        @if(isset($fiAdd->userFile->file_path))
                                                        <a title="Download Document" href="{{ Storage::url($fiAdd->userFile->file_path) }}" download="{{$document->userFile->file_name}}"><i class="fa fa-download"></i></a>
                                                        @endif

                                                        @if($fiList->cmFiStatus && $fiList->cmFiStatus->cmStatus->status_name == 'Positive')
                                                        <!-- Take Rest -->
                                                        @elseif($fiAdd->is_active && Auth::user()->agency_id !=null)
                                                        <button class="btn-upload btn-sm trigger-for-fi-doc" style="padding: 1px 8px;" type="button" data-fiadd_id="{{$fiAdd->fi_addr_id}}"> <i class="fa fa-upload"></i></button>
                                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                        @else
                                                        @endif

                                                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" data-fi_address_id="{{$fiAdd->fi_addr_id}}">
                                                        @foreach($status_lists as $status_id => $status_name)
                                                            <a class="dropdown-item change-agent-status" href="javascript:void(0);" value="{{$status_id}}">{{$status_name}}</a>
                                                        @endforeach
                                                        </div>
                                                    </td>
                                                 </tr>
                                                 @endif
                                                 @empty
                                                 <tr style="text-align: center;">
                                                    <td width="100%" colspan="5">No data found</td>
                                                 </tr>
                                                 @endforelse
                                              </tbody>
                                           </table>
                                        </td>
                                    </tr>
                                @empty
                                <tr><td colspan="7">No data found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                           {{-- @if(request()->get('view_only')) --}}
                           <button class="btn btn-success btn-sm" id="trigger-for-fi">Trigger for Inspection</button>
                           <a data-toggle="modal" data-target="#assignFiFrame" data-url ="{{route('show_assign_inspection', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" data-height="300px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openFiModal" style="display: none;"><i class="fa fa-plus"></i>Assign Inspection</a>
                           {{-- @endif --}}
                           <a data-toggle="modal" data-target="#uploadFiDocFrame" data-url ="{{route('inspection_upload', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" data-height="200px" data-width="100%" data-placement="top" class="add-btn-cls float-right" id="openFiDocModal" style="display: none;"><i class="fa fa-plus"></i>Assign Inspection</a>
                           <input type="hidden" id="fiaid4upload" value="">
                            <!--<a href="#" class="btn btn-success" data-toggle="modal" data-target="#myModal1" style="clear: both;">Report Uploads</a>-->
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{!!Helpers::makeIframePopup('assignFiFrame','Assign Inspection', 'modal-lg')!!}
{!!Helpers::makeIframePopup('uploadFiDocFrame','Upload Inspection Document', 'modal-md')!!}
@endsection

@section('jscript')
<script>
$(document).ready(function(){
    $('#trigger-for-fi').on('click', function(){
        if($('.address_id').is(':checked')){
            $('#openFiModal').trigger('click');
        }else{
            alert('First check at least one checkbox.');
        }
    });

    $(document).on('click', '.trigger-for-fi-doc', function(){
        $('#fiaid4upload').val($(this).data('fiadd_id'));
        $('#openFiDocModal').trigger('click');
    });

    $(document).on('click', '.change-cm-status', function(){
        let address_id = $(this).parent('div').data('address_id');
        let status = $(this).attr('value');
        let token = '{{ csrf_token() }}';
        $('.isloader').show();

        $.ajax({
            url: "{{route('change_cm_fi_status')}}",
            type: "POST",
            data: {"addr_id": address_id, "status": status, "_token":token},
            //dataType:'json',
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success: function(res){
                if(res.status == 1){
                    $('#fi_list').load(' #fi_list');
                }else{
                    alert(res.message);
                }
                $('.isloader').hide();
              }
        });
    });

    $(document).on('click', '.change-agent-status', function(){
        let fi_addr_id = $(this).parent('div').data('fi_address_id');
        let status = $(this).attr('value');
        let token = '{{ csrf_token() }}';
        $('.isloader').show();
        
        $.ajax({
            url: "{{route('change_agent_fi_status')}}",
            type: "POST",
            data: {"fi_addr_id": fi_addr_id, "status": status, "_token":token},
            //dataType:'json',
            error:function (xhr, status, errorThrown) {
                $('.isloader').hide();
                alert(errorThrown);
            },
            success: function(res){
                if(res.status == 1){
                    $('#fi_list').load(' #fi_list');
                }else{
                    alert(res.message);
                }
                $('.isloader').hide();
              }
        });
    });
});
</script>
@endsection