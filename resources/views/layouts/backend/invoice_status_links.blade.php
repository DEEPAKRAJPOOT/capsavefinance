<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item ">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_invoice') active @endif"  href="{{Route('backend_get_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Pending</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_invoice') active @endif"  href="{{Route('backend_get_invoice')}}">Pending</a>
        @endif
    </li>
    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_approve_invoice') active @endif"  href="{{Route('backend_get_approve_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Approved</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_approve_invoice') active @endif"  href="{{Route('backend_get_approve_invoice')}}">Approved</a>
        @endif
    </li>
    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Disbursement Queue</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed_invoice') active @endif"  href="{{Route('backend_get_disbursed_invoice')}}">Disbursement Queue</a>
        @endif
    </li>

    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_sent_to_bank') active @endif" href="{{Route('backend_get_sent_to_bank',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Sent to Bank</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_sent_to_bank') active @endif" href="{{Route('backend_get_sent_to_bank')}}">Sent to Bank</a>
        @endif
    </li>
    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_failed_disbursment') active @endif" href="{{Route('backend_get_failed_disbursment',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Failed Disbursement</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_failed_disbursment') active @endif" href="{{Route('backend_get_failed_disbursment')}}">Failed Disbursement</a>
        @endif
    </li>
    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed') active @endif" href="{{Route('backend_get_disbursed',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Disbursed</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_disbursed') active @endif" href="{{Route('backend_get_disbursed')}}">Disbursed</a>
        @endif
    </li>
    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Repaid</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_repaid_invoice') active @endif" href="{{Route('backend_get_repaid_invoice')}}">Repaid</a>
        @endif
    </li>
    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_reject_invoice') active @endif" href="{{Route('backend_get_reject_invoice',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Reject</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_reject_invoice') active @endif" href="{{Route('backend_get_reject_invoice')}}">Reject</a>
        @endif
    </li>

    <li class="nav-item">
        @if($flag == 1)
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_exception_cases') active @endif" href="{{Route('backend_get_exception_cases',[ 'user_id' => $userInfo->user_id, 'app_id' => $userInfo->app->app_id, 'flag' => 1 ])}}">Exception Cases</a>
        @else
        <a class="nav-link @if(Route::currentRouteName()=='backend_get_exception_cases') active @endif" href="{{Route('backend_get_exception_cases')}}">Exception Cases</a>
        @endif
    </li>
</ul>