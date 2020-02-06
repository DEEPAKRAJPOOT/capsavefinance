@extends('layouts.backend.admin-layout')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="fa fa-clipboard" aria-hidden="true"></i>
        </div>
        <div class="header-title">
            <h3 class="mt-3">Manage Invoice</h3>
            <ol class="breadcrumb">
                <li><a href="/admin/dashboard"><i class="fa fa-home"></i> Home</a></li>
                <li class="active">Manage Invoice</li>
            </ol>
        </div>
        <div class="clearfix"></div>
    </section>
    <div class="row grid-margin mt-3">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item ">
                            <a class="nav-link active" data-toggle="tab" href="#menu1">Pending</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#home">Approved</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#home1">Disbursed</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-toggle="tab" href="#home2">Repaid</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="menu1" class=" active tab-pane ">
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-3">
                                                <div class="dataTables_length" id="supplier-listing_length">
                                                    <label>
                                                        Show 
                                                        <select name="supplier-listing_length" aria-controls="supplier-listing" class="form-control form-control-sm">
                                                            <option value="10">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                        entries
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-9">
                                                <div id="supplier-listing_filter" class="dataTables_filter">
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Anchor  </option>
                                                            <option value="10">Anchor 1</option>
                                                            <option value="25">Anchor 2</option>
                                                            <option value="50">Anchor 3</option>
                                                            <option value="100">Anchor 4</option>
                                                        </select>
                                                    </label>
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Supplier  </option>
                                                            <option value="10">Supplier 1</option>
                                                            <option value="25">Supplier 2</option>
                                                            <option value="50">Supplier 3</option>
                                                            <option value="100">Supplier 4</option>
                                                        </select>
                                                    </label>
                                                    <button type="button" class="btn btn-success btn-sm ml-2" data-toggle="modal" data-target="#myModal6">Upload Invoices</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="invoive-listing" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                                    <thead>
                                                        <tr role="row">
                                                            <th><input type="checkbox"></th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending" width="8%">Invoice Id.</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice No.: activate to sort column ascending" width="10%">Anchor Name</th>
                                                            <th class="white-space sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Issue Date: activate to sort column ascending" width="10%">Customer/Supplier</th>
                                                            <th class="white-space sorting" width="10%" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Due Date: activate to sort column ascending">Invoice Date</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Borrower: activate to sort column ascending">Invoice Due Date</th>
                                                            <th class="white-space numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice Amount (₹): activate to sort column ascending" width="12%">Invoice Amount (₹)</th>
                                                            <th class="numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Buyer: activate to sort column ascending">Invoice Approved Amount</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr role="row" class="odd">
                                                            <td><input type="checkbox"></td>
                                                            <td class="sorting_1"><a href="view-pending-invoice.php">1</a></td>
                                                            <td>Achor 1</td>
                                                            <td class=" white-space">Customer 1</td>
                                                            <td class=" white-space">17-Oct-2019</td>
                                                            <td>17-Dec-2019</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td>
                                                                <div class="d-flex inline-action-btn">
                                                                    <div class="dropdown">
                                                                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                                                        Pending
                                                                        </button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item" href="view-pending-invoice.php">Approved</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr role="row" class="odd">
                                                            <td><input type="checkbox"></td>
                                                            <td class="sorting_1"><a href="view-pending-invoice.php">2</a></td>
                                                            <td>Achor 1</td>
                                                            <td class=" white-space">Customer 2</td>
                                                            <td class=" white-space">19-Oct-2019</td>
                                                            <td>19-Dec-2019</td>
                                                            <td class=" numericCol">90,000</td>
                                                            <td class=" numericCol">90,000</td>
                                                            <td>
                                                                <div class="d-flex inline-action-btn">
                                                                    <div class="dropdown">
                                                                        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
                                                                        Pending
                                                                        </button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item" href="view-pending-invoice.php">Approved</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                        <div id="home" class=" tab-pane fade">
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-3">
                                                <div class="dataTables_length" id="supplier-listing_length">
                                                    <label>
                                                        Show 
                                                        <select name="supplier-listing_length" aria-controls="supplier-listing" class="form-control form-control-sm">
                                                            <option value="10">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                        entries
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-9">
                                                <div id="supplier-listing_filter" class="dataTables_filter">
                                                    <!-- <label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="supplier-listing"></label>-->
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Anchor  </option>
                                                            <option value="10">Anchor 1</option>
                                                            <option value="25">Anchor 2</option>
                                                            <option value="50">Anchor 3</option>
                                                            <option value="100">Anchor 4</option>
                                                        </select>
                                                    </label>
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Supplier  </option>
                                                            <option value="10">Supplier 1</option>
                                                            <option value="25">Supplier 2</option>
                                                            <option value="50">Supplier 3</option>
                                                            <option value="100">Supplier 4</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="invoive-listing" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                                    <thead>
                                                        <tr role="row">
                                                            <th><input type="checkbox"></th>
                                                            <th class="sorting_asc" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending" width="8%">Invoice Id.</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice No.: activate to sort column ascending" width="10%">Anchor Name</th>
                                                            <th class="white-space sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Issue Date: activate to sort column ascending" width="10%">Customer/Supplier</th>
                                                            <th class="white-space sorting" width="10%" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Due Date: activate to sort column ascending">Invoice Date</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Borrower: activate to sort column ascending">Invoice Due Date</th>
                                                            <th class="white-space numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice Amount (₹): activate to sort column ascending" width="12%">Invoice Amount (₹)</th>
                                                            <th class="numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Buyer: activate to sort column ascending">Invoice Approved Amount</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr role="row" class="odd">
                                                            <td><input type="checkbox"></td>
                                                            <td class="sorting_1"><a href="view-invoice.php">1</a></td>
                                                            <td>Achor 1</td>
                                                            <td class=" white-space">Customer 1</td>
                                                            <td class=" white-space">17-Oct-2019</td>
                                                            <td>17-Dec-2019</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td>
                                                                <div class="d-flex inline-action-btn">
                                                                    <div class="dropdown">
                                                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                                                        Approved
                                                                        </button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item" data-toggle="modal" data-target="#myModal3" href="#">To Be Disburse</a>
                                                                            <a class="dropdown-item" data-toggle="modal" data-target="#myModal4" href="#">Reject</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                        <div id="home1" class=" tab-pane fade">
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-3">
                                                <div class="dataTables_length" id="supplier-listing_length">
                                                    <label>
                                                        Show 
                                                        <select name="supplier-listing_length" aria-controls="supplier-listing" class="form-control form-control-sm">
                                                            <option value="10">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                        entries
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-9">
                                                <div id="supplier-listing_filter" class="dataTables_filter">
                                                    <!-- <label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="supplier-listing"></label>-->
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Anchor  </option>
                                                            <option value="10">Anchor 1</option>
                                                            <option value="25">Anchor 2</option>
                                                            <option value="50">Anchor 3</option>
                                                            <option value="100">Anchor 4</option>
                                                        </select>
                                                    </label>
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Supplier  </option>
                                                            <option value="10">Supplier 1</option>
                                                            <option value="25">Supplier 2</option>
                                                            <option value="50">Supplier 3</option>
                                                            <option value="100">Supplier 4</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="invoive-listing" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                                    <thead>
                                                        <tr role="row">
                                                            <th class="sorting_asc" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending" width="8%">Invoice Id.</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice No.: activate to sort column ascending" width="10%">Anchor Name</th>
                                                            <th class="white-space sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Issue Date: activate to sort column ascending" width="10%">Customer/Supplier</th>
                                                            <th class="white-space sorting" width="10%" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Due Date: activate to sort column ascending">Invoice Date</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Borrower: activate to sort column ascending">Invoice Due Date</th>
                                                            <th class="white-space numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice Amount (₹): activate to sort column ascending" width="12%">Invoice Amount (₹)</th>
                                                            <th class="numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Buyer: activate to sort column ascending">Invoice Approved Amount</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr role="row" class="odd">
                                                            <td class="sorting_1"><a href="view-invoice.php">1</a></td>
                                                            <td>Achor 1</td>
                                                            <td class=" white-space">Customer 1</td>
                                                            <td class=" white-space">17-Oct-2019</td>
                                                            <td>17-Dec-2019</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td>
                                                                <div class="d-flex inline-action-btn">
                                                                    <div class="dropdown">
                                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                                        Disbursed
                                                                        </button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item" data-toggle="modal" data-target="#myModal5" href="#">Repay</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
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
                        <div id="home2" class=" tab-pane fade">
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div id="supplier-listing_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-3">
                                                <div class="dataTables_length" id="supplier-listing_length">
                                                    <label>
                                                        Show 
                                                        <select name="supplier-listing_length" aria-controls="supplier-listing" class="form-control form-control-sm">
                                                            <option value="10">10</option>
                                                            <option value="25">25</option>
                                                            <option value="50">50</option>
                                                            <option value="100">100</option>
                                                        </select>
                                                        entries
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-9">
                                                <div id="supplier-listing_filter" class="dataTables_filter">
                                                    <!-- <label>Search:<input type="search" class="form-control form-control-sm" placeholder="" aria-controls="supplier-listing"></label>-->
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Anchor  </option>
                                                            <option value="10">Anchor 1</option>
                                                            <option value="25">Anchor 2</option>
                                                            <option value="50">Anchor 3</option>
                                                            <option value="100">Anchor 4</option>
                                                        </select>
                                                    </label>
                                                    <label class="ml-2">
                                                        <select class="form-control form-control-sm">
                                                            <option>Select Supplier  </option>
                                                            <option value="10">Supplier 1</option>
                                                            <option value="25">Supplier 2</option>
                                                            <option value="50">Supplier 3</option>
                                                            <option value="100">Supplier 4</option>
                                                        </select>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <table id="invoive-listing" class="table table-striped dataTable no-footer overview-table" cellspacing="0" width="100%" role="grid" aria-describedby="invoive-listing_info" style="width: 100%;">
                                                    <thead>
                                                        <tr role="row">
                                                            <th class="sorting_asc" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Sr.No.: activate to sort column descending" width="8%">Invoice Id.</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice No.: activate to sort column ascending" width="10%">Anchor Name</th>
                                                            <th class="white-space sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Issue Date: activate to sort column ascending" width="10%">Customer/Supplier</th>
                                                            <th class="white-space sorting" width="10%" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Due Date: activate to sort column ascending">Invoice Date</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Borrower: activate to sort column ascending">Invoice Due Date</th>
                                                            <th class="white-space numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Invoice Amount (₹): activate to sort column ascending" width="12%">Invoice Amount (₹)</th>
                                                            <th class="numericCol sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Buyer: activate to sort column ascending">Invoice Approved Amount</th>
                                                            <th class="sorting" tabindex="0" aria-controls="invoive-listing" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr role="row" class="odd">
                                                            <td class="sorting_1"><a href="view-invoice.php">1</a></td>
                                                            <td>Achor 1</td>
                                                            <td class=" white-space">Customer 1</td>
                                                            <td class=" white-space">17-Oct-2019</td>
                                                            <td>17-Dec-2019</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td class=" numericCol">60,000</td>
                                                            <td>
                                                                <div class="d-flex inline-action-btn">
                                                                    <div class="dropdown">
                                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                                                        Repaid
                                                                        </button>
                                                                    </div>
                                                                </div>
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
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info" id="supplier-listing_info" role="status" aria-live="polite">Showing 1 to 10 of 49 entries</div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="supplier-listing_paginate">
                                <ul class="pagination justify-content-end">
                                    <li class="paginate_button page-item previous disabled" id="supplier-listing_previous"><a href="#" aria-controls="supplier-listing" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li>
                                    <li class="paginate_button page-item active"><a href="#" aria-controls="supplier-listing" data-dt-idx="1" tabindex="0" class="page-link">1</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="supplier-listing" data-dt-idx="2" tabindex="0" class="page-link">2</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="supplier-listing" data-dt-idx="3" tabindex="0" class="page-link">3</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="supplier-listing" data-dt-idx="4" tabindex="0" class="page-link">4</a></li>
                                    <li class="paginate_button page-item "><a href="#" aria-controls="supplier-listing" data-dt-idx="5" tabindex="0" class="page-link">5</a></li>
                                    <li class="paginate_button page-item next" id="supplier-listing_next"><a href="#" aria-controls="supplier-listing" data-dt-idx="6" tabindex="0" class="page-link">Next</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal3">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5>Disburse Invoice</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Invoice Amount
                                </label>
                                <input type="text" class="form-control " value="60,000" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Interest Rate (%) 
                                </label>
                                <input type="text" class="form-control" value="14%" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Processing Fee 
                                </label>
                                <input type="text" class="form-control" value="0" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Margin Rate(%) 
                                </label>
                                <input type="text" class="form-control" value="10%" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Funded Amount : 
                                </label>
                                <input type="text" class="form-control" value="₹56,000" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Final Funded Amount  : 
                                </label>
                                <input type="text" class="form-control" value="₹48146" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Limit Offered  : 
                                </label>
                                <input type="text" class="form-control" value="₹1,000,0000" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Limit available for disburse : 
                                </label>
                                <input type="text" class="form-control" value="₹99,40,000" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Tenor (Days): 
                                </label>
                                <input type="text" class="form-control" value="90" disabled="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Fund Date: 
                                </label>
                                <input type="date" class="form-control" value="2019-12-13">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Processing Fee: (₹150,000.00) 
                                </label>
                                <input type="date" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Payment Receipt: 
                                </label>
                                <input type="file" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Disburse</button> 
                    <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal4">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5>Invoice Confirmation</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Payment Receipt:  <span class="error_message_label doc-error">*</span>
                                </label>
                                <input type="file" class="form-control" value="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="txtCreditPeriod">Comment  <span class="error_message_label doc-error">*</span>
                                </label>
                                <textarea class="form-control" cols="4" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Save</button> 
                    <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal5">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5>Repayment Details | Invoice Number : INV-112</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body text-left">
                    <div class="listing-modal repayment-form-modal mb-3">
                        <ul>
                            <li>
                                <div class="listing-modal-left"> Repay count: </div>
                                <div class="listing-modal-right"> <span id="repay_count">1</span></div>
                                <span hidden="" id="overdue_percentage_raw">16</span>
                                <span hidden="" id="remaining_overdue_raw">110000.1328125</span>
                            </li>
                            <li>
                                <!-- <span style="display:none" id="repay_count"></span> -->
                                <div class="listing-modal-left"> Invoice Approved Amount (₹): </div>
                                <div class="listing-modal-right"> <span id="invoice_approved_amount">₹60,000.00</span></div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Funded Amount (₹): </div>
                                <div class="listing-modal-right"> <span id="funded_amount">₹56,000.00</span></div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Final Funded Amount (₹): </div>
                                <div class="listing-modal-right"> <span id="final_funded_amount_repay">₹48146.00</span></div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Funded Date:  </div>
                                <div class="listing-modal-right"> <span id="funded_date_show">13-Dec-2019</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Tenor (in days): </div>
                                <div class="listing-modal-right"> <span id="term_days">90</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left"> Payment Due Date: </div>
                                <div class="listing-modal-right"> <span id="payment_due_date" payment_due_date_raw="2019-10-17">14-March-2020</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Interest Per Annum (%): </div>
                                <div class="listing-modal-right"> <span id="interest_percentage">12</span><span> %</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Processing Fee (%): </div>
                                <div class="listing-modal-right"> <span id="processing_fee_repay">1</span><span> %</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Discount Type: </div>
                                <div class="listing-modal-right"> <span id="discount_type">front end</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Grace period (in days): </div>
                                <div class="listing-modal-right"> <span id="penal_grace">0</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Penal Interest Per Annum (%): </div>
                                <div class="listing-modal-right"> <span id="penal_interest">0</span><span> %</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Repayment Amount: </div>
                                <div class="listing-modal-right"> <span id="repayment_amount">₹0</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Total Amount Repaid: </div>
                                <div class="listing-modal-right"> <span id="already_repaid_amount">₹0</span></div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Penal days: </div>
                                <div class="listing-modal-right"> <span id="penal_days">41</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Penalty Amount: </div>
                                <div class="listing-modal-right"> <span id="penalty_amount">₹0</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Principal Amount: </div>
                                <div class="listing-modal-right"> <span id="remaining_overdue">₹60,000</span>
                                </div>
                            </li>
                            <li>
                                <div class="listing-modal-left">Total Amount to Repay: </div>
                                <div class="listing-modal-right"> <span id="remaining_repay_amount">₹0</span></div>
                            </li>
                        </ul>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Repayment Date :</label>
                                <input type="date" class="form-control " value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Repayment Amount :</label>
                                <input type="date" class="form-control " value="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Payment Type :</label>
                                <select class="form-control">
                                    <option value=""> Select Payment Type </option>
                                    <option value="1"> Online RTGS/NEFT </option>
                                    <option value="2"> Cheque</option>
                                    <option value="3"> Other </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="repaid_amount" class="form-control-label">Upload Documents :</label>
                                <input type="file" class="form-control " value="">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="repaid_amount" class="form-control-label">Comment : </label>
                            <textarea class="form-control" cols="4" rows="4"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Save</button> 
                    <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="myModal6">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5>Upload Invoices</h5>
                    <button type="button" class="close close-btns" data-dismiss="modal">×</button>
                </div>
                <!-- Modal body -->
                <div class="modal-body text-left">
                    <div class="form-group">
                        <label for="limit-offer" class="form-control-label">Upload CSV for invoices:</label>
                        <input name="bulkfile" class="form-control form-control-sm" type="file">
                        <a class="float-right" href="#"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Download Template </a>
                    </div>
                    <div class="clearfix">
                    </div>
                    <button type="submit" class="btn btn-success float-right btn-sm mt-3 ml-2">Upload</button> 
                    <button type="submit" class="btn btn-secondary btn-sm mt-3 float-right" data-dismiss="modal">Close</button> 		
                </div>
            </div>
        </div>
    </div>
</div>
@endsection