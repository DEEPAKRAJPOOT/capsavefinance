@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container mt-4">
      @php
            $route_name = \Request::route()->getName();
            // echo $route_name;
        @endphp
      <!-- 
      <div class="row">
         <div class="col-md-12">
         @can('mail_reviewer_summary')
            <a href="{{route('mail_reviewer_summary', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}"><button type="" class="btn btn-success btn-sm float-right">Send Mail</button></a>                 
         @endcan
         </div>
      </div>
      -->
      <!--Start-->
      @if($is_editable)
      <form method="post" action="{{ route('save_reviewer_summary') }}" id="camReviewerForm">
      @endif
      @csrf
      <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}"> 
      <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}"> 
      <input type="hidden" name="user_id" value="{{ $user_id }}"> 
      <input type="hidden" name="cam_reviewer_summary_id" value="{{isset($reviewerSummaryData->cam_reviewer_summary_id) ? $reviewerSummaryData->cam_reviewer_summary_id : ''}}" />                                                                          
      <div class="card mt-4">
         <div class="card-body ">
            <div class="row">
               <div class="col-md-12">
                     <h4><small>Cover Note</small></h4>
                     <textarea id="cover_note" name="cover_note" class="form-control" cols="10" rows="10">{!! isset($reviewerSummaryData->cover_note) ? $reviewerSummaryData->cover_note : '' !!}</textarea>
               </div>
               @include('backend.cam.deal_structure_offers')
               <div class="col-md-12 mt-4">
               @include('backend.cam.security_deposit_form',['route_name'=>$route_name,'arrAppSecurityDoc'=>$arrAppSecurityDoc,'securityDocumentListJson'=>$securityDocumentListJson])
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Approval criteria for IC</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending">Parameter</th>
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Criteria</th>
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Deviation</th>
                                 <th class="" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Remarks</th>
                           </tr>
                        </thead>
                        <tbody>  
                           <tr role="row" class="odd">
                                 <td class="">
                                    Nominal RV Position
                                 </td>
                                 <td class="">
                                    Max 5% over the values mentionedin the matrix
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_rv_position_yes" class="form-check-input" name="criteria_rv_position" value="Yes" {{((isset($reviewerSummaryData->criteria_rv_position) && $reviewerSummaryData->criteria_rv_position == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_rv_position_no" class="form-check-input" name="criteria_rv_position" value="No"  {{!isset($reviewerSummaryData->criteria_rv_position) || $reviewerSummaryData->criteria_rv_position == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_rv_position_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_rv_position_remark) ? $reviewerSummaryData->criteria_rv_position_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>         
                           <tr role="row" class="odd">
                                 <td class="">
                                    Asset concentration as % of the total portfolio
                                 </td>
                                 <td class="">
                                    - IT assets and telecommunications max 70%<br/>
                                    - Plant and machinery max 50%<br/>
                                    - Furniture and fit outs max 30%<br/>
                                    - Any other asset type max 20%
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_asset_portfolio_yes" class="form-check-input" name="criteria_asset_portfolio" value="Yes" {{((isset($reviewerSummaryData->criteria_asset_portfolio) && $reviewerSummaryData->criteria_asset_portfolio == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_asset_portfolio_no" class="form-check-input" name="criteria_asset_portfolio" value="No"  {{!isset($reviewerSummaryData->criteria_asset_portfolio) || $reviewerSummaryData->criteria_asset_portfolio == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_asset_portfolio_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_asset_portfolio_remark) ? $reviewerSummaryData->criteria_asset_portfolio_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Single Borrower Limit
                                 </td>
                                 <td class="">
                                    Max 15% of Net owned funds @if($borrowerLimitData['single_limit'] !== 0)(Rs {{$borrowerLimitData['single_limit']}} Mn)@endif
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_sing_borr_limit_yes" class="form-check-input" name="criteria_sing_borr_limit" value="Yes" {{((isset($reviewerSummaryData->criteria_sing_borr_limit) && $reviewerSummaryData->criteria_sing_borr_limit == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_sing_borr_limit_no" class="form-check-input" name="criteria_sing_borr_limit" value="No"  {{!isset($reviewerSummaryData->criteria_sing_borr_limit) || $reviewerSummaryData->criteria_sing_borr_limit == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_sing_borr_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_sing_borr_remark) ? $reviewerSummaryData->criteria_sing_borr_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Borrower Group Limit 
                                 </td>
                                 <td class="">
                                    Max 25% of Net owned funds @if($borrowerLimitData['multiple_limit'] !== 0)(Rs {{$borrowerLimitData['multiple_limit']}} Mn)@endif
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_borr_grp_limit_yes" class="form-check-input" name="criteria_borr_grp_limit" value="Yes" {{((isset($reviewerSummaryData->criteria_borr_grp_limit) && $reviewerSummaryData->criteria_borr_grp_limit == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_borr_grp_limit_no" class="form-check-input" name="criteria_borr_grp_limit" value="No"  {{!isset($reviewerSummaryData->criteria_borr_grp_limit) || $reviewerSummaryData->criteria_borr_grp_limit == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_borr_grp_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_borr_grp_remark) ? $reviewerSummaryData->criteria_borr_grp_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Exposure on customers below investment grade <br/>
                                    (BBB -CRISIL/CARE/ICRA/India Ratings) and unrated customers
                                 </td>
                                 <td class="">
                                    Max 50% of CFPL portfolio
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_invest_grade_yes" class="form-check-input" name="criteria_invest_grade" value="Yes" {{((isset($reviewerSummaryData->criteria_invest_grade) && $reviewerSummaryData->criteria_invest_grade == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_invest_grade_no" class="form-check-input" name="criteria_invest_grade" value="No"  {{!isset($reviewerSummaryData->criteria_invest_grade) || $reviewerSummaryData->criteria_invest_grade == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_invest_grade_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_invest_grade_remark) ? $reviewerSummaryData->criteria_invest_grade_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    Exposure to a particular industry/sector as a percentage of total portfolio
                                 </td>
                                 <td class="">
                                    Max 50% of the total CFPL portfolio
                                 </td>
                                 <td class="">
                                    <label for="cibil_check_yes" class="form-check-label">
                                    <input type="radio" id="criteria_particular_portfolio_yes" class="form-check-input" name="criteria_particular_portfolio" value="Yes" {{((isset($reviewerSummaryData->criteria_particular_portfolio) && $reviewerSummaryData->criteria_particular_portfolio == 'Yes')) ? 'checked' : ''}} >Yes
                                    <i class="input-helper"></i></label>
                                    <label for="cibil_check_no" class="form-check-label">
                                    <input type="radio" id="criteria_particular_portfolio_no" class="form-check-input" name="criteria_particular_portfolio" value="No"  {{!isset($reviewerSummaryData->criteria_particular_portfolio) || $reviewerSummaryData->criteria_particular_portfolio == 'No' ? 'checked' : ''}} >No
                                    <i class="input-helper"></i></label>
                                 </td>
                                 <td class="">
                                    <textarea name="criteria_particular_portfolio_remark" class="form-control form-control-sm">{{isset($reviewerSummaryData->criteria_particular_portfolio_remark) ? $reviewerSummaryData->criteria_particular_portfolio_remark : ''}}</textarea>                                 
                                 </td>
                           </tr>                           
                        </tbody>
                  </table>
               </div>                  
               <div class="col-md-12 mt-4">
                     <h4><small>Risk Comments</small></h4>
                     <h5><small>Deal Positives</small></h5>
                     @if(isset($positiveRiskCmntArr) && count($positiveRiskCmntArr)>0)
                        @foreach($positiveRiskCmntArr as $postkey =>$postval)
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="positive_cond[]" value="" class="form-control form-control-sm">{{$postval['cond']}}</textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="positive_timeline[]" value="" class="form-control form-control-sm">{{$postval['timeline']}}</textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa fa-times-circle remove-ptpq-block  remove-positive"></i>
                           </div>
                        </div>
                        @endforeach
                     @endif
                     <div class="after-add-more-positive">
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="positive_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="positive_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa  fa-plus-circle add-ptpq-block add-more-positive"></i>
                           </div>
                        </div>
                     </div>
                     <!-- Copy Fields -->
                     <div class="copy-positive hide">
                        <div class="control-group input-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="positive_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="positive_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                              <i class="fa  fa-times-circle remove-ptpq-block remove-positive"></i>
                           </div>
                        </div>
                     </div>
                     <h5 class="mt-3"><small>Deal Negatives</small></h5>
                     @if(isset($negativeRiskCmntArr) && count($negativeRiskCmntArr)>0)
                        @foreach($negativeRiskCmntArr as $postkey =>$postval)
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="negative_cond[]" value="" class="form-control form-control-sm">{{$postval['cond']}}</textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="negative_timeline[]" value="" class="form-control form-control-sm">{{$postval['timeline']}}</textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa fa-times-circle remove-ptpq-block  remove-negative"></i>
                           </div>
                        </div>
                        @endforeach
                     @endif
                     <div class="after-add-more-negative">
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="negative_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="negative_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa  fa-plus-circle add-ptpq-block add-more-negative"></i>
                           </div>
                        </div>
                     </div>
                     <!-- Copy Fields -->
                     <div class="copy-negative hide">
                        <div class="control-group input-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="negative_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="negative_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                              <i class="fa  fa-times-circle remove-ptpq-block remove-negative"></i>
                           </div>
                        </div>
                     </div>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Recommendation</small></h4>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row">
                                 <td class="">
                                    <textarea  name="recommendation" id="recommendation" class="form-control form-control-sm" cols="3" rows="3">{!! isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : '' !!}</textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-2">
               @if($is_editable)
               @can('save_reviewer_summary')
                  <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
               @endcan
               @endif
               </div>
            </div>
         </div>
      </div>
      @if($is_editable)
      </form>
      @endif
      <!--End-->
   </div>
</div>   
@endsection
@section('jscript')
<script src="{{ asset('common/js/additional-methods.min.js') }}"></script>
<script type="text/javascript">
   appId = '{{$appId}}';
   appurl = '{{URL::route("financeAnalysis") }}';
   process_url = '{{URL::route("process_financial_statement") }}';
   _token = "{{ csrf_token() }}";
</script>

<script type="text/javascript">
      var ckeditorOptions =  {
        filebrowserUploadUrl: "{{route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'file' ])}}",
        filebrowserUploadMethod: 'form',
        imageUploadUrl:"{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'image' ]) }}",
        disallowedContent: 'img{width,height};'
      };
    
   $(document).ready(function(){
      getAllSecurityDocumentName(1);
      $("#cover_note").focus();
   });

    CKEDITOR.replace('cover_note', ckeditorOptions);
    CKEDITOR.replace('recommendation', ckeditorOptions);

   $(document).on('click', '.getAnalysis', function() {
      data = {appId, _token};
      $.ajax({
         url  : appurl,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {
            // body...
         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })

   $(document).on('click', '.process_stmt', function() {
      biz_perfios_id = $(this).attr('pending');
      data = {appId, _token, biz_perfios_id};
      $.ajax({
         url  : process_url,
         type :'POST',
         data : data,
         beforeSend: function() {
           $(".isloader").show();
         },
         dataType : 'json',
         success:function(result) {
            console.log(result);
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            $(".isloader").hide();
            if (result['status']) {
               window.open(result['value']['file_url'], '_blank');
            }
         },
         error:function(error) {

         },
         complete: function() {
            $(".isloader").hide();
         },
      })
   })

$(document).ready(function() {

   $(".add-more").click(function(){ 
      var html = $(".copy").html();
      $(".after-add-more").append(html);
   });


   $("body").on("click",".remove",function(){ 
      $(this).parents(".control-group").remove();
   });

   $(".add-more-post").click(function(){ 
      var html = $(".copy-post").html();
      $(".after-add-more-post").append(html);
   });


   $("body").on("click",".remove-post",function(){ 
      $(this).parents(".control-group").remove();
   });

   // Risk Comments
   $(".add-more-positive").click(function(){ 
      var html = $(".copy-positive").html();
      $(".after-add-more-positive").append(html);
   });


   $("body").on("click",".remove-positive",function(){ 
      $(this).parents(".control-group").remove();
   });

   $(".add-more-negative").click(function(){ 
      var html = $(".copy-negative").html();
      $(".after-add-more-negative").append(html);
   });


   $("body").on("click",".remove-negative",function(){ 
      $(this).parents(".control-group").remove();
   });
   //Start code security document
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

      $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    }, 'File size must be less than {0}');
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

   $('#camReviewerForm').validate(); // initialize the plugin
   //End code security document
});
//Start code security document
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
    '<div class="col-md-5 mt-1">'+
            '<label for="txtPassword"><b>Description</b></label>'+
            '<div class="relative">'+
                '<textarea name="description[]" class="form-control description" placeholder="Description" autocomplete="off" id="description_'+counter+'"></textarea>'+
            '</div>'+
    '</div>'+
    '<div class="col-md-1 mt-1" style="display: flex;flex-direction: column;justify-content: center;align-items: center;padding-top: 2px;">'+
        '<i class="fa fa-2x fa-times-circle remove-security-doc-block ml-2" style="color: red;margin-top: 15%;"></i>'+
    '</div>'+
        '</div>';
    $('#security-doc-block').append(scdocc_block);
    
    makeRequiredFields(counter,'add');
    getAllSecurityDocumentName(counter);
    counter++;
    $('.sc-doc-date').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     startDate: new Date(),
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
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
             jQuery.ajax({
                 url: messages.update_app_security_doc,
                 method: 'post',
                 dataType: 'json',
                 data: dataStore,
                 error: function (xhr, status, errorThrown) {
                                   // alert(errorThrown);
                 },
                 success: function (data) {  
                 }
             });   
    }
        $(this).closest('.toRemoveDiv1').remove();
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
        ids=id.replace(/\d/g, '');
        $(this).attr('id', ids+''+j);
      })
    }
    j++;
  });
}
function makeRequiredFields(counters, reqType){
    $('#camReviewerForm').validate({ignore: ".desexception_received_from"});
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
   //  $('#security-doc-block input.due_date').each(function () {
   //      $(this).rules("add",
   //          {
   //              required: true,
   //              messages: {
   //                  required: "This field is required.",
   //              }
   //          });
   //  });
}
@if(!empty($arrAppSecurityDoc))
        @foreach($arrAppSecurityDoc as $key=>$arr)
        @php 
            $key =  $key+1;    
        @endphp
        makeRequiredFields({{ $key }},'update');
    @endforeach
    @endif
//End code security document
</script>
@endsection