@extends('layouts.backend.admin-layout')
@section('content')
<ul class="main-menu">
    <li>
        <a href="company-details.php" class="active">Application details</a>
    </li>
    <li>
        <a href="cam.php">CAM</a>
    </li>
    <li>
        <a href="residence.php">FI/RCU</a>
    </li>
    <li>
        <a href="Collateral.php">Collateral</a>
    </li>
    <li>
        <a href="notes.php">Notes</a>
    </li>
    <li>
        <a href="commercial.php">Submit Commercial</a>
    </li>
</ul>
<!-- partial -->
<div class="content-wrapper">
    <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="company-details.php" class="active">Company Details</a>
        </li>
        <li>
            <a href="promoter-details.php">Promoter Details</a>
        </li>
        <li>
            <a href="document.php">Documents</a>
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
                <div class="card-body">
                    <div class=" form-fields">
                        <div class="col-md-12">
                            <h5 class="card-title form-head-h5">Promoter Details  </h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Promoter Name

                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter First Name" required="">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod " class="opacity-0">lastname
                                        </label>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter Last Name" required="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">DOB
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="date" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Pin Code" required="">
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-4">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">Gender
                                            <span class="mandatory">*</span>
                                        </label>
                                        <select class="form-control ">
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

                                        <a href="javascript:void(0);" class="verify-owner-no verify-show">Verify</a>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter PAN Number" required="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group password-input">
                                        <label for="txtPassword">Shareholding (%)

                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Shareholding " required="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtEmail">Educational Qualification

                                        </label>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Qualification">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="txtEmail">Other Ownerships
                                        </label>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Ownerships">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group INR">
                                        <label for="txtEmail">Networth


                                        </label><a href="javascript:void(0);" class="verify-owner-no"><i class="fa fa-inr" aria-hidden="true"></i></a>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" tabindex="1" placeholder="Enter Networth">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="txtCreditPeriod">Address
                                            <span class="mandatory">*</span>
                                        </label>
                                        <input type="text" name="employee" id="employee" value="" class="form-control" placeholder="Enter Your Address" required="">
                                    </div>
                                </div>

                            </div>
                            <h5 class="card-title form-head-h5 mt-3">Document </h5>									
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="prtm-full-block">       
                                        <div class="prtm-block-content">
                                            <div class="table-responsive ps ps--theme_default" data-ps-id="9615ce02-be28-0492-7403-d251d7f6339e">
                                                <table class="table text-center table-striped table-hover">
                                                    <thead class="thead-primary">
                                                        <tr>
                                                            <th class="text-left">S.No</th>
                                                            <th>Document Name</th>
                                                            <th>File Name	</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <th class="text-left">1</th>
                                                            <td>PAN Card	</td>
                                                            <td>drop-box.jpg	</td>
                                                            <td>
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="myfile">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-left">1</th>
                                                            <td>PAN Card	</td>
                                                            <td>drop-box.jpg	</td>
                                                            <td>
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="myfile">
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th class="text-left">1</th>
                                                            <td>PAN Card	</td>
                                                            <td>drop-box.jpg	</td>
                                                            <td>
                                                                <div class="file-browse float-left position-seta">
                                                                    <button class="btn-upload   btn-sm" type="button"> <i class="fa fa-download"></i></button>
                                                                    <input type="file" id="file_1" dir="1" onchange="FileDetails(this.getAttribute('dir'))" multiple="">
                                                                </div>
                                                                <div class="upload-btn-wrapper setupload-btn">
                                                                    <button class="btn">Upload</button>
                                                                    <input type="file" name="myfile">
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-right mt-3">
                                        <button type="button" id="btnAddMore" data-toggle="modal" data-target="#myModal" class="btn btn-primary">
                                            <i class="fa fa-plus"></i>
                                            Add Promoter
                                        </button>
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
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('jscript')
    @endsection