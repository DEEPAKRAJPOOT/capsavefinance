@extends('layouts.frame')

@section('content')
        @php $validNovltyArr = Helpers::getValidNovltyCheckList('for_valid_claim' , '1') @endphp
         @php $invalidFuncArr = Helpers::getValidNovltyCheckList('for_invalid_claim' , '2') @endphp
        
        <form action ="{{route('save_report',['user_id'=>$userId,'right_id'=>$rightId])}}" id ="validRightFrom"   method="post" enctype="multipart/form-data">                  
            <div class="from-group pt-4">     <div class="comment-box valid-claim ml-auto" style="padding: 10px;">
                <div style ="color:red" class="form-errors"></div>      
                
                <div  class = "div2">    
                    <div class="pt-0 ml-2 mr-2 ">
                        
                        <div class="row pb-4 novelty"> <div class="col-sm-12 pl-0"> <h4>Novelty</h4> </div>   
                         @foreach($validNovltyArr as $key => $index)
                            <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox otherChk clsRequired custom-control-input" value ="{{$index->id}}" name ="check_in[{{$key}}]" id="check_in{{$key}}">
                                <label class="custom-control-label" for="check_in{{$key}}">{{$index->title}} </label>
                            </div>
                         @endforeach
                        </div>
                    </div>
                    <div class="row pb-4 function">  
                        <div class="col-sm-12 pl-0"><h4> Functional</h4></div> 
                        @foreach($invalidFuncArr as $key => $index)
                        <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox otherChk clsRequired custom-control-input" value ="{{$index->id}}" name ="check_in_f[{{$key}}]" id="check_in_f{{$key}}">
                                <label class="custom-control-label" for="check_in_f{{$key}}">{{$index->title}} </label>
                        </div>
                         @endforeach
                         <hr>
                         <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox clsRequired custom-control-input" value ="all_of_these" name ="all_of_these" id="all_of_these">
                                <label class="custom-control-label" for="all_of_these">All Of These</label>
                        </div>
                         
                         
                     </div>
                </div>
               
                    <div class="col-sm-12">
                        <input class=" attachFiles" type="file" multiple="multiple" name="validattachment[]" id="validattachment[]" style="">
                        <input type="hidden" name="_token" value="{{ csrf_token()}}">
                    <textarea rows="3" placeholder="Write Comment" name="comment" style="margin-top: 0px;margin-bottom: 0px;height: 71px;width: 100%;"></textarea>
                    </div>
                </div>
                    <div class="text-center">
                        <button  type ="submit" class="mysubmit btn btn-primary btn-sm"> Send <i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
                    </div>
           
        </form>
@endsection
@section('pageTitle')
My-Account
@endsection
@section('addtional_css')

@endsection
@section('jscript')
<script>
   
var messages = {
    is_accept: "{{ Session::get('is_accept') }}",
    paypal_gatway: "{{ route('confirm_payment') }}",
};
     $(document).ready(function(){
        if(messages.is_accept == 1){
        var parent =  window.parent;     
       parent.jQuery("#ReportPopup").modal('hide');
       window.parent.jQuery('#my-loading').css('display','block');
       window.parent.location.href = messages.paypal_gatway;
    }
        
    })
    </script>
    <script src="{{ asset('frontend/inside/js/rights_details.js')}}"></script>
@endsection