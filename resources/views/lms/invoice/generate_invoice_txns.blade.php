   @php
      $total_base_amt = 0;
      $total_sgst_amt = 0;
      $total_cgst_amt = 0;
      $total_igst_amt = 0;
      $sum_total_rental = 0;
      $count = 0;
   @endphp
   @if(!empty($intrest_charges))
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
         @if(!empty($checkbox))
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;"><input type="checkbox" class="trans_check" name="trans_id[]" value="{{$txns['trans_id']}}"></span>
         </td>
         @endif
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$count}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['desc']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['sac']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['base_amt']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['sgst_rate']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['sgst_amt']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['cgst_rate']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['cgst_amt']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['igst_rate']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['igst_amt']}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{$txns['total_rental']}}</span>
         </td>
      </tr>
      @endforeach
      <tr>
         @if(!empty($checkbox))
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         @endif
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>Total</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{$total_base_amt}}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{$total_sgst_amt}}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{$total_cgst_amt}}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{$total_igst_amt}}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{$sum_total_rental}}</strong></span>
         </td>
      </tr>
   @else
      <tr>
         <td style="border: 1px solid #ddd;padding: 5px;" colspan="{{!empty($checkbox) ? 12 : 11}}">No records found</td>
      </tr>
   @endif