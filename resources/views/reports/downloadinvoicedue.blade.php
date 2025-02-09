<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{url('backend/assets/css/data-table.css')}}" />
        <style>
            @page {
                margin: 2cm 1.44cm 2.25cm 1.44cm;
            }
            /** Define the header rules **/
            header {
                position: fixed;
                top:-0.5cm;
            }
            footer {
                position: fixed;
                bottom:0.5cm;
            }
            *{
                font-size:13px !important;
                font-family: 'source-sans-pro-regular', sans-serif !important;
            }
            table{
                width: 100% !important;
                /*border:1px solid #ccc;*/
            }
            table th{
                background-color:#808080 !important;
                -webkit-print-color-adjust: exact;
                border-right:#c5c5c5 solid 1px;
                padding:5px 10px;
                color:#ffffff;
            }
            table th.bg-second{
                background-color: #9c9b9b !important;
                -webkit-print-color-adjust: exact;
                border-top: #e2e2e2 solid 1px;
            }
            table th:last-child{ 
                border-right:none;
            }
            table td{
                /*border-right:#c5c5c5 solid 1px;*/
                /*border-bottom:#c5c5c5 solid 1px;*/
                padding:5px 10px;
            }
            table td:last-child{
                border-right:none;
            }
            table td.blank{ 
                background-color:#cccccc !important;
                -webkit-print-color-adjust: exact;
            }
            p{
                margin:0px;
            }
            .data {
                border: 1px solid #e9ecef; 
                margin-bottom: 25px;
            }
            .sub-title.bg {
                background: #efefef;
                padding: 10px 15px;
                width: 100%;
                float: left;
                margin: 0px;
                box-sizing: border-box;
            }
            .pl-4.pr-4.pb-4.pt-2{
                padding:15px;
                clear: both;
            }
             #filterTable td,th{
                text-align: left;
            }
            .pagenum:before {
                content: counter(page);
            }
            /*div.breakNow { page-break-inside:avoid; page-break-after:always; }*/
        </style>

    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
            @php
            $date = \Carbon\Carbon::now();   
            @endphp
            <span align="right" style="float: left;"><b>{{ $date->isoFormat('MMMM D, Y')}}</b></span>
        </header>
        <footer>
            <hr>
            <span class="pagenum"></span><b> |</b> CFPL
        </footer>
        <main>
            <div  align="center" style="margin-top:40px;">
                <p><b><b>CAPSAVE FINANCE PRIVATE LIMITED</b></b></p>
                <br>
            </div>
             @if(!empty($filter))
            <table class="table  table-td-right" id="filterTable">
                <tbody>
                    @if(!empty($filter['userInfo']))
                    <tr>
                        <td><strong>Business Name</strong></td>
                        <td> {{$filter['userInfo']->biz->biz_entity_name}}    </td> 
                        <td><strong>Full Name</strong></td>
                        <td>{{$filter['userInfo']->f_name}} {{$filter['userInfo']->m_name}} {{$filter['userInfo']->l_name}}</td> 

                    </tr>
                    <tr>
                        <td><strong>Email</strong></td>
                        <td>{{$filter['userInfo']->email}}    </td> 
                        <td><strong>Mobile</strong></td>
                        <td>{{$filter['userInfo']->mobile_no}} </td> 
                    </tr>
                    @endif
                    @if($filter['from_date'] && $filter['to_date'])
                    <tr>
                        <td><strong>From Date</strong></td>
                        <td>{{$filter['from_date']}}</td> 
                        <td><strong>To Date</strong></td>
                        <td>{{$filter['to_date']}}</td> 
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif
          
            <div class="breakNow">
                <table border="1px" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                    <tr>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Customer Id</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Batch No</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Batch Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Bills No</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Bill Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Due Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Bill Amount</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Approve Amount</strong></span>
                        </td>
                       
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Balance</strong></span>
                        </td>
                      
                    </tr>
                    @php
                    $invBal = 0;
                    $invApprBal = 0;
                    $subBal = 0;
                    @endphp
                   @foreach($userInfo as $invoice) 
                   @php
                   $invBal += $invoice->invoice->invoice_amount;
                   $invApprBal += $invoice->invoice->invoice_approve_amount;
                   @endphp
                    <tr>
                        <td>
                            <span style="font-size: small;">{{$invoice->customer_id}}</span>
                        </td>
                        <td>
                            <span style="font-size: small;">{{$invoice->disbursal->disbursal_batch->batch_id}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{date('d/m/Y',strtotime($invoice->disbursal->disbursal_batch->created_at))}}</span>
                        </td>
                        <td>
                            <span style="font-size: small;">{{$invoice->invoice->invoice_no}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{\Carbon\Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y')}}</span>
                        </td>
                        <td>
                              <span style="font-size: small;">{{\Carbon\Carbon::parse($invoice->payment_due_date)->format('d/m/Y')}}</span>
                       
                        </td>
                        <td>
                            <span style="font-size: small;">{{number_format($invoice->invoice->invoice_amount)}}</span>
                        </td>
                         <td>
                            <span style="font-size: small;">{{number_format($invoice->invoice->invoice_approve_amount)}}</span>
                        </td>
                        <td>
                            <span style="font-size: small;">
                       @php
                       
                            echo  number_format($invoice->invoice->invoice_approve_amount); 
                            $getBal =  $invoice->invoice->invoice_approve_amount;   
                       
                        $subBal += $getBal;
                      @endphp
                            </span>
                        </td>
                    </tr>
                    @endforeach
                       <tr>
                           <td colspan="6">
                            <span style="font-size: small;"><strong>Grand Total</strong></span>
                        </td>
                          <td>
                            <span style="font-size: small;"><strong>{{number_format($invBal)}}</strong></span>
                        </td>
                          <td>
                            <span style="font-size: small;"><strong>{{number_format($invApprBal)}}</strong></span>
                        </td>
                          <td>
                            <span style="font-size: small;"><strong>{{number_format($subBal)}}</strong></span>
                        </td>
                    </tr>
                </table>
            </div>
            
        </main>
    </body>
</html>



