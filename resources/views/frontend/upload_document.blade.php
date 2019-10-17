@extends('layouts.app')

@section('content')

<style type="text/css">
    
.btn-file {
    position: relative;
    overflow: hidden;
}

    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>


<section>
  <div class="container">
   <div class="row">
  
    <div id="header" class="col-md-3">
        @include('layouts.user-inner.left-menu')

      <!--  <div class="list-section">
         <div class="kyc">
           <h2>KYC</h2>
           <p class="marT15 marB15">Individual Natural Person (director, shareholder, Ultimate Beneficial Owner)</p>
           <ul class="menu-left">
             <li><a href="#">Company Details</a></li>
             <li><a href="#">Address Details</a></li>
             <li><a href="#">Shareholding Structure</a></li>
             <li><a href="#">Financial Information</a></li>
              <li><a class="active" href="#">Documents & Declaration</a></li>
           </ul>
         
        </div>
       </div> -->
    </div>

    <div class="col-md-9 dashbord-white">

     <div class="form-section">
        <h3 id="msg" style='color:green;'>   </h3>
       <div class="row marB10">
           <div class="col-md-12">
             <h3 class="h3-headline">Documents & Declaration</h3>
           </div>
           

          
                    <form id="documentform" enctype="multipart/form-data" class="needs-validation form" novalidate method="post">
            @csrf
            <div class="row marT15 marB10">
             <div class="col-md-12">
                <div class="form-group">
                  <label for="pwd">Documents to be provided along with this form</label> 
                </div>
              </div>

            </div>

        
@php
 $ii = 0;
 $g=0;
@endphp


@if($documentArray!=null)
@foreach($documentArray as $document)
@php $g = $ii++ @endphp




<div class="row marB20">
        <div class="col-md-9">
            <p class="text-color bullet marB5">{{$document['upload_doc_name']}}</p>

@php
$docList = [];

$docList=Helpers::getDocumentList(Auth()->user()->user_id, $document['user_req_doc_id'] );
@endphp
@if(count($docList) > 0 )
    @foreach($docList as $val)
        @php
          $docName ='';
            $docName = $val->doc_name.".".$val->doc_ext;
        @endphp
       <ul class="document-list">
            <li>
                
                <a href="{{route('import_document',['enc_id' => $val->enc_id])}}">
         
                         {{$docName}} &nbsp; &nbsp; Download  
                </a> 

            </li>
        </ul>
    @endforeach
@endif




            
        </div>
        <label>   </label>
        

    <div class="col-md-3 text-right row files" id="files{{$g}}">
        
    <span class="btn btn-default btn-file">
         <label for="pwd">   Browse  </label><input id="pics{{$g}}" type="file"  name="files[]" data-id="{{$document['user_req_doc_id'].'#'.$document['user_kyc_id'].'#'.$document['doc_id']
    }}" multiple class="upload"/>
    </span>
        <br />
        <ul class="fileList"></ul>
    </div>

</div>  





<input type = "text" value = "{{$document['user_req_doc_id'].'#'.$document['user_id'].'#'.$document['doc_id']
            }}" id ="filId{{$g}}" style="display: none;">
@endforeach
@endif



<input type = "text" value = "{{$g}}" id ="gval" style='display: none'>

    
        <div class="row marB25 marT25">
              <div class="col-md-12">
               <div class="form-group">
                  <label for="pwd">Declaration</label> 
                  <p class="text-color font-13">We hereby declare that the particulars given herein are true, correct &amp; complete to the best of our knowledge &amp; belief. We undertake to promptly inform Dexter Capital Financial Consultancy LLC of any changes or information provided hereinabove.</p>
                </div>
            </div>    
        </div>


     
        <div class="row marT30">
            <div class="col-md-6">
               <div class="form-group">
                  <div class="form-check-inline">
                    <label class="form-check-label" for="check2">
                    <input type="checkbox" class="form-check-input" id="check2" name="termcondition" value="something">I accept the Terms and Conditions 
                      
                        
                    </label>
                    </div>
              </div>
            </div>
             <div class="col-md-6 text-right">
              <a href="#" class="btn btn-prev">Previous</a> 
              <button  type="button"  id="indivisualuploadBtn" class="btn btn-save">Submit</button>

              
             </div>
        </div>
            
            
         </form>
      </div>
    </div>
    
   </div>   
  </div>
</div>
</section>

<?php
$userKycId      =   $benifinary['user_kyc_id'];
$corpUserId     =   $benifinary['corp_user_id'];
$isBycompany    =   $benifinary['is_by_company'];

?>




<script src="{{ asset('backend/theme/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('frontend/outside/js/validation/socialmedia.js')}}"></script>
<script>
$(document).ready(function () {
    // $('.datepicker').datepicker();
    var date = $('.dobdatepicker').datepicker({dateFormat: 'yy-mm-dd'});
    var date = $('.maturityDate').datepicker({dateFormat: 'yy-mm-dd'});


});


var globlvar={
    "save_doc_url":"{{ route('upload_document',['user_kyc_id' => $userKycId, 'corp_user_id' => $corpUserId, 'is_by_company' => $isBycompany]) }}",
};

</script>


@include('frontend.company.documentfileuploadjs')

@endsection
