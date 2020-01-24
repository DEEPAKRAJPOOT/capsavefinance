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

                                @php
                                $Lessee = \Helpers::customIsset($userData, 'biz_name');
                                $sanctionAmount = \Helpers::customIsset($offerData, 'prgm_limit_amt');
                                $sanctionValidity = \Helpers::customIsset($offerData, 'tenor');

                                $delay_pymt_chrg = \Helpers::customIsset($sanctionData,'delay_pymt_chrg');
                                $insurance = \Helpers::customIsset($sanctionData,'insurance');
                                $bank_chrg = \Helpers::customIsset($sanctionData,'bank_chrg');
                                $legal_cost = \Helpers::customIsset($sanctionData,'legal_cost');
                                $po = \Helpers::customIsset($sanctionData,'po');
                                $pdp = \Helpers::customIsset($sanctionData,'pdp');
                                $disburs_guide = \Helpers::customIsset($sanctionData,'disburs_guide');
                                $other_cond = \Helpers::customIsset($sanctionData,'other_cond');
                                $covenants = \Helpers::customIsset($sanctionData,'covenants');
                               
                                $loanAmount = \Helpers::customIsset($offerData, 'loan_amount');
                                $loan_offer = \Helpers::customIsset($offerData, 'loan_offer');
                                $interest_rate = \Helpers::customIsset($offerData, 'interest_rate');
                                $tenor = \Helpers::customIsset($offerData, 'tenor');
                                $tenor_old_invoice = \Helpers::customIsset($offerData, 'tenor_old_invoice');
                                $margin = \Helpers::customIsset($offerData, 'margin');
                                $overdue_interest_rate = \Helpers::customIsset($offerData, 'overdue_interest_rate');
                                $adhoc_interest_rate = \Helpers::customIsset($offerData, 'adhoc_interest_rate');                                
                                $grace_period = \Helpers::customIsset($offerData, 'grace_period');
                                $processing_fee = \Helpers::customIsset($offerData, 'processing_fee');
                                $check_bounce_fee = \Helpers::customIsset($offerData, 'check_bounce_fee');
                                $comment = \Helpers::customIsset($offerData, 'comment');
                                @endphp



                                <p>Ref No: CFPL/Apr19/198 <br><br>
                                January 23, 2020<br><br>
                                <b>{{ $Lessee }},<br>
                                Warehouse no 1, 2nd floor, The Integrated Park, 
                                Np, Village Kurund, Bhiwandi, Mumbai, 
                                Maharashtra - 421101 <br><br>
                                Kind Attention: Mr. Madhusudan Bihani<br><br>
                                Sub: Sanction Letter for {{ $Lessee }}</b><br><br>
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
                                            <td>{{ $Lessee }}</td>
                                        </tr>
                                        <tr>
                                            <td>4.</td>
                                            <td>Sanction Amount</td>
                                            <td>{!! $sanctionAmount ? \Helpers::formatCurreny($sanctionAmount) : '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td>5.</td>
                                            <td>Sanction validity</td>
                                            <td>{{ $sanctionValidity }}</td>
                                        </tr>
                                        <tr>
                                            <td>6.</td>
                                            <td>Equipment type</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>7.</td>
                                            <td>Lease Tenor</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>8.</td>
                                            <td>Rental Rate – Per Thousand Per Quarter </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>9.</td>
                                            <td>Refundable Security Deposit</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>10.</td>
                                            <td>Processing Fees</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>11.</td>
                                            <td>Security</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>12.</td>
                                            <td>Rental payment frequency</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>13.</td>
                                            <td>Payment mechanism</td>
                                            <td></td>
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
                                  Accepted for and behalf of<br>
                                  For Pepcart Logistics Pvt Ltd<br>
                                  Authorized Signatory</b></p>
                            </div>
                        </div>	
                    </div>	 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection