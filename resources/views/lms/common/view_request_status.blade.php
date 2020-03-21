@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'lms_save_request_status',
                    'id' => 'frmSaveReqStatus',
                    )
                    ) 
                    !!}            
            <div class="row">                
                <div class="col-12">

                    @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                    <label class='error'>You cannot move this application to next stage as limit assessment is not done.</label><br>
                    @endif

                 <div class="form-group">
                     {!! Form::label('status', 'Select Status', ['class' => 'control-label'] )  !!}
                     <div class="col-lg-10">
                         {!!  Form::select('status', $statusList, '', ['class' => 'form-control' ]) !!}
                     </div>
                 </div>


               </div>
                <div class="col-12">
                    <div class="form-group">
                       <label for="txtCreditPeriod">Comment
                       <span class="mandatory">*</span>
                       </label>
                       <textarea type="text" name="comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                    </div>
                    {!! Form::hidden('req_id', $reqId) !!}
                    
                <button type="submit" class="btn btn-success btn-sm btn-move-next-stage">Submit</button> &nbsp;
                <button id="close_btn" type="button" class="btn btn-secondary btn-sm">Cancel</button>   
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
        var targetModel = 'lms_update_request_status';
        var parent =  window.parent;  
                
        $('.btn-move-next-stage').click(function() {            
            if ($('#frmSaveReqStatus').valid()) {
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
        
        $('#frmSaveReqStatus').validate({
            rules: {
                status: {
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
