<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_new') active @endif"  href="{{Route('lms_refund_new')}}">New</a>
    </li>
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_pending') active @endif"  href="{{Route('lms_refund_pending')}}">Pending</a>
    </li>
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_approved') active @endif"  href="{{Route('lms_refund_approved')}}">Approved</a>
    </li>
    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='request_list') active @endif"  href="{{Route('request_list')}}">Refund Queue</a>
    </li>

    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_sentbank') active @endif" href="{{Route('lms_refund_sentbank')}}">Sent to Bank</a>
    </li>

    <li class="nav-item itemBackground">
        <a class="itemBackgroundColor invoiceLinkHover nav-link @if(Route::currentRouteName()=='lms_refund_refunded') active @endif" href="{{Route('lms_refund_refunded')}}">Refunded</a>
    </li>

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