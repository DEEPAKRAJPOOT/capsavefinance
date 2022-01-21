@extends('layouts.backend.admin-layout')
@section('additional_css')
@endsection
@section('content')
@include('layouts.backend.partials.admin-subnav')
@php $actionText = 'View'; @endphp
@php $actionIcon = 'fa fa-eye'; @endphp
<div class="content-wrapper">
    <section class="content-header">
        <div class="header-icon">
            <i class="{{ $actionIcon }}"></i>
        </div>
        <div class="header-title">
            <h3>{{ $actionText }} Sanction Letter</h3>
            <small>{{ $actionText }} Sanction Letter</small>
            <ol class="breadcrumb">
                <li style="color:#374767;"> Home </li>
                <li style="color:#374767;">New Sanction Letter</li>
                <li class="active">{{ $actionText }} Sanction Letter</li>
            </ol>
        </div>
    </section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <a data-toggle="modal" data-target="#previewSupplyChainSanctionLetter" data-height="500px" data-width="100%" data-placement="top" href="#" data-url="http://admin.rent.local/application/preview_supply_chain_sanction_letter?__signature=7e300ee5-d6ad-4e9d-be10-25c00935721b" class="btn btn-success btn-sm float-right ml-3" style="margin: 0px 0 10px 0;">Preview/Send
                        Mail</a>
                    <div class=" form-fields">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify;">
                            <thead>
                                <tr>
                                </tr>
                                <th bgcolor="#cccccc" class="text-center" height="30"><span>SANCTION LETTER</span></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <span>Ref No: CFPL/Aug20/463<br />
                                            October 9th, 2020<br /><br />
                                            <b>Suumaya Lifestyle Limited</b><br />
                                            Gala No.5 F/D,<br />
                                            Malad Industrial Units, <br />
                                            Coop Soc Ltd, Kachpada, <br />
                                            Ramchandra Lane Extension, <br />
                                            Malad (W), Mumbai - 400064</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span><b>Kind Attention :</b> <input type="text" style="width:250px; height:30px;"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span><b>Subject :</b> Sanction Letter for Working Capital Demand Loan Facility
                                            to Suumaya Lifestyle Limited.</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>
                                            <b>Dear :</b>
                                            <select style="width:150px; height:30px;">
                                                <option>Sir</option>
                                                <option>Madam</option>
                                                <option>Sir/Madam</option>
                                            </select>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>We are pleased to inform you that Capsave Finance Private Limited has
                                            sanctioned the below mentioned credit
                                            facility, based upon the information furnished in the loan application form
                                            and supporting documents submitted to us.
                                            The credit facility is subject to acceptance of the terms and condition as
                                            set out in the attached annexures.</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="1">
                                            <tr>
                                                <td width="30%">Borrower</td>
                                                <td>Suumaya Lifestyle Limited (referred to as “Borrower” henceforth)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Lender</td>
                                                <td>Capsave Finance Private Limited (referred to as “Lender” henceforth)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Corporate Anchor</td>
                                                <td>Zetwerk Manufacturing Businesses Private Limited (referred to as
                                                    “Anchor” henceforth)</td>
                                            </tr>
                                            <tr>
                                                <td width="30%">Total Sanction Amount</td>
                                                <td>INR 65 Mn (Rupees Sixty-Five Million only)</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>I /We accept all the terms and conditions as per the attached annexures
                                            which have been read and understood by
                                            me/us. </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>We request you to acknowledge and return a copy of the same as a
                                            confirmation. </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0">
                                            <tr>
                                                <td width="50%" valign="top" height="40"><b>Yours Sincerely</b></td>
                                                <td valign="top" height="40"><b>Accepted for and behalf of Borrower</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="50%" valign="top" height="40"><b>For Capsave Finance Private
                                                        Limited</b></td>
                                                <td valign="top" height="40"><b>For Suumaya Lifestyle Limited</b></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="40">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0">
                                            <tr>
                                                <td width="50%" valign="top" height="40"><b>Authorized Signatory</b>
                                                </td>
                                                <td valign="top" height="40"><b>Authorized Signatory</b></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify; margin-top:25px;">
                            <thead>
                                <tr>
                                    <th bgcolor="#cccccc" class="text-center" height="30">Annexure I – Specific Terms
                                        and Conditions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b><br />FACILITY -1 </b></td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="1">
                                            <tr>
                                                <td width="30%" valign="top"><b>Facility</b></td>
                                                <td>Working Capital Demand Loan Facility (referred to as “Facility 1”
                                                    henceforth)</td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Sanction Amount</b></td>
                                                <td>INR 65 Mn (Rupees Sixty-Five Million only)</td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Facility Tenor</b></td>
                                                <td>
                                                    3 <input type="text" value=" months" style=" min-height:30px;padding:0 5px; ">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Purpose of the facility</b></td>
                                                <td><input type="text" value="Working Capital" style=" min-height:30px;padding:0 5px; margin-top:5px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Rate of Interest </b></td>
                                                <td>13.5% per annum reckoned from the date of disbursement until the
                                                    date on which repayment becomes due.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Tenor for each tranche</b></td>
                                                <td>Upto 30 days from date of disbursement of each tranche
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Grace Period</b></td>
                                                <td>NIL (in case grace period is nil in offer – not to capture in final
                                                    SL)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Old Invoice</b></td>
                                                <td>Borrower can submit invoices not older <input type="text" value="than" style=" min-height:30px;padding:0 5px; margin-top:5px;"> 30 days
                                                    (deviation upto 90 days for first disbursement)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Margin</b></td>
                                                <td>
                                                    30% on invoice
                                                    <select style="min-height:30px; padding:0 5px; min-width:180px;">
                                                        <option>Purchase Order</option>
                                                        <option>Invoice</option>
                                                        <option>Proforma Invoice</option>
                                                    </select>
                                                    (in case margin is nil in offer – not to capture in final SL)
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Interest frequency </b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%"><span style="margin-top:7px;display: inline-block;"><b>1.</b></span>
                                                            </td>
                                                            <td valign="top">
                                                                To be paid by
                                                                <select style="min-height:30px; padding:0 5px; min-width:180px;">
                                                                    <option>Anchor</option>
                                                                    <option>Borrower</option>
                                                                </select>
                                                                upfront for a period upto 30 days at the time of
                                                                disbursement of each tranche.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>2.</b></td>
                                                            <td valign="top">Lender will deduct upfront interest for a
                                                                period upto 30 days at the time of disbursement of each
                                                                tranche.</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><span style="margin-top:7px;display: inline-block;"><b>3.</b></span>
                                                            </td>
                                                            <td valign="top">
                                                                Lender shall charge monthly interest to the
                                                                <select style="min-height:30px; padding:0 5px; min-width:180px;">
                                                                    <option>Anchor</option>
                                                                    <option>Borrower</option>
                                                                </select>
                                                                at the month end based on utilization done during the
                                                                month. (Interest need to be paid by the borrower
                                                                immediately, after which delayed penal charges on
                                                                interest would be applicable)
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>One time Processing Charges at the time of Sanction
                                                        of credit facility</b></td>
                                                <td>
                                                    0.50% of the sanctioned limit + applicable taxes payable by the
                                                    <select style="min-height:30px; padding:0 5px; min-width:180px;">
                                                        <option>Purchase Order</option>
                                                        <option>Invoice</option>
                                                        <option>Proforma Invoice</option>
                                                    </select>
                                                    . *(If Nil is selected in offer– not to capture in final SL).
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Penal Interest</b></td>
                                                <td>
                                                    2% per month in case any tranche remains unpaid after the expiry of
                                                    approved tenor from the
                                                    disbursement date. Penal interest to be charged for the relevant
                                                    tranche for such overdue period
                                                    till actual payment of such tranche.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Applicable Taxes</b></td>
                                                <td>
                                                    Any charges/interest payable by the Borrower/Anchor as mentioned in
                                                    the sanction letter are
                                                    excluding applicable taxes. Taxes applicable would be levied
                                                    additionally
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Security from Borrower</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Charge on receivable from Anchor/ Pari Passu charge on
                                                                the current assets of the Borrower/ First
                                                                & Exclusive/Pari-Pasu charge on current assets by way of
                                                                Hypothecation of current assets.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Personal Guarantee of Mr. Atulya Bhatia and Mr. Sushant
                                                                Gaur.</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Payment mechanism</b></td>
                                                <td>
                                                    <select style="min-height:30px; padding:0 5px; min-width:180px;">
                                                        <option>1.NACH Mandate from the Borrower.</option>
                                                        <option>2.RTGS from its customers on or before due date.
                                                        </option>
                                                        <option>3. Repayment through NACH/PDC/RTGS/NEFT from Anchor on
                                                            or before due date.</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Transaction process</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Borrower will submit a disbursal request along with
                                                                proforma invoices / invoices and Anchor will
                                                                confirm the proforma invoices / invoices.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Lender will disburse the payment against the proforma
                                                                invoice / invoices in Borrower’s
                                                                working capital account/current account / Anchor's
                                                                working capital account
                                                                (in case of re-imbursement) post receiving confirmation
                                                                from <input type="text" value="Anchor" style=" min-height:30px;padding:0 5px; margin-top:5px;">.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Disbursement amount should not exceed 70% of proforma
                                                                invoices / invoices.</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>On due date, Anchor will make payment to Lender within
                                                                credit period of 30 days.</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Specific pre-disbursement conditions</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Personal Guarantee of Co-Borrower – Name</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Filing of CHG-1 form for creation of charge on entire
                                                                current assets of borrower</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Details of working capital bank account number with
                                                                cancelled cheque.</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>Anchor Acceptance of master letter served by Borrower
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>NACH Mandate from Borrower</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td>3 cheques covering the sanctioned limits.</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Specific post-disbursement conditions</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                </tr>
                        </table>
                        </td>
                        </tr>
                        </tbody>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="text-align:justify; margin-top:25px;">
                            <thead>
                                <tr>
                                    <th bgcolor="#cccccc" class="text-center" height="30"> <input type="text" value="Annexure II - General Terms and Conditions" style=" min-height:30px;padding:0 5px; min-width:300px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <table width="100%" border="1">
                                            <tr>
                                                <td width="30%" valign="top"><b>Review Date</b></td>
                                                <td>28th February <input type="date" style="min-height:30px;"></td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Sanction validity for first disbursement</b></td>
                                                <td><input type="text" value="60 days" style=" min-height:30px;padding:0 5px; "> from the date of
                                                    sanction.</td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Default Event</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td><input type="text" value="Payments not received on or before the due date will be treated as overdue / default by the Borrower." style=" min-height:30px;padding:0 5px; min-width:100%;">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">●</td>
                                                            <td><input type="text" value="No further disbursement will be made in case of any default under the Facility." style=" min-height:30px;padding:0 5px; min-width:100%;">
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>General pre-disbursement conditions</b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td colspan="2">One-time requirement:</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>1.</b></td>
                                                            <td>Accepted Sanction Letter</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>2.</b></td>
                                                            <td>Loan Agreement </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>3.</b></td>
                                                            <td>
                                                                Self-Attested KYC of borrower (True Copy)
                                                                <table width="100%" border="0">
                                                                    <tr>
                                                                        <td valign="top" width="3%">●</td>
                                                                        <td>
                                                                            Certificate of incorporation, MOA, AOA/
                                                                            Partnership Deed/ Shop and Establishment
                                                                            registration
                                                                            certificate / Udyog Adhar.
                                                                            <select style="width:100%; min-height:30px; padding:0 5px; margin-top:10px;">
                                                                                <option>COI, MOA& AOA – Private
                                                                                    /PublicLimited Partnership Deed –
                                                                                    Partnership Firm</option>
                                                                                <option>Shop and Establishment
                                                                                    registration certificate / Udyog
                                                                                    Aadhar – Proprietorship firm
                                                                                </option>
                                                                            </select>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" width="3%">●</td>
                                                                        <td>Address Proof (Not older than 60 days)</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" width="3%">●</td>
                                                                        <td>PAN Card</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" width="3%">●</td>
                                                                        <td>GST registration letter</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>4.</b></td>
                                                            <td>
                                                                Partnership Authority Letter/ Proprietorship
                                                                Declaration/ Board Resolution signed by 2
                                                                directors or Company Secretary in favour of company
                                                                officials to execute such agreements or
                                                                documents.<br />
                                                                <select style="width:200px; min-height:30px; padding:0 5px; margin-top:10px;">
                                                                    <option>BR</option>
                                                                    <option>PAL</option>
                                                                    <option>PD</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>5.</b></td>
                                                            <td>
                                                                KYC of authorized signatory:
                                                                <table width="100%" border="0">
                                                                    <tr>
                                                                        <td valign="top" width="3%">●</td>
                                                                        <td>Name of authorized signatories with their
                                                                            Self Attested ID proof and address proof
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td valign="top" width="3%">●</td>
                                                                        <td>Signature Verification of authorized
                                                                            signatories from Borrower's banker
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%"><b>6.</b></td>
                                                            <td>Any other documents considered necessary by Lender from
                                                                time to time
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30%" valign="top"><b>Monitoring Covenants</b></td>
                                                <td>
                                                    <select style="width:200px; min-height:30px; padding:0 5px;">
                                                        <option>Applicable</option>
                                                        <option>Not applicable</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td valign="top"><b>Other Conditions </b></td>
                                                <td>
                                                    <table width="100%" border="0">
                                                        <tr>
                                                            <td valign="top" width="5%">1.</td>
                                                            <td>Borrower undertakes that no deferral or moratorium will
                                                                be sought by the borrower during the tenure
                                                                of the facility
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">2.</td>
                                                            <td>The loan shall be utilized for the purpose for which it
                                                                is sanctioned, and it should not be utilized for –</td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">3.</td>
                                                            <td>The Borrower shall maintain adequate books and records
                                                                which should correctly reflect their
                                                                financial position and operations and it should submit
                                                                to CFPL at regular intervals such statements
                                                                as may be prescribed by CFPL in terms of the RBI /
                                                                Bank’s instructions issued from time to time.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">4.</td>
                                                            <td>The Borrower will keep CFPL informed of the happening of
                                                                any event which is likely to have an
                                                                impact on their profit or business and more
                                                                particularly, if the monthly production or sale and
                                                                profit are likely to be substantially lower than already
                                                                indicated to CFPL. The Borrower will
                                                                inform accordingly with reasons and the remedial steps
                                                                proposed to be taken.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">5.</td>
                                                            <td>CFPL will have the right to examine at all times the
                                                                Borrower’s books of accounts and to have
                                                                the Borrower’s factory(s)/branches inspected from time
                                                                to time by officer(s) of the CFPL and/or
                                                                qualified auditors including stock audit and/or
                                                                technical experts and/or management consultants of
                                                                CFPL’s choice and/or we can also get the stock audit
                                                                conducted by other banker. The cost of such
                                                                inspections will be borne by the Borrower
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">6.</td>
                                                            <td>The Borrower should not pay any consideration by way of
                                                                commission, brokerage, fees or in any
                                                                other form to guarantors directly or indirectly.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">7.</td>
                                                            <td>The Borrower and Guarantor(s) shall be deemed to have
                                                                given their express consent to CFPL to disclose the
                                                                information and data furnished by them to CFPL and also
                                                                those regarding the credit facility/ies enjoyed by the
                                                                Borrower, conduct of accounts and guarantee obligations
                                                                undertaken by guarantor to the Credit Information Bureau
                                                                (India) Ltd. (“CIBIL”), or RBI or any other agencies
                                                                specified by RBI who are authorized to seek and publish
                                                                information.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">8.</td>
                                                            <td>The Borrower will keep the CFPL advised of any
                                                                circumstances adversely affecting their financial
                                                                position including any action taken by any creditor,
                                                                Government authority against them.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">9.</td>
                                                            <td>The Borrower shall procure consent every year from the
                                                                auditors appointed by the borrower to
                                                                comply with and give report / specific comments in
                                                                respect of any query or requisition made by us
                                                                as regards the audited accounts or balance sheet of the
                                                                Borrower. We may provide information and
                                                                documents to the Auditors in order to enable the
                                                                Auditors to carry out the investigation requested
                                                                for by us. In that event, we shall be entitled to make
                                                                specific queries to the Auditors in the light
                                                                of Statements, particulars and other information
                                                                submitted by the Borrower to us for the purpose of
                                                                availing finance, and the Auditors shall give specific
                                                                comments on the queries made by us
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">10.</td>
                                                            <td>The sanction limits would be valid for acceptance for 30
                                                                days from the date of the issuance
                                                                of letter.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">11.</td>
                                                            <td>CFPL reserves the right to alter, amend any of the
                                                                condition or withdraw the facility,
                                                                at any time without assigning any reason and also
                                                                without giving any notice.
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td valign="top" width="5%">12.</td>
                                                            <td>Provided further that notwithstanding anything to the
                                                                contrary contained in this Agreement,
                                                                CFPL may at its sole and absolute discretion at any
                                                                time, terminate, cancel or withdraw the Loan
                                                                or any part thereof (even if partial or no disbursement
                                                                is made) without any liability and without
                                                                any obligations to give any reason whatsoever, whereupon
                                                                all principal monies, interest thereon and
                                                                all other costs, charges, expenses and other monies
                                                                outstanding (if any) shall become due and payable
                                                                to CFPL by the Borrower forthwith upon demand from CFPL
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td>I /We accept all the terms and conditions which have been read and understood by
                                        me/us. </td>
                                </tr>
                                <tr>
                                    <td>We request you to acknowledge and return a copy of the same as a confirmation.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0">
                                            <tbody>
                                                <tr>
                                                    <td width="50%" valign="top" height="40"><b>Yours Sincerely</b></td>
                                                    <td valign="top" height="40"><b>Accepted for and behalf of
                                                            Borrower</b></td>
                                                </tr>
                                                <tr>
                                                    <td width="50%" valign="top" height="40"><b>For Capsave Finance
                                                            Private Limited</b></td>
                                                    <td valign="top" height="40"><b>For Suumaya Lifestyle Limited</b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="40">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <table width="100%" border="0">
                                            <tbody>
                                                <tr>
                                                    <td width="50%" valign="top" height="40"><b>Authorized Signatory</b>
                                                    </td>
                                                    <td valign="top" height="40"><b>Authorized Signatory</b></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="30">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <div style="font-family: 'Federo', sans-serif;"><span style="font-size:20px; font-weight:bold;">CAPSAVE FINANCE PRIVATE
                                                LIMITED</span><br />
                                            Registered office: Unit No.501 Wing-D, Lotus Corporate Park, Western Express
                                            Highway, Goregaon (East), Mumbai - 400063<br />
                                            Ph: +91 22 6173 7600, CIN No: U67120MH1992PTC068062
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
{!! Helpers::makeIframePopup('previewSanctionLetter', 'Preview/Send Mail Sanction Letter', 'modal-lg') !!}
{!! Helpers::makeIframePopup('previewSupplyChainSanctionLetter', 'Send Mail Supply Chain Letter', 'modal-lg') !!}
{!! Helpers::makeIframePopup('uploadSanctionLetter', 'Upload Sanction Letter', 'modal-md') !!}
@endsection
@section('jscript')
<script>
    var messages = {
        get_applications: "{{ URL::route('ajax_app_list') }}"
        , data_not_found: "{{ trans('error_messages.data_not_found') }}"
        , token: "{{ csrf_token() }}",

    };

    var ckeditorOptions = {
        filebrowserUploadUrl: "{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'file']) }}"
        , filebrowserUploadMethod: 'form'
        , imageUploadUrl: "{{ route('upload_ckeditor_image', ['_token' => csrf_token(), 'type' => 'image']) }}"
        , disallowedContent: 'img{width,height};'
    };

    CKEDITOR.replace('delay_pymt_chrg', ckeditorOptions);
    CKEDITOR.replace('insurance', ckeditorOptions);
    CKEDITOR.replace('bank_chrg', ckeditorOptions);
    CKEDITOR.replace('legal_cost', ckeditorOptions);
    CKEDITOR.replace('po', ckeditorOptions);
    CKEDITOR.replace('pdp', ckeditorOptions);
    CKEDITOR.replace('disburs_guide', ckeditorOptions);
    CKEDITOR.replace('other_cond', ckeditorOptions);
    CKEDITOR.replace('covenants', ckeditorOptions);
    CKEDITOR.replace('rating_rational', ckeditorOptions);
    $(document).ready(function() {
        $('#payment_type').on('change', function() {
            $('#payment_type_comment').val('');
            if ($(this).val() == '5') {
                $('#payment_type_comment').removeClass('hide');
            } else {
                $('#payment_type_comment').addClass('hide');
            }
        })

        $("input[name='sanction_validity_date']").datetimepicker({
            format: 'dd/mm/yyyy'
            , autoclose: true
            , minView: 2
            , startDate: '-0m'
        , }).on('changeDate', function(e) {
            $("input[name='sanction_expire_date']").val(ChangeDateFormat(e.date, 'dmy', '/', 30));

        });

        $("input[name='sanction_expire_date']").datetimepicker({
            format: 'dd/mm/yyyy'
            , autoclose: true
            , minView: 2
            , startDate: '+1m'
        });
    });


    function ChangeDateFormat(dateObj, out_format = 'ymd', out_separator = '/', dateAddMinus = 0) {
        dateObj.setDate(dateObj.getDate() + dateAddMinus);
        var twoDigitMonth = ((dateObj.getMonth().length + 1) === 1) ? (dateObj.getMonth() + 1) : '0' + (dateObj
            .getMonth() + 1);
        var twoDigitDate = dateObj.getDate() + "";
        if (twoDigitDate.length == 1) twoDigitDate = "0" + twoDigitDate;
        var Digityear = dateObj.getFullYear();
        switch (out_format) {
            case 'myd':
                outdate = twoDigitMonth + out_separator + Digityear + out_separator + twoDigitDate;
                break;
            case 'ydm':
                outdate = Digityear + out_separator + twoDigitDate + out_separator + twoDigitMonth;
                break;
            case 'dmy':
                outdate = twoDigitDate + out_separator + twoDigitMonth + out_separator + Digityear;
                break;
            case 'dym':
                outdate = twoDigitDate + out_separator + Digityear + out_separator + twoDigitMonth;
                break;
            case 'mdy':
                outdate = twoDigitMonth + out_separator + twoDigitDate + out_separator + Digityear;
                break;
            default:
                outdate = Digityear + out_separator + twoDigitMonth + out_separator + twoDigitDate;
                break;
        }
        return outdate;
    }

    $(document).on('click', '.clone_covenants', function() {
        // covenants_clone_tr_html =  $('.covenants_clone_tr').html();
        covenants_clone_tr_html =
            '<td><input maxlength="100" value="" type="text" name="covenants[name][]" class="input_sanc" placeholder="Enter Covenants"></td><td><input maxlength="10" value="" type="text" name="covenants[ratio][]" class="input_sanc" placeholder="Enter Minimum/Maximum ratio"></td><td><select class="select" name="covenants[ratio_applicability][]"><option selected="">Applicable</option><option>Not Applicable</option></select></td>';
        $('.FinancialCovenantsTBody').append("<tr>" + covenants_clone_tr_html + "</tr>");
    })
    $(document).on('click', '.remove_covenants', function() {
        totalrows = $('.FinancialCovenantsTBody').children().length;
        if (totalrows > 1) {
            $('.FinancialCovenantsTBody tr:last-child').remove();
        }
    })

    $(document).ready(function() {
        jQuery.validator.addMethod("alphanumeric", function(value, element) {
            return this.optional(element) || /^[\w\s.]+$/i.test(value);
        }, "Letters, numbers, and underscores only please");

        jQuery.validator.addMethod("ratio", function(value, element) {
            return this.optional(element) || /^[0-9:]+$/i.test(value);
        }, "Numbers and colon only please");

        $('#new_sanction_letter_form').validate({
            rules: {
                "pdc_facility_no": {
                    number: true
                }
                , "pdc_facility_name": {
                    alphanumeric: true
                }
                , "pdc_facility_amt": {
                    number: true
                }
                , "pdc_facility_purpose": {
                    alphanumeric: true
                }
                , "pdc_no_of_cheque[]": {
                    number: true
                }
                , "pdc_not_above[]": {
                    alphanumeric: true
                }
                , "nach_facility_no": {
                    number: true
                }
                , "nach_facility_name": {
                    alphanumeric: true
                }
                , "nach_facility_amt": {
                    number: true
                }
                , "nach_facility_purpose": {
                    alphanumeric: true
                }
                , "nach_no_of_cheque[]": {
                    number: true
                }
                , "nach_not_above[]": {
                    alphanumeric: true
                }
                , "dsra_amt": {
                    number: true
                }
                , "dsra_tenure": {
                    number: true
                }
                , "dsra_comment": {
                    alphanumeric: true
                }
                , "other_sucurities": {
                    alphanumeric: true
                }
                , "covenants[name][]": {
                    alphanumeric: true
                }
                , "covenants[ratio][]": {
                    number: true
                    , min: 0
                    , max: 1.24
                }
            }
        });
    });

</script>
@endsection
