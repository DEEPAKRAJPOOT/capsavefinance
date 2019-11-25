@extends('layouts.backend.admin-layout')
@section('additional_css')
<style>
.upload-btn-wrapper input[type=file] {
    font-size: inherit;
    width: 63px;
    position: absolute;
    margin-left: 92px;
}
.setupload-btn > .error {
  position: absolute;
  top: -3px;
}
</style>
@endsection
@section('content')
<ul class="main-menu">
    <li>
        <a href="#" class="active">Application details</a>
    </li>
    <li>
        <a href="#">CAM</a>
    </li>
    <li>
        <a href="#">FI/RCU</a>
    </li>
    <li>
        <a href="#">Collateral</a>
    </li>
    <li>
        <a href="#">Notes</a>
    </li>
    <li>
        <a href="#">Submit Commercial</a>
    </li>
</ul>
<!-- partial -->
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Company Details</a>
        </li>
        <li>
            <a href="{{ route('promoter_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}"  class="active">Promoter Details</a>
        </li>
        <li>
            <a href="{{ route('documents', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Documents</a>
        </li>
        <!--<li>
                <a href="buyers.php">Buyers </a>
        </li>-->
        <!-- <li>
                <a href="third-party.php">Third party</a>
        </li> -->
    </ul>
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                  <form id="signupForm">
                <div class="card-body">
                   @csrf
                  <input type="hidden" name="app_id" id="app_id"  value="{{ (!empty($appId)) ? $appId : '' }}" >
                  <input type="hidden" name="biz_id" id="biz_id"  value="{{ (!empty($bizId)) ? $bizId : '' }}" >   
                 @php ($i = 0)
                 @foreach($ownerDetails as $row)    @php ($i++)
                    <div class=" form-fields">
                          @csrf
                        <div class="col-md-12">
                            <h5 class="card-title form-head-h5">Promoter Details  </h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Promoter Name

                                            <span class="mandatory">*</span>
                                        </label>
                                         <input type="hidden" id="rowcount" value="{{count($ownerDetails)}}">
                                         <input type="hidden" name="ownerid[]" id="ownerid{{isset($row->first_name) ? $i : '1'}}" value="{{$row->biz_owner_id}}">   
                                         <input type="text" name="first_name[]" id="first_name{{isset($row->first_name) ? $i : '1'}}" vname="first_name1" value="{{$row->first_name}}" class="form-control first_name" placeholder="Enter First Name" >
                                                         </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod " class="opacity-0">lastname
                                        </label>
                                         <input type="text" name="last_name[]" id="last_name{{isset($row->first_name) ? $i : '1'}}" value="{{$row->last_name}}" class="form-control last_name" placeholder="Enter Last Name" >
                                                     </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">DOB
                                            <span class="mandatory">*</span>
                                        </label>
                                       <input type="date" name="date_of_birth[]" id="date_of_birth{{isset($row->first_name) ? $i : '1'}}" value="{{$row->date_of_birth}}" class="form-control date_of_birth" tabindex="1" placeholder="Enter Date Of Birth" >
                                                     </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">Gender
                                            <span class="mandatory">*</span>
                                        </label>
                                     <select class="form-control gender" name="gender[]" id="gender{{isset($row->first_name) ? $i : '1'}}">
                                                            
                                                            <option value=""> Select Gender</option>
                                                            <option value="1" @if($row->gender==1)  selected="selected" @endif> Male </option>
                                                            <option value="2" @if($row->gender==2)  selected="selected" @endif>Female </option>
                                                        </select>
                                                       </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">PAN Number
                                            <span class="mandatory">*</span>
                                        </label>

                                        <a href="javascript:void(0);" data-id="{{isset($row->first_name) ? $i : '1'}}" id="pan_verify{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no promoter_pan_verify" style="pointer-events:{{ (isset($row->pan->pan_gst_hash)) ? 'none' : ''}}">{{ (isset($row->pan->pan_gst_hash)) ? 'Verified' : 'Verify' }}</a>
                                                        <input type="text" name="pan_no[]" id="pan_no{{isset($row->first_name) ? $i : '1'}}" value="{{ (isset($row->pan->pan_gst_hash)) ? $row->pan->pan_gst_hash : '' }}" class="form-control pan_no" placeholder="Enter Pan Number" {{ (isset($row->pan->pan_gst_hash)) ? '    readonly' : '' }}>
                                                        <input name="response[]" id="response{{isset($row->first_name) ? $i : '1'}}" type="hidden" value="">
                                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">Shareholding (%)

                                            <span class="mandatory">*</span>
                                        </label>
                                       <input type="text" name="share_per[]" id="share_per{{isset($row->first_name) ? $i : '1'}}" value="{{$row->share_per}}" class="form-control share_per" tabindex="1" placeholder="Enter Shareholder" >
                                       </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtEmail">Educational Qualification

                                        </label>
                                        <input type="text" name="edu_qualification[]" id="edu_qualification{{isset($row->first_name) ? $i : '1'}}" value="{{$row->edu_qualification}}" class="form-control edu_qualification" tabindex="1" placeholder="Enter Education Qualification">
                                       </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtEmail">Other Ownerships
                                        </label>
                                       <input type="text" name="other_ownership[]" id="other_ownership{{isset($row->first_name) ? $i : '1'}}" value="{{$row->other_ownership}}" class="form-control other_ownership" tabindex="1" placeholder="Other Ownership">
                                     </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group INR">
                                        <label for="txtEmail">Networth


                                        </label><a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="networth[]" id="networth{{isset($row->first_name) ? $i : '1'}}" value="{{$row->networth}}" class="form-control networth" tabindex="1" placeholder="Enter Networth">
                                             </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Address
                                            <span class="mandatory">*</span>
                                        </label>
                                         <textarea class="form-control textarea address" placeholder="Enter Address" name="owner_addr[]" id="address{{isset($row->first_name) ? $i : '1'}}">{{$row->owner_addr}}</textarea>
                                      </div>
                                </div>

                            </div>
                            <h5 class="card-title form-head-h5 mt-3">Document </h5>									
                            <div class="row mt-2 mb-4">
                                <div class="col-md-12">
                                    <div class="prtm-full-block">       
                                        <div class="prtm-block-content">
                                            <div class="table-responsive ps ps--theme_default" data-ps-id="9615ce02-be28-0492-7403-d251d7f6339e">
                                                <table class="table text-center table-striped table-hover">
                                                    <thead class="thead-primary">
                                                        <tr>
                                                            <th class="text-left">S.No</th>
                                                            <th>Document Name</th>
                                                            <th>Document ID No.</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-left">1</td>
                                                            <td width="30%">Pan Card</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='ppan{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show veripan" style="top:0px;">Verify</a>
                                                                    <input type="text"  name="veripan[]" id="veripan{{isset($row->first_name) ? $i : '1'}}" value="" class="form-control verifydl" tabindex="1" placeholder="Enter PAN Number" required="">
                                                                </div>
                                                            </td>
                                                            <td width="28%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" class="verifyfile" name="verifyfile[]" id="verifyfile{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" class="panfile" data-id="{{isset($row->first_name) ? $i : '1'}}" required="required" name="panfile[]" id="panfile{{isset($row->first_name) ? $i : '1'}}" onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, 2)">
                                                                    <span class="fileUpload"></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">2</td>
                                                            <td width="30%">Driving License</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='ddriving{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show veridl" style="top:0px;">Verify</a>
                                                                    <input type="text"  name="verifydl[]" id="verifydl{{isset($row->first_name) ? $i : '1'}}" value="" class="form-control verifydl" tabindex="1" placeholder="Enter DL Number" required="">
                                                                </div>
                                                            </td>
                                                            <td width="28%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" id="downloaddl{{isset($row->first_name) ? $i : '1'}}" name="downloaddl[]" class="downloaddl" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="dlfile[]" data-id="{{isset($row->first_name) ? $i : '1'}}" required="required" id="dlfile{{isset($row->first_name) ? $i : '1'}}" class="dlfile"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, 31)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">3</td>
                                                            <td width="30%">Voter ID</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='vvoter{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no verify-show verivoter" style="top:0px;">Verify</a>
                                                                    <input type="text" name="verifyvoter[]" id="verifyvoter{{isset($row->first_name) ? $i : '1'}}" value="" class="form-control verifyvoter" tabindex="1" placeholder="Enter Voter's Epic Number" required="">
                                                                </div>
                                                                </td>
                                                            <td width="28%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" name="downloadvoter[]" class="downloadvoter" id="downloadvoter{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="voterfile[]" data-id="{{isset($row->first_name) ? $i : '1'}}" required="required" class="voterfile" id="voterfile{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, 30)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">4</td>
                                                            <td width="30%">Passport</td>
                                                            <td width="30%" >
                                                                <div class="col-md-12">
                                                                    <a href="javascript:void(0);" id='ppassport{{isset($row->first_name) ? $i : '1'}}' data-id="{{isset($row->first_name) ? $i : '1'}}"  class="verify-owner-no verify-show veripass" style="top:0px;">Verify</a>
                                                                    <input type="text" name="verifypassport[]" id="verifypassport{{isset($row->first_name) ? $i : '1'}}" value="" class="form-control verifypassport" tabindex="1" placeholder="Enter File Number" required="">
                                                                </div>
                                                                </td>
                                                            <td width="28%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" name="downloadpassport[]" class="downloadpassport" id="downloadpassport{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="passportfile[]" data-id="{{isset($row->first_name) ? $i : '1'}}" required="required" class="passportfile" id="passportfile{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, 32)">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-left">5</td>
                                                            <td width="30%">Photo</td>
                                                            <td width="30%" >
                                                               
                                                            </td>
                                                            <td width="28%">
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" class="downloadphoto"  name="downloadphoto[]" id="downloadphoto{{isset($row->first_name) ? $i : '1'}}" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" class="photofile" required="required" name="photofile[]" id="downloadphoto{{isset($row->first_name) ? $i : '1'}}"  onchange="uploadFile({{isset($row->first_name) ? $i : '1'}}, 22)">
                                                                </div>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                                
                                                <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>	
                           
                           
                            <div class="modal" id="myModal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Modal Header -->
                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <br/>
                                            <div class="form-group password-input">
                                                <label for="txtPassword"><b>Select Promoter Type</b>
                                                    <span class="mandatory">*</span>
                                                </label>
                                                <select class="form-control ">
                                                    <option> Select</option>
                                                    <option> Co-Applicant</option>
                                                    <option>Guarantor </option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="text-right mt-3">
                                                        <button type="button" id="btnAddMore" class="btn btn-primary">

                                                            Submit
                                                        </button>
                                                    </div>
                                                </div>						
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>					
                        </div>	
                    </div>	
                   
                    @endforeach
                 <span class="form-fields-appand"></span>   
                <div class="row">
                    
                    <div class="col-md-12 mt-2">
                   
           <div class="d-flex btn-section ">
            <div class="ml-auto text-right">
 
           <button type="button" id="btnAddMore" class="btn btn-primary btn-add ml-auto">
                    <i class="fa fa-plus"></i>
                    Add Promoter
                    </button>  </div>
            </div>				
							
		</div>
                    
                    <div class="col-md-12 mt-2">
                   


            <div class="d-flex btn-section ">
            <div class="ml-auto text-right">
                <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href='company-details.php'">
               <input type="button" value="Save and Continue" id="submit" class="btn btn-primary">
                            
            </div>
               </div>	
		</div>						
		</div>
                    
                </div>
                  </form>
            </div>
        </div>
    </div>
    
@endsection
@section('jscript')

<script type="text/javascript">
    var messages = {
        promoter_document_save: "{{ URL::route('promoter_document_save') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
$(document).ready(function () {
    $('#submit').on('click', function (event) {
        $('input.first_name').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });
        $('input.date_of_birth').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });
        $('select.gender').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });
        $('input.pan_no').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });

        $('input.share_per').each(function () {
            $(this).rules("add",
                    {
                        required: true,
                        number: true
                    })
        });


        $('input.edu_qualification').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });

        $('input.other_ownership').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });
        $('input.networth').each(function () {
            $(this).rules("add",
                    {
                        required: true,
                        number: true
                    })
        });
        $('textarea.address').each(function () {
            $(this).rules("add",
                    {
                        required: true
                    })
        });


        /* $('.privacy_chk').each(function () {
         $(this).rules("add",
         {
         required: true
         })
         }); */
        // test if form is valid 
        if ($('form#signupForm').validate().form()) {
            var panCount = 0;
            $(".pan_no").each(function (k, v) {
                panCount++;
                var result = $("#pan_verify" + panCount).text();
                if (result == "Verify")
                {
                    $('#pan_no' + panCount).css({"border": "2px solid red"});
                    $('#pan_no' + panCount).focus();
                    e.preventDefault();
                    return false;
                }

            });
            var form = $("#signupForm");
            $.ajax({
                type: "POST",
                url: '{{Route('promoter_detail_save')}}',
                data: form.serialize(), // serializes the form's elements.
                cache: false,
                success: function (res)
                {
                    if (res.status == 1)
                    {
                        window.location.href = "/application/document";
                    }
                },
                error: function (error)
                {
                    console.log(error);
                }

            });
        } else {
            console.log("does not validate");
        }
    })

    $('form#signupForm').validate();
});

function FileDetails(clicked_id) {
    // GET THE FILE INPUT.
    var fi = document.getElementById('file_' + clicked_id);
    // VALIDATE OR CHECK IF ANY FILE IS SELECTED.
    if (fi.files.length > 0) {

        // THE TOTAL FILE COUNT.
        var x = 'filePath_' + clicked_id;
        //var x = document.getElementById(id);alert(id);
        document.getElementById(x).innerHTML = '';

        // RUN A LOOP TO CHECK EACH SELECTED FILE.
        for (var i = 0; i <= fi.files.length - 1; i++) {

            var fname = fi.files.item(i).name; // THE NAME OF THE FILE.
            var fsize = fi.files.item(i).size; // THE SIZE OF THE FILE.
            // SHOW THE EXTRACTED DETAILS OF THE FILE.
            document.getElementById(x).innerHTML =
                    '<div class="file-name" id="fileId"> ' +
                    fname + '' + '<button type="button"  class="close-file" onclick="myDelete()" > x' + '</button>' + '</div>';
        }
    } else {
        alert('Please select a file.');
    }
}

function myDelete() {
    document.getElementById("fileId").remove();
}



$(document).on('click', '#btnAddMore', function () {
    var rowcount = parseInt($("#rowcount").val());
    if (rowcount > 0)
    {
        var x = rowcount + 1;
    } else
    {
        var x = 2;
    }
                        $("#rowcount").val(x);
                         $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><div class='col-md-12'><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='date' name='date_of_birth[]'  id='date_of_birth" + x + "' value='' class='form-control date_of_birth' tabindex='1' placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per' tabindex='1' placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification' tabindex='1' placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership' tabindex='1' placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'>INR</a><input type='text' name='networth[]' id='networth" + x + "' value='' class='form-control networth' tabindex='1' placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'></textarea></div></div> <h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'><div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><a href='javascript:void(0);' id='ppan"+ x +"' data-id='"+ x +"' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan"+ x +"' value='' class='form-control' tabindex='1' placeholder='Enter PAN Number' required=''></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' required='required' name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl' tabindex='1' placeholder='Enter DL Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' required='required'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter' tabindex='1' placeholder='Enter Voter's Epic Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x +"' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport' tabindex='1' placeholder='Enter File Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x' tabindex='0' style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y' tabindex='0' style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div> </div> </div></div></div> ");
                         x++;
});
//////////CIN webservice for get promoter details start here//////////////////////////////////////        
$(document).on('click', '.clsdiv', function () {
    alert();
    $(this).parent().parent().remove();
});

jQuery(document).ready(function () {
    var countOwnerRow = $("#rowcount").val();
            if(countOwnerRow > 0) {
                return false;
            } 
    $('.isloader').show();
    var CIN = '{{ (isset($cin_no)) ? $cin_no : "" }}';
    var consent = "Y";
    var key = "h3JOdjfOvay7J8SF";
    var dataStore = ({'consent': consent, 'entityId': CIN});
    var jsonData = JSON.stringify(dataStore);
    jQuery.ajax({
        url: "https://testapi.kscan.in/v1/corp/profile",
        headers: {
            'Content-Type': "application/json",
            'x-karza-key': key
        },
        method: 'post',
        dataType: 'json',
        data: jsonData,
        error: function (xhr, status, errorThrown) {
            alert(errorThrown);
            $('.isloader').hide();

        },
        success: function (result) {
                   
                    $(".isloader").hide();
                    obj = result.result.directors;
                    var count = 0;
                    var arr = new Array();
                    var x  = 0;
                    $(obj).each(function (k, v) { 
                        var temp = {};
                        var dob = v.dob;
                        var dateAr = dob.split('-');
                        var newDate =  '';
                        if(dateAr!='')
                        {
                         
                            var newDate = dateAr[0] + '/' + dateAr[1] + '/' + dateAr[2]; 
                        }
                       
                        if (k >= 0)
                        {  
                            console.log(v.name);
                           /// $("#first_name"+count).val();
                            $("#address"+count).val(v.address);
                            $("#date_of_birth"+count).prop("type", "text").val(newDate);
                           temp['first_name'] = v.name;
                           temp['address'] = v.address;
                           temp['dob'] = newDate;
                           arr.push(temp);
                         
                             $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><div class='col-md-12'><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='hidden' id='ownerid"+x+"' name='ownerid[]' value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='"+v.name+"' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' value='"+newDate+"' class='form-control date_of_birth' tabindex='1' placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span></label><a href='javascript:void(0);' data-id='" + x + "' id='pan_verify" + x + "' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response" + x + "' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per' tabindex='1' placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification' tabindex='1' placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership' tabindex='1' placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'>INR</a><input type='text' name='networth[]' id='networth" + x + "' value='' class='form-control networth' tabindex='1' placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'>"+v.address+"</textarea></div></div> <h5 class='card-title form-head-h5 mt-3'>Document </h5><div class='row mt-2 mb-4'><div class='col-md-12'> <div class='prtm-full-block'><div class='prtm-block-content'><div class='table-responsive ps ps--theme_default' data-ps-id='9615ce02-be28-0492-7403-d251d7f6339e'><table class='table text-center table-striped table-hover'><thead class='thead-primary'><tr><th class='text-left'>S.No</th><th>Document Name</th><th>Document ID No.</th><th>Action</th></tr></thead><tbody><tr><td class='text-left'>1</td><td width='30%'>Pan Card</td><td width='30%'><div class='col-md-12'><a href='javascript:void(0);' id='ppan"+ x +"' data-id='"+ x +"' class='verify-owner-no verify-show veripan' style='top:0px'>Verify</a><input type='text'  name='veripan[]' id='veripan"+ x +"' value='' class='form-control' tabindex='1' placeholder='Enter PAN Number' required=''></div></td><td width='28%'><div class='file-browse float-left position-seta'><button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button><input type='file' name='verifyfile[]' class='verifyfile' id='verifyfile" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' required='required' name='panfile[]' data-id='" + x + "' class='panfile' id='panfile" + x + "'> </div> </td> </tr><tr> <td class='text-left'>2</td> <td width='30%'>Driving License</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='ddriving" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show veridl' style='top:0px;'>Verify</a> <input type='text' name='verifydl[]' id='verifydl" + x + "' value='' class='form-control verifydl' tabindex='1' placeholder='Enter DL Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' id='downloaddl" + x + "' name='downloaddl[]' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple='' class='downloaddl'> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' required='required'  name='dlfile[]' data-id='" + x + "' class='dlfile' id='dlfile" + x + "'> </div> </td> </tr> <tr> <td class='text-left'>3</td> <td width='30%'>Voter ID</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='vvoter" + x + "' data-id='" + x +"'  class='verify-owner-no verify-show verivoter' style='top:0px;'>Verify</a> <input type='text' name='verifyvoter[]' id='verifyvoter" + x + "' value='' class='form-control verifyvoter' tabindex='1' placeholder='Enter Voter's Epic Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadvoter[]' class='downloadvoter' id='downloadvoter" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  class='voterfile' name='voterfile[]' id='voterfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>4</td> <td width='30%'>Passport</td> <td width='30%' > <div class='col-md-12'> <a href='javascript:void(0);' id='ppassport" + x + "' data-id='" + x +"' class='verify-owner-no verify-show veripass' style='top:0px;'>Verify</a> <input type='text' name='verifypassport[]' id='verifypassport" + x + "' value='' class='form-control verifypassport' tabindex='1' placeholder='Enter File Number' required=''> </div> </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadpassport[]' class='downloadpassport'  id='downloadpassport" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  name='passportfile[]' class='passportfile' id='passportfile" + x + "'> </div> </td> </tr> </tr> <tr> <td class='text-left'>5</td> <td width='30%'>Photo</td> <td width='30%' > </td> <td width='28%'> <div class='file-browse float-left position-seta'> <button class='btn-upload btn-sm' type='button'> <i class='fa fa-download'></i></button> <input type='file' name='downloadphoto[]' class='downloadphoto' id='downloadphoto" + x + "' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''> </div> <div class='upload-btn-wrapper setupload-btn'> <button class='btn'>Upload</button> <input type='file' data-id='" + x + "' required='required'  name='photofile[]' name='photofile' id='photofile" + x + "'> </div> </td> </tr> </tbody> </table> <div class='ps__scrollbar-x-rail' style='left: 0px; bottom: 0px;'><div class='ps__scrollbar-x' tabindex='0' style='left: 0px; width: 0px;'></div></div><div class='ps__scrollbar-y-rail' style='top: 0px; right: 0px;'><div class='ps__scrollbar-y' tabindex='0' style='top: 0px; height: 0px;'></div></div> </div> </div> </div> </div> </div> </div></div></div> ");
                         x++;
                        }
                        count++;
                    });
                        var bizId = $('input[name=biz_id]').val();
                        var getRes = savePromoter(arr, bizId);
                        
                        ///$(".form-design").load(" .form-design");
                      /// window.location.href = "{{ route('promoter-detail',[])}}";
                       /// console.log( getRes);
                        
                }
    });
});

  /* save promoter details after cin number api hit */
      function  savePromoter(data, bizId)
      {
          
            var data = {'data' : data, 'biz_id' : bizId};
            jQuery.ajax({
                url: "/application/promoter-save",
                headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                method: 'post',
                contentType: "json",
                processData: false,
                data: JSON.stringify(data),
                success: function (data) {
                    var promoId = 0;
                    $(data.data).each(function(k,v){
                        console.log(v);
                        $("#ownerid"+promoId).val(v);
                        promoId++;
                    });
                       $("#rowcount").val(k);
                       return data;
                    }
            });
      }
              
      
///////////////Promotor web service for pan verified start here//////////////////////////
$(document).on('click', '.promoter_pan_verify', function () {
    var count = $(this).attr('data-id');
    var PAN = $("#pan_no" + count).val();
    var consent = "Y";
    var key = "h3JOdjfOvay7J8SF";
    var dataStore = ({'consent': consent, 'pan': PAN});
    var jsonData = JSON.stringify(dataStore);
    $('#pan_verify' + count).text('Waiting...');
    jQuery.ajax({
        url: "https://testapi.karza.in/v2/pan",
        headers: {
            'Content-Type': "application/json",
            'x-karza-key': key,
        },
        method: 'post',
        dataType: 'json',
        data: jsonData,
        error: function (xhr, status, errorThrown) {
            alert(errorThrown);
        },
        success: function (data) {
            var name = data['result']['name'];
            var request_id = data['request_id'];
            var status = data['status-code'];

            if (data['status-code'] == 101)
            {
                var MergeResonse = name.concat(request_id, status);
                $('#response' + count).val(MergeResonse);
                $('#pan_no' + count).attr('readonly', true);
                $('#pan_verify' + count).text('Verified')
                $('#pan_verify' + count).css('pointer-events', 'none');
                $('#pan_verify' + count).css({"border": "1px solid #cacdd1"});
                $('#pan_no' + count).css({"border": "2px solid #cacdd1"});
                $("#submit").attr("disabled", false);

            } else {
                $('#pan_verify' + count).text('Verify');
                $('#pan_verify' + count).css({"border": "1px solid red"});
                $('#pan_no' + count).css({"border": "2px solid red"});
                $("#submit").attr("disabled", true);
            }
        }
    });
});

var messages = {
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",
        chk_user_voterid_karza: "{{ URL::route('chk_user_voterid_karza') }}",
        chk_user_dl_karza: "{{ URL::route('chk_user_dl_karza') }}",
        chk_user_passport_karza: "{{ URL::route('chk_user_passport_karza') }}",
        chk_user_pan_status_karza: "{{ URL::route('chk_user_pan_status_karza') }}",
        

    };
 /////////////////Karja Api pan status /////////////////////////////////////
      
       $(document).on('click','.veripan',function () {
         var count = $(this).attr('data-id');
         var PAN = $("#veripan"+count).val();
         var dataStore = {'pan': 'BVZPS1846R','name':'Omkar Milind Shirhatti','dob':'17/08/1987','_token': messages.token };
            var postData = dataStore;
            $('#ppan'+count).text('Waiting...');
             jQuery.ajax({
            
                url: messages.chk_user_pan_status_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                   alert(errorThrown);
                },
                success: function (data) {
                                          if(data['status-code'] > 0)
                                           {   
                                                 $('#veripan'+count).attr('readonly',true);
                                                 $('#ppan'+count).text('Verified');
                                                 $('#ppan'+count).css('pointer-events','none');
                                                 $("#submit").attr("disabled", false); 
                                           }else{
                                               $('#ppan'+count).text('Verify');
                                               $('#veripan'+count).css({"border": "2px solid red"});
                                               $("#submit").attr("disabled", true);
                                          }                           
                                   
                                       
                                         }
                                    });
                                });
                                
      ///////////////////////DL api ///////////////
       $(document).on('click','.veridl',function () {
         var count = $(this).attr('data-id');
         var bizId = $('input[name=biz_id]').val();
         var app_id = $('#app_id').val();
         var ownerid = $('#ownerid'+count).val();
         var PAN = $("#verifydl"+count).val();
         var dl_no = $("#verifydl"+count).val();
         var dob = $("#date_of_birth"+count).val();
         var dataStore = {'dl_no': dl_no,'dob':dob,'_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id};
         ////var dataStore = {'dl_no': 'MH01 20090091406','dob':'12-06-1987','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id};
          var postData = dataStore;
            $('#ddriving'+count).text('Waiting...');
             jQuery.ajax({
                url: messages.chk_user_dl_karza,
                 method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                   alert(errorThrown);
                },
                  success: function (data) {
                                   if(data['status']==1)
                                           {   
                                                 $('#verifydl'+count).attr('readonly',true);
                                                 $('#ddriving'+count).text('Verified');
                                                 $('#verifydl'+count).css({"border": "1px solid #e9ecef"});
                                                 $('#ddriving'+count).css('pointer-events','none');
                                                 $("#submit").attr("disabled", false); 
                                           }else{
                                               $('#ddriving'+count).text('Verify');
                                               $('#verifydl'+count).css({"border": "2px solid red"});
                                               $("#submit").attr("disabled", true);
                                          }                           
                                   
                                       
                                         }
                                    });
                                });
                                
      
      /////////////////Karja Api Voter Card/////////////////////////////////////
      
       
       $(document).on('click','.verivoter',function () {
             var count = $(this).attr('data-id');
             var voterId = $("#verifyvoter"+count).val();
             var bizId = $('input[name=biz_id]').val();
             var app_id = $('#app_id').val();
             var ownerid = $('#ownerid'+count).val();
             var dataStore = {'epic_no':'SHA4722088','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id };
            ///  var dataStore = {'epic_no': 'SHA4722088','_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id };
             var postData = dataStore;
            $('#vvoter'+count).text('Waiting...');
            jQuery.ajax({
                url: messages.chk_user_voterid_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                   alert(errorThrown);
                },
                   success: function (data) {
                     console.log(data.value);
                                          if(data.value > 0)
                                           {   
                                                 $('#verifyvoter'+count).attr('readonly',true);
                                                 $('#vvoter'+count).text('Verified');
                                                 $('#vvoter'+count).css('pointer-events','none');
                                                 $('#verifyvoter'+count).css({"border": "1px solid #e9ecef"});
                                                 $("#submit").attr("disabled", false); 
                                           }else{
                                               $('#vvoter'+count).text('Verify');
                                               $('#verifyvoter'+count).css({"border": "2px solid red"});
                                               $("#submit").attr("disabled", true);
                                          }                           
                                   
                                       
                                         }
                                    });
                                });
                                
                                
 /////////////////Karja Api Passport Card/////////////////////////////////////
      
      
       $(document).on('click','.veripass',function ()  {
             var count = $(this).attr('data-id');
             var voterId = $("#verifypassport"+count).val();
             var bizId = $('input[name=biz_id]').val();
             var app_id = $('#app_id').val();
             var ownerid = $('#ownerid'+count).val();
             var file = $("#verifypassport"+count).val();
             var dob = $("#date_of_birth"+count).val();
             var dataStore = {'fileNo': file,'dob':dob,'_token': messages.token,'biz_id':bizId,'ownerid':ownerid,'app_id':app_id};
           //var dataStore = {'fileNo': 'BO3072344560818','dob':'17/08/1987','_token': messages.token };
            var postData = dataStore;
            $('#ppassport'+count).text('Waiting...');
            jQuery.ajax({
            
                url: messages.chk_user_passport_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                   alert(errorThrown);
                },
                   success: function (data) {
                                           if(data['status']==1)
                                           {   
                                             
                                                 $('#verifypassport'+count).attr('readonly',true);
                                                 $('#ppassport'+count).text('Verified');
                                                 $('#ppassport'+count).css('pointer-events','none');
                                                 $('#verifypassport'+count).css({"border": "1px solid #e9ecef"});
                                                 $("#submit").attr("disabled", false); 
                                           }else{
                                               $('#ppassport'+count).text('Verify');
                                               $('#verifypassport'+count).css({"border": "2px solid red"});
                                               $("#submit").attr("disabled", true);
                                          }                           
                                   
                                       
                                         }
                                    });
                                });
 </script>
 <style>
     .error
     {
         
         color:red;
     }
 </style>
 <script src="{{ url('backend/js/promoter.js') }}"></script>
@endsection