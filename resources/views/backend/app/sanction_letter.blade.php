@extends('layouts.backend.admin-layout')
@section('additional_css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysiwyg/0.3.3/bootstrap3-wysihtml5.min.css" />

@endsection
@section('content')
@include('layouts.backend.partials.admin-subnav')
<div class="content-wrapper">
    <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class=" form-fields">
                        <div class="col-md-12">
                            <h5 class="card-title form-head-h5">Sanction Letter
                            {{-- @if(request()->get('view_only')) --}}
                                <a data-toggle="modal" data-target="#uploadSanctionLetter" data-height="200px" data-width="100%" data-placement="top" href="#" data-url="{{ route('show_upload_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'upload'=>1 ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Upload</a>    
                                <a href="{{ route('download_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'download'=>1, 'sanction_id'=>$sanctionId ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Download</a>                            
                            {{-- @endif --}}
                            </h5> 
                            <div class="col-md-12">

                                @php

                                $Lessee = \Helpers::customIsset($userData, 'biz_name');
                                $sanctionAmount = \Helpers::customIsset($offerData, 'prgm_limit_amt');
                                $sanctionValidity = \Helpers::customIsset($offerData, 'tenor');

                                $loanAmount = \Helpers::customIsset($offerData, 'loan_amount');
                                $loan_offer = \Helpers::customIsset($offerData, 'loan_offer');
                                $interest_rate = \Helpers::customIsset($offerData, 'interest_rate');
                                $tenor_old_invoice = \Helpers::customIsset($offerData, 'tenor_old_invoice');
                                $margin = \Helpers::customIsset($offerData, 'margin');
                                $overdue_interest_rate = \Helpers::customIsset($offerData, 'overdue_interest_rate');
                                $adhoc_interest_rate = \Helpers::customIsset($offerData, 'adhoc_interest_rate');         
                                $grace_period = \Helpers::customIsset($offerData, 'grace_period');
                                $processing_fee = \Helpers::customIsset($offerData, 'processing_fee');
                                $check_bounce_fee = \Helpers::customIsset($offerData, 'check_bounce_fee');
                                $comment = \Helpers::customIsset($offerData, 'comment');
                                @endphp

                                {{--
                                <table class="table-striped table">
                                    <tbody><tr>
                                            <td><b>Apply Loan Amount :</b></td>
                                            <td>{!! $loanAmount ? \Helpers::formatCurreny($loanAmount) : '' !!}</td>
                                        </tr>
                                        <tr>
                                            <td><b>Loan Offer :</b></td>
                                            <td>{{ $loan_offer }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Interest Rate (%) :</b></td>
                                            <td>{{ $interest_rate }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Tenor (Days) :</b></td>
                                            <td>{{ $tenor }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Tenor for old invoice (Days) :</b></td>
                                            <td>{{ $tenor_old_invoice }}</td>
                                        </tr> 

                                        <tr>
                                            <td><b>Margin (%) :</b></td>
                                            <td>{{ $margin }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Overdue Interest Rate (%) :</b></td>
                                            <td>{{ $overdue_interest_rate }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Adhoc Interest Rate (%) :</b></td>
                                            <td>{{ $adhoc_interest_rate }}</td>
                                        </tr> 

                                        <tr>
                                            <td><b>Grace Period  (Days) :</b></td>
                                            <td>{{ $grace_period }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Processing Fee :</b></td>
                                            <td>{{ $processing_fee }}</td>
                                        </tr>  

                                        <tr>
                                            <td><b>Check Bounce Fee :</b></td>
                                            <td>{{ $check_bounce_fee }}</td>
                                        </tr>

                                        <tr>
                                            <td><b>Comment :</b></td>
                                            <td>{{ $comment }}</td>
                                        </tr>                                        

                                    </tbody>
                                </table>--}}
                              
                                <form action="{{route('save_sanction_letter')}}" method="POST">
                                    @csrf
                                    <table class="table table-bordered overview-table">
                                        <tbody>
                                            <tr>
                                                <td with="25%"><b>Nature of facility</b></td>
                                                <td with="25%">Rental Facility </td>
                                                <td with="25%"><b>Lessor</b></td>
                                                <td with="25%">Capsave Finance Private Limited (CFPL)</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Lessee</b></td>
                                                <td with="25%">{{ $Lessee }}</td>
                                                <td with="25%"><b>Sanction Amount</b></td>
                                                <td with="25%">{!! $sanctionAmount ? \Helpers::formatCurreny($sanctionAmount) : '' !!}</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Sanction validity</b></td>
                                                <td with="25%">{{ $sanctionValidity }}</td>
                                                <td with="25%"><b>Equipment type</b></td>
                                                <td with="25%"></td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Lease Tenor</b></td>
                                                <td with="25%"></td>
                                                <td with="25%"><b>Rental Rate – Per Thousand Per Quarter</b></td>
                                                <td with="25%"></td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Refundable Security Deposit</b></td>
                                                <td with="25%"></td>
                                                <td with="25%"><b>Processing Fees</b></td>
                                                <td with="25%"></td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Security</b></td>
                                                <td with="25%"></td>
                                                <td with="25%"><b>Rental payment frequency</b></td>
                                                <td with="25%"></td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Payment mechanism</b></td>
                                                <td colspan="3"></td>
                                            </tr>
                                            <tr>
                                                <td><b>Delayed payment charges</b></td>
                                                <td  colspan="3">
                                                    <textarea class="form-control textarea" name="delay_pymt_chrg" id="delay_pymt_chrg" cols="30" rows="10">Any delay in the payment of the rentals shall attract overdue interest on the rentals due but unpaid at the overdue rate mentioned in the Master Rental Agreement.</textarea>
                                                </td>
                                            </tr>    
                                            <tr>
                                                <td><b>Insurance</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="insurance" id="insurance" cols="30" rows="10">The equipment is required to be insured throughout the period of the rental for its full insurable value against physical loss and damage. Lessee may elect to: Insure the equipment through their own insurance company or request CFPL to insure the equipment. Should the lessee elect CFPL to insure the equipment, the lessor shall advise on the cost once full equipment details are known. Insurance policy of the assets under lease endorsed in favour of Capsave Finance Pvt Ltd within 30 days of disbursement of each tranche.</textarea>
                                                </td>
                                            </tr>
                                            <tr>   
                                                <td><b>GST/Bank Charges</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="bank_chrg" id="bank_chrg" cols="30" rows="10">Extra as applicable. It is not included in the above rental rates and would be for the account of the Lessee. Bank charges include LC and remittance charges.</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Legal Costs</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="legal_cost" id="legal_cost" cols="30" rows="10">Any legal costs will be for the account of the Lessee.</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Purchase Orders</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="po" id="po" cols="30" rows="10">Purchase orders shall not be raised by the Lessee on a vendor without our prior written approval and signed by an authorized person of CFPL. Any purchase order raised by CFPL shall be raised on the express understanding and agreement that it is being raised by CFPL as agent for you until such time as CFPL agrees to accept the rental order and or the invoice. CFPL shall not be responsible to pay any vendor any amount until such time that it agrees to do the aforementioned.</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Pre-disbursement conditions</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="pdp" id="pdp" cols="30" rows="10"><b>One-time requirement:</b><br><ol><li>&nbsp;Accepted Sanction Letter<br></li><li>Signed MRA&nbsp;</li><li>Self-Attested KYC of client and Vendors (True Copy)&nbsp;</li><ul><li>&nbsp;Certificate of incorporation, MOA, AOA&nbsp;</li><li>&nbsp;Address Proof (Not older than 90 days)&nbsp;</li><li>&nbsp;PAN Card&nbsp;</li><li>&nbsp;GST registration letter&nbsp;</li></ul><li>Board Resolution signed by 2 directors or Company Secretary. or Power of Attorney in favour of company officials to execute such agreements or documents. BR should be dated before the MRA date.&nbsp;</li><li>Bank Guarantee or Lien on Fixed deposit of 70% of the invoice value.&nbsp;</li><li>Unconditional and irrevocable bank guarantee should be in CFPL approved format.&nbsp;</li><li>Bank guarantee should be valid for at least a quarter more than the expiry of the lease tenure.&nbsp;</li><li>KYC of authorized signatory:&nbsp;</li><ul><li>&nbsp;Name of authorized signatories with their Self Attested ID proof and address proof&nbsp;</li><li>&nbsp;Signature Verification of authorized signatories from company's banker&nbsp;</li></ul><li>CIN (company identification number)</li></ol></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Disbursement Guidelines/Documentation</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="disburs_guide" id="disburs_guide" cols="30" rows="10"><b>With every tranche:</b><br><ol><li>Original Invoices, Delivery challans, lorry receipt, installation report&nbsp;</li><li>In case of import transactions, Packing list, Bill of Entry for home consumption in the joint name, Airway bill/Bill of Lading, Rewarehousing Certificate, PE certificate, TRC copy&nbsp;</li><li>Signed Rental Schedule&nbsp;</li><li>NACH form&nbsp;</li><li>All documents as mentioned in Annexure-1 </li></ol></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Other Conditions</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="other_cond" id="other_cond" cols="30" rows="10"> <ul><li>Rentals will be calculated on total invoice value.</li><li>First rental shall commence from date of invoice or date of payment to the vendor whichever is earlier.&nbsp;</li><li>Any financial or operational covenants stipulated by CFPL from time to time.&nbsp;</li><li>All the other conditions stipulated in MRA/Rental Schedule will remain applicable at all times </li></ul></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Information and other covenants</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="covenants" id="covenants" cols="30" rows="10"> <ul><li>The Lessee hereby agrees &amp; gives consent for the disclosures by the Lender/Lessor of all or any such:&nbsp;</li><ol><li>&#65279;Information &amp; data relating to the lessee.&nbsp;</li><li>Information and data relating to any credit or lease facility availed/to be availed by the lessee and data relating to their obligations as lessee/guarantor.&nbsp;</li><li>Obligations assumed/to be assumed by the lessee in relation to the lease facility(ies).&nbsp;</li><li>In compliance with the regulatory requirements, CFPL shall disclose and furnish details&nbsp;of the transaction including defaults, if any, committed by Lessee in discharge of their obligations hereunder or under any Transaction Documents, to Credit Information Bureau Limited (“CIBIL”) or any other agency as authorized by Reserve Bank of India (“RBI”).&nbsp;</li></ol><li>The lessee declares that the information and data furnished by the lessee to the lender/lessor are true and correct.&nbsp;</li></ul>The Lessee undertakes that CIBIL or any other agency so authorized may use/process the said information and data disclosed by the lessee in the manner as may be deemed fit by them. CIBIL or any other agency so authorized may furnish for consideration the processed information, data, and products thereof prepared by them to banks, financial institutions, or credit granters or registered users as may be specified by RBI in this behalf.</textarea>
                                                </td>
                                            </tr>                 
                                        </tbody>
                                    </table>
                                    @if(!$sanctionId)
                                    <input type="hidden" name="app_id" value="{{$appId}}">
                                    <input type="hidden" name="offer_id" value="{{$offerId}}">
                                    <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
                                    @endif
                                </form>
                            </div>
                        </div>	
                    </div>	 
                </div>
            </div>
        </div>
    </div>
</div>

{!!Helpers::makeIframePopup('uploadSanctionLetter','Upload Sanction Letter', 'modal-md')!!}
@endsection
@section('jscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/wysihtml5/0.3.0/wysihtml5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.2/handlebars.runtime.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysiwyg/0.3.3/bootstrap3-wysihtml5.all.min.js"></script>
<script>
    $(document).ready(function(){ 
        $('.textarea').wysihtml5({
            toolbar: {
            fa: true
            }
        });
    });
</script>
@endsection