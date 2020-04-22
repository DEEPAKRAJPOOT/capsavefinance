<div style="width: 700px;margin:auto;">
   <p align="center">
      <span style="color: #2d2d2d;font-size: xx-large;"><strong>{{$comp_name}}</strong></span>
   </p>
   <p align="center">
      <span style="font-size: small;">
         <strong>Registered office: {{$comp_registered_addr}}</strong>
      </span>
   </p>
   <p align="center">
      <span style="font-size: small;"><strong>Ph:</strong></span>
      <span style="font-size: small;"> {{$phone}}; </span>
      <span style="font-size: small;"><strong>CIN No:</strong></span>
      <span style="font-size: small;">{{$cin}};</span>
      <span style="font-size: small;"><strong>Email:</strong></span><span style="font-size: small;"> {{$email}}</span>
   </p>
   <hr />
   <p align="center" style="color: #000000;"><u><strong>GST Tax Invoice</strong></u></p>
   <p style="font-size: small;text-align: right"><strong><u>Original for Recipient</u></strong></p>
   <div style="text-align: center">
      <table border="1px" style="width: 100%" align="center" cellspacing="0" cellpadding="1">
         <tr>
            <td width="70%"><span style="font-size: small"><strong>Billing Address: </strong></span></td>
            <td width="30%"><span style="font-size: small"><strong>Invoice No: </strong> {{$invoice_no}}</span></td>
         </tr>
         <tr>
            <td rowspan="3"><span style="font-size: small"><strong>{{$comp_billing_addr}}</strong></td>
            <td><span style="font-size: small"><strong>Invoice Date: </strong> {{$invoice_date}}</span></td>
         </tr>
         <tr>
            <td><span style="font-size: small"><strong>Reference No: </strong> {{$ref_no}}</span></td>
         </tr>
         <tr>
            <td><span style="font-size: small"><strong>Place of Supply: </strong> {{$place_of_supply}}</span></td>
         </tr>
      </table>
   </div> 
   <div style="text-align: center;margin-top: 20px">
      <table border="1px" style="width: 100%;" align="center" cellspacing="0" cellpadding="1">
         <tr>
            <td rowspan="2" bgcolor="#f2f2f2" width="10" height="7">
               <span style="font-size: small;"><strong>Sr No</strong></span>
            </td>
            <td rowspan="2" bgcolor="#f2f2f2" width="20">
               <span style="font-size: small;"><strong>Description</strong></span>
            </td>
            <td rowspan="2" bgcolor="#f2f2f2" width="15">
               <span style="font-size: small;"><strong>SAC</strong></span>
            </td>
            <td rowspan="2" bgcolor="#f2f2f2" width="20">
               <span style="font-size: small;"><strong>Base Amount (Rs)</strong></span>
            </td>
            <td colspan="2" bgcolor="#f2f2f2" width="40">
               <span style="font-size: small;"><strong>SGST/UTGST</strong></span>
            </td>
            <td colspan="2" bgcolor="#f2f2f2" width="40">
               <span style="font-size: small;"><strong>CGST</strong></span>
            </td>
            <td colspan="2" bgcolor="#f2f2f2" width="40">
               <span style="font-size: small;"><strong>IGST</strong></span>
            </td>
            <td rowspan="2" bgcolor="#f2f2f2" width="15">
               <span style="font-size: small;"><strong>Total Rental</strong></span>
            </td>
         </tr>
         <tr>
            <td bgcolor="#f2f2f2" width="10">
               <span style="font-size: small;"><strong>Rate (%)</strong></span>
            </td>
            <td bgcolor="#f2f2f2" width="30">
               <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
            </td>
            <td bgcolor="#f2f2f2" width="10">
               <span style="font-size: small;"><strong>Rate (%)</strong></span>
            </td>
            <td bgcolor="#f2f2f2" width="30">
               <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
            </td>
            <td bgcolor="#f2f2f2" width="10">
               <span style="font-size: small;"><strong>Rate (%)</strong></span>
            </td>
            <td bgcolor="#f2f2f2" width="30">
               <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
            </td>
         </tr>
         @php
         $total_base_amt = 0;
         $total_sgst_amt = 0;
         $total_cgst_amt = 0;
         $total_igst_amt = 0;
         $sum_total_rental = 0;
         $count = 0;
         @endphp
         @foreach($intrest_charges as $key => $txns)
         @php
         $total_base_amt += $txns['base_amt'];
         $total_sgst_amt += $txns['sgst_amt'];
         $total_cgst_amt += $txns['cgst_amt'];
         $total_igst_amt += $txns['igst_amt'];
         $sum_total_rental += $txns['total_rental'];
         $count++;
         @endphp
          <tr>
            <td>
               <span style="font-size: small;">{{$count}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['desc']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['sac']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['base_amt']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['sgst_rate']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['sgst_amt']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['cgst_rate']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['cgst_amt']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['igst_rate']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['igst_amt']}}</span>
            </td>
            <td>
               <span style="font-size: small;">{{$txns['total_rental']}}</span>
            </td>
         </tr>
         @endforeach
         <tr>
            <td bgcolor="#f2f2f2">
               <span style="font-size: small;"><strong>&nbsp;</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
               <span style="font-size: small;"><strong>Total</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>&nbsp;</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>{{$total_base_amt}}</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>&nbsp;</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>{{$total_sgst_amt}}</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>&nbsp;</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>{{$total_cgst_amt}}</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>&nbsp;</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>{{$total_igst_amt}}</strong></span>
            </td>
            <td bgcolor="#f2f2f2">
              <span style="font-size: small;"><strong>{{$sum_total_rental}}</strong></span>
            </td>
         </tr>
      </table>
   </div>
   <p><span style="font-family: 'Book Antiqua', serif;font-size: small;"><u><strong>Payment Instructions:</strong></u></span></p>
   <span style="font-family: 'Book Antiqua', serif;font-size: small;">Please send your cheque/DD payable at par in Mumbai for <strong>Rs {{$sum_total_rental}} </strong> to </span>
   <div style="margin-top: 10px;font-size: small;"><strong>{{$comp_name}}</strong></div>
   <span style="font-size: small;"><strong>{{$comp_registered_addr}}</strong></span>

   <p><span style="font-size: small;"><u><strong>RTGS Details:</strong></u></span></p>
   <p lang="en-US" align="justify" style="font-family: 'Book Antiqua', serif;font-size: small;"><strong>Beneficiary: Capsave Finance Pvt. Ltd; HDFC Bank, ESCROW A/C NO: 50200030310781; Branch Name: A.K. Vaidya Marg, Mumbai; IFSC Code: HDFC0000212</strong></p>
   <table style="width: 100%" align="center" border="1" cellspacing="0" cellpadding="1">
      <tbody>
         <tr>
            <td style="width: 30%">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>PAN: </strong></span>
            </td>
            <td style="width: 70%">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">AAACA4269Q</span>
            </td>
         </tr>
         <tr>
            <td>
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>State:</strong></span>
            </td>
            <td>
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">Maharashtra</span>
            </td>
         </tr>
         <tr>
            <td>
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>Address:</strong></span>
            </td>
            <td>
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">UNIT NO. 501, WING-D, LOTUS CORPORATE PARK, WESTERN EXPRESS HIGHWAY, GOREGAON (EAST), MUMBAI - 400 063, MAHARASHTRA</span>
            </td>
         </tr>
         <tr>
            <td>
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>GSTIN:</strong></span>
            </td>
            <td>
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">27AAACA4269Q2Z5</span>
            </td>
         </tr>
      </tbody>
   </table>
   <p align="center"><span style="font-family: 'Book Antiqua', serif;"><span style="font-size: small;">This is a digitally signed invoice. The certification details of the signatory can be accessed on Acrobat Reader DC.</span></span></p>
   <p align="center">&nbsp;</p>
   <p><span style="font-family: 'Book Antiqua', serif;"><span style="font-size: small;"><strong>For Capsave Finance Pvt. Ltd.</strong></span></span></p>
   <p lang="en-US">&nbsp;</p>
   <p lang="en-US">&nbsp;</p>
   <p lang="en-US"><span style="font-family: 'Book Antiqua', serif;"><span style="font-size: small;"><strong>Authorized Signatory</strong></span></span></p>
   <p>&nbsp;</p>
</div>
