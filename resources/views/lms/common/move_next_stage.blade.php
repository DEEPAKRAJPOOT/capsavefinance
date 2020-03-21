@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'lms_accept_next_stage',
                    'id' => 'frmMoveStage',
                    )
                    ) 
                    !!}            
            <div class="row">                
               <div class="col-12">
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                   <label class='error'>You cannot move this application to next stage as limit assessment is not done.</label><br>
                   @endif
                   
                   @if ($back_stage)
                        Are you sure to move the previous stage <strong>({{ $back_stage }})</strong>?<br>
                        @php 
                        $confirmBtn = 'Yes';
                        $closeBtn = 'No';
                        @endphp
                   @else
                        Are you sure to move the next stage <strong>({{ $next_stage ? $next_stage : '' }})</strong>?<br>
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
                    {!! Form::hidden('req_id', $reqId) !!}
                    {!! Form::hidden('back_stage', $back_stage) !!}
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
    error_code : "{{ Session::has('error_code') }}",
 };
     $(document).ready(function(){
        var back_stage = $("input[name=back_stage]").val(); 
        var targetModel = back_stage != '' ? 'lms_move_prev_stage' : 'lms_move_next_stage';
        var parent =  window.parent;  
                
        $('.btn-move-next-stage').click(function() {            
            if ($('#frmMoveStage').valid()) {
                parent.$('.isloader').show();
            }
        });
        
        if (messages.error_code) {
            parent.$('.isloader').hide();
        }
        
        if(messages.is_accept == 1){
           parent.jQuery("#"+targetModel).modal('hide');  
           parent.oTable.draw();
           parent.$('.isloader').hide();           
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
