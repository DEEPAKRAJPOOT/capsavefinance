<ul class="main-menu">
    <li>
        <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ (request()->is('application/company-details') || request()->is('application/promoter-details') || request()->is('application/documents')) ? 'active' : '' }}">Application details</a>
    </li>
    <li>
        <a href="{{ route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/cam/*') ? 'active' : '' }}">CAM</a>
    </li>
    <li>
        <a href="{{ route('backend_fircu_index', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/fircu/*') ? 'active' : '' }}">FI/RCU</a>
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
    @php
    $wfStageData = \Helpers::getWfStageToProcess(request()->get('app_id'));
    $wfStageToProcess = $wfStageData ? $wfStageData->stage_code : '';
    $isWfStageCompleted = \Helpers::isWfStageCompleted('sales_queue', request()->get('app_id'));    
    @endphp
    
    @if($wfStageToProcess == 'sales_queue' || $isWfStageCompleted)
    <li>
        <a href="{{ route('view_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/notes') ? 'active' : '' }}">View Offer</a>
    </li>
    @endif
</ul>