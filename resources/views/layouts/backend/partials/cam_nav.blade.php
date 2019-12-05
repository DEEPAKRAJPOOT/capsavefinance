 <?php
    $route_name = \Request::route()->getName();
  ?>
 <ul class="sub-menu-main pl-0 m-0">
     @can('cam_overview')
        <li>
            <a href="{{route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{ $route_name == 'cam_overview' ? 'active' : ''}}">Overview</a>
        </li>
        @endcan
    
        <li>
            <a href="#" class="{{$route_name == 'cam_anchor' ? 'active' : '' }}">Anchor</a>
        </li>
       
     @can('cam_promoter')
        <li>
            <a href="{{route('cam_promoter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_promoter' ? 'active' : '' }}">Promoter</a>
        </li>
        @endcan
     @can('cam_cibil')
        <li>
            <a href="{{route('cam_cibil', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_cibil' ? 'active' : '' }}">Credit History &amp; Hygine Check</a>
        </li>
        @endcan
     @can('cam_bank')
        <li>
            <a href="{{route('cam_bank', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_bank' ? 'active' : '' }}">Banking</a>
        </li>
        @endcan
     @can('cam_finance')
        <li>
            <a href="{{ route('cam_finance', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'cam_finance' ? 'active' : '' }}">Financial</a>
        </li>
         @endcan
     @can('cam_gstin')
    
        <li>
            <a href="{{ route('cam_gstin', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'cam_gstin' ? 'active' : '' }}">GST/Ledger Detail</a>
        </li>
        @endcan
     @can('limit_assessment')
    
        <li>
            <a href="{{ route('limit_assessment', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'limit_assessment' ? 'active' : '' }}">Limit Assessment</a>
        </li>
        @endcan
     
    
        <li>
            <a href="#" class="{{$route_name == 'cam_gst' ? 'active' : '' }}">Limit Management</a>
        </li>
       
     
    </ul>