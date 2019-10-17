@extends('layouts.frame')

@section('content')
               <form action="{{route('accept_term_condition',['user_id'=>$userId, 'right_id'=>$rightId])}}" id="appForm" method="post">
                <div class="modal-body text-center">
                <div class="term">
                    <label class="checkbox text-left check-left">
                        <input required="required" type="checkbox" value="remember-me">
                        <span class="label-text">I accept the terms and conditions for signing up to this service, and hereby confirm I have read the privacy policy.</span>
                    </label>
                </div>
                   <input type="hidden" name="_token" value="{{ csrf_token()}}">
                    <input type="submit"  class="btn btn-success btn-sm mbt" value = "Accept"> 
                </div>
            </form>    
       
@endsection
@section('pageTitle')
Term and Condition
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
       parent.jQuery("#putStakePopup").modal('hide');  
       window.parent.jQuery('#my-loading').css('display','block');
       window.parent.location.href = messages.paypal_gatway;
    }
        
    })
    </script>
@endsection