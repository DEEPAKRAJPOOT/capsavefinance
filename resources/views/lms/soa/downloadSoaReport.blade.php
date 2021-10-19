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
            .pagenum:before {
                content: counter(page);
            }
            div.breakNow { page-break-inside:avoid; page-break-after:always; }
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
            <div  align="center">
                <p><b>CAPSAVE FINANCE PRIVATE LIMITED</b></p>
                <p>Statement Of Account</p>
                <br>
            </div>
            @if($userInfo)
            <table class="table  table-td-right">
                <tbody>
                    <tr>
                        <td class="text-left" width="30%"><b>Business Name</b></td>
                        <td> {{$userInfo->biz->biz_entity_name}}	</td> 
                        <td class="text-left" width="30%"><b>Full Name</b></td>
                        <td>{{$userInfo->f_name}} {{$userInfo->m_name}}	{{$userInfo->l_name}}</td> 

                    </tr>
                    <tr>
                        <td class="text-left" width="30%"><b>Email</b></td>
                        <td>{{$userInfo->email}}	</td> 
                        <td class="text-left" width="30%"><b>Mobile</b></td>
                        <td>{{$userInfo->mobile_no}} </td> 
                    </tr>
                    @if($fromdate && $todate)
                    <tr>
                        <td class="text-left" width="30%"><b>From Date</b></td>
                        <td>{{$fromdate}}</td> 
                        <td class="text-left" width="30%"><b>To Date</b></td>
                        <td>{{$todate}}</td> 
                    </tr>
                    @endif
                </tbody>
            </table>
            @endif
            @foreach($soaRecord as $soak => $soaRec)
            <div class="breakNow">
                <table border="1px" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                    <tr>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Customer ID</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Trans Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Value Date</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Tran Type</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Batch No</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Invoice No</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Capsave Invoice No</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Narration</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Currency</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Debit</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Credit</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
                            <span style="font-size: small;"><strong>Balance</strong></span>
                        </td>
                    </tr>
                    @foreach($soaRec as $key => $record)
                    <tr style="background: {{$record['soabackgroundcolor']}};">
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['customer_id']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['trans_date']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['value_date']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['trans_type']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['batch_no']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['invoice_no']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['capsave_invoice_no']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['narration']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['currency']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['debit']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['credit']}}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$record['balance']}}</span>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endforeach
        </main>
    </body>
</html>



