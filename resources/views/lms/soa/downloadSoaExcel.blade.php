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
                border:1px solid #000000;
            }
            table th{
                background-color: #FFFFFF !important;
                /*-webkit-print-color-adjust: exact;*/
                border-right:#000000 solid 1px;
                padding:5px 10px;
                color:	#000000;
            }
            table th.bg-second{
                background-color: #9c9b9b !important;
                -webkit-print-color-adjust: exact;
                /*border-top: #e2e2e2 solid 1px;*/
            }
            table th:last-child{ 
                border-right:none;
            }
            table td{
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
            <table class="table">
                <tbody>
                    <tr>
                        <th></th>
                        <th><b>A</b></th> 
                        <th><b>B</b></th>
                        <th><b>C</b></th>
                        <th><b>D</b></th>
                        <th><b>E</b></th>
                        <th><b>F</b></th>
                        <th><b>G</b></th>
                        <th><b>H</b></th>
                        <th><b>I</b></th>
                        <th><b>J</b></th>
                        <th><b>K</b></th>
                        <th><b>L</b></th>
                        <th><b>M</b></th>
                    </tr>
            </table>
        </main>
    </body>
</html>



