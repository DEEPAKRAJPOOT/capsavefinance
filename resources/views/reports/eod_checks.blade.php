<div
  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f8f8f8; margin: 0;">

  <table border="0" cellpadding="0" cellspacing="0"
    style="width: 100%; background-color: #f4f8fb; font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important;"
    bgcolor="#f8f8f8">

    <tr>
      <td>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
          style="width:600px; background-color: #ffffff; color: #514d6a; padding: 40px; line-height: 28px;"
          bgcolor="#ffffff">

          <tr>
            <td
              style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:20px; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              Dear Sir/Madam,
            </td>
          </tr>
          <tr>
            <td style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:15px;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              Please check below mentioned EOD checks data.
            </td>
          </tr>

          @if(count($tally_data))          
          <tr>
            <td>
              <table border="0" cellpadding="0" cellspacing="0"
                style="width: 100%;border: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                <tbody>
                  <tr style="background-color: #eceff1;">
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Sr. No
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Tally Batch No
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Start Date
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      End Date
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Total Record
                    </td>  
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Matched Record
                    </td>  
                  </tr>
                  @php $i = 0; @endphp
                  @foreach($tally_data as $batchNo => $tally)
                    <tr>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">                        
                        {{ ++$i }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $batchNo }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $tally['start_date'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $tally['end_date'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $tally['total_record'] }}
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $tally['matched_record'] }}
                      </td>
                    </tr>

                    @if(!empty($tally['trans_wise_data']) && count($tally['trans_wise_data']))
                    <tr style="background-color: #eceff1;">
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">                        
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                        Transaction Type
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                        Total Record
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                        Total Amount
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                        Record Matched/Unmatched
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                        Amount Matched/Unmatched
                      </td>
                    </tr>
                    @php
                      $tallyTransWiseData = $tally['trans_wise_data'];
                    @endphp
                    @foreach($tallyTransWiseData as $transName => $transWiseData)
                    <tr>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $transName }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $transWiseData['transCount'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $transWiseData['transAmt'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ isset($transWiseData['tallyCount']) ? $transWiseData['tallyCount'] : 0 }}/{{ ($transWiseData['transCount'] - (isset($transWiseData['tallyCount']) ? $transWiseData['tallyCount'] : 0)) }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ isset($transWiseData['tallyAmt']) ? round($transWiseData['tallyAmt'], 2) : 0 }}/{{ round(($transWiseData['transAmt'] - (isset($transWiseData['tallyAmt']) ? round($transWiseData['tallyAmt'], 2) : 0)), 2) }}
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  @endforeach
                </tbody>
              </table>
            </td>
          </tr>
          @endif

          @if(count($tally_error_data))
          <tr>
            <td style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:15px;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              <strong>Tally Error Records.</strong>
            </td>
          </tr>      
          <tr>
            <td>
              <table border="0" cellpadding="0" cellspacing="0"
                style="width: 100%;border: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                <tbody>
                  <tr style="background-color: #eceff1;">
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Transaction Type
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Tally Batch No
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Trans Ids
                    </td>
                  </tr>
                  @foreach($tally_error_data as $batchNo => $tally_error)
                    @foreach($tally_error as $error)
                    <tr>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $error['transaction_name'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $batchNo }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $error['trans_ids'] }}
                      </td>
                    </tr>
                  @endforeach
                  @endforeach

                </tbody>
              </table>
            </td>
          </tr>
          @endif

          @if(count($payments))
          <tr>
            <td style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:15px;padding-bottom:15px;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              <strong>Duplicate Payments</strong>
            </td>
          </tr>
          <tr>
            <td>
              <table border="0" cellpadding="0" cellspacing="0"
                style="width: 100%;border: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                <tbody>
                  <tr style="background-color: #eceff1;">
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Payment Id
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Customer Id 
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Amount
                    </td>  
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      UTR No
                    </td>  
                  </tr>
                  @forelse($payments as $key => $payment)
                    <tr>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $payment->payment_ids }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $payment->customer_id }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $payment->amount }}
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $payment->com_utr_no }}
                      </td>
                    </tr>
                  @empty
                  <tr>
                    <td colspan="4" style="text-align:center;">No Payments Found.</td>
                  </tr>  
                  @endforelse

                </tbody>
              </table>
            </td>
          </tr>
          @endif

          @if(count($disbursals))
          <tr>
            <td style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:15px;padding-bottom:15px;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              <strong>Duplicate Disbursals</strong>
            </td>
          </tr>
          <tr>
            <td>
              <table border="0" cellpadding="0" cellspacing="0"
                style="width: 100%;border: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                <tbody>
                  <tr style="background-color: #eceff1;">
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                       Disbursal Id
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Customer Id 
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Amount
                    </td>
                  </tr>
                  @forelse($disbursals as $key => $disbursal)
                    <tr>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $disbursal->disbursal_ids }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $disbursal->customer_id }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $disbursal->amount }}
                      </td>
                    </tr>
                  @empty
                  <tr>
                    <td colspan="3" style="text-align:center;">No Disbursals Found.</td>
                  </tr>  
                  @endforelse
                </tbody>
              </table>
            </td>
          </tr>
          @endif  
          <tr>
            <td
              style="box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:30px;font-weight: 600; font-size: 0.917rem !important; font-family: Calibri !important; line-height: 21px; padding-bottom:5px;color: #111;">
              Warm Regards,
            </td>
          </tr>
          <tr>
            <td
              style="box-sizing: border-box; font-size: 0.917rem !important; text-align: left;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; line-height: 21px; padding-bottom:5px;color: #111;">
              Team Capsave
            </td>
          </tr>

          <tr>
            <td
              style="box-sizing: border-box; font-size: 0.917rem !important; text-align: left;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; line-height: 21px; padding-top:25px;padding-bottom:5px;color: #111;">
              This is a computer generated statement
            </td>
          </tr>

        </table>
      </td>
    </tr>

  </table>

</div>