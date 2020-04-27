@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-sidebar')
@include('layouts.backend.partials.admin-subnav')

<?php 
   $class_enable = 'getAnalysis';
?>

<div class="content-wrapper">
   @include('layouts.backend.partials.cam_nav')
   <div class="inner-container">
      <div class="card mt-4">
         <div class="card-body ">
            <div id="pullMsg"></div>
            <div class="data">
               <h2 class="sub-title bg mb-4">Last 6 months Bank statement-</h2>
               <div class="clearfix"></div>
               <div class="pl-4 pr-4 pb-4 pt-2">
                  @if($bankdocs->count() > 0)
                     @foreach($bankdocs as $bankdoc)
                  <div class="doc"  style="text-align: center;" id="bank_doc_{{$bankdoc->app_doc_file_id}}">
                     <small>{{ $bankdoc->doc_name }}</small>
                     <ul>
                        <li><span class="icon"><i class="fa fa-file-pdf-o"></i></span></li>
                        <li><a href="{{ Storage::url($bankdoc->file_path) }}" download target="_blank">Download Bank Statement</a></li>
                        <li>
                             <a href="javascript:void(0)" data-toggle="modal" data-target="#uploadBankDocument" data-url ="{{route('upload_bank_document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'app_doc_file_id' => $bankdoc->app_doc_file_id,'doc_id' => $bankdoc->doc_id]) }}" data-height="300px" data-width="100%" class="hide" id="reprocess_{{$bankdoc->app_doc_file_id}}">Re-Process</a>
                        </li>
                     </ul>
                  </div>
                     @endforeach
                  @endif
                  <div class="clearfix"></div>

                  <div style="text-align: right;">                  
                  @if(!empty($active_json_filename) && file_exists(storage_path("app/public/user/docs/$appId/banking/".$active_xlsx_filename)))
                     <a class="btn btn-success btn-sm" href="{{ Storage::url('user/docs/'.$appId.'/banking/'.$active_xlsx_filename) }}" download>Download</a>
                     @can('upload_xlsx_document')
                     <a class="btn btn-success btn-sm" href="javascript:void(0)"  data-toggle="modal" data-target="#uploadXLSXdoc" data-url ="{{route('upload_xlsx_document', ['app_id' => request()->get('app_id'), 'file_type' => 'banking']) }}" data-height="150px" data-width="100%">Upload XLSX</a>
                     @endcan
                  @endif 
                  @if(!empty($pending_rec) && $pending_rec['status'] == 'fail')
                     @php $class_enable="disabled"; @endphp
                     <a class="btn btn-success btn-sm process_stmt" pending="{{ $pending_rec['biz_perfios_id'] }}" href="javascript:void(0)">Process</a>
                  @endif
                  <a href="javascript:void(0)" class="btn btn-success btn-sm hide" biz_perfios_id="" id="getReport">Get Report</a>
                  @if(request()->get('view_only') && $bankdocs->count() > 0)
                    @can('getAnalysis')
                     <a href="javascript:void(0)" class="btn btn-success btn-sm <?php echo $class_enable ?>">Get Analysis</a>
                     @endcan
                  @endif   
                  </div> 
                  <div class="clearfix"></div>
                  <br/>
                  <hr>
                  <div class="clearfix"></div>
                  <div class=" pb-4 pt-2">
                     <table cellspacing="0" cellpadding="0" class="table overview-table">
                        <tbody>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="3" style="font-size:18px;">Bank Summary </td>
                           </tr>
                           <tr>
                              <td style="border-right:0px;">
                                 <p style="margin-bottom: 0.5rem;margin-top: 0.5rem;"><b>Name :</b> &nbsp; {{ !empty($customers_info) ? $customers_info[0]['name'] : '' }}</p>
                                  <p style="margin-bottom: 0.5rem;"><b>Email :</b>  &nbsp; {{ !empty($customers_info) ? strtolower($customers_info[0]['email']) : '' }}</p>
                              </td>
                              <td  style="border-left:0px;">
                                 <p style="margin-bottom: 0.5rem;margin-top: 0.5rem;"><b>Account Number :</b>  &nbsp; {{ !empty($customers_info) ? $customers_info[0]['account_no'] : '' }}</p>
                                 <p style="margin-bottom: 0.5rem;"><b>Mobile :</b>  &nbsp; {{ !empty($customers_info) ? strtolower($customers_info[0]['mobile']) : '' }}</p>
                              </td>
                               <td  style="border-left:0px;">
                                  <p style="margin-bottom: 0.5rem;"><b>Bank Name :</b>  &nbsp; {{ !empty($customers_info) ? $customers_info[0]['bank'] : '' }}</p>
                                 <p style="margin-bottom: 0.5rem;"><b>Pan :</b>  &nbsp; {{ !empty($customers_info) ? strtolower($customers_info[0]['pan']) : '' }}</p>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                     <div class="clearfix"></div>
                  </div>
                  <div id="paginate">
                        <?php 
                           echo $xlsx_pagination;
                        ?>
                  </div>
                  <div id="gridView">
                     <?php 
                        echo $xlsx_html;
                     ?>
                  </div>
               </div>
               <div class="clearfix"></div>
               <br/>
               <form method="POST" action="{{route('save_bank_detail')}}">
                  <input type="hidden" name="app_id" value="{{$appId}}">  
                  <input type="hidden" name="biz_id" value="{{$biz_id}}">  
                  <input type="hidden" name="bank_detail_id" value="{{$debtPosition->bank_detail_id ?? ''}}">  
                  @csrf
                  
                  <div class="mt-4">
                     <h2 class="sub-title bg">Banking Analysis</h2>
                     <table class="after-add-more-ba table-ba table table-striped no-footer overview-table" role="grid" cellpadding="0" cellspacing="0"> 
                        <thead>
                            <tr bgcolor="#ccc">
                               <th>Name of Bank</th>
                               <th>Account Type</th>
                               <th colspan="3">Utilization %</th>
                               <th colspan="4">Cheque Return</th>
                               <th colspan="2">Summations</th>
                               <th></th>
                            </tr>
                         </thead>
                         <tbody>
                         <tr bgcolor="#19c38f">
                            <td></td>
                            <td></td>
                            <td width="5%">Max </td>
                            <td width="5%">Min</td>
                            <td width="5%">Avg</td>
                            <td>Inward</td>
                            <td width="5%">% of total cheques presented</td>
                            <td>Outward</td>
                            <td width="5%">% of total cheques deposited  </td>
                            <td>Credit</td>
                            <td>Debit</td>
                            <td>Over drawings in last 6 months</td>
                        </tr> 

                        @if(isset($dataBankAna) && count($dataBankAna)>0)
                           @foreach($dataBankAna as $key =>$val)
                           <tr class="control-group-ba">
                              <td width="12%"><input maxlength="100" type="text" name="bank_name_ba[]" value="{{$val['bank_name']}}" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="10" type="text" name="act_type[]" value="{{$val['act_type']}}" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="5" type="text" name="uti_max[]" value="{{$val['uti_max']}}" onchange="maxPercent(this);" class="maxPercent numericOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="5" type="text" name="uti_min[]" value="{{$val['uti_min']}}" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="5" type="text" name="uti_avg[]" value="{{$val['uti_avg']}}" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="3" type="text" name="chk_inward[]" value="{{number_format($val['chk_inward'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="5" type="text" name="chk_presented_per[]" value="{{$val['chk_presented_per']}}" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="3" type="text" name="chk_outward[]" value="{{number_format($val['chk_outward'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="5" type="text" name="chk_deposited_per[]" value="{{$val['chk_deposited_per']}}" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="12" type="text" name="submission_credit[]" value="{{number_format($val['submission_credit'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="12" type="text" name="submission_debbit[]" value="{{number_format($val['submission_debbit'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="">
                                 <div class="input-group-btn">
                                    <input maxlength="12" type="text" name="overdrawing_in_six_month[]" value="{{$val['overdrawing_in_six_month']}}" class="numericOnly number_format col-md-8 form-control form-control-sm" />
                                    <i class="fa fa-times-circle remove-ptpq-block remove-ba"></i>
                                 </div>
                              </td>
                           </tr> 
                           @endforeach
                        @endif

                        <tr class="control-group-ba">
                           <td width="12%"><input maxlength="100" type="text" name="bank_name_ba[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="10" type="text" name="act_type[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="5" type="text" name="uti_max[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="5" type="text" name="uti_min[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="5" type="text" name="uti_avg[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="3" type="text" name="chk_inward[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="5" type="text" name="chk_presented_per[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="3" type="text" name="chk_outward[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="5" type="text" name="chk_deposited_per[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="12" type="text" name="submission_credit[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="12" type="text" name="submission_debbit[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="">
                              <div class="input-group-btn">
                                 <input maxlength="12" type="text" name="overdrawing_in_six_month[]" value="" class="numericOnly number_format col-md-8 form-control form-control-sm" />
                                 <i class="fa fa-plus-circle add-ptpq-block add-more-ba"></i>
                              </div>
                           </td>
                        </tr>    
                        </tbody>
                        <!-- Copy Fields -->
                        <tbody class="copy-ba hide">
                           <tr class="control-group-ba">
                                <td width="12%"><input maxlength="100" type="text" name="bank_name_ba[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="10" type="text" name="act_type[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="5" type="text" name="uti_max[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="5" type="text" name="uti_min[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="5" type="text" name="uti_avg[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="3" type="text" name="chk_inward[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="5" type="text" name="chk_presented_per[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="3" type="text" name="chk_outward[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="5" type="text" name="chk_deposited_per[]" value="" onchange="maxPercent(this);" class="numericOnly form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="12" type="text" name="submission_credit[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                                <td width="12%"><input maxlength="12" type="text" name="submission_debbit[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                                <td width="">
                                 <div class="input-group-btn">
                                    <input maxlength="12" type="text" name="overdrawing_in_six_month[]" value="" class="numericOnly number_format col-md-8 form-control form-control-sm" />
                                    <i class="fa fa-times-circle remove-ptpq-block remove-ba"></i>
                                 </div>
                                </td>
                           </tr> 
                        </tbody>
                     </table>
                  </div>
                  <div class="mt-4">
                     <h2 class="sub-title bg">Working Capital Facility</h2>
                     <table class="after-add-more table-wcf table table-striped no-footer overview-table" role="grid" cellpadding="0" cellspacing="0"> 
                        <tr role="row">
                           <td class="" tabindex="0" rowspan="1" colspan="1" width="12%">Name of Bank/NBFC</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Fund based Facility</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Facility Amount(Rs. Mn)</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">O/S as on <input type="text" class="col-md-10" value="{{$debtPosition->fund_ason_date ?? '' }}" name="fund_date" id="fund_date" placeholder="Date"> (Rs. Mn)</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Non-fund based Facility</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Facility Amount(Rs. Mn)</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">O/S as on <input type="text" class="col-md-10" value="{{$debtPosition->nonfund_ason_date ?? '' }}" name="nonfund_date" id="nonfund_date" placeholder="Date"> (Rs. Mn)</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Length of Relationship</td>
                        </tr>  

                        @if(isset($dataWcf) && count($dataWcf)>0)
                           @foreach($dataWcf as $key =>$val)
                           <tr class="control-group-wcf">
                              <td width="12%"><input maxlength="100" type="text" name="bank_name[]" value="{{$val['bank_name']}}" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="100" type="text" name="fund_facility[]" value="{{$val['fund_facility']}}" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%">&#8377;<input maxlength="20" type="text" name="fund_amt[]" value="{{number_format($val['fund_amt'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%">&#8377;<input maxlength="20" type="text" name="fund_os_amt[]" value="{{number_format($val['fund_os_amt'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="100" type="text" name="nonfund_facility[]" value="{{$val['nonfund_facility']}}" class="form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="nonfund_amt[]" value="{{number_format($val['nonfund_amt'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="nonfund_os_amt[]" value="{{number_format($val['nonfund_os_amt'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="">
                                 <div class="input-group-btn">
                                    <input maxlength="3" type="text" name="relationship_len[]" value="{{$val['relationship_len']}}" class="numericOnly col-md-8 form-control form-control-sm" />
                                    <i class="fa fa-times-circle remove-ptpq-block remove"></i>
                                 </div>
                              </td>
                           </tr> 
                           @endforeach
                        @endif

                        <tr class="control-group-wcf">
                           <td width="12%"><input maxlength="100" type="text" name="bank_name[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="100" type="text" name="fund_facility[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                           <td width="12%">&#8377; <input maxlength="20" type="text" name="fund_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%">&#8377; <input maxlength="20" type="text" name="fund_os_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="100" type="text" name="nonfund_facility[]" value="" class="form-control form-control-sm" /></td>
                           <td width="12%">&#8377; <input maxlength="20" type="text" name="nonfund_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%">&#8377; <input maxlength="20" type="text" name="nonfund_os_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="">
                              <div class="input-group-btn">
                                 <input maxlength="3" type="text" name="relationship_len[]" value="" class="numericOnly col-md-8 form-control form-control-sm" />
                                 <i class="fa fa-plus-circle add-ptpq-block add-more"></i>
                              </div>
                           </td>
                        </tr>    
                        
                        <!-- Copy Fields -->
                        <tbody class="copy hide">
                           <tr class="control-group-wcf">
                              <td width="12%"><input maxlength="100" type="text" name="bank_name[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="100" type="text" name="fund_facility[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="fund_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="fund_os_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="100" type="text" name="nonfund_facility[]" value="" class="form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="nonfund_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="nonfund_os_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="">
                                 <div class="input-group-btn">
                                    <input maxlength="3" type="text" name="relationship_len[]" value="" class="numericOnly col-md-8 form-control form-control-sm" />
                                    <i class="fa fa-times-circle remove-ptpq-block remove"></i>
                                 </div>
                              </td>
                           </tr> 
                        </tbody>
                     </table>
                  </div>
                  <div class="mt-4">
                     <h2 class="sub-title bg">Term Loans & Business Loans</h2>
                     <table class="after-add-more-tlbl table-wcf table table-striped no-footer overview-table" role="grid" cellpadding="0" cellspacing="0"> 
                        <tr role="row">
                           <td class="" tabindex="0" rowspan="1" colspan="1" width="20%">Name of the bank</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Loan name</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Facility amount(Mn)</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">O/S as On <input type="text" class="col-md-10" value="{{$debtPosition->tbl_fund_ason_date ?? '' }}" name="tbl_fund_date" id="tblfund_ason_date" placeholder="Date"> (Rs. Mn)</td>
                           <td class="" tabindex="0" rowspan="1" colspan="1">Length of Relationship</td>
                        </tr>  

                        @if(isset($dataTlbl) && count($dataTlbl)>0)
                           @foreach($dataTlbl as $key =>$val)
                           <tr class="control-group-tlbl">
                              <td width="12%"><input maxlength="100" type="text" name="bank_name_tlbl[]" value="{{$val['bank_name_tlbl']}}" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="100" type="text" name="loan_name[]" value="{{$val['loan_name']}}" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="facility_amt[]" value="{{number_format($val['facility_amt'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="facility_os_amt[]" value="{{number_format($val['facility_os_amt'])}}" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="">
                                 <div class="input-group-btn">
                                    <input maxlength="3" type="text" name="relationship_len_tlbl[]" value="{{$val['relationship_len_tlbl']}}" class="numericOnly col-md-8 form-control form-control-sm" />
                                    <i class="fa fa-times-circle remove-ptpq-block remove-post"></i>
                                 </div>
                              </td>
                           </tr>  
                           @endforeach
                        @endif

                        <tr class="control-group-tlbl">
                           <td width="12%"><input maxlength="100" type="text" name="bank_name_tlbl[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                           <td width="12%"><input maxlength="100" type="text" name="loan_name[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                           <td width="12%">&#8377; <input maxlength="20" type="text" name="facility_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="12%">&#8377; <input maxlength="20" type="text" name="facility_os_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                           <td width="">
                              <div class="input-group-btn">
                                 <input maxlength="3" type="text" name="relationship_len_tlbl[]" value="" class="numericOnly col-md-8 form-control form-control-sm" />
                                 <i class="fa fa-plus-circle add-ptpq-block add-more-post"></i>
                              </div>
                           </td>
                        </tr>    
                        
                        <!-- Copy Fields -->
                        <tbody class="copy-post hide">
                           <tr class="control-group-tlbl">
                              <td width="12%"><input maxlength="100" type="text" name="bank_name_tlbl[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%"><input maxlength="100" type="text" name="loan_name[]" value="" class="alphaOnly form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="facility_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="12%">&#8377; <input maxlength="20" type="text" name="facility_os_amt[]" value="" class="numericOnly number_format form-control form-control-sm" /></td>
                              <td width="">
                                 <div class="input-group-btn">
                                    <input maxlength="3" type="text" name="relationship_len_tlbl[]" value="" class="numericOnly col-md-8 form-control form-control-sm" />
                                    <i class="fa fa-times-circle remove-ptpq-block remove-post"></i>
                                 </div>
                              </td>
                           </tr> 
                        </tbody>
                     </table>
                  </div>                 
                  <div class="mt-4">
                     <h2 class="sub-title bg">Debt Position</h2>
                     <div class="pl-4 pr-4 pb-4 pt-2">
                        <div class="form-group row">
                         <label for="debt_on" class="col-sm-2 col-form-label">Date As On</label>
                         <div class="col-sm-4">
                           <input type="text" class="form-control" value="{{$debtPosition->debt_on ?? '' }}" name="debt_on" id="debt_on" placeholder="Select Date">
                         </div>
                        </div>
                        <textarea class="form-control form-control-sm" id="debt_position_comments" name="debt_position_comments" rows="3" spellcheck="false">{{$debtPosition->debt_position_comments ?? '' }}</textarea>
                     </div>
                 </div>
                 <br/>
                 <button type="submit" class="btn btn-success btn-sm float-right mt-2 mb-3"> Save</button>
              </form>
            </div>
         </div>
      </div>
   </div>
</div>
{!!Helpers::makeIframePopup('uploadBankDocument','Re-Upload Document', 'modal-md')!!}
{!!Helpers::makeIframePopup('uploadXLSXdoc','Upload XLSX Document', 'modal-md')!!}
@endsection
@section('jscript')
<script type="text/javascript">
   appId = '{{$appId}}';
   appurl = '{{URL::route("getAnalysis") }}';
   process_url = '{{URL::route("process_banking_statement") }}';
   _token = "{{ csrf_token() }}";
</script>
<script type="text/javascript">
    function maxPercent(input) {
        if (input.value < 0) input.value = 0;
        if (input.value > 100) input.value = 100;
      }
    $(document).on("change keypress", ".alphaOnly", function (e) {
        var regex = new RegExp("^[a-zA-Z ]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (!regex.test(str)) {
            return false;
        }
        return true;
    });
    $(document).on("change keypress", ".numericOnly", function (evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.\,]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
        {
          evt.preventDefault();
        }
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
            let mclass = result['status'] ? 'success' : 'danger';
            var html = '<div class="alert-'+ mclass +' alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+result['message']+'</div>';
            $("#pullMsg").html(html);
            if(result['errors']){
               $errors = result['errors'];
               Object.keys($errors).forEach(function(key) {
                  $('#reprocess_' + key).removeClass('hide');
                   $('#bank_doc_' + key).append('<small class="error" id="append_'+ key +'">'+ $errors[key] +'</small>');
               });
            }
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



   $(document).on('click', '#getReport', function(argument) {
      var biz_perfios_id = $(this).attr('biz_perfios_id');
      getReport(biz_perfios_id);
   })

    $(document).on('click', '.process_stmt', function(argument) {
      var biz_perfios_id = $(this).attr('pending');
      getReport(biz_perfios_id);
   })

    function getReport(biz_perfios_id) {
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
    }

      $(document).on('click','.pagination',function() {
         pageNo = $(this).attr('id');
         getExcel(pageNo);
    })

      function getresult(pageNo) {
          getExcel(pageNo);
      }

    function getExcel(page = 1) {
       var fileType = 'banking';
       data = {appId, page, _token, fileType};
       $.ajax({
          url  : '{{URL::route("getExcelSheet") }}',
          type :'POST',
          data : data,
          dataType : 'json',
          success:function(result) {
             $('#gridView').html(result.response.data);
             $('#paginate').html(result.response.paginate);
          },
          error:function(error) {

          },
          complete: function() {

          },
       })
    }
</script>
<script>
      var ckeditorOptions =  {
        filebrowserUploadUrl: "{{route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'file' ])}}",
        filebrowserUploadMethod: 'form',
        imageUploadUrl:"{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'image' ]) }}",
        disallowedContent: 'img{width,height};'
      };
    
   $('#debt_on, #fund_date, #nonfund_date, #tblfund_ason_date').datetimepicker({
     format: 'dd/mm/yyyy',
     pickTime: false,
     minView: 2, 
     pickerPosition: 'bottom-right', 
   }).on('changeDate', function(e){
       $(this).datetimepicker('hide');
   });
 CKEDITOR.replace('debt_position_comments', ckeditorOptions);

$(document).ready(function() {
   $(".add-more").click(function(){ 
      var html = $(".copy").html();
      $(".after-add-more").append(html);
   });

   $("body").on("click",".remove",function(){ 
      $(this).parents(".control-group-wcf").remove();
   });

   $(".add-more-post").click(function(){ 
      var html = $(".copy-post").html();
      $(".after-add-more-tlbl").append(html);
   });

   $("body").on("click",".remove-post",function(){ 
      $(this).parents(".control-group-tlbl").remove();
   });
   
   $(".add-more-ba").click(function(){ 
      var html = $(".copy-ba").html();
      $(".after-add-more-ba").append(html);
   });

   $("body").on("click",".remove-ba",function(){ 
      $(this).parents(".control-group-ba").remove();
   });
});
</script>
@endsection
