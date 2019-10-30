@extends('layouts.guest')
@section('content')

<div class="step-form pt-5">

    <div class="container">
        <ul id="progressbar">
            <li class="active">
                <div class="count-heading">Business Information </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="active">
                <div class="count-heading"> Authorized Signatory KYC </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/kyc.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="active">
                <div class="count-heading">Business Documents </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/business-document.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="active">
                <div class="count-heading"> Associate Buyers </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/buyers.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
            <li class="count-active">
                <div class="count-heading"> Associate Logistics </div>
                <div class="top-circle-bg">
                    <div class="count-top">
                        <img src="{{ url('backend/signup-assets/images/logistics.png') }}" width="36" height="36">
                    </div>
                    <div class="count-bottom">
                        <img src="{{ url('backend/signup-assets/images/tick-image.png') }}" width="36" height="36">
                    </div>
                </div>
            </li>
        </ul>

    </div>



    <div class="container">

        <div class="mt-4">
            <div class="form-heading pb-3">
                <h2>Associate Logistics
                    <small> ( Please Add the Logistics's Information )

                    </small>
                </h2>
            </div>
            <div class="col-md-12 form-design ">

                <div id="reg-box">
                    <form>
                        <div class=" form-fields pt-4">
                            <table class="table table-bordered" cellspacing="0" cellpadding="0">
                                <thead>
                                <th>Company Name *</th>
                                <th width="180">GST *</th>
                                <th>Contact Person *</th>
                                <th>Email *</th>
                                <th>Mobile *</th>
                                <th width="150" class="text-center">Logistics Agreement</th>
                                <th width="100" class="text-center">Action</th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="form-control">
                                                <option>Select an Option </option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="relative"><a href="javascript:void(0);" class="verify-owner-no verify-gst">Verify</a><input type="text" class="form-control" placeholder="Enter GST"></div>
                                        </td>
                                        <td><input type="text" class="form-control" placeholder="Enter Contact Person"></td>
                                        <td><input type="text" class="form-control" placeholder="Enter Email"></td>
                                        <td><input type="text" class="form-control" placeholder="Enter Mobile No"></td>
                                        <td class="text-center"><i class="fa fa-upload" aria-hidden="true"></i></td>
                                        <td class="text-center">
                                            <button class="btn btn-primary btn-sm mr-1"> <i class="fa fa-plus-circle" aria-hidden="true"></i> </button>

                                            <button class="btn btn-info btn-sm"> <i class="fa fa-copy" aria-hidden="true"></i> </button>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="d-flex btn-section ">

                                <div class="col-md-4 ml-auto text-right">
                                    <input type="button" value="Back" class="btn btn-warning" onclick="window.location.href = 'associate-buyers.php'">

                                    <input type="button" value="Skip" class="btn btn-primary" onclick="window.location.href = '#'"> <input type="button" value="Submit" class="btn btn-primary" onclick="window.location.href = '#'"> </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection