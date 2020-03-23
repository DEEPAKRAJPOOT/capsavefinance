@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
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
               <div class="col-12">
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                   <label class='error'>You cannot move this application to next stage as limit assessment is not done.</label><br>
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_approved')
                   <label class='error'>You cannot move this application to next stage as the limit is not approved by all approval authority.</label><br>                   
                   @endif
                                     
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_accepted')
                   <label class='error'>Still, offer is not accepted by sales queue to move the next stage.</label><br>                   
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_docs_found')
                   <label class='error'>No required documents found.</label><br>                   
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_pre_docs_uploaded')
                   <label class='error'>You cannot move this application to next stage as no pre-sanction documents are uploaded.</label><br>                   
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_post_docs_uploaded')
                   <label class='error'>You cannot move this application to next stage as no post-sanction documents are uploaded.</label><br>                   
                   @endif
                   
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_approval_users_found')
                   <label class='error'>No members found in approval authority team.</label><br>                   
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
                <div class="col-12">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Comment
                       <span class="mandatory">*</span>
                       </label>
                       <textarea type="text" name="sharing_comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                    </div>
                    {!! Form::hidden('app_id', $app_id) !!}
                    {!! Form::hidden('user_id', $user_id) !!}
                    {!! Form::hidden('curr_role_id', $curr_role_id) !!}
                    {!! Form::hidden('assign_case', $assign_case) !!}
                    {!! Form::hidden('biz_id', $biz_id) !!}
                <!-- <button type="submit" class="btn btn-success">{{ $confirmBtn }}</button>
                <button id="close_btn" type="button" class="btn btn-secondary">{{ $closeBtn }}</button>               -->
                <button type="submit" class="btn btn-success btn-sm btn-move-next-stage">{{ $confirmBtn }}</button> &nbsp;
                <button id="close_btn" type="button" class="btn btn-secondary btn-sm">{{ $closeBtn }}</button>   
            </div>
            </div>
                {!!
                Form::close()
                !!}                      
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
        
        /*
        $('.btn-move-next-stage').click(function() {
            //$('.isloader').show();
            if ($('#frmMoveStage').valid()) {
                $(this).attr('disabled', 'disabled');
            }
        });
        */
       
        if(messages.is_accept == 1){
           parent.jQuery("#"+targetModel).modal('hide');  
           parent.oTable.draw();
           //$('.isloader').hide();           
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