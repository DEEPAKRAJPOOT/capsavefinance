@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
<div class="row grid-margin mt-3 mb-2">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
    <div class="card">
        <div class="card-body">
          <div class=" form-fields">
           <div class="col-md-12">
              <h5 class="card-title form-head-h5">Query  
              <a data-toggle="modal" data-target="#queryFrame" data-url ="{{route('query_management_from',['app_id' => request()->get('app_id')])}}" data-height="500px" data-width="100%" data-placement="top" class="add-btn-cls btn btn-success btn-sm float-right"><i class="fa fa-plus">&nbsp;</i>Add Query</a>
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
                                                    <th class="text-left">Assign To</th>
                                                    <th class="text-left">Query Details</th>
                                                    <th class="text-right">Added By</th>   
                                                    <th class="text-right">Action</th>   
                                                </tr> 
                                                @foreach($arrData as $data)
                                                <tr>
                                                    <td class="text-left">{{ $arrRole[$data->assign_role_id] }}</td>
                                                    <td class="text-left">{!! $data->qms_cmnt !!}</td>
                                                    <td class="text-right">{{$data->f_name.' '.$data->m_name}}</td>      <td>
                                                        <a data-toggle="modal" data-target="#queryFrame" data-url ="{{route('query_management_from',['app_id' => request()->get('app_id')])}}" data-height="500px" data-width="100%" data-placement="top" class="add-btn-cls btn btn-success btn-sm float-right"><i class="fa fa-plus">&nbsp;</i>Add Query</a>
                                                    </td>                                                                 
                                                   <!--  <td class="text-right">View Attachment</td>   -->                                                                      
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

{!!Helpers::makeIframePopup('queryFrame','Add Query', 'modal-lg')!!}

@endsection
