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
            .bld {  font-weight: bold;}
        </style>
    </head>
    <body>
     
        <table  border="1" align="center" cellspacing="0" cellpadding="0" style="font-size:15px;padding:20px;font-family:Montserrat,Arial,sans-serif;line-height: 24px;width: 100%">
            <tbody>

                <tr>
                    <td align="center" style="">
                        <table width="100%" border="1">
                            <tbody>
                                <tr>
                                    <td colspan="4"><b>Invoice Number : {{($result->invoice_no) ? $result->invoice_no : '' }} </b> </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>   Repay count :  </b>
                                    </td>
                                    <td>1</td> 
                                    <td>  
                                   <b>  Invoice Approved Amount (₹): </b>
                                      </td>
                                    <td>  {{($result->invoice_due_date) ? $result->invoice_amount : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                          <b>    Funded Amount (₹):  </b> 
                                       </td>
                                    <td>  {{($result->disbursal->principal_amount) ? $result->disbursal->principal_amount : '' }}
                                    </td> 
                                    <td>
                                     <b>    Final Funded Amount (₹):  </b>
                                     </td>
                                    <td>   {{($result->disbursal->disburse_amount) ? $result->disbursal->disburse_amount : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                     <b>    Funded Date:  </b>
                                       </td>
                                    <td>   {{($result->disbursal->disburse_date) ? $result->disbursal->disburse_date : '' }}
                                    </td> 
                                    <td>
                                     <b>    Tenor (in days): </b>
                                        </td>
                                    <td> {{($result->disbursal->tenor_days) ? $result->disbursal->tenor_days : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <b>     Payment Due Date:  </b>
                                      </td>
                                    <td>   14-March-2020
                                    </td> 
                                    <td>
                                     <b>    Interest Per Annum (%):  </b>
                                       </td>
                                    <td>  {{($result->disbursal->interest_rate) ? $result->disbursal->interest_rate : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <b>     Processing Fee (%):   </b>
                                     </td>
                                    <td>    1 %
                                    </td> 
                                    <td>
                                    <b>     Discount Type:  </b>
                                      </td>
                                    <td>   front end
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    <b>     Grace period (in days):  </b>
                                      </td>
                                    <td>   0
                                    </td> 
                                    <td>
                                     <b>    Penal Interest Per Annum (%):  </b>
                                      </td>
                                    <td>   0 %
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                   <b>      Repayment Amount:  </b>
                                    </td>
                                    <td>     ₹0
                                    </td> 
                                    <td>
                                     <b>    Total Amount Repaid:  </b>
                                      </td>
                                    <td>   ₹0
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                     <b>    Penal days:  </b>
                                     </td>
                                    <td>    41
                                    </td> 
                                    <td>
                                      <b>   Penalty Amount:  </b>
                                       </td>
                                    <td>  ₹0
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                      <b>   Principal Amount:  </b>
                                      </td>
                                    <td>   ₹60,000
                                    </td> 
                                    <td>
                                     <b>    Total Amount to Repay:  </b>
                                      </td>
                                    <td>   ₹0
                                    </td>
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