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
                    <tr>
                        <td width="50%">Date of birth</td>
                        <td width="50%"><strong>{{$response['identity']['date_of_birth']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Gender</td>
                        <td width="50%"><strong>{{$response['identity']['gender']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Name</td>
                        <td width="50%"><strong>{{$response['identity']['name']}}</strong></td>
                    </tr>
                    
                    <tr>
                        <td width="50%">Education</td>
                        <td width="50%"><strong>{{$response['profile']['education']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Marital status</td>
                        <td width="50%"><strong>{{$response['profile']['marital_status']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Occupation</td>
                        <td width="50%"><strong>{{$response['profile']['occupation']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Relationships</td>
                        <td width="50%"><strong>{{$response['profile']['relationships']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Workplace</td>
                        <td width="50%"><strong>{{$response['profile']['workplace']}}</strong></td>
                    </tr>
                    
                     <tr>
                        <td width="50%">Activation Date</td>
                        <td width="50%"><strong>{{$response['sim_details']['activation_date']}}</strong></td>
                    </tr>
                    <tr>
                        <td width="50%">Last Activity date</td>
                        <td width="50%"><strong>{{$response['sim_details']['last_activity_date']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Otp Validated</td>
                        <td width="50%"><strong>{{$response['sim_details']['otp_validated']}}</strong></td>
                    </tr>
                     <tr>
                        <td width="50%">Provider</td>
                        <td width="50%"><strong>{{$response['sim_details']['provider']}}</strong></td>
                    </tr>
                     <tr>
                         <td width="50%">Type</td>
                        <td width="50%"><strong>{{$response['sim_details']['type']}}</strong></td>
                    </tr>
                    
                </tbody>
             </table>
            </td>
         </tr>

</tbody>
</table>

    
</body>
</html>