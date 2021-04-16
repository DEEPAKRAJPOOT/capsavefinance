@extends('layouts.app')

@section('content')

<!-- partial dasboard content -->
<div class="content-wrapper">
    <h3 class="page-title">Dashboard</h3>
    <div class="row  grid-margin">
                     
                     <div class="col-12">
                       <div class="row grid-margin">
                         <div class="col-12">
                           <div class="card card-statistics">
                             <div class="card-body">
                               <div class="d-flex mb-3">
                                   <div class="text-primary">
                                     <i class="fa fa-book highlight-icon" aria-hidden="true"></i>
                                   </div>
                                   <div class="ml-2 highlight-text">
                                       <p class="card-text">Total # of Invoice</p>
                                       <p class="statistics-number">{{isset($supplierData->Total_Invoices) ? $supplierData->Total_Invoices : '0'}}</p>
                                   </div>
                               </div>
                                 <div class="row">
                                    <div class="col-md-3">
                                        <p class="text-muted btn btn-outline-warning text-left w-100">Pending Invoice<span class="pull-right">{{isset($supplierData->Total_Invoice_Pending) ? $supplierData->Total_Invoice_Pending : '0'}}</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="text-muted btn btn-outline-warning text-left w-100">Approved Invoice<span class="pull-right">{{isset($supplierData->Total_Approved_Invoice) ? $supplierData->Total_Approved_Invoice : '0'}}</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="btn btn-outline-success text-left w-100">Funded Invoice<span class="pull-right">{{isset($supplierData->Total_Disbursed_Invoice) ? $supplierData->Total_Disbursed_Invoice : '0'}}</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="text-muted btn btn-outline-warning text-left w-100">Repaid Invoice<span class="pull-right">{{isset($supplierData->Total_Invoice_Repaid) ? $supplierData->Total_Invoice_Repaid : '0'}}</span></p>
                                    </div>
                                 </div>
                               
                             </div>
                           </div>
                         </div>
                       </div>
                         
                       <div class="row grid-margin">
                         
                        <div class="col-6">
                           <div class="card card-statistics">
                             <div class="card-body">
                               <div class="d-flex mb-3">
                                   <div class="text-primary">
                                     <i class="fa fa-money highlight-icon" aria-hidden="true"></i>
                                   </div>
                                   <div class="ml-2 highlight-text">
                                       <p class="card-text">Total Amount Funded(₹)</p>
                                       <p class="statistics-number">{{isset($supplierData->Total_Disbursed_Value) ? number_format($supplierData->Total_Disbursed_Value) : '0'}}</p>
                                   </div>
                               </div>
                             </div>
                           </div>
                         </div>
                           
                         <div class="col-6">
                           <div class="card card-statistics">
                             <div class="card-body">
                               <div class="d-flex mb-3">
                                   <div class="text-primary">
                                     <i class="fa fa-money highlight-icon" aria-hidden="true"></i>
                                   </div>
                                   <div class="ml-2 highlight-text">
                                       <p class="card-text">Total Outstanding Amount(₹) <span style="font-size: 11px;color: #8181e8;">*Included Charges</span></p>
                                       <p class="statistics-number">{{isset($outstandingAmt) && ($outstandingAmt > 0) ? number_format($outstandingAmt, 2) : '0'}}</p>
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