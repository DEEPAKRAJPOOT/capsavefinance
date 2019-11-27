<ul class="main-menu">
    <li>
        <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ (request()->is('application/company-details') || request()->is('application/promoter-details') || request()->is('application/documents')) ? 'active' : '' }}">Application details</a>
    </li>
    <li>
        <a href="{{ route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/cam/*') ? 'active' : '' }}">CAM</a>
    </li>
    <li>
        <a href="{{ route('fircu/index', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/fircu/*') ? 'active' : '' }}">FI/RCU</a>
    </li>
    <li>
        <a href="#" class="{{ request()->is('application/collateral/*') ? 'active' : '' }}">Collateral</a>
    </li>
    <li>
        <a href="{{ route('notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/notes') ? 'active' : '' }}">Notes</a>
    </li>
    <li>
        <a href="#">Submit Commercial</a>
    </li>
</ul>