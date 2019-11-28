@extends('layouts.backend.admin-layout')

@section('content')
<style>
   .isloader{ 
                position: fixed;    
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,.6);
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-content: center;
                z-index: 9;
            }
</style>
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
 @include('layouts.backend.partials.cam_nav')
<div class="inner-container">
  <div class="card mt-4">
   <div class="card-body ">
      <div class="data">
       <!--
         <h2 class="sub-title bg mb-4"><span class=" mt-2">Company CIBIL</span> <button  class="btn btn-primary  btn-sm float-right"> Upload Document</button></h2>
         -->
         <div class="pl-4 pr-4 pb-4 pt-2">
            <div class="row mt-3">
               <div class="col-sm-12">
                  <table id="cibil-table" class="table table-striped  no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="cibil-table_info" style="width: 100%;">
                     <thead>
                        <tr role="row">
                           <th class="sorting_asc" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 118px;" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending">Sr.No.</th>
                           <th class="sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 162px;" aria-label="Company: activate to sort column ascending">Company</th>
                           <th class="sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 96px;" aria-label="PAN: activate to sort column ascending">PAN</th>
                           <th class="sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 105px;" aria-label="Rank: activate to sort column ascending">Rank</th>
                           <th class="numericCol sorting" tabindex="0" aria-controls="cibil-table" rowspan="1" colspan="1" style="width: 209px;" aria-label="Action: activate to sort column ascending">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                           @php
                              $i = 0;
                           @endphp
                       @foreach($arrCompanyDetail as $arr)
                              @php
                                 $i++;
                              @endphp
                              <tr role="row" class="odd">
                                 <td class="sorting_1" width="15%">{{$i}}</td>
                                 <td width="20%">{{$arr->biz_entity_name}}</td>
                                 <td width="20%">{{$arr->pan_gst_hash}}</td>
                                 <td width="20%"></td>
                                 <td class=" numericCol" width="25%">

                                   <!-- <button class="btn btn-success btn-sm" supplier="49" onclick="pull_cibil_org(this)">PULL</button>
                                    <button class="btn btn-warning btn-sm" supplier="49" onclick="pull_cibil_org(this)">DOWNLOAD</button>
                                    <button class="btn btn-info btn-sm" supplier="49" onclick="pull_cibil_org(this)">UPLOAD</button> -->
                                 </td>
                              </tr>
                        @endforeach
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
      <div class="data mt-4">
         <h2 class="sub-title bg mb-3">Director / Proprietor / Owner / Partner</h2>
         <div id="pullMsg"></div>
         <div class="pl-4 pr-4 pb-4 pt-2">
            <div class="row mt-3">
               <div class="col-sm-12">
                  <table id="cibil-dpop-table" class="table table-striped  no-footer overview-table"" cellspacing="0" width="100%" role="grid" aria-describedby="cibil-dpop-table_info" style="width: 100%;">
                     <thead>
                        <tr role="row">
                           <th class="sorting_asc" tabindex="0" width="15%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending">Sr.No.</th>
                           <th class="sorting" tabindex="0" width="20%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-label="Name: activate to sort column ascending">Name</th>
                           <th class="sorting" tabindex="0" width="20%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-label="PAN: activate to sort column ascending">PAN</th>
                           <th class="sorting" tabindex="0" width="20%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1" aria-label="Score: activate to sort column ascending">Score</th>
                           <th class="sorting" tabindex="0" width="25%" aria-controls="cibil-dpop-table" rowspan="1" colspan="1"  aria-label="Action: activate to sort column ascending">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                         @php
                              $i = 0;
                           @endphp
                       @foreach($arrCompanyOwnersData as $arr)
                              @php
                                 $i++;
                              @endphp
                              <tr role="row" class="odd">
                                 <td class="sorting_1" width="15%">{{$i}}</td>
                                 <td width="20%">{{$arr->first_name." ".$arr->last_name}}</td>
                                 <td width="20%">{{$arr->pan_gst_hash}}</td>
                                 <td width="20%" id="cibilScore{{$arr->biz_owner_id}}"></td>
                                 <td class=" numericCol" width="25%">
                                    <button class="btn btn-success btn-sm" id="cibilScoreBtn{{$arr->biz_owner_id}}" supplier="49" onclick="pull_cibil_promoterModal({{$arr->biz_owner_id}})">PULL</button>
                                    <button class="btn btn-warning btn-sm" supplier="49" onclick="downloadPromoterCibil({{$arr->biz_owner_id}})" >View Report</button>
                                     <!--
                                    <button class="btn btn-info btn-sm" supplier="49" onclick="">UPLOAD</button>
                                    -->
                                 </td>
                              </tr>
                        @endforeach  
                     </tbody>
                  </table>
                  
               </div>
            </div>
         </div>
      </div>
      <div class="data mt-4">
         <h2 class="sub-title bg">Hygiene Check</h2>
         <div class="pl-4 pr-4 pb-4 pt-2">
            <table class="table overview-table">
               <tbody>
                  <tr>
                     <td style="width:30%">
                        <b>
                           Whether Appearing in any of the below
                           <ul>
                              <li>CIBIL Defaulters List</li>
                           </ul>
                        </b>
                     </td>
                     <td>
                        <table style="width: 100%;">
                           <tbody>
                              <tr>
                                 <td colspan="4">
                                    <div class="form-check" style="display: inline-block; margin-right:10px;">
                                       <label class="form-check-label">
                                       <input type="radio" id="cibil_check_yes" class="form-check-input" name="optradio" value="1">Yes
                                       <i class="input-helper"></i></label>
                                    </div>
                                    <div class="form-check" style="display: inline-block;">
                                       <label class="form-check-label">
                                       <input type="radio" id="cibil_check_no" class="form-check-input" name="optradio" value="0">No
                                       <i class="input-helper"></i></label>
                                    </div>
                                    <p style="margin: 0;">CIBIL Analysis (for promoters / guarantors):</p>
                                 </td>
                              </tr>
                              <tr>
                                 <td>
                                    <table style="width: 100%;">
                                       <tbody>
                                          <tr>
                                          </tr>
                                       </tbody>
                                       <thead>
                                          <tr>
                                             <th>Name</th>
                                             <th class="white-space">PAN Number</th>
                                             <th class="white-space">CIBIL Rank/Score</th>
                                             <th>Remarks</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <tr>
                                             <td name="name[]">trtretr</td>
                                             <td name="pan_number_director[]"></td>
                                             <td name="cibil_score[]">0</td>
                                             <td>
                                                <input type="text" name="remarks" id="remarks" class="form-control" value="">
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <tr>
                                 <td>No negative observation found</td>
                              </tr>
                           </tbody>
                        </table>
                     </td>
                  </tr>
                  <tr>
                     <td><b>RBI Willful Defaulters List </b></td>
                     <td><input type="text" id="rbi_willfull_defaulters_list" class="form-control from-inline" value=""></td>
                  </tr>
                  <tr>
                     <td><b>Watchoutinvestors </b></td>
                     <td><input type="text" id="watch_out_investors" class="form-control from-inline" value=""></td>
                  </tr>
                  <tr>
                     <td><b>Politically Exposed Person</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="politically_check" id="politically_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="politically_check" id="politically_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="politically_exposed_person_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Any CDR/BIFR History</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="CDR/BIFR" id="CDR_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="CDR/BIFR" id="CDR_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="CDR_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>UNSC List</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="UNSC" id="UNSC_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="UNSC" id="UNSC_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="UNSC_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Any  NPA  History  of the  Account  Holder  or Any  of  the  Directors  / Partners  /  Guarantors/ assoc</b>iate concerns? </td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="npa_history_account_holder_check" id="npa_history_account_holder_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="npa_history_account_holder_check" id="npa_history_account_holder_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="npa_history_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Any Corporate Governance issues?</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="cop_gov_issues_check" id="cop_gov_issues_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="cop_gov_issues_check" id="cop_gov_issues_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="cop_gov_issues_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Change in Auditor</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="change_in_auditor_check" id="change_in_auditor_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="change_in_auditor_check" id="change_in_auditor_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="change_in_auditor_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Any Auditor’s Qualifications as per latest Audited Financials</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="auditor_qualification_check" id="auditor_qualification_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="auditor_qualification_check" id="auditor_qualification_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="auditor_qualification_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Any delay in repayment of Statutory Dues (As per tax audit report/ auditor’s report)</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="audit_report_check" id="audit_report_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="audit_report_check" id="audit_report_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="audit_report_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Availability of adequate insurance cover for stock and fixed assets</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="adequate_insurance_check" id="adequate_insurance_check_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="adequate_insurance_check" id="adequate_insurance_check_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="adequate_insurance_comments" value="">
                     </td>
                  </tr>
                  <tr>
                     <td><b>Any other Negative news reported on public domain (by way of Google search etc.)</b></td>
                     <td>
                        <div class="form-check" style="display: inline-block; margin-right:10px;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="negative_news_report" id="negative_news_report_yes" value="1">Yes
                           <i class="input-helper"></i></label>
                        </div>
                        <div class="form-check" style="display: inline-block;">
                           <label class="form-check-label">
                           <input type="radio" class="form-check-input" name="negative_news_report" id="negative_news_report_no" value="0">No
                           <i class="input-helper"></i></label>
                        </div>
                        <input type="text" class="form-control from-inline" id="negative_news_report_comments" value="">
                     </td>
                  </tr>
               </tbody>
            </table>
            <div class="row">
               <div class="col-md-12 mt-3">
                  <div class="form-group text-right">
                     <button  class="btn btn-primary btn-ext submitBtnBank" data-toggle="modal" data-target="#myModal">Submit</button>                                        
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
  </div>
 </div>
</div>

<div class="modal fade" id="pull_cibil_promoterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
         <input type="hidden"  id="biz_owner_id">
        <p>Are you sure you want to pull the cibil score for this promoter?</p>
        <p id="pullMsg"></p>
      </div>
      <div class="modal-footer">
      
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
       <button type="button" class="btn btn-success btn-sm" id="cibilScoreBtn" onclick="pull_cibil_promoter()">Yes</button>
        
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="download_cibil_promoterModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body table-responsive" id="download_user_cibil" style="max-height: 500px;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="isloader" style="display:none;">  
        <img src="http://rent.local/backend/assets/images/loader.gif">
</div>


                
@endsection
@section('jscript')
<script>
      function pull_cibil_promoterModal(biz_owner_id) {
         $("#pull_cibil_promoterModal").modal("show");
         $("#biz_owner_id").val(biz_owner_id);
        
      }

      function pull_cibil_promoter(biz_owner_id){
            var biz_owner_id = $("#biz_owner_id").val();
          // $("#cibilScoreBtn").text("Waiting...");
          $("#pull_cibil_promoterModal").modal("hide");
               $(".isloader").show();
            var messages = {
                 chk_user_cibil: "{{ URL::route('chk_user_cibil') }}",
                 data_not_found: "{{ trans('error_messages.data_not_found') }}",
                 token: "{{ csrf_token() }}",
            };
            var dataStore = {'biz_owner_id': biz_owner_id,'_token': messages.token };
            var postData = dataStore;
             jQuery.ajax({
                url: messages.chk_user_cibil,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                  // alert(errorThrown);
                },
                success: function (data) {
                 // $("#cibilScoreBtn").text("Yes");
                  
                 $(".isloader").hide();
                   var status =  data['status'];
                   var html = '<div class="alert-success alert" role="alert"> <span><i class="fa fa-bell fa-lg" aria-hidden="true"></i></span><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>'+data['message']+'</div>';
                     if(status==1)
                       {  
                            //alert(data['message']);
                           
                            $("#pullMsg").html(html);
                            $("#cibilScore"+biz_owner_id).text(data['cibilScore']);
                            
                       }else{
                            $("#pullMsg").html(html);
                      }                          
                       
               }

          });
      }   


      function downloadPromoterCibil(biz_owner_id) {
         var messages = {
                 download_user_cibil: "{{ URL::route('download_user_cibil') }}",
                 data_not_found: "{{ trans('error_messages.data_not_found') }}",
                 token: "{{ csrf_token() }}",
            };
            var dataStore = {'biz_owner_id': biz_owner_id,'_token': messages.token };
            var postData = dataStore;
         jQuery.ajax({
                url: messages.download_user_cibil,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                  // alert(errorThrown);
                },
                success: function (data) {
                   var status =  data['status'];
                     if(status==1)
                       {  
                              $("#download_cibil_promoterModal").modal("show");
                              $("#download_user_cibil").html(window.atob(data['cibilScoreData']));
                       }else{
                           $("#download_cibil_promoterModal").modal("show");
                              $("#download_user_cibil").text(data['cibilScoreData']);
                      }                          
                       
               }
         });
      }

</script>
   
   
@endsection