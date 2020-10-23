<ul class="nav nav-tabs custom-tab" role="tablist">
    @can('nach_repayment_list')
    <li class="nav-item">
        <a class="invoiceLinkHover nav-link @if(Route::currentRouteName()=='nach_repayment_list') active @endif"  href="{{Route('nach_repayment_list')}}">Pending</a>
        
    </li>
    @endcan
   
    <li class="nav-item">
        
        <a class="invoiceLinkHover nav-link @if(Route::currentRouteName()=='nach_repayment_list') active @endif"  href="#">Send to Bank</a>
        
    </li>
    
    @can('nach_repayment_trans_list')
    <li class="nav-item">
        <a class="invoiceLinkHover nav-link @if(Route::currentRouteName()=='nach_repayment_trans_list') active @endif"  href="{{Route('nach_repayment_trans_list')}}">Approved</a>        
    </li>
    @endcan
</ul>