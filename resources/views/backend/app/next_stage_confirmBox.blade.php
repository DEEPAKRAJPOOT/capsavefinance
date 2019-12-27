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
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                   <label class='error'>Please fill limit assessment data before move to next stage</label><br>
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_approved')
                   <label class='error'>Application is not approved by all approval authority to move the next stage.</label><br>                   
                   @endif
                                     
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_accepted')
                   <label class='error'>Still, offer is not accepted by sales queue to move the next stage.</label><br>                   
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_post_docs_uploaded')
                   <label class='error'>No any post sanctions documents are uploaded.</label><br>                   
                   @endif
                   
                   @if ($assign_case)
                    <label for="txtCreditPeriod">Please select Assignee
                      <span class="mandatory">*</span>
                    </label>
                   <br>
                    @if ($roles)
                    {!!
                    Form::select('sel_assign_role',
                    [
                    ''=>'Assignee']+$roles,
                    null,
                    array('id' => 'is_active',
                    'class'=>'form-control'))
                    !!}
                    @endif 
                    @php 
                    $confirmBtn = 'Assign';
                    $closeBtn = 'Cancel';
                    @endphp
                   @else
                    Are you sure to move the next stage <strong>({{ isset($roles[$next_role_id]) ? $roles[$next_role_id] : '' }})</strong>?<br>
                    @php 
                    $confirmBtn = 'Yes';
                    $closeBtn = 'No';
                    @endphp                    
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
                    {!! Form::hidden('curr_role_id', $curr_role_id) !!}
                    {!! Form::hidden('assign_case', $assign_case) !!}
                   
                    <br>
                    
                <button type="submit" class="btn btn-success">{{ $confirmBtn }}</button>
                <button id="close_btn" type="button" class="btn btn-secondary">{{ $closeBtn }}</button>              
                
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
        var assign_case = $("input[name=assign_case]").val(); 
        var targetModel = assign_case == '1' ? 'assignCaseFrame' : 'sendNextstage';
        var parent =  window.parent;        
        if(messages.is_accept == 1){
           parent.jQuery("#"+targetModel).modal('hide');  
           parent.oTable.draw();
        }

        $('#close_btn').click(function() {
            //alert('targetModel ' + targetModel);
            parent.$('#'+targetModel).modal('hide');
        });
        
        $('#frmMoveStage').validate({
            rules: {
                sel_assign_role: {
                    required: true
                },
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