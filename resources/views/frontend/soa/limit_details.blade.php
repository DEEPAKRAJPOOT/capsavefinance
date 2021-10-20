<table class="table  table-td-right">
    <tbody>
        <tr>
            <td class="text-left" width="30%"><b>Business Name</b></td>
            <td> {{ (isset($userInfo->biz)) ? $userInfo->biz->biz_entity_name : '' }} </td> 
                <td class="text-left" width="30%"><b>Full Name</b></td>
            <td>{{$userInfo->f_name}} {{$userInfo->m_name}}	{{$userInfo->l_name}}</td> 
            
        </tr>
        <tr>
            <td class="text-left" width="30%"><b>Email</b></td>
            <td>{{$userInfo->email}}	</td> 
                <td class="text-left" width="30%"><b>Mobile</b></td>
            <td>{{$userInfo->mobile_no}} </td> 
        </tr>
        
        <tr>
            <td class="text-left" width="30%"><b>Product Limit</b></td>
            <td>{{ $userInfo->total_limit }} </td> 
            <td class="text-left" width="30%"><b>Utilize Product Limit</b></td>
            <td>{{  $userInfo->consume_limit }} </td> 
        </tr>
        <tr>
            <td class="text-left" width="30%"><b>Remaining Product Limit</b></td>
            <td>{{ $userInfo->utilize_limit }} </td> 
            <td class="text-left" width="30%"><b>Sales Manager</b></td>
            <td>{{ (isset($userInfo->anchor->salesUser)) ? $userInfo->anchor->salesUser->f_name.' '.$userInfo->anchor->salesUser->m_name.' '.$userInfo->anchor->salesUser->l_name : '' }} </td>
        </tr>
        @if( isset($maxDPD) && $maxDPD>0)
        <tr>
            <td class="text-left" width="30%"><b>Max Principal DPD</b></td>
            <td>@if($maxPrincipalDPD){{$maxPrincipalDPD->dpd}} @if($maxPrincipalDPD->dpd>1) Days @else Day @endif @else 0 Day @endif</td>
            <td class="text-left" width="30%"><b>Max Interest DPD</b></td>
            <td>@if($maxInterestDPD){{$maxInterestDPD->dpd}} @if($maxInterestDPD->dpd>1) Days @else Day @endif @else 0 Day @endif </td>
        </tr>
        @endif
        @if($userInfo->outstandingAmt || $userInfo->marginOutstandingAmt || $userInfo->nonfactoredOutstandingAmt || $userInfo->unsettledPaymentAmt)
        <tr>
            <td class="text-left" width="30%"><b>@if($userInfo->outstandingAmt) Outstanding Amt @endif</b></td>
            <td>
                @if($userInfo->outstandingAmt)
                <a href="{{route('apport_unsettled_view', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}"> {{ $userInfo->outstandingAmt }} </a>
                @endif
            </td>
            <td class="text-left" width="30%"><b>@if($userInfo->marginOutstandingAmt) Margin Outstanding Amt @endif</b></td>
            <td>
                @if($userInfo->marginOutstandingAmt)
                <a href="{{route('apport_unsettled_view', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}">{{ $userInfo->marginOutstandingAmt }}</a>
                @endif
            </td>
        </tr>
        <tr>
            <td class="text-left" width="30%"><b>@if($userInfo->nonfactoredOutstandingAmt) Non-Factored Outstanding Amt @endif</b></td>
            <td>
                @if($userInfo->nonfactoredOutstandingAmt)
                <a href="{{route('apport_unsettled_view', ['user_id' => request()->get('user_id'), 'sanctionPageView' => true])}}">{{ $userInfo->nonfactoredOutstandingAmt }}</a>
                @endif
            </td>
            <td class="text-left" width="30%"><b> @if($userInfo->unsettledPaymentAmt) Unallocated Payment Amt @endif</b></td>
            <td>
                @if($userInfo->unsettledPaymentAmt)
                <a href="{{route('unsettled_payments', ['user_id' => request()->get('user_id')])}}">{{ $userInfo->unsettledPaymentAmt }}</a>
                @endif
            </td>
        </tr>
        @endif
    </tbody>
</table>