@extends('layouts.confirm-layout')

@section('content')


<div class="modal-body text-left">
            <div class="row">
               <div class="col-md-4">
                    {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'accept_application_pool',
                    'app_id'=>$app_id,
                    'user_id'=>$user_id
                    )
                    ) 
                    !!}
                    
                     <input type="hidden" name="app_id" value="{{$app_id}}">
                      <input type="hidden" name="user_id" value="{{$user_id}}">
                   <input type="hidden" name="_token" value="{{csrf_token()}}">

                Are you sure you want to pick up this application?<br>
                <button type="submit" class="btn btn-success btn-sm float-right" id="pickupBtn">Yes</button>
                {!!
                Form::close()
                !!}
              </div>
            </div>
        </div>


@endsection

@section('jscript')

<script>
   
var messages = {
    is_accept: "{{ Session::get('is_accept') }}",
 };
     $(document).ready(function(){
         
     if(messages.is_accept == 1){
       var parent =  window.parent;     
       parent.jQuery("#pickLead").modal('hide');  
       //window.parent.jQuery('#my-loading').css('display','block');
        parent.oTable1.draw();
       //window.parent.location.href = messages.paypal_gatway;
    }
        
    });
    </script>
@endsection