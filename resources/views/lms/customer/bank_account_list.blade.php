@extends('layouts.backend.admin-layout')

@section('content')

<ul class="main-menu">
	<li>
		<a href="" >Summary</a>
	</li>
        <li>
            <a class="active" href="{{ route('lms_get_bank_account', [ 'user_id' => $userInfo->user_id ]) }}">Bank Account</a>
	</li>
	<li>
		<a href="{{ route('lms_get_application_invoice', [ 'user_id' => $userInfo->user_id ]) }}">View Invoices</a>
	</li>
	<li>
		<a href="">Repayment History</a>
	</li>
	<li>
		<a href="">Charges</a>
	</li>
	<li>
		<a href="">SOA</a>
	</li>
	<li>
		<a href="">Bank Account</a>
	</li>
</ul>
<div class="content-wrapper">





    <div class="row grid-margin mt-3">
        <div class="  col-md-12  ">
            <section class="content-header">
                <div class="header-icon">
                    <i class="fa fa-clipboard" aria-hidden="true"></i>
                </div>
                <div class="header-title">
                    <h3 class="mt-2">Bank Account</h3>

                    <ol class="breadcrumb">
                        <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                        <li class="active">Bank Account</li>
                    </ol>
                </div>
                <div class="clearfix"></div>
            </section>
            <div class="row">
                <div class="col-sm-12">
                    <div class="head-sec">

                        <a data-toggle="modal" data-target="#myModal1" id="register">
                            <button class="btn  btn-success btn-sm float-right mb-3" type="button">

                                + Add Bank
                            </button>
                        </a>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">





                    <div class="row">
                        <div class="col-sm-12">

                            <table id="invoive-listing" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                <thead>
                                    <tr role="row">

                                        <th class="sorting_asc" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending">Acc. Holder Name </th>


                                        <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice No.: activate to sort column ascending">Acc. Number</th>


                                        <th class="white-space sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Issue Date: activate to sort column ascending">Bank Name</th>

                                        <th class="white-space sorting" width="15%" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Due Date: activate to sort column ascending">IFSC Code</th>

                                        <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Borrower: activate to sort column ascending">Branch Name </th>


                                        <th class="numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Buyer: activate to sort column ascending">Status</th>

                                        <th class="numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Buyer: activate to sort column ascending">Action</th>









                                    </tr>
                                </thead>
                                <tbody>
                                    <tr role="row" class="odd">

                                        <td class="sorting_1">Chandan </td>
                                        <td>042150458956</td>
                                        <td>SBI</td>
                                        <td class=" white-space">SBI0125689</td>
                                        <td class=" white-space">Noida</td>


                                        <td class=" white-space">
                                            <span class="badge badge-success">Active</span>
                                        </td>

                                        <td>
                                            <input type="checkbox" id="add" name="add"><label for="add">Default</label>

                                        </td>
                                    </tr>













                                </tbody>
                            </table>


                        </div>
                    </div>









                </div>
            </div>


        </div>





    </div>




















    <div class="modal align-middle" id="myModal1">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5>Add Bank</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Account Holder Name
                                    <span class="mandatory">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Account Holder Name">

                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Account Number
                                    <span class="mandatory">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Account Number">

                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Bank Name
                                    <span class="mandatory">*</span>
                                </label>
                                <select class="form-control form-control-sm">
                                    <option>Select</option>
                                    <option>SBI</option>
                                    <option>HDFC</option>
                                </select>

                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">IFSC Code
                                    <span class="mandatory">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter IFSC Code">

                            </div>

                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Branch Name
                                    <span class="mandatory">*</span>
                                </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Enter Branch Name">

                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Status</label><br>
                                <select class="form-control form-control-sm">
                                    <option>Select</option>
                                    <option>Active</option>
                                    <option>Inactive</option>
                                </select>


                            </div>

                        </div>



                    </div>

                    <button type="submit" class="btn btn-success float-right btn-sm mt-3">Submit</button>  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection