@extends('layouts.backend.admin_popup_layout')

@section('content')

       <div class="modal-body text-left">
           <form id="consentForm" name="consentForm" method="POST" action="{{route('save_manual_consent')}}" enctype="multipart/form-data">
		@csrf
             <div>
                 <label for="email">Upload Document<span class="mandatory">*</span></label>
             </div> 
            <div class="custom-file mb-3 mt-2">
               <label for="email">Upload Document</label>
               <input type="file" class="custom-file-input getFileName consent_upload" id="consent_upload" name="consent_upload">
               <label class="custom-file-label val_print" for="consent_upload">Choose file</label>
               {!! $errors->first('consent_upload', '<span class="error">:message</span>') !!}
            </div>

            <div class="custom-file mb-3 mt-2 form-group">
               <label for="email">Comment<span class="mandatory">*</span></label>
               <textarea rows="1" name="comment" class="form-control"></textarea>
               {!! $errors->first('comment', '<span class="error">:message</span>') !!}
            </div>
            <input type="hidden" name="user_id" value="{{$requestData['user_id']}}">
            <input type="hidden" name="biz_id" value="{{$requestData['biz_id']}}">
            <input type="hidden" name="userUcicId" value="{{$requestData['userUcicId']}}">
            @if(isset($requestData['ckyc_consent_id']) && !empty($requestData['ckyc_consent_id']))
            <input type="hidden" name="ckyc_consent_id" value="{{$requestData['ckyc_consent_id']}}">
            @endif
            @if(isset($requestData['biz_owner_id']) && !empty($requestData['biz_owner_id']))
            <input type="hidden" name="biz_owner_id" value="{{$requestData['biz_owner_id']}}">
            @endif
                
                           
                <br> <br>
                <button type="submit" class="btn btn-success btn-sm float-right" id="saveCFrm">Submit</button>  
           </form>
         </div>
     



@endsection

@section('jscript')

<script src="{{ asset('common/js/jquery.validate.js') }}"></script>
<script src="{{ asset('backend/js/ajax-js/lead.js') }}" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script>

    var messages = {
        //get_lead: "{{ URL::route('get_lead') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        is_accept: "{{ Session::get('is_accept') }}",
        message : "{{ trans('backend_messages.user_manual_consent') }}",
        error_msg : "{{ Session::get('error_msg') }}"
    };
</script>
<script type="text/javascript">
        $(document).ready(function () {
              $('#consentForm').validate({ // initialize the plugin
                rules: {
                consent_upload: {
                required: true,
                extension: "png|jpg|jpeg|pdf"
                },
                comment: {
                required: true,
                maxlength: 100
                }
                },
                messages: {
                consent_upload: {
                required: "Please select file.",
                extension:"Please select only png, jpg, jpeg,pdf format.",
                },
                comment:{
                    required : "Please enter comment.",
                    maxlength: "You can provide max. of 100 characters."
                }
                },
                onblur: function(element) {
                this.element(element);
                },
                onkeyup: function(element) {
                    this.element(element);
                },
                onfocusin: function(element) {
                    $(element).valid();
                }
                });

            $("#consentForm").submit(function(){
                if($(this).valid()){
                    $("#saveCFrm").attr("disabled","disabled");
                }
            });
   
    if (messages.is_accept == 1){
        var parent =  window.parent;
        window.parent.jQuery('#iframeMessage').show();
        window.parent.jQuery('#iframeMessage').html('<div class=" alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+messages.message+'</div>');
        parent.jQuery("#modalManualConsent").modal('hide');
        parent.jQuery("#consent_button").hide();
        parent.jQuery("#pull_button").show();
        setTimeout(function(){
            parent.location.reload();
        }, 1000);
         
    }
    
    if (messages.error_msg != '') {
        window.jQuery('#iframeMessage').show();
        window.jQuery('#iframeMessage').html('<div class=" alert-danger alert" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+messages.error_msg+'</div>');
    }
        
 });

$(document).on('change', '.getFileName', function(){
        $(this).parent('div').children('.custom-file-label').html('Choose file');
})

$(document).on('change', '.getFileName', function(e){
    if (e.target.files.length) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    }
});
</script>
@endsection