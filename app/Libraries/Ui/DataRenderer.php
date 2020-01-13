<?php

namespace App\Libraries\Ui;

use DataTables;
use Helpers;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\AppAssignment;
use App\Libraries\Ui\DataRendererHelper;
use App\Contracts\Ui\DataProviderInterface;
use App\Inv\Repositories\Models\Master\DoaLevelRole;

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
                       $achorId= $userInfo->f_name.' '.$userInfo->l_name;
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
                       $saleName=$userInfo->f_name. ' '.$userInfo->l_name;  
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
                    'name',
                    function ($app) {                        
                        return $app->name ? $app->name : '';
                })
                ->addColumn(
                    'email',
                    function ($app) {                        
                        return $app->email ? $app->email : '';
                })
                ->addColumn(
                    'mobile_no',
                    function ($app) {                        
                        return $app->mobile_no ? $app->mobile_no : '';
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
                    //if ($app->to_id){
                    //    $userInfo = Helpers::getUserInfo($app->to_id);                    
                    //    $assignName = $userInfo->f_name. ' ' . $userInfo->l_name;  
                    //} else {
                    //    $assignName=''; 
                    //} 
                    //return $assignName;
                    $userInfo = Helpers::getAppCurrentAssignee($app->app_id);
                    if($userInfo){
                        return $userInfo->assignee ? $userInfo->assignee . '<br><small>(' . $userInfo->assignee_role . ')</small>' : '';
                    }
                    return '';
                })
                ->addColumn(
                    'assigned_by',
                    function ($app) {
                        if ($app->from_role && !empty($app->from_role)) {
                            return $app->assigned_by ? $app->assigned_by .  '<br><small>(' . $app->from_role . ')</small>' : '';
                        } else {
                            return $app->assigned_by ? $app->assigned_by : '';
                        }
                        //$fromData = AppAssignment::getOrgFromUser($app->app_id);
                        //return isset($fromData->assigned_by) ? $fromData->assigned_by . '<br><small>(' . $fromData->from_role . ')</small>' : '';
                })                
                ->addColumn(
                    'shared_detail',
                    function ($app) {
                    return $app->sharing_comment ? $app->sharing_comment : '';

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
                        $view_only = Helpers::isAccessViewOnly($app->app_id);
                        if ($view_only && $app->status == 1) {
                            if(Helpers::checkPermission('add_app_note')){
                                $act = $act . '<a title="Add App Note" href="#" data-toggle="modal" data-target="#addCaseNote" data-url="' . route('add_app_note', ['app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="170px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>';
                            }
                            if(Helpers::checkPermission('send_case_confirmBox')){
                                $act = $act . '&nbsp;<a href="#" title="Move to Next Stage" data-toggle="modal" data-target="#sendNextstage" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="370px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
                                $roleData = Helpers::getUserRole();
                                $currentStage = Helpers::getCurrentWfStage($app->app_id);
                                if ($roleData[0]->id != 4 && !empty($currentStage->assign_role)) {
                                    $act = $act . '&nbsp;<a href="#" title="Move to Back Stage" data-toggle="modal" data-target="#assignCaseFrame" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id'), 'assign_case' => 1]) . '" data-height="370px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
                                }
                            }
                            
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
     * Get application list
     */
    public function getFiRcuAppList(Request $request, $app)
    {
        return DataTables::of($app)
                ->rawColumns(['app_id', 'action', 'status'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $link = route('backend_fi', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . $app->app_id . "</a> ";
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
                /*->addColumn(
                    'action',
                    function ($app) use ($request) {
                        return '<div class="d-flex inline-action-btn">
                                <a href="'.route('business_information_open', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]).'" title="View Application" class="btn btn-action-btn btn-sm">View</a>
                                <a href="'.route('front_gstin', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]).'" title="Pull GST Detail" class="btn btn-action-btn btn-sm">Pull Gst</a>
                            </div>';
                    }
                )*/
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
                        return '<div class="d-flex inline-action-btn">
                            <a href="'.route('front_upload_invoice', ['anchor_id' =>$app->anchor_id, 'user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]).'" title="Upload Invoice" class="btn btn-action-btn btn-sm">Invoice</a>
                                <a href="'.route('business_information_open', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]).'" title="View Application" class="btn btn-action-btn btn-sm">View</a>
                                <a href="'.route('front_gstin', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]).'" title="Pull GST Detail" class="btn btn-action-btn btn-sm">Pull Gst</a>
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
     * Get Invoice list for frontend
     */
    public function getInvoiceList(Request $request,$invoice)
    { 
        return DataTables::of($invoice)
                ->rawColumns(['status'])
                ->addColumn(
                    'anchor_name',
                    function ($invoice) {                        
                        return $invoice->anchor->comp_name ? $invoice->anchor->comp_name : '';
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        return $invoice->supplier->f_name ? $invoice->supplier->f_name : '';
                })
                 ->addColumn(
                    'program_name',
                    function ($invoice) {                        
                        return $invoice->program->prgm_name ? $invoice->program->prgm_name : '';
                })
                ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                         return $invoice->invoice_date ? $invoice->invoice_date : '';
                })
                 ->addColumn(
                    'invoice_approve_amount',
                    function ($invoice) {                        
                         return $invoice->invoice_approve_amount ? $invoice->invoice_approve_amount : '';
                })
                
               ->addColumn(
                    'status',
                    function ($invoice) {
                    //$app_status = config('inv_common.app_status');                    
                    return '<label class="badge '.(($invoice->status == 1)? "badge-primary":"badge-warning").'">'.(($invoice->status == 1)? "Completed":"Incomplete").'</label>';

                })
              ->make(true);
    }  
    
    /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceList(Request $request,$invoice)
    { 
      
        return DataTables::of($invoice)
               ->rawColumns(['status','anchor_id','action'])
                ->addColumn(
                    'anchor_id',
                    function ($invoice) {                        
                        return '<input type="checkbox" name="chkstatus" value="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="chkstatus">';
                })
                ->addColumn(
                    'anchor_name',
                    function ($invoice) {                        
                        return $invoice->anchor->comp_name ? $invoice->anchor->comp_name : '';
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        return $invoice->supplier->f_name ? $invoice->supplier->f_name : '';
                })
                 ->addColumn(
                    'program_name',
                    function ($invoice) {                        
                        return $invoice->program->prgm_name ? $invoice->program->prgm_name : '';
                })
                ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                         return $invoice->invoice_date ? $invoice->invoice_date : '';
                })
                 ->addColumn(
                    'invoice_approve_amount',
                    function ($invoice) {                        
                         return $invoice->invoice_approve_amount ? $invoice->invoice_approve_amount : '';
                })
                
               ->addColumn(
                    'status',
                    function ($invoice) {
                    //$app_status = config('inv_common.app_status');                    
                    return '<label class="badge '.(($invoice->status == 1)? "badge-primary":"badge-warning").'">'.(($invoice->status == 1)? "Completed":"Incomplete").'</label>';

                })
                 ->addColumn(
                    'action',
                    function ($invoice) {
                    //$app_status = config('inv_common.app_status');                    
                    return '<a title="Edit" href="#" data-toggle="modal" data-target="#myModal7" class="btn btn-action-btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>';

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
                        //$roleData = User::getBackendUser(\Auth::user()->user_id);
                        //if ($roleData[0]->is_superadmin == 1) {
                        //    $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);                                                            
                        //} else {
                            $link = '#';
                        //}                        
                        //$link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id, 'user_id' => $app->user_id]);
                        return '<a id="app-id-' . $app->app_id . ' rel="tooltip" href="' . $link . '">' . 'CAPS000'.$app->app_id . '</a>';
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
                      
                     if(Helpers::checkPermission('manage_program')){                        
                        $act.=  '<a title="Manage Program" href="'.route('manage_program',['anchor_id' => $users->anchor_id]).'" class="btn btn-action-btn btn-sm "><i class="fa fa-cog" aria-hidden="true"></i></a>';
                     }
                     if(Helpers::checkPermission('edit_anchor_reg')){                        
                        $act .= "<a  data-toggle=\"modal\" data-target=\"#editAnchorFrm\" data-url =\"" . route('edit_anchor_reg', ['anchor_id' => $users->anchor_id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Anchor Detail\"><i class=\"fa fa-edit\"></a>";
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
                ->rawColumns(['role_id', 'name', 'checkbox', 'action', 'active','assigned'])
                
                ->addColumn(
                    'srno',
                    function ($role) {
                      return "==";
                    })
                    
                ->editColumn(
                        'name',
                        function ($role) {
                    $name = $role->f_name.' '.$role->l_name ;
                    if ($role->is_appr_required == 1) {
                        $name .= '<br><small>(Approval Authority)</small>';
                    }
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
                    'reporting_mgr',
                    function ($role) {
                    $reporting_mgr = $role->reporting_mgr; 
                    return $reporting_mgr;

                })                                
                ->editColumn(
                    'active',
                    function ($role) {
                    $disc = ($role->is_active == 1)?'Active':'Not Active'; 
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

    
    
    /**
     * 
     * @param type $request
     * @param type $program
     * @return type
     * 
     * 
     * 
     *  {data: 'program_id'},
                {data: 'anchor_name'},
                {data: 'program_type'},
                {data: 'anchor_limit'},
                {data: 'anchor_sub_limit'},
                {data: 'status'},
                {data: 'action'}
     */
    
    
    function getPromgramList($request , $program)
    {
         return DataTables::of($program)
                ->rawColumns([ 'action', 'active','status' ,'anchor_limit'])                
                ->editColumn(
                    'prgm_id',
                    function ($program) {                   
                      return $program->prgm_id;
                    })
                ->editColumn(
                    'f_name',
                    function ($program) {                   
                      return $program->f_name;
                    })
                ->editColumn(
                    'product_id',
                    function ($program) {                   
                      return $program->product_name;
                    })
                ->editColumn(
                    'prgm_name',
                    function ($program) {                   
                      return $program->prgm_name;
                    })
                ->editColumn(
                    'prgm_type',
                    function ($program) {                   
                      return ($program->prgm_type==1) ? 'Vendor Finance' : 'Channel Finance';
                    })
                ->editColumn(
                    'anchor_limit',
                    function ($program) {                   
                      return  \Helpers::formatCurreny($program->anchor_limit);
                    })
                ->addColumn(
                    'anchor_sub_limit',
                    function ($program) {                   
                      return '-';
                    })
                ->addColumn(
                    'loan_size',
                    function ($program) {                   
                      return '-';
                    })
                ->editColumn(
                    'status',
                    function ($program) {

                          return ($program->status == '0')?'<div class="btn-group ">
                                             <label class="badge badge-warning current-status">In Active</label>
                                             
                                          </div></b>':'<div class="btn-group ">
                                             <label class="badge badge-success current-status">Active</label>
                                             
                                          </div></b>';
                        })
                        ->addColumn(
                    'action',
                    function ($program) {
                        $action = '';
                      if(Helpers::checkPermission('manage_sub_program')){
                          $action .='<a title="View Sub-Program" href="'.route('manage_sub_program',['program_id'=>$program->prgm_id ,'anchor_id'=>$program->anchor_id]).'" class="btn btn-action-btn btn-sm "><i class="fa fa-cog" aria-hidden="true"></i></a>';
                      }
                    
                    //add_sub_program
                    
                      if($program->status){
                           return $action.'<a title="In Active" href="'.route('change_program_status', [ 'program_id'=> $program->prgm_id , 'status'=>0 ]).'"  class="btn btn-action-btn btn-sm program_status "><i class="fa fa-eye" aria-hidden="true"></i></a>';
                      }else{
                           return $action.'<a title="Active" href="'.route('change_program_status', [ 'program_id'=> $program->prgm_id , 'status'=>1 ]).'"  class="btn btn-action-btn btn-sm  program_status"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>';
                      }
                      
                   
                    })
                    ->filter(function ($query) use ($request) {
                        
                    })
                    ->make(true);
    }


    public function getAgencyList(Request $request, $agency)
    {
        
        return DataTables::of($agency)
                ->rawColumns(['agency_id', 'action'])
                ->addColumn(
                    'agency_id',
                    function ($agency) {
                    $link = '000'.$agency->agency_id;
                    return $link;
                      // return "<a id=\"" . $user->user_id . "\" href=\"".route('lead_detail', ['user_id' => $user->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                        
                    } )
                ->editColumn(
                    'agency_name',
                    function ($agency) {
                    return $agency->comp_name;
                })              
                ->editColumn(
                    'address',
                    function ($agency) {
                    return $agency->comp_addr; 
                })
                ->editColumn(
                    'email',
                    function ($agency) {
                    return $agency->comp_email  ;

                })
                ->editColumn(
                    'phone',
                    function ($agency) {
                    return $agency->comp_phone; 
                }) 
                ->editColumn(
                    'created_at',
                    function ($agency) {
                    return ($agency->created_at)? date('d-M-Y',strtotime($agency->created_at)) : '---';
                })
                ->addColumn(
                    'action',
                    function ($agency) {
                       $act = '';
                     //if(Helpers::checkPermission('edit_anchor_reg')){
                        $act = "<a  data-toggle=\"modal\" data-target=\"#editAgencyFrame\" data-url =\"" . route('edit_agency_reg', ['agency_id' => $agency->agency_id]) . "\" data-height=\"400px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Agency Detail\"><i class=\"fa fa-edit\"></a>";
                     //}
                     return $act;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_name') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('by_name'));
                            $query->where('agency.comp_name', 'like',"%$search_keyword%")
                            ->orWhere('agency.comp_email', 'like', "%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }

     public function getChargesList(Request $request, $charges){
        $this->chrg_applicable_ids = array(
            '1' => 'Limit Amount',
            '2' => 'Outstanding Amount',
            '3' => 'Outstanding Principal',
            '4' => 'Outstanding Interest',
            '5' => 'Overdue Amount'
        );
        return DataTables::of($charges)
                ->rawColumns(['is_active'])
                ->addColumn(
                    'chrg_name',
                    function ($charges) {
                    return $charges->chrg_name;
                })
                ->addColumn(
                    'chrg_type',
                    function ($charges) {
                    return ($charges->chrg_type == '1') ? 'Auto' : 'Manual';
                })
                ->addColumn(
                    'chrg_calculation_type',
                    function ($charges) {
                    return $charges->chrg_calculation_type == 1 ? 'Fixed' : 'Percent';
                })  
                ->addColumn(
                    'chrg_calculation_amt',
                    function ($charges) {
                    return $charges->chrg_calculation_amt;
                })              
                ->addColumn(
                    'is_gst_applicable',
                    function ($charges) {
                    return ($charges->is_gst_applicable == 1) ? 'Yes' : 'No'; 
                })
                ->addColumn(
                    'chrg_applicable_id',
                    function ($charges) {
                    return $this->chrg_applicable_ids[$charges->chrg_applicable_id] ?? 'N/A'; 
                }) 
                ->addColumn(
                    'chrg_desc',
                    function ($charges) {
                     return $charges->chrg_desc;
                })
                ->addColumn(
                    'created_at',
                    function ($charges) {
                    return ($charges->created_at) ? date('d-M-Y',strtotime($charges->created_at)) : '---';
                })
                ->addColumn(
                    'created_by',
                    function ($charges) {
                    return $charges->userDetail->f_name.' '.$charges->userDetail->l_name;
                })
                ->addColumn(
                    'is_active',
                    function ($charges) {
                       $act = $charges->is_active;
                       $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editChargesFrame" title="Edit Charge Detail" data-url ="'.route('edit_charges',['id' => $charges->id]).'" data-height="400px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                       $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success' : 'danger').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                     return $status;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('chrg_desc', 'like',"%$search_keyword%")
                            ->orWhere('chrg_calculation_amt', 'like', "%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }

     public function getDocumentsList(Request $request, $documents){
         $this->doc_type_ids = array(
            '1' => 'Onboarding',
            '2' => 'Pre Sanction',
            '3' => 'Post Sanction',
        );
        return DataTables::of($documents)
                ->rawColumns(['is_active'])
                ->addColumn(
                    'doc_type_id',
                    function ($documents) {
                    return $this->doc_type_ids[$documents->doc_type_id] ?? 'N/A'; 
                })
                ->addColumn(
                    'doc_name',
                    function ($documents) {
                    return $documents->doc_name;
                })
                ->addColumn(
                    'is_rcu',
                    function ($documents) {
                    return $documents->is_rcu == 1 ? 'Enabled' : 'Disabled';
                })  
                ->addColumn(
                    'created_at',
                    function ($documents) {
                    return ($documents->created_at) ? date('d-M-Y',strtotime($documents->created_at)) : '---';
                })
                ->addColumn(
                    'created_by',
                    function ($documents) {
                    return $documents->userDetail->f_name.' '.$documents->userDetail->l_name;
                })
                ->addColumn(
                    'is_active',
                    function ($documents) {
                       $act = $documents->is_active;
                       $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editDocumentsFrame" title="Edit Document Detail" data-url ="'.route('edit_documents',['id' => $documents->id]).'" data-height="400px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                       $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success' : 'danger').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                     return $status;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('doc_name', 'like',"%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }

    public function getIndustriesList(Request $request, $industries){

        return DataTables::of($industries)
                ->rawColumns(['is_active'])
                ->addColumn(
                    'name',
                    function ($industries) {
                    return $industries->name;
                }) 
                ->addColumn(
                    'created_at',
                    function ($industries) {
                    return ($industries->created_at) ? date('d-M-Y',strtotime($industries->created_at)) : '---';
                })
                ->addColumn(
                    'created_by',
                    function ($industries) {
                    return $industries->userDetail->f_name.' '.$industries->userDetail->l_name;
                })
                ->addColumn(
                    'is_active',
                    function ($industries) {
                       $act = $industries->is_active;
                       $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editIndustriesFrame" title="Edit Industry Detail" data-url ="'.route('edit_industries',['id' => $industries->id]).'" data-height="250px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                       $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success' : 'danger').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                     return $status;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('name', 'like',"%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }

    // Entities
    public function getAllEntity(Request $request, $data)
    {
        return DataTables::of($data)
                ->rawColumns(['is_active'])
                ->addColumn(
                    'id',
                    function ($data) {
                        return $data->id;
                })
                ->addColumn(
                    'entity_name',
                    function ($data) {
                    return $data->entity_name;
                })
                ->addColumn(
                    'created_at',
                    function ($data) {
                    return ($data->created_at) ? date('d-M-Y',strtotime($data->created_at)) : '---';
                })
                ->addColumn(
                    'is_active',
                    function ($data) {
                       $act = $data->is_active;
                       $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editEntityFrame" title="Edit Entitry Detail" data-url ="'.route('edit_entity',['id' => $data->id]).'" data-height="400px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                       $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success' : 'danger').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                     return $status;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('chrg_desc', 'like',"%$search_keyword%")
                            ->orWhere('chrg_calculation_amt', 'like', "%$search_keyword%");
                        });
                    }
                })
                ->make(true);
       
    }

    public function getAgencyUserLists(Request $request, $user)
    {
        return DataTables::of($user)
                ->rawColumns(['user_id', 'action'])
                ->addColumn(
                    'user_id',
                    function ($user) {
                    $link = '000'.$user->user_id;
                    return $link;
                    } )             
                ->editColumn(
                    'user_name',
                    function ($user) {
                    return $user->f_name.' '.$user->l_name; 
                })
                ->editColumn(
                    'agency_name',
                    function ($user) {
                    return $user->agency->comp_name;
                }) 
                ->editColumn(
                    'email',
                    function ($user) {
                    return $user->email  ;

                })
                ->editColumn(
                    'phone',
                    function ($user) {
                    return $user->mobile_no; 
                }) 
                ->editColumn(
                    'created_at',
                    function ($user) {
                    return ($user->created_at)? date('d-M-Y',strtotime($user->created_at)) : '---';
                })
                ->addColumn(
                    'action',
                    function ($user) {
                       $act = '';
                     //if(Helpers::checkPermission('edit_anchor_reg')){
                        $act = "<a  data-toggle=\"modal\" data-target=\"#editAgencyUserFrame\" data-url =\"" . route('edit_agency_user_reg', ['user_id' => $user->user_id]) . "\" data-height=\"350px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Agency User Detail\"><i class=\"fa fa-edit\"></a>";
                     //}
                     return $act;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_name') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('by_name'));
                            $query->where('users.f_name', 'like',"%$search_keyword%")
                            ->orWhere('users.email', 'like', "%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }
    
    
    
    /**
     * sub program 
     * 
     * @param type $request
     * @param type $program
     * @return type mixed 
     */
    function getSubProgramList($request, $program)
    {
        return DataTables::of($program)
                        ->rawColumns(['user_id', 'status', 'action' ,'anchor_sub_limit' ,'anchor_limit' ,'loan_size'])
                        ->editColumn(
                                'prgm_id',
                                function ($program) {
                            return $program->prgm_id;
                        })
                        ->editColumn(
                                'product_name',
                                function ($program) {
                            return $program->product_name;
                        })
                        ->editColumn(
                                'anchor_sub_limit',
                                function ($program) {
                            return  \Helpers::formatCurreny($program->anchor_sub_limit);
                        })
                        ->editColumn(
                                'anchor_limit',
                                function ($program) {
                            return  \Helpers::formatCurreny($program->anchor_limit);
                        })
                        ->addColumn(
                                'loan_size',
                                function ($program) {
                             return  \Helpers::formatCurreny($program->min_loan_size) .'-' . \Helpers::formatCurreny($program->max_loan_size);
                           
                        })
                        ->editColumn(
                                'status',
                                function ($program) {
                            return ($program->status == '0')?'<div class="btn-group ">
                                             <label class="badge badge-warning current-status">In Active</label>
                                             
                                          </div></b>':'<div class="btn-group ">
                                             <label class="badge badge-success current-status">Active</label>
                                             
                                          </div></b>';
                        })
                        ->addColumn(
                                'action',
                                function ($program) {
                            $act = '';
                            //if(Helpers::checkPermission('edit_anchor_reg')){
                            $act = "<a  href='". route('add_sub_program',['anchor_id'=> $program->anchor_id ,'program_id' => $program->prgm_id ,  'action' => 'edit'] )."' class=\"btn btn-action-btn btn-sm\" title=\"Edit Sub-Program\"><i class=\"fa fa-edit\"></a>";
                            //}
                            return $act;
                        }
                        )->make(true);
    }

    /*
     * 
     * get all lms customer list
     */
    public function lmsGetCustomers(Request $request, $customer)
    {
        return DataTables::of($customer)
                ->rawColumns(['customer_id', 'status', 'action'])
                ->addColumn(
                    'customer_id',
                    function ($customer) {
                        $link = $customer->customer_id;
                        return "<a id=\"" . $customer->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $customer->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                    }
                )
                ->addColumn(
                    'virtual_acc_id',
                    function ($customer) {
                        return $customer->virtual_acc_id;
                    }
                )     
                ->editColumn(
                        'customer_name',
                        function ($customer) {
                        $full_name = $customer->user->f_name.' '.$customer->user->l_name;
                        return $full_name;
                    }
                )
                ->editColumn(
                        'customer_email',
                        function ($customer) {
                    $email = $customer->user->email;
                    return $email;
                    
                })
                ->editColumn(
                        'customer_mobile',
                        function ($customer) {
                    $mobile_no = $customer->user->mobile_no;
                    return $mobile_no;
                    
                })
                ->editColumn(
                    'limit',
                    function ($customer) {
                    return 12;

                })
                ->editColumn(
                    'interest_rate',
                    function ($customer) {                    
                    return 12;
                })
                ->editColumn(
                    'consume_limit',
                    function ($customer) {
                    return 12;
                })
                ->editColumn(
                    'available_limit',
                    function ($customer) {
                    
                    return 12;
                })
                ->editColumn(
                    'tenor_days',
                    function ($customer) {
                    return 12;
                })
                ->editColumn(
                    'assignee',
                    function ($customer) {
                    return 'xyz';
                })
                ->editColumn(
                    'assigned_by',
                    function ($customer) {
                    return 'xyz';

                })
                ->editColumn(
                    'status',
                    function ($customer) {
                    if ($customer->is_assign == 0) {
                        return "<label class=\"badge badge-warning current-status\">Pending</label>";
                    } else {
                        return "<span style='color:green'>Assigned</span>";
                    }
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_email') != '') {
                        if ($request->has('by_email')) {
                            $query->whereHas('user', function($query) use ($request) {
                                $by_nameOrEmail = trim($request->get('by_email'));
                                $query->where('f_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('l_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere('email', 'like', "%$by_nameOrEmail%");
                            });
                        }
                    }
                    if ($request->get('is_assign') != '') {
                        if ($request->has('is_assign')) {
                            $query->whereHas('user', function($query) use ($request) {
                                $by_status = (int) trim($request->get('is_assign'));
                                
                                $query->where('is_assigned', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    
    /**
     * List Doa Levels  
     * 
     * @param Request $request
     * @param mixed $doa
     * @return mixed 
     */
    function getDoaLevelsList($request, $doa)
    {
        return DataTables::of($doa)
            ->rawColumns(['action', 'role', 'amount'])

            ->editColumn(
                    'level_code',
                    function ($doa) {
                return $doa->level_code;
            })
            ->editColumn(
                    'level_name',
                    function ($doa) {
                return $doa->level_name;
            })
            ->editColumn(
                    'city',
                    function ($doa) {
                return $doa->city;
            })
            ->addColumn(
                    'amount',
                    function ($doa) {
                return \Helpers::formatCurreny($doa->min_amount) . ' - ' . \Helpers::formatCurreny($doa->max_amount);
            })
            ->editColumn(
                    'role',
                    function ($doa) {
                $roles = DoaLevelRole::getDoaLevelRoles($doa->doa_level_id);
                $rolesName = '';
                foreach($roles as $role) {
                    $rolesName .= $role->role . ', ';
                }
                return rtrim($rolesName,', ');
            })                        
             ->addColumn(
                    'action',
            function ($doa) {
                $act = '';
                $act = '<a  data-toggle="modal" data-target="#editDoaLevelFrame" data-url ="' . route('edit_doa_level', ['doa_level_id' => $doa->doa_level_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Level"><i class="fa fa-edit"></i></a>';
                $act .= '&nbsp;&nbsp;<a  data-toggle="modal" data-target="#assignRoleLevelFrame" data-url ="' . route('assign_role_level', ['doa_level_id' => $doa->doa_level_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="Assign Role"><i class="fa fa-angle-right"></i></a>';
                return $act;
            })
            ->filter(function ($query) use ($request) {
                if ($request->get('search_keyword') != '') {
                    $query->where(function ($query) use ($request) {
                        $search_keyword = trim($request->get('search_keyword'));
                        $query->where('doa_level.level_name', 'like',"%$search_keyword%");                                    
                    });
                }
            })
            ->make(true);
    }
    
    
    
    
    /**
     * bank list 
     * 
     * @param type $request
     * @param type $bank
     * @return type mixed
     */
    function getBankAccountList($request, $bank)
    {
        return DataTables::of($bank)
                        ->rawColumns(['action', 'is_active'])
                        ->editColumn(
                                'bank_name',
                                function ($bank) {
                            return $bank->bank_name;
                        })
                        ->addColumn(
                                'action',
                                function ($bank) {

                            $checked = ($bank->is_default == 1) ? 'checked' : null;
                            $act = '';
                            if($bank->is_active){
                              $act .= '    <input type="checkbox"  ' . $checked . ' data-rel = "' . \Crypt::encrypt($bank->bank_account_id) . '"  class="make_default" name="add"><label for="add">Default</label> ';
                            }
                          
                            if (Helpers::checkPermission('add_bank_account')) {
                                $act .= '<a data-toggle="modal"  data-height="550px" 
                           data-width="100%" 
                           data-target="#add_bank_account"
                           data-url="' . route('add_bank_account', ['bank_account_id' => $bank->bank_account_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Bank Account"><i class="fa fa-edit"></i></a>';
                            }
                            return $act;
                        })
                        ->editColumn(
                                'is_active',
                                function ($bank) {
                            if ($bank->is_active) {
                                return '<span class="badge badge-success">Active</span>';
                            } else {
                                return '<span class="badge badge-warning current-status">InActive</span>';
                            }
                        })
                        ->make(true);
    }

}