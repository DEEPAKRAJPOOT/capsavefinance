          <table class="table  table-td-right">
                             <tbody>
                                <tr>
                                    <td class="text-left" width="30%"><b>Business Name</b></td>
                                    <td> {{$userInfo->biz->biz_entity_name}}	</td> 
                                     <td class="text-left" width="30%"><b>Full Name</b></td>
                                    <td>{{$userInfo->f_name}} {{$userInfo->m_name}}	{{$userInfo->l_name}}</td> 
                                   
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%"><b>Email</b></td>
                                    <td>{{$userInfo->email}}	</td> 
                                     <td class="text-left" width="30%"><b>Mobile</b></td>
                                    <td>{{$userInfo->mobile_no}} </td> 
                                </tr>
                                
                                <tr>
                                    <td class="text-left" width="30%"><b>Product Limit</b></td>
                                    <td>{{ $userInfo->total_limit }} </td> 
                                    <td class="text-left" width="30%"><b>Utilize Product Limit</b></td>
                                    <td>{{  $userInfo->consume_limit }} </td> 
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%"><b>Remaining Product Limit</b></td>
                                    <td>{{ $userInfo->utilize_limit }} </td> 
                                    <td class="text-left" width="30%"><b>Sales Manager</b></td>
                                    <td>{{ (isset($userInfo->anchor->salesUser)) ? $userInfo->anchor->salesUser->f_name.' '.$userInfo->anchor->salesUser->m_name.' '.$userInfo->anchor->salesUser->l_name : '' }} </td>
                                </tr>
                            </tbody>
                       
                        </table>