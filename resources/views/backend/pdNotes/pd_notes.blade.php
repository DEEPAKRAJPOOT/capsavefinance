@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
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
            <a href="{{ route('backend_inspection', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Inspection</a>
        </li>
        @endcan
        <li>
            <a href="{{ route('pd_notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active"> Personal Discussion </a>
        </li>
    </ul>


<div class="row grid-margin mt-3 mb-2">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
    <div class="card">
        <div class="card-body">
          <div class=" form-fields">
           <div class="col-md-12">
              <h5 class="card-title form-head-h5">Personal Discussion Note  
              <a data-toggle="modal" data-target="#pdNoteFrame" data-url ="{{route('backend_pd_notes_from',['app_id' => request()->get('app_id')])}}" data-height="500px" data-width="100%" data-placement="top" class="add-btn-cls btn btn-success btn-sm float-right"><i class="fa fa-plus">&nbsp;</i>Add Note</a>
            </h5>
                    <div class="col-md-12-cls">
                            <div class="prtm-full-block">       
                                <div class="prtm-block-content">
                                    <div class="table-responsive">
                                        <table class="table text-center table-striped table-hover">
                                            <thead class="thead-primary">
                                               
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="text-left">Type</th>
                                                    <th class="text-left">Title</th>
                                                    <th class="text-left">Note Details</th>
                                                    <th class="text-right">Created By</th>   
                                                    <th class="text-right">Created At</th>   
                                                </tr> 
                                                @foreach($arrData as $data)
                                                <tr>
                                                    <td class="text-left">@if($data->type==1) physical @elseif($data->type==2) Tele  @endif</td>
                                                    <td class="text-left">{{ $data->title }}</td>
                                                    <td class="text-left"><div style="max-height: 100px; max-width: 647px; overflow:auto;">{!! $data->comments !!}</div></td>
                                                    <td class="text-right">{{$data->f_name.' '.$data->m_name}}</td>  
                                                    <td class="text-right">{{$data->created_at }}</td>                                                                        
                                                </tr>
                                                @endforeach
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
</div>
</div>
</div>

{!!Helpers::makeIframePopup('pdNoteFrame','Add Personal Discussion Note', 'modal-lg')!!}

@endsection
