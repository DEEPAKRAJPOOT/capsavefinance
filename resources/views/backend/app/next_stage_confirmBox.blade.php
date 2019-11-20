@extends('layouts.backend.admin_popup_layout')

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
                    'route' => 'accept_next_stage',
                    )
                    ) 
                    !!}
                    
                    {!! Form::hidden('app_id', $app_id) !!}
                    {!! Form::hidden('user_id', $user_id) !!}
                   

               Are you sure to move the next stage?
                <button type="submit" class="btn btn-success">Yes</button>
                <button id="close_btn" type="button" class="btn btn-secondary">No</button>
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
       var parent =  window.parent;    
     if(messages.is_accept == 1){
       parent.jQuery("#pickLead").modal('hide');  
        parent.oTable1.draw();
    }
    
    $('#close_btn').click(function() {
    parent.$('#sendNextstage').modal('hide');
});
        
    })
    
    
    </script>
@endsection