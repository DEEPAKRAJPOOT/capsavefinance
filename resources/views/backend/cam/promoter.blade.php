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
                             @foreach($arrPromoterData as $row)
                                        @php ($count++)
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
                                                <td>{{$row->date_of_birth}}</td>
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
                                                <td width="25%"><b>S.No.</b></td>
                                                <td width="25%"><b>Document Name</b></td>
                                                <td width="25%"><b>File Name</b></td>
                                                <td width="25%"><b>Action</b></td>
                                             </tr>
                                             <tr>
                                                <td>1</td>
                                                <td>Pan Card</td>
                                                <td>drop-box.jpg</td>
                                                <td><div class="file-browse float-left ">
                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                        <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                  </div></td>
                                             </tr>
                                             
                                             <tr>
                                                <td>2</td>
                                                <td>Adhar Card</td>
                                                <td>aadhar.jpg</td>
                                                <td><div class="file-browse float-left ">
                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                        <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                  </div></td>
                                             </tr>
                                           
                                          
                                           
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                              
                              
                            @endforeach  



                              
                              <div class="card card-color mb-0">
                                 <div class="card-header collapsed" data-toggle="collapse" href="#collapse2">
                                    <a class="card-title">
                                    Promoter2
                                    </a>
                                 </div>
                                 <div id="collapse2" class="card-body collapse" data-parent="#accordion">
                                    <div class="col-md-12">
                                       <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="25%"><b>Promoter Name</b></td>
                                                <td width="25%">XYZ</td>
                                                <td width="25%"><b>Lastname </b></td>
                                                <td width="25%">XYZ</td>
                                             </tr>
                                             <tr>
                                                <td><b>DOB </b></td>
                                                <td>12/12/1992</td>
                                                <td><b>Gender </b></td>
                                                <td>Male</td>
                                             </tr>
                                             <tr>
                                                <td><b>PAN Number</b></td>
                                                <td>ABCPS9578A</td>
                                                <td><b>Shareholding (%)</b></td>
                                                <td>
                                                   12
                                                </td>
                                             </tr>
                                             <tr>
                                                <td><b>Educational Qualification</b></td>
                                                <td>Graduate</td>
                                                <td><b>Other Ownerships</b></td>
                                                <td>Demo</td>
                                             </tr>
                                             <tr>
                                                <td><b>Networth</b></td>
                                                <td>7868687678</td>
                                                <td><b>Address</b></td>
                                                <td>Your address here</td>
                                             </tr> 
                                             <tr>
                                                <td><b>State </b></td>
                                                <td>U.p.</td>
                                                <td><b>City & pin code</b></td>
                                                <td>Noida (201304)</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                       
                                       
                                         <table class="table table-bordered overview-table mt-3" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="25%"><b>S.No.</b></td>
                                                <td width="25%"><b>Document Name</b></td>
                                                <td width="25%"><b>File Name</b></td>
                                                <td width="25%"><b>Action</b></td>
                                             </tr>
                                             <tr>
                                                <td>1</td>
                                                <td>Pan Card</td>
                                                <td>drop-box.jpg</td>
                                                <td><div class="file-browse float-left ">
                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                        <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                  </div></td>
                                             </tr>
                                             
                                             <tr>
                                                <td>2</td>
                                                <td>Adhar Card</td>
                                                <td>aadhar.jpg</td>
                                                <td><div class="file-browse float-left ">
                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                        <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                  </div></td>
                                             </tr>
                                           
                                          
                                           
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                              
                              
                              <div class="card card-color mb-0">
                                 <div class="card-header collapsed" data-toggle="collapse" href="#collapse3">
                                    <a class="card-title">
                                    Promoter3
                                    </a>
                                 </div>
                                 <div id="collapse3" class="card-body collapse" data-parent="#accordion">
                                    <div class="col-md-12">
                                       <table class="table table-bordered overview-table" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="25%"><b>Promoter Name</b></td>
                                                <td width="25%">XYZ</td>
                                                <td width="25%"><b>Lastname </b></td>
                                                <td width="25%">XYZ</td>
                                             </tr>
                                             <tr>
                                                <td><b>DOB </b></td>
                                                <td>12/12/1992</td>
                                                <td><b>Gender </b></td>
                                                <td>Male</td>
                                             </tr>
                                             <tr>
                                                <td><b>PAN Number</b></td>
                                                <td>ABCPS9578A</td>
                                                <td><b>Shareholding (%)</b></td>
                                                <td>
                                                   12
                                                </td>
                                             </tr>
                                             <tr>
                                                <td><b>Educational Qualification</b></td>
                                                <td>Graduate</td>
                                                <td><b>Other Ownerships</b></td>
                                                <td>Demo</td>
                                             </tr>
                                             <tr>
                                                <td><b>Networth</b></td>
                                                <td>7868687678</td>
                                                <td><b>Address</b></td>
                                                <td>Your address here</td>
                                             </tr> 
                                             <tr>
                                                <td><b>State </b></td>
                                                <td>U.p.</td>
                                                <td><b>City & pin code</b></td>
                                                <td>Noida (201304)</td>
                                             </tr>
                                          </tbody>
                                       </table>
                                       
                                       
                                         <table class="table table-bordered overview-table mt-3" cellpadding="0" cellspacing="0" border="1">
                                          <tbody>
                                             <tr>
                                                <td width="25%"><b>S.No.</b></td>
                                                <td width="25%"><b>Document Name</b></td>
                                                <td width="25%"><b>File Name</b></td>
                                                <td width="25%"><b>Action</b></td>
                                             </tr>
                                             <tr>
                                                <td>1</td>
                                                <td>Pan Card</td>
                                                <td>drop-box.jpg</td>
                                                <td><div class="file-browse float-left ">
                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                        <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                  </div></td>
                                             </tr>
                                             
                                             <tr>
                                                <td>2</td>
                                                <td>Adhar Card</td>
                                                <td>aadhar.jpg</td>
                                                <td><div class="file-browse float-left ">
                                        <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                        <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                  </div></td>
                                             </tr>
                                           
                                          
                                           
                                          </tbody>
                                       </table>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="data mt-4">
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
                     </div>
                 
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

    </div>
</div>
@endsection
@section('jscript')

@endsection