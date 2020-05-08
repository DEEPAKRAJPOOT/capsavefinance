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
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'active_app_found')
                   <label class='error'>You can't create a new application before sanctions.</label><br>
                   @endif                                  
                  
                   
                  @if ($flag == 1)
                     You can't create a new application before sanctions.<br>
                    @php 
                    $confirmBtn = 'Yes';
                    $closeBtn = 'Close';
                    @endphp                        
                  @else
                    @if ($appType == 2)
                      Are you sure to copy application for limit enhancement?<br>
                    @elseif ($appType == 3)
                      Are you sure to copy application for reduce limit?<br>                                 
                    @else
                      Are you sure to copy/renew the application?<br>
                    @endif  

                    @php 
                    $confirmBtn = 'Yes';
                    $closeBtn = 'No';
                    @endphp                    
                 @endif  
                    <br>
                    <br>
                    <br>
                    <br>
                    
                    
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
                    {!! Form::hidden('flag', $flag) !!}
                    
                @if ($flag == '0')    
                <button type="submit" class="btn btn-success btn-sm btn-move-next-stage">{{ $confirmBtn }}</button> &nbsp;
                @endif
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
        var targetModel = ''; 
        var app_type = $("input[name=app_type]").val();
        if (app_type == '1') {
            targetModel = 'confirmCopyApp';    
        } else if(app_type == '2') {
            targetModel = 'confirmEnhanceLimit';
        } else if(app_type == '3') {
            targetModel = 'confirmReduceLimit';
        }
        
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