
<table border="0" cellpadding="0" cellspacing="0" style="width: 100%; font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size:10px;" >
    @php
        $tick = url('backend/assets/images/checkmark-outline.png');
    @endphp
    <tr>
      <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="width: 100%; background-color: #ffffff; color: #514d6a; padding:12px;" bgcolor="#ffffff">
                <tr>
                    <td style="text-align: left; vertical-align: middle; font-size: 18px; color: #000;">
                        NACH DEBIT MANDATE FORM
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                       
                    </td>
                </tr>

                <tr>
                    <td colspan="2"  align="top" style="border: 1px solid #ddd; padding:5px;">
                        <table border="0" style="width: 100%;">
                            <tr>
                                <td valign="top" style="width: 110px;">
                                    <table style="border: 1px solid #ddd; padding: 10px;">
                                        <tr>
                                            <td colspan="2" style="vertical-align:top;"> Tick (<img src="{{url('backend/assets/images/checkmark-outline.png')}}" height="10px" width="8px">)</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:top;"><span style="border: 1px solid #ddd; height: 18px;width: 18px;display: inline-block;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['request_for']) && ($nachDetail['request_for'] == 1)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php }@endphp</span></td>
                                            <td style="vertical-align:top;font-size:12px;"> CREATE</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:top;"><span style="border: 1px solid #ddd; height: 18px;width: 18px;display: inline-block;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['request_for']) && ($nachDetail['request_for'] == 2)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php }@endphp</span></td>
                                            <td style="vertical-align:top;font-size:12px;"> MODIFY</td>
                                        </tr>
                                        <tr>
                                            <td style="vertical-align:top;"><span style="border: 1px solid #ddd; height: 18px;width: 18px;display: inline-block;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['request_for']) && ($nachDetail['request_for'] == 3)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php }@endphp</span></td>
                                            <td style="vertical-align:top;font-size:12px;"> CANCEL</td>
                                        </tr>
                                    </table>
                                </td>
                                <td  style="vertical-align:top;">
                                  <table style="width: 100%;">
                                        <tr>
                                            <td  style="vertical-align:top;">
                                                <table>
                                                    <tr>
                                                        <td width="80">UMRN</td>
                                                        <td>
                                                            <table cellspacing="0" cellpadding="0" style="border-collapse: separate;">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 220px;">{{isset($nachDetail['umrn']) ? $nachDetail['umrn'] : ''}}&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td align="right" valign="top"  style="vertical-align:top;">
                                                <table>
                                                    <tr>
                                                        <td width="50">Date</td>
                                                        <td>
                                                            <table cellspacing="0" cellpadding="0" style="border-collapse: separate;">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 200px; padding-left: 5px;">{{isset($nachDetail['nach_date']) ? \Carbon\Carbon::parse($nachDetail['nach_date'])->format('d/m/Y') : ''}}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>

                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="50%">
                                                            <table width="100%">
                                                                <tr>
                                                                    <td style="width: 140px;">
                                                                        Sponsor Bank Code 
                                                                    </td>
                                                                    <td style="border: 1px solid #ddd;">
                                                                        {{isset($nachDetail['sponsor_bank_code']) ? $nachDetail['sponsor_bank_code'] : ''}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td width="50%">
                                                            <table width="100%">
                                                                <tr>
                                                                    <td style="width: 150px;padding-left:10px;">
                                                                        Utility Code 
                                                                    </td>
                                                                    <td style="border: 1px solid #ddd;">
                                                                        {{isset($nachDetail['utility_code']) ? $nachDetail['utility_code'] : ''}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="50%">
                                                            <table width="100%">
                                                                <tr>
                                                                    <td style="width: 140px;">
                                                                        SI/We hereby authorize
                                                                    </td>
                                                                    <td style="border: 1px solid #ddd;">
                                                                        {{isset($nachDetail['here_by_authorize']) ? $nachDetail['here_by_authorize'] : ''}}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td width="50%">
                                                            <table width="100%">
                                                                <tr>
                                                                    <td style="width: 100px;padding-left:10px;">
                                                                        to debit Tick (<img src="{{url('backend/assets/images/checkmark-outline.png')}}" height="10px" width="8px">)
                                                                    </td>
                                                                    <td style="border: 1px solid #fff;">
                                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                                            <tr>
                                                                                <td style="border: 1px solid #ddd; height: 22px; width: 20px;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['debit_tick']) && ($nachDetail['debit_tick'] == 1)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                                <td style="padding-left: 5px;">SB</td>
                                                                                <td style="border: 1px solid #ddd; height: 22px; width: 20px;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['debit_tick']) && ($nachDetail['debit_tick'] == 2)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                                <td style="padding-left: 5px;">CA</td>
                                                                                <td style="border: 1px solid #ddd; height: 22px; width: 20px;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['debit_tick']) && ($nachDetail['debit_tick'] == 3)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                                <td style="padding-left: 5px;">CC</td>
                                                                                <td style="border: 1px solid #ddd; height: 22px; width: 20px;padding-left: 5px; padding-top: 1px;">@php if(isset($nachDetail['debit_tick']) && ($nachDetail['debit_tick'] == 4)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                                <td style="padding-left: 5px;">Other</td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table>
                                        <tr> 
                                            <td width="80" style="white-space: nowrap;">Bank a/c number </td>
                                            <td>
                                                <table cellspacing="0" cellpadding="0" style="border-collapse: separate;">
                                                    <tr>
                                                        <td style="border: 1px solid #ddd; height: 22px; width: 600px;padding-left: 5px;">{{isset($nachDetail['acc_no']) ? $nachDetail['acc_no'] : ''}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr> 
                                            <td width="30%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:80px;">
                                                            with Bank
                                                        </td>
                                                        <td style="border: 1px solid #ddd;padding-left: 5px;">
                                                            {{isset($nachDetail['user_bank']['bank']['bank_name']) ? $nachDetail['user_bank']['bank']['bank_name'] : ''}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="30%">
                                                <table>
                                                    <tr>
                                                        <td width="50">IFSC</td>
                                                        <td>
                                                            <table cellspacing="0" cellpadding="0" style="border-collapse: separate;">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 220px;padding-left: 5px;">{{isset($nachDetail['ifsc_code']) ? $nachDetail['ifsc_code'] : ''}}</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="40%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:80px;">
                                                           or MICR
                                                        </td>
                                                        <td style="border: 1px solid #ddd;padding-left: 5px;">
                                                            {{isset($nachDetail['micr']) ? $nachDetail['micr'] : ''}}&nbsp;
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr> 
                                            <td width="75%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:160px;">
                                                            an amount of Rupees 
                                                        </td>
                                                        <td style="border: 1px solid #ddd;">
                                                            {{isset($nachDetail['amount']) ? ucwords(\Helpers::numberToWord($nachDetail['amount'])) : ''}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="25%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:30px;">
                                                          <img src="{{url('backend/assets/images/Indian_Rupee_symbol.png')}}" height="10px" width="8px">
                                                        </td>
                                                        <td style="border: 1px solid #ddd;">
                                                            {{isset($nachDetail['amount']) ? $nachDetail['amount'] : ''}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr>
                                            <td style="width:150px;">Frequency</td><td>
                                                <table width="100%">
                                                    <tr>
                                                        <td style="border: 1px solid #fff;">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['frequency']) && ($nachDetail['frequency'] == 1)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;">Monthly</td>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['frequency']) && ($nachDetail['frequency'] == 2)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;">Qtly</td>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['frequency']) && ($nachDetail['frequency'] == 3)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;">H.Yrly </td>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['frequency']) && ($nachDetail['frequency'] == 4)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;">Yrly</td>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['frequency']) && ($nachDetail['frequency'] == 5)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;">As & when presented </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>

                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            
                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr>
                                            <td style="width:150px;">Debit Type</td>
                                            <td>
                                                <table width="100%">
                                                    <tr>
                                                        <td style="border: 1px solid #fff;">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['debit_type']) && ($nachDetail['debit_type'] == 1)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;width:100px;">Fixed Amount</td>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['debit_type']) && ($nachDetail['debit_type'] == 2)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                    <td style="padding-left: 5px;">Maximum Amount</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr> 
                                            <td width="60%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:160px;">
                                                            Reference 1  
                                                        </td>
                                                        <td style="border: 1px solid #ddd;padding-left: 5px;">
                                                            {{isset($nachDetail['reference_1']) ? $nachDetail['reference_1'] : ''}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="40%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:80px;">
                                                           Phone No.
                                                        </td>
                                                        <td style="border: 1px solid #ddd; padding-left: 5px;">{{isset($nachDetail['phone_no']) ? $nachDetail['phone_no'] : ''}}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr> 
                                            <td width="60%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:160px;">
                                                            Reference 2  
                                                        </td>
                                                        <td style="border: 1px solid #ddd;padding-left: 5px;">
                                                            {{isset($nachDetail['reference_2']) ? $nachDetail['reference_2'] : ''}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="40%">
                                                <table width="100%">
                                                    <tr>
                                                        <td style="width:80px;">
                                                          Email ID
                                                        </td>
                                                        <td style="border: 1px solid #ddd;padding-left: 5px;">
                                                            {{isset($nachDetail['email_id']) ? $nachDetail['email_id'] : ''}}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 10px;line-height: 10px;">
I have understood that the bank, where I have authorised the debit, may levy onetime mandate processing charges as mentioned in their latest schedule of charges published by the bank.
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px;line-height:20px;"><b>PERIOD</b></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <table width="100%">
                                        <tr>
                                          <td width="30%">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="60">From</td>
                                                        <td>
                                                            <table cellspacing="0" cellpadding="0" style="border-collapse: separate;">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 150px; ">{{isset($nachDetail['period_from']) ? \Carbon\Carbon::parse($nachDetail['period_from'])->format('d/m/Y') : ''}}&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="60">To</td>
                                                        <td>
                                                            <table cellspacing="0" cellpadding="0" style="border-collapse: separate;">
                                                                <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 150px;">{{isset($nachDetail['period_to']) ? \Carbon\Carbon::parse($nachDetail['period_to'])->format('d/m/Y') : ''}}&nbsp;</td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="60">OR</td>
                                                        <td>
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                 <tr>
                                                                    <td style="border: 1px solid #ddd; height: 22px; width: 20px;">@php if(isset($nachDetail['period_until_cancelled']) && ($nachDetail['period_until_cancelled'] == 2)) {@endphp <img src="{{$tick}}" height="10px" width="8px"> @php } else {echo('&nbsp;'); }@endphp</td>
                                                                     <td style="padding-left: 5px;">Until Cancelled</td>
                                                              </tr>
                                                          </table>                                                           
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="70%" valign="top">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="20"></td>
                                                        <td style="border-bottom:1px solid #ddd;"></td>
                                                        <td width="20"></td>
                                                        <td style="border-bottom:1px solid #ddd;"></td>
                                                        <td width="20">&nbsp;</td>
                                                        <td style="border-bottom:1px solid #ddd;"></td>
                                                    </tr>
                                                </table>
                                                <table width="100%">
                                                    <tr>
                                                        <td width="20" valign="bottom">1</td>
                                                      <td style="border-bottom:1px solid #ddd;"></td>
                                                        <td width="20" valign="bottom">2</td>
                                                      <td style="border-bottom:1px solid #ddd;"></td>
                                                        <td width="20" valign="bottom">3</td>
                                                        <td style="border-bottom:1px solid #ddd;"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2" style="font-size: 10px;line-height: 10px;">This is to confirm that the declaration has been carefully read, understood and made by me/us.
                            </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 10px;line-height: 10px;">I am authorizing the User entity/Corporate to debit my account.
                            </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 10px;line-height: 10px;">I have understood that i am authorized to cancel/amend this mandate by appropriately communicating the cancellation/amendment request to the User entity/Corporate or the bank where i have authorized the debit.
                                </td>
                            </tr>


                        </table>
                    </td>
                </tr>
            </table>
      </td>
    </tr>
  </table>
