@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    @include('layouts.backend.partials.cam_nav')
    <div class="inner-container">
        <div class="row grid-margin mt-3">
   <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
      <div class="card">
         <div class="card-body">
            
            <div class=" form-fields">
               <div class="form-sections">
                     <!-- <div id="js-grid-static"></div>    -->
                     <div class="data">
                        <h2 class="sub-title bg">Management Information</h2>
                        <div class="p-2 full-width">
                           <div id="accordion" class="accordion d-table col-sm-12">
                              @php ($count = 0)
                              @php ($j = 0)
                              @php ($i = 0)
                              @php ($panNoFilePath = $panNoFileName = $dlNoFilePath = $dlNoFileName = $voterNoFilePath = $voterNoFileName = $passNoFilePath = $passNoFileName = $photoFilePath = $photoFileName = $aadharFilePath = $aadharFileName = $arrPan = $arrDl = $arrVoterNo = $arrPassNo = $arrMobileNo = $arrMobileOtpNo = [])
                             @foreach($arrPromoterData as $key=>$row)
                                        @php ($i++)
                                        @php ($count++)

                                         <?php 
                                         foreach($row->document as $row2) {
                                             if($row2->doc_id == 2) { 
                                                $panNoFilePath[$key] =   $row2->userFile->file_path;
                                                $panNoFileName[$key] =   $row2->userFile->file_name;
                                            }
                                            if($row2->doc_id == 31) { 
                                                $dlNoFilePath[$key] = $row2->userFile->file_path;
                                                $dlNoFileName[$key] =   $row2->userFile->file_name;
                                            }
                                            if($row2->doc_id == 30) { 
                                                $voterNoFilePath[$key] = $row2->userFile->file_path;
                                                $voterNoFileName[$key] =   $row2->userFile->file_name;
                                            }
                                            if($row2->doc_id == 32) { 
                                                $passNoFilePath[$key] = $row2->userFile->file_path;
                                                $passNoFileName[$key] =   $row2->userFile->file_name;
                                            }
                                             if($row2->doc_id == 22) { 
                                                $photoFilePath[$key] = $row2->userFile->file_path;
                                                $photoFileName[$key] =   $row2->userFile->file_name;
                                            }
                                            if ($row2->doc_id == 34) {
                                                $aadharFilePath[$key] = $row2->userFile->file_path;
                                                $aadharFileName[$key] =   $row2->userFile->file_name;
                                            }
                                            if ($row2->doc_id == 37) {
                                                $electricityFilePath[$key] = $row2->userFile->file_path;
                                                $electricityFileName[$key] =   $row2->userFile->file_name;
                                            }
                                            if ($row2->doc_id == 38) {
                                                $telephoneFilePath[$key] = $row2->userFile->file_path;
                                                $telephoneFileName[$key] =   $row2->userFile->file_name;
                                            }
                           
                                         } 


                                        foreach($row->businessApi as $row1) {
                        
                                          if($row1->type == 3) { 
                                                $arrPan[$key] = json_decode($row1->karza->req_file);
                                            }
                                            else if($row1->type == 5) { 
                                                $arrDl[$key] = json_decode($row1->karza->req_file);
                                            }
                                             else if($row1->type == 4) { 
                                                $arrVoterNo[$key] = json_decode($row1->karza->req_file); 
                                            }
                                            else if($row1->type == 6) { 
                                                $arrPassNo[$key] = json_decode($row1->karza->req_file); 
                                            }else if ($row1->type == 7) {
                                                $arrMobileNo[$key] = json_decode($row1->karza->req_file);
                                            }else if ($row1->type == 8) {
                                                $arrMobileOtpNo[$key] = json_decode($row1->karza->req_file);
                                            }else if ($row1->type == 9) {
                                                $arrPanVerifyNo[$key] = json_decode($row1->karza->req_file);
                                            }
                                        } 
                                       
                                        ?>


                              <div class="card card-color mb-0">
                                 <div class="card-header collapsed" data-toggle="collapse" href="#collapse{{$count}}">
                                    <a class="card-title">
                                    Management Information ({{$count}})
                                    </a>
                                 </div>
                                 <div id="collapse{{$count}}" class="card-body collapse @if ($count == 1) show @endif" data-parent="#accordion">
                                    <div class="col-md-12">
                                       <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="25%"><b> Name</b></td>
                                                <td width="25%">{{$row->first_name}}</td>
                                                <td width="25%"><b>Is Promoter </b></td>
                                                <td width="25%">@if($row->is_promoter==1) Yes @else No @endif</td>
                                             </tr>
                                             <tr>
                                                <td><b>DOB </b></td>
                                                <td>{{ ($row->date_of_birth) ? date('d/m/Y', strtotime($row->date_of_birth)) : '' }}</td>
                                                <td><b>Gender </b></td>
                                                <td>@if($row->gender==1) Male @elseif($row->gender==2) Female @endif</td>
                                             </tr>
                                             <tr>
                                                <td><b>PAN Number</b></td>
                                                <td>{{  $row->pan_number }} 
                                                     <a data-toggle="modal" data-target="#modalPromoter9" data-height="400px" data-width="100%" accesskey="" data-url ="{{route('show_pan_verify_data',['type'=>9,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($arrPanVerifyNo[$j]->requestId) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="View Details (Verify Pan Status)" data-type="3"> <i class="fa fa-eye"></i></button></a>
                                                                          </td>
                                                <td><b>Shareholding (%)</b></td>
                                                <td>{{$row->share_per}}</td>
                                             </tr>
                                             <tr>
                                                <td><b>Designation</b></td>
                                                <td>{{$row->designation}}</td>
                                                <td><b>Other Ownerships</b></td>
                                                <td>{{$row->other_ownership}}</td>
                                             </tr>
                                             <tr>
                                                <td><b>Networth</b></td>
                                                <td> {!! $row->networth ? \Helpers::formatCurreny($row->networth) : '' !!}</td>
                                                <td><b>Address</b></td>
                                                <td>{{($row->address->addr_1) ? $row->address->addr_1 : ''}}</td>
                                             </tr> 
                                             <tr>
                                                <td><b>Mobile</b></td>
                                                <td>
                                                     <div class="col-md-12">
                                                        <input type="text" readonly='readonly'  value="{{ isset($arrMobileNo[$j]->mobile) ? $arrMobileNo[$j]->mobile : '' }}" name="verifyvoter[]" id="verifyvoter{{isset($row->first_name) ? $i : '1'}}"  class="form-control verifyvoter" >
                                                       <span class="text-success float-left"  style="display:{{isset($arrMobileNo[$j]->mobile) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified</i> </span>
                                                    </div>   
                                                </td>

                                                <td>
                                                    <a data-toggle="modal"  data-target="#modalPromoter7" data-height="400px" data-width="100%" accesskey="" data-url ="{{ route('mobile_verify',['type' => 7,'ownerid' => $row->biz_owner_id]) }}" style="display:{{isset($arrMobileNo[$j]->mobile) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="View Mobile Detail (Without OTP)" data-type="7"> <i class="fa fa-eye"></i></button></a>
                                                </td>
                                                <td>
                                                    <a data-toggle="modal"  data-target="#modalPromoter8" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('mobile_otp_view',['type'=> 8,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($arrMobileOtpNo[$j]->request_id) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="View Mobile Detail (With OTP)"  data-type="8"> <i class="fa fa-eye"></i></button></a>
                                                </td>
                                             </tr>
                                          </tbody>
                                       </table>
                                       
                                       
                                         <table class="table table-bordered overview-table mt-3" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="20%"><b>S.No.</b></td>
                                                <td width="20%"><b>Document Name</b></td>
                                                <td width="20%"><b> Document ID No.</b></td>
                                                <td width="20%"><b>File Name</b></td>
                                                <td width="20%"><b>Action</b></td>
                                             </tr>
                                             <tr>
                                                <td>1</td>
                                                <td>Pan Card</td>
                                                <td>
                                                    <div class="col-md-12">
                                                        <input type="text" readonly='readonly' value="{{ isset($arrPan[$j]->requestId) ? $arrPan[$j]->requestId : '' }}"  class="form-control verifydl">
                                                        <span class="text-success float-left" style="display:{{isset($arrPan[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified </i> </span>
                                                    </div>

                                                </td>
                                                <td>{{isset($panNoFileName[$j]) ? $panNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                        <a data-toggle="modal"  data-target="#modalPromoter" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('show_pan_data',['type'=>3,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($arrPan[$j]->requestId) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="View Pan Card Detail"  data-type="3"> <i class="fa fa-eye"></i></button>
                                                        </a>
                                                        <a  href="{{ isset($panNoFilePath[$j]) ? Storage::url($panNoFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($panNoFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                    </div>  
                                                </td>
                                             </tr>
                                             
                                            <tr>
                                                <td>2</td>
                                                <td>Driving License</td>
                                                <td>
                                                    <div class="col-md-12">
                                                        <input type="text" readonly='readonly'  value="{{ isset($arrDl[$j]->requestId) ? $arrDl[$j]->requestId : '' }}"  class="form-control verifydl"  >
                                                        <span class="text-success float-left" style="display:{{isset($arrDl[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified </i> </span>
                                                        
                                                    </div>

                                                </td>
                                                <td>{{isset($dlNoFileName[$j]) ? $dlNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                         <a data-toggle="modal" data-target="#modalPromoter1" data-height="400" data-width="100%" accesskey="" data-url="{{route('show_dl_data',['type'=>'5','ownerid' => $row->biz_owner_id ])}}" style="display:{{ (isset($arrDl[$j]->requestId)) ? 'inline' : 'none'}}">  <button class="btn-upload btn-sm" type="button" title="View Driving License Detail" data-type="5" > <i class="fa fa-eye"></i></button></a>
                                                        <a  href="{{ isset($dlNoFilePath[$j]) ? Storage::url($dlNoFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($dlNoFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                    </div>            
                                                   
                                                </td>
                                            </tr>
                                           

                                            <tr>
                                                <td>3</td>
                                                <td>Voter ID</td>
                                                <td>
                                                    <div class="col-md-12">
                                                        <input type="text" readonly='readonly'  value="{{ isset($arrVoterNo[$j]->requestId) ? $arrVoterNo[$j]->requestId : '' }}" name="verifyvoter[]" id="verifyvoter{{isset($row->first_name) ? $i : '1'}}"  class="form-control verifyvoter" >
                                                       <span class="text-success float-left" id="v3successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{isset($arrVoterNo[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified</i> </span>
                                                        
                                                    </div>

                                                </td>
                                                <td>{{isset($voterNoFileName[$j]) ? $voterNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                        <a data-toggle="modal" data-target="#modalPromoter2" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('show_voter_data',['type'=>4,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($arrVoterNo[$j]->requestId) ? 'inline' : 'none'}}">   <button class="btn-upload btn-sm" type="button" title="View Voter ID Detail" data-type="4"> <i class="fa fa-eye"></i></button></a>
                                                        <a  href="{{ isset($voterNoFilePath[$j]) ? Storage::url($voterNoFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($voterNoFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                    </div>                
                                                   
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>4</td>
                                                <td>Passport</td>
                                                <td>
                                                    <div class="col-md-12">
                                                         <input type="text" readonly='readonly'  value="{{ isset($arrPassNo[$j]->requestId) ? $arrPassNo[$j]->requestId : '' }}" name="verifypassport[]" id="verifypassport{{isset($row->first_name) ? $i : '1'}}"  class="form-control verifypassport" >
                                                    
                                                          <span class="text-success float-left" id="v4successpanverify{{isset($row->first_name) ? $i : '1'}}"  style="display:{{isset($arrPassNo[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified</i> </span>
                                                    </div>


                                                </td>
                                                <td>{{isset($passNoFileName[$j]) ? $passNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                        <a data-toggle="modal"  data-target="#modalPromoter3" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('show_pass_data',['type'=>6,'ownerid' => $row->biz_owner_id ])}}"  style="display:{{isset($arrPassNo[$j]->requestId) ? 'inline' : 'none'}}">     <button class="btn-upload btn-sm" type="button" title="View Passport Detail" data-type="6"> <i class="fa fa-eye"></i></button></a>
                                                        <a  href="{{ isset($passNoFilePath[$j]) ? Storage::url($passNoFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($passNoFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                    </div>               
                                                   
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>5</td>
                                                <td>Photo</td>
                                                <td></td>
                                                <td>{{isset($photoFileName[$j]) ? $photoFileName[$j] : '' }}</td>
                                                <td>
                                                <a  href="{{ isset($photoFilePath[$j]) ? Storage::url($photoFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($photoFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                   
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>6</td>
                                                <td>Aadhar Card</td>
                                                <td></td>
                                                <td>{{isset($aadharFileName[$j]) ? $aadharFileName[$j] : '' }}</td>
                                                <td>
                                                <a  href="{{ isset($aadharFilePath[$j]) ? Storage::url($aadharFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($aadharFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                   
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>7</td>
                                                <td>Electricity Bill    </td>
                                                <td></td>
                                                <td>{{isset($electricityFileName[$j]) ? $electricityFileName[$j] : '' }}</td>
                                                <td>
                                                <a  href="{{ isset($electricityFilePath[$j]) ? Storage::url($electricityFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($electricityFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>8</td>
                                                <td>Telephone Bill</td>
                                                <td></td>
                                                <td>{{isset($telephoneFileName[$j]) ? $telephoneFileName[$j] : '' }}</td>
                                                <td>
                                                <a  href="{{ isset($telephoneFilePath[$j]) ? Storage::url($telephoneFilePath[$j]) : '' }}" class="btn-upload   btn-sm" type="button"  style="display:{{ isset($telephoneFilePath[$j]) ? 'inline' : 'none'}}" download> <i class="fa fa-download"></i></a>
                                                   
                                                </td>
                                            </tr>

                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                              
                             @php ($j++) 
                            @endforeach  
  
                           </div>
                        </div>
                     </div>
              <form method="POST" action="{{route('cam_promoter_comment_save')}}"> 
                 @csrf

                <input type="hidden" name="app_id" value="{{isset($attribute['app_id']) ? $attribute['app_id'] : ''}}" />             
                <input type="hidden" name="biz_id" value="{{isset($attribute['biz_id']) ? $attribute['biz_id'] : ''}}" />             
                <input type="hidden" name="cam_report_id" value="{{isset($arrCamData->cam_report_id) ? $arrCamData->cam_report_id : ''}}" /> 
                     
                     <div class="data mt-4">
                        <h2 class="sub-title bg" style="margin-bottom: 0px; border: 1px solid #d1d1d1;">Risk Comments on the Management</h2>
                        <!-- <div class="pl-4 pr-4 pb-4 pt-2"> -->
                           <textarea class="form-control" id="promoter_cmnt" name="promoter_cmnt" rows="3" spellcheck="false">{{isset($arrCamData->promoter_cmnt) ? $arrCamData->promoter_cmnt : ''}}</textarea>
                        <!-- </div> -->
                     </div>
                     <button class="btn btn-success pull-right  mt-3" type="Submit"> Save</button>
                </form>
                 
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

    </div>
</div>


    {!!Helpers::makeIframePopup('modalPromoter','View PAN Card Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter1','View Driving License Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter2','View Voter ID  Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter3','View Passport Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter7','Mobile Verify Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter8','OTP Verify Detail', 'modal-lg')!!}
    {!!Helpers::makeIframePopup('modalPromoter9','PAN Verify Status Detail', 'modal-lg')!!}


    
@endsection
@section('jscript')
<script>
    var ckeditorOptions =  {
      filebrowserUploadUrl: "{{route('upload_ckeditor_image', ['_token' => csrf_token() ])}}",
      filebrowserUploadMethod: 'form',
      disallowedContent: 'img{width,height};'
    };     
CKEDITOR.replace('promoter_cmnt', ckeditorOptions);
</script>
@endsection