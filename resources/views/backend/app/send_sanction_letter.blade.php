<style>
    @page {
        size: A4 portrait;
        margin: 0;
    }
</style>
@extends('layouts.backend.admin_popup_layout')
@section('content')
<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">

                    <div class=" form-fields">
                      
                            <h5 class="card-title form-head-h5 text-center">Sanction Letter</h5>                            
                            

                            <p>Ref No: CFPL/{{$date->isoFormat('MMMYY') }}/{{$sanctionData->sanction_id}} <br><br>
                                    {{ $date->isoFormat('MMMM D, Y') }}<br><br>
                                <b>{{ $biz_entity_name }},<br>
                                    {{ $businessAddress->addr_1 }}<br>
                                    {{ $businessAddress->addr_2 }}<br>
                                    {{ $businessAddress->city_name }}
                                    @if($businessAddress->state)
                                    {{ $businessAddress->state->name }}
                                    @endif
                                    
                                    @if( $businessAddress->pin_code) - {{ $businessAddress->pin_code }} @endif <br><br>
                                Kind Attention:{{ $contact_person }}<br><br>
                                Sub: Sanction Letter for {{ $biz_entity_name }}</b><br><br>
                                Dear Sir, <br><br>
                                Capsave Finance Private Limited is pleased to offer you rental facility subject to the following terms:</p> 
                                <table class="table table-bordered overview-table">
                                    <tbody>
                                        <tr>
                                            <td>1.</td>
                                            <td>Nature of facility</td>
                                            <td>Rental Facility </td>
                                        </tr>
                                        <tr>
                                            <td>2.</td>
                                            <td>Lessor</td>
                                            <td>Capsave Finance Private Limited (CFPL)</td>
                                        </tr>
                                        <tr>
                                            <td>3.</td>
                                            <td>Lessee</td>
                                            <td>{{ $biz_entity_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>4.</td>
                                            <td>Sanction Amount</td>
                                            <td>{!! $offerData->prgm_limit_amt ? \Helpers::formatCurreny($offerData->prgm_limit_amt) : '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>5.</td>
                                            <td>Sanction validity</td>
                                            <td>{{ \Carbon\Carbon::parse($sanctionData->validity_date)->format('d/m/Y')}}</td>
                                        </tr>
                                    </tbody>
                                </table>        




                                <table class="table overview-table" cellpadding="0" cellspacing="0" border="1">
                                        <thead>
                                        <tr>
                                            <td  style="background: #e9ecef;"><b>Facility Type</b></td>
                                            <td  style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Equipment Type</b></td>
                                            <td  style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Limit of the Equipment</b></td>
                                            <td  style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Tenor (Months)</b></td>
                                            <td  style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>PTP Frequency</b></td>
                                            <td  style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>    XIRR/Discounting(%)</b></td>
                                            <td  style="background: #e9ecef; border-left: 1px solid #c6cfd8;"><b>Processing Fee (%)</b></td>
                                            
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($leaseOfferData as $key=>$leaseOffer)
                                        
                                                <tr>
                                                    <td>{{isset($leaseOffer->facility_type_id) ?  $facilityTypeList[$leaseOffer->facility_type_id]  : ''}}</td>
                                                    <td>{{isset($leaseOffer->equipment_type_id) ?  (\Helpers::getEquipmentTypeById($leaseOffer->equipment_type_id)['equipment_name']) : ''}}</td>
                                                    <td>{!! isset($leaseOffer->prgm_limit_amt) ? ' INR '.number_format($leaseOffer->prgm_limit_amt)  : '0' !!}</td>
                                                    <td>{{isset($leaseOffer->tenor) ? $leaseOffer->tenor : ''}}</td>
                                                    <td>
                                                        @php 
                                                            $i = 1;
                                                            if(!empty($leaseOffer->offerPtpq)){
                                                            $total = count($leaseOffer->offerPtpq);
                                                         @endphp   
                                                            @foreach($leaseOffer->offerPtpq as $key => $arr) 

                                                                  @if ($i > 1 && $i < $total)
                                                                  ,
                                                                  @elseif ($i > 1 && $i == $total)
                                                                     and
                                                                  @endif
                                                                  {!!  'INR' !!} {{$arr->ptpq_rate}}  for  {{floor($arr->ptpq_from)}}- {{floor($arr->ptpq_to)}} {{$arrStaticData['rentalFrequencyForPTPQ'][$leaseOffer->rental_frequency]}}

                                                                  @php 
                                                                     $i++;
                                                                  @endphp     
                                                            @endforeach
                                                            @php 
                                                               }
                                                            @endphp 

                                                    </td>
                                                    <td>
                                                        @if($leaseOffer->facility_type_id == 3)
                                                             {{$leaseOffer->discounting}}%
                                                          @else
                                                             <b>Ruby Sheet</b>: {{$leaseOffer->ruby_sheet_xirr}}%<br/><b>Cash Flow</b>: {{$leaseOffer->cash_flow_xirr}}%
                                                          @endif

                                                    </td>
                                                    <td>{{isset($leaseOffer->processing_fee) ? $leaseOffer->processing_fee.' %': ''}}</td>
                                                </tr>

                                              @empty
                                                 <tr>

                                                     <p>No Offer Found</p>
                                                 </div>
                                           @endforelse  
                                            
                                        </tbody>
                                </table>



                                <table class="table table-bordered overview-table">
                                    <tbody>
                                        <tr>
                                            <td>1.</td>
                                            <td>Payment mechanism</td>
                                            <td>
                                                @switch($sanctionData->payment_type)
                                                    @case(1) NACH @break
                                                    @case(2) RTGS @break
                                                    @case(3) NEFT @break
                                                    @case(4) Advance Cheque @break
                                                    @case(5) Other: {{$sanctionData->payment_type_other}} @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>2.</td>
                                            <td>Delayed payment charges</td>
                                            <td>{!! $delay_pymt_chrg !!}</td>
                                        </tr>
                                        <tr>
                                            <td>3.</td>
                                            <td>Insurance</td>
                                            <td>{!! $insurance !!}</td>
                                        </tr>
                                        <tr>
                                            <td>4.</td>
                                            <td>GST/Bank Charges</td>
                                            <td>{!! $bank_chrg !!}</td>
                                        </tr>
                                        <tr>
                                            <td>5.</td>
                                            <td>Legal Costs</td>
                                            <td>{!! $legal_cost !!}</td>
                                        </tr>
                                        <tr>
                                            <td>6.</td>
                                            <td>Purchase Orders</td>
                                            <td>{!! $po !!}</td>
                                        </tr>
                                        <tr>
                                            <td>7.</td>
                                            <td>Pre-disbursement conditions</td>
                                            <td>{!! $pdp !!}</td>
                                        </tr>
                                        <tr>
                                            <td>8.</td>
                                            <td>Disbursement Guidelines/Documentation</td>
                                            <td>{!! $disburs_guide !!}</td>
                                        </tr>
                                        <tr>
                                            <td>9.</td>
                                            <td>Other Conditions </td>
                                            <td>{!! $other_cond !!}</td>
                                        </tr>
                                        <tr>
                                            <td>10.</td>
                                            <td>Information and other covenants</td>
                                            <td>{!! $covenants !!}</td>
                                        </tr>
                                    </tbody>
                                </table><br>
                                <p>I /We accept all the terms and conditions which have been read and understood by me/us.<br>
                                   We request you to acknowledge and return a copy of the same as a confirmation.<br><br>
                                <b>
                                   Yours Sincerely,<br>
                                   For Capsave Finance Private Limited<br>
                                {{-- <p style="page-break-before: always"> --}}
                                  Authorized Signatory<br>
                                  <hr>
                                  @if($biz_entity_name)
                                  Accepted for and behalf of<br>
                                  For {{ $biz_entity_name }}<br>
                                  Authorized Signatory
                                    @endif
                                </b></p>
                           
                   
            </div>
        </div>
    </div>
</div>
@endsection