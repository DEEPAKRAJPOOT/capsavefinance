@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container mt-4">
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
      <form method="post" action="{{ route('save_reviewer_summary') }}">
      @endif
      @csrf
      <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}"> 
      <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}"> 
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
                     <h4><small>Pre Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="50%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>                   
                     </table>
                     @if(isset($preCondArr) && count($preCondArr)>0)
                        @foreach($preCondArr as $prekey =>$preval)
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_cond[]" value="" class="form-control form-control-sm">{{$preval['cond']}}</textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_timeline[]" value="" class="form-control form-control-sm">{{$preval['timeline']}}</textarea>
                           </div>
                           <div class="input-group-btn "> 
                            <i class="fa  fa-times-circle remove-ptpq-block remove"></i>
                           </div>
                        </div>
                        @endforeach
                     @endif
                     <div class="after-add-more">
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa  fa-plus-circle add-ptpq-block add-more"></i>
                           </div>
                        </div>
                     </div>
                     <!-- Copy Fields -->
                     <div class="copy hide">
                        <div class="control-group input-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="pre_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                              <i class="fa  fa-times-circle remove-ptpq-block remove"></i>
                           </div>
                        </div>
                     </div>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Post Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="50%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>
                     </table>
                     @if(isset($postCondArr) && count($postCondArr)>0)
                        @foreach($postCondArr as $postkey =>$postval)
                        <div class="input-group control-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_cond[]" value="" class="form-control form-control-sm">{{$postval['cond']}}</textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_timeline[]" value="" class="form-control form-control-sm">{{$postval['timeline']}}</textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa fa-times-circle remove-ptpq-block  remove-post"></i>
                           </div>
                        </div>
                        @endforeach
                     @endif
                     <div class="after-add-more-post">
                        <div class="input-group control-group  row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                           <i class="fa  fa-plus-circle add-ptpq-block add-more-post"></i>
                           </div>
                        </div>
                     </div>
                     <!-- Copy Fields -->
                     <div class="copy-post hide">
                        <div class="control-group input-group row">
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_cond[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn col-md-6"> 
                              <textarea name="post_timeline[]" value="" class="form-control form-control-sm"></textarea>
                           </div>
                           <div class="input-group-btn "> 
                             <i class="fa  fa-times-circle remove-ptpq-block remove-post"></i>
                           </div>
                        </div>
                     </div>
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
                                    Max 15% of Net owned funds (Rs{{$borrowerLimitData['single_limit']}} Mn)
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
                                    Max 25% of Net owned funds (Rs{{$borrowerLimitData['multiple_limit']}} Mn)
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
});
</script>
@endsection