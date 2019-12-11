

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
                        <td width="50%">Address </td>
                        <td width="50%"><strong>{{$response['contact']['address']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">alt contact </td>
                        <td width="50%"><strong>{{$response['contact']['alt_contact']}}</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">Email id </td>
                        <td width="50%"><strong>{{$response['contact']['email_id']}}</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">Work Email</td>
                        <td width="50%"><strong>{{$response['contact']['work_email']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">3G Support</td>
                        <td width="50%"><strong>{{$response['device']['3g_support']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Device Activation Date</td>
                        <td width="50%"><strong>{{$response['device']['device_activation_date']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Imei</td>
                        <td width="50%"><strong>{{$response['device']['imei']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Model</td>
                        <td width="50%"><strong>{{$response['device']['model']}}</strong></td>
                    </tr>
                    
                   @foreach($response['history'] as $history)
                     <tr>
                        <td width="50%">Amount</td>
                        <td width="50%"><strong>{{$history['amount']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Payment date</td>
                        <td width="50%"><strong>{{$history['payment_date']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Payment Type</td>
                        <td width="50%"><strong>{{$history['payment_type']}}</strong></td>
                    </tr>
                    
                   @endforeach 
                    
                    
                </tbody>
             </table>
            </td>
         </tr>

</tbody>
</table>

    
</body>
</html>