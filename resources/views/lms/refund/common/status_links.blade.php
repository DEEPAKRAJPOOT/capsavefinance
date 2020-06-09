<ul class="nav nav-tabs" role="tablist">
    @can('lms_refund_new')
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_new') active @endif"  href="{{Route('lms_refund_new')}}">New</a>
    </li>
    @endcan
    @can('lms_refund_pending')
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_pending') active @endif"  href="{{Route('lms_refund_pending')}}">Pending</a>
    </li>
    @endcan
    @can('lms_refund_approved')
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_approved') active @endif"  href="{{Route('lms_refund_approved')}}">Approved</a>
    </li>
    @endcan
    @can('request_list')
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='request_list') active @endif"  href="{{Route('request_list')}}">Disbursed Queue</a>
    </li>
    @endcan
    @can('lms_refund_sentbank')
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_sentbank') active @endif" href="{{Route('lms_refund_sentbank')}}">Sent to Bank</a>
    </li>
    @endcan
    @can('lms_refund_refunded')
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_refunded') active @endif" href="{{Route('lms_refund_refunded')}}">Refunded</a>
    </li>
    @endcan
</ul>
<style>
    .itemBackground 
    { 
        border: 1px solid #199e75; 
        background-color: #138864;
    }
    .itemBackgroundColor 
    { 
        color:white;
    }
    .invoiceLinkHover:hover {
        color: #fff;
        background-color: #0d83ca;
        border-color: #0c7bbe;
    }
</style>