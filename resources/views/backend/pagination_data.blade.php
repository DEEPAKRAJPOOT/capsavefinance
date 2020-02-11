<div class="table-responsive">
        <table class="table table-striped table-sm">
                <thead>
                        <tr>
                                <th>S. No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile No.</th>
                                <th>Created on</th>
                                <th>KYC Status</th>
                                <th>Status</th>
                                <th>Actions</th>
                        </tr>
                </thead>
                <tbody>
                    @php $i = ($usersList->currentpage()-1)* $usersList->perpage() + 1;@endphp
                    @foreach ($usersList as $user)
                    @php $bounty = Helpers::getKycDetails($user->user_id); 
                        if(isset($bounty) && $bounty['is_kyc_completed']==1) {
                         $boun = 'Completed';
                         $boun_status = 'st_complete';
                        } else {
                         $boun = 'Pending';
                         $boun_status = 'st_not_complete';
                        }
                        if(isset($bounty) && $bounty['is_approve']==1) {
                         $boun_appr = 'Approved';
                         $boun_appr_status = 'text-approved';
                        } else {
                         $boun_appr = 'Not Approved';
                         $boun_appr_status = 'text-disapproved';
                        }
                        
                    @endphp

                        <tr>
                            <td> <a href ="{{ route("user_detail",['user_id' => $user->user_id])}}">{{ $i++ }}</a></td>
                                <td>{{ ucwords($user->f_name)." ".ucwords($user->l_name) }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_no }}</td>
                                <td>{{(isset($user->created_at) && !empty($user->created_at)) ? Helpers::getDateByFormat($user->created_at,'Y-m-d H:i:s', 'd F Y') : '' }}</td>
                                <td><span class="status-icon {{$boun_status}}"> </span> {{ isset($boun) ? $boun : '' }}</td>

                                <td><span class="{{ $boun_appr_status }}">{{ isset($boun_appr) ? $boun_appr : '' }}</span></td>
                                <!--				-->
                                <td>
                                        <div class="action-btn-i" data-toggle="dropdown">
                                                <ul>
                                                        <li></li>
                                                        <li></li>
                                                        <li></li>
                                                </ul>
                                                <ul class="show-invoice">
                                                        <li><a href="#">Get Invoice</a></li>
                                                        <li><a href="#">Contract Details</a></li>
                                                        <li><a href="#" onclick="db_blockchain()">Blockchain Details</a></li>
                                                </ul>
                                        </div>


                                                <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route("user_detail",['user_id' => $user->user_id])}}">View Detail</a>
<!--                                                        <a class="dropdown-item" href="#">Lock</a>-->
                                                </div>


                                </td>
                        </tr>
                        @endforeach
                </tbody>
        </table>

        <div class="d-md-flex align-items-center mt-4">
                <div class="ml-md-auto">
                    {!! $usersList->links() !!}
                </div>
        </div>
</div>  