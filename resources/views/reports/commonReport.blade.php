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
            }
            table th{
                background-color:#808080 !important;
                -webkit-print-color-adjust: exact;
                border-right:#c5c5c5 solid 1px;
                color:#ffffff;
            }
            table td{
                border-right:#c5c5c5 solid 1px;
                border-bottom:#c5c5c5 solid 1px;
            }
            td,th{
                font-size: <?php echo count($pdfArr[0]) > 8 ? '10px' : '15px'; ?> !important;
                padding:5px;
                text-align: center;
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
            <div class="breakNow">
                <table border="0" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                       <tr>
                        @php
                           $header_cols = array_keys($pdfArr[0]);
                           foreach($header_cols as $key) {
                             $key = ucwords(str_replace('_', ' ', $key));
                             echo "<th>".$key."</th>";
                           }
                        @endphp
                       </tr>
                       @foreach($pdfArr as $val)
                        <tr>
                        @php
                           foreach($val as $rec) {
                             echo "<td>".$rec."</td>";
                           }
                        @endphp
                        </tr>
                       @endforeach
                </table>
            </div>
        </main>
    </body>
</html>



