

    @foreach($childs as $key1 => $childData)
    @php $childDatas = Helpers::getByParent($childData['id'],'1')->toArray() @endphp
    @php $checked1 = '' @endphp
    @php $rr1 = Helpers::checkRole($childData['id'], $role_id) @endphp
    @if($rr1)

    @if( $rr1->permission_id == $childData['id'] && $rr1->role_id == $role_id)
    @php $checked1 = 'checked' @endphp
    @endif
    @endif
    <ul>
    <li >
            <input class="c-chk-{{$ParentData['id']}} rere" {{$checked1}} type="checkbox" name="child[{{$childData['id']}}]" id="permission_id[{{$ParentData['id']}}][{{$childData['id']}}]" value="{{$childData['id']}}">{{$childData['display_name']}}
        
    

    @if($childDatas)
    @include('backend.acl.manageChild',['childs' => $childDatas])
    
    @endif
    </li>
    </ul>
    @endforeach

