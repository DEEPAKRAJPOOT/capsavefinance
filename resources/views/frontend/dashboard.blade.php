@extends('layouts.app')

@section('content')

<!-- partial dasboard content -->
<div class="content-wrapper">
    <h3 class="page-title">Dashboard</h3>
    <div class="row  grid-margin">
        <div class="col-12 col-lg-6">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <i class="fa fa-book highlight-icon"></i>
                                    </div>
                                    <div class="suppliers-box highlight-text">
                                        <p class="card-text">Total # of Invoice</p>
                                        <p class="statistics-number"></p>
                                    </div>
                                    <!--
                                    <div class="approved-box">
                                        <div class="invoice-right">
                                            <p class="card-text">Pending Invoice</p>
                                            <p class="statistics-number"></p>
                                        </div>
                                        <div class="invoice-left">
                                            <p class="card-text">Funded Invoice</p>
                                            <p class="statistics-number"></p>
                                        </div>
                                        <div class="invoice-right">
                                            <p class="card-text">Repaid Invoice</p>
                                            <p class="statistics-number"></p>
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--funded information---->
        <div class="col-12 col-lg-6">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <i class="fa fa-credit-card	 highlight-icon   highlight-icon"> </i>
                                    </div>
                                    <div class="suppliers-box highlight-text">
                                        <p class="card-text">Total Amount Funded (₹)</p>
                                        <p class="statistics-number"></p>
                                    </div>
                                    <!--
                                    <div class="approved-box">
                                        <div class="fundAmtinvoice-right">
                                            <p class="card-text">Total Repaid Amount (₹)</p>
                                            <p class="statistics-number"></p>
                                        </div>
                                        <div class="fundAmtinvoice-left">
                                            <p class="card-text">Total Remaining Amount (₹)</p>
                                            <p class="statistics-number"></p>
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer contains the footer section -->
    </div>
</div>
</div>
@endsection