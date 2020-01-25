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
                            @if($sanctionData)
                          {{--
                            <a data-toggle="modal" data-target="#uploadSanctionLetter" data-height="200px" data-width="100%" data-placement="top" href="#" data-url="{{ route('show_upload_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'upload'=>1 ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Upload</a>
                                
                            <a href="{{ route('send_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'download'=>1, 'sanction_id'=>$sanction_id ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Download</a>  --}}                         
                                
                                <a data-toggle="modal" data-target="#previewSanctionLetter" data-height="500px" data-width="100%" data-placement="top" href="#" data-url="{{ route('preview_sanction_letter', ['app_id' => $appId, 'biz_id' => $bizId, 'offer_id' => $offerId, 'upload'=>1, 'sanction_id'=>$sanction_id ]) }}" class="btn btn-success btn-sm float-right mt-3 ml-3">Preview/Send Mail</a>
                            @endif
                            </h5> 
                            <div class="col-md-12">
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
                                                <td with="25%"><b>Nature of Facility</b></td>
                                                <td with="25%">Rental Facility </td>
                                                <td with="25%"><b>Lessor</b></td>
                                                <td with="25%">Capsave Finance Private Limited (CFPL)</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Lessee</b></td>
                                                <td with="25%">{{ $biz_entity_name }}</td>
                                                <td with="25%"><b>Sanction Amount</b></td>
                                                <td with="25%"> {!! $offerData->prgm_limit_amt ? \Helpers::formatCurreny($offerData->prgm_limit_amt) : '' !!}</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Sanction Validity</b></td>
                                                <td with="25%" colspan="3">
                                                    <div class="row">
                                                        <div class="col">
                                                            <input type="text" name="sanction_validity_date" value="{{old('sanction_validity_date', \Carbon\Carbon::parse($validity_date)->format('d/m/Y'))}}" class="form-control datepicker-dis-pdate" tabindex="5" placeholder="Enter Validity Date" autocomplete="off" readonly >
                                                        </div>
                                                        <div class="col">
                                                        <input type="text" class="form-control" placeholder="Enter Comment" name="sanction_validity_comment" value="{{ $validity_comment }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Equipment Type</b></td>
                                                <td with="25%" colspan="3">{{ $equipmentData->equipment_name }}</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Lease Tenor</b></td>
                                                <td with="25%">
                                                    @if($offerData->tenor)
                                                        {{ $offerData->tenor }}
                                                        @if($product_id == 1)
                                                            @if($offerData->tenor>1)Days @else Day @endif 
                                                        @else
                                                            @if($offerData->tenor>1)Months @else Month @endif 
                                                        @endif
                                                    @endif
                                                </td>
                                                <td with="25%"><b>Rental Rate – Per Thousand Per 
                                                @switch ($offerData->rental_frequency) 
                                                    @case(4) Monthly  @break
                                                    @case(3) Quaterly  @break
                                                    @case(2) Bi-Yearly  @break
                                                    @case(1) Yearly  @break
                                                @endswitch </b></td>
                                                <td with="25%">
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
                                                <td with="25%"><b>Refundable Security Deposit</b></td>
                                                <td with="25%">
                                                    @if($offerData->security_deposit_type == 1 &&  $offerData->security_deposit >0)
                                                        Flat {{ $offerData->security_deposit }} of the {{ $security_deposit_of }}
                                                    @elseif($offerData->security_deposit_type == 2 &&  $offerData->security_deposit >0)
                                                        {{ $offerData->security_deposit }} % of the {{ $security_deposit_of }}
                                                    @endif
                                                </td>
                                                <td with="25%"><b>Processing Fees</b></td>
                                                <td with="25%">{!! $offerData->processing_fee ? \Helpers::formatCurreny($offerData->processing_fee) : '' !!}</td>
                                            </tr>
                                            <tr>
                                                <td with="25%"><b>Security</b></td>
                                                <td with="25%">
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
                                                <td with="25%"><b>Rental Payment Frequency</b></td>
                                                <td with="25%">
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
                                                <td with="25%"><b>Payment Mechanism</b></td>
                                                <td colspan="3">
                                                    <div class="row">
                                                        <div class="col">
                                                            <select class="form-control" id="payment_type" name="payment_type">
                                                                <option value="">Choose...</option>
                                                                <option @if($payment_type == '1')selected @endif value="1">NACH</option>
                                                                <option @if($payment_type == '2')selected @endif value="2">RTGS</option>
                                                                <option @if($payment_type == '3')selected @endif value="3">NEFT</option>
                                                                <option @if($payment_type == '4')selected @endif value="4">Advance Cheque</option>
                                                                <option @if($payment_type == '5')selected @endif value="5">Other Specify</option>
                                                            </select>
                                                        </div>
                                                        <div class="col">
                                                        <input type="text" class="form-control @if($payment_type != '5') hide @endif" name="payment_type_comment" id="payment_type_comment" placeholder="Enter Payment Mechanism" value="{{ $payment_type_other }}">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Delayed payment charges</b></td>
                                                <td  colspan="3">
                                                    <textarea class="form-control textarea" name="delay_pymt_chrg" id="delay_pymt_chrg" cols="30" rows="10">@if($delay_pymt_chrg){!! $delay_pymt_chrg !!} @else Any delay in the payment of the rentals shall attract overdue interest on the rentals due but unpaid at the overdue rate mentioned in the Master Rental Agreement.@endif</textarea>
                                                </td>
                                            </tr>    
                                            <tr>
                                                <td><b>Insurance</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="insurance" id="insurance" cols="30" rows="10">@if($insurance) {!! $insurance !!} @else The equipment is required to be insured throughout the period of the rental for its full insurable value against physical loss and damage. Lessee may elect to: Insure the equipment through their own insurance company or request CFPL to insure the equipment. Should the lessee elect CFPL to insure the equipment, the lessor shall advise on the cost once full equipment details are known. Insurance policy of the assets under lease endorsed in favour of Capsave Finance Pvt Ltd within 30 days of disbursement of each tranche.@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>   
                                                <td><b>GST/Bank Charges</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="bank_chrg" id="bank_chrg" cols="30" rows="10">@if($bank_chrg) {!! $bank_chrg !!}@else Extra as applicable. It is not included in the above rental rates and would be for the account of the Lessee. Bank charges include LC and remittance charges.@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Legal Costs</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="legal_cost" id="legal_cost" cols="30" rows="10">@if($legal_cost) {!! $legal_cost !!} @else Any legal costs will be for the account of the Lessee. @endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Purchase Orders</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="po" id="po" cols="30" rows="10">@if($po) {!! $po !!} @else Purchase orders shall not be raised by the Lessee on a vendor without our prior written approval and signed by an authorized person of CFPL. Any purchase order raised by CFPL shall be raised on the express understanding and agreement that it is being raised by CFPL as agent for you until such time as CFPL agrees to accept the rental order and or the invoice. CFPL shall not be responsible to pay any vendor any amount until such time that it agrees to do the aforementioned.@endif </textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Pre-disbursement conditions</b></td>
                                                <td colspan="3">
                                                    <textarea class="form-control textarea" name="pdp" id="pdp" cols="30" rows="10">@if($pdp) {!! $pdp !!} @else<b>One-time requirement:</b><br><ol><li>&nbsp;Accepted Sanction Letter<br></li><li>Signed MRA&nbsp;</li><li>Self-Attested KYC of client and Vendors (True Copy)&nbsp;</li><ul><li>&nbsp;Certificate of incorporation, MOA, AOA&nbsp;</li><li>&nbsp;Address Proof (Not older than 90 days)&nbsp;</li><li>&nbsp;PAN Card&nbsp;</li><li>&nbsp;GST registration letter&nbsp;</li></ul><li>Board Resolution signed by 2 directors or Company Secretary. or Power of Attorney in favour of company officials to execute such agreements or documents. BR should be dated before the MRA date.&nbsp;</li><li>Bank Guarantee or Lien on Fixed deposit of 70% of the invoice value.&nbsp;</li><li>Unconditional and irrevocable bank guarantee should be in CFPL approved format.&nbsp;</li><li>Bank guarantee should be valid for at least a quarter more than the expiry of the lease tenure.&nbsp;</li><li>KYC of authorized signatory:&nbsp;</li><ul><li>&nbsp;Name of authorized signatories with their Self Attested ID proof and address proof&nbsp;</li><li>&nbsp;Signature Verification of authorized signatories from company's banker&nbsp;</li></ul><li>CIN (company identification number)</li></ol>@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Disbursement Guidelines/Documentation</b></td>
                                                <td colspan="3"> 
                                                <textarea class="form-control textarea" name="disburs_guide" id="disburs_guide" cols="30" rows="10">@if($disburs_guide){!! $disburs_guide !!} @else <b>With every tranche:</b><br><ol><li>Original Invoices, Delivery challans, lorry receipt, installation report&nbsp;</li><li>In case of import transactions, Packing list, Bill of Entry for home consumption in the joint name, Airway bill/Bill of Lading, Rewarehousing Certificate, PE certificate, TRC copy&nbsp;</li><li>Signed Rental Schedule&nbsp;</li><li>NACH form&nbsp;</li><li>All documents as mentioned in Annexure-1 </li></ol> @endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Other Conditions</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="other_cond" id="other_cond" cols="30" rows="10">@if($other_cond){!! $other_cond !!}@else<ul><li>Rentals will be calculated on total invoice value.</li><li>First rental shall commence from date of invoice or date of payment to the vendor whichever is earlier.&nbsp;</li><li>Any financial or operational covenants stipulated by CFPL from time to time.&nbsp;</li><li>All the other conditions stipulated in MRA/Rental Schedule will remain applicable at all times </li></ul>@endif</textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><b>Information and other covenants</b></td>
                                                <td colspan="3"> 
                                                    <textarea class="form-control textarea" name="covenants" id="covenants" cols="30" rows="10">@if($covenants) {!! $covenants !!}@else <ul><li>The Lessee hereby agrees &amp; gives consent for the disclosures by the Lender/Lessor of all or any such:&nbsp;</li><ol><li>&#65279;Information &amp; data relating to the lessee.&nbsp;</li><li>Information and data relating to any credit or lease facility availed/to be availed by the lessee and data relating to their obligations as lessee/guarantor.&nbsp;</li><li>Obligations assumed/to be assumed by the lessee in relation to the lease facility(ies).&nbsp;</li><li>In compliance with the regulatory requirements, CFPL shall disclose and furnish details&nbsp;of the transaction including defaults, if any, committed by Lessee in discharge of their obligations hereunder or under any Transaction Documents, to Credit Information Bureau Limited (“CIBIL”) or any other agency as authorized by Reserve Bank of India (“RBI”).&nbsp;</li></ol><li>The lessee declares that the information and data furnished by the lessee to the lender/lessor are true and correct.&nbsp;</li></ul>The Lessee undertakes that CIBIL or any other agency so authorized may use/process the said information and data disclosed by the lessee in the manner as may be deemed fit by them. CIBIL or any other agency so authorized may furnish for consideration the processed information, data, and products thereof prepared by them to banks, financial institutions, or credit granters or registered users as may be specified by RBI in this behalf.@endif</textarea>
                                                </td>
                                            </tr>                 
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="sanction_id" value="{{$sanction_id}}">
                                    <input type="hidden" name="app_id" value="{{$appId}}">
                                    <input type="hidden" name="offer_id" value="{{$offerId}}">
                                    <input type="hidden" name="biz_id" value="{{$bizId}}">
                                    <button type="submit" class="btn  btn-success btn-sm float-right">Submit</button>  
                                </form>
                            </div>
                        </div>	
                    </div>	 
                </div>
            </div>
        </div>
    </div>
</div>
{!!Helpers::makeIframePopup('previewSanctionLetter','Preview/Send Mail Sanction Letter', 'modal-lg')!!}
{!!Helpers::makeIframePopup('uploadSanctionLetter','Upload Sanction Letter', 'modal-md')!!}
@endsection
@section('jscript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/wysihtml5/0.3.0/wysihtml5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.2/handlebars.runtime.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-wysiwyg/0.3.3/bootstrap3-wysihtml5.all.min.js"></script>
<script>
    var messages = {
        get_applications: "{{ URL::route('ajax_app_list') }}",
        data_not_found: "{{ trans('error_messages.data_not_found') }}",
        token: "{{ csrf_token() }}",

    };
    $(document).ready(function(){ 
        $('.textarea').wysihtml5({
            toolbar: {
            fa: true
            }
        });
        $('#payment_type').on('change', function(){
            $('#payment_type_comment').val('');
            if($(this).val()  == '5'){
                $('#payment_type_comment').removeClass('hide');
            }else{
                $('#payment_type_comment').addClass('hide');
            }
        })
    });

</script>
@endsection