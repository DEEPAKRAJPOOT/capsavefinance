<ul class="main-menu">    
    @can('company_details')
    <li>
        <a href="{{ route('company_details', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ (request()->is('application/company-details') || request()->is('application/promoter-details') || request()->is('application/documents')) ? 'active' : '' }}">Application Information</a>
    </li>
    @endcan
    @can('cam_overview')
    <li>
        <a href="{{ route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/cam/*') ? 'active' : '' }}">CAM</a>
    </li>
    @endcan
    @can('backend_fi')
    <li>
        <a href="{{ route('backend_fi', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/fircu/*') ? 'active' : '' }}">FI/RCU</a>
    </li>
    @endcan
    <!--
    <li>
        <a href="#" class="{{ request()->is('application/collateral/*') ? 'active' : '' }}">Collateral</a>
    </li>
    -->
    @can('notes_list')
    <li>
        <a href="{{ route('notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/notes') ? 'active' : '' }}">Notes</a>
    </li>
    @endcan
    <!--
    <li>
        <a href="#">Submit Commercial</a>
    </li>
    -->
    @can('pp_document_list')
     <li>
        <a href="{{ route('pp_document_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('document/list') ? 'active' : '' }}"> Documents </a>
    </li>
    @endcan
    
     <!-- <li>
        <a href="{{ route('pd_notes_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/pd-notes') ? 'active' : '' }}"> Personal Discussion </a>
    </li> -->
     
    @can('query_management_list')
    <li>
        <a href="{{ route('query_management_list', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/query-management') ? 'active' : '' }}"> QMS</a>
    </li>
    @endcan
    
    @php
        //$wfStageData = \Helpers::getWfStageToProcess(request()->get('app_id'));
        //$wfStageToProcess = $wfStageData ? $wfStageData->stage_code : '';
        //$isWfStageCompleted = \Helpers::isWfStageCompleted('sales_queue', request()->get('app_id'));    

        //$currentStage = \Helpers::getCurrentWfStage(request()->get('app_id'));   
        //$roleData = \Helpers::getUserRole();        
        //$isNavAccessible = $currentStage->role_id == $roleData[0]->id ? 1 : 0;            
    @endphp
    
    {{--@if ($currentStage->stage_code == 'sales_queue' && $isNavAccessible)--}}
    @can('view_offer')    
    <li>
        <a href="{{ route('view_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/view-offer') ? 'active' : '' }}">View Offer</a>
    </li>
    @endcan
    {{--@endif--}}

    @can('colender_view_offer')    
    <li>
        <a href="{{ route('colender_view_offer', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('colender/application/view-offer') ? 'active' : '' }}">View Co-lender Offer</a>
    </li>
    @endcan
    @php
        $appSanctionLetterDataFlag = \Helpers::appSanctionLetterStatus(request()->get('app_id'));  
        $appCurrentStatus = \Helpers::appCurrentStatus(request()->get('app_id'));
        $appData = \Helpers::appDataCurrent(request()->get('app_id')); 
        $productsArr = $appData->products->pluck('id')->toArray();
        $appSanctionLetterGenerated = \Helpers::appSanctionLetterGenerated(request()->get('app_id'));      
    @endphp
    {{--@if ($currentStage->stage_code == 'sanction_letter' && $isNavAccessible)--}}
    @can('gen_sanction_letter')
    <li>
        <a href="{{ route('gen_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/sanction-letter') ? 'active' : '' }}">Sanction Letter</a>
    </li>
    @endcan 
    @if($appSanctionLetterGenerated || $appSanctionLetterDataFlag)
    @if (in_array(1, $productsArr))
    @can('list_new_sanction_letter')
    <li>
        <a href="{{ route('list_new_sanction_letter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{ request()->is('application/new-sanction-letter','application/create-new-sanction-letter','application/view-new-sanction-letter') ? 'active' : '' }}">New Sanction Letter</a>
    </li>
    @endcan 
    @endif  
    @endif
    {{--@endif--}}
</ul>