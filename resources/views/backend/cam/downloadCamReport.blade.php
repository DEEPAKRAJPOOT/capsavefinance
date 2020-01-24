@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    



<div class="inner-container">
    <div class="card mt-3">
        <div class="card-body pt-3 pb-3">
            <button onclick="downloadCam(49)" class="btn btn-primary float-right btn-sm "> Download Report</button>
        </div>
    </div>
   <div class="card mt-3">
      <div class="card-body pt-3 pb-3">
         <div class="row">
            <div class="col-md-12">
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Group</th>
                        <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Borrower</th>
                        <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Proposed Limit (₹ Mn)</th>
                        <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Existing Exposure (₹ Mn)</th>
                        <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="20%">Total Exposure (₹ Mn)</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr role="row" class="odd">
                        <td class="">-</td>
                        <td class="">-</td>
                        <td class="">-</td>
                        <td class="">-</td>
                        <td class="">-</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Deal Structure:</h5>
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="30%">Criteria</th>
                        <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Particulars</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr role="row" class="odd">
                        <td class="">Facility Type</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Limit (₹ In Mn)</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Tenor (Months)</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Equipment Type</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Security Deposit</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Rental Frequency</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">PTPQ</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="" valign="top">XIRR</td>
                        <td class="" valign="top">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Additional Security</td>
                        <td class="">-</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Pre/ Post Disbursement Conditions:</h5>
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr role="row">
                        <th class="sorting_asc" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="60%">Condition</th>
                        <th class="sorting" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending">Timeline</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr role="row" class="odd">
                        <td class="">NACH mandate for the rentals</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Inspection of Assets</td>
                        <td class="">-</td>
                     </tr>
                     <tr role="row" class="odd">
                        <td class="">Insurance policy of the assets under rental to be endorsed in favor of CFPL</td>
                        <td class="">-</td>
                     </tr>
                     
                  </tbody>
               </table>
               <h5 class="mt-4">The proposed deal is approved/declined/deferred subject to above conditions and any other conditions mentioned below.</h5>
               <table id="invoice_history" class="table  no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Recommended By</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="3" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="75%">Investment Committee Members</th>
                     </tr>
                     <tr role="row">
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%" style="background:#62b59b;">Dhriti Barman</th>
                        <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%" style="background:#62b59b;">Vivek Tolat/Sharon Coorlawala</th>
                        <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%" style="background:#62b59b;">Jinesh Kumar Jain</th>
                        <th class="sorting text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-label="Docs : activate to sort column ascending" width="25%" style="background:#62b59b;">Praveen Chauhan</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr role="row" class="odd">
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                        <td align="center">-</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Minimum Acceptance Criteria as per NBFC Credit Policy:</h5>
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Parameter</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Criteria</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Deviation</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Remarks</th>
                     </tr>
                     <tr>
                        <th class="sorting_asc " tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%" style="background:#62b59b;">Borrower Vintage &amp; Constitution</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="3" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="75%" style="background:#62b59b;"></th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Constitution</td>
                        <td>
                           <p class="m-0">-</p>
                        </td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Vintage</td>
                        <td>
                           <p class="m-0">-</p>
                        </td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td colspan="4" bgcolor="#cccccc">&nbsp;</td>
                     </tr>
                     <tr>
                        <td>CFPL Defaulter List</td>
                        <td>-</td>
                        <td>-</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>RBI Defaulter list</td>
                        <td>-</td>
                        <td>-</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>CDR/ BIFR/ OTS/ Restructuring</td>
                        <td>-</td>
                        <td>-</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>CIBIL</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Watchout Investors</td>
                        <td>- </td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Google Search (Negative searches)</td>
                        <td>- </td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td colspan="4" bgcolor="#cccccc">&nbsp;</td>
                     </tr>
                     <tr>
                        <td>Satisfactory contact point verification</td>
                        <td>- </td>
                        <td>-</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>Satisfactory banker reference</td>
                        <td>- </td>
                        <td>-</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>Satisfactory trade reference</td>
                        <td>- </td>
                        <td>-</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td colspan="4" bgcolor="#cccccc">&nbsp;</td>
                     </tr>
                     <tr>
                        <td>Adjusted Tangible Net Worth</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Cash Profit</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>DSCR</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Debt/EBIDTA</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td colspan="4" bgcolor="#cccccc">
                           <h5 class="m-0">Other</h5>
                        </td>
                     </tr>
                     <tr>
                        <td>Negative Industry Segment</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Exposure to sensitive sectors</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>Sensitive geography/region/area</td>
                        <td>No</td>
                        <td>No</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>Politically exposed person</td>
                        <td>No</td>
                        <td>No</td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>KYC risk profile</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>UNSC List</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Approval criteria for IC:</h5>
               <table id="invoice_history" class="table   table-striped no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="10%">Sr. No.</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Parameter</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="40%">Criteria</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="25%">Remarks</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>1</td>
                        <td>Nominal RV Position</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>2</td>
                        <td>Asset concentration as % of the total portfolio</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>3</td>
                        <td>Single Borrower Limit</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>4</td>
                        <td>Borrower Group Limit</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>5</td>
                        <td>Exposure on customers below investment grade (BBB - CRISIL/CARE/ICRA/India Ratings) and unrated customers</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                     <tr>
                        <td>6</td>
                        <td>Exposure to a particular industry/sector as a percentage of total portfolio</td>
                        <td>-</td>
                        <td>-</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Purpose of Rental Facility</h5>
               <p>Rental facility of Rs. 40 Mn for procurement of plant and machinery for tenor of 60 months.</p>
               <h5 class="mt-4"> About the Company</h5>
               <p>------</p>
               <p><strong>ARIL has 6 existing manufacturing locations as provided below:</strong></p>
               <table class="table   no-footer overview-table" role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="7%">Sr. No.</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="10%">Unit</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="15%">Location</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="14%">Area in Sq. Meters</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="14%">Particulars</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Type of Manufacturing Plant</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Customers</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>1</td>
                        <td colspan="3" width="39%" style="padding:0px !important;">
                           <table width="100%" class="">
                              <tbody>
                                 <tr>
                                    <td width="25%">Unit -IA</td>
                                    <td width="39%">Plot No. 8110,8110,8111, GIDC Sachin, Surat - 394230</td>
                                    <td width="36%">9,000.00</td>
                                 </tr>
                                 <tr>
                                    <td>Unit -II</td>
                                    <td>Plot No. 701, GIDC Sachin, Surat - 394230</td>
                                    <td>3,500.00</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td>Amino Di Phenyl Ethers</td>
                        <td>Multipurpose Plant</td>
                        <td>Syngenta, Basf, Globachem, Dow Agrosciences LLC, Bayer AG, Falcon, Ipca Laboratories, Alka Laboratories, Anchor Pharma Falcon</td>
                     </tr>
                     <tr>
                        <td>2</td>
                        <td colspan="3" width="39%" style="padding:0px !important;">
                           <table width="100%" class="">
                              <tbody>
                                 <tr>
                                    <td width="25%">Unit -IA</td>
                                    <td width="39%">Plot No. 8110,8110,8111, GIDC Sachin, Surat - 394230</td>
                                    <td width="36%">9,000.00</td>
                                 </tr>
                                 <tr>
                                    <td>Unit -II</td>
                                    <td>Plot No. 701, GIDC Sachin, Surat - 394230</td>
                                    <td>3,500.00</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td>Other Miscellaneous</td>
                        <td>Multipurpose Plant</td>
                        <td>Colourtex, Kadam Dyes &amp; Intermediates, Zeel DyeChem, Alok Industries, Amar Impex</td>
                     </tr>
                     <tr>
                        <td>3</td>
                        <td>Unit -IB</td>
                        <td>Plot No. 8104, GIDC Sachin, Surat - 394230</td>
                        <td>8,550.00</td>
                        <td colspan="2" width="34%" style="padding:0px !important;">
                           <table width="100%" class="">
                              <tbody>
                                 <tr>
                                    <td width="41%">Amino Di Phenyl Ethers</td>
                                    <td width="59%">Multipurpose Plant</td>
                                 </tr>
                                 <tr>
                                    <td>Dichlorophenol</td>
                                    <td>Multipurpose Plant</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td>Syngenta, Basf, Sumitomo Chemical Company Limited, Globachem, Amar Impex</td>
                     </tr>
                     <tr>
                        <td>4</td>
                        <td>Unit -IV</td>
                        <td>Plot No. 907/3, Jhagadia Industrial Estate, Bharuch -393110</td>
                        <td>27,000.00</td>
                        <td colspan="2" width="34%" style="padding:0px !important;">
                           <table width="100%" class="">
                              <tbody>
                                 <tr>
                                    <td width="41%">1,4-Dioxane</td>
                                    <td width="59%">Product Specific Plant</td>
                                 </tr>
                                 <tr>
                                    <td>MetaDichloro Benzene</td>
                                    <td>Product Specific Plant</td>
                                 </tr>
                                 <tr>
                                    <td>TCBA(T.can)</td>
                                    <td>Product Specific Plant</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td>CadilaHealthCare,Hetero Lab,HonourLab,Mylan Laboratories,Virchow Petrochemical</td>
                     </tr>
                     <tr>
                        <td>5</td>
                        <td>Unit-III</td>
                        <td>Plot No. 905/1, Jhagadia Industrial Estate, Bharuch -393110</td>
                        <td>82,000.00</td>
                        <td style="padding:0px !important;">
                           <table width="100%">
                              <tbody>
                                 <tr>
                                    <td>Dicamba</td>
                                 </tr>
                                 <tr>
                                    <td>Hydrogenation</td>
                                 </tr>
                                 <tr>
                                    <td>Distillation Plant</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td>Multipurpose Plant</td>
                        <td>Japanese customer, Syngenta</td>
                     </tr>
                     <tr>
                        <td>6</td>
                        <td>Unit - VI</td>
                        <td>Plot No. 2425, GIDC Sachin, Surat - 394230</td>
                        <td>9,375.00</td>
                        <td style="padding:0px !important;">
                           <table width="100%">
                              <tbody>
                                 <tr>
                                    <td>Multi   Purpose Pharmaceuticals Plant (Plant - 1)</td>
                                 </tr>
                                 <tr>
                                    <td>2,4  DFNB  and DFBA Plant</td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                        <td>Multipurpose Plant</td>
                        <td>Dishman, Sumitomo</td>
                     </tr>
                  </tbody>
               </table>
               <br>
               <p>The distillation plant(backward integration project) expected to be completed by end of December 2019. Further the Unit VI is expected to go onstream from February 2020. All other plants are operational and have started catering to respective customers. The company also has full-fledged R&amp;D laboratory capable of running many product development streams at the same time, which is independently controlled by the R&amp;D Head.</p>
               <h5 class="mt-4">Brief Background of Mr. Anand Desai; Managing Director :</h5>
               <p>------</p>
               <p class="text-center "><img class="img-fluid" src="assets/img/image.png"></p>
               <h5 class="mt-4"> Board of Directors as on December 2019</h5>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th width="50%">Name of Director</th>
                        <th width="50%">Designation</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Mr. Anand Sureshbhai Desai</td>
                        <td>Managing Director</td>
                     </tr>
                     <tr>
                        <td>Ms. Mona Anandbhai Desai</td>
                        <td>Wholetime Director</td>
                     </tr>
                     <tr>
                        <td>Mr.Kiran Chhotubhai Patel</td>
                        <td>Director</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">  Shareholding Pattern as on October 30, 2019</h5>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th class="text-center" width="50%">Name</th>
                        <th class="text-center" width="50%">% Holding</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <th>Promoters</th>
                        <th class="text-center">38.40</th>
                     </tr>
                     <tr>
                        <td><span style="padding-left:25px;">Mr. Anand Desai<span></span></span></td>
                        <td class="text-center">25.50</td>
                     </tr>
                     <tr>
                        <td><span style="padding-left:25px;">Rehash Industrial and Resins Chemicals Pvt Ltd</span></td>
                        <td class="text-center">6.80</td>
                     </tr>
                     <tr>
                        <td><span style="padding-left:25px;">Ms. Mona Anand Desai</span></td>
                        <td class="text-center">5.30</td>
                     </tr>
                     <tr>
                        <td><span style="padding-left:25px;">Other promoters</span></td>
                        <td class="text-center">0.80</td>
                     </tr>
                     <tr>
                        <td><strong>Mr. Milan Thakkar</strong></td>
                        <td class="text-center"><strong>25.60</strong></td>
                     </tr>
                     <tr>
                        <td><strong>Kiran Pallavi Investments LLC</strong></td>
                        <td class="text-center"><strong>36.00</strong></td>
                     </tr>
                     <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-center"><strong>100.00</strong></td>
                     </tr>
                  </tbody>
               </table>
               <br>       
               <p><strong>Company has the following fixed annual contracts with the following MNCs:</strong></p>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th width="20%">Name of Customer</th>
                        <th width="20%">Period</th>
                        <th width="35%">Started From</th>
                        <th width="25%">Annual Turnover(Take and Pay)</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Syngenta</td>
                        <td>Till 2024</td>
                        <td>Syngenta is a customer to the client 
                           since 2006. Since FY19 the company 
                           has increased from one product to 3 
                           products to the client
                        </td>
                        <td>Rs.1200 Mn</td>
                     </tr>
                     <tr>
                        <td>BASF</td>
                        <td>Till 2022</td>
                        <td>BASF is a customer to the company 
                           since 2012. 3 products with the client
                        </td>
                        <td>Rs.800 Mn</td>
                     </tr>
                     <tr>
                        <td>Sumitomo</td>
                        <td>Till 2024</td>
                        <td>Sumitomo was acquired in FY20 but 
                           the first full year of operations started 
                           in FY19
                        </td>
                        <td>Rs.1500 Mn</td>
                     </tr>
                     <tr>
                        <td>Others</td>
                        <td>Yearly contracts with auto renewals</td>
                        <td>Customer bases such as Mylan, Dow,Mitsui, Aticus etc.</td>
                        <td>Rs. 3000 Mn</td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">External Rating<br><br>Rated by CRISIL A-/Stable/A2+ (upgraded from CRISIL BBB+/ Stable/A2) on Oct 04, 2019<br><br>Rating rationale of Anupam Rasayan India Limited :</h5>
               <p>CRISIL has upgraded its ratings on the bank facilities of Anupam Rasayan India Limited (ARIL) to 'CRISIL A-
                  /Stable/CRISIL A2+' from 'CRISIL BBB+/Stable/CRISIL A2'.<br>
                  The upgrade reflects CRISIL's expectation of sustained revenue growth, healthy operating margin, and substantial 
                  cash accrual over the medium term. This would be supported by healthy order flows, increasing customer base due 
                  to an improved product mix, and shift of focus to high-value-added products following continuous capital 
                  expenditure (capex) over the past three fiscals.<br>
                  Revenue increased to around Rs 512 crore in fiscal 2019 from Rs 346 crore in the fiscal 2018, while the operating 
                  margin remained healthy at around 22% and is expected to be maintained over the medium term. The rising cash 
                  accrual has strengthened the financial risk profile. The networth and gearing were Rs 510 crore and 0.86 time, 
                  respectively, as on March 31, 2019, and the debt protection metrics were healthy, reflected in interest coverage and 
                  net cash accrual to total debt (NCATD) ratios of 4.60 times and 0.18 time, respectively, for fiscal 2019. The metrics 
                  are expected to improve over the medium term.<br>
                  The ratings continue to reflect an established position in the specialty chemicals business, a strong relationship with 
                  large global customers, and a healthy financial risk profile. These strengths are partially offset by sizeable working 
                  capital requirement, and susceptibility to volatility in foreign exchange (forex) rates, economic downturns, and 
                  intense competition from global players.
               </p>
               <h5 class="mt-3">Standalone Financials of ARIL:</h5>
               <table id="invoice_history" class="table   no-footer overview-table " role="grid" aria-describedby="invoice_history_info" cellpadding="0" cellspacing="0">
                  <thead>
                     <tr>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="15%">Particulars</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="13%">2017</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="13%">2018</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="13%">2019</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="13%">2020</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="13%">2021</th>
                        <th class="sorting_asc text-center" tabindex="0" aria-controls="invoice_history" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No: activate to sort column descending" width="20%">Remarks based on FY19</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td></td>
                        <td class=" text-center"><strong>Aud.</strong></td>
                        <td class=" text-center"><strong>Aud.</strong></td>
                        <td class=" text-center"><strong>Aud.</strong></td>
                        <td class=" text-center"><strong>Proj.</strong></td>
                        <td class=" text-center"><strong>Proj.</strong></td>
                        <td></td>
                     </tr>
                     <tr>
                        <td>Net Sales</td>
                        <td class=" text-center">2883.12</td>
                        <td class=" text-center">3480.65</td>
                        <td class=" text-center">5133.24</td>
                        <td class=" text-center">6759.81</td>
                        <td class=" text-center">1051.97</td>
                        <td rowspan="2">Refer Notes</td>
                     </tr>
                     <tr>
                        <td>Net Sales Growth</td>
                        <td class=" text-center"></td>
                        <td class=" text-center">20.72%</td>
                        <td class=" text-center">47.48%</td>
                        <td class=" text-center">31.69%</td>
                        <td class=" text-center">55.62%</td>
                     </tr>
                     <tr>
                        <td>Non-operating Income</td>
                        <td class=" text-center">109.11</td>
                        <td class=" text-center">11.10</td>
                        <td class=" text-center">72.65</td>
                        <td class=" text-center">-</td>
                        <td class=" text-center">-</td>
                        <td>Majorly includes export benefit, interestincome &amp; excise duty drawback income</td>
                     </tr>
                     <tr>
                        <td>PBDIT</td>
                        <td class=" text-center">703.32</td>
                        <td class=" text-center">801.45</td>
                        <td class=" text-center">1052.95</td>
                        <td class=" text-center">1474.42</td>
                        <td class=" text-center">2327.87</td>
                        <td rowspan="2">Refer Notes</td>
                     </tr>
                     <tr>
                        <td>PBDIT / Net Sales (%)</td>
                        <td class=" text-center">24.39%</td>
                        <td class=" text-center">23.03%</td>
                        <td class=" text-center">20.51%</td>
                        <td class=" text-center">21.81%</td>
                        <td class=" text-center">22.13%</td>
                     </tr>
                     <tr>
                        <td>Interest</td>
                        <td class=" text-center">211.24</td>
                        <td class=" text-center"> 145.64</td>
                        <td class=" text-center">243.09</td>
                        <td class=" text-center">316.17</td>
                        <td class=" text-center">505.23</td>
                        <td class=" text-center"></td>
                     </tr>
                     <tr>
                        <td>Interest Coverage = PBIDT/Interest</td>
                        <td class=" text-center">3.33</td>
                        <td class=" text-center"> 5.50</td>
                        <td class=" text-center">4.33</td>
                        <td class=" text-center">4.66</td>
                        <td class=" text-center">4.61</td>
                        <td class=" text-center">Acceptable</td>
                     </tr>
                     <tr>
                        <td>Depreciation</td>
                        <td class=" text-center">153.75</td>
                        <td class=" text-center">175.56</td>
                        <td class=" text-center">225.12</td>
                        <td class=" text-center">388.92</td>
                        <td class=" text-center">525.80</td>
                        <td class=" text-center"></td>
                     </tr>
                     <tr>
                        <td>Net Profit</td>
                        <td class=" text-center">347.90</td>
                        <td class=" text-center">398.79</td>
                        <td class=" text-center">502.33</td>
                        <td class=" text-center">630.86</td>
                        <td class=" text-center">1027.74</td>
                        <td class=" text-center" rowspan="2">Refer Notes</td>
                     </tr>
                     <tr>
                        <td>Net Profit / Net Sales (%)</td>
                        <td class=" text-center">12.07%</td>
                        <td class=" text-center">11.46%</td>
                        <td class=" text-center">9.79%</td>
                        <td class=" text-center">9.33%</td>
                        <td class=" text-center">9.77%</td>
                     </tr>
                     <tr>
                        <td>Cash Profit</td>
                        <td class=" text-center">501.65</td>
                        <td class=" text-center">574.35</td>
                        <td class=" text-center">727.45</td>
                        <td class=" text-center"> 1019.78</td>
                        <td class=" text-center"> 1553.54</td>
                        <td class=" text-center"> </td>
                     </tr>
                     <tr>
                        <td>Cash Profit / Net Sales (%)</td>
                        <td class=" text-center">17.40%</td>
                        <td class=" text-center">16.50%</td>
                        <td class=" text-center">14.17%</td>
                        <td class=" text-center"> 15.09%</td>
                        <td class=" text-center"> 14.77%</td>
                        <td class=" text-center"> </td>
                     </tr>
                     <tr>
                        <td>DSCR</td>
                        <td class=" text-center">3.37</td>
                        <td class=" text-center">1.53</td>
                        <td class=" text-center">1.42</td>
                        <td class=" text-center">1.59</td>
                        <td class=" text-center">1.99</td>
                        <td class=" text-center"> Acceptable</td>
                     </tr>
                     <tr>
                        <td>Debt/PBIDT</td>
                        <td class=" text-center">4.44</td>
                        <td class=" text-center">5.30</td>
                        <td class=" text-center">6.29</td>
                        <td class=" text-center">4.83</td>
                        <td class=" text-center">2.80</td>
                        <td class=" text-center"> </td>
                     </tr>
                     <tr>
                        <td>Total outside liabilities</td>
                        <td class=" text-center">3825.50</td>
                        <td class=" text-center">5330.79</td>
                        <td class=" text-center">8074.10</td>
                        <td class=" text-center">8477.01</td>
                        <td class=" text-center">8727.92</td>
                        <td class=" text-center"> </td>
                     </tr>
                     <tr>
                        <td>Total fixed assets</td>
                        <td class=" text-center">3949.46</td>
                        <td class=" text-center">6141.81</td>
                        <td class=" text-center">8515.29</td>
                        <td class=" text-center">9159.04</td>
                        <td class=" text-center">9133.24</td>
                        <td class=" text-center"> </td>
                     </tr>
                     <tr>
                        <td>Tangible Net Worth</td>
                        <td class=" text-center">2443.25</td>
                        <td class=" text-center">4412.69</td>
                        <td class=" text-center">4932.49</td>
                        <td class=" text-center">6045.64</td>
                        <td class=" text-center">7073.38</td>
                        <td class=" text-center">Refer Notes </td>
                     </tr>
                     <tr>
                        <td>TOL/TNW</td>
                        <td class=" text-center">1.57</td>
                        <td class=" text-center">1.21</td>
                        <td class=" text-center">1.64</td>
                        <td class=" text-center">1.40</td>
                        <td class=" text-center">1.23</td>
                        <td class=" text-center">Acceptable </td>
                     </tr>
                     <tr>
                        <td>Cash and bank balance</td>
                        <td class=" text-center">120.37</td>
                        <td class=" text-center">89.30</td>
                        <td class=" text-center">69.22</td>
                        <td class=" text-center">159.75</td>
                        <td class=" text-center">121.23</td>
                        <td class=" text-center"></td>
                     </tr>
                     <tr>
                        <td>Current Investments</td>
                        <td class=" text-center">-</td>
                        <td class=" text-center">-</td>
                        <td class=" text-center">-</td>
                        <td class=" text-center">130.00</td>
                        <td class=" text-center">130.00</td>
                        <td class=" text-center"></td>
                     </tr>
                     <tr>
                        <td>Receivable Days</td>
                        <td class=" text-center">71</td>
                        <td class=" text-center">94</td>
                        <td class=" text-center">88</td>
                        <td class=" text-center">78</td>
                        <td class=" text-center">61</td>
                        <td class=" text-center"></td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Notes:</h5>
               <ul class="pl-3">
                  <li><i class="fa fa-check" aria-hidden="true"></i> Cash profit = PAT + Depreciation + Non-operating non-cash outflow items – Provisions</li>
                  <li><i class="fa fa-check" aria-hidden="true"></i> Total Outside liabilities = Current Liabilities + Term Liabilities</li>
                  <li><i class="fa fa-check" aria-hidden="true"></i> Net Worth = Share Capital + Reserves – Revaluation reserve</li>
               </ul>
               <h5 class="mt-4">Notes:</h5>
               <p><strong>1. Revenue:</strong> The company is engaged into manufacturing and trading of industrial chemicals. It derived majority 
                  of revenue from sale of products through exports (60%) to various countries namely Europe, North America and 
                  other regions and domestic sales (40%). Revenue has increased during FY19 on account of addition of new 
                  products and increase in volumes of the existing product lines. Segmental sale is given below :
               </p>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th width="50%">Segment</th>
                        <th width="50%">% of sales to the End Industry</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Agro Chemical</td>
                        <td>70%</td>
                     </tr>
                     <tr>
                        <td>Pharmaceuticals</td>
                        <td>10%</td>
                     </tr>
                     <tr>
                        <td>Polymers &amp; Personal Care</td>
                        <td>10%</td>
                     </tr>
                     <tr>
                        <td>Other</td>
                        <td>10%</td>
                     </tr>
                     <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>100%</strong></td>
                     </tr>
                  </tbody>
               </table>
               <br>
               <p><strong>2. Profitability :</strong> During FY19, PBDIT margins of the company have slightly deteriorated over last year on account 
                  of increase in material cost and other expenses (majorly includes repairs and maintenance cost, rent, rates and 
                  January 10, 2020
                  8 | C F P L
                  taxes , consultancy charges and others) as a percent of sale and addition of new products which have not 
                  achieved the desired capacity utilisation(above 65%).
               </p>
               <p><strong>3. Projection:</strong> Company has projected to achieve Rs. 6760 Mn by FY20 against which it has recorded Rs. 4000 Mn 
                  by December 31, 2019 and is expecting to achieve balance in the last quarter with the commissioning of the two 
                  Sumitomo Plants which will contribute additional Rs. 250 MN -Rs. 300 Mn revenue per month.
               </p>
               <div class="row">
                  <div class="col-md-9">
                     <h5 class="mt-4">YTD Performance (Refers to period April 01, 2019 to Sept 30, 2019)</h5>
                  </div>
                  <div class="col-md-3">
                     <h5 class="mt-4">Rs in Mn</h5>
                  </div>
               </div>
               <table class="table table-bordered ">
                  <tbody>
                     <tr>
                        <td width="50%"><strong>Net Sales<strong></strong></strong></td>
                        <td width="50%"><strong>2482.30*<strong></strong></strong></td>
                     </tr>
                     <tr>
                        <td><strong>PBDIT<strong></strong></strong></td>
                        <td><strong>551.30<strong></strong></strong></td>
                     </tr>
                     <tr>
                        <td><strong>PAT<strong></strong></strong></td>
                        <td><strong>242.50<strong></strong></strong></td>
                     </tr>
                     <tr>
                        <td><strong>Total Debt<strong></strong></strong></td>
                        <td><strong>4767.00<strong></strong></strong></td>
                     </tr>
                  </tbody>
               </table>
               <p>*ARIL has achieved 40% of the sales in the first half of the year and balance 60% is achieved in next half as the 
                  company majorly caters to the agro based companies whose buying pattern is skewed to the second half on account 
                  of the crop season.
               </p>
               <h5 class="mt-4">Debt Position as on March 31, 2019:</h5>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th width="50%">Particulars</th>
                        <th width="50%">Rs in Mn</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>Long term borrowings*</td>
                        <td>4552.10</td>
                     </tr>
                     <tr>
                        <td>Short term borrowings</td>
                        <td>1538.80</td>
                     </tr>
                  </tbody>
               </table>
               <p>*Includes long term ECB loan from Pref Shareholders (Kiran Pallavi Investments LLC) of Rs. 2343.70 Mn</p>
               <h5 class="mt-4">Debt breakup for next five years: Rs in Mn</h5>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th>Particulars</th>
                        <th>FY18</th>
                        <th>FY19</th>
                        <th>FY20</th>
                        <th>FY21</th>
                        <th>FY22</th>
                        <th>FY23</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>LT – Term loan</td>
                        <td>2684.2</td>
                        <td>2208.4</td>
                        <td>2201.2</td>
                        <td>1596.6</td>
                        <td>1080.7</td>
                        <td>576.6</td>
                     </tr>
                     <tr>
                        <td>LT- loan from pref. shareholder*</td>
                        <td>-</td>
                        <td>2343.7</td>
                        <td>2340</td>
                        <td>1803.8</td>
                        <td>1218.7</td>
                        <td>633.8</td>
                     </tr>
                     <tr>
                        <td>ST- loan from bank</td>
                        <td>1122.4</td>
                        <td>1538.8</td>
                        <td>2000</td>
                        <td>2000</td>
                        <td>2000</td>
                        <td>2000</td>
                     </tr>
                     <tr>
                        <td>Repayment</td>
                        <td>441</td>
                        <td>530.4</td>
                        <td>574.3</td>
                        <td>1123.8</td>
                        <td>1100.8</td>
                        <td>1089.1</td>
                     </tr>
                     <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>4247.6</strong></td>
                        <td><strong>6621.3</strong></td>
                        <td><strong>7115.5</strong></td>
                        <td><strong>6524.2</strong></td>
                        <td><strong>5400.2</strong></td>
                        <td><strong>4299.5</strong></td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">DSCR Sensitization scenario:</h5>
               <ul class="p-0">
                  <li>- Constant Net profit at Rs 900 Mn (9% of FY21 Sales) till FY24. (no increase in sales but repayments of loans as 
                     per regular schedule to continue)
                  </li>
                  <li>- DSCR is above par in the pessimistic scenario.</li>
               </ul>
               <table class="table table-bordered overview-table">
                  <thead>
                     <tr>
                        <th>Particulars</th>
                        <th>31.03.2020</th>
                        <th>31.03.2021</th>
                        <th>31.03.2022</th>
                        <th>31.03.2023</th>
                        <th>31.03.2024</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td></td>
                        <td>(Estimated)</td>
                        <td>(Projected)</td>
                        <td>(Projected)</td>
                        <td>(Projected)</td>
                        <td>(Projected)</td>
                     </tr>
                     <tr>
                        <td>Net Profit after Tax</td>
                        <td>6308.56</td>
                        <td>9000</td>
                        <td>9000</td>
                        <td>9000</td>
                        <td>9000</td>
                     </tr>
                     <tr>
                        <td>Add: Depreciation</td>
                        <td>3889.18</td>
                        <td>5258.02</td>
                        <td>5258.02</td>
                        <td>5258.02</td>
                        <td>5258.02</td>
                     </tr>
                     <tr>
                        <td>Add: Interest on Loan Facility</td>
                        <td>3161.66</td>
                        <td>5052.39</td>
                        <td>5558.71</td>
                        <td>4770.25</td>
                        <td>4172.22</td>
                     </tr>
                     <tr>
                        <td><strong>Total (A)</strong></td>
                        <td><strong>13359.40</strong></td>
                        <td><strong>19310.41</strong></td>
                        <td><strong>19816.73</strong></td>
                        <td><strong>19028.27</strong></td>
                        <td><strong>18430.23</strong></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                     </tr>
                     <tr>
                        <td>Repayment of term Borrowings</td>
                        <td>5303.71</td>
                        <td>5743.46</td>
                        <td>11237.98</td>
                        <td>11008.12</td>
                        <td>10890.51</td>
                     </tr>
                     <tr>
                        <td>Add : Interest on Loan Facility</td>
                        <td>3161.66</td>
                        <td>5052.39</td>
                        <td>5558.71</td>
                        <td>4770.25</td>
                        <td>4172.22</td>
                     </tr>
                     <tr>
                        <td><strong>Total (B)</strong></td>
                        <td><strong>8465.37</strong></td>
                        <td><strong>10795.84</strong></td>
                        <td><strong>16796.69</strong></td>
                        <td><strong>15778.37</strong></td>
                        <td><strong>15062.73</strong></td>
                     </tr>
                     <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                     </tr>
                     <tr>
                        <td><strong>DSCR (A/B)</strong></td>
                        <td><strong>1.58</strong></td>
                        <td><strong>1.79</strong></td>
                        <td><strong>1.18</strong></td>
                        <td><strong>1.21</strong></td>
                        <td><strong>1.22</strong></td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Contingent Liabilities and Auditors Observations as on March 31, 2019:</h5>
               <p>Nil as on March 31, 2019.</p>
               <h5 class="mt-4">Risk Comments:</h5>
               <h5 class="mt-2"><small>Deal Positives:</small></h5>
               <table class="table table-bordered ">
                  <tbody>
                     <tr>
                        <td width="50%"><strong>Long track record of the company and experienced management</strong></td>
                        <td width="50%">Anupam Rasayan India Limited was incorporated on Sept 30, 2003 and has a 
                           vintage of 16 years indicating stability and ability to sustain the business cycles. 
                           The company is promoted by Mr. Anand Desai who possess around two decades 
                           of experiences in present line of business. He looks after exports, marketing, 
                           customer relations &amp; Public relations of the company . Thus company is supported 
                           by strong and experienced management which helps the company steer through 
                           operational hurdle.
                        </td>
                     </tr>
                     <tr>
                        <td><strong>External credit rating<strong></strong></strong></td>
                        <td>The company is rated by CRISIL at CRISIL A-/Stable/A2+ (upgraded from CRISIL 
                           BBB+/ Stable/A2) on Oct 04, 2019 indicating acceptable creditworthiness.
                        </td>
                     </tr>
                     <tr>
                        <td><strong>Acceptable financial metrics<strong></strong></strong></td>
                        <td>Company has acceptable financial metrics during FY19 reflected at PBDIT margins 
                           at 18.79%, PAT margins at 10%, TOL/TNW at 1.64x, DSCR at 1.42x.
                        </td>
                     </tr>
                     <tr>
                        <td><strong>Established clients<strong></strong></strong></td>
                        <td>The company deal with reputed clients in chemical industry namely BASF, 
                           Syngenta, Mitsui &amp; Co. Ltd , Bayer CropScience, Cadilla Pharmaceuticals Ltd, 
                           Dupont, Dow, Sumitomo Chemical, Atticus, Devis Laboratories Ltd and others.
                        </td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-2"><small>Deal Negatives:</small></h5>
               <table class="table table-bordered ">
                  <tbody>
                     <tr>
                        <td width="50%"><strong>Competition</strong></td>
                        <td width="50%">The company operates in highly fragmented and competitive industry. Any 
                           change in price may affect the profitability margins of the company. There is a 
                           significant competition from Global players in the agro chemical industry; 
                           particularly from China; also limits the bargaining power.
                        </td>
                     </tr>
                     <tr>
                        <td><strong>Forex risk<strong></strong></strong></td>
                        <td>ARIL generates 60% revenue from exports to Europe, North America and other 
                           regions. However, the risk can be mitigated of forex risk by adopting hedging 
                           strategy namely forward contracts and others.
                        </td>
                     </tr>
                     <tr>
                        <td><strong>High Debt/PBDIT<strong></strong></strong></td>
                        <td>The capital structure of the company stood leveraged reflected at its Debt/PBDIT 
                           at 6.29 times during FY19. To mitigate this risk; company has completed debt 
                           funded capex last year. Debt/PBDIT will improve from FY20 onwards on account 
                           of scheduled repayment of long term loans and by achieving the projected 
                           revenue of ~Rs. 6000 Mn during FY20. Client is not expected to borrow any 
                           further from the current levels.
                        </td>
                     </tr>
                  </tbody>
               </table>
               <h5 class="mt-4">Recommendation:</h5>
               <p>The deal is recommended for rental facility of Rs. 40 Mn on the basis of established clients, external credit rating 
                  and acceptable financial metrics of the company.
               </p>
            </div>
         </div>
      </div>
   </div>
</div>









</div>
@endsection
