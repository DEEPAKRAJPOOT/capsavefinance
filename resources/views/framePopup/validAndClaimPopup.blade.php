@extends('layouts.frame')

@section('content')
        @php $validNovltyArr = Helpers::getValidNovltyCheckList('for_valid_claim' , '1') @endphp
         @php $invalidFuncArr = Helpers::getValidNovltyCheckList('for_invalid_claim' , '2') @endphp
        
        <form action ="{{route('valid_and_claim',['user_id'=>$userId,'right_id'=>$rightId])}}" id ="validRightFrom"   method="post" enctype="multipart/form-data">                  
            <div class="from-group pt-4">      <input class="radiobtn mr-2 ml-2" name="claim" id="claim1" checked="checked" type="radio" value="1">Valid
            <input class="radiobtn ml-2 mr-2" name="claim" id="claim2" type="radio" value="2">InValid</div>
            <div class="comment-box valid-claim ml-auto" style="padding: 10px;">
                <div style ="color:red" class="form-errors"></div>      
                <div class = "div1">    
                    <div class="pt-0 ml-2 mr-2 ">
                        <div class="row pb-4 novelty"> <div class="col-sm-12 pl-0"> <h4>Novelty</h4> 
                                <span class="tooltip-1">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <p>Uniqueness of Rights is measured with a weightage of 0.7</p>
                                </span> </div>   
                         @foreach($validNovltyArr as $key => $index)
                            <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox clsRequired custom-control-input" value ="{{$index->id}}" name ="check_nvl[{{$key}}]" id="check_nvl{{$key}}">
                                <label class="custom-control-label" for="check_nvl{{$key}}">{{$index->title}} </label>
                            </div>
                         @endforeach
                        </div>
                    </div>
                    <div class="row pb-4 function">  
                        <div class="col-sm-12 pl-0"><h4> Functional</h4>
                                <span class="tooltip-1">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <p>Functionality of Rights is measured with a weightage of 0.3</p>
                                </span> 
                        </div> 
                        @foreach($invalidFuncArr as $key => $index)
                        <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox clsRequired custom-control-input" value ="{{$index->id}}" name ="check_fun[{{$key}}]" id="check_fun{{$key}}">
                                <label class="custom-control-label" for="check_fun{{$key}}">{{$index->title}} </label>
                        </div>
                         @endforeach
                     </div>
                </div>
                <div style="display:none" class = "div2">    
                    <div class="pt-0 ml-2 mr-2 ">
                        
                        <div class="row pb-4 novelty"> <div class="col-sm-12 pl-0"> <h4>Novelty</h4> 
                                                            <span class="tooltip-1">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <p>Uniqueness of Rights is measured with a weightage of 0.7</p>
                                </span>
                            </div>   
                         @foreach($validNovltyArr as $key => $index)
                            <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox otherChk clsRequired custom-control-input" value ="{{$index->id}}" name ="check_in[{{$key}}]" id="check_in{{$key}}">
                                <label class="custom-control-label" for="check_in{{$key}}">{{$index->title}} </label>
                            </div>
                         @endforeach
                        </div>
                    </div>
                    <div class="row pb-4 function">  
                        <div class="col-sm-12 pl-0"><h4> Functional</h4>
                                                        <span class="tooltip-1">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <p>Functionality of Rights is measured with a weightage of 0.3</p>
                                </span>
                        </div> 
                        @foreach($invalidFuncArr as $key => $index)
                        <div class="custom-control custom-checkbox col-sm-6">
                                <input  type="checkbox" class="mycheckbox otherChk clsRequired custom-control-input" value ="{{$index->id}}" name ="check_in_f[{{$key}}]" id="check_in_f{{$key}}">
                                <label class="custom-control-label" for="check_in_f{{$key}}">{{$index->title}} </label>
                        </div>
                         @endforeach
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
};
     $(document).ready(function(){
        if(messages.is_accept == 1){
     var parent =  window.parent;     
       parent.jQuery("#validClaimPopup").modal('hide');  
       window.parent.location.reload();
    }
        
    })
    </script>
    <script src="{{ asset('frontend/inside/js/rights_details.js')}}"></script>
@endsection