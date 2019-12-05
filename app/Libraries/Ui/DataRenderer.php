<?php

namespace App\Libraries\Ui;

use DataTables;
use Helpers;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\AppAssignment;
use App\Libraries\Ui\DataRendererHelper;
use App\Contracts\Ui\DataProviderInterface;

class DataRenderer implements DataProviderInterface
{
    /**
     * Helper object for DataRenderer.
     *
     * @var \App\Libraries\Ui\DataRendererHelper
     */
    protected $helper;

    /**
     * Class constructor
     *
     * @param  void
     * @return void
     */
    public function __construct()
    {
        $this->helper = new DataRendererHelper();
    }

    /**
     * Initializationcreated_at
     *
     * @param  void
     * @return void
     */
    public function init()
    {
        //
    }
    
    /*
     * 
     * get all country list
     */
    public function getUsersList(Request $request, $user)
    {
        return DataTables::of($user)
                ->rawColumns(['id', 'checkbox', 'action', 'email','assigned'])
                ->addColumn(
                    'id',
                    function ($user) {
                    $link = '000'.$user->user_id;
                        return "<a id=\"" . $user->user_id . "\" href=\"".route('lead_detail', ['user_id' => $user->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                        
                    }
                )
                ->editColumn(
                        'name',
                        function ($user) {
                    $full_name = $user->f_name.' '.$user->l_name;
                    return $full_name;
                    
                })
                ->editColumn(
                    'email',
                    function ($user) {
                    return "<a  data-original-title=\"Edit User\"  data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";

                })
                ->editColumn(
                    'anchor',
                    function ($user) {                    
                    if($user->UserAnchorId){
                      $userInfo=User::getUserByAnchorId($user->UserAnchorId);
                       $achorId= $userInfo->f_name.''.$userInfo->l_name;
                    }else{
                      $achorId='';  
                    }
                    //$achorId = $user->UserAnchorId; 
                    return $achorId;
                })
                ->editColumn(
                    'userType',
                    function ($user) {
                    if($user->AnchUserType==1){
                        $achorUserTpe='Supplier';
                    }else if($user->AnchUserType==2){
                         $achorUserTpe='Buyer';
                    }else{
                        $achorUserTpe='';
                    }
                    //$achorUserTpe = $user->AnchUserType; 
                    return $achorUserTpe;
                })
                ->editColumn(
                    'salesper',
                    function ($user) {
                    if($user->to_id){
                    $userInfo=Helpers::getUserInfo($user->to_id);                    
                       $saleName=$userInfo->f_name. ''.$userInfo->l_name;  
                    }else{
                       $saleName=''; 
                    } 
                    return $saleName;
                })
                ->editColumn(
                    '',
                    function ($user) {
                    $full_name = $user->mobile_no; 
                    return $full_name;
                })
                ->editColumn(
                        'assigned',
                        function ($user) {
                    if ($user->is_assign == 0) {
                        return "<label class=\"badge badge-warning current-status\">Pending</label>";
                    } else {
                        return "<span style='color:green'>Assigned</span>";
                    }
                })
                ->editColumn(
                    'biz_name',
                    function ($user) {
                    return ($user->biz_name)? $user->biz_name: '---';

                })
                ->editColumn(
                    'created_at',
                    function ($user) {
                    return ($user->created_at)? date('d-M-Y',strtotime($user->created_at)) : '---';

                })
                ->addColumn(
                    'action',
                    function ($users) {
                    return  "<a title=\"edit Lead\"  data-toggle=\"modal\" data-target=\"#editLead\" data-url =\"" . route('edit_backend_lead', ['user_id' => $users->user_id]) . "\" data-height=\"230px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-warning btn-sm  report-btn btn-x-sm\" title=\"Edit Lead Detail\"><i class=\"fa fa-edit\"></a>";
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_email') != '') {
                        if ($request->has('by_email')) {
                            $query->where(function ($query) use ($request) {
                                $by_nameOrEmail = trim($request->get('by_email'));
                                $query->where('users.f_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('users.l_name', 'like', "%$by_nameOrEmail%")
                                //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                            });
                        }
                    }
                    if ($request->get('is_assign') != '') {
                        if ($request->has('is_assign')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('is_assign'));
                                
                                $query->where('users.is_assigned', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    
    /*      
     * Get application list
     */
    public function getAppList(Request $request, $app)
    {
        return DataTables::of($app)
                ->rawColumns(['app_id','assignee', 'assigned_by', 'action'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                    
                    if(Helpers::checkPermission('company_details')){
                        $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . $app->app_id . "</a> ";
                    }else{
                        return "<a id=\"app-id-" . $app->app_id . "\" rel=\"tooltip\">" . $app->app_id . "</a> ";
                    }
                    
                        
                    }
                )
                ->addColumn(
                    'biz_entity_name',
                    function ($app) {                        
                        return $app->biz_entity_name ? $app->biz_entity_name : '';
                })
                ->addColumn(
                    'assoc_anchor',
                    function ($app) {
                        //return "<a  data-original-title=\"Edit User\" href=\"#\"  data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";
                        
                    //if($app->anchor_id){
                    //  $userInfo=User::getUserByAnchorId($app->anchor_id);
                    //   $achorName= $userInfo->f_name.''.$userInfo->l_name;
                    //}else{
                    //  $achorName='';  
                    //}                    
                    //return $achorName;
                    return isset($app->assoc_anchor) ? $app->assoc_anchor : '';
                })
                ->addColumn(
                    'user_type',
                    function ($app) {
                    if($app->user_type && $app->user_type==1){
                       $anchorUserType='Supplier'; 
                    }else if($app->user_type && $app->user_type==2){
                        $anchorUserType='Buyer';
                    }else{
                        $anchorUserType='';
                    }
                       return $anchorUserType;
                })                
                ->addColumn(
                    'assignee',
                    function ($app) {                    
                    //if($app->to_id){
                    //$userInfo=Helpers::getUserInfo($app->to_id);                    
                    //   $assignName=$userInfo->f_name. ''.$userInfo->l_name;  
                    //}else{
                    //   $assignName=''; 
                    //} 
                    //    return $assignName;
                    return $app->assignee ? $app->assignee . '<br><small>(' . $app->assignee_role . ')</small>' : '';
                })
                ->addColumn(
                    'assigned_by',
                    function ($app) {
                        //return $app->assigned_by ? $app->assigned_by . '<br>(' . $app->from_role . ')' : '';
                        $fromData = AppAssignment::getOrgFromUser($app->app_id);
                        return isset($fromData->assigned_by) ? $fromData->assigned_by . '<br><small>(' . $fromData->from_role . ')</small>' : '';
                })                
                ->addColumn(
                    'shared_detail',
                    function ($app) {
                    return '';

                })
                ->addColumn(
                    'status',
                    function ($app) {
                    //$app_status = config('inv_common.app_status');                    
                    return $app->status == 1 ? 'Completed' : 'Incomplete';

                })
                ->addColumn(
                    'action',
                    function ($app) use ($request) {
                        $act = '';
                        if(Helpers::checkPermission('add_app_note')){
                            $act = $act . '<a title="Add App Note" href="#" data-toggle="modal" data-target="#addCaseNote" data-url="' . route('add_app_note', ['app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="170px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>';
                        }
                        if(Helpers::checkPermission('send_case_confirmBox')){
                            $act = $act . '&nbsp;<a href="#" title="Assign Case" data-toggle="modal" data-target="#sendNextstage" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="300px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
                           
                        }
                        return $act;
                                      
                    }
                )
                ->filter(function ($query) use ($request) {
                    
                    if ($request->get('search_keyword') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('app.app_id', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    if ($request->get('is_assign') != '') {
                        $query->where(function ($query) use ($request) {
                            $is_assigned = $request->get('is_assign');
                            $query->where('app.is_assigned', $is_assigned);
                        });
                    }
                    
                })
                ->make(true);
    }

    /*      
     * Get user application list for frontend
     */
    public function getUserAppList(Request $request, $app)
    {
        return DataTables::of($app)
                ->rawColumns(['app_id', 'action', 'status'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return $app->app_id;
                        //return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . $app->app_id . "</a> ";
                    }
                )
                ->addColumn(
                    'biz_entity_name',
                    function ($app) {                        
                        return $app->biz_entity_name ? $app->biz_entity_name : '';
                })
                ->addColumn(
                    'user_name',
                    function ($app) {                        
                        return $app->f_name.' '.$app->m_name.' '.$app->l_name;
                })
                ->addColumn(
                    'user_email',
                    function ($app) {                        
                        return $app->email;
                })
                ->addColumn(
                    'user_phone',
                    function ($app) {                        
                        return $app->mobile_no;
                })
                ->addColumn(
                    'assoc_anchor',
                    function ($app) {                        
                     if($app->anchor_id){
                    $userInfo=User::getUserByAnchorId($app->anchor_id);
                       $achorName= ($userInfo)? ucwords($userInfo->f_name.' '.$userInfo->l_name): 'NA';
                    }else{
                      $achorName='';  
                    }                    
                    return $achorName;
                })
                ->addColumn(
                    'applied_loan_amount',
                    function ($app) {
                    return $app->loan_amt ? number_format($app->loan_amt) : '';
                })                
                ->addColumn(
                    'created_at',
                    function ($app) {                    
                    return $app->created_at ? date('d/m/Y', strtotime($app->created_at)) : '';
                })
                ->addColumn(
                    'status',
                    function ($app) {
                    //$app_status = config('inv_common.app_status');                    
                    return '<label class="badge '.(($app->status == 1)? "badge-primary":"badge-warning").'">'.(($app->status == 1)? "Completed":"Incomplete").'</label>';

                })
                ->addColumn(
                    'action',
                    function ($app) use ($request) {
                                /*<a href="#" title="View Offered Limit" data-toggle="modal" data-target="#ViewOfferedLimit" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="200px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm">View Offered Limit</a>*/
                        return '<div class="d-flex inline-action-btn">
                                <a href="'.route('business_information_open', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]).'" title="View Application" class="btn btn-action-btn btn-sm">View Application</a>
                            </div>';
                    }
                )
                ->filter(function ($query) use ($request) {
                    
                    if ($request->get('search_keyword') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('app.app_id', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    if ($request->get('is_status') != '') {
                        $query->where(function ($query) use ($request) {
                            $is_assigned = $request->get('is_status');
                            $query->where('app.status', $is_assigned);
                        });
                    }
                    
                })
                ->make(true);
    }  
    
    /*
     * get application pool
     * 
     */
    
    public function getAppLicationPool(Request $request, $app)
    {
        return DataTables::of($app)
                ->rawColumns(['app_id', 'action'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        //$link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id, 'user_id' => $app->user_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" rel=\"tooltip\">" . 'CAPS000'.$app->app_id . "</a> ";
                    }
                )
                ->addColumn(
                    'biz_entity_name',
                    function ($app) {                        
                        return $app->biz_entity_name ? $app->biz_entity_name : '';
                })
                ->addColumn(
                    'assoc_anchor',
                    function ($app) {                        
                        return $app->assoc_anchor ? $app->assoc_anchor : '';
                })
                ->addColumn(
                    'user_type',
                    function ($app) {                        
                    if($app->user_type && $app->user_type==1){
                       $anchorUserType='Supplier'; 
                    }else if($app->user_type && $app->user_type==2){
                        $anchorUserType='Buyer';
                    }else{
                        $anchorUserType='';
                    }
                       return $anchorUserType;
                })
                ->addColumn(
                    'assigned_by',
                    function ($app) {
                        return isset($app->assigned_by) ? $app->assigned_by : '';
                })                
                ->addColumn(
                    'assignee',
                    function ($app) {
                        return isset($app->assignee) ? $app->assignee : '';
                })
                ->addColumn(
                    'shared_detail',
                    function ($app) {
                    return isset($app->sharing_comment) ? $app->sharing_comment : '';

                })
                ->addColumn(
                    'status',
                    function ($app) {
                 return $app->status == 1 ? 'Completed' : 'Incomplete';

                })
                ->addColumn(
                    'action',
                    function ($app) {
                        $act = '';
                     if(Helpers::checkPermission('confirm_box')){
                        $act = "<div class=\"d-flex inline-action-btn\">
                        <a title=\"Pick Lead\"  data-toggle=\"modal\" data-target=\"#pickLead\" data-url =\"" . route('confirm_box', ['user_id' => $app->user_id , 'app_id' => $app->app_id] ) . "\" data-height=\"150px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\">Pickup Case</a>
                <div>";
                    }
                     return $act;
                   
              }
                )
                ->filter(function ($query) use ($request) {
                    
                    if ($request->get('search_keyword') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('app.app_id', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    if ($request->get('is_assign') != '') {                                 
                    }                    
                    
                })
                ->make(true);
    } 
    
    
    public function getAnchorList(Request $request, $user)
    {
        
        return DataTables::of($user)
                ->rawColumns(['anchor_id', 'checkbox', 'action', 'email','assigned'])
                ->addColumn(
                    'anchor_id',
                    function ($user) {
                    $link = '000'.$user->anchor_id;
                    return $link;
                      // return "<a id=\"" . $user->user_id . "\" href=\"".route('lead_detail', ['user_id' => $user->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                        
                    } )
                ->editColumn(
                        'name',
                        function ($user) {
                    $full_name = $user->f_name;
                    return $full_name;
                    
                })              
                ->editColumn(
                    'biz_name',
                    function ($user) {
                    $comp_name = $user->comp_name; 
                    return $comp_name;

                })
                ->editColumn(
                    'email',
                    function ($user) {
                    return "<a  data-original-title=\"Edit User\"  data-placement=\"top\" class=\"CreateUser\" >".$user->comp_email."</a> ";

                })
                ->editColumn(
                    'phone',
                    function ($user) {
                    $achorId = $user->comp_phone; 
                    return $achorId;
                }) 
                ->editColumn(
                    'created_at',
                    function ($user) {
                    return ($user->created_at)? date('d-M-Y',strtotime($user->created_at)) : '---';

                })
                ->addColumn(
                    'action',
                    function ($users) {
                       $act = '';
                     if(Helpers::checkPermission('edit_anchor_reg')){
                        $act = "<a  data-toggle=\"modal\" data-target=\"#editAnchorFrm\" data-url =\"" . route('edit_anchor_reg', ['anchor_id' => $users->anchor_id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Anchor Detail\"><i class=\"fa fa-edit\"></a>";
                     }
                     return $act;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_email') != '') {
                        if ($request->has('by_email')) {
                            $query->where(function ($query) use ($request) {
                                $by_nameOrEmail = trim($request->get('by_email'));
                                $query->where('users.f_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('users.l_name', 'like', "%$by_nameOrEmail%")
                                //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                            });
                        }
                    }
                    if ($request->get('is_assign') != '') {
                        if ($request->has('is_assign')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('is_assign'));
                                
                                $query->where('users.is_assigned', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    
    public function getAnchorLeadList(Request $request, $user)
    {
        
        return DataTables::of($user)
                ->rawColumns(['id', 'checkbox', 'action', 'email','assigned', 'status'])
                ->addColumn(
                    'id',
                    function ($user) {
                    $link = '000'.$user->anchor_user_id;
                        //return "<a id=\"" . $user->user_id . "\" href=\"".route('lead_detail', ['user_id' => $user->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                        
                    }
                )
                ->editColumn(
                        'name',
                        function ($user) {
                    $full_name = $user->name.' '.$user->l_name;
                    return $full_name;
                    
                })               
                ->editColumn(
                    'biz_name',
                    function ($user) {
                    $biz_name = $user->biz_name;
                    return $biz_name;

                })
                ->editColumn(
                    'email',
                    function ($user) {
                    return "<a  data-original-title=\"Edit User\"  data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";

                })
                ->editColumn(
                    'phone',
                    function ($user) {
                    $achorId = $user->phone; 
                    return $achorId;
                })
                ->editColumn(
                    'created_at',
                    function ($user) {
                    return ($user->created_at)? date('d-M-Y',strtotime($user->created_at)) : '---';

                })
                ->addColumn(
                    'status',
                    function ($users) {
                    if($users->is_registered==1){
                       return "<label class=\"badge badge-success current-status\">Registered</label>";
                    } else {
                        return "<label class=\"badge badge-warning current-status\">Unregistered</label>";
                    }
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_email') != '') {
                        if ($request->has('by_email')) {
                            $query->where(function ($query) use ($request) {
                                $by_nameOrEmail = trim($request->get('by_email'));
                                $query->where('users.f_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('users.l_name', 'like', "%$by_nameOrEmail%")
                                //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                            });
                        }
                    }
                    if ($request->get('is_assign') != '') {
                        if ($request->has('is_assign')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('is_assign'));
                                
                                $query->where('users.is_assigned', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    
   
    
    public function getRoleList(Request $request, $role)
    {
        
        return DataTables::of($role)
                ->rawColumns(['role_id', 'checkbox', 'action', 'active','assigned'])
                
                ->addColumn(
                    'role_id',
                    function ($role) {
                    $link = '000'.$role->id;
                       return "<a id=\"" . $role->user_id . "\" href=\"#\" rel=\"tooltip\"   >$link</a> ";
                    })
                    
                ->editColumn(
                        'name',
                        function ($role) {
                    $name = $role->name;
                    return $name;
                    
                })              
                ->editColumn(
                    'description',
                    function ($role) {
                    $disc = $role->description; 
                    return $disc;

                })
                 ->editColumn(
                    'active',
                    function ($role) {
                    return ($role->is_active == '0')?'<div class="btn-group ">
                                             <label class="badge badge-warning current-status">In Active</label>
                                             
                                          </div></b>':'<div class="btn-group ">
                                             <label class="badge badge-warning current-status">Active</label>
                                             
                                          </div></b>';

                })
                
                ->editColumn(
                    'created_at',
                    function ($role) {
                    return ($role->created_at)? date('d-M-Y',strtotime($role->created_at)) : '---';

                })
                
                ->addColumn(
                    'action',
                    function ($role) {
                    return  "<a title=\"Edit Role\" data-toggle=\"modal\" data-target=\"#addRoleFrm\" data-url =\"" . route('add_role', ['role_id' => $role->id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-edit\"></i></a> &nbsp; <a title=\"Manage Permission\" id=\"" . $role->id . "\" href =\"" . route('manage_role_permission', ['role_id' => $role->id, 'name' =>$role->name ]) . "\" rel=\"tooltip\"   > <i class='fa fa-2x fa-cog'></i></a>";
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->get('by_email') != '') {
                            if ($request->has('by_email')) {
                                $query->where(function ($query) use ($request) {
                                    $by_nameOrEmail = trim($request->get('by_email'));
                                    $query->where('users.f_name', 'like',"%$by_nameOrEmail%")
                                    ->orWhere('users.l_name', 'like', "%$by_nameOrEmail%")
                                    //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                    ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                                });
                            }
                        }
                        if ($request->get('is_assign') != '') {
                            if ($request->has('is_assign')) {
                                $query->where(function ($query) use ($request) {
                                    $by_status = (int) trim($request->get('is_assign'));
                                    
                                    $query->where('users.is_assigned', 'like',
                                            "%$by_status%");
                                });
                            }
                        }
                    })
                    ->make(true);
                
    }
    /**
     * Get user wise role
     * 
     * @param Request $request
     * @param type $role
     * @return type
     */
    public function getUserRoleList(Request $request, $role)
    {
        
        return DataTables::of($role)
                ->rawColumns(['role_id', 'checkbox', 'action', 'active','assigned'])
                
                ->addColumn(
                    'srno',
                    function ($role) {
                      return "==";
                    })
                    
                ->editColumn(
                        'name',
                        function ($role) {
                    $name = $role->f_name.' '.$role->l_name ;
                    return $name;
                    
                })              
                ->editColumn(
                    'email',
                    function ($role) {
                    $disc = $role->email; 
                    return $disc;

                })
                ->editColumn(
                    'mobile',
                    function ($role) {
                    $disc = $role->mobile_no; 
                    return $disc;

                })
                ->editColumn(
                    'rolename',
                    function ($role) {
                    $disc = $role->name; 
                    return $disc;

                })
                ->editColumn(
                    'active',
                    function ($role) {
                    $disc = ($role->u_active == 1)?'Active':'Not Active'; 
                    return $disc;

                })
//                 ->editColumn(
//                    'active',
//                    function ($role) {
//                    return ($role->is_active == '0')?'<div class="btn-group ">
//                                             <label class="badge badge-warning current-status">In Active</label>
//                                             
//                                          </div></b>':'<div class="btn-group ">
//                                             <label class="badge badge-warning current-status">Active</label>
//                                             
//                                          </div></b>';
//
//                })
                
                ->editColumn(
                    'created_at',
                    function ($role) {
                    return ($role->created_at)? date('d-M-Y',strtotime($role->created_at)) : '---';

                })
                
                ->addColumn(
                    'action',
                    function ($role) {
                    return  "<a title=\"Edit User\"  data-toggle=\"modal\" data-target=\"#manageUserRole\" data-url =\"" . route('edit_user_role', ['role_id' => $role->id,'user_id'=>$role->user_id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-edit\"></i></a> ";
                    })
                    ->filter(function ($query) use ($request) {
                        if ($request->get('by_email') != '') {
                            if ($request->has('by_email')) {
                                $query->where(function ($query) use ($request) {
                                    $by_nameOrEmail = trim($request->get('by_email'));
                                    $query->where('users.f_name', 'like',"%$by_nameOrEmail%")
                                    ->orWhere('users.l_name', 'like', "%$by_nameOrEmail%")
                                    //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                    ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                                });
                            }
                        }
                        if ($request->get('is_assign') != '') {
                            if ($request->has('is_assign')) {
                                $query->where(function ($query) use ($request) {
                                    $by_status = (int) trim($request->get('is_assign'));
                                    
                                    $query->where('role_user.role_id', 'like',
                                            "%$by_status%");
                                });
                            }
                        }
                    })
                    ->make(true);
                
    }

    /*      
     * Get address list for FI
     */
    public function getFiListsList(Request $request, $data)
    {
        $type = ['Company (Registered Address)', 'Company (Communication Address)', 'Company (GST Address)', 'Company (Warehouse Address)', 'Company (Factory Address)','Promoter Address'];
        return DataTables::of($data)
                ->rawColumns(['id', 'action', 'status'])
                ->addColumn(
                    'id',
                    function ($data) {
                        //$link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return '<input type="checkbox" value="'.$data->id.'#'.$data->address_type.'">'.$data->id;
                        //return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . $app->app_id . "</a> ";
                    }
                )
                ->addColumn(
                    'address_type',
                    function ($data) use ($type) {                        
                        return $type[$data->address_type];
                })
                ->addColumn(
                    'name',
                    function ($data) {                        
                        return $data->name ? $data->name : '';
                })
                ->addColumn(
                    'address',
                    function ($data) {
                    return $data->address ? $data->address: '';
                }) 
                ->addColumn(
                    'status',
                    function ($data) {
                    return '<label class="badge badge-warning">Pending</label>';

                })
                ->addColumn(
                    'action',
                    function ($data) use ($request) {
                        //$link = route('business_information_open', ['app_id' => $request->app_id, 'biz_id' => $request->biz_id]);
                        return '<div class="d-flex inline-action-btn">
                                <a href="#" title="View FI" class="btn btn-action-btn btn-sm">View FI</a>
                            </div>';
                    }
                )
                ->filter(function ($query) use ($request) {            
                    /*if ($request->get('search_keyword') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('app.app_id', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }*/
                    /*if ($request->get('is_status') != '') {
                        $query->where(function ($query) use ($request) {
                            $is_assigned = $request->get('is_status');
                            $query->where('app.status', $is_assigned);
                        });
                    }*/
                    
                })
                ->make(true);
    } 
}