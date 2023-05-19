<ul class="sub-menu-main pl-0 m-0">
    @can('business_details')
    <li>
        <a href="{{ route('business_details', ['userUcicId' => $ucic->user_ucic_id]) }}" class="{{ request()->route()->getName() == 'business_details' ? 'active' : ''}}">Business Information</a>
    </li>
    @endcan 
    @can('management_details')
    <li>
        <a href="{{ route('management_details', ['userUcicId' => $ucic->user_ucic_id]) }}" class="{{ request()->route()->getName() == 'management_details' ? 'active' : ''}}">Management Information</a>
    </li>
    @endcan
    @can('group_linking')
    <li>
        <a href="{{ route('group_linking', ['userUcicId' => $ucic->user_ucic_id]) }}" class="{{ request()->route()->getName() == 'group_linking' ? 'active' : ''}}">Group Linking</a>
    </li>
    @endcan
    @can('ckycdetails')
    <li>
        <a href="{{ route('ckycdetails', ['userUcicId' => $ucic->user_ucic_id,'app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id'),'user_id' => request()->get('user_id')]) }}" class="{{ request()->route()->getName() == 'ckycdetails' ? 'active' : ''}}">CKYC</a>
    </li>  
    @endcan 
</ul>