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
              <h5 class="card-title form-head-h5">Notes  
              @if(request()->get('view_only'))    
              <a data-toggle="modal" data-target="#noteFrame" data-url ="{{route('backend_notes_from',['app_id' => request()->get('app_id')])}}" data-height="500px" data-width="100%" data-placement="top" class="add-btn-cls btn btn-success btn-sm float-right"><i class="fa fa-plus">&nbsp;</i>Add Note</a>
              @endif
            </h5>
                    <div class="col-md-12-cls">
                            <div class="prtm-full-block">       
                                <div class="prtm-block-content">
                                    <div class="table-responsive">
                                        <table class="table text-center table-striped table-hover">
                                            <thead class="thead-primary">
                                                <tr>
                                                    <th class="text-left" conspan="2">Case Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="text-left">Note Details</th>
                                                    <td class="text-right">Added By</td>                                 </tr> 
                                                @foreach($arrData as $data)
                                                <tr>
                                                    <th class="text-left">{{$data->note_data}}</th>
                                                    <td class="text-right">{{$data->f_name.' '.$data->m_name}}</td>                                                                        
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

{!!Helpers::makeIframePopup('noteFrame','Add Note', 'modal-lg')!!}

@endsection
