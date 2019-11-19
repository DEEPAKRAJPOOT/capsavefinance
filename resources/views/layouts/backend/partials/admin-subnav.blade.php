<ul class="main-menu">
    <li>
        <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="active">Application details</a>
    </li>
    <li>
        <a href="{{ route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">CAM</a>
    </li>
    <li>
        <a href="#">FI/RCU</a>
    </li>
    <li>
        <a href="#">Collateral</a>
    </li>
    <li>
        <a href="{{ route('notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}">Notes</a>
    </li>
    <li>
        <a href="#">Submit Commercial</a>
    </li>
</ul>