@extends('layouts.app')

@section('content')

<!-- partial dasboard content -->
<div class="content-wrapper">
    <h3 class="page-title">Dashboard</h3>
    <div class="row  grid-margin">
        <div class="col-12 col-lg-3">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <!-- <i class="fa fa-book highlight-icon"></i> -->
                                    </div>
                                    <div class="suppliers-box highlight-text text-center">
                                        <p class="card-text" style="font-size: 25px;font-weight: 700;">Invoice Value</p>
                                        <p class="statistics-number">&#8377; 674754.60</p>
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
        <div class="col-12 col-lg-3">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <!-- <i class="fa fa-credit-card highlight-icon"> </i> -->
                                    </div>
                                    <div class="suppliers-box highlight-text text-center">
                                        <p class="card-text" style="font-size: 25px;font-weight: 700;">Invoice Approved</p>
                                        <p class="statistics-number">&#8377; 674754.60</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <!-- <i class="fa fa-credit-card highlight-icon"> </i> -->
                                    </div>
                                    <div class="suppliers-box highlight-text text-center">
                                        <p class="card-text" style="font-size: 25px;font-weight: 700;">Invoice Repaid</p>
                                        <p class="statistics-number">&#8377; 674754.60</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <!-- <i class="fa fa-credit-card highlight-icon"> </i> -->
                                    </div>
                                    <div class="suppliers-box highlight-text text-center">
                                        <p class="card-text" style="font-size: 25px;font-weight: 700;">Invoice Due</p>
                                        <p class="statistics-number">&#8377; 674754.60</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- footer contains the footer section -->
    </div>
    <div class="row  grid-margin">
        <div class="col-12 col-lg-6">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <!-- <i class="fa fa-book highlight-icon"></i> -->
                                    </div>
                                    <canvas class="suppliers-box highlight-text text-center" id="payment_due"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="row">
                <div class="col-12">
                    <div class="card card-statistics">
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="total-supply">
                                    <div class="text-primary">
                                        <!-- <i class="fa fa-credit-card highlight-icon"> </i> -->
                                    </div>
                                    <canvas class="suppliers-box highlight-text text-center" id="repayment_trend"></canvas>
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
<script src="{{ asset('backend/js/Reports/chart.min.js') }}" type="text/javascript"></script>
<script>
$(document).ready(function(){
    var pieData = {
        datasets: [{
            data: [12, 19, 3, 17, 28, 24, 7],
            backgroundColor: ["#2ecc71","#3498db","#95a5a6","#9b59b6","#f1c40f","#e74c3c","#34495e"],
            label: 'Dataset 1'
        }],
        labels: ['Red','Orange','Yellow','Green','Blue']
    };

    var piectx = document.getElementById("payment_due").getContext('2d');
    var pieChart = new Chart(piectx, {
        type: 'pie',
        data: pieData,
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Pie Chart'
            }
        }
    });
});




$(document).ready(function(){
    var mixData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            type: 'line',
            label: 'Dataset 1',
            borderColor: '#3498db',
            borderWidth: 2,
            fill: false,
            data: [12, 19, 13, 17, 28, 24, 7]
        }, {
            type: 'bar',
            label: 'Dataset 2',
            backgroundColor: '#e74c3c',
            data: [12, 19, 3, 17, 28, 24, 7],
            borderColor: 'white',
            borderWidth: 2
        }]
    };
    var mixctx = document.getElementById("repayment_trend").getContext('2d');
    var mixedChart = new Chart(mixctx, {
        type: 'bar',
        data: mixData,
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Combo Bar Line Chart'
            },
            tooltips: {
                mode: 'index',
                intersect: true
            }
        }
    });
})
</script>
@endsection