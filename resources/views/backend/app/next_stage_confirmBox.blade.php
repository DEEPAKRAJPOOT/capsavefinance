@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="content-wrapper">
    <div class="card">
        <div class="card-body">
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'accept_next_stage',
                    'id' => 'frmMoveStage',
                    )
                    ) 
                    !!}            
            <div class="row">                
               <div class="col-md-12">
                    
                   @php $data = [];@endphp
                   @foreach($roles as $role)
                   @php $data[$role['id']] = $role['name'] @endphp
                   @endforeach
                   
                  
                   
               Are you sure to move the next stage?<br>

                    
                    @if ($data)
                    {!!
                    Form::select('assign_role',
                    [
                    ''=>'Status']+$data,
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                    @endif
                    
              </div>
                <div class="col-md-12">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Comment
                       <span class="mandatory">*</span>
                       </label>
                       <textarea type="text" name="sharing_comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                    </div>
                 </div>
                
                    {!! Form::hidden('app_id', $app_id) !!}
                    {!! Form::hidden('user_id', $user_id) !!}
                   
                    <br>
                <button type="submit" class="btn btn-success">Yes</button>
                <button id="close_btn" type="button" class="btn btn-secondary">No</button>              
                
            </div>
                {!!
                Form::close()
                !!}                      
        </div>
    </div>

</div>


@endsection

@section('jscript')
<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
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
        
        $('#frmMoveStage').validate({
            rules: {
                sharing_comment: {
                   required: true
                }
            },
            messages: {
            }
        });
            
    })
    
    
    </script>
@endsection