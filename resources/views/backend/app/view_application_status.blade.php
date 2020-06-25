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
        <main>
            <div class="breakNow">
                <table border="1px" style="width: 100%;clear: both; margin-top: 10px;" align="center" cellspacing="0" cellpadding="1">
                    <tr>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#138864">
                            <span style="font-size: small;"><strong>S. No.</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#138864">
                            <span style="font-size: small;"><strong>Status</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#138864">
                            <span style="font-size: small;"><strong>Comments</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#138864">
                            <span style="font-size: small;"><strong>Created At</strong></span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#138864">
                            <span style="font-size: small;"><strong>Created By</strong></span>
                        </td>
                    </tr>
                    @if($allCommentsData)
                    @php
                        $i = 1;
                        $statusIdArr = [
                            43 => 'Rejected',
                            44 => 'Cancelled',
                            45 => 'On Hold',
                            46 => 'Data Pending'
                        ]
                    @endphp
                    @foreach($allCommentsData as $rowData)
                    <tr>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{ $i++ }}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{ $statusIdArr[$rowData->status_id] }}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{ $rowData->note_data }}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{ $rowData->created_at }}</span>
                        </td>
                        <td style="border: 1px solid #ddd;padding: 5px;">
                            <span style="font-size: small;">{{$rowData->f_name.' '.$rowData->m_name}}</span>
                        </td>
                    </tr>
                    @endforeach
                    @else
                        <tr>
                            <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#138864">
                                <span style="font-size: small;"><strong>Data Not Found....</strong></span>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </main>
    </body>
</html>



