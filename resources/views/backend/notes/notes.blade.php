@extends('layouts.backend.admin-layout')
@include('layouts.backend.partials.admin-sidebar')
@section('content')
<ul class="main-menu">
    <li>
        <a href="company-details.php" >Application details</a>
    </li>
    <li>
              <a href="cam.php">CAM</a>
    </li>
    <li>
        <a href="residence.php">FI/RCU</a>
    </li>
    <li>
        <a href="Collateral.php">Collateral</a>
    </li>
    <li>
        <a href="notes" class="active">Notes</a>
    </li>
    <li>
        <a href="commercial.php">Submit Commercial</a>
    </li>
</ul>
<div class="content-wrapper">
<div class="row grid-margin mt-3 mb-2">
<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
    <div class="card">
        <div class="card-body">
          <div class=" form-fields">
           <div class="col-md-12">
              <h5 class="card-title form-head-h5">Notes  
              <a data-toggle="modal" data-target="#noteFrame" data-url ="{{route('backend_notes_from')}}" data-height="500px" data-width="100%" data-placement="top" class="add-btn-cls float-right"><i class="fa fa-plus"></i>Add Note</a>
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
                                                    <td class="text-right">Added By</td>                                                                        
                                                </tr> 

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

{!!Helpers::makeIframePopup('noteFrame','Add Note')!!}

@endsection
