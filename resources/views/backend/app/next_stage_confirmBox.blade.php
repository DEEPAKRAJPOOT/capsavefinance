@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
            <div class="row">
               <div class="col-md-12">
                    
                   @php $data = [];@endphp
                   @foreach($roles as $role)
                   @php $data[$role['id']] = $role['name'] @endphp
                   @endforeach
                   
                  
                   
               Are you sure to move the next stage?<br>
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'accept_next_stage',
                    )
                    ) 
                    !!}
                    
                    
                    {!!
                    Form::select('assign_role',
                    [
                    ''=>'Status']+$data,
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                    
                    {!! Form::hidden('app_id', $app_id) !!}
                    {!! Form::hidden('user_id', $user_id) !!}
                   
                    <br>
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
       parent.jQuery("#sendNextstage").modal('hide');  
        parent.oTable.draw();
    }
    
    $('#close_btn').click(function() {
    parent.$('#sendNextstage').modal('hide');
});
        
    })
    
    
    </script>
@endsection