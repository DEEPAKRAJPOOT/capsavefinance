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
                        <h2 class="sub-title bg">Promoter Details</h2>
                        <div class="p-2 full-width">
                           <div id="accordion" class="accordion d-table col-sm-12">
                            @php ($count = 0)
                         @php ($j = 0)
                          @php ($i = 0)
                             @foreach($arrPromoterData as $row)
                                        @php ($i++)
                                        @php ($count++)

                                         <?php 
                                         foreach($row->document as $row2) {
                                             if($row2->doc_id == 2) { 
                                                $panNoFilePath[] =   $row2->userFile->file_path;
                                                $panNoFileName[] =   $row2->userFile->file_name;
                                            }
                                            if($row2->doc_id == 31) { 
                                                $dlNoFilePath[] = $row2->userFile->file_path;
                                                $dlNoFileName[] =   $row2->userFile->file_name;
                                            }
                                            if($row2->doc_id == 30) { 
                                                $voterNoFilePath[] = $row2->userFile->file_path;
                                                $voterNoFileName[] =   $row2->userFile->file_name;
                                            }
                                            if($row2->doc_id == 32) { 
                                                $passNoFilePath[] = $row2->userFile->file_path;
                                                $passNoFileName[] =   $row2->userFile->file_name;
                                            }
                                             if($row2->doc_id == 22) { 
                                                $photoFilePath[] = $row2->userFile->file_path;
                                                $photoFileName[] =   $row2->userFile->file_name;
                                            }
                           
                                         } 


                                         foreach($row->businessApi as $row1) {
                        
                                          if($row1->type == 3) { 
                                                $arrPan[] = json_decode($row1->karza->req_file);
                                            }
                                            else if($row1->type == 5) { 
                                                $arrDl[] = json_decode($row1->karza->req_file);
                                            }
                                             else if($row1->type == 4) { 
                                                $arrVoterNo[] = json_decode($row1->karza->req_file); 
                                            }
                                            else if($row1->type == 6) { 
                                                $arrPassNo[] = json_decode($row1->karza->req_file); 
                                            }
                                        } 
                                        ?>


                              <div class="card card-color mb-0">
                                 <div class="card-header collapsed" data-toggle="collapse" href="#collapse{{$count}}">
                                    <a class="card-title">
                                    Promoter{{$count}}
                                    </a>
                                 </div>
                                 <div id="collapse{{$count}}" class="card-body collapse @if ($count == 1) show @endif" data-parent="#accordion">
                                    <div class="col-md-12">
                                       <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="25%"><b>Promoter Name</b></td>
                                                <td width="25%">{{$row->first_name}}</td>
                                                <td width="25%"><b>Lastname </b></td>
                                                <td width="25%">{{$row->last_name}}</td>
                                             </tr>
                                             <tr>
                                                <td><b>DOB </b></td>
                                                <td>{{ ($row->date_of_birth) ? date('d/m/Y', strtotime($row->date_of_birth)) : '' }}</td>
                                                <td><b>Gender </b></td>
                                                <td>@if($row->gender==1) Male @elseif($row->gender==2) Female @endif</td>
                                             </tr>
                                             <tr>
                                                <td><b>PAN Number</b></td>
                                                <td>{{ (isset($row->pan->pan_gst_hash)) ? $row->pan->pan_gst_hash : '' }}</td>
                                                <td><b>Shareholding (%)</b></td>
                                                <td>{{$row->share_per}}</td>
                                             </tr>
                                             <tr>
                                                <td><b>Educational Qualification</b></td>
                                                <td>{{$row->edu_qualification}}</td>
                                                <td><b>Other Ownerships</b></td>
                                                <td>{{$row->other_ownership}}</td>
                                             </tr>
                                             <tr>
                                                <td><b>Networth</b></td>
                                                <td>{{$row->networth}}</td>
                                                <td><b>Address</b></td>
                                                <td>{{$row->owner_addr}}</td>
                                             </tr> 
                                             <!-- <tr>
                                                <td><b>State </b></td>
                                                <td>{{$row->other_ownership}}</td>
                                                <td><b>City & pin code</b></td>
                                                <td>Noida (201304)</td>
                                             </tr> -->
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
                                                        <input type="text" readonly='readonly' value="{{ isset($arrPan[$j]->requestId) ? $arrPan[$j]->requestId : '' }}"  class="form-control verifydl"  >
                                                        <span class="text-success float-left" style="display:{{isset($arrPan[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified </i> </span>
                                                        <span class="text-danger float-left" id="v1failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>
                                                    </div>

                                                </td>
                                                <td>{{isset($panNoFileName[$j]) ? $panNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                        <a data-toggle="modal" id="ppanVeriView{{isset($row->first_name) ? $i : '1'}}" data-target="#modalPromoter" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('show_pan_data',['type'=>3,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($arrPan[$j]->requestId) ? 'inline' : 'none'}}"> <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="3"> <i class="fa fa-eye"></i></button>
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
                                                        <span class="text-danger float-left"  style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>
                                                    </div>

                                                </td>
                                                <td>{{isset($dlNoFileName[$j]) ? $dlNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                         <a data-toggle="modal" id="ddrivingVeriView{{isset($row->first_name) ? $i : '1'}}"  data-target="#modalPromoter1" data-height="400" data-width="100%" accesskey="" data-url="{{route('show_dl_data',['type'=>'5','ownerid' => $row->biz_owner_id ])}}" style="display:{{ (isset($arrDl[$j]->requestId)) ? 'inline' : 'none'}}">  <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="5" > <i class="fa fa-eye"></i></button></a>
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
                                                       <span class="text-success float-left" id="v3successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{isset($arrVoterNo[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>
                                                        <span class="text-danger float-left" id="v3failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>
                                                    </div>

                                                </td>
                                                <td>{{isset($voterNoFileName[$j]) ? $voterNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                        <a data-toggle="modal" id="vvoterVeriView{{isset($row->first_name) ? $i : '1'}}"  data-target="#modalPromoter2" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('show_voter_data',['type'=>4,'ownerid' => $row->biz_owner_id ])}}" style="display:{{isset($arrVoterNo[$j]->requestId) ? 'inline' : 'none'}}">   <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="4"> <i class="fa fa-eye"></i></button></a>
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
                                                    
                                                          <span class="text-success float-left" id="v4successpanverify{{isset($row->first_name) ? $i : '1'}}"  style="display:{{isset($arrPassNo[$j]->requestId) ? 'inline' : 'none'}}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>

                                                         <span class="text-danger float-left" id="v4failurepanverify{{isset($row->first_name) ? $i : '1'}}"  style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>
                                                     
                                                    </div>


                                                </td>
                                                <td>{{isset($passNoFileName[$j]) ? $passNoFileName[$j] : '' }}</td>
                                                <td>
                                                    <div class="file-browse float-left position-seta">
                                                        <a data-toggle="modal" id="ppassportVeriView{{isset($row->first_name) ? $i : '1'}}" data-target="#modalPromoter3" data-height="400px" data-width="100%" accesskey=""data-url ="{{route('show_pass_data',['type'=>6,'ownerid' => $row->biz_owner_id ])}}"  style="display:{{isset($arrPassNo[$j]->requestId) ? 'inline' : 'none'}}">     <button class="btn-upload btn-sm" type="button" title="view Details" data-id="{{isset($row->first_name) ? $i : '1'}}" data-type="6"> <i class="fa fa-eye"></i></button></a>
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
                    <!--  <div class="data mt-4">
                        <h2 class="sub-title bg">Brief Profile about the Promoters</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                           <textarea class="form-control" id="profile_of_company" name="profile_of_company" rows="3" spellcheck="false"></textarea>
                        </div>
                     </div>
                     <div class="data mt-4">
                        <h2 class="sub-title bg">Risk Comments on the Promoters</h2>
                        <div class="pl-4 pr-4 pb-4 pt-2">
                           <textarea class="form-control" id="profile_of_company" name="profile_of_company" rows="3" spellcheck="false"></textarea>
                        </div>
                     </div> -->
                 
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
@endsection
@section('jscript')

@endsection