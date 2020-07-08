@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
            {!!
             Form::open(
                array(
                    'method' => 'post',
                    'route' => 'save_end_program',
                    'id' => 'frmConfirmEndProgram',
                )
             ) 
             !!}            
            <div class="row">                
               <div class="col-12">
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'error_prgm_limit')
                   <label class='error'>Unable to edit program limit</label><br>
                   @endif
                          
                                      
                    Are you sure to edit the Program Limit?<br>
                    <label for="txtCreditPeriod">Please select reason <span class="mandatory">*</span> </label>
                    <br>

                    {!! 
                    Form::select('reason', 
                    [ '' => 'Select Reason'] + $reasonList, 
                    null, 
                    array('id' => 'is_active', 'class'=>'form-control')
                    ) 
                    !!}                   
                                       
              </div>
                <div class="col-12">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Comment
                       <span class="mandatory">*</span>
                       </label>
                       <textarea type="text" name="comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                    </div>                    
                <button type="submit" id="save_btn" class="btn btn-success btn-sm">Yes</button> &nbsp;
                <button type="button" id="close_btn" class="btn btn-secondary btn-sm">No</button>   
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
    error_code : "{{ Session::has('error_code') }}",
    route_url : "{{ Session::has('route_url') }}",
};

$(document).ready(function() {
    var targetModel = 'modifyProgramLimit';
    var parent =  window.parent;

    $('#frmConfirmEndProgram').submit(function() {
        if ($(this).valid()) {
           $("#save_btn").attr("disabled","disabled");
        }
    });

    if (messages.is_accept == 1) {
       parent.jQuery("#"+targetModel).modal('hide');
       parent.window.location = messages.route_url;
    }

    $('#close_btn').click(function() {            
        parent.$('#'+targetModel).modal('hide');
    });

    $('#frmConfirmEndProgram').validate({
        rules: {
            sel_reason: {
                required: true
            },
            comment: {
               required: true
            }
        },
        messages: {
        }
    });                          
})    
</script>
@endsection