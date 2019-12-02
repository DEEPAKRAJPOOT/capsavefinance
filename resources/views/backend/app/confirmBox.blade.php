@extends('layouts.confirm-layout')

@section('content')


<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
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

                Are You sure to accept it ?.<br>
                <button type="submit" class="btn btn-success btn-sm float-right">Yes</button>
                {!!
                Form::close()
                !!}
              </div>
            </div>
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
        
    })
    </script>
@endsection