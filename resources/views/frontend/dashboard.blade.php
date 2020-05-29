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
                                        <select name="repay_year" id="repay_year">
                                            <option value="<?= date('Y')-2?>"><?= date('Y')-2?></option>
                                            <option value="<?= date('Y')-1?>"><?= date('Y')-1?></option>
                                            <option value="<?= date('Y')?>" selected><?= date('Y')?></option>
                                        </select>
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
var pieChart, mixedChart;    
var pieData = {
    datasets: [{
        data: [12, 19, 3, 17, 28, 24],
        backgroundColor: ["#2ecc71","#3498db","#95a5a6","#9b59b6","#f1c40f","#e74c3c"],
        label: 'Dataset 1'
    }],
    labels: ['0-15','16-30','31-45','46-60','61-75', '75-90']
};

function createPieChart(data, ele){
    var piectx = document.getElementById(ele).getContext('2d');
    if(typeof pieChart == 'object'){
        pieChart.destroy();
    }
    pieChart = new Chart(piectx, {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Payment Due'
            }
        }
    });
}

var mixData = {
    labels: ['Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb'],
    datasets: [{
        type: 'line',
        label: 'Sum of Numbers',
        borderColor: '#2ecc71',
        borderWidth: 2,
        fill: false,
        data: [12, 19, 13, 17, 28, 24, 7, 19, 13, 17, 28, 24]
    }, {
        type: 'bar',
        label: 'Sum of Invoice Value',
        backgroundColor: '#3498db',
        data: [12, 19, 3, 17, 28, 24, 7, 3, 17, 28, 24, 7],
        borderColor: 'white',
        borderWidth: 2
    }]
};

function createMixedChart(data, ele){
    var mixctx = document.getElementById(ele).getContext('2d');
    if(typeof mixedChart == 'object'){
        mixedChart.destroy();
    }
    mixedChart = new Chart(mixctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            title: {
                display: true,
                text: 'Repayment Trend'
            },
            tooltips: {
                mode: 'index',
                intersect: true
            }
        }
    });
}

$(document).ready(function(){
    createPieChart(pieData, 'payment_due');
    createMixedChart(mixData, 'repayment_trend');
});


$(document).ready(function(){
    $('#repay_year').on('change', function(){
        let year = $(this).val();
        let mixData2 = {
            labels: ['Mar', 'Apr', 'May', 'Jan', 'Feb'],
            datasets: [{
                type: 'line',
                label: 'Sum of Numbers',
                borderColor: '#2ecc71',
                borderWidth: 2,
                fill: false,
                data: [12, 19, 13, 17, 28]
            }, {
                type: 'bar',
                label: 'Sum of Invoice Value',
                backgroundColor: '#3498db',
                data: [12, 19, 3, 17, 28, 24, 7],
                borderColor: 'white',
                borderWidth: 2
            }]
        };
        createMixedChart(mixData2, 'repayment_trend');
    })
});
</script>
@endsection