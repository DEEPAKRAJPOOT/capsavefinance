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
                        <img src="assets/images/business-document.png" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="assets/images/tick-image.png" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="count-active">
                <div class="count-heading"> Authorized Signatory KYC </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="assets/images/kyc.png" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="assets/images/tick-image.png" width="36" height="36">
                    </div>
                </div>
            </li>
            <li>
                <div class="count-heading">Business Documents </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="assets/images/business-document.png" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="assets/images/tick-image.png" width="36" height="36">
                    </div>
                </div>
            </li>
            <li>
                <div class="count-heading"> Associate Buyers </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="assets/images/buyers.png" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="assets/images/tick-image.png" width="36" height="36">
                    </div>
                </div>
            </li>
            <li>
                <div class="count-heading"> Associate Logistics </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="assets/images/logistics.png" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="assets/images/tick-image.png" width="36" height="36">
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

                <button type="button" id="btnAddMore" class="btn btn-add ml-auto">
                    <i class="fa fa-plus"></i>


                    Add Promoter

                </button>
            </div>
            <div class="col-md-12 form-design ">
                <div id="reg-box">
                    <form method="post" action="{{Route('authorized_signatory_save')}}" id="signupForm">
                        @csrf
                        <div class=" form-fields">
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
                                                        <label for="txtCreditPeriod" for="first_name">Promoter Name

                                                            <span class="mandatory" >*</span>
                                                        </label>
                                                        <input type="text" name="first_name[]" id="first_name1" vname="first_name1" value="" class="form-control first_name" placeholder="Enter First Name" >

                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="last_name " class="opacity-0">lastname
                                                        </label>
                                                        <input type="text" name="last_name[]" id="last_name1" value="" class="form-control" placeholder="Enter Last Name" >
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">




                                                <div class="col-md-4">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">DOB
                                                            <span class="mandatory">*</span>
                                                        </label>
                                                        <input type="date" name="date_of_birth[]" id="date_of_birth1" value="" class="form-control" tabindex="1" placeholder="Enter Pin Code" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">Gender
                                                            <span class="mandatory">*</span>
                                                        </label>
                                                        <select class="form-control" name="gender[]" id="gender1">
                                                            <option> Select Gender</option>
                                                            <option> Male </option>
                                                            <option>Female </option>


                                                        </select>
                                                    </div>
                                                </div>





                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="txtCreditPeriod">PAN Number

                                                            <span class="mandatory">*</span>
                                                        </label>
                                                        <input type="text" name="pan_no[]" id="pan_no1" value="" class="form-control" placeholder="Enter Email" >
                                                    </div>
                                                </div>


                                            </div>

                                            <div class="row">

                                                <div class="col-md-4">

                                                    <div class="form-group password-input">
                                                        <label for="txtPassword">Shareholding (%)

                                                            <span class="mandatory">*</span>
                                                        </label>
                                                        <input type="text" name="share_per[]" id="share_per1" value="" class="form-control" tabindex="1" placeholder="Enter Mobile Number" >
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="txtEmail">Educational Qualification

                                                        </label>
                                                        <input type="text" name="edu_qualification[]" id="edu_qualification1" value="" class="form-control" tabindex="1" placeholder="Enter Home Ph.">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="txtEmail">Other Ownerships


                                                        </label>
                                                        <input type="text" name="other_ownership[]" id="other_ownership1" value="" class="form-control" tabindex="1" placeholder="Enter Home Ph.">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="networth">Networth 


                                                        </label><a href="javascript:void(0);" class="verify-owner-no">INR</a>
                                                        <input type="text" name="networth[]" id="networth1" value="" class="form-control" tabindex="1" placeholder="Enter Home Ph.">
                                                    </div>
                                                </div>

                                            </div>



                                        </div>


                                        <div class="col-md-8">

                                            <div class="form-group password-input">
                                                <label for="address">Address </label>
                                                    <textarea class="form-control textarea" placeholder="Enter Address" name="address[]" id="address1"></textarea>

                                            </div>
                                        </div>
                                    </div>
                                    <!--
                                    <div class="col-md-4">
                                            <div class="col-md-12 ">
                                                    <h3 class="full-width">Documents

                                                    </h3>
                                                    <p><small>Maximum file upload size : 5MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX
                                                            </small></p>
                                            </div>

                                            <div class="col-md-12">
                                                    <div id="uploadsection3" class="fil-uploaddiv" style="display: block;">
                                                            <div class="row ">
                                                                    <div class="col-md-12">
                                                                            <div class="justify-content-center d-flex">
                                                                                    <label class="mb-0"><span class="file-icon"><img src="assets/images/contractdocs.svg"> </span> PAN Card * </label>
                                                                                    <div class="ml-auto">
                                                                                            <div class="file-browse">
                                                                                                    <button class="btn btn-upload btn-sm"> <i class="fa fa-upload"></i> </button>
                                                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                                            </div>
                                                                                    </div>

                                                                            </div>
                                                                            <div id="filePath_1" class="filePath"></div>
                                                                            <hr>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                            <div class="justify-content-center d-flex">
                                                                                    <label class="mb-0"><span class="file-icon"><img src="assets/images/contractdocs.svg"> </span> Address Proof * </label>
                                                                                    <div class="ml-auto">
                                                                                            <div class="file-browse">
                                                                                                    <button class="btn btn-upload btn-sm"> <i class="fa fa-upload"></i> </button>
                                                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                                            </div>
                                                                                    </div>

                                                                            </div>
                                                                            <div id="filePath_1" class="filePath"></div>
                                                                            <hr>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                            <div class="justify-content-center d-flex">
                                                                                    <label class="mb-0"><span class="file-icon"><img src="assets/images/contractdocs.svg"> </span> Partner's Photo * </label>
                                                                                    <div class="ml-auto">
                                                                                            <div class="file-browse">
                                                                                                    <button class="btn btn-upload btn-sm"> <i class="fa fa-upload"></i></button>
                                                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                                            </div>
                                                                                    </div>

                                                                            </div>
                                                                            <div id="filePath_1" class="filePath"></div>

                                                                    </div>
                                                            </div>
                                                    </div>
                                            </div>

                                    </div>
                                    -->

                                </div>

                            </div>

                            <span class="form-fields-appand"></span>

                            <div class="term-apply ">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" value="">
                                    <p class="mb-0 ml-2">I authorize Zuron to pull my consumer/commercial CIBIL.</p>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" value="">
                                    <p class="mb-0 ml-2">I agree to the <a href="#">Terms of Use </a> & <a href="#"> Privacy Policy of Zuron</a></p>
                                </div>
                            </div>

                            <div class="d-flex btn-section ">
                                <div class="col-md-4 ml-auto text-right">
                                    <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'business-documents.php'">
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
        $('#submit').on('click', function(event) {
            $('input.first_name').each(function() {
                $(this).rules("add", 
                    {
                        required: true
                    })
            });            

            // test if form is valid 
            if($('form#signupForm').validate().form()) {
                console.log("validates");
                $( "#actual_submit" ).trigger('click');
            } else {
                console.log("does not validate");
            }
        })
        $("#btnAddMore").on('click', addInput);
        $('form#signupForm').validate();
        });
        
    </script>

    <script>
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
        var x=2;
        function addInput() {
            $(".form-fields-appand").append("<div class='form-sections'><button class='close clsdiv' type='button'>x</button><div class='row'><div class='col-md-12'><div class='col-md-12'><h3>Promoter</h3></div><div class='col-md-12'><div class='row'><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod' for='first_name'>Promoter Name<span class='mandatory'>*</span></label><input type='text' name='first_name[]' vname='first_name"+x+"' id='first_name"+x+"' value='' class='form-control first_name' placeholder='Enter First Name' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod ' class='opacity-0'>lastname</label><input type='text' name='last_name[]' id='employee' value='' class='form-control' placeholder='Enter Last Name' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>DOB<span class='mandatory'>*</span></label><input type='date' name='date_of_birth[]' id='employee' value='' class='form-control' tabindex='1' placeholder='Enter Pin Code' ></div></div><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Gender<span class='mandatory'>*</span></label><select class='form-control' name='gender[]'><option> Select Gender</option><option> Male </option><option>Female </option></select></div></div><div class='col-md-4'><div class='form-group'><label for='txtCreditPeriod'>PAN Number<span class='mandatory'>*</span></label><input type='text' name='pan_no[]' id='employee' value='' class='form-control' placeholder='Enter Email' ></div></div></div><div class='row'><div class='col-md-4'><div class='form-group password-input'><label for='txtPassword'>Shareholding (%)<span class='mandatory'>*</span></label><input type='text' name='share_per[]' id='employee' value='' class='form-control' tabindex='1' placeholder='Enter Mobile Number' ></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Educational Qualification</label><input type='text' name='edu_qualification[]' id='employee' value='' class='form-control' tabindex='1' placeholder='Enter Home Ph.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Other Ownerships</label><input type='text' name='other_ownership[]' id='employee' value='' class='form-control' tabindex='1' placeholder='Enter Home Ph.'></div></div><div class='col-md-4'><div class='form-group'><label for='txtEmail'>Networth </label><a href='javascript:void(0);' class='verify-owner-no'>INR</a><input type='text' name='networth[]' id='employee' value='' class='form-control' tabindex='1' placeholder='Enter Home Ph.'></div></div> </div></div><div class='col-md-8'><div class='form-group password-input'><label for='txtPassword'>Address<span class='mandatory'>*</span></label><textarea class='form-control textarea' placeholder='Enter Address' name='address[]'></textarea></div></div></div><!--<div class='col-md-4'><div class='col-md-12 '><h3 class='full-width'>Documents</h3><p><small>Maximum file upload size : 5MB. Allowed Formats : JPG,PNG,PDF,DOC,DOCX</small></p></div><div class='col-md-12'><div id='uploadsection3' class='fil-uploaddiv' style='display: block;'><div class='row '><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> PAN Card * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i> </button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div><hr></div><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> Address Proof * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i> </button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div><hr></div><div class='col-md-12'><div class='justify-content-center d-flex'><label class='mb-0'><span class='file-icon'><img src='assets/images/contractdocs.svg'> </span> Partner's Photo * </label><div class='ml-auto'><div class='file-browse'><button class='btn btn-upload btn-sm'> <i class='fa fa-upload'></i></button><input type='file' id='file_1' dir='1' onchange='FileDetails(this.getAttribute('dir'))' multiple=''></div></div></div><div id='filePath_1' class='filePath'></div></div></div></div></div></div>--></div></div> ");
            x++;
        }
    </script>
    @endsection