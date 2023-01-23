@extends('layouts.email')
@section('email_content')
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0" style="margin-top:10px; font-family:Calibri !important; font-size: 0.917rem; ">
    <thead>
       <tr role="row">
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Customer Id</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Virtual Account Id</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Invoice Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Invoice Approve Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Disbursal Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Total Interest</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Margin Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Actual Invoice Disbursed</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Principal Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Transaction Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Tally Amount</th>
            <th style="padding:8px 10px; font-family: Calibri !important; font-size: 13px;border-right:#ccc solid 1px;border-bottom: #ccc solid 1px;" align="left" >Result</th>
        </tr>
    </thead>
    <tbody>
        <tr role="row">
        </tr>
    </tbody>
</table>

@endsection