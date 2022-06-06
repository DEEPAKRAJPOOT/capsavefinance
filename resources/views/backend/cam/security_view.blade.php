   @php
       use Carbon\Carbon;
   @endphp
   <div class="row grid-margin mt-3 mb-2">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div id="listing" class="listing">
                                <!-- Start View Supply Chain Offer Block -->
                                <div class="card card-color mb-0">
                                        <table cellspacing="0" cellpadding="0" width="100%" class="table table-striped table-bordered">
                                            <thead>
                                                <tr role="row" style="background: #62b59b;color: #fff; text-align: left;">
                                                   <th width="7%">Doc No.</th>
                                                   <th width="93%" colspan="4">{{ $title }} Pre/Post Disbursement Details</th>
                                                   {{-- <th width="15%">Status</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($securityListingData as $key=>$listingData)
                                            @php
                                                $doctype = $listingData->doc_type;
                                                if($doctype == 1){
                                                    $doctype = 'Pre Disbursement';
                                                }else{
                                                    $doctype = 'Post Disbursement';
                                                }
                                                // dd($listingData->status == 2);
                                                @endphp
                                                @if($listingData->status ==2)
                                                <tr>
                                                    <td style="text-align: center;font-weight: 600;">{{$key+1}}</td>
                                                    <td><b>Pre/Post Disbursement: </b> </td>
                                                    <td>{{$doctype ? : 'N/A'}}</td>
                                                    <td><b>Type Of Document:  </b> </td>
                                                    <td>{{$listingData->mstSecurityDocs->name ? : 'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>Description: </b></td>
                                                    <td>{{$listingData->description}}</td>
                                                    <td><b>Original Due Date : </b></td>
                                                    <td>{{!empty($listingData->due_date) ? Carbon::parse($listingData->due_date)->format('d-m-Y') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>Created By: </b></td>
                                                        <td>{{$listingData->createdByUser ? $listingData->createdByUser->f_name.' '.$listingData->createdByUser->l_name : 'N/A'}}</td>
                                                    <td><b>Created At: </b></td>
                                                    <td>{{ !empty($listingData->created_at) ? Carbon::parse($listingData->created_at)->format('d-m-Y') : 'N/A' }}</td>
                                                </tr>
                                                @if(!$loop->last)
                                                <tr>
                                                <td style="background-color:#808080" colspan="5"></td>
                                                </tr>
                                                @endif
                                                @else
                                                <tr>
                                                    <td style="text-align: center;font-weight: 600;">{{$key+1}}</td>
                                                    <td><b>Pre/Post Disbursement: </b> </td>
                                                    <td>{{$doctype ? : 'N/A'}}</td>
                                                    <td><b>Type Of Document:  </b> </td>
                                                    <td>{{$listingData->mstSecurityDocs->name ? : 'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>Description: </b></td>
                                                    <td>{{$listingData->description}}</td>
                                                    <td><b>Original Due Date : </b></td>
                                                    <td>{{!empty($listingData->due_date) ? Carbon::parse($listingData->due_date)->format('d-m-Y') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td><b>Document Number: </b> </td>
                                                    <td>{{$listingData->document_number ? : 'N/A'}}</td>
                                                   <td><b>Completed: </b> </td>
                                                    <td>{{$listingData->completed ? : 'N/A'}}</td>
                                                </tr>

                                                <tr>
                                                <td></td>
                                                    <td><b>Exception Received: </b></td>
                                                    <td>{{$listingData->exception_received ? : 'N/A'}}</td>
                                                    <td><b>Exception Received From: </b></td>
                                                    <td>{{$listingData->exception_received_from ? : 'N/A'}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Exception Received Date: </b></td>
                                                    <td>{{!empty($listingData->exception_received_date) ? Carbon::parse($listingData->exception_received_date)->format('d-m-Y') :'N/A' }}</td>
                                                    <td><b>Exception Remark: </b></td>
                                                    <td>{{$listingData->exception_remark ? : 'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Extended Due Date: </b></td>
                                                    <td>{{!empty($listingData->extended_due_date) ? Carbon::parse($listingData->extended_due_date)->format('d-m-Y') : 'N/A' }}</td>
                                                    <td><b>Maturity Date: </b></td>
                                                    <td>{{ !empty($listingData->maturity_date) ?Carbon::parse($listingData->maturity_date)->format('d-m-Y') : 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                <td></td>
                                                    <td><b>Renewal Reminder Days: </b></td>
                                                    <td>{{$listingData->renewal_reminder_days ? : 'N/A'}}</td>
                                                    <td><b>Amount Expected: </b></td>
                                                    <td>{{$listingData->amount_expected ? : 'N/A'}}</td>
                                                </tr>
                                                <tr>
                                                        <td></td><td><b>Document Amount: </b></td>
                                                        <td>{{$listingData->document_amount ? : 'N/A'}}</td>
                                                        <td><b>Created By: </b></td>
                                                        <td>{{$listingData->createdByUser ? $listingData->createdByUser->f_name.' '.$listingData->createdByUser->l_name : 'N/A'}}</td>
                                                </tr>
                                                    <tr>
                                                    <td></td>
                                                    <td><b>Doc Upload:</b></td>
                                                    <td><a href="{{ route('download_s3_file', ['file_id' => $listingData['file_id'] ]) }}" title="Download Document"><i class="fa fa-lg fa-download ml-3" aria-hidden="true"></i></a></td>
                                                    <td><b>Created At: </b></td>
                                                    <td>{{ !empty($listingData->created_at) ? Carbon::parse($listingData->created_at)->format('d-m-Y') : 'N/A' }}</td>
                                                </tr>
                                                
                                                @if(!$loop->last)
                                                <tr>
                                                <td style="background-color:#808080" colspan="5"></td>
                                                </tr>
                                                @endif
                                                @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                </div>
                                <!-- End View Term loan Offer Block -->
                                <!-- Start View Leasing Offer Block -->
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
