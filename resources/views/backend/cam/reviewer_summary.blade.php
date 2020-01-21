@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
      <!--Start-->
      <form method="post" action="{{ route('save_reviewer_summary') }}">
      @csrf
      <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}"> 
      <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}"> 
      <input type="hidden" name="cam_reviewer_summary_id" value="{{isset($reviewerSummaryData->cam_reviewer_summary_id) ? $reviewerSummaryData->cam_reviewer_summary_id : ''}}" />                                                                          
      <div class="card mt-4">
         <div class="card-body ">
            <div class="row">
               <div class="col-md-12">
                     <h4><small>Cover Note</small></h4>
                     <textarea id="cover_note" name="cover_note" class="form-control" cols="10" rows="10">
                        {{isset($reviewerSummaryData->cover_note) ? $reviewerSummaryData->cover_note : ''}}
                     </textarea>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Deal Structure:</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Particulars</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="">Facility Type</td>
                                 <td class="">Lease Loan</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Limit (₹ In Mn)</td>
                                 <td class="">{{isset($limitOfferData->limit_amt) ? $limitOfferData->limit_amt : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Tenor (Months)</td>
                                 <td class="">{{isset($limitOfferData->tenor) ? $limitOfferData->tenor : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Equipment Type</td>
                                 <td class="">{{isset($limitOfferData->equipment_type) ? $limitOfferData->equipment_type : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Security Deposit</td>
                                 <td class="">{{isset($limitOfferData->security_deposit) ? $limitOfferData->security_deposit : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Rental Frequency</td>
                                 <td class="">{{isset($limitOfferData->rental_frequency) ? config('common.rental_frequency.'.$limitOfferData->rental_frequency) : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">PTPQ</td>
                                 <td class="">{{isset($limitOfferData->ptpq) ? $limitOfferData->ptpq : ''}}</td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" valign="top">XIRR</td>
                                 <td class="" valign="top">{{isset($limitOfferData->xirr) ? $limitOfferData->xirr : ''}}
                                    <!-- Ruby Sheet : 14.69%
                                    <br/>Cash Flow : 13.79% -->
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">Additional Security</td>
                                 <td class="">{{ isset($limitOfferData->addl_security) ? config('common.addl_security.'.$limitOfferData->addl_security) : ''}}
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Pre/ Post Disbursement Conditions:</small></h4>
                     <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <thead>
                           <tr role="row">
                                 <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                                 <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_nach" value="{{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text" name="time_nach" value="{{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_insp_asset" value="{{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text" name="time_insp_asset" value="{{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_insu_pol_cfpl" value="{{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text" name="time_insu_pol_cfpl" value="{{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text"  name="cond_personal_guarantee" value="{{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_personal_guarantee" value="{{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text"  name="cond_pbdit" value="{{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_pbdit" value="{{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_dscr" value="{{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : ''}}"  class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_dscr" value="{{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_lender_cfpl" value="{{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_lender_cfpl" value="{{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" valign="top">
                                    <input type="text" name="cond_ebidta" value="{{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text"  name="time_ebidta" value="{{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="">
                                    <input type="text" name="cond_credit_rating" value="{{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <input type="text" name="time_credit_rating" value="{{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : ''}}" class="form-control form-control-sm">
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Risk Comments:</small></h4>
                     <h5><small>Deal Positives:</small></h5>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_track_rec" value="{{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <textarea name="cmnt_pos_track_rec" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_credit_rating" value="{{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_pos_credit_rating" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_fin_matric" value="{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : ''}}" class="form-control form-control-sm">
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_pos_fin_matric" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_pos_establish_client" value="{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_pos_establish_client" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
                     <h5 class="mt-3"><small>Deal Negatives:</small></h5>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_neg_competition" value="{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea name="cmnt_neg_competition" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_neg_forex_risk" value="{{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_neg_forex_risk" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                           <tr role="row" class="odd">
                                 <td class="" width="30%">
                                    <input type="text" name="cond_neg_pbdit" value="{{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : ''}}" class="form-control form-control-sm">  
                                 </td>
                                 <td class="">
                                    <textarea  name="cmnt_neg_pbdit" class="form-control form-control-sm">
                                    {{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-4">
                     <h4><small>Recommendation:</small></h4>
                     <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" cellpadding="0" cellspacing="0">
                        <tbody>
                           <tr role="row">
                                 <td class="">
                                    <textarea  name="recommendation" class="form-control form-control-sm" cols="3" rows="3">
                                    {{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : ''}}
                                    </textarea>
                                 </td>
                           </tr>
                        </tbody>
                     </table>
               </div>
               <div class="col-md-12 mt-2">
                     <button type="submit" class="btn btn-success btn-sm float-right">Submit</button>
               </div>
            </div>
         </div>
      </div>
      </form>
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
   $(document).ready(function(){
      $("#cover_note").focus();
   });
   
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
</script>
@endsection