@extends('layouts.frame')

@section('content')

<div class="form-box" style=" background-color: #fff;">

    <form action="{{route('save_buy_right_popup',['right_id'=>$rightId])}}" id="appForm" method="post">
        <div class="row">
            <div class="form-group  col-md-12">
                <label>Name</label>
                <input type="Text" required="required" class="form-control"  placeholder="Name" value="{{$userData->first_name." ".$userData->last_name}}" name="buyername" id="buyername">
            </div>
        </div>
        <div class="row  ">
            <div class="col-md-6 form-group">
                <label>Mobile No</label>
                @if(!empty($userData->phone))
                @php $phone = "(".substr($userData->phone, 0, 3).")"."-(".substr($userData->phone, 3, 3).")-(".substr($userData->phone,6).")"; @endphp
                @else
                @php $phone = '' @endphp
                @endif
                <input type="Text" required="required" class="form-control"  placeholder="(XXX)-(XXX)-(XXXX)" value="{{$phone}}" name="buyerphone" id="buyerphone" maxlength="10">
            </div>
            <div class="col-md-6 form-group">
                <label>Email ID</label>

                <input type="email" required="required" class="form-control"  placeholder="email" value="{{$userData->email}}" name="buyeremail" id="buyeremail">
            </div>
            <div class="col-md-6 form-group">
                <label>Location</label>
                <!--<select class="form-control">

                </select>-->


                {!!
                Form::select('country_id',
                [''=>'Select']+Helpers::getCountryDropDown()->toArray(),
                (isset($userData->country_id) && !empty($userData->country_id)) ? $userData->country_id : (old('country_id') ? old('country_id') : ''),
                array('id' => 'country_id',
                'class'=>'form-control select2Cls'))
                !!}


            </div>


        </div>

        <!--<div class="row">
            <div class="col-md-12">
                <p class="available">There are two options available to license this right.</p>
            </div>
        </div>-->
        <!-- <div class="row">-->
        <!--<div class="col-md-6 >-->

        @php $dataRights = Helpers::getRightPricebyId($rightId)@endphp

        @php 
        $rightPrice=0;
        $rightPrice2=0;
        $check1="";
        $check2="";
        $rightExpiryDate=$dataRights[0]->expiry_date;
        @endphp
        @if($dataRights[0]->is_exclusive_purchase == 1 && $dataRights[0]->is_non_exclusive_purchase == 1)
        @php $rightType = "Exclusive";$check1="checked";
        $rightPrice = $dataRights[0]->exclusive_purchase_price;
        $rightPrice2= $dataRights[0]->non_exclusive_purchase_price;
        $rightLicTime = "Years";
        @endphp
        @elseif($dataRights[0]->is_exclusive_purchase == 1)
        @php $rightType = "Exclusive";$check1="checked";
        $rightPrice = $dataRights[0]->exclusive_purchase_price;
        $rightLicTime = "Years";
        @endphp
        @elseif($dataRights[0]->is_non_exclusive_purchase == 1)
        @php $rightType = "Non-Exclusive";$check2="checked";

        $rightPrice2= $dataRights[0]->non_exclusive_purchase_price;
        $rightLicTime = "Months";

        @endphp
        @endif

        <div class="row">
            @if($dataRights[0]->is_exclusive_purchase == 1)
            <div class="col-md-6 mb-2">
                <div class="custom-control custom-radio radio-bg ">
                    <input type="radio" class="custom-control-input licensing" id="FreeHolder" name="purchase_type"  value="1" required="required" <?= $check1 ?>>
                    <label class="custom-control-label" for="FreeHolder">Exclusive Licensing</label>
                </div>

            </div>
            @endif
            <!-- Default checked -->
            @if($dataRights[0]->is_non_exclusive_purchase == 1)
            <div class="col-md-6 mb-2">
                <div class="custom-control custom-radio radio-bg">
                    <input type="radio" class="custom-control-input licensing" id="PremiumRetailer" name="purchase_type"  value="2" required="required" <?= $check2 ?>>
                    <label class="custom-control-label" for="PremiumRetailer">Non-Exclusive Licensing</label>
                </div>


            </div>
            @endif
        </div>
        <div class="row" >
            <div class="col-md-6 ">
                <div class="row">

                    <div class="col-sm-8"> <label id="rightTime">Number of {{@$rightLicTime}} for licensing</label></div>
                    <div class="col-sm-4"> <input type="Text"  class="form-control numcls time-limit"  placeholder="" required="required" name="buyforyear" id="buyforyear" >
                    </div>
                </div>
            </div>    
            <div class="col-md-6">
                <div class="radio-box mb-3">

                    <div class="custom-control">
                        <label class="cost-label " for="one" id="rightType">{{@$rightType}} Cost</label>
                    </div>
                    <div id="Exclusive1" class="desc ml-2"> ${{(@$rightPrice>0)?@$rightPrice:@$rightPrice2}}</div>
                    <input type="hidden" name="priceTag" id="priceTag" value="{{(@$rightPrice>0)?@$rightPrice:@$rightPrice2}}">
                    <input type="hidden" name="updatedprice" id="updatedprice" value="">
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                &nbsp;
            </div>

        </div>
         @if($rightExpiryDate!='' && $rightExpiryDate!=null && $rightExpiryDate!='0000-00-00')

        <div class="row">
            <div class="col-md-12">
                <p><i class="fa fa-sticky-note-o" aria-hidden="true"></i> This Right expires on {{date('d M Y',strtotime($rightExpiryDate))}}</p>
            </div>
        </div>
        @endif

        <!--<div class="row">

            <div class="col-sm-8"> <label>Number Of {{$rightLicTime}} for licensing</label></div>
            <div class="col-sm-4"> <input type="Text" class="form-control numcls" required="required" placeholder="" required="required" name="buyforyear" id="buyforyear">
            </div>
        </div>-->
        <!-- </div>-->
        <!--<div class="col-md-6">
            <div class="radio-box mb-3">-->
        <!-- Default unchecked -->





        <!--<div class="custom-control">
            <label class="cost-label " for="one">{{@$rightType}} Cost</label>
        </div>
        <div id="Exclusive1" class="desc ml-2" > ${{@$rightPrice}}

        </div>
        <input type="hidden" name="priceTag" id="priceTag" value="{{@$rightPrice}}">
        <input type="hidden" name="updatedprice" id="updatedprice" value="">-->



        <!--</div>

    </div>-->
        <!--</div>-->




        <div class="row">
            <div class="col-md-12 terms">
                <div class="heading-text">Software License Agreement</div>
                <div class="term-condition">
                    <div class="container">
                        <div class="license">
                            <div class="user-head "><h4 class="text-center">SOFTWARE LICENSE AGREEMENT</h4>
                                <p>This Software License Agreement ("Agreement") is entered into this <strong><span>{{$now->format('d')}}</span></strong> day of <strong><span>{{$now->format('F')}}</span></strong>, <strong><span>{{$now->year}} </span></strong>(the "Effective Date") by and between
                                    <strong>{{ucfirst($holderData->first_name)." ".ucfirst($holderData->last_name)}}</strong>, an<strong><span> NA</span></strong> company , with offices at<strong><span> {{ucwords(Helpers::getCountryById($holderData->country_id)->country_name)}}</span></strong>
                                    (Licensor), and <strong><span>{{ucfirst($userData->first_name)." ".ucfirst($userData->last_name)}},</span></strong> an <strong><span>NA</span></strong> corporation,
                                    with offices at<strong><span> {{ucwords(Helpers::getCountryById($userData->country_id)->country_name)}}</span></strong> (Licensee). </p>
                                 
                                <p>In consideration of the mutual covenants and agreements set forth in this Agreement, and other good and valuable consideration, the receipt and adequacy of which are hereby acknowledged, the parties do hereby agree as follows: </p>
                                  



                            </div>
                            <div class="user-head">
                                <h4>1. DEFINITIONS</h4>
                                <p>(a)"Documentation" shall mean the technical written material which relates to the Software, describes the functionality of the Software and instructs Licensee in the use of the Software. </p>
                                <p>(b)"Intellectual Property Rights" means copyright rights (including, without limitation, the exclusive right to use, reproduce, modify, distribute, publicly display and publicly perform the copyrighted work), patent rights (including, without limitation, the exclusive right to make, use, sell and import), trade secrets, moral rights, goodwill and all other intellectual property rights as may exist now and/or hereafter come into existence and all applications, renewals and extensions thereof, regardless of whether such rights arise under the law of the United States or any other state, country or jurisdiction. Intellectual Property Rights does not include trademark rights (including, without limitation, trade names, trademarks, service marks, and trade dress).</p>
                                <p>(c)"Software" shall mean both the object code and source code versions of the computer programs described in Schedule A and any related Documentation, in machine readable and/or printed form, furnished to Licensee under this Agreement.</p>
                            <p>"Territory" shall mean {{ucwords(Helpers::getCountryById($userData->country_id)->country_name)}}.</p>
                            </div>
                            <div class="user-head">
                                <h4>2. LICENSE GRANT</h4>
                                <p><span style='text-decoration: underline;'>(a)License Grant. </span> Subject to the terms and conditions of this Agreement and in consideration of Licensee’s obligation to pay monetary fees as outlined in Schedule B, Licensor hereby grants, and Licensee hereby accepts, a perpetual, <span id="rightTypeName">NA</span>, revocable, nontransferable license under the Intellectual Property Rights owned by Licensor to reproduce and distribute the Software in object code form within the Territory. <span id="onlyExlText"> </span>
                                </p>
                                <p><span style='text-decoration: underline;'>(b)Licensee Rights.</span>  Licensor also grants permission to Licensee to make and create customizations, updates or corrections to the Software (“Modifications”). Licensee assigns all right, title and interest in the Intellectual Property Rights worldwide in the Modifications to Licensor and the Modifications shall be included in the definition of Software.</p>

                                <p><span style='text-decoration: underline;'>(c)Restrictions. </span> Licensee shall not directly or indirectly, nor will Licensee permit any party to, do any of the following:
                                    (i) reverse engineer, decompile, disassemble or otherwise attempt to discover the source code form of the Software or the underlying structure, ideas, know-how or algorithms relevant to the services or any software, documentation or data related to the Software; (ii) use the Software or Documentation in violation of export control laws and regulations; (iii) remove any proprietary notices from any of the Software, Documentation or any other Licensor materials furnished or made available hereunder; (iv) access any of the Software to copy any features, functions or graphics of any of the Software, or attempt to gain unauthorized access to any of the Software; (v) use any of the Software to store or transmit malicious code; or (vi) publish or disclose to third parties any evaluation of any of the Software without Licensor’s prior written consent.  Licensee (1) is solely responsible for the accuracy, quality, integrity and legality of all electronic data or information collected and processed by the Software; (2) shall use commercially reasonable efforts to prevent unauthorized access to or use of the Software, and notify Licensor promptly of any such unauthorized access or use, and (3) shall use the Software only in accordance with the Documentation and applicable laws and government regulations.
                                    Licensee is responsible for compliance with the terms of this Agreement by any of its affiliates and users.</p>
                            </div>

                            <div class="user-head">
                                <h4>3. WARRANTIES OF LICENSOR</h4>
                                <p><span style='text-decoration: underline;'>(a)Ownership.</span>  Licensor represents and warrants that Licensor is the sole and exclusive owner of all right, title and interest in the Software and all Intellectual Property Rights therein. <span id="onlyExclon">NA</span></p>

                                <p><span style='text-decoration: underline;'>(b)Non-Infringement.</span>  Licensor represents and warrants that (i) there are no claims, pending or threatened, with respect to Licensor's rights in the Software, and (ii) Licensor is not aware of any facts or circumstances which, if true, would possibly lead to claims of infringement or misappropriation.</p>

                            <p> <span style='text-decoration: underline;'>(c)Authority.</span>  Licensor represents and warrants that Licensor has full power and authority to enter into and perform this Agreement, this Agreement is enforceable against Licensor in accordance with its terms, and this Agreement does not conflict with or violate any obligation or contract to which Licensor is a party or bound.  </p>
                            <p><span style='text-decoration: underline;'>(d)No Open Source.</span> Licensor represents and warrants that the Software is not subject to any "open source," "free software," "copyleft" or similar license agreement.</p>
                            <p><span style='text-decoration: underline;'>(e)Public Domain. </span>  Licensor represents and warrants that the Software is not in the public domain.</p>
                            </div>





                            <div class="user-head">
                                <h4>4. WARRANTIES OF LICENSEE</h4>
                                <p>Licensee represents and warrants that:
                                    (i) it is organized and validly existing under the laws of the state of its formation and it has full authority to enter into this Agreement and to carry out its obligations hereunder; (ii)  this Agreement is a legal and valid obligation binding upon it and enforceable according to its terms, except to the extent such enforceability may be limited by bankruptcy, reorganization, insolvency or similar laws of general applicability governing the enforcement of the rights of creditors or by the general principles of equity (regardless of whether considered in a proceeding at law or in equity); and (iii) its execution, delivery and performance of this Agreement does not conflict with any agreement,
                                    instrument or contract, oral or written, to which it is bound.</p>





                            </div>

                            <div class="user-head">
                                <h4>5. PROPERTY RIGHTS AND CONFIDENTIALITY </h4>
                                <p>a) <span style='text-decoration: underline;'>Property Rights. </span> Licensee recognizes that the Software, and Modifications, if any, are the property of, and all rights thereto, are owned by Licensor. Licensee also acknowledges that such are a trade secret of Licensor, are valuable and confidential to Licensor, and that their use and disclosure must be carefully and continuously controlled. </p>

                                <p> <span style='text-decoration: underline;'>Confidentiality.</span>  Each party, as recipient, shall not use the other party’s confidential information, or know-how relating to the Software (“Confidential Information”) except as necessary to exercise the rights under or perform this Agreement and shall not disclose Confidential Information to any third party. Each party agrees to take all reasonable steps to ensure that Confidential Information is not disclosed or distributed by its employees or agents in violation of the terms of this Agreement, and shall immediately give notice to the other party of any unauthorized use or disclosure of Confidential Information. Notwithstanding any of the foregoing, either party may disclose Confidential Information if required by law or regulatory authorities, provided that such party shall notify the disclosing party of the required disclosure promptly in writing and shall cooperate with the disclosing party in any lawful action to contest or limit the scope of the required disclosure before disclosing any Confidential Information. Confidential Information shall not include information that: (i) is or becomes a part of the public domain through no act or omission of the receiving party; (ii) was in the receiving party's lawful possession prior to the disclosure and had not been obtained by the receiving party either directly or indirectly from the disclosing party; (iii) is lawfully disclosed to the receiving party by a third party without restriction on disclosure; or (iv) is independently developed by the receiving party.</p>


                            </div>

                            <div class="user-head">
                                <h4>6. TERM AND TERMINATION </h4>
                                <p>(a)<span style='text-decoration: underline;'>Term.</span> This Agreement shall continue from the Effective Date until <strong><span id="licensedate">NA</span></strong>. 
                                In the event of any expiration or termination of this Agreement, all licenses granted under this
                                Agreement shall be deemed terminated and Licensee shall return all copies of the Software to Licensor and delete all copies of Software owned or controlled by Licensee.</p>

                                <p>(b)<span style='text-decoration: underline;'>Termination. </span> In the event a party materially breaches this Agreement,
                                    the other party may terminate this Agreement without penalty or fee upon 30 days’ advance written notice to the non-terminating party, if the breach is not cured within such 30 day period.
                                    Any failure to pay fees hereunder is a material breach of this Agreement.</p>
                                <p>(c)<span style='text-decoration: underline;'>Survival.</span>  Termination of this Agreement shall not
                                relieve either party of its obligations pursuant to Sections 4, 5, 6, 7, 8 and 9 hereof. </p>

                            </div>

                            <div class="user-head">
                                <h4>7. DISCLAIMER OF WARRANTIES</h4>
                                <p>EXCEPT AS EXPRESSLY PROVIDED IN THIS AGREEMENT, TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW,
                                    LICENSOR PROVIDES THE SOFTWARE AS-IS WITHOUT ANY WARRANTIES OF ANY KIND, WHETHER EXPRESS, IMPLIED, STATUTORY,
                                    OR OTHERWISE, AND LICENSOR SPECIFICALLY DISCLAIMS ANY IMPLIED WARRANTIES OR CONDITIONS OF MERCHANTABILITY,
                                    FITNESS FOR A PARTICULAR PURPOSE, NON-INFRINGEMENT, TITLE, AND ANY WARRANTIES ARISING FROM COURSE OF DEALING
                                    OR COURSE OF PERFORMANCE REGARDING OR RELATING TO THE SOFTWARE. LICENSOR DOES NOT WARRANT THAT THE SOFTWARE
                                    WILL OPERATE UNINTERRUPTED, OR THAT IT WILL BE FREE FROM DEFECTS OR THAT THE SOFTWARE WILL MEET (OR IS DESIGNED TO MEET)
                                    LICENSEE’S BUSINESS REQUIREMENTS. FURTHER, LICENSOR DOES NOT WARRANT THAT ALL ERRORS IN ANY OF THE SOFTWARE ARE CORRECTABLE OR WILL BE CORRECTED.</p>


                            </div>
                            <div class="user-head">
                                <h4>8. INDEMNIFICATION AND LIMITATION OF LIABILITY</h4>
                                <p>(a)<span style='text-decoration: underline;'>Licensee Indemnity.</span>
                                Licensee shall defend Licensor against any cause of action, suit
                                or proceeding (each a "Claim") made or brought against Licensor,
                                and shall indemnify and hold Licensor harmless from and against
                                all losses, costs, expenses, damages or liability
                                (including reasonable attorney’s fees)
                                related to or arising out of a Claim by a third party against Licensor
                                based upon (i) Licensee’s use of the Software, including any customizations, updates and/or corrections to the Software; or (ii) infringement or misappropriation of any intellectual property rights by the Modifications developed by Licensee.</p>

                            <p>(b)<span style='text-decoration: underline;'>Licensor Indemnity.</span>  Licensor shall defend Licensee against any Claim made or brought against Licensee, and shall indemnify
                                and hold Licensee harmless from and against all losses, costs, expenses, damages or liability (including reasonable attorney’s fees)
                                related to or arising out of a Claim by a third party against Licensee based upon infringement or misappropriation of any intellectual property rights by the Software,
                                but not including any Modifications developed by Licensee or any third party. </p>
                            <p>(c)<span style='text-decoration: underline;'>Indemnity Obligations.</span>  Obligations set forth herein are contingent upon the other party
                                (i) providing the indemnifying party with prompt written notice of any action brought against the other party; and (ii) cooperating with the indemnifying party in the defense of any such action, and allowing the
                                indemnifying party to control the defense and settlement of any such action at its expense. </p>
                            <p>(d) <span style='text-decoration: underline;'>Exceptions.</span>  Licensor shall have no obligation to defend any Claim or indemnify Licensee from damage if (i) Licensee, in developing or providing Modifications, infringes upon or misappropriates the intellectual property of any third party; (ii) Licensee is not using the most current version of the Software; (iii) Licensee has modified the Software and such Claim would have been avoided without such modification; or (iv) Licensee is using the Software in combination with other Software and the Claim would have been avoided without such combined use.</p>
                            <p>(e)<span style='text-decoration: underline;'>Limitation Of Liability.</span>  EXCEPT FOR A BREACH OF THE INDEMNITY OBLIGATIONS HEREIN, THE MAXIMUM EXTENT PERMITTED BY LAW, IN NO EVENT SHALL EITHER PARTY BE LIABLE TO THE OTHER PARTY FOR ANY LOST PROFITS OR BUSINESS OPPORTUNITIES, LOSS OF USE, LOSS OF REVENUE, LOSS OF GOODWILL, BUSINESS INTERRUPTION, LOSS OF DATA, INABILITY TO USE THE SOFTWARE, OR ANY OTHER INDIRECT, SPECIAL, INCIDENTAL, OR CONSEQUENTIAL DAMAGES UNDER ANY THEORY OF LIABILITY, WHETHER BASED IN CONTRACT, TORT, NEGLIGENCE, PRODUCT LIABILITY OR OTHERWISE. EXCEPT FOR A BREACH OF THE INDEMNITY OBLIGATIONS HEREIN, A PARTY’S LIABILITY UNDER THIS AGREEMENT SHALL NOT, IN ANY EVENT, EXCEED THE AMOUNTS PAID IN THE 12 MONTHS PRECEDING THE EVENT GIVING RISE TO SUCH LIABILITY. THE FOREGOING LIMITATIONS SHALL APPLY REGARDLESS OF WHETHER A PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES AND REGARDLESS OF WHETHER ANY REMEDY FAILS OF ITS ESSENTIAL PURPOSE. </p>
                            </div>

                            <div class="user-head">
                                <h4>9. GENERAL</h4>
                                <p> (a) <span style='text-decoration: underline;'>Notices.</span>  All notices or other communications required or permitted to be given pursuant to this
                                    Agreement shall be in writing and shall be considered as properly given or made if hand delivered,
                                    mailed from within the United States by certified or registered mail, to the applicable address(es) appearing in the preamble to this Agreement, or to such other address as a party may have designated by like notice forwarded to the other parties hereto. All notices, except notices of change of address, shall be deemed given when mailed or
                                    hand delivered and notices of change of address shall be deemed given when received..</p>
                            <p>(b) <span style='text-decoration: underline;'>Binding Agreement; Non-Assignability.</span> This Agreement and the rights and obligations under this Agreement shall not be transferable, sub-licensable or assignable to any other person, firm or corporation by Licensee, without the express prior written consent of the Licensor. Each of the provisions and agreements contained in this Agreement shall be
                                binding upon and inure to the benefit of the successors and assigns of the respective parties hereto.</p>
                            <p>(c) <span style='text-decoration: underline;'>Entire Agreement.</span> This Agreement constitutes the entire understanding of the
                            parties hereto with respect to the subject matter hereof, and supersedes any prior
                            understandings or agreements, oral or written, and no amendment, modification or
                            alteration of the terms hereof shall be binding unless the same shall be in writing,
                            dated subsequent to the date hereof and duly approved and executed by each of the
                            parties hereto.</p>

                            <p>(d)<span style='text-decoration: underline;'>Application of New York Law.</span>  This Agreement and the application or interpretation thereof, shall be governed exclusively by its terms and by the laws of the State of New York.</p>

                              <p>(e) <span style='text-decoration: underline;'>Counterparts.</span>  This Agreement may be executed in any number of counterparts, each of which shall be deemed an original, but all of which together shall constitute one and the same instrument.</p>
                              <p>(f) <span style='text-decoration: underline;'>No Waiver.</span> The failure of either party to enforce any rights granted under this Agreement or to take action against the other party in the event of any breach hereunder shall not be deemed a waiver by that party as to the subsequent enforcement of rights or subsequent action
                                  in the event of future breaches.</p>
                              <p>(g)  <span style='text-decoration: underline;'>Severability.</span> If any term or provision of this Agreement or the application thereof is determined by a court of competent jurisdiction to be invalid, illegal or unenforceable, all other terms and provisions of this Agreement shall nevertheless
                                  remain in full force and effect.</p>
                            <p>(h) <span style='text-decoration: underline;'>Relationship of the Parties.</span>  The parties to this Agreement are affiliated companies and this Agreement will not establish any relationship of partnership, joint venture, employment, franchise, or agency between the Parties. Neither Party will have the power to bind the other or incur obligations on the
                                other's behalf without the other’s prior written consent.</p>
                            <p>(i) <span style='text-decoration: underline;'>Publicity. </span> A public disclosure of this Agreement may be made by Inventit, Inc. dba InvenTrust. </p>

                                </div>




                            <div class="user-head">
                                <h4>IN WITNESS WHEREOF, the parties hereto have set their hands as of the day and year first above written. </h4>


                            </div>
                            <div class="user-head row">
                                <div class="col"><h5><strong>Licensor</strong></h5>

                                    <p><strong>Name:</strong> {{ ucfirst($holderData->first_name).' '.ucfirst($holderData->last_name)}}<span></span></p>
                                    <p><strong>Title:</strong> {{ ucfirst($holderData->role)}}<span></span></p>
                                    <p><strong>Date:</strong> {{ $now->toFormattedDateString()}}<span></span></p>
                                    <p><strong>By:</strong> NA<span></span></p>

                                </div>
                                <div class="col"><h5><strong>Licensee</strong></h5>
                                    <p><strong>Name:</strong> {{ ucfirst($userData->first_name).' '.ucfirst($userData->last_name)}}<span></span></p>
                                    <p><strong>Title:</strong> {{ ucfirst($userData->role)}}<span></span></p>
                                    <p><strong>Date:</strong> {{ $now->toFormattedDateString()}}<span></span></p>
                                    <p><strong>By:</strong> NA<span></span></p>
                                </div>
                            </div>


                            <div class="user-head">
                                <h4>SCHEDULE A: Description of Software :</h4> 
                                <p>Right Description will be here</p></div>
                            <div class="user-head">
                                <h4>SCHEDULE A: Schedule of License Fees:</h4> 
                                <p>The payments of<strong><span> ${{@$rightPrice}} </span></strong>are to be made in full before by Licensee
                                    by <strong><span>{{ $now->toFormattedDateString()}}.</span></strong></p></div>

                        </div>
                    </div>
                </div>
                <!-- Default checked -->
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="defaultChecked2" required="required">
                    <label class="custom-control-label" for="defaultChecked2">I accept the Software License Agreement terms & conditions.</label>
                </div>
                <div class="form-group form-check">

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 terms">
                <div class="heading-text">Software Purchase Agreement</div>
                <div class="term-condition">
                    <div class="container">
                            <div class="license">
                                <div class="user-head"><h4 class="text-center">SOFTWARE PURCHASE AGREEMENT</h4>
                                    <p>THIS SOFTWARE PURCHASE AGREEMENT ("Agreement") is made this <strong>{{$now->format('d')}}</strong> day of <strong>{{$now->format('F')}}</strong>, <strong>{{$now->year}}</strong>, by
                                        and between <strong>{{ucfirst($holderData->first_name)." ".ucfirst($holderData->last_name)}}</strong>, an <strong><span>NA</span></strong> corporation having its principal office at
                                        <strong>{{ucwords(Helpers::getCountryById($holderData->country_id)->country_name)}}</strong> ("Seller") and  <strong>{{ucfirst($userData->first_name)." ".ucfirst($userData->last_name)}}</strong>, a
                                        <strong>NA</strong> corporation, having its principal office at <strong>{{ucwords(Helpers::getCountryById($userData->country_id)->country_name)}}</strong>
                                        ("Buyer").</p>
                                <p>In consideration of the mutual covenants and agreements set forth in this Agreement, and other good and valuable consideration, the receipt and adequacy of which are hereby acknowledged,
                                    the parties do hereby agree as follows:</p>
                                </div>
                                <div class="user-head">
                                    <h4 class="text-center">1) <span style='text-decoration: underline;'>DEFINITIONS:</span>For purposes of this Agreement, the following definitions shall apply:</h4>

                                    <p> (a) "Documentation" shall mean the technical written material which relates to the Software, describes the
                                        functionality of the Software and instructs Buyer in the use of the Software.</p>
                                    <p>(b) "Intellectual Property Rights" means copyright rights (including, without limitation, the exclusive right to use, reproduce, modify, distribute, publicly display and publicly perform the copyrighted work), patent rights (including, without limitation, the exclusive right to make, use, sell and import), trade secrets, moral rights, goodwill and all other intellectual property rights as may exist now and/or hereafter come into existence and all applications, renewals and extensions thereof, regardless of whether such rights arise under the law of the United States or any other state, country or jurisdiction. Intellectual Property Rights does not include trademark rights
                                        (including, without limitation, trade names, trademarks, service marks, and trade dress).</p>
                                    <p>(c) "Software" shall mean both the object code and source code versions of the computer programs described in Schedule A, and any related Documentation, in machine readable and/or printed form, furnished to Buyer under this Agreement.</p>


                                </div>
                                <div class="user-head">
                                    <h4>2.PURCHASE AND SALE OF SOFTWARE.</h4>
                                    <p>(a)<span style='text-decoration: underline;'>Sale.</span>  Subject to the terms and conditions of this Agreement and in consideration of Buyer’s obligation to make the payments herein, Seller hereby sells, grants, assigns, transfers, conveys, and delivers to Buyer and Buyer hereby accepts all of Seller’s right, title and interest in, to and under both the tangible and the intangible property constituting the Software and any Intellectual Property Rights therein, and the Documentation. </p>
                                    <p>(b)<span style='text-decoration: underline;'>Further Assistance.</span>  Seller hereby agrees to perform all acts deemed necessary or desirable by Buyer to permit and assist Buyer, at Buyer’s expense, in perfecting and enforcing its rights in the Software throughout the world.  Such acts may include, but are not limited to, execution of documents and assistance or cooperation in the registration and enforcement, including litigation, of applicable patents and copyrights or other legal proceedings.  In the event that Buyer is unable for any reason whatsoever to secure a signature to any document it believes is reasonably required in order to apply for or execute any patent, copyright or other application with respect to the Software (including improvements, renewals, extensions, continuations, divisions or continuations in part thereof), Seller hereby irrevocably designates and appoints Buyer and its duly authorized officers and agents as Seller’s agents and its attorneys-in-fact to act for and on its behalf and instead of it, to execute and file any such application and to do all other lawfully permitted acts to further the prosecution and issuance of patents, copyrights or other
                                        Intellectual Property Rights therein with the same legal force and effect as if executed by Seller.</p>
                                </div>
                                <div class="user-head">
                                    <h4>3.PAYMENT.</h4>
                                    <p>(a)<span style='text-decoration: underline;'>Purchase Price</span>. In consideration for the purchase by Buyer of all of the Software and Documentation under this Agreement,
                                        Buyer shall pay Seller an aggregate of ${{@$rightPrice}} ("Purchase Price")</p>
                                    <p>(b)<span style='text-decoration: underline;'>Taxes.</span>  Any sales, use, and other transfer taxes arising out of or incurred in
                                        connection with the transaction contemplated by this Agreement shall be paid by Seller.</p>

                                </div>

                                <div class="user-head">
                                    <h4>4.REPRESENTATIONS AND WARRANTIES.</h4>
                                    <p>(a)<span style='text-decoration: underline;'>Ownership.</span>  Seller represents and warrants that Seller is the sole and exclusive owner of all right,
                                        title and interest in the Software and all Intellectual Property Rights therein.  Seller represents and warrants that the Software and the Intellectual Property Rights therein are free and clear of all encumbrances, including, without limitation,
                                        security interests, licenses, liens, charges or other restrictions.</p>
                                <p>(b)<span style='text-decoration: underline;'>Non-Infringement.</span>  Seller represents and warrants that (a) there are no claims, pending or threatened, with respect to Seller's rights in the Software, and (b) Seller is not aware of any facts or circumstances which,
                                    if true, would possibly lead to claims of infringement or misappropriation.</p>
                                <p>(c)<span style='text-decoration: underline;'>Authority.</span>  Seller represents and warrants that Seller has full power and authority to enter into and perform this Agreement,
                                    this Agreement is enforceable against Seller in accordance with its terms, and this Agreement does not conflict with or violate any obligation or contract to which Seller is a party or bound.  Seller further represents and warrants that the execution and delivery of this Agreement will transfer to and vest in Company good, valid and marketable title to the Software free and clear of all security interests,
                                    liens, encumbrances, charges or other restrictions.</p>
                                <p>(d)<span style='text-decoration: underline;'>No Open Source.<span>  Seller represents and warrants that the Software is not subject to any "open source," "free software," "copyleft" or similar license agreement.</p>
                                        <p>(e)<span style='text-decoration: underline;'>Public Domain.</span>  Seller represents and warrants that the Software is not in the public domain.</p>

                                        </div>





                                <div class="user-head">
                                    <h4>5.DISCLAIMER OF WARRANTY.</h4>
                                    <p>SELLER ASSIGNS THE SOFTWARE TO BUYER "AS IS," AND SELLER DISCLAIMS ALL WARRANTIES EXPRESS OR IMPLIED WITH RESPECT TO THE SOFTWARE, INCLUDING (WITHOUT LIMITATION) ANY WARRANTY OF MERCHANTABILITY, PROFITABILITY OR FITNESS FOR A PARTICULAR PURPOSE. BUYER ASSUMES ALL RISKS AND LOSSES RELATED TO THE SOFTWARE AND DOCUMENTATION</p>


                                </div>

                                <div class="user-head">
                                    <h4>6.CONFIDENTIALITY.</h4>  
                                    <p>Each party, as recipient, shall not use the other party’s confidential information, or know-how relating to the Software (“Confidential Information”) except as necessary to exercise the rights under or perform this Agreement and shall not disclose Confidential Information to any third party. Each party agrees to take all reasonable steps to ensure that Confidential Information is not disclosed or distributed by its employees or agents in violation of the terms of this Agreement, and shall immediately give notice to the other party of any unauthorized use or disclosure of Confidential Information. Notwithstanding any of the foregoing, either party may disclose Confidential Information if required by law or regulatory authorities, provided that such party shall notify the disclosing party of the required disclosure promptly in writing and shall cooperate with the disclosing party in any lawful action to contest or limit the scope of the required disclosure before disclosing any Confidential Information. Confidential Information shall not include information that: (a) is or becomes a part of the public domain through no act or omission of the receiving party; (b) was in the receiving party's lawful possession prior to the disclosure and had not been obtained by the receiving party either directly or indirectly from the disclosing party; (c) is lawfully disclosed to the receiving party by a third party without restriction on disclosure; or (d) is independently developed by the receiving party.</p>


                                </div>

                                <div class="user-head">
                                    <h4>7.GENERAL. </h4>
                                    <p>(a)<span style='text-decoration: underline;'>Notices.</span> All notices or other communications required or permitted to be given pursuant to this Agreement shall be in writing and shall be considered as properly given or made if hand delivered, mailed from within the United States by certified or registered mail, or sent by prepaid telegram to the applicable address(es) appearing in the preamble to this Agreement, or to such other address as a party may have designated by like notice forwarded to the other parties hereto. All notices, except notices of change of address, shall be deemed given when mailed or hand delivered and
                                        notices of change of address shall be deemed given when received.</p>
                                <p> (b)<span style='text-decoration: underline;'>Binding Agreement; Non-Assignability.</span> Each of the provisions and agreements contained in this Agreement shall be binding upon and inure to the benefit of the successors
                                    and assigns of the respective parties hereto.</p>
                                <p>(c)<span style='text-decoration: underline;'>Entire Agreement.</span>  This Agreement constitutes the entire understanding of the parties hereto with respect to the subject matter hereof, and supersedes any prior understandings or agreements, oral or written, and no amendment, modification or alteration of the terms hereof shall be binding unless the same shall be in writing, dated subsequent
                                    to the date hereof and duly approved and executed by each of the parties hereto.</p>

                                <p>(d)<span style='text-decoration: underline;'>Application of New York Law.</span>  This Agreement and the application or interpretation thereof,
                                    shall be governed exclusively by its terms and by the laws of the State of New York.</p>
                                <p>(e)<span style='text-decoration: underline;'>Counterparts.</span>  This Agreement may be executed in any number of counterparts, each of which shall be deemed an original, but all of which together shall
                                    constitute one and the same instrument.</p>
                                <p>(f)<span style='text-decoration: underline;'>No Waiver.</span>  The failure of either party to enforce any rights granted under this Agreement or to take action against the other party in the event of any breach hereunder shall not be deemed a waiver by that party as to the subsequent enforcement of
                                    rights or subsequent action in the event of future breaches.</p>
                                <p>(g)<span style='text-decoration: underline;'>Severability.</span> If any term or provision of this Agreement or the application thereof is determined by a court of competent jurisdiction to be invalid, illegal or unenforceable, all other terms and provisions of this
                                    Agreement shall nevertheless remain in full force and effect.</p>
                                <p>(h)<span style='text-decoration: underline;'>Publicity.</span>  A public disclosure of this Agreement may be made by Inventit, Inc. dba InvenTrust. </p>

                                </div>


                                <div class="user-head">
                                    <h4>IN WITNESS WHEREOF,  the parties have caused this Agreement to be executed by their duly authorized officers on the date first above written. </h4>


                                </div>

                                <div class="user-head row">
                                    <div class="col"><h5><strong>Seller</strong></h5>
                                        <p><strong>By:</strong> <span>NA</span></p>
                                        <p><strong>Name:</strong> <span> {{ ucfirst($holderData->first_name).' '.ucfirst($holderData->last_name)}}</span></p>
                                        <p><strong>Title:</strong> <span>{{ ucfirst($holderData->role)}}</span></p>

                                    </div>
                                    <div class="col"><h5><strong>Buyer</strong></h5>
                                        <p><strong>By:</strong> <span>NA</span></p>
                                        <p><strong>Name:</strong> <span> {{ ucfirst($userData->first_name).' '.ucfirst($userData->last_name)}}</span></p>
                                        <p><strong>Title:</strong> <span>{{ ucfirst($userData->role)}}</span></p></div>
                                </div>

                            </div>
                    </div>
                </div>
                <!-- Default checked -->
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="defaultChecked3" required="required">
                    <label class="custom-control-label" for="defaultChecked3">I accept the Software Purchase Agreement terms & conditions.</label>
                </div>
                <div class="form-group form-check">

                </div>
            </div>
        </div>


        <div class="row justify-content-center ">
            <input type="hidden" name="_token" value="{{ csrf_token()}}">
            <button class="btn btn-x-md btn-primary mr-2" data-dismiss="modal" data-toggle="modal" data-target="#complete-purchase">Submit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>


        </div>


    </form>

</div>


@endsection
@section('pageTitle')
My-Account
@endsection
@section('addtional_css')

@endsection
@section('jscript')
<script>

    var messages = {
        is_accept: "{{ Session::get('is_accept') }}",
        paypal_gatway: "{{ route('confirm_payment') }}",
    };
    $(document).ready(function () {
        if (messages.is_accept == 1) {
            var parent = window.parent;
            parent.jQuery("#rightpurchase").modal('hide');
            window.parent.jQuery('#my-loading').css('display', 'block');
            window.parent.location.href = messages.paypal_gatway;
        }
        
        var purchase_type1 = $("input[name='purchase_type']:checked").val();
        if(parseInt(purchase_type1)==1){
            $("#rightTypeName").text("exclusive");
            $("#onlyExlText").text("This license is exclusive to Licensee and Licensor agrees not to enter into any future Software license obligations or licenses with any third-parties without the prior written consent of Licensee."); 
            $("#onlyExclon").text("Licensor represents and warrants that the Software and the Intellectual Property Rights therein are free and clear of all encumbrances, including, without limitation, security interests, licenses, liens, charges or other restrictions.");
        }else if(parseInt(purchase_type1)==2){
            $("#rightTypeName").text("non-exclusive");
            $("#onlyExlText").text(""); 
            $("#onlyExclon").text("");
        }
        

        $("#buyforyear").on('paste', function (e) {
            e.preventDefault();
        });



        $("#buyforyear").blur(function (e) {
            var MainPrice = "";
            var newPrice = "";
            var inputval = $("#buyforyear").val();
            var Price = $("#buyforyear").val();
            var MainPrice = $("#priceTag").val();
            //alert(MainPrice +"---"+inputval);
            var newPrice = (MainPrice * inputval);
            //------------------------------
            var expary_date = "<?= $rightExpiryDate ?>";
            var purchase_type = $("input[name='purchase_type']:checked").val();
            var purchase_validity = '';
  
            var d1 = new Date('<?=date('Y-m-d')?>');
            var d2 = new Date("<?= $rightExpiryDate ?>");
            var month1=d1.getMonth()+1;
            var year1=d1.getFullYear();
            var day1 = d1.getDate();
            
           
            var month2=d2.getMonth()+1;
            var year2=d2.getFullYear();
            var day2 = d2.getDate();
            var myDate = new Date();
           
            
            if(parseInt(purchase_type)==1){
                year1=parseInt(year1)+parseInt(inputval);
            }else if(parseInt(purchase_type)==2){
                month1=parseInt(month1)+parseInt(month1);
            }
             
            if(year2>year1){

                $("#Exclusive1").html('$' + newPrice);
                $("#updatedprice").val(newPrice);
                if(parseInt(purchase_type)==1){
                    myDate.setFullYear(myDate.getFullYear() + parseInt(inputval));
                    $("#licensedate").text(myDate.getFormatDate());
                }else if(parseInt(purchase_type)==2){
                    myDate.setMonth(myDate.getMonth() + parseInt(inputval));
                    $("#licensedate").text(myDate.getFormatDate());
                }

                
                
            }else if(year2==year1 && month2>month1){
                $("#Exclusive1").html('$' + newPrice);
                $("#updatedprice").val(newPrice);
                if(parseInt(purchase_type)==1){
                    myDate.setFullYear(myDate.getFullYear() + parseInt(inputval));
                    $("#licensedate").text(myDate.getFormatDate());
                }else if(parseInt(purchase_type)==2){
                    myDate.setMonth(myDate.getMonth() + parseInt(inputval));
                    $("#licensedate").text(myDate.getFormatDate());
                }
                
            }else if(month2==month1 && day2>=day1){
                $("#Exclusive1").html('$' + newPrice);
                $("#updatedprice").val(newPrice);
                if(parseInt(purchase_type)==1){
                    myDate.setFullYear(myDate.getFullYear() + parseInt(inputval));
                    $("#licensedate").text(myDate.getFormatDate());
                }else if(parseInt(purchase_type)==2){
                    myDate.setMonth(myDate.getMonth() + parseInt(inputval));
                    $("#licensedate").text(myDate.getFormatDate());
                }
            }else{
               // alert(year2+'-'+year1);
               // alert(month2+'-'+month1);
               // alert(day2+'-'+day1);
                $("#buyforyear").val('');
                $("#priceTag").val(MainPrice);
                if(parseInt(purchase_type)==1){
                  alert("Number Of Years for licensing should not be exceed Right expiry date")
                }else if(parseInt(purchase_type)==2){
                  
                     alert("Number Of Months for licensing should not be exceed Right expiry date")
                }
            } 
            
 
            
        });

        Date.prototype.getFormatDate = function () {
            var monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];
            return this.getDate() + ' ' + monthNames[this.getMonth()] + ', ' + this.getFullYear();
        }


        $('.licensing').on('click', function () {
            var sel = $(this).val();
            if (sel == '1') {
                $("#rightTime").html('Number Of Years for licensing');
                $("#rightType").html('Exclusive Cost');
                var rightPrice = "{{@$rightPrice}}";

                $("#priceTag").val(rightPrice);
               // $("#buyforyear").val('1');
                $("#Exclusive1").html('$' + rightPrice);
                $("#updatedprice").val(rightPrice);
                $("#rightTypeName").text("exclusive");
                $("#onlyExlText").text("This license is exclusive to Licensee and Licensor agrees not to enter into any future Software license obligations or licenses with any third-parties without the prior written consent of Licensee."); 
                $("#onlyExclon").text("Licensor represents and warrants that the Software and the Intellectual Property Rights therein are free and clear of all encumbrances, including, without limitation, security interests, licenses, liens, charges or other restrictions.");

            } else if (sel == '2') {

                $("#rightTime").html('Number Of Months for licensing');
                $("#rightType").html('Non-Exclusive Cost');
                var rightPrice = "{{@$rightPrice2}}";
                $("#priceTag").val(rightPrice);

                //$("#buyforyear").val('1');
                $("#Exclusive1").html('$' + rightPrice);
                $("#updatedprice").val(rightPrice);
                $("#rightTypeName").text("non-exclusive");
                $("#onlyExlText").text(""); 
                $("#onlyExclon").text("");
            }
        });



    });
</script>
<style>
    .user-head h4 {
        color: #3eb9ce;
        text-decoration: underline;
    }
    .license {background: #fff;}
    .user-head {
        padding: 10px 15px;
        background: #fff;
        margin: 10px;
    }
    .user-head td {
        color: #666;
        font-size: 14px;
    }

</style>
<script src="{{ asset('frontend/inside/js/rights_details.js')}}"></script>
@endsection