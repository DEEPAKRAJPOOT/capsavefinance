   <h2 style="font-size: 15px;font-family: Calibri;margin: 0;" align="center"><strong>{{$registeredCompany->cmp_name}}</strong></h2>
   <h2 style="font-size: 8.5px;font-family: Calibri;margin: 0;" align="center"><strong>Registered office: {{$registeredCompany->cmp_add}}</strong></h2>
   <h2 align="center" style="font-size: 8.5px;font-family: Calibri;">
      <span><strong>Ph:</strong></span>
      <span> {{$registeredCompany->cmp_mobile}}; </span>
      <span><strong>CIN No:</strong></span>
      <span>{{$registeredCompany->cin_no}};</span>
      <span><strong>Email:</strong></span><span style="font-size: small;"> <a href="mailto:{{$registeredCompany->cscsfsfs}}">{{$registeredCompany->cmp_email}}</a></span>
   </h2>
   <hr />
   <h2  style="font-size: 10px;text-align: center; margin: 5px 0 5px;"><strong><u>GST TAX INVOICE</u></strong></h2>
   <span style="font-family:Book Antiqua;padding-left: 6px;margin-bottom: 10px;border-left: 6px solid #108763; float: left;width: 50%; font-size: 9px;">
      <strong>
      <span>BILLING ADDRESS:</span><br />
      <span style="line-height: 1.5;">{{$billingDetails['name']}}</span><br />
      <span style="line-height: 1.5;">{{$billingDetails['address']}}</span><br />
      <span style="line-height: 1.5;">GSTIN: {{$billingDetails['gstin_no']}}</span><br />
      <span style="line-height: 1.5;">PAN Number: {{$billingDetails['pan_no']}}</span>
      </strong>
   </span>
   <span style="font-family:Book Antiqua;float: right;width: 45%;text-align: right;font-size: 9px;">
      <span><strong>Original for Recipient:</strong></span><br />
      <span>Invoice No: {{$origin_of_recipient['invoice_no']}}</span><br />
      <span>Invoice Date: {{$origin_of_recipient['invoice_date']}}</span><br />
      <span>Reference No: #{{$origin_of_recipient['reference_no']}}</span><br />
      <span>Place of Supply: {{$billingDetails['state_name']}}</span><br />
   </span>
   <table border="1px" style="width: 100%;clear: both;" align="center" cellspacing="0" cellpadding="1">
      <tr>
         <td style="border: 1px solid #ddd;padding: 2px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Sr No</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Description</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>SAC</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Base Amount (Rs)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" colspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>SGST/UTGST</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" colspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>CGST</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" colspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>IGST</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" rowspan="2" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Total Rental</strong></span>
         </td>
      </tr>
      <tr>
         <td style="border: 1px solid #ddd;padding: 2px;" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Rate (%)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Amount (Rs)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Rate (%)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Amount (Rs)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Rate (%)</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 2px;" bgcolor="#f2f2f2">
            <span style="font-size: 9px;font-family: Calibri;"><strong>Amount (Rs)</strong></span>
         </td>
      </tr>
      @include('lms.invoice.generate_invoice_txns')
   </table>
   <p style="font-family:Calibri;font-size: 9px; margin: 4px;"><strong>Payment Instructions:</strong></p>
   <p style="font-family:Calibri;font-size: 9px;margin: 0px;">Please send your cheque/DD payable at per in Mumbai for <strong>Rs {{sprintf('%.2F', $total_sum_of_rental) }} </strong> to </p>
   <p style="font-family:Calibri;font-size: 9px;margin: 0px;"><strong>{{$company_data['name']}}</strong></p>
   <p style="font-family:Calibri;font-size: 9px;margin: 0px;"><strong>{{$company_data['address']}}</strong></p>
   <p style="font-family:Calibri;font-size: 9px;margin: 0px;"><strong>Beneficiary: {{$company_data['name']}}; {{$company_data['bank_name']}}, ESCROW A/C NO: {{$company_data['acc_no']}}; Branch Name: {{$company_data['branch_name']}}; IFSC Code: {{$company_data['ifsc_code']}}</strong></p>

   <table style="width: 100%" align="center" border="1" cellspacing="0" cellpadding="1">
      <tbody>
         <tr>
            <td style="border: 1px solid #ddd;padding: 5px;" style="width: 30%">
               <span style="font-size: 9px;font-family: Book Antiqua;"><strong>PAN: </strong></span>
            </td>
            <td style="border: 1px solid #ddd;padding: 5px;" style="width: 70%">
               <span style="font-size: 9px;font-family: Book Antiqua;">{{$company_data['pan_no']}}</span>
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
