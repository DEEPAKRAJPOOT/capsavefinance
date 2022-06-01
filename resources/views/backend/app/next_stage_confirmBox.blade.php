@extends('layouts.backend.admin_popup_layout')

@section('content')


<div class="modal-body text-left">
                   {!!
                    Form::open(
                    array(
                    'method' => 'post',
                    'route' => 'accept_next_stage',
                    'id' => 'frmMoveStage',
                    'enctype' => 'multipart/form-data'
                    )
                    ) 
                    !!}            
            <div class="row">                
               <div class="col-12">
                    
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_found')
                   <label class='error'>You cannot move this application to next stage as offer still not created.</label><br>
                   @endif
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_approved')
                   <label class='error'>You cannot move this application to next stage as the limit is not approved by all approval authority.</label><br>                   
                   @endif
                                     
                   @if (Session::has('error_code') && Session::get('error_code') == 'no_offer_accepted')
                   <label class='error'>You cannot move this application to the next stage as the offer is not accepted.</label><br>                   
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
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'validate_limit_enhance_amt')
                   <label class='error'>{{ trans('backend_messages.validate_limit_enhance_amt') }}</label><br>                   
                   @endif 
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'validate_reduce_limit_amt')
                   <label class='error'>{{ trans('backend_messages.validate_reduce_limit_amt') }}</label><br>                   
                   @endif                         
                   
                   @if (Session::has('error_code') && Session::get('error_code') == 'limit_rejected')
                   <label class='error'>{{ trans('backend_messages.validate_limit_rejected') }}</label><br>                   
                   @endif

                   @if (Session::has('error_code') && Session::get('error_code') == 'validate_fi_status')
                   <label class='error'>You cannot move this application to the next stage as the fi verification is pending for this customer.</label><br>                   
                   @endif
                   @if ($assign_case)
                        <label for="txtCreditPeriod">Please select Assignee <span class="mandatory">*</span> </label>
                        <br>
                        @if ($roles)
                            {!! Form::select('sel_assign_role', [ ''=>'Assignee']+$roles, null, array('id' => 'is_active', 'class'=>'form-control')) !!}
                        @endif 
                    @php 
                    $confirmBtn = 'Assign';
                    $closeBtn = 'Cancel';
                    @endphp
                   @else
                    @if ($nextStage && $nextStage->stage_code == 'disbursed_or_in_lms')
                    Are you sure want to Activate the limit..?<br>
                    @else
                    Are you sure to move the next stage <strong>({{ isset($roles[$next_role_id]) ? $roles[$next_role_id] : '' }})</strong>?<br>
                    @endif
                    @php 
                    $confirmBtn = 'Yes';
                    $closeBtn = 'No';
                    @endphp                    
                   @endif

                   @if (Session::has('error_code') && Session::get('error_code') == 'validate_offer_approved')
                   <label class='error'>You cannot pull back this application as the offer has been approved by all the approvers.</label><br>                   
                   @endif
                   
                    @if(!$isAppPullBack)
                       @if ($assign_case)
                            <label for="txtCreditPeriod">Please select Assignee <span class="mandatory">*</span> </label>
                            <br>
                            @if ($roles)
                                {!! Form::select('sel_assign_role', [ ''=>'Assignee']+$roles, null, array('id' => 'is_active', 'class'=>'form-control')) !!}
                            @endif 
                            @php 
                                $confirmBtn = 'Assign';
                                $closeBtn = 'Cancel';
                            @endphp
                        @else
                            @if ($nextStage && $nextStage->stage_code == 'disbursed_or_in_lms')
                                Are you sure want to Activate the limit..?<br>
                            @else                        
                                Are you sure to move the next stage <strong>({{ isset($roles[$next_role_id]) ? $roles[$next_role_id] : '' }})</strong>?<br>
                            @endif
                            @php 
                                $confirmBtn = 'Yes';
                                $closeBtn = 'No';
                            @endphp                    
                        @endif
                    @else
                        @if($isAppPullBack)
                            Are you sure want to Pull Back the Application..?<br>
                        @endif
                        @php 
                            $confirmBtn = 'Yes';
                            $closeBtn = 'No';
                        @endphp
                    @endif                                   
              </div>
              <div class="col-12">
                    @if(!$isAppPullBack)
                        <div class="form-group">
                            <label for="txtCreditPeriod">Comment<span class="mandatory">*</span></label>
                            <textarea type="text" name="sharing_comment" value="" class="form-control" tabindex="1" placeholder="Add Comment" required=""></textarea>
                        </div>
                        @if(!$assign_case && $nextStage && $nextStage->stage_code=='approver')  
                            @if(count($approvers) > 0)
                                <div class="form-group">
                                    <label for="txtCreditPeriod">Approver List<span class="mandatory">*</span></label>
                                </div>          
                                @foreach($approvers as $row)                    
                                    <div> <input type="checkbox" checked="checked" name="approver_list[]" class="approver_list" value="{{$row->user_id}}" id="approver_list">&nbsp; {{$row->f_name}}&nbsp;{{$row->l_name}}&nbsp; ({{$row->product_name}})</div>                       
                                @endforeach
                            @else               
                                <div class="error"> <i>Approver is not found...</i></div>
                            @endif
                        @endif
                    @endif
                      {!! Form::hidden('app_id', $app_id) !!}
                    {!! Form::hidden('biz_id', $biz_id) !!}
                    {!! Form::hidden('user_id', $user_id) !!}
                    {!! Form::hidden('curr_role_id', $curr_role_id) !!}
                    {!! Form::hidden('assign_case', $assign_case) !!}
                    {!! Form::hidden('biz_id', $biz_id) !!}
                    {!! Form::hidden('is_app_pull_back', $isAppPullBack) !!}
                <!-- <button type="submit" class="btn btn-success">{{ $confirmBtn }}</button>
                <button id="close_btn" type="button" class="btn btn-secondary">{{ $closeBtn }}</button>               -->
                <button type="submit" @php if($nextStage && $nextStage->stage_code=='approver') { @endphp id="submit" @php } @endphp class="btn btn-success btn-sm btn-move-next-stage">{{ $confirmBtn }}</button> &nbsp;
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
    assign_case : "{{ $assign_case ? 1 : 0 }}",
    is_app_pull_back : "{{ $isAppPullBack }}"
 };
     $(document).ready(function(){
        var assign_case = $("input[name=assign_case]").val(); 
        var targetModel = assign_case == '1' ? 'assignCaseFrame' : 'sendNextstage';

        if (messages.is_app_pull_back) {
            targetModel = 'pullBackAssignCaseFrame';
        }
        
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
           parent.oTable.draw();
           parent.jQuery("#"+targetModel).modal('hide');  
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
   
   $(document).on("click","#submit",function(){
        var len = $(".approver_list:checked").length;
       if ( messages.assign_case == '0' && len === 0)
       {
          parent.$('.isloader').hide();
          alert('Please select at least one Approver.');
          return false;
       }  
       return true;
    })
    
    </script>
@endsection