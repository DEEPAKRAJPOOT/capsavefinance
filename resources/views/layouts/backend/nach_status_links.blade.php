<ul class="nav nav-tabs custom-tab" role="tablist">
    @can('nach_repayment_list')
    <li class="nav-item">
        <a class="invoiceLinkHover nav-link @if(Route::currentRouteName()=='nach_repayment_list') active @endif"  href="{{Route('nach_repayment_list')}}">Pending Request</a>
        
    </li>
    @endcan
    @can('nach_repayment_trans_list')
    <li class="nav-item">
        <a class="invoiceLinkHover nav-link @if(Route::currentRouteName()=='nach_repayment_trans_list') active @endif"  href="{{Route('nach_repayment_trans_list')}}">Send to Bank</a>        
    </li>
    @endcan
</ul>