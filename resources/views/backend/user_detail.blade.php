@extends('layouts.backend.admin-layout')

@section('content')






<div class="col-md-10 dashbord-white">

    <div class="form-section">

        <div class="breadcrumbs">
            <div class="d-md-flex mb-3">
                <div class="breadcrumbs-bg">

                    <ul>
                        <li>
                            <a onclick="window.location.href = '{{ route('show_user') }}'"> Manage Users</a>
                        </li>
                        <li>
                            User KYC Details
                        </li>

                    </ul>

                </div>
                @php
                        $bounty = Helpers::getKycDetails($userData->user_id); 
                        if(isset($bounty) && $bounty['is_approve']==1) {
                         $boun_appr = 'Approved';
                         $boun_appr_status = 'btn-approved';
                        } else {
                         $boun_appr = 'Disapproved';
                         $boun_appr_status = 'btn-disapproved';
                        }
                @endphp
                <div class="ml-md-auto d-flex form action-btns">
<!--                    <select class="form-control" name="uname" required="">
                        <option>Select Status</option>
                        <option>Approved</option>
                        <option>Pending </option>
                        <option>Disapproved </option>
                        <option>Locked </option>
                    </select>-->
                    <button type="button" class="btn btn-default btn-sm {{ $boun_appr_status }}" data-toggle="modal" data-target="#Approved_Action">{{$boun_appr }}</button>
<!--                    <button type="button" class="btn btn-default btn-sm btn-disapproved">Disapproved</button>-->
<!--                    <button type="button" class="btn btn-default btn-sm btn-locked">Lock</button>-->
                </div>
            </div>
        </div>
        <div class="tabs-section">

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#tab01">Registration Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab02">User KYC Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#tab03">Third Party Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#tab04">Documents</a>
                </li>

            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane container active" id="tab01">

                    <div class="row mb-4">
                        <div class="col-md-3 view-detail">
                            <label>Nationality</label>
                            <span>India</span>
                        </div>
                        <div class="col-md-3 view-detail">
                            <label>First Name</label>
                            <span>{{$userData->f_name}}</span>
                        </div>
                        <div class="col-md-3 view-detail">
                            <label>Middle Name</label>
                            <span>{{$userData->m_name}}</span>
                        </div>
                        <div class="col-md-3 view-detail">
                            <label>Last Name</label>
                            <span>{{$userData->l_name}}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 view-detail">
                            <label>Official Email</label>
                            <span>{{$userData->email}}</span>
                        </div>
                        <div class="col-md-3 view-detail">
                            <label>Official Mobile No.</label>
                            <span>{{$userData->phone_no}}</span>
                        </div>

                    </div>

                </div>
                <div class="tab-pane container" id="tab02">

                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#subtab01">Personal Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#subtab02">Profesional Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#subtab03">Financial Information</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane container active" id="subtab01">

                            <div class="row mb-4">

                                <div class="col-md-3 view-detail">
                                    <label>First Name</label>
                                    <span>{{$userData->f_name}}</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Middle Name</label>
                                    <span>{{$userData->m_name}}</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Last Name</label>
                                    <span>{{$userData->l_name}}</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Gender</label>
                                    <span>Male</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Date of Birth</label>
                                    <span>{{$userPersonalData->date_of_birth}}</span>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Country of Birth</label>
                                    <span>India</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>State of birth</label>
                                    <span>Delhi</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>City of birth</label>
                                    <span>New Delhi</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Father's Name</label>
                                    <span>Siddiqui</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Mother's first name</label>
                                    <span>Zoya</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Mother's maiden name</label>
                                    <span>Khan</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Register Nr. & Place</label>
                                    <span>No</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Nationality</label>
                                    <span> Select Nationality</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Secondary Nationality</label>
                                    <span>Select Secondary Nationality</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Document Type</label>
                                    <span>PAN Card</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Document Number</label>
                                    <span>4457457</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Issuance Date</label>
                                    <span>12 Mar, 2012</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Expiry Date</label>
                                    <span>12 Mar. 2024</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Social Media</label>
                                    <span>Facebook</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Social Media Link</label>
                                    <span>www.facebook.com/Anuj_Dubey</span>

                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Residence Status</label>
                                    <span>Yes</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Family Status</label>
                                    <span>Single</span>

                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Legal Guardian’s Name <small>(if applicable)</small></label>
                                    <span>Azar Khan</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Legal maturity date</label>
                                    <span>12, Mar, 2019</span>

                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Educational Level </label>
                                    <span> B.Des </span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Residency card </label>
                                    <span> No </span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label> Political position </label>
                                    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget. </p>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label> Political position </label>
                                    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget.</p>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="heading col-md-12">
                                    <h4>Family Information</h4>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Spouse first Name </label>
                                    <span> Sofia </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Spouse maiden name </label>
                                    <span> Khan </span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Spouse professional Status </label>
                                    <span> Select professional status </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Spouse’s profession (if only) </label>
                                    <span> UX Design </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Spouse’s employer (if any) </label>
                                    <span> Fiyo Design Studios</span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="heading col-md-12 sub-heading">
                                    <h4>Children Information</h4>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Child 1 </label>
                                    <span> Naushad </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Date of Birth</label>
                                    <span> 12, Mar, 1999 </span>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="heading col-md-12">
                                    <h4>Address Information</h4>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Country </label>
                                    <span> UAE </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> City </label>
                                    <span> Dubai </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Region </label>
                                    <span> Dubai 1 </span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Building </label>
                                    <span> Building 1 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Floor </label>
                                    <span> 7th </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Street </label>
                                    <span> Street 1 </span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Postal Code </label>
                                    <span> 110091 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>P.O Box </label>
                                    <span> 11009147 </span>
                                </div>

                            </div>
                            <div class="row mb-4">

                                <div class="col-md-3 view-detail">
                                    <label>Email </label>
                                    <span> info@gmail.com </span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Telephone No. </label>
                                    <span> 120458458 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Mobile No. </label>
                                    <span> +91 9858478569 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Fax No. </label>
                                    <span> +1 323 555 1234</span>
                                </div>

                            </div>

                        </div>
                        <div class="tab-pane container" id="subtab02">
                            <div class="row mb-4">

                                <div class="col-md-3 view-detail">
                                    <label>Professional Status</label>
                                    <span>Employed</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label>Profession/ Occupation in detail Previous Profession/ Occupation if retired</label>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget.</p>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label>Position/ Job title Last Position/ Job title if retired</label>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget.</p>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Date of employment/ Retirement</label>
                                    <span>12 Mar, 2017</span>
                                </div>
                                <div class="col-md-8 view-detail">
                                    <label>Last monthly salary if retired</label>
                                    <span>24 Nov Jun, 2017</span>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="heading col-md-12">
                                    <h4>For Sole Proprietorship/Self Employed, Please Specify</h4>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Commercial name</label>
                                    <span>12 Mar, 2017</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Date of establishment</label>
                                    <span>12 Mar, 2017</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Country of establishment</label>
                                    <span>India</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Commercial Register No.</label>
                                    <span>4574474</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Place</label>
                                    <span>India</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Country</label>
                                    <span>India</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Country(ies) of Activity</label>
                                    <span>Select Country(ies) of Activity</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Syndicate No.</label>
                                    <span>4574474</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Taxation ID No.</label>
                                    <span>4574174DRF154</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Taxation ID</label>
                                    <span>45474574ADR</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Annual Business Turnover (in $)</label>
                                    <span>$ 1,20,00,000</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Main Suppliers</label>
                                    <span>Sofocle Tecnologies</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Main Clients</label>
                                    <span>Sofocle Tecnologies</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Name of authorized signatory</label>
                                    <span>Roach Sanderson</span>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="heading col-md-12">
                                    <h4>Business Address</h4>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Country </label>
                                    <span> UAE </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> City </label>
                                    <span> Dubai </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Region </label>
                                    <span> Dubai 1 </span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Building </label>
                                    <span> Building 1 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Floor </label>
                                    <span> 7th </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label> Street </label>
                                    <span> Street 1 </span>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Postal Code </label>
                                    <span> 110091 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>P.O Box </label>
                                    <span> 11009147 </span>
                                </div>

                            </div>
                            <div class="row mb-4">

                                <div class="col-md-3 view-detail">
                                    <label>Email </label>
                                    <span> info@gmail.com </span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Telephone No. </label>
                                    <span> 120458458 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Mobile No. </label>
                                    <span> +91 9858478569 </span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Fax No. </label>
                                    <span> +1 323 555 1234</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="heading col-md-12">
                                    <h4>Mailing Address</h4>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label> Hold Mail </label>
                                    <span> sales.prolitu@prolitus.com </span>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label> In case of sending documents through mail, please specify mailing address </label>
                                    <span> Secondary Address </span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label> Relation with Exchange Company/ Establishment </label>
                                    <p> Are you or your spouse or any of your dependents (ascendants and descendants) the owner or shareholder or partner or director or signatory of an exchange establishment/ company? If yes please disclose the full names of the concerned parties and the full name and details of the establishment / company </p>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Name of Concerned Party</label>
                                    <span> Microsoft</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label>Name/Details of Establishment/Company</label>
                                    <span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget.</span>
                                </div>

                            </div>

                        </div>
                        <div class="tab-pane container" id="subtab03">
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Source of funds</label>
                                    <span>Profession</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Jurisdiction of Funds</label>
                                    <span>$ 12,00,0000</span>
                                </div>

                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Annual Income (in USD)</label>
                                    <span>100,001 to 200,000</span>
                                </div>
                                <div class="col-md-3 view-detail">
                                    <label>Estimated Wealth (in USD)</label>
                                    <span>1,000,001 to 2,500,000</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label>Kindly provide details on the source(s) of your wealth</label>
                                    <span>Commercial business activities</span>
                                </div>

                            </div>
                            <hr>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>US TIN Code</label>
                                    <span>101274</span>
                                </div>
                                <div class="col-md-4 view-detail">
                                    <label>Was US citizenship abandoned after June 2014?</label>
                                    <span>Yes</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>Please specify date of abandonment</label>
                                    <span>12 Mar, 2007</span>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label>Reason</label>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget.</p>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 view-detail">
                                    <label>Justification (If reason B is selected)</label>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ornare nunc sit amet posuere eleifend. Vivamus consequat tincidunt massa, finibus convallis leo pharetra eget.</p>
                                </div>

                            </div>
                            <div class="row mb-4">
                                <div class="col-md-3 view-detail">
                                    <label>TIN Country</label>
                                    <span>America</span>
                                </div>
                                <div class="col-md-8 view-detail">
                                    <label>TIN (Taxpayer Identification Number) or functional equivalent of the TIN</label>
                                    <span>4574474</span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane container p-0 mt-3" id="tab03">
                    <div class="d-md-flex mt-3 mb-3">

                        <div class="ml-md-auto">
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Repull Data</button>
                        </div>
                    </div>

                    <div class="table-responsive">

                        <!--<table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th width="30%">Transaction ID</th>
                                    <th width="30%">Request Date</th>
                                    <th width="40%">Status</th>
                                    <th class="text-right">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ASD4574574547</td>
                                    <td>September 14, 2013</td>
                                    <td><span class="text-requested">Requested</span></td>
                                    <td class="text-right">
                                        <button type="button" class="getSimilarWCI"  id="getSimilarWCI">Get Similar</button>
                                    </td>
                                </tr>
                                

                            </tbody>
                        </table>-->

                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th width="30%">Name</th>
                                    <th width="30%">Dob</th>
                                    <th width="40%">Country</th>
                                    <th class="text-right">Actions</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{$userData->f_name}}</td>
                                    <td>September 14, 2013</td>
                                    <td><span class="text-requested">India</span></td>
                                    <td class="text-right">
                                        <button type="button" class="getSimilarWCI"  id="getSimilarWCI">Get Similar</button>
                                    </td>
                                </tr>


                            </tbody>
                        </table>




                    </div>

                    <!--Similar records  Start-->

                    <div class="table-responsive">

                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th width="30%">Name</th>
                                    <th width="30%">Dob</th>
                                    <th width="40%">Status</th>
                                    <th class="text-right">Actions</th>

                                </tr>
                            </thead>
                            <tbody id="similarRecords">
                                <tr>
                                    <td>putin</td>
                                    <td>September 14, 2013</td>
                                    <td><span class="text-requested">Requested</span></td>
                                    <td class="text-right">
                                        <button type="button" class="getSimilar"  id="getSimilar"></button>


                                    </td>
                                </tr>


                            </tbody>
                        </table>

                    </div>

                    <!--Similar records End-->

                </div>








                <div class="tab-pane container  p-0 mt-3" id="tab04">

                    <div class="row mb-2 align-items-center">
                        <div class="col-md-4 view-detail pl-4">
                            <label class="m-0">Document Name</label>

                        </div>
                        <div class="col-md-4 view-detail pl-2">
                            <label class="m-0">Uploaded on</label>

                        </div>
                        <div class="col-md-4 view-detail text-right">
                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-refresh"></i> Repull Data</button>

                        </div>
                    </div>

                    <div class="documents-list full-width  pl-1">
                        <ul>
                            <li>
                                <div class="d-flex justify-content-between mb-3">
                                    <h4>Passport Copy</h4>
                                    <button type="button" class="transparent-btn">Download All <i class="fa fa-download"></i></button>
                                </div>

                                <table class="data-download full-width">
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                </table>

                            </li>
                            <li>
                                <div class="d-flex justify-content-between mb-3">
                                    <h4>Passport Size Photo</h4>
                                    <button type="button" class="transparent-btn">Download All <i class="fa fa-download"></i></button>
                                </div>

                                <table class="data-download full-width">
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>

                                </table>

                            </li>
                            <li>
                                <div class="d-flex justify-content-between mb-3">
                                    <h4>Proof of residential address not older than 3 months (a recent utility bill containing details of address)</h4>
                                    <button type="button" class="transparent-btn">Download All <i class="fa fa-download"></i></button>
                                </div>

                                <table class="data-download full-width">
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                    <tr>
                                        <td width="33%"><span class="db_uplad-file">Passport_Copy.png <i class="fa fa-download"></i></span></td>
                                        <td>Uploaded on 12 Mar, 2017</td>
                                    </tr>
                                </table>

                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

{{ $content }}

@endsection
@section('pageTitle')
User Detail
@endsection

@section('jscript')
<script>
var messages = {
    get_users_wci: "{{ URL::route('get_users_wci') }}",
    get_users_wci_single: "{{ URL::route('get_users_wci_single') }}",
    delete_users: "{{ URL::route('delete_users') }}",
    data_not_found: "{{ trans('error_messages.data_not_found') }}",
    token: "{{ csrf_token() }}",
    token2: "{{ csrf_token() }}",
    APISecret: "{{config('common.APISecret')}}",
    gatwayurl: "{{config('common.gatwayurl')}}",
    contentType: "{{config('common.contentType')}}",
    gatwayhost: "{{config('common.gatwayhost')}}",
    apiKey: "{{config('common.apiKey')}}",
    groupId: "{{config('common.groupId')}}",
    content: "{{ $content }}",

};
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
<script src="{{ asset('backend/js/wciapicall.js') }}" type="text/javascript"></script>
@endsection

