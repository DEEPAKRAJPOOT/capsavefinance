@extends('layouts.guest')
@section('content')

<style>
    .opacity-0 {
        opacity: 0
    }

</style>
<div class="step-form pt-5">
    <div class="container">
        <ul id="progressbar">
            <li class="active">
                <div class="count-heading">Business Information </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('frontend/assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('frontend/assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
           <li class="count-active">
				<div class="count-heading"> Promoter Details </div>
				<div class="top-circle-bg">
					<div class="count-top">
						<img src="{{url('frontend/assets/images/kyc.png')}}" width="36" height="36">
					</div>
					<div class="count-bottom">
						<img src="{{url('frontend/assets/images/tick-image.png')}}" width="36" height="36">
					</div>
				</div>
			</li>
            <li class="count-active">
                <div class="count-heading">Documents</div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{url('frontend/assets/images/business-document.png')}}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{url('frontend/assets/images/tick-image.png')}}" width="36" height="36">
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="container">
        <div class="mt-4">
            <div class="form-heading pb-3 d-flex pr-0">
                <h2>Promoter Details
                    <small> ( Please fill the Director's Information )</small>
                </h2>
               
            </div>
            <div class="col-md-12 form-design ">
                <div id="reg-box">
                    <form id="signupForm">
               @csrf
                       <input type="hidden" name="biz_id" value="{{ request()->get('biz_id') }}">
                       <input type="hidden" name="app_id" value="{{ request()->get('app_id') }}">
              
                    <div class=" form-fields">
                          @csrf
                           @php ($i = 0)
                             @foreach($ownerDetails as $row)    @php ($i++)
                            <div class="form-sections">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <h3>Promoter</h3>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod" for="first_name" >Promoter Name

                                                            <span class="mandatory">*</span>
                                                        </label>
                                                        <input type="hidden" id="rowcount" value="{{count($ownerDetails)}}">
                                                        <input type="hidden" name="ownerid[]" id="ownerid{{isset($row->first_name) ? $i : '1'}}" value="{{$row->biz_owner_id}}">   
                                                        <input type="text" name="first_name[]"  id="first_name{{isset($row->first_name) ? $i : '1'}}" vname="first_name1" value="{{$row->first_name}}" class="form-control first_name" placeholder="Enter First Name" >
                                                      </div>
                                                </div>
                                                  <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Last Name
                                        </label>
                                         <input type="text" name="last_name[]" id="last_name{{isset($row->first_name) ? $i : '1'}}" value="{{$row->last_name}}" class="form-control last_name" placeholder="Enter Last Name" >
                                                     </div>
                                </div>
 <div class="col-md-4">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">DOB
                                                            <span class="mandatory">*</span>
                                                        </label>
                                                        <input type="text" name="date_of_birth[]" id="date_of_birth{{isset($row->first_name) ? $i : '1'}}" value="{{$row->date_of_birth}}" class="form-control date_of_birth datepicker-dis-fdate" tabindex="1" placeholder="Enter Date Of Birth" >
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
                                                            <span class=" text-success" id="successpanverify{{isset($row->first_name) ? $i : '1'}}" style="display:{{ (isset($row->pan->pan_gst_hash)) ? 'inline' : 'none' }}"><i class="fa fa-check-circle" aria-hidden="true"></i> <i>Verified Successfully</i> </span>
                                                          <span class=" text-danger" id="failurepanverify{{isset($row->first_name) ? $i : '1'}}" style="display:none;"><i class="fa fa-close" aria-hidden="true"></i> <i>Not Verified</i> </span>
                                                        </label>
                                                        <a href="javascript:void(0);" data-id="{{isset($row->first_name) ? $i : '1'}}" id="pan_verify{{isset($row->first_name) ? $i : '1'}}" class="verify-owner-no promoter_pan_verify" style="pointer-events:{{ (isset($row->pan->pan_gst_hash)) ? '    none' : '' }}">{{ (isset($row->pan->pan_gst_hash)) ? '    Verified' : 'Verify' }}</a>
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
                                                        <label for="networth">Networth 
 </label> <a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a> <input type="text" name="networth[]" id="networth{{isset($row->first_name) ? $i : '1'}}" value="{{$row->networth}}" class="form-control networth" tabindex="1" placeholder="Enter Networth">
                                                    </div>
                                                </div>

                                            </div>



                                        </div>


                                        <div class="col-md-8">

                                            <div class="form-group password-input">
                                                <label for="address">Address </label>
                                                <textarea class="form-control textarea address" placeholder="Enter Address" name="owner_addr[]" id="address{{isset($row->first_name) ? $i : '1'}}">{{$row->owner_addr}}</textarea>

                                            </div>
                                        </div>
                                        
                                    </div>
                                   
                                </div>

                            </div>
                           @endforeach
                            <div class="row">
					<div class="col-md-12">
					<div class="text-right mt-3">
								
                    <button type="button" id="btnAddMore" class="btn btn-primary btn-add ml-auto">
                    <i class="fa fa-plus"></i>
                    Add Promoter
                    </button>
							</div>
					</div>						
					</div>
                           <span class="form-fields-appand"></span>   
                          <!--
                            <div class="term-apply ">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" value="" name="cibil" id="privacy_chk" class="privacy_chk">
                                    <p class="mb-0 ml-2">I authorize Capsave to pull my consumer/commercial CIBIL.</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" value="" name="privacy" id="privacy_chk1" class="privacy_chk">
                                    <p class="mb-0 ml-2">I agree to the <a href="#">Terms of Use </a> & <a href="#"> Privacy Policy of Capsave</a></p>
                                </div>
                            </div>
                      -->
                            <div class="d-flex btn-section ">
                                <div class="col-md-4 ml-auto text-right">
                                    <!-- <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = '#'"> -->
                                    <input type="button" value="Save and Continue" id="submit" class="btn btn-primary">
                                    <input type="submit" value="Save" id="actual_submit" style="display: none;">
                                </div>
                            </div>


                        </div>
                    </form>
                </div>
                
            </div>
        </div>
        
    </div>
   @endsection
   
    @section('scripts')
    <script type="text/javascript">
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
                      $(".pan_no").each(function(k,v){
                          panCount++;
                        var result =  $("#pan_verify"+panCount).text();
                       
                        if(result=="Verify")
                        {
                             $('#failurepanverify'+panCount).show();
                             $('#failurepanverify'+panCount).focus();
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
                                window.location.href = "{{ route('document', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id') ]) }}";
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
         $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><button class='close clsdiv' type='button'>x</button><div class='col-md-12'><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' value='' class='form-control date_of_birth datepicker-dis-fdate' tabindex='1' placeholder='Enter Date Of Birth' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span><span class='text-success' id='successpanverify"+x+"' style='display:none;'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='failurepanverify"+x+"' style='display:none;''><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span></label><a href='javascript:void(0);' data-id='"+x+"' id='pan_verify"+x+"' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[] id='response"+x+"' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per' tabindex='1' placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification' tabindex='1' placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership' tabindex='1' placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' name='networth[]' id='networth" + x + "' value='' class='form-control networth' tabindex='1' placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]' id='address" + x + "'></textarea></div></div></div><!--<div class='col-md-4'><div class='col-md-12 '><h3 class='full-width'>Documents</h3><p><small>Maximum file upload size : 5MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX</small></p></div><div class='col-md-12'><div id='uploadsection3' class='fil-uploaddiv' style='display: block;'><div class='row '><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> PAN Card * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i> </button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div><hr></div><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> Address Proof * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i> </button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div><hr></div><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> Partner's Photo * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i></button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div></div></div></div></div></div>--></div></div> ");
                    x++;
         });
        //////////CIN webservice for get promoter details start here//////////////////////////////////////        
        $(document).on('click', '.clsdiv', function () {
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

               ///url: "https://stub.kscan.in/v1/corp/profile",

                url:"https://testapi.kscan.in/v1/corp/profile",
                 headers: {
                    'Content-Type': "application/json",
                    'x-karza-key': key
                },
                method: 'post',
                dataType: 'json',
                data: jsonData,
                error:function (xhr, status, errorThrown) {
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
                      
                        $("#first_name"+count).val(v.name);
                        $("#address"+count).val(v.address);
                        $("#date_of_birth"+count).prop("type", "text").val(newDate);
                        
                        if (k >= 0)
                        {  
                           temp['first_name'] = v.name;
                           temp['address'] = v.address;
                           temp['dob'] = newDate;
                           arr.push(temp);
                         
                             $(".form-fields-appand").append("<div class='fornm-sections'><div class='row'><div class='col-md-12'><!--<button class='close clsdiv' type='button'>x</button>--><div class='col-md-12'><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label> <input type='hidden' id='ownerid"+x+"' name='ownerid[]' value=''><input type='text' name='first_name[]' vname='first_name" + x + "' id='first_name" + x + "' value='" + v.name + "' class='form-control first_name'  placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name' >Last Name</label><input type='text' name='last_name[]' id='last_name" + x + "' value='' class='form-control last_name' placeholder='Enter Last Name' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='text' name='date_of_birth[]'  id='date_of_birth" + x + "' value='" + newDate + "' class='form-control date_of_birth datepicker-dis-fdate' tabindex='1'  placeholder='Enter Date Of Birth'></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='gender'>Gender<span class='mandatory'>*</span></label><select class='form-control gender' name='gender[]'   id='gender" + x + "'><option value=''> Select Gender</option><option value='1'> Male </option><option value='2'>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='pan_no'>PAN Number<span class='mandatory'>*</span><span class='text-success' id='successpanverify"+ x +"' style='display:none'><i class='fa fa-check-circle' aria-hidden='true'></i> <i>Verified Successfully</i> </span><span class=' text-danger' id='failurepanverify"+ x +"' style='display:none'><i class='fa fa-close' aria-hidden='true'></i> <i>Not Verified</i></span></label><a href='javascript:void(0);' data-id='"+ x +"' id='pan_verify"+ x +"' class='verify-owner-no promoter_pan_verify'>Verify</a><input type='text' name='pan_no[]'  id='pan_no" + x + "' value='' class='form-control pan_no' placeholder='Enter Pan No' ><input name='response[]' id='response"+ x +"' type='hidden' value=''></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='share_per" + x + "' id='employee' value='' class='form-control share_per' tabindex='1' placeholder='Enter Shareholder' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]'  id='edu_qualification" + x + "' value='' class='form-control edu_qualification' tabindex='1' placeholder='Enter Education Qualification.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='other_ownership" + x + "' value='' class='form-control other_ownership' tabindex='1' placeholder='Enter Other Ownership'></div></div><div class='col-md-4'><div class='form-group INR'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'><i class='fa fa-inr' aria-hidden='true'></i></a><input type='text' name='networth[]' id='networth" + x + "' value='' class='form-control networth' tabindex='1' placeholder='Enter Networth'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea address' placeholder='Enter Address' name='owner_addr[]'  id='address" + x + "'>" + v.address + "</textarea></div></div></div><!--<div class='col-md-4'><div class='col-md-12 '><h3 class='full-width'>Documents</h3><p><small>Maximum file upload size : 5MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX</small></p></div><div class='col-md-12'><div id='uploadsection3' class='fil-uploaddiv' style='display: block;'><div class='row '><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> PAN Card * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i> </button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div><hr></div><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> Address Proof * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i> </button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div><hr></div><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> Partner's Photo * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i></button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div></div></div></div></div></div>--></div></div> ");
                             x++;
                        }
                        count++;
                    });
                        var bizId = $('input[name=biz_id]').val();
                        var appId = $('input[name=app_id]').val();
                        var getRes = savePromoter(arr, bizId, appId);
                }
            });
        });
      ///////////////Promotor web service for pan verified start here//////////////////////////
       
      /* save promoter details after cin number api hit */
      function  savePromoter(data, bizId, appId)
      {
          
            var data = {'data' : data, 'biz_id' : bizId, 'app_id' : appId};
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
              
      
      /////////////////Karja Api/////////////////////////////////////
      
      var messages = {
        chk_user_pan_karza: "{{ URL::route('chk_user_pan_karza') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
       $(document).on('click','.promoter_pan_verify',function () {
      
            var count = $(this).attr('data-id');
            var PAN = $("#pan_no"+count).val();
            var dataStore = {'pan': PAN,'_token': messages.token };
            var postData = dataStore;
            $('#pan_verify'+count).text('Waiting...');
             jQuery.ajax({
            
                url: messages.chk_user_pan_karza,
                method: 'post',
                dataType: 'json',
                data: postData,
                error: function (xhr, status, errorThrown) {
                                   alert(errorThrown);
                                   
                },
                success: function (data) {
                                  var status =  data['status-code'];
                                  if(status==101)
                                            {   
                                                 var name = data['result']['name'];
                                                 var request_id = data['request_id'];
                                                 var MergeResonse = name.concat(request_id, status);       
                                                  $('#response'+count).val(MergeResonse);
                                                  $('#pan_no'+count).attr('readonly',true);
                                                  $('#pan_verify'+count).text('Verified');
                                                  $('#successpanverify'+count).show();
                                                  $('#failurepanverify'+count).hide();
                                                  $('#pan_verify'+count).css('pointer-events','none');
                                                  $("#submit").attr("disabled", false); 
                                                  
                                            }else{
                                                $('#pan_verify'+count).text('Verify');
                                                 $('#successpanverify'+count).hide();
                                                $('#failurepanverify'+count).show();
                                                $("#submit").attr("disabled", true);
                                           }                           
                                    
                                        }

                                    });
                                });
      
      
      
      
  </script>
@endsection