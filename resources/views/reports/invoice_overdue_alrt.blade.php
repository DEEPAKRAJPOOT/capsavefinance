<table border="0" cellpadding="0" cellspacing="0"
    style="width: 100%; background-color: #f4f8fb; font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important;"
    bgcolor="#f8f8f8">

    <tr>
      <td>
        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600"
          style="width:100%; background-color: #ffffff; color: #514d6a; padding: 40px; line-height: 28px;"
          bgcolor="#ffffff">

          <tr>
            <td
              style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:20px; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              {{ $userData['user_name'].' - '.$userData['email'] }} <br/><br/>
              Dear Sir/Madam,
            </td>
          </tr>
          <tr>
            <td style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding-top:15px;font-weight: 500; font-size: 0.917rem !important; font-family: Calibri !important; color: #111; line-height: 11px;">
              This is to inform you that the below invoices are in overdue towards the Supply Chain Facility.
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
                      Customer Id 
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Batch No 
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Batch Date 
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Bill No 
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Bill Date
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid#ccc; color: #262626;">
                      Due Date
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Bill Amount
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Approve Amount
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                      Discounted Amount
                    </td>
                    <td
                      style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;color: #262626;">
                      Amount Due
                    </td>
                    

                  </tr>
                  @foreach($data as $key=>$val)
                    <tr>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['cust_id'] }}
                      </td>


                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['batch_no'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['batch_date'] }}
                      </td>

                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['inv_no'] }}
                      </td>
                      <td
                        style="text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['bill_date'] }}
                      </td>
                      <td
                        style="text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['due_date'] }}
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['bill_amt'] }}
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['approve_amt'] }}
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ $val['discounted_amt'] }}
                      </td>
                      <td
                        style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                        {{ number_format($val['balance']) }}
                      </td>
                    </tr>
                    </tbody>
                    @if(isset($val['transactions']))
                      <tr >
                        <td colspan="10">
                          <table border="0" cellpadding="0" cellspacing="0"
                            style="width: 100%;border: 1px solid #ccc; margin-top: 10px; margin-bottom: 10px;">
                            <tbody>
                              <tr style="background-color: #eceff1;">
                                <td
                                  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                                  Bill No 
                                </td>
                                <td
                                  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                                  Trans Date 
                                </td>
                                <td
                                  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                                  Value Date 
                                </td>
                                <td
                                  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                                  Trans Name 
                                </td>
                                <td
                                  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;border-right:1px solid #ccc;color: #262626;">
                                  Amount
                                </td>
                                <td
                                  style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; font-weight: 600; padding-bottom: 10px;font-size: 0.917rem !important;;white-space: nowrap;padding:2px 5px;color: #262626;">
                                  Amount Due
                                </td>
                                

                              </tr>
                              
                              @foreach($val['transactions'] as $key1 => $val1)
                                <tr>
                                  <td
                                    style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                                    {{ $val['inv_no'] }}
                                  </td>

                                  <td
                                    style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                                    {{ $val1['trans_date'] }}
                                  </td>


                                  <td
                                    style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                                    {{ $val1['value_date'] }}
                                  </td>

                                  <td
                                    style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                                    {{ $val1['trans_name'] }}
                                  </td>

                                  <td
                                    style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;border-right:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                                    {{ number_format($val1['amount']) }}
                                  </td>
                                  <td
                                    style="font-family: Calibri !important; box-sizing: border-box; font-size: 0.917rem !important; text-align: left; padding: 10px 10px 10px 0px; border-top:1px solid #ccc;padding: 2px 5px;font-size: 0.917rem !important;line-height: 18px;vertical-align: top;">
                                    {{ number_format($val1['outstanding']) }}
                                  </td>

                                </tr>
                              @endforeach

                            </tbody>
                          </table>
                          </td>
                      </tr>
                      @endif
                  @endforeach

                
              </table>
            </td>
          </tr>
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
