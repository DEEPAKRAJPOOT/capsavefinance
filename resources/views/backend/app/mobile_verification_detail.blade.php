<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title></title>

<style>
@import url("https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,700,900");
body{margin:0; padding: 0;}
td, th{padding: 5px;}
</style>
</head>
<body>
<table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" style="font-size:13px;padding:20px;background-color:#efefef;font-family:Montserrat,Arial,sans-serif;table-layout:fixed">
    <tbody>
         
         <tr>
            <td align="center" style="">
             <table width="100%" border="0">
                 <tbody>
                    <tr>
                        <td width="50%">Mobile Number is Valid?</td>
                        <td width="50%"><strong>{{$response['isValid'] ? 'YES' : 'NO'}} </strong></td>
                    </tr>
                    <tr>
                        <td width="50%">Connection Type</td>
                        <td width="50%">{{$response['connectionType']}}</td>
                    </tr>
                    <tr>
                        <td width="50%">Subscriber Status</td>
                        <td width="50%">{{$response['subscriberStatus']}}</td>
                    </tr>
                    <tr>
                        <td width="50%">ID</td>
                        <td width="50%">-</td>
                    </tr>
                    <tr>
                        <td width="50%">Is Ported</td>
                        <td width="50%">{{$response['isPorted'] ? 'YES' : 'NO'}}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>Connection Status</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">Status Code</td>
                        <td width="50%">{{$response['connectionStatus']['statusCode'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">Serving HLR</td>
                        <td width="50%">{{$response['connectionStatus']['servingHlr'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">Error Code ID</td>
                        <td width="50%">{{$response['connectionStatus']['errorCodeId'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td colspan="2"><strong>MSISDN Details</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">MSISDN</td>
                        <td width="50%">{{$response['msisdn']['msisdn'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">MCC</td>
                        <td width="50%">{{$response['msisdn']['mcc'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">MCCMNC</td>
                        <td width="50%">{{$response['msisdn']['mccMnc'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">MSIN</td>
                        <td width="50%">{{$response['msisdn']['msin'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">MSISDN Country Code</td>
                        <td width="50%">{{$response['msisdn']['msisdnCountryCode'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">Serving MSC</td>
                        <td width="50%">{{$response['msisdn']['servingMsc'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">MNC</td>
                        <td width="50%">{{$response['msisdn']['mnc'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td width="50%">IMSI</td>
                        <td width="50%">{{$response['msisdn']['imsi'] ?? '--'}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="">
                            <table width="100%" border="0">
                             <tbody>
                                <tr>
                                    <th style="padding-left: 0;" width="25%" align="left">Service Providers</th>
                                    <th width="25%" align="left">Original</th>
                                    <th width="25%" align="left">Current</th>
                                    <th width="25%" align="left">Roaming</th>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Country Name</td>
                                    <td>{{$response['originalServiceProvider']['countryName'] ?? '--'}}</td>
                                    <td>{{$response['currentServiceProvider']['countryName'] ?? '--'}}</td>
                                    <td>{{$response['roamingServiceProvider']['countryName'] ?? '--'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">network Prefix</td>
                                    <td>{{$response['originalServiceProvider']['networkPrefix'] ?? '--'}}</td>
                                    <td>{{$response['currentServiceProvider']['networkPrefix'] ?? '--'}}</td>
                                    <td>{{$response['roamingServiceProvider']['networkPrefix'] ?? '--'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Network Name</td>
                                    <td>{{$response['originalServiceProvider']['networkName'] ?? '--'}}</td>
                                    <td>{{$response['currentServiceProvider']['networkName'] ?? '--'}}</td>
                                    <td>{{$response['roamingServiceProvider']['networkName'] ?? '--'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Country Code</td>
                                    <td>{{$response['originalServiceProvider']['countryCode'] ?? '--'}}</td>
                                    <td>{{$response['currentServiceProvider']['countryCode'] ?? '--'}}</td>
                                    <td>{{$response['roamingServiceProvider']['countryCode'] ?? '--'}}</td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 0;">Country Prefix</td>
                                    <td>{{$response['originalServiceProvider']['countryPrefix'] ?? '--'}}</td>
                                    <td>{{$response['currentServiceProvider']['countryPrefix'] ?? '--'}}</td>
                                    <td>{{$response['roamingServiceProvider']['countryPrefix'] ?? '--'}}</td>
                                </tr>
                             </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
             </table>
            </td>
         </tr>

</tbody>
</table>

    
</body>
</html>