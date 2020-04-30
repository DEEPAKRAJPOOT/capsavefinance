   <h2 style="font-size: 2.2em;font-weight: normal;margin: 0;" align="center">{{$registeredCompany->cmp_name}}</h2>
   <h2 style="font-size: small;" align="center">Registered office: {{$registeredCompany->cmp_add}}</h2>
   <h2 align="center" style="font-size: small;">
      <span style="font-size: small;"><strong>Ph:</strong></span>
      <span style="font-size: small;"> {{$registeredCompany->cmp_mobile}}; </span>
      <span style="font-size: small;"><strong>CIN No:</strong></span>
      <span style="font-size: small;">{{$registeredCompany->cin_no}};</span>
      <span style="font-size: small;"><strong>Email:</strong></span><span style="font-size: small;"> <a href="mailto:{{$registeredCompany->cscsfsfs}}">{{$registeredCompany->cmp_email}}</a></span>
   </h2>
   <hr />
   <h2  style="font-size: 1.8em;text-align: center; margin: 10px 0 10px; font-weight: 600;">GST TAX INVOICE</h2>
   <span style="padding-left: 6px;margin-bottom: 10px;border-left: 6px solid #108763; float: left;width: 50%;">
      <span style="color: #777;">BILLING ADDRESS:</span>
      <h2 style="font-size: 1.4em;margin: 0;line-height: 1.5;">{{$billingDetails['name']}}</h2>
      <h2 style="font-size: small;font-family: unset;margin: 0;line-height: 1.5;">{{$billingDetails['address']}}</h2>
      <h2 style="font-size: small;font-family: unset;margin: 0;line-height: 1.5;">GSTIN: {{$billingDetails['gstin_no']}}</h2>
      <h2 style="font-size: small;font-family: unset;margin: 0;line-height: 1.5;">PAN Number: {{$billingDetails['pan_no']}}</h2>
   </span>
   <span style="margin-bottom: 10px; float: right;width: 45%;text-align: right;">
      <span style="color: #777;">Original for Recipient:</span>
      <h2 style="font-size: 1.4em;margin: 0;line-height: 1.5;">Invoice No: {{$origin_of_recipient['invoice_no']}}</h2>
      <h2 style="font-size: 1.1em;font-family: unset;margin: 0;line-height: 1.5;color:#777;">Invoice Date: {{$origin_of_recipient['invoice_date']}}</h2>
      <h2 style="font-size: 1.1em;font-family: unset;margin: 0;line-height: 1.5;color:#777;">Reference No: #{{$origin_of_recipient['reference_no']}}</h2>
      <h2 style="font-size: 1.1em;font-family: unset;margin: 0;line-height: 1.5;color:#777;">Place of Supply: {{$billingDetails['state_name']}}</h2>
   </span>
   <table border="1px" style="width: 100%;clear: both;" align="center" cellspacing="0" cellpadding="1">
      <tr>
         <td style="border: 1px solid #ddd;padding: 5px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Sr No</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Description</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>SAC</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Base Amount (Rs)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" colspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>SGST/UTGST</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" colspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>CGST</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" colspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>IGST</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Total Rental</strong></span>
         </td>
      </tr>
      <tr>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Rate (%)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Rate (%)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Rate (%)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Amount (Rs)</strong></span>
         </td>
      </tr>
      @include('lms.invoice.generate_invoice_txns')
   </table>
   <p style="font-family: 'Book Antiqua', serif;font-size: small; margin-top: 15px;"><u><strong>Payment Instructions:</strong></u></p>
   <span style="font-family: 'Book Antiqua', serif;font-size: small;">Please send your cheque/DD payable at per in Mumbai for <strong>Rs {{sprintf('%.2F', $total_sum_of_rental) }} </strong> to </span>
   <div style="margin-top: 10px;font-size: small;font-family: 'Book Antiqua', serif;"><strong>{{$company_data['name']}}</strong></div>
   <span style="font-size: small;">{{$company_data['address']}}</span>

   <p align="justify" style="font-family: 'Book Antiqua', serif;font-size: small;"><strong>Beneficiary: {{$company_data['name']}}; {{$company_data['bank_name']}}, ESCROW A/C NO: {{$company_data['acc_no']}}; Branch Name: {{$company_data['branch_name']}}; IFSC Code: {{$company_data['ifsc_code']}}</strong></p>

   <table style="width: 100%" align="center" border="1" cellspacing="0" cellpadding="1">
      <tbody>
         <tr>
            <td style="border: 1px solid #ddd;padding: 5px;" style="width: 30%">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>PAN: </strong></span>
            </td>
            <td style="border: 1px solid #ddd;padding: 5px;" style="width: 70%">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">{{$company_data['pan_no']}}</span>
            </td>
         </tr>
         <tr>
            <td style="border: 1px solid #ddd;padding: 5px;">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>State:</strong></span>
            </td>
            <td style="border: 1px solid #ddd;padding: 5px;">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">{{$origin_of_recipient['place_of_supply']}}</span>
            </td>
         </tr>
         <tr>
            <td style="border: 1px solid #ddd;padding: 5px;">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>Address:</strong></span>
            </td>
            <td style="border: 1px solid #ddd;padding: 5px;">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">{{$company_data['address']}}</span>
            </td>
         </tr>
         <tr>
            <td style="border: 1px solid #ddd;padding: 5px;">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;"><strong>GSTIN:</strong></span>
            </td>
            <td style="border: 1px solid #ddd;padding: 5px;">
               <span style="font-size: small;font-family: 'Book Antiqua', serif;">{{$company_data['gst_no']}}</span>
            </td>
         </tr>
      </tbody>
   </table>
   <p align="center"><span style="font-family: 'Book Antiqua', serif;"><span style="font-size: small;">This is a digitally signed invoice. The certification details of the signatory can be accessed on Acrobat Reader DC.</span></span></p>
   <span style="font-family: 'Book Antiqua', serif;"><span style="font-size: small;"><strong>FOR {{$company_data['name']}}</strong></span></span>
   <p lang="en-US">&nbsp;</p>
   <p lang="en-US">&nbsp;</p>
   <p lang="en-US"><span style="font-family: 'Book Antiqua', serif;"><span style="font-size: small;"><strong>Authorized Signatory</strong></span></span></p>
   <p>&nbsp;</p>
