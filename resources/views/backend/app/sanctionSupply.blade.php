<style>
  h5{ 
    margin:0px;font-size: 14px;margin-bottom:15px;
  }
  .blank{
    background-color:#cccccc !important;
    -webkit-print-color-adjust: exact;
  }
  .table-border.table-inner{
    border:none; margin:0px;
  }
  .pd-0{
    padding:0px !important;
  }
  .alert {
    padding: 8px;
    background-color: #387d38;
    color: #FFF;
    border-radius: 5px;
  }
  .closebtn {
    margin-left: 15px;
    color: white;
    font-weight: bold;
    float: right;
    font-size: 22px;
    line-height: 20px;
    cursor: pointer;
    transition: 0.3s;
  }
  .closebtn:hover {
    color: black;
  }
  .overlay {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: fixed;
    background: #141415ad;
    display: none;
}
.overlay__inner {
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    position: absolute;
}
.overlay__content {
    left: 50%;
    position: absolute;
    top: 50%;
    transform: translate(-50%, -50%);
    color: #FFF;
}
.spinner {
    width: 40px;
    height: 40px;
    display: inline-block;
    border-width: 2px;
    border-color: rgba(255, 255, 255, 0.05);
    border-top-color: #fff;
    animation: spin 1s infinite linear;
    border-radius: 100%;
    border-style: solid;
    vertical-align: middle;
}
@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
</style>
<style media="print">
  .height{
    height:48px;
  }
   @page {
        size: A4 portrait;
        margin: 0;
    }
</style>
<div class=" row-offcanvas row-offcanvas-right">
    @if(Session::has('message'))
    <div class="content-wrapper-msg">
    <div class="alert"><span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>{{ Session::get('message')}}</div>
    </div>
    @endif
</div>
<div id="overlay" class="overlay">
    <div class="overlay__inner">
        <div class="overlay__content">Sending Email....  &nbsp;<span class="spinner">wait</span></div>
    </div>
</div>
<div class="content-wrapper">
  <div class="row grid-margin mt-3 mb-2">
    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
      <div class="card">
        <div class="card-body">
          <div class="tab-content">
            <div id="sanctionSupplyChain" class="tab-pane fadein active">
              <form action="{{route('save_sanction_letter_supplychain')}}" method="POST">
                @if(!empty($supplyChaindata['offerData']) && $supplyChaindata['offerData']->count())
                <div class="form-fields">
                  <h5 style="text-align: center; font-size: 20px;">Sanction Letter For Supply Chain</h5>
                  <table style="width:100%;font-family:Arial;font-size: 14px;" cellpadding="0" cellspacing="0">
                    <tr>
                      <td><b>To</b></td>
                    </tr>
                    @if(!empty($supplyChaindata['ConcernedPersonName']))
                    <tr>
                      <td style="padding: 2px">{{$supplyChaindata['ConcernedPersonName']}}</td>
                    </tr>
                    @endif
                    @if(!empty($supplyChaindata['EntityName']))
                    <tr>
                      <td style="padding: 2px">{{$supplyChaindata['EntityName']}}</td>
                    </tr>
                     @endif
                     @if(!empty(trim($supplyChaindata['Address'])))
                    <tr>
                      <td style="padding: 2px">{{$supplyChaindata['Address']}}</td>
                    </tr>
                    @endif
                    @if(!empty($supplyChaindata['EmailId']))
                    <tr>
                      <td style="padding: 2px">{{$supplyChaindata['EmailId']}}</td>
                    </tr>
                    @endif
                    @if(!empty($supplyChaindata['MobileNumber']))
                    <tr>
                      <td style="padding: 2px">{{$supplyChaindata['MobileNumber']}}</td>
                    </tr>
                    @endif
                  </table>
                  <br />
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility (Product)</th>
                        <th width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Amount (Rs. In Mn)</th>
                        <th width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Sub-Limit of</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{getProductType($supplyChaindata['product_id'])}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$supplyChaindata['tot_limit_amt']}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['sublimit_of']}}</td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>Section 1:- Conditions for individual facilities<br/><small>(Select facilitylies from below mentioned facilities and delete others while submitting the final term sheet.)</small></h5>
                  <!-- Vender Program -->
                  <table  style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility No</td>
                        <td width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$supplyChaindata['prgm_type']}}</td>
                        <td width="30%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility Name</td>
                        <td width="30%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$supplyChaindata['prgm_type'] == '2' ? 'Purchase Finance Facility  /  Channel Financing' : 'Vendor Finance Facility'}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility Amount</td>
                        <td width="66.66%"  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" colspan="3">{{$supplyChaindata['limit_amt']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Purpose</td>
                        <td width="66.66%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" colspan="3">{{$supplyChaindata['purpose']}}</td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Expiry of Limit</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%" colspan="3"> Limit will be valid for 1 year from {{$postData['expiry_of_limit']}} (Date will be selected from sanction letter itself) Documents required for renewal of facility to be submitted to Capsave Finance Pvt Limited at least 40 days prior to limit expiry.
                        </td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Specific Condition</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%" colspan="3">
                          <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                            <li>Invoices should not be older than 30 days from {{$postData['specific_cond']}}.</li>
                            <li>Discounting proceed to be credited to working capital account of the borrowers.</li>
                          </ul>
                        </td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Specific Pre-disbursement Condition</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%" colspan="3">
                            @if(!empty($supplyChaindata['reviewerSummaryData']['preCond']))
                           <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                              @foreach($supplyChaindata['reviewerSummaryData']['preCond'] as $k => $precond)
                              <li>{{$precond}}</li>
                              @endforeach
                           </ul>
                           @endif
                        </td>
                     </tr>
                     <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%"> Specific Post-disbursement Condition</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%" colspan="3">
                           @if(!empty($supplyChaindata['reviewerSummaryData']['postCond']))
                           <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                              @foreach($supplyChaindata['reviewerSummaryData']['postCond'] as $k => $postcond)
                              <li>{{$postcond}}</li>
                              @endforeach
                           </ul>
                           @endif
                        </td>
                     </tr>
                    </tbody>
                  </table>
                  <br />
                  @foreach($supplyChaindata['offerData'] as $key =>  $offerD)
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;"  cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="100%" colspan="3" class="pd-0" style="padding: 0px !important;">
                          <table style="width:100%;font-family:Arial;font-size: 14px;border:none;margin: 0" cellpadding="0" cellspacing="0">
                            <thead>
                              <tr>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Apprv. Debtor Name</th>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Sub Limit</th>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Max. Discounting Period</th>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Grace Period</th>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">ROI</th>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Bench Mark Date</th>
                                <th  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Margin</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" >{{$supplyChaindata['anchorData'][$offerD->anchor_id]['comp_name'] ?? ''}}</td>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$offerD->prgm_limit_amt}}</td>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$offerD->tenor}}</td>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$offerD->grace_period}}</td>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$offerD->interest_rate}}</td>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{getBenchmarkType($offerD->benchmark_date)}}</td>
                                <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$offerD->margin}}</td>
                              </tr>
                            </tbody>
                          </table>
                        </td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Investment Payment Frequency</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%" colspan="3">{{getInvestmentPaymentFrequency($offerD['payment_frequency'])}}</td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Methodology for calculating for  Drawing Power</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%" colspan="3">As mentioned in Margin Section</td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Penal Interest</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%">

                          <ul style="padding:0px 0px 0px 15px; margin:0px; line-height:23px;list-style-type:unset;">
                            @if(!empty($postData['penal_applicable'][$key][0]) && strtolower($postData['penal_applicable'][$key][0]) == 'applicable')
                            <li>{{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} {{$postData['penal_on'][$key][0]}} entire principal / payable interest on delay in repayment of principal / Interest / charges.</li>
                            @endif
                             @if(!empty($postData['penal_applicable'][$key][1]) && strtolower($postData['penal_applicable'][$key][1]) == 'applicable')
                            <li>The rate of interest will be {{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} higher than the rate stipulated under each of the facilities till the security is created.</li>
                            @endif
                             @if(!empty($postData['penal_applicable'][$key][2]) && strtolower($postData['penal_applicable'][$key][2]) == 'applicable')
                            <li>If security is not created within the stipulated timeframe then a penal interest of 
                            {{!empty($offerD['overdue_interest_rate']) ? $offerD['overdue_interest_rate'] .'%' : ''}} p.a. {{$postData['penal_on'][$key][1]}} entire principle.</li>
                            @endif
                          </ul>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  @endforeach
                  <br />

                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Prepayment</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%">{{$postData['prepayment'] ?? 'In case borrower desires to prepay the loan, the prepayment of loan will be accepted on the terms and conditions to be decided by CFPL for time to time.'}}</td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Payment Mechanism of Interest</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%">{{$postData['payment_machanism_of_interest'] ?? ''}}</td>
                      </tr>
                      <tr>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="33.33%">Payment Mechanism of Principal</td>
                        <td  style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="66.66%">{{$postData['payment_machanism_of_principal']}}</td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5>Section 2:- Common Securities << Depending on Addition Security selected on Limit Assesment>></h5>
                  @foreach($supplyChaindata['offerData'] as $offerD)
                  <div style="border: 2px solid #cccccc;margin-bottom: 20px;">
                    @if($offerD->offerPs->count())
                    <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;"> Primary Security </h5>
                    <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="20%">Security</th>
                          <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="20%">Type of security</th>
                          <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="20%">Status of security</th>
                          <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="20%">Time for perfecting security</th>
                          <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;" width="20%">Description of security</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerPs as $PrimarySecurity)
                        <tr>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.ps_security_id.'.$PrimarySecurity->ps_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.ps_type_of_security_id.'.$PrimarySecurity->ps_type_of_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.ps_status_of_security_id.'.$PrimarySecurity->ps_status_of_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.ps_time_for_perfecting_security_id.'.$PrimarySecurity->ps_time_for_perfecting_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$PrimarySecurity->ps_desc_of_security}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerCs->count())
                    <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;"> Collateral Security </h5>
                    <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Type of security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Status of security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Time for perfecting security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Description of security</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerCs as $CollateralSecurity)
                        <tr>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.cs_desc_security_id.'.$CollateralSecurity->cs_desc_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.cs_type_of_security_id.'.$CollateralSecurity->cs_type_of_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.cs_status_of_security_id.'.$CollateralSecurity->cs_status_of_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.cs_time_for_perfecting_security_id.'.$CollateralSecurity->cs_time_for_perfecting_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$CollateralSecurity->cs_desc_of_security}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerPg->count())
                    <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">Personal Guarantee</h5>
                    <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Name of Guarantor</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Time for perfecting security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Residential Address </th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Net worth as per IT return/CA Certificate</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Comment if any </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerPg as $PersonalGuarantee)
                        <tr>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$supplyChaindata['bizOwnerData'][$PersonalGuarantee->pg_name_of_guarantor_id]['first_name'] ?? ''}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.pg_time_for_perfecting_security_id.'.$PersonalGuarantee->pg_time_for_perfecting_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$PersonalGuarantee->pg_residential_address}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$PersonalGuarantee->pg_net_worth}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$PersonalGuarantee->pg_comments}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerCg->count())
                    <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">Corporate Guarantee/ Letter of Comfort/ Put Option</h5>
                    <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Type</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Name of Guarantor</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Time for perfecting security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Registered Address</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Comment if any </th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerCg as $CorporateGuarantee)
                        <tr>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.cg_type_id.'.$CorporateGuarantee->cg_type_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$supplyChaindata['bizOwnerData'][$CorporateGuarantee->cg_name_of_guarantor_id]['first_name'] ?? ''}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.cg_time_for_perfecting_security_id.'.$CorporateGuarantee->cg_time_for_perfecting_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$CorporateGuarantee->cg_residential_address}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$CorporateGuarantee->cg_comments}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                    @if($offerD->offerEm->count())
                    <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">Escrow Mechanism</h5>
                    <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                      <thead>
                        <tr>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Name of Debtor</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Expected cash flow per month</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Time for perfecting security</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Mechanism</th>
                          <th width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Comment if any</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($offerD->offerEm as $EscrowMechanism)
                        <tr>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$EscrowMechanism->em_debtor_id}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$EscrowMechanism->em_expected_cash_flow}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{config('common.em_time_for_perfecting_security_id.'.$EscrowMechanism->em_time_for_perfecting_security_id)}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$EscrowMechanism->em_mechanism_id}}</td>
                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$EscrowMechanism->em_comments}}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                    @endif
                  </div>
                  @endforeach
                  <h5>Section 3:Specific Security</h5>
                  <h5>Section 4:- Security PDCs/ECS Mandate with Undertaking, DSRA and Other Securities</h5>
                  <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">PDC</h5>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility No</td>
                        <td width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_facility_no']}}</td>
                        <td width="30%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility Name</td>
                        <td width="30%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_facility_name']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility Amount</td>
                        <td width="66.66%" colspan="3" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_facility_amt']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Purpose</td>
                        <td width="66.66%" colspan="3" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_facility_purpose']}}</td>
                      </tr>
                    </tbody>
                  </table>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Cheque for</th>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">No of Cheque </th>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Not Above </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Principal</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_no_of_cheque'][0]}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_not_above'][0]}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Interest</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_no_of_cheque'][1]}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_not_above'][1]}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Repayment</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_no_of_cheque'][2]}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_not_above'][2]}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Other</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_no_of_cheque'][3]}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_not_above'][3]}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">security</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_no_of_cheque'][4]}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['pdc_not_above'][4]}}</td>
                      </tr>
                    </tbody>
                  </table>
                  <br />
                  <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">NACH Mandate with undertaking</h5>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility No</td>
                        <td width="20%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_facility_no']}}</td>
                        <td width="30%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility Name</td>
                        <td width="30%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_facility_name']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Facility Amount</td>
                        <td width="66.66%" colspan="3" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_facility_amt']}}</td>
                      </tr>
                      <tr>
                        <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Purpose</td>
                        <td width="66.66%" colspan="3" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_facility_purpose']}}</td>
                      </tr>
                    </tbody>
                  </table>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Cheque for</th>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">No of Cheque </th>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Not Above </th>
                      </tr>
                    </thead>
                     <tbody>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Principal</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_no_of_cheque']['0']}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_not_above']['0']}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Interest</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_no_of_cheque']['1']}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_not_above']['1']}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Repayment</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_no_of_cheque']['2']}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_not_above']['2']}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Other</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_no_of_cheque']['3']}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_not_above']['3']}}</td>
                      </tr>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">security</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_no_of_cheque']['4']}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['nach_not_above']['4']}}</td>
                      </tr>
                    </tbody>
                  </table>
                  @if(!empty($postData['dsra_applicability']) && strtolower($postData['dsra_applicability']) == 'applicable')
                  <br />
                  <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">DSRA</h5>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Amount(lacs in INR )</th>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Tenure(in months)</th>
                        <th style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Comment if any</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['dsra_amt'] ?? NULL}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['dsra_tenure'] ?? NULL}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['dsra_comment'] ?? NULL}}</td>
                      </tr>
                    </tbody>
                  </table>
                  @endif

                  @if(!empty($postData['dsra_applicability']) && strtolower($postData['dsra_applicability']) == 'applicable')
                  <br />
                  <h5 style="background-color: #ccc;padding: 10px;margin-bottom: 0;">Any other security</h5>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <tbody>
                      <tr>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['other_sucurities'] ?? NULL}}</td>
                      </tr>
                    </tbody>
                  </table>
                   @endif
                  <br />
                  <h5>Section 5:- Financial Covenants</h5>
                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                    <thead>
                      <tr>
                        <th width="75%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Covenants</th>
                        <th width="25%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Minimum/Maximum ratio</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($postData['covenants']['name'] as $k => $val)
                      @if(!empty($postData['covenants']['ratio_applicability'][$k]) && strtolower($postData['covenants']['ratio_applicability'][$k]) == 'applicable')
                      <tr class="covenants_clone_tr">
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['covenants']['name'][$k]}}</td>
                        <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">{{$postData['covenants']['ratio'][$k]}}</td>
                      </tr>
                      @endif
                      @endforeach
                    </tbody>
                  </table>
                  <p>The financial covenants shall be tested on a choose an item.basis and shall be reported in the monitoring report to be submitted by choose an item.</p>

                  <h5>Section 6:- General Pre-disbursement and Post Disbursement conditions</h5>
                      <div>
                        <ol>
                          @if(!empty($postData['pre_post_condition']['0']) && strtolower($postData['pre_post_condition']['0']) == 'applicable')       
                          <li>Form CHG-1 to be filed with ROC within 30 days from the date of execution of Security Documents of the borrower/Corporate Guarantor</li>
                          @endif

                          @if(!empty($postData['pre_post_condition']['1']) && strtolower($postData['pre_post_condition']['1']) == 'applicable')
                          <li>CFPL shall, at its discretion, obtain a confidential credit report on the borrower from its other lenders.</li>
                          @endif

                           @if(!empty($postData['pre_post_condition']['2']) && strtolower($postData['pre_post_condition']['2']) == 'applicable')
                          <li>All the assets charged to the CFPL are to be insured for full value covering all risks with usual CFPL clause. A copy of the insurance policy(ies) to be furnished to the CFPL within 30 days of security perfection.</li>
                          @endif

                           @if(!empty($postData['pre_post_condition']['3']) && strtolower($postData['pre_post_condition']['3']) == 'applicable')
                          <li>The obligation of the Lender to make disbursements out of the Facility shall be subject to the Borrower complying with the following conditions to the satisfaction of CFPL .The Borrower shall complete all documentation as stipulated, to the satisfaction of CFPL.The Borrower to furnish title investigation search and valuation of security ( being mortgaged to CFPL) prior to disbursement.</li>
                          @endif  

                          @if(!empty($postData['pre_post_condition']['4']) && strtolower($postData['pre_post_condition']['4']) == 'applicable')
                          <li>The borrower shall finalise its selling arrangements to the satisfaction of CFPL.</li>
                          @endif 

                          @if(!empty($postData['pre_post_condition']['5']) && strtolower($postData['pre_post_condition']['5']) == 'applicable')
                          <li>The borrower shall obtain necessary sanction of power, water, fuel, etc from the relevant authorities to the satisfaction of CFPL.</li>
                          @endif

                          @if(!empty($postData['pre_post_condition']['6']) && strtolower($postData['pre_post_condition']['6']) == 'applicable')
                          <li>The borrower shall make adequate arrangements for treatment and disposal of effluents, solid waste and emissions from its project and shall furnish appropriate approvals from the authorities in this regard.</li>
                          @endif 

                          @if(!empty($postData['pre_post_condition']['7']) && strtolower($postData['pre_post_condition']['7']) == 'applicable')
                          <li>The borrower shall broadbase its Board of Directors and finalise and strengthen its management set-up to the satisfaction of CFPL, if necessary.</li>
                          @endif 

                          @if(!empty($postData['pre_post_condition']['8']) && strtolower($postData['pre_post_condition']['8']) == 'applicable')
                          <li>The borrower shall carry out safety/environment/energy audit of its project to the satisfaction of CFPL.</li>
                          @endif 

                         @if(!empty($postData['pre_post_condition']['9']) && strtolower($postData['pre_post_condition']['9']) == 'applicable')
                        <li>CFPL reserves the right to appoint qualified accountants / technical experts /management consultants of its choice to examine the books of accounts, factories and operations of the borrower or to carry out a full concurrent/statutory audit. The cost of such inspection shall be borne by the {{$postData['abfl_or_borrower'] ?? ''}}</li>
                        @endif

                        @if(!empty($postData['pre_post_condition']['10']) && strtolower($postData['pre_post_condition']['10']) == 'applicable')
                        <li>In case any condition is stipulated by any other lender that is more favorable to them than the terms stipulated by CFPL, CFPL shall at its discretion, apply to this loan such equivalent conditions to bring its loan at par with those of the other lenders.</li>
                        @endif

                        @if(!empty($postData['pre_post_condition']['11']) && strtolower($postData['pre_post_condition']['11']) == 'applicable')
                        <li>The borrower shall forward to CFPL, provisional balance sheet and Profit & Loss Account within {{$postData['profit_loss_account_within'] ?? '1'}}  months of year-end and audited accounts within 6 months of year end. Quarterly financial results shall be submitted within 60 days from the end of each quarter or with the filing with stock exchange for listed borrower.</li> 
                        @endif

                        @if(!empty($postData['pre_post_condition']['12']) && strtolower($postData['pre_post_condition']['12']) == 'applicable')
                        <li>Inspection of assets charged to CFPL may be carried out once in {{$postData['cfpl_carried_in'] ?? '1'}} months or at more frequent intervals as decided by CFPL by its own officials or through persons/firm appointed by CFPL. The cost of inspection is to be borne by the borrower.</li>
                        @endif

                        @if(!empty($postData['pre_post_condition']['13']) && strtolower($postData['pre_post_condition']['13']) == 'applicable')
                        <li>During the currency of CFPL’s credit facility(s), the borrower will not without CFPL’s prior {{$postData['cfpl_prior'] ?? ''}} in writing: 
                          <ol>
                            <li>conclude any fresh borrowing arrangement either secured or unsecured with any other Bank or Financial Institutions, borrower or otherwise, not create any further charge over their fixed assets without our prior approval in writing. </li>
                            <li>undertake any expansion or fresh project or acquire fixed assets, while normal capital expenditure, e.g. replacement of parts, can be incurred. </li>
                            <li>invest by way of share capital in or lend or advance to or place deposits with any other concern (normal trade credit or security deposit in the routine course of business or advances to employees can, however, be extended). </li>
                            <li>formulate any scheme of amalgamation with any other borrower or reconstruction, acquire any borrower. </li>
                            <li>undertake guarantee obligations on behalf of any other borrower or any third party. </li>
                            <li>declare dividend for any year except out of profits relating to that year after making all the due and necessary provisions provided that no default had occurred in any repayment obligation and Bank’s permission is obtained. </li>
                            <li>make any repayment of the loans and deposits and discharge other liabilities except those shown in the funds flow statement submitted from time to time. </li>
                            <li>make any change in their management set-up. </li>
                          </ol></li>
                        @endif
                        </ol>
                    </div>
                    <h5>Section 7:- Monitoring Conditions </h5>
                           <div class="section7">
                                  <ul style="list-style-type:unset;">
                                    <li>{{$postData['monitoring_condition_1'] ?? NULL}} or {{$postData['monitoring_condition_2'] ?? NULL}} to be submitted as under:
                                    </li>
                                    @if(!empty($postData['stock_n_book_statement']) && strtolower($postData['stock_n_book_statement']) == 'applicable')
                                    <li>
                                      Stock and Book Debt statements
                                    </li>
                                    @endif
                                    @if(!empty($postData['stock_n_book_statement_applicable']) && strtolower($postData['stock_n_book_statement_applicable']) == 'applicable')
                                    <li>
                                      The stock and book debt statement as on the last day of the month is to be submitted by {{$postData['stock_n_book_debt_date1'] ?? NULL}} {{$postData['stock_n_book_debt_date2'] ?? NULL}} th of next month.Basis of Valuation of Inventory and Book Debts.
                                    </li>
                                    @endif
                                  </ul>
                                  <table style="width:100%;font-family:Arial;font-size: 14px;border:#ccc solid 1px;" cellpadding="0" cellspacing="0">
                                    <tbody>
                                       <tr>
                                          <td width="33.33%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Raw Material</td>
                                          <td width="66.67%" style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">At Cost Price or Market Price, whichever is lower</td>
                                       </tr>
                                       <tr>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Stock in Process</td>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">At Cost of production</td>
                                       </tr>
                                       <tr>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Stores and Spares</td>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">At Cost Price or Market Price, whichever is lower</td>
                                       </tr>
                                       <tr>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Finished Goods</td>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">At Cost of Sales or Controlled Price or Market Price, whichever is lower</td>
                                       </tr>
                                       <tr>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Domestic receivables(Period upto 90/120 days)</td>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">At invoice Value</td>
                                       </tr>
                                       <tr>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">Export receivables(Period upto 90/120 days)</td>
                                          <td style="border-right: 1px solid #cccccc;border-bottom: 1px solid #cccccc;vertical-align: top;font-size: 14px;text-align:left;padding:5px 10px;">At invoice Value</td>
                                       </tr>
                                    </tbody>
                                 </table>
                                 @if(!empty($postData['any_other_doc_monitoring']) && strtolower($postData['any_other_doc_monitoring']) == 'applicable')
                                 <ul style="list-style-type:unset;">
                                    <li>Any other document for post disbursement monitoring</li>
                                    @if(!empty($postData['any_other_doc_monitoring_1']))
                                    <li>{{$postData['any_other_doc_monitoring_1'] ?? NULL}}</li>
                                    @endif
                                    @if(!empty($postData['any_other_doc_monitoring_2']))
                                    <li>{{$postData['any_other_doc_monitoring_2'] ?? NULL}}</li>
                                    @endif
                                    @if(!empty($postData['any_other_doc_monitoring_3']))
                                    <li>{{$postData['any_other_doc_monitoring_3'] ?? NULL}}</li>
                                    @endif
                                  </ul>
                                 @endif
                                 <p>
                                     Non submission of any of the above mentioned documents within the stipulated timelines, CFPL shall reserve the right to charge penalty from the due date of such submission at 2% p.a over and above the prevailing interest rates.
                                  </p>
                                 <br />
                           </div>
                           <h5>Section 8:- General Conditions </h5>
                           <div class="section8">
                              <ol>
                                    <li>The loan shall be utilised for the purpose for which it is sanctioned and it should not be utilised for –
                                      <ul style="list-style-type:unset;">
                                        <li>Subscription to or purchase of shares/debentures.</li>
                                        <li>Extending loans to subsidiary companies/associates or for making inter-corporate deposits.</li>
                                        <li>Any speculative purposes.</li>
                                      </ul>
                                    </li>
                                    <li>The borrower shall maintain adequate books and records which should correctly reflect their financial position and operations and it should submit to CFPL at regular intervals such statements as may be prescribed by CFPL in terms of the RBI / Bank’s instructions issued from time to time.</li>
                                    <li>The borrower will keep CFPL informed of the happening of any event which is likely to have an impact on their profit or business and more particularly, if the monthly production or sale and profit are likely to be substantially lower than already indicated to CFPL. The borrower will inform accordingly with reasons and the remedial steps proposed to be taken. </li>
                                    <li>CFPL will have the right to examine at all times the borrower’s books of accounts and to have the borrower’s factory(s)/branches inspected from time to time by officer(s) of the CFPL and/or qualified auditors including stock audit and/or technical experts and/or management consultants of CFPL’s choice and/or we can also get the stock audit conducted by other banker. The cost of such inspections will be borne by the borrower.</li>
                                    <li>The borrower should not pay any consideration by way of commission, brokerage, fees or in any other form to guarantors directly or indirectly.</li>
                                    <li>The Borrower and Guarantor(s) shall be deemed to have given their express consent to CFPL to disclose the information and data furnished by them to CFPL and also those regarding the credit facility/ies enjoyed by the borrower, conduct of accounts and guarantee obligations undertaken by guarantor to the Credit Information Bureau (India) Ltd. (“CIBIL”), or RBI or any other agencies specified by RBI who are authorised to seek and publish information.</li>
                                    <li>The Borrower will keep the CFPL advised of any circumstances adversely affecting their financial position including any action taken by any creditor, Government authority against them.</li>
                                    <li>The borrower shall procure a consent every year from the auditors appointed by the borrower to comply with and give report / specific comments in respect of any query or requisition made by us as regards the audited accounts or balance sheet of the borrower. We may provide information and documents to the Auditors in order to enable the Auditors to carry out the investigation requested for by us. In that event, we shall be entitled to make specific queries to the Auditors in the light of Statements, particulars and other information submitted by the borrower to us for the purpose of availing finance, and the Auditors shall give specific comments on the queries made by us.</li>
                                    <li>The sanction limits would be valid for acceptance for 30 days from the date of the issuance of letter.</li>
                                    <li>CFPL reserves the right to alter, amend any of the condition or withdraw the facility, at any time without assigning any reason and also without giving any notice.</li>
                                    <li>Provided further that notwithstanding anything to the contrary contained in this Agreement, CFPL may at its sole and absolute discretion at any time, terminate, cancel or withdraw the Loan or any part thereof (even if partial or no disbursement is made) without any liability and without any obligations to give any reason whatsoever, whereupon all principal monies, interest thereon and all other costs, charges, expenses and other monies outstanding (if any) shall become due and payable to CFPL by the Borrower forthwith upon demand from CFPL</li>
                              </ol>
                           </div>
                           @if(!empty($download) && $download == true)
                           <div align="center"><a href="{{ route('send_sanction_letter_supplychain', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId]) }}" style=" background-color: #30878e;border: none;border-radius: 5px;color: white;padding: 10px 10px;text-align: center;text-decoration: none;display: inline-block;font-size: 14px;margin: 4px 2px;cursor: pointer;" onclick="document.getElementById('overlay').style.display='block'"> Send Email</a></div>
                           @endif
                </div>
                @endif

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
