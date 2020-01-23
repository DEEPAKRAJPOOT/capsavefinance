<style>
    .mail-table > tbody > tr > td {
        padding: 8px 10px;
        font-size: 14px;
        border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;
    }
    
    .mail-table > tbody > tr > th {
        background: #8a8989;
        color: #ffffff;
        text-align: left;
        padding: 10px;
        font-size: 14px;
        border-right: #ccc solid 1px;
        border-bottom: #ccc solid 1px;
    }
    
    .mail-table > tbody > tr > td:last-child,
    .mail-table > tbody > tr > th:last-child {
        border-right: none;
    }
</style>
<table width="700" align="center" cellpadding="0" cellspacing="0" border="0" style="font-size:14px; font-family:Arial; ">
    <tr>
        <td align="left">
            <h2 style="font-size:18px; margin:0px 0px 10px;">Cover Note</h2></td>
    </tr>
    <tr>
        <td align="left">{{isset($reviewerSummaryData->cover_note) ? $reviewerSummaryData->cover_note : 'dddddddddddddddddddddddd'}}</td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:20px 0px 10px;">Deal Structure:</h3></td>
    </tr>

    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="50%">Criteria</th>
                    <th width="50%">Particulars</th>
                </tr>
                <tr>
                    <td>Facility Type</td>
                    <td>Lease Loan</td>
                </tr>
                <tr>
                    <td>Limit (₹ In Mn)</td>
                    <td>{{isset($limitOfferData->limit_amt) ? $limitOfferData->limit_amt : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td>Tenor (Months)</td>
                    <td>{{isset($limitOfferData->tenor) ? $limitOfferData->tenor : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td>Equipment Type</td>
                    <td>{{isset($limitOfferData->equipment_type) ? $limitOfferData->equipment_type : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td class="">Security Deposit</td>
                    <td class="">{{isset($limitOfferData->security_deposit) ? $limitOfferData->security_deposit : 'dddddddddddddddddddddddd'}}</td>
                </tr>

                <tr>
                    <td class="">Rental Frequency</td>
                    <td class="">{{isset($limitOfferData->rental_frequency) ? config('common.rental_frequency.'.$limitOfferData->rental_frequency) : 'dddddddddddddddddddddddd'}}</td>
                </tr>
                <tr>
                    <td class="">PTPQ</td>
                    <td class="">{{isset($limitOfferData->ptpq) ? $limitOfferData->ptpq : 'dddddddddddddddddddddddd'}}</td>
                </tr>
                <tr>
                    <td class="" valign="top">XIRR</td>
                    <td class="" valign="top">{{isset($limitOfferData->xirr) ? $limitOfferData->xirr : 'dddddddddddddddddddddddd'}}
                        <!-- Ruby Sheet : 14.69%
                                 <br/>Cash Flow : 13.79% -->
                    </td>
                </tr>
                <tr>
                    <td class="">Additional Security</td>
                    <td class="">{{ isset($limitOfferData->addl_security) ? config('common.addl_security.'.$limitOfferData->addl_security) : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td align="left">
            <h3 style="font-size:16px; margin:20px 0px 10px;">Pre/ Post Disbursement Conditions:</h3></td>
    </tr>
    <tr>
        <td align="left">

            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <th width="50%">Condition</th>
                    <th>Timeline</th>
                </tr>
                <tr>
                    <td>{{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : 'dddddddddddddddddddddddd'}}</td>
                    <td>{{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : 'dddddddddddddddddddddddd'}}</td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : 'dddddddddddddddddddddddd'}}
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td align="left">
            <h2 style="font-size:18px; margin:20px 0px 10px;">Risk Comments:</h2></td>
    </tr>
    <tr>
        <td>
            <h3 style="font-size:16px; margin:0px 0px 10px;">Deal Positives:</h3></td>
    </tr>
    <tr>
        <td align="left">
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td width="50%">
                        <strong> {{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong> {{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>{{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : 'dddddddddddddddddddddddd'}} </strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <h3 style="font-size:16px; margin:20px 0px 10px;">Deal Negatives:</h3></td>
    </tr>
    <tr>
        <td>
            <table width="100%" class="mail-table" border="0" cellpadding="0" cellspacing="0" style="border:#ccc solid 1px;">
                <tr>
                    <td width="50%">
                        <strong>{{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong> {{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : 'dddddddddddddddddddddddd'}} </strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong> {{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : 'dddddddddddddddddddddddd'}}</strong>
                    </td>
                    <td>
                        {{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : 'dddddddddddddddddddddddd'}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <h3 style="font-size:16px; margin:20px 0px 10px;">Recommendation:</h3></td>
    </tr>
    <tr>
        <td> {{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : 'dddddddddddddddddddddddd'}}</td>
    </tr>
</table>


<!--<div class="inner-container">
   
   <div class="card mt-4">
      <div class="card-body ">
         <div class="row">
            
            <div class="col-md-12 mt-4">
                 
                  <table id="invoice_history" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                    
                     <tbody>
                        <tr role="row" class="odd">
                             
                        </tr>
                        <tr role="row" class="odd">
                              
                        </tr>
                        <tr role="row" class="odd">
                              
                        </tr>
                        <tr role="row" class="odd">
                              
                        </tr>
                        <tr role="row" class="odd">
                              
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">Rental Frequency</td>
                              <td class="">{{isset($limitOfferData->rental_frequency) ? config('common.rental_frequency.'.$limitOfferData->rental_frequency) : 'dddddddddddddddddddddddd'}}</td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">PTPQ</td>
                              <td class="">{{isset($limitOfferData->ptpq) ? $limitOfferData->ptpq : 'dddddddddddddddddddddddd'}}</td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" valign="top">XIRR</td>
                              <td class="" valign="top">{{isset($limitOfferData->xirr) ? $limitOfferData->xirr : 'dddddddddddddddddddddddd'}}
                                
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">Additional Security</td>
                              <td class="">{{ isset($limitOfferData->addl_security) ? config('common.addl_security.'.$limitOfferData->addl_security) : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                     </tbody>
                  </table>
            </div>
            <div class="col-md-12 mt-4">
                  
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
                                 {{isset($reviewerSummaryData->cond_nach) ? $reviewerSummaryData->cond_nach : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_nach) ? $reviewerSummaryData->time_nach : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_insp_asset) ? $reviewerSummaryData->cond_insp_asset : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_insp_asset) ? $reviewerSummaryData->time_insp_asset : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_insu_pol_cfpl) ? $reviewerSummaryData->cond_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_insu_pol_cfpl) ? $reviewerSummaryData->time_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_personal_guarantee) ? $reviewerSummaryData->cond_personal_guarantee : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_personal_guarantee) ? $reviewerSummaryData->cond_insu_pol_cfpl : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_pbdit) ? $reviewerSummaryData->cond_pbdit : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_pbdit) ? $reviewerSummaryData->time_pbdit : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_dscr) ? $reviewerSummaryData->cond_dscr : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_dscr) ? $reviewerSummaryData->time_dscr : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_lender_cfpl) ? $reviewerSummaryData->cond_lender_cfpl : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_lender_cfpl) ? $reviewerSummaryData->time_lender_cfpl : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" valign="top">
                                 {{isset($reviewerSummaryData->cond_ebidta) ? $reviewerSummaryData->cond_ebidta : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_ebidta) ? $reviewerSummaryData->time_ebidta : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="">
                                 {{isset($reviewerSummaryData->cond_credit_rating) ? $reviewerSummaryData->cond_credit_rating : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->time_credit_rating) ? $reviewerSummaryData->time_credit_rating : 'dddddddddddddddddddddddd'}}
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
                                 {{isset($reviewerSummaryData->cond_pos_track_rec) ? $reviewerSummaryData->cond_pos_track_rec : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_pos_track_rec) ? $reviewerSummaryData->cmnt_pos_track_rec : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" width="30%">
                                 {{isset($reviewerSummaryData->cond_pos_credit_rating) ? $reviewerSummaryData->cond_pos_credit_rating : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_pos_credit_rating) ? $reviewerSummaryData->cmnt_pos_credit_rating : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" width="30%">
                                 {{isset($reviewerSummaryData->cond_pos_fin_matric) ? $reviewerSummaryData->cond_pos_fin_matric : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_pos_fin_matric) ? $reviewerSummaryData->cmnt_pos_fin_matric : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" width="30%">
                                 {{isset($reviewerSummaryData->cond_pos_establish_client) ? $reviewerSummaryData->cond_pos_establish_client : 'dddddddddddddddddddddddd'}} 
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_pos_establish_client) ? $reviewerSummaryData->cmnt_pos_establish_client : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                     </tbody>
                  </table>
                  <h5 class="mt-3"><small>Deal Negatives:</small></h5>
                  <table id="" class="table table-striped dataTable no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr role="row" class="odd">
                              <td class="" width="30%">
                                 {{isset($reviewerSummaryData->cond_neg_competition) ? $reviewerSummaryData->cond_neg_competition : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_neg_competition) ? $reviewerSummaryData->cmnt_neg_competition : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" width="30%">
                                 {{isset($reviewerSummaryData->cond_neg_forex_risk) ? $reviewerSummaryData->cond_neg_forex_risk : 'dddddddddddddddddddddddd'}} 
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_neg_forex_risk) ? $reviewerSummaryData->cmnt_neg_forex_risk : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                        <tr role="row" class="odd">
                              <td class="" width="30%">
                                 {{isset($reviewerSummaryData->cond_neg_pbdit) ? $reviewerSummaryData->cond_neg_pbdit : 'dddddddddddddddddddddddd'}}
                              </td>
                              <td class="">
                                 {{isset($reviewerSummaryData->cmnt_neg_pbdit) ? $reviewerSummaryData->cmnt_neg_pbdit : 'dddddddddddddddddddddddd'}}
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
                                 {{isset($reviewerSummaryData->recommendation) ? $reviewerSummaryData->recommendation : 'dddddddddddddddddddddddd'}}
                              </td>
                        </tr>
                     </tbody>
                  </table>
            </div>
         </div>
      </div>
   </div>-->
   <!--End-->
</div>
