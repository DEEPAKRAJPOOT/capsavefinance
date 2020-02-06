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
            <div class="card">
                <div class="card-body">
                    <div class=" form-fields">
                        <div class="col-md-12">
                            <h5 class="card-title form-head-h5 text-center">Sanction Letter</h5>                            
                            <div class="col-md-12">

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
                                        <tr>
                                            <td>6.</td>
                                            <td>Equipment type</td>
                                            <td> @if($equipmentData)
                                                {{ $equipmentData->equipment_name }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>7.</td>
                                            <td>Lease Tenor</td>
                                            <td> 
                                                @if($offerData->tenor)
                                                    {{ $offerData->tenor }}
                                                    @if($product_id == 1)
                                                        @if($offerData->tenor>1)Days @else Day @endif 
                                                    @else
                                                        @if($offerData->tenor>1)Months @else Month @endif 
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>8.</td>
                                            <td>Rental Rate – @switch ($offerData->rental_frequency)
                                                @case(4) PTPM  @break
                                                @case(3) PTPQ  @break
                                                @case(2) PTPBi-Y  @break
                                                @case(1) PTPY  @break
                                            @endswitch </td>
                                            <td>
                                                @if($ptpqrData->count())
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>From Period</td>
                                                            <td>To Period</td>
                                                            <td>Rate</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($ptpqrData as $ptpqr)
                                                        <tr>
                                                            <td>{{ $ptpqr->ptpq_from }}</td>
                                                            <td>{{ $ptpqr->ptpq_to }}</td>
                                                            <td>{{ $ptpqr->ptpq_rate }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>9.</td>
                                            <td>Refundable Security Deposit</td>
                                            <td>
                                                @if($offerData->security_deposit_type == 1 &&  $offerData->security_deposit >0)
                                                    Flat {{ $offerData->security_deposit }} of the {{ $security_deposit_of }}
                                                @elseif($offerData->security_deposit_type == 2 &&  $offerData->security_deposit >0)
                                                    {{ $offerData->security_deposit }} % of the {{ $security_deposit_of }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>10.</td>
                                            <td>Processing Fees</td>
                                            <td>{!! $offerData->processing_fee ? $offerData->processing_fee. ' %' : '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>11.</td>
                                            <td>Security</td>
                                            <td>
                                                @switch($offerData->addl_security)
                                                    @case(1)
                                                        BG
                                                        @break
                                                    @case(2)
                                                        MF
                                                        @break
                                                    @case(3)
                                                        {{ $offerData->comment }}
                                                        @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>12.</td>
                                            <td>Rental payment frequency</td>
                                            <td>
                                                Rentals are due 
                                                @switch ($offerData->rental_frequency) 
                                                    @case(4) Monthly  @break
                                                    @case(3) Quaterly  @break
                                                    @case(2) Bi-Yearly  @break
                                                    @case(1) Yearly  @break
                                                @endswitch 
                                                in 
                                                @switch($offerData->rental_frequency_type)
                                                    @case(1) Advance @break
                                                    @case(2) Arrears @break
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>13.</td>
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
                                            <td>14.</td>
                                            <td>Delayed payment charges</td>
                                            <td>{!! $delay_pymt_chrg !!}</td>
                                        </tr>
                                        <tr>
                                            <td>15.</td>
                                            <td>Insurance</td>
                                            <td>{!! $insurance !!}</td>
                                        </tr>
                                        <tr>
                                            <td>16.</td>
                                            <td>GST/Bank Charges</td>
                                            <td>{!! $bank_chrg !!}</td>
                                        </tr>
                                        <tr>
                                            <td>17.</td>
                                            <td>Legal Costs</td>
                                            <td>{!! $legal_cost !!}</td>
                                        </tr>
                                        <tr>
                                            <td>18.</td>
                                            <td>Purchase Orders</td>
                                            <td>{!! $po !!}</td>
                                        </tr>
                                        <tr>
                                            <td>19.</td>
                                            <td>Pre-disbursement conditions</td>
                                            <td>{!! $pdp !!}</td>
                                        </tr>
                                        <tr>
                                            <td>20.</td>
                                            <td>Disbursement Guidelines/Documentation</td>
                                            <td>{!! $disburs_guide !!}</td>
                                        </tr>
                                        <tr>
                                            <td>21.</td>
                                            <td>Other Conditions </td>
                                            <td>{!! $other_cond !!}</td>
                                        </tr>
                                        <tr>
                                            <td>22.</td>
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
            </div>
        </div>
    </div>
</div>
@endsection