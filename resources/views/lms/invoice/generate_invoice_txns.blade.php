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
         <td style="border: 1px solid #ddd;padding: 5px;text-align: center;">
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
            <span style="font-size: small;">{{sprintf('%.2F', $txns['base_amt'])}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{ !empty($txns['sgst_rate']) ? sprintf('%.2F', $txns['sgst_rate']) : '-'}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{ !empty($txns['sgst_amt']) ? sprintf('%.2F', $txns['sgst_amt']) : '-'}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{ !empty($txns['cgst_rate']) ? sprintf('%.2F', $txns['cgst_rate']) : '-'}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{ !empty($txns['cgst_amt']) ? sprintf('%.2F', $txns['cgst_amt']) : '-'}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{ !empty($txns['igst_rate']) ? sprintf('%.2F', $txns['igst_rate']) : '-'}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{ !empty($txns['igst_amt']) ? sprintf('%.2F', $txns['igst_amt']) : '-'}}</span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;">
            <span style="font-size: small;">{{sprintf('%.2F', $txns['total_rental'])}}</span>
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
            <span style="font-size: small;"><strong>{{ !empty($total_base_amt)  ? sprintf('%.2F', $total_base_amt) : '-' }}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{ !empty($total_sgst_amt) ? sprintf('%.2F', $total_sgst_amt) : '-' }}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{ !empty($total_cgst_amt) ? sprintf('%.2F', $total_cgst_amt) : '-' }}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>&nbsp;</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{ !empty($total_igst_amt) ? sprintf('%.2F', $total_igst_amt) : '-' }}</strong></span>
         </td>
         <td style="border: 1px solid #ddd;padding: 5px;" bgcolor="#f2f2f2">
            <span style="font-size: small;"><strong>{{ sprintf('%.2F', $sum_total_rental) }}</strong></span>
         </td>
      </tr>
   @else
      <tr><td style="border: 1px solid #ddd;padding: 5px;" colspan="{{!empty($checkbox) ? 12 : 11}}">No records found</td></tr>
   @endif