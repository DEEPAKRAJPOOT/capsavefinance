@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'renew_application',
                    'id' => 'frmCopyApp',
                    'target' => '_top'
                    )
                    ) 
                    !!}            
            <div class="row">                
               <div class="col-12">
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                   <label class='error'>You cannot move this application to next stage as offer still not created.</label><br>
                   @endif                                  
                   
                  @if ($appType == 2)
                    Are you sure to copy application for limit enhancement?<br>
                  @else
                    Are you sure to copy/renew the application?<br>
                  @endif  
                  <br>
                  <br>
                  <br>
                  <br>
                    @php 
                    $confirmBtn = 'Yes';
                    $closeBtn = 'No';
                    @endphp                    
                   

                    
                    
              </div>
                <div class="col-12">
                    <!--
                    <div class="form-group">
                       <label for="txtCreditPeriod">Comment
                       <span class="mandatory">*</span>
                       </label>
                       <textarea type="text" name="sharing_comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                    </div>
                    -->
                    {!! Form::hidden('app_id', $appId) !!}
                    {!! Form::hidden('biz_id', $bizId) !!}
                    {!! Form::hidden('user_id', $userId) !!}
                    {!! Form::hidden('app_type', $appType) !!}
                    
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
    redirect_url : "{{ route('copy_app_confirmbox', ['user_id' => $userId,'app_id' => $appId, 'biz_id' => $bizId]) }}"
 };
     $(document).ready(function(){        
        var targetModel = 'confirmCopyApp';
        var parent =  window.parent;  
        
        if (messages.error_code) {
            //parent.$('.isloader').hide();
        }
        
        if(messages.is_accept == 1){
           parent.jQuery("#"+targetModel).modal('hide');  
           parent.window.location = redirect_url;
           //parent.$('.isloader').hide();           
        }

        $('#close_btn').click(function() {            
            parent.$('#'+targetModel).modal('hide');
        });     
    })
    
    
    </script>
@endsection