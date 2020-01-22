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

                     <a class="btn btn-success btn-sm" href="javascript:void(0)"  data-toggle="modal" data-target="#uploadXLSXdoc" data-url ="{{route('upload_xlsx_document', ['app_id' => request()->get('app_id'), 'file_type' => 'banking']) }}" data-height="150px" data-width="100%">Upload XLSX</a>
                  @endif 
                  @if(!empty($pending_rec) && $pending_rec['status'] == 'fail')
                     @php $class_enable="disabled"; @endphp
                     <a class="btn btn-success btn-sm process_stmt" pending="{{ $pending_rec['biz_perfios_id'] }}" href="javascript:void(0)">Process</a>
                  @endif
                  <a href="javascript:void(0)" class="btn btn-success btn-sm hide" biz_perfios_id="" id="getReport">Get Report</a>
                  @if(request()->get('view_only') && $bankdocs->count() > 0)
                     <a href="javascript:void(0)" class="btn btn-success btn-sm <?php echo $class_enable ?>">Get Analysis</a>
                  @endif   
                  </div> 
                  <div class="clearfix"></div>
                  <br/>
                  <hr>
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
                     <div class="clearfix"></div>
                     <br/>
                  <h2 class="sub-title mt-4">Banking Analysis</h2>
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
                           <tr>
                              <td>&nbsp;</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Credit Entries    (Without Returns)</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >I/W Returns</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Cash Deposit</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Salary</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Loan Credit</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Other Credits</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Contra Credits (Counter of O/W Returns)</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Credit Entries (With Returns)</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Debit Entries    (Without Returns)</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >O/W Returns</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Bouncing Charges</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Cash Withdrawl</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >EMI</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Tax Payments</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Interest    Debited</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Charges</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Other Debits</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Contra Debits (Counter of I/W Returns)</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Debit Entries (With Returns)</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Opening    Balance</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Closing    Balance</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Cheque Issued</td>
                           </tr>
                           <tr>
                              <td>Count</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7">Cheque Percentage</td>
                           </tr>
                           <tr>
                              <td>Value(Percentage Of Number)</td>
                              <td></td>
                           </tr>
                           <tr>
                              <td>Value(Percentage Of Value)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Lifestyle</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                           <tr bgcolor="#f2f2f2">
                              <td colspan="7" >Investment Debit</td>
                           </tr>
                           <tr>
                              <td>Value    (in Lakhs)</td>
                              <td></td>
                           </tr>
                        </tbody>
                     </table>
                     <div class="clearfix"></div>
                  </div>
                  <div class="data mt-4">
                     <h2 class="sub-title bg">Details of Banking Relationships</h2>
                     <div class="pl-4 pr-4 pb-4 pt-2">
                        <p ><b>A. Working Capital Facility: </b></p>
                        <table class="table table-bordered overview-table" id="myTable3">
                           <thead>
                              <tr bgcolor="#ccc">
                                 <th style="vertical-align: top;">Name of Bank/ NBFC</th>
                                 <th style="vertical-align: top;">Fund based Facility</th>
                                 <th style="vertical-align: top;">Facility Amount</th>
                                 <th style="vertical-align: top;">O/S as on
                                    <input type="text" name="" id="fund_facility_date" class="form-control" value="">
                                 </th>
                                 <th style="vertical-align: top;">Non-fund based Facility</th>
                                 <th style="vertical-align: top;">Facility Amount</th>
                                 <th style="vertical-align: top;">O/S as on
                                    <input type="text" name="" id="non_fund_facility_date" class="form-control" value="">
                                 </th>
                                 <th style="vertical-align: top;">Length of Relationship</th>
                              </tr>
                           </thead>
                           <tbody id="working_capital_facility">
                           </tbody>
                           <thead>
                              <tr>
                                 <td>TOTAL</td>
                                 <td></td>
                                 <td id="total_facility_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">0</td>
                                 <td id="total_os" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">0</td>
                                 <td></td>
                                 <td id="total_non_facility_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">0</td>
                                 <td id="total_non_total_os" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">0</td>
                                 <td></td>
                              </tr>
                           </thead>
                        </table>
                        @if(request()->get('view_only'))
                        <button class="btn btn-success pull-right btn-sm mt-3"> + Add Row</button>
                        @endif
                        <div class="clearfix"></div>
                        <p class="mt-3">
                           <b>B. Term Loans &amp; Business Loans: </b>
                        </p>
                        <table class="table table-bordered overview-table" id="myTable8">
                           <thead>
                              <tr bgcolor="#ccc">
                                 <th style="vertical-align: top;">Name of Bank/ NBFC</th>
                                 <th style="vertical-align: top;">Facility</th>
                                 <th style="vertical-align: top;">Facility Amount</th>
                                 <th style="vertical-align: top;">O/S as on
                                    <input type="text" name="" id="loans_date" class="form-control" value="">
                                 </th>
                                 <th style="vertical-align: top;">Non-fund based Facility</th>
                              </tr>
                           </thead>
                           <tbody id="loans_cam">
                           </tbody>
                           <thead>
                              <tr>
                                 <td>TOTAL</td>
                                 <td></td>
                                 <td id="loans_total_facility_amt" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">0</td>
                                 <td id="loans_total_facility_os" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">0</td>
                                 <td></td>
                              </tr>
                           </thead>
                        </table>
                        @if(request()->get('view_only'))
                        <button class="btn btn-success pull-right btn-sm mt-3"> + Add Row</button>
                        @endif
                        <div class="clearfix"></div>
                     </div>
                  </div>
                  <div class="data mt-4">
                     <h2 class="sub-title bg">Inter-Group Transactions:</h2>
                     <div class="pl-4 pr-4 pb-4 pt-2">
                        <table class="table overview-table" id="myTable6">
                           <thead>
                              <tr>
                                 <td>Sister Concern</td>
                                 <td>Nature of Transaction</td>
                                 <td>
                                    <select class="form-control form-control-select" id="deb_first_years">
                                       <option value="">Select</option>
                                       <option value="2017">2017</option>
                                       <option value="2018">2018</option>
                                       <option value="2019">2019</option>
                                    </select>
                                    <small>Amount (Rs Lakh)</small>
                                 </td>
                              </tr>
                           </thead>
                           <tbody id="inter_group_transaction">
                           </tbody>
                        </table>
                        @if(request()->get('view_only'))
                        <button class="btn btn-success pull-right btn-sm mt-3"> + Add Row</button>
                        @endif
                        <div class="clearfix"></div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 mt-3">
                        <div class="form-group text-right">
                           @if(request()->get('view_only')) 
                           <button  class="btn btn-success btn-sm btn-ext submitBtnBank" data-toggle="modal" data-target="#myModal">Submit</button>                                        
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
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
@endsection
