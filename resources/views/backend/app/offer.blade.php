@extends('layouts.backend.admin-layout')
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="accordion" class="accordion">
                                <!-- Start View Supply Chain Offer Block -->
                                <div class="card card-color mb-0">
                                    <div class="card-header" data-toggle="collapse" href="#collapseOne" aria-expanded="false"><h5 class="mb-0">Supply Chain Offer Details</h5>     
                                    </div>
                                    <div id="collapseOne" class="card-body bdr p-0 show" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i table-offer">
                                            <thead>
                                                <tr role="row">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="40%">Offer Details</th>
                                                   <th width="25%">Created By</th>
                                                   <th width="15%">Status</th>
                                                   <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row" class="odd">
                                                    <td width="10%">1.</td>
                                                    <td width="40%">
                                                        <table class="" width="70%">
                                                            <tbody>
                                                                <tr>
                                                                    <td><b>Apply Loan Amount : </b></td>
                                                                    <td>2000000</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Loan Offer : </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Interest Rate(%) : </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor (Days) : </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Margin (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Grace Period (Days): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Processing Fee: </b></td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Check Bounce Fee: </b></td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Comment: </b></td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td width="25%">Admin</td>
                                                    <td width="15%"><label class="badge badge-success current-status">Approved</label></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- End View Supply Chain Offer Block -->
                                <!-- Start View Term loan Offer Block -->
                                <div class="card card-color mb-0">
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false"><h5 class="mb-0">Term Loan Offer Details</h5>     
                                    </div>
                                    <div id="collapseTwo" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i table-offer">
                                            <thead>
                                                <tr role="row">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="40%">Offer Details</th>
                                                   <th width="25%">Created By</th>
                                                   <th width="15%">Status</th>
                                                   <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row" class="odd">
                                                    <td width="10%">1.</td>
                                                    <td width="40%">
                                                        <table class="" width="70%">
                                                            <tbody>
                                                                <tr>
                                                                    <td><b>Apply Loan Amount : </b></td>
                                                                    <td>2000000</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Loan Offer : </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Interest Rate(%) : </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor (Days) : </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Margin (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Grace Period (Days): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Processing Fee: </b></td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Check Bounce Fee: </b></td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Comment: </b></td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td width="25%">Admin</td>
                                                    <td width="15%"><label class="badge badge-success current-status">Approved</label></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- End View Term loan Offer Block -->
                                <!-- Start View Leasing Offer Block -->
                                <div class="card card-color mb-0">
                                    <div class="card-header collapsed" data-toggle="collapse" href="#collapseThree" aria-expanded="false"><h5 class="mb-0">Leasing Offer Details</h5>     
                                    </div>
                                    <div id="collapseThree" class="card-body bdr p-0 collapse" data-parent="#accordion" style="">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table-i table-offer">
                                            <thead>
                                                <tr role="row">
                                                   <th width="10%">Sr. No.</th>
                                                   <th width="40%">Offer Details</th>
                                                   <th width="25%">Created By</th>
                                                   <th width="15%">Status</th>
                                                   <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr role="row" class="odd">
                                                    <td width="10%">1.</td>
                                                    <td width="40%">
                                                        <table class="" width="70%">
                                                            <tbody>
                                                                <tr>
                                                                    <td><b>Apply Loan Amount : </b></td>
                                                                    <td>2000000</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Loan Offer : </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Interest Rate(%) : </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor (Days) : </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                   <td><b>Tenor for old invoice (Days): </b></td>
                                                                   <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Margin (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Overdue Interest Rate (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Adhoc Interest Rate (%): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Grace Period (Days): </b></td>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Processing Fee: </b></td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Check Bounce Fee: </b></td>
                                                                    <td>0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><b>Comment: </b></td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td width="25%">Admin</td>
                                                    <td width="15%"><label class="badge badge-success current-status">Approved</label></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- End View Leasing Offer Block -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

