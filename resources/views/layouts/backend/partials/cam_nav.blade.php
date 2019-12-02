 <?php
    $route_name = \Request::route()->getName();
  ?>
 <ul class="sub-menu-main pl-0 m-0">
        <li>
            <a href="{{route('cam_overview', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{ $route_name == 'cam_overview' ? 'active' : ''}}">Overview</a>
        </li>
        <li>
            <a href="#" class="{{$route_name == 'cam_anchor' ? 'active' : '' }}">Anchor</a>
        </li>
        <li>
            <a href="{{route('cam_promoter', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_promoter' ? 'active' : '' }}">Promoter</a>
        </li>
        <li>
            <a href="{{route('cam_cibil', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_cibil' ? 'active' : '' }}">Credit History &amp; Hygine Check</a>
        </li>
        <li>
            <a href="{{route('cam_bank', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')])}}" class="{{$route_name == 'cam_bank' ? 'active' : '' }}">Banking</a>
        </li>
        <li>
            <a href="{{ route('cam_finance', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'cam_finance' ? 'active' : '' }}">Financial</a>
        </li>
        <li>
            <a href="{{ route('cam_gstin', ['app_id' => request()->get('app_id'), 'biz_id' => request()->get('biz_id')]) }}" class="{{$route_name == 'cam_gstin' ? 'active' : '' }}">GST/Ledger Detail</a>
        </li>
        <li>
            <a href="#" class="{{$route_name == 'cam_gst' ? 'active' : '' }}">Limit Assessment</a>
        </li>
        <li>
            <a href="#" class="{{$route_name == 'cam_gst' ? 'active' : '' }}">Limit Management</a>
        </li>
    </ul>