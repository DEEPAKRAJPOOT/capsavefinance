@extends('layouts.backend.admin-layout')

@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        @php
            $route_name = \Request::route()->getName();
            // echo $route_name;
        @endphp
        <div class="card mt-4">
            <div class="card-body ">
            @if(count($securityListingDataApproved) > 0)
             <form method="POST" id="camForm" enctype="multipart/form-data" action="{{route('save_security_deposit')}}"> 
             @csrf
                <input type="hidden" name="app_id" value="{{isset($arrRequest['app_id']) ? $arrRequest['app_id'] : ''}}" />             
                <input type="hidden" name="biz_id" value="{{isset($arrRequest['biz_id']) ? $arrRequest['biz_id'] : ''}}" />             
                <input type="hidden" name="cam_reviewer_summary_id" value="{{isset($reviewerSummaryData->cam_reviewer_summary_id) ? $reviewerSummaryData->cam_reviewer_summary_id : ''}}" />
                @include('backend.cam.security_deposit_form',['route_name'=>$route_name,'arrAppSecurityDoc'=>$arrAppSecurityDoc,'securityDocumentListJson'=>$securityDocumentListJson])
                @if(request()->get('view_only'))
                @can('save_security_deposit')
                    <button class="btn btn-success pull-right  mt-3" type="Submit"> Save</button>
                @endcan
                @endif
              </form>
              
              @else
              <div class="card card-color mb-0">
                <div class="card-header">
                   <a class="card-title ">Offers are not Accepted yet.</a>
                </div>
             </div>
              @endif
              
            </div>
            @if (count($securityListingDataApproved) > 0)
            <div class="card mt-4">
                @include('backend.cam.security_view',['securityListingData' => $securityListingDataApproved,'userId' => $userId, 'title'=>'Approved'])
              </div>
              @endif
              @if (count($securityListingDataSanctioned) > 0)
              <div class="card mt-4">
                @include('backend.cam.security_view',['securityListingData' => $securityListingDataSanctioned,'userId' => $userId,'title'=>'Sanctioned'])
              </div>
              @endif
        </div>
    </div>
</div>

@endsection
@section('jscript')
<script src="{{ asset('common/js/additional-methods.min.js') }}"></script>
<script type="text/javascript">
    $('#camForm').validate({ignore: ".desexception_received_from"});
    makeRequiredFields(1,'add');
    getAllSecurityDocumentName(1);
    getAllOfferList(1);   
    $(document).on('keypress','.float_format', function(event) {
    let num = $(this).val();
    num.split('.')[1]
        if(event.which == 8 || event.which == 0){
            return true;
        }
        if(event.which < 46 || event.which > 59) {
            return false;
        }
    
        if(event.which == 46 && $(this).val().indexOf('.') != -1) {
            return false;
        }
    if(typeof num.split('.')[1] !== 'undefined' && num.split('.')[1].length > 1){
    return false;
    }
    });

$(document).ready(function () {
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');
    // $.validator.addMethod("notOnlyZero", function (value, element, param) {
    //     return this.optional(element) || parseInt(value) == 0;
    // });

    $.validator.addMethod('checkRenewalDate', function (value, element, param) {
        let cur_date_val = $((element.id.split('_')[0]=='update'?'#update_':'#')+'maturity_date_'+element.id.split('_')[element.id.split('_').length-1]).val().split("/");

        let cur_date = new Date(+cur_date_val[2], cur_date_val[1] - 1, +cur_date_val[0]);
        cur_date.setDate(cur_date.getDate()-value+1);
        
        if(cur_date.toISOString().slice(0, 10)>=(new Date().toISOString().slice(0, 10)))
            return true;
        else
            return false;
    }, 'Renewal Reminder Days must be lower');

    jQuery.validator.addMethod("alphanumeric", function(value, element) {
        if(value != ''){
          if(value == 0){
            return false; 
          }
        }
        return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
   }); 
    var messages = {
        unique_security_doc_number: "{{ URL::route('check_unique_security_doc_number') }}",
        token: "{{ csrf_token() }}",
    };
    $.validator.addMethod("checkDocumentNumber",
        function(value, element, params) {
            var result = true;
            var data = {doc_number : value, _token: messages.token};
            var app_security_doc_id = $(element).closest('.toRemoveDiv1').find('.app_security_doc_id').val();
            if(app_security_doc_id > 0 && app_security_doc_id != 'undefined'){
                data['id'] = app_security_doc_id;
            }
                data['app_id'] = $("input[name='app_id']").val();
            $.ajax({
                type:"POST",
                async: false,
                url: messages.unique_security_doc_number, // script to validate in server side
                data: data,
                success: function(data) {                        
                    result = (data.status == 1) ? false : true;
                }
            }); 
            return result;                
        },'Document Number is already exists'
    );
// $('#camForm').validate();// initialize the plugin
});
$(document).on('submit', '#camForm', function(e) {
    var valid = true;
    // $.each($('input[name="document_number[]"]'), function (index1, item1) {

    //     $.each($('input[name="document_number[]"]').not(this), function (index2, item2) {
    //         $(item1).removeAttr("style");
    //         if ($(item1).val() == $(item2).val()) {
    //             $(item1).css("border-color", "red");
    //             valid = true;
    //         }

    //     });
    // });
    $('#security-doc-block select.doc_type').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block select.security_doc_id').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block textarea.description').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.document_number').each(function () {
        $(this).rules("add",
            {
                required: true,
                //notOnlyZero: '0',
                alphanumeric: true,
                // checkDocumentNumber: true,
                messages: {
                    required: "This field is required.",
                    //notOnlyZero:"Document Number can not be zero.",
                    alphanumeric:"Please enter letters and numbers only.",
                }
            });
    });
    // $('#security-doc-block input.due_date').each(function () {
    //     $(this).rules("add",
    //         {
    //             required: true,
    //             messages: {
    //                 required: "This field is required.",
    //             }
    //         });
    // });
    $('#security-doc-block select.completed').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block select.exception_received').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });

    $('#security-doc-block input.maturity_date').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.renewal_reminder_days').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 0,
                max: 365,
                digits:true,
                checkRenewalDate : $(this).attr('id'),
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.amount_expected').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.document_amount').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.doc_file_sec').each(function () {
        $(this).rules("add",
            {
                // required: true,
                extension: "jpg,png,pdf,doc,docx",
                filesize : 200000000,
                messages: {
                    required: "This field is required.",
                    extension:"Only support jpg,png,pdf,doc,docx type format.",
                    filesize:"maximum size for upload 20 MB.",
                }
            });
    });
    $('#security-doc-block select.prgm_offer_id').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    if (!$('#camForm').valid()) {
        var valid = false;
    }
   return valid;
});

var counter;
$(document).on('click', '.add-security-doc-block', function(){
    counter = $('#security-doc-block .toRemoveDiv1').length + 1;
    @if(empty($arrAppSecurityDoc)) 
        counter = $('#security-doc-block .toRemoveDiv1').length + 1;
    @endif
    $('.toRemoveDiv1').each(function() {
        $(this).find('div').removeClass('exceptionFields_'+counter);
    });
    let scdocc_block = '<div class="row p-2 mt-1 toRemoveDiv1" style="background-color: #e9e7e7;">'+
        '<div class="col-md-2 mt-1">'+
        '<label for="txtPassword"><b>Pre/Post Disbursement</b></label>'+
        '<div class="relative">'+
            '<select class="form-control doc_type" name="doc_type[]" id="doc_type_'+counter+'">'+
                '<option value="">Select</option>'+
                '<option value="1">Pre Disbursement</option>'+
                '<option value="2">Post Disbursement</option>'+
            '</select>'+
       '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1">'+
            '<label for="txtPassword"><b>Type of Document</b></label>'+
            '<select class="form-control security_doc_id" name="security_doc_id[]" id="security_doc_id_'+counter+'">'+
            '<option value="">Select</option>'+
            '</select>'+
    '</div>'+
    '<div class="col-md-2 mt-1">'+
            '<label for="txtPassword"><b>Original Due Date</b></label>'+
            '<div class="relative">'+
                    '<input type="text" name="due_date[]" maxlength="20" class="form-control sc-doc-date due_date" value="" placeholder="Original Due Date" autocomplete="off" id="due_date_'+counter+'" readonly="readonly"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-4 mt-1">'+
            '<label for="txtPassword"><b>Description</b></label>'+
            '<div class="relative">'+
                '<textarea name="description[]" class="form-control description" placeholder="Description" autocomplete="off" id="description_'+counter+'"></textarea>'+
            '</div>'+
    '</div>'+
    @if($route_name=="security_deposit")
    '<div class="col-md-2 mt-1">'+
            '<label for="txtPassword"><b>Document Number</b></label>'+
            '<div class="relative">'+
            '<input type="text" name="document_number[]" class="form-control document_number" value="" placeholder="Document Number" autocomplete="off" id="document_number_'+counter+'"/>'+
            '</div>'+
    '</div>'+
    @endif
    @if($route_name=="security_deposit")
    '<div class="col-md-2 mt-1">'+
        '<label for="txtPassword"><b>Completed</b></label>'+
        '<div class="relative">'+
            '<select class="form-control completed" name="completed[]" id="completed_'+counter+'">'+
                '<option value="">Select</option>'+
                '<option value="yes">Yes</option>'+
                '<option value="no">No</option>'+
            '</select>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1">'+
            '<label for="txtPassword"><b>Exception Received</b></label>'+
            '<div class="relative">'+
                '<select class="form-control exception_received" name="exception_received[]" onchange="displayExceptionFields(this.value,'+counter+');" id="exception_received_'+counter+'" data-previous="'+counter+'">'+
                    '<option value="">Select</option>'+
                    '<option value="yes">Yes</option>'+
                    '<option value="no">No</option>'+
                '</select>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1 exceptionFields_'+counter+'" style="display: none;">'+
            '<label for="txtPassword"><b>Exception Received From</b></label>'+
            '<div class="relative">'+
            '<input type="text" name="exception_received_from[]" class="form-control exception_received_from required" value="" placeholder="Exception Received From" autocomplete="off" id="exception_received_from_'+counter+'" style="visibility: hidden;height: 0;"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1 exceptionFields_'+counter+'" style="display: none;">'+
            '<label for="txtPassword"><b>Exception Received Date</b></label>'+
            '<div class="relative">'+
            '<input type="text" name="exception_received_date[]" class="form-control sc-doc-date-r exception_received_date required" value="" placeholder="Exception Received Date" autocomplete="off" id="exception_received_date_'+counter+'" style="visibility: hidden;height: 0;" readonly="readonly"/>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1 exceptionFields_'+counter+'" style="display: none;">'+
        '<label for="txtPassword"><b>Exception Remark</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="exception_remark[]" class="form-control exception_remark required" value="" placeholder="Exception Remark" autocomplete="off" id="exception_remark_'+counter+'" style="visibility: hidden;height: 0;"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1 exceptionFields_'+counter+'" style="display: none;">'+
        '<label for="txtPassword"><b>Extended Due Date</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="extended_due_date[]" class="form-control extended_due_date sc-doc-date required" value="" placeholder="Extended Due Date" autocomplete="off" id="extended_due_date_'+counter+'" style="visibility: hidden;height: 0;" readonly="readonly"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1">'+
        '<label for="txtPassword"><b>Maturity Date</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="maturity_date[]" class="form-control sc-doc-date maturity_date" value="" placeholder="Maturity Date" autocomplete="off" id="maturity_date_'+counter+'" readonly="readonly"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1">'+
        '<label for="txtPassword"><b>Renewal Reminder Days</b></label>'+
        '<div class="relative">'+
        '<input type="text" name="renewal_reminder_days[]" class="form-control digits renewal_reminder_days" value="" placeholder="Renewal Reminder Days" autocomplete="off" id="renewal_reminder_days_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1 INR">'+
        '<label for="txtPassword"><b>Amount Expected</b></label>'+
        '<div class="relative">'+
        '<a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>'+
        '<input type="text" name="amount_expected[]" class="form-control number float_format amount_expected" value="" placeholder="Amount Expected" autocomplete="off" id="amount_expected_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1 INR">'+
       '<label for="txtPassword"><b>Document Amount</b></label>'+
        '<div class="relative">'+
        '<a href="javascript:void(0);" class="verify-owner-no" ><i class="fa fa-inr" aria-hidden="true"></i></a>'+
        '<input type="text" name="document_amount[]" class="form-control number float_format document_amount" value="" placeholder="Document Amount" autocomplete="off" id="document_amount_'+counter+'"/>'+
        '</div>'+
    '</div>'+
    '<div class="col-md-2 mt-1">'+
    '<label for="txtPassword"><b>Offer ID</b></label>'+
    '<select class="form-control prgm_offer_id" name="prgm_offer_id[]" id="prgm_offer_id_'+counter+'">'+
       '<option value="">Select</option>'+
        '</select>'+
    '</div>'+
    '<div class="col-md-3 mt-1">'+
        '<label for="txtPassword"><b>Doc Upload</b></label>'+
        '<div class="relative">'+
            '<div class="custom-file upload-btn-cls mb-3">'+
                '<input type="file" class="custom-file-input getFileName doc_file_sec required" id="doc_file_'+counter+'" name="doc_file_sec[]">'+
                '<label class="custom-file-label" for="customFile">Choose file</label>'+
            '</div>'+
        '</div>'+
    '</div>'+
    @endif
    '<div class="col-md-1 mt-1" style="display: flex;flex-direction: column;justify-content: center;align-items: center;padding-top: 15px;">'+
        '<i class="fa fa-2x fa-times-circle remove-security-doc-block ml-2" style="color: red;"></i>'+
    '</div>'+
        '</div>';
    $('#security-doc-block').append(scdocc_block);
    
    makeRequiredFields(counter,'add');
    getAllSecurityDocumentName(counter);
    getAllOfferList(counter);
    counter++;
    $('.sc-doc-date').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     startDate: new Date(),
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
       if($(this).valid()){
          $(this).removeClass('invalid').addClass('success');   
        }
   });
   $('.sc-doc-date-r').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
       if($(this).valid()){
          $(this).removeClass('invalid').addClass('success');   
        }
   });
  });

  $(document).on('click', '.remove-security-doc-block', function(){
    var app_security_doc_id = $(this).closest('.toRemoveDiv1').find('.app_security_doc_id').val();

    if(app_security_doc_id > 0 && app_security_doc_id != 'undefined'){
            var messages = {
                update_app_security_doc: "{{ URL::route('update_app_security_doc') }}",
                  token: "{{ csrf_token() }}",
             };

             var dataStore = {'app_security_doc_id': app_security_doc_id,'_token': messages.token };
             var element = this;
             jQuery.ajax({
                 url: messages.update_app_security_doc,
                 method: 'post',
                 dataType: 'json',
                 data: dataStore,
                 error: function (xhr, status, errorThrown) {
                                   // alert(errorThrown);
                 },
                 success: function (data) { 
                     if(!data){
                         alert('Approved or Sanctioned data can not delete');
                     }else{
                        $(element).closest('.toRemoveDiv1').remove();

                     }
                 }
             });   
        }else{
        $(this).closest('.toRemoveDiv1').remove();
    }
        resetIndexes();
  });
  $(document).on('change','.getFileName',function(){
        $(this).parent('div').children('.custom-file-label').html('Choose file');
    });
    
    $(document).on('change','.getFileName',function(e) {
        var fileName = e.target.files[0].name;
        $(this).parent('div').children('.custom-file-label').html(fileName);
    });
    $('.sc-doc-date').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     startDate: new Date(),
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
       if($(this).valid()){
          $(this).removeClass('invalid').addClass('success');   
        }
   });
   $('.sc-doc-date-r').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
       if($(this).valid()){
          $(this).removeClass('invalid').addClass('success');   
        }
   });
function getAllSecurityDocumentName(selectId){
  var securityDoc= {!! $securityDocumentListJson !!};
  if(securityDoc){
    $.each(securityDoc, function(i, item) {
        $('#security_doc_id_'+selectId)
          .append($('<option>', { value : item.security_doc_id })
          .text(item.name));
    });
  }
}
function displayExceptionFields(exceptionVal,divId){
    $(".exceptionFields_"+divId).css("display", "none");
    $(".exceptionFields_"+divId).find('input').css("visibility", "hidden").removeClass('required');
    if(exceptionVal == 'yes'){
      $(".exceptionFields_"+divId).removeAttr("style");
      $(".exceptionFields_"+divId).find('input').removeAttr("style").addClass('required');
    }
    if(exceptionVal == 'no'){
        $(".exceptionFields_"+divId).find('input').val('');
    }
    makeRequiredFields(divId,'update');
}

function resetIndexes() {
  var j = 1, id,name, $this;
  // for each element on the page with the class .input-wrap
  var previousId = [];  
  $('#security-doc-block .toRemoveDiv1').each(function() {
    if (j > 1) {
      // within each matched .input-wrap element, find each <input> element
      $(this).find('input, select').each(function() {
        $this = $(this);
        id = $this.attr("id");
        name = $this.attr("name");
        if(name == 'exception_received[]'){
            $this.removeAttr("onchange");
            $this.attr("onchange",'displayExceptionFields(this.value,'+j+');');
        }
        ids=id.replace(/\d/g, '');
        $(this).attr('id', ids+''+j);
            if(name == 'exception_received_from[]' || name == 'exception_received_date[]' || name == 'exception_remark[]' || name == 'extended_due_date[]'){
                previousIds = $this.attr("data-previous");
                $(this).parent().parent().removeClass('exceptionFields_'+previousIds).addClass('exceptionFields_'+j);
        }
      })
    }
    j++;
  });
}
function makeRequiredFields(counters, reqType){
    $('#camForm').validate({ignore: ".desexception_received_from"});
    $('#security-doc-block select.doc_type').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block select.security_doc_id').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block textarea.description').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.document_number').each(function () {
        $(this).rules("add",
            {
                required: true,
                //notOnlyZero: '0',
                alphanumeric: true,
                // checkDocumentNumber: true,
                messages: {
                    required: "This field is required.",
                    //notOnlyZero:"Document Number can not be zero.",
                    alphanumeric:"Please enter letters and numbers only.",
                }
            });
    });
    // $('#security-doc-block input.due_date').each(function () {
    //     $(this).rules("add",
    //         {
    //             required: true,
    //             messages: {
    //                 required: "This field is required.",
    //             }
    //         });
    // });
    $('#security-doc-block select.completed').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block select.exception_received').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    
    $('#security-doc-block input.maturity_date').each(function () {
        $(this).rules("add",
            {
                required: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.renewal_reminder_days').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 0,
                max: 365,
                digits:true,
                checkRenewalDate : $(this).attr('id'),
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.amount_expected').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.document_amount').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    $('#security-doc-block input.doc_file_sec').each(function () {
        $(this).rules("add",
            {
                // required: true,
                extension: "jpg,png,pdf,doc,docx",
                filesize : 200000000,
                messages: {
                    required: "This field is required.",
                    extension:"Only support jpg,png,pdf,doc,docx type format.",
                    filesize:"maximum size for upload 20 MB.",
                }
            });
    });
    $('#security-doc-block select.prgm_offer_id').each(function () {
        $(this).rules("add",
            {
                required: true,
                min: 1,
                number: true,
                messages: {
                    required: "This field is required.",
                }
            });
    });
    
}
function getAllOfferList(selectId){
  var offerList= {!! $offerListJson !!};
  if(offerList){
    $.each(offerList, function(i, item) {
        prgmAmtLimit = item.prgm_limit_amt;
        prgmAmtLimit = new Intl.NumberFormat('hi-IN', {currency: 'INR' }).format(prgmAmtLimit);
        $('#prgm_offer_id_'+selectId)
          .append($('<option>', { value : item.prgm_offer_id })
          .html(item.prgm_offer_id+' (Amount: &#8377;'+prgmAmtLimit+')'));
    });
  }
}
@if(!empty($arrAppSecurityDoc))
        @foreach($arrAppSecurityDoc as $key=>$arr)
        @php 
            $key =  $key+1;    
        @endphp
        makeRequiredFields({{ $key }},'update');
    @endforeach
    @endif
</script>
@endsection