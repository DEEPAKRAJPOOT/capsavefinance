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
            <h5 class="card-title form-head-h5">Notes  <a class="add-btn-cls float-right" data-toggle="modal" data-target="#myModal"> <i class="fa fa-plus"></i> Add Note</a></h5>

            <h5 class="card-title form-head-h5">Notes Demo 
              <a data-toggle="modal" data-target="#noteFrame" data-url ="{{route('backend_notes_from')}}" data-height="500px" data-width="100%" data-placement="top" class="add-btn-cls float-right"><i class="fa fa-plus"></i>Add Note2</a>
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
                                                <tr>
                                                    <th class="text-left">Abc under writer</th>
                                                    <td class="text-right">Lorem ispur data here</td>                                                                        
                                                </tr>	<tr>
                                                    <th class="text-left">Abc under writer</th>
                                                    <td class="text-right">Lorem ispur data here</td>                                                                        
                                                </tr>	<tr>
                                                    <th class="text-left">Abc under writer</th>
                                                    <td class="text-right">Lorem ispur data here</td>                                                                        
                                                </tr>	<tr>
                                                    <th class="text-left">Abc under writer</th>
                                                    <td class="text-right">Lorem ispur data here</td>                                                                        
                                                </tr>	<tr>
                                                    <th class="text-left">Abc under writer</th>
                                                    <td class="text-right">Lorem ispur data here</td>                                                                        
                                                </tr>	<tr>
                                                    <th class="text-left">Abc under writer</th>
                                                    <td class="text-right">Lorem ispur data here</td>                                                                        
                                                </tr>	<tr>
                                                    <th class="text-left">Abc under writer</th>
                                                </tr>
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

<!--modal-->
<div class="modal" id="myModal">
<div class="modal-dialog">
  <div class="modal-content">
     <!-- Modal Header -->
     <div class="modal-header">
        <h5>Add Note</h5>
        <button type="button" class="close close-btns" data-dismiss="modal">&times;</button>
     </div>
     <!-- Modal body -->
     <div class="modal-body text-left">
       
            <div class="form-group">
                <label class="">Comment</label>                                          
                <textarea class="form-control" id='notesData'></textarea>
                <span id='errorMsg'></span>
            </div> 
        <button type="submit" class="btn btn-primary float-right" onclick="submitNotes();">Submit</button> 
     </div>
  </div>
</div>
</div>




{!!Helpers::makeIframePopup('noteFrame','Add Note')!!}

@endsection
@section('jscript')
<script>
   function submitNotes(){ 
       var notesData = $.trim($('#notesData').val());
       if(notesData == ''){
            $('#errorMsg').html('Please Enter Comment');
            setTimeout(function(){ $('#errorMsg').html(''); }, 1000);
       }else{
           $.ajax({
               type: 'POST',
               url:'/notes',
               data:{'notesData':notesData},
               dataType:'html',
               headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
               success:function(data){
                console.log(data);
               }
           });
       }

    }
</script>
@endsection