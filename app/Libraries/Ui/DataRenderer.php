<?php
namespace App\Libraries\Ui;
use DataTables;
use Helpers;
use DB;
use Session;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\User;
use App\Inv\Repositories\Models\BizInvoice;
use Illuminate\Support\Facades\Storage;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Models\Application;
use App\Libraries\Ui\DataRendererHelper;
use App\Contracts\Ui\DataProviderInterface;
use App\Inv\Repositories\Models\Master\DoaLevelRole;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;

class DataRenderer implements DataProviderInterface
{
    use LmsTrait;

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
     * Initialization created_at
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
                      $achorId='N/A';  
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
                    return  "<a title=\"edit Lead\"  data-toggle=\"modal\" data-target=\"#editLead\" data-url =\"" . route('edit_backend_lead', ['user_id' => $users->user_id]) . "\" data-height=\"230px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Lead Detail\"><i class=\"fa fa-edit\"></a>";
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
                ->rawColumns(['app_id','assignee', 'assigned_by', 'action','contact','name'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $user_role = Helpers::getUserRole(\Auth::user()->user_id)[0]->pivot->role_id;
                        $app_id = $app->app_id;
                        if(Helpers::checkPermission('company_details')){
                           if($user_role == config('common.user_role.APPROVER'))
                                $link = route('cam_report', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);
                           else
                                $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);
                           return "<a id='app-id-$app_id' href='$link' rel='tooltip'>" . \Helpers::formatIdWithPrefix($app_id, 'APP') . "</a>";
                        }else{
                            return "<a id='app-id-$app_id' rel='tooltip'>" . \Helpers::formatIdWithPrefix($app_id, 'APP') . "</a>";
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
                        if($app->user_type && $app->user_type==1){
                            $anchorUserType='<small class="aprveAppListBtn">( Supplier )</small>'; 
                        }else if($app->user_type && $app->user_type==2){
                            $anchorUserType='<small class="aprveAppListBtn">( Buyer )</small>';
                        }else{
                            $anchorUserType='';
                        }
                        return $app->name ? $app->name .'<br>'. $anchorUserType : $anchorUserType;
                })
                ->addColumn(
                    'contact',
                    function ($app) {
                        $contact = '';
                        $contact .= $app->email ? '<span><b>Email:&nbsp;</b>'.$app->email.'</span>' : '';
                        $contact .= $app->mobile_no ? '<br><span><b>Mob:&nbsp;</b>'.$app->mobile_no.'</span>' : '';
                        return $contact;
                    }
                )
                // ->addColumn(
                //     'email',
                //     function ($app) {                        
                //         return $app->email ? $app->email : '';
                // })
                // ->addColumn(
                //     'mobile_no',
                //     function ($app) {                        
                //         return $app->mobile_no ? $app->mobile_no : '';
                // })                
                ->addColumn(
                    'assoc_anchor',
                    function ($app) {
                        //return "<a  data-original-title=\"Edit User\" href=\"#\"  data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";
                    /////return isset($app->assoc_anchor) ? $app->assoc_anchor : '';
                    
                    if($app->anchor_id){
                       $userInfo = User::getUserByAnchorId($app->anchor_id);
                       $achorName= $userInfo->f_name . ' ' . $userInfo->l_name;
                    } else {
                       $achorName='';  
                    }                    
                    return $achorName;
                    
                })
                // ->addColumn(
                //     'user_type',
                //     function ($app) {
                //     if($app->user_type && $app->user_type==1){
                //        $anchorUserType='Supplier'; 
                //     }else if($app->user_type && $app->user_type==2){
                //         $anchorUserType='Buyer';
                //     }else{
                //         $anchorUserType='';
                //     }
                //        return $anchorUserType;
                // })                
                ->addColumn(
                    'assignee',
                    function ($app) {  
                        $data = '';                  
                    //if ($app->to_id){
                    //    $userInfo = Helpers::getUserInfo($app->to_id);                    
                    //    $assignName = $userInfo->f_name. ' ' . $userInfo->l_name;  
                    //} else {
                    //    $assignName=''; 
                    //} 
                    //return $assignName;
                    $userInfo = Helpers::getAppCurrentAssignee($app->app_id);
                    if($userInfo){
                        $data .= $userInfo->assignee ? $userInfo->assignee . '<br><small>(' . $userInfo->assignee_role . ')</small>' : '';
                    }
                   // $data .= '<a  data-toggle="modal" data-target="#viewApprovers" data-url ="' . route('view_approvers', ['app_id' => $app->app_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Approver List"><i class="fa fa-eye"></i></a>';
                    $data .= '<a  data-toggle="modal" data-target="#viewApprovers" data-url ="' . route('view_approvers', ['app_id' => $app->app_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="aprveAppListBtn" title="View Approver List">View Approver List</a>';
                    return $data;
                })
                ->addColumn(
                    'assigned_by',
                    function ($app) {
                        $data = '';
                        if ($app->from_role && !empty($app->from_role)) {
                            $data .= $app->assigned_by ? $app->assigned_by .  '<br><small>(' . $app->from_role . ')</small>' : '';
                        } else {
                            $data .= $app->assigned_by ? $app->assigned_by : '';
                        }
                       // $data .= '<a  data-toggle="modal" data-target="#viewSharedDetails" data-url ="' . route('view_shared_details', ['app_id' => $app->app_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Shared Details"><i class="fa fa-eye"></i></a>';
                        $data .= '<a  data-toggle="modal" data-target="#viewSharedDetails" data-url ="' . route('view_shared_details', ['app_id' => $app->app_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="aprveAppListBtn" title="View Shared Details">View Shared Details</a>';
                        return $data;
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
                          //// $act = $act . '<a title="Copy application" href="#" data-toggle="modal" data-target="#addAppCopy" data-url="' . route('add_app_copy', ['user_id' =>$app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]) . '" data-height="190px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm">Copy Application</a>';
                           if(Helpers::checkPermission('add_app_note')){
                                $act = $act . '<a title="Add App Note" href="#" data-toggle="modal" data-target="#addCaseNote" data-url="' . route('add_app_note', ['app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="190px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>';
                            }
                            if(Helpers::checkPermission('send_case_confirmBox')){
                                $currentStage = Helpers::getCurrentWfStage($app->app_id);
                                $roleData = Helpers::getUserRole();                                
                                if ($currentStage && $currentStage->order_no <= 16 ) {
                                    $act = $act . '&nbsp;<a href="#" title="Move to Next Stage" data-toggle="modal" data-target="#sendNextstage" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="370px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
                                }
                                
                                if ($roleData[0]->id != 4 && !empty($currentStage->assign_role)) {
                                    $act = $act . '&nbsp;<a href="#" title="Move to Back Stage" data-toggle="modal" data-target="#assignCaseFrame" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id'), 'assign_case' => 1]) . '" data-height="320px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
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
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . \Helpers::formatIdWithPrefix($app->app_id, $type='APP') . "</a> ";
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
                    'tenor',
                    function ($invoice) {                        
                         return $invoice->tenor ? $invoice->tenor : '';
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
     * Get  User Wise Invoice list for backend
     */
    public function getUserWiseInvoiceList(Request $request,$invoice)
    {   
        return DataTables::of($invoice)
               ->rawColumns(['anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><span><b>Business Name:&nbsp;</b>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                 ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Inv. Date:&nbsp;</b>'.$invoice->invoice_date.'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Inv. Due Date:&nbsp;</b>'.$invoice->invoice_due_date.'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor IN Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
                ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amount:&nbsp;</b>'.$invoice->invoice_amount.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Approve Amount:&nbsp;</b>'.$invoice->invoice_approve_amount.'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(            
                    'status',
                    function ($invoice) {                        
                    
                        return  $invoice->mstStatus->status_name ? $invoice->mstStatus->status_name : '';
                       
                })
                ->filter(function ($query) use ($request) {
                    
                    if ($request->get('status_id') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('status_id'));
                            $query->where('status_id',"$search_keyword");
                        });                        
                    }
                   
                    
                })
              ->make(true);
    } 
    
    /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceList(Request $request,$invoice)
    {   
        return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
           
                ->addColumn(
                    'anchor_id',
                    function ($invoice) { 
                        $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        if( $chkUser->id==1)
                        {
                             $customer  = 1;
                        }
                        else if( $chkUser->id==11)
                        {
                             $customer  = 2;
                        }
                        else
                        {
                            $customer  = 3;
                        }
                   
                       $expl  =  explode(",",$invoice->program->invoice_approval); 
                      if(in_array($customer, $expl)) 
                      {         
                        return '<input type="checkbox" name="chkstatus" value="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="chkstatus">';
                      }
                      else {
                        return "";
                      }
                    })
                  ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                 ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
                ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(
                    'action',
                    function ($invoice) {
                     $action ="";
                      if(($invoice->file_id != 0)) {
                          $action .='<a href="'.Storage::URL($invoice->userFile->file_path).'" download ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
                         } else  {
                            /// return '<input type="file" name="doc_file" id="file'.$invoice->invoice_id.'" dir="1"  onchange="uploadFile('.$invoice->app_id.','.$invoice->invoice_id.')" title="Upload Invoice">';
                           $action .='<div class="image-upload"><label for="file-input"><i class="fa fa-upload circle btnFilter" aria-hidden="true"></i> </label>
                                     <input name="doc_file" id="file-input" type="file" class="file'.$invoice->invoice_id.'" dir="1"  onchange="uploadFile('.$invoice->app_id.','.$invoice->invoice_id.')" title="Upload Invoice"/></div>';
                         }   
                        
                      $action .='<a title="Edit" href="#" data-amount="'.(($invoice->invoice_amount) ? $invoice->invoice_amount : '' ).'" data-approve="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount : '' ).'"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" data-toggle="modal" data-target="#myModal7" class="btn btn-action-btn btn-sm changeInvoiceAmount"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                        $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        if( $chkUser->id==1)
                        {
                             $customer  = 1;
                        }
                        else if( $chkUser->id==11)
                        {
                             $customer  = 2;
                        }
                        else
                        {
                            $customer  = 3;
                        }
                   
                       $expl  =  explode(",",$invoice->program->invoice_approval); 
                      if(in_array($customer, $expl)) 
                      {             
                          $action .='<a title="Approve" data-status="8"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="btn btn-action-btn btn-sm approveInv"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>';
                      }
                      return $action;
                })
                   ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    } 
    
     /*      
     * Get Invoice list for backend
     */
    public function getFrontendInvoiceList(Request $request,$invoice)
    { 
        return DataTables::of($invoice)
               ->rawColumns(['anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','invoice_upload','invoice_id','invoice_due_date'])
           
              
                 ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                          
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                 ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.$invoice->invoice_date.'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.$invoice->invoice_due_date.'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
                ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amount:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Approve Amount:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(
                    'invoice_upload',
                    function ($invoice) {
                     $action ="";
                      if(($invoice->file_id != 0)) {
                          $action .='<a href="'.Storage::URL($invoice->userFile->file_path).'" download ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
                         } else  {
                            /// return '<input type="file" name="doc_file" id="file'.$invoice->invoice_id.'" dir="1"  onchange="uploadFile('.$invoice->app_id.','.$invoice->invoice_id.')" title="Upload Invoice">';
                           $action .='<div class="image-upload"><label for="file-input"><i class="fa fa-upload circle btnFilter" aria-hidden="true"></i> </label>
                                     <input name="doc_file" id="file-input" type="file" class="file'.$invoice->invoice_id.'" dir="1"  onchange="uploadFile('.$invoice->app_id.','.$invoice->invoice_id.')" title="Upload Invoice"/></div>';
                         }                  
                    return $action;
                })
                ->addColumn(            
                    'status',
                    function ($invoice) {                        
                    
                        return  $invoice->mstStatus->status_name ? $invoice->mstStatus->status_name : '';
                       
                })    
                ->filter(function ($query) use ($request) {
                    
                    if ($request->get('status_id') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('status_id'));
                            $query->where('status_id',"$search_keyword");
                        });                        
                    }
                   
                    
                })
              ->make(true);
    } 
    
    
     /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListApprove(Request $request,$invoice)
    { 
    
    return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
           
                ->addColumn(
                    'anchor_id',
                    function ($invoice) {                        
                        return '<input type="checkbox" name="chkstatus" value="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="chkstatus">';
                })
                 ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })
               ->addColumn(
                    'action',
                    function ($invoice) {
                     $action = "";
                     $id = Auth::user()->user_id;
                     $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                     $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                     if( $chkUser->id!==11)
                     {
                      $action .='<a title="Disbursed Que" data-status="9"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="btn btn-action-btn btn-sm approveInv"><i class="fa fa-share-square" aria-hidden="true"></i></a>';
                      $action .='</br></br><div class="d-flex"><select  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv1"><option value="0">Change Status</option><option value="7">Pending</option><option value="14">Reject</option></select></div>';
                     }
                      return  $action;
                })
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    } 
    
       /*      
     * Get Invoice list for backend
     */
    public function getFrontendInvoiceListApprove(Request $request,$invoice)
    { 
    
    return DataTables::of($invoice)
               ->rawColumns(['anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                          return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
               }) 
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><span><b>Business Name:&nbsp;</b>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                  ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
             ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
              
              ->make(true);
    } 
   /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListDisbursedQue(Request $request,$invoice)
    { 
    
      return DataTables::of($invoice)
                ->rawColumns(['updated_at','invoice_checkbox', 'anchor_name','supplier_name','invoice_date','invoice_amount','status','anchor_id','action'])

                ->addColumn(
                    'invoice_checkbox',
                    function ($invoice) {                        
                        return '<input type="checkbox" class="invoice_id" name="checkinvoiceid" value="'.$invoice->invoice_id.'">';
                })
                ->addColumn(
                    'anchor_id',
                    function ($invoice) use ($request)  {     
                        if($request->front)
                        {
                            return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
                        else {
                            return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
                })
               ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                  ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                          $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                    ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(
                    'action',
                    function ($invoice) {
                        $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        if( $chkUser->id==1)
                        {
                             $customer  = 1;
                        }
                        else if( $chkUser->id==11)
                        {
                             $customer  = 2;
                        }
                        else
                        {
                            $customer  = 3;
                        }
                         $expl  =  explode(",",$invoice->program->invoice_approval); 
                         $action = "";
                    if( $chkUser->id!==11)
                     {   
                         $action .='</br><div class="d-flex"><select  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv1"><option value="0">Change Status</option><option value="7">Pending</option>';
                       if(in_array($customer, $expl)) 
                       {
                         $action .='<option value="8">Approve</option>';
                       }
                        $action .='<option value="14">Reject</option></select></div>';
                     }    
                        return  $action;
                })
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    }  
    
    
     /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListBank(Request $request,$invoice)
    { 
    
         return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                  ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                }) 
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    }  
    
    
    /*      
     * Get Invoice list for backend
     */
    public function getFrontendInvoiceListBank(Request $request,$invoice)
    { 
    
         return DataTables::of($invoice)
               ->rawColumns(['anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                          return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
               })
             ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                  ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                        
              ->make(true);
    }  
     /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListFailedDisbursed(Request $request,$invoice)
    { 
      
        return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                  ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                  ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })      
                  ->addColumn(
                    'action',
                    function ($invoice) use ($request) {
                        $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        if( $chkUser->id==1)
                        {
                             $customer  = 1;
                        }
                        else if( $chkUser->id==11)
                        {
                             $customer  = 2;
                        }
                        else
                        {
                            $customer  = 3;
                        }
                         $expl  =  explode(",",$invoice->program->invoice_approval); 
                       $action = "";
                      if( $chkUser->id!=11)
                      {  
                       $action .= '<div class="d-flex"><select  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv"><option value="0">Change Status</option>';
                       if(in_array($customer, $expl)) 
                       {
                        $action .='<option value="8">Approve</option>';
                       }
                        $action .= '<option value="9">Disb Que</option></select>&nbsp;&nbsp;<a data-toggle="modal"  data-target="#modalInvoiceFailed" data-height="400px" data-width="100%" accesskey="" data-url ="'.route("invoice_failed_status",["invoice_id" => $invoice->invoice_id]).'"> <button class="btn-upload btn-sm" type="button" title="View Failed Disbursement"> <i class="fa fa-eye"></i></button></a></div>';
                      }  
                        $action .= '&nbsp;&nbsp;<a data-toggle="modal"  data-target="#modalInvoiceFailed" data-height="400px" data-width="100%" accesskey="" data-url ="'.route("invoice_failed_status",["invoice_id" => $invoice->invoice_id]).'"> <button class="btn-upload btn-sm" type="button" title="View Failed Disbursement"> <i class="fa fa-eye"></i></button></a></div>';
                     
                        return $action;
                })
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    }  
     
     /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListDisbursed(Request $request,$invoice)
    { 
      return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','customer_detail','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             ->addColumn(
                    'batch_id',
                    function ($invoice) {  
                       return  ($invoice->disbursal->disbursal_batch->batch_id) ? $invoice->disbursal->disbursal_batch->batch_id : '';
                })
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'customer_detail',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                   ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->disbursal ? '<span><b>Disburse Date:&nbsp;</b>'.Carbon::parse($invoice->disbursal->disburse_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->disbursal ? '<br><span><b>Payment Due Date:&nbsp;</b>'.Carbon::parse($invoice->disbursal->payment_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
                 ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= ($invoice->disbursal) ? '<br><span><b>Disburse Amt.:&nbsp;</b>'.number_format($invoice->disbursal->principal_amount).'</span>' : '';
                        $inv_amount .= ($invoice->disbursal) ? '<br><span><b>Actual Disburse Amt.:&nbsp;</b>'.number_format($invoice->disbursal->disburse_amount).'</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })     
                   ->addColumn(
                    'action',
                    function ($invoice) use ($request) {
                       $act="";
                     /// $act .='<div class="d-flex inline-action-btn">&nbsp;&nbsp;<a data-toggle="modal"  data-target="#modalInvoiceDisbursed" data-height="430px" data-width="100%" accesskey="" data-url ="'.route("invoice_success_status",["invoice_id" => $invoice->invoice_id,'app_id' => $invoice->app_id]).'"> <button class="btn-upload btn-sm" type="button" title="View Disbursement"> <i class="fa fa-eye"></i></button></a></div>';
                      if(($invoice->disbursal)) { 
                      $act .='</br><a data-toggle="modal"  data-height="550px" 
                            data-width="100%" 
                            data-target="#viewInterestAccrual"
                            data-url="' . route('view_interest_accrual', ['disbursal_id' =>$invoice->disbursal->disbursal_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="View Interest Accrual"><i class="fa fa-eye"></i></a>';
                      }
                            return $act;
                })
                  ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    }  
    
      /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListRepaid(Request $request,$invoice)
    { 
    
      return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                 ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })  
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    }  
    
      /*      
     * Get execption list for backend
     */
    public function getBackendEpList(Request $request,$invoice)
    { 
    
       return DataTables::of($invoice)
               ->rawColumns(['anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                   ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                })
              ->make(true);
    }  
    
    
      /*      
     * Get Invoice list for backend
     */
    public function getBackendInvoiceListReject(Request $request,$invoice)
    { 
    
      return DataTables::of($invoice)
               ->rawColumns(['updated_at','anchor_name','supplier_name','invoice_date','invoice_amount','view_upload_invoice','status','anchor_id','action','invoice_id','invoice_due_date'])
               ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                           if($request->front)
                           {
                              return '<a href="'.route("frontend_view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
            
                           }
                        else {
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                        }
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span>' : '';
                        return $custo_name;
                })
                  ->addColumn(
                    'invoice_date',
                    function ($invoice) {                        
                        $inv_date = '';
                        $inv_date .= $invoice->invoice_date ? '<span><b>Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Due Date:&nbsp;</b>'.Carbon::parse($invoice->invoice_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
              ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        return $inv_amount;
                })
                   ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->user ? '<span><b>Name:&nbsp;</b>'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.Carbon::parse($invoice->updated_at)->format('d-m-Y H:i:s').'</span>' : '';
                        return $inv_amount;
                })     
                 ->addColumn(
                    'action',
                    function ($invoice) {
                      $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        if( $chkUser->id==1)
                        {
                             $customer  = 1;
                        }
                        else if( $chkUser->id==11)
                        {
                             $customer  = 2;
                        }
                        else
                        {
                            $customer  = 3;
                        }
                         $expl  =  explode(",",$invoice->program->invoice_approval); 
                       $action = "";
                       if( $chkUser->id!=11)
                      { 
                       $action .= '<div class="d-flex"><select  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv"><option value="0">Change Status</option>';
                       $action .= '<option value="7">Pending</option>';
                       if(in_array($customer, $expl)) 
                       {
                        $action .='<option value="8">Approve</option>';
                       }
                        $action .='</select></div>';
                      }
                        return $action;

                })
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                        $query->whereHas('business',function ($query) use ($request) {
                            $search_keyword = trim($request->get('biz_id'));
                            $query->where('invoice_no', 'like',"%$search_keyword%")
                              ->orWhere('biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    
                }) 
                 
              ->make(true);
    }  
    
    /*
     * Get bulk transaction  
     */
     
     public function getAllManualTransaction(Request $request,$trans)
     {
        /// dd($trans->disburse);
    
         return DataTables::of($trans)
               ->rawColumns(['trans_by','customer_id','virtual_account_no'])
                ->addIndexColumn()
                
                ->addColumn(
                    'customer_id',
                    function ($trans) {                        
                        $customer = '';
                        $customer .= ($trans->biz!=null) ? '<span>'.$trans->biz->biz_entity_name.'</span>' : '';
                        $customer .= $trans->lmsUser ? '<br><span><b>Customer Id:&nbsp;</b>'.$trans->lmsUser->customer_id.'</span>' : '';
                         $customer .= $trans->virtual_acc_id ? '<br><span><b>Virtual Acc. No.:&nbsp;</b>'.$trans->virtual_acc_id.'</span>' : '';
                        return $customer;
                })
                ->addColumn(
                    'virtual_account_no',
                    function ($trans) { 
                        $payment = '';
                        $payment .= $trans->trans_date ? '<span><b>Trans. Date:&nbsp;</b>'.date("Y-m-d", strtotime($trans->trans_date)).'</span>' : '';
                        $payment .= $trans->trans_detail ? '<br><span><b>Trans. Type:&nbsp;</b>'.$trans->trans_detail->trans_name.'</span>' : '';
                        $payment .= $trans->amount ? '<br><span><b>Trans. Amount:&nbsp;</b>'.number_format($trans->amount).'</span>' : '';
                        return $payment;
                         return $trans->virtual_acc_id 	 ? $trans->virtual_acc_id : '';
                })
               
                 ->addColumn(
                    'trans_by',
                    function ($trans) {  
                       if($trans->trans_by==1)
                       {
                         $type =  'Manual';
                       }
                       else if($trans->trans_by==2)
                       {
                         $type =  'Excel';
                       }
                       else
                       {
                           $type =  'N/A';
                       }
                       $transaction = '';
                       
                    if($trans->mode_of_pay){
                        $mode  = ['1' =>  'Online RTGS/NEFT','2' => 'Cheque','3' => 'NACH','4' => 'Other'];
                        $refNo  = ['1' =>  'utr_no','2' => 'cheque_no','3' => 'unr_no','4' => 'unr_no'];
                        $refNoShpw  = ['1' =>  'Utr No.','2' => 'Cheque No.','3' => 'Unr No.','4' => 'Other '];
                        $rfNo = $refNo[$trans->mode_of_pay];
                        $refNoShpw = $refNoShpw[$trans->mode_of_pay];
                           $transaction .= $trans->mode_of_pay ? '<span><b>Payment Mode:&nbsp;</b>'.$mode[$trans->mode_of_pay].'</span>' : '';
                           $transaction .= $trans->$rfNo ? '<br><span><b>'.$refNoShpw.':&nbsp;</b>'.$trans->$rfNo.'</span>' : '<br><span><b>'.$refNoShpw.':&nbsp;</b>N/A</span>';
                           $transaction .= $trans->lmsUser ? '<br><span><b>Trigger Type:&nbsp;</b>'.$type.'</span>' : '';
                    }
                    return $transaction;
                })
                 ->addColumn(
                    'comment',
                    function ($trans) {                        
                         return $trans->comment ? $trans->comment : '';
                })  
                ->addColumn(
                    'created_by',
                    function ($trans) {                        
                         return $trans->created_at ? $trans->created_at : '';
                })
                 ->filter(function ($query) use ($request) {
                    if ($request->get('type') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('type'));
                            $query->where('trans_by',$search_keyword);
                           
                        });                        
                    }
                    else if ($request->get('date') != '') {                        
                        $query->where(function ($query) use ($request) {
                             $search_keyword = Carbon::createFromFormat('d/m/Y', $request->get('date'))->format('Y-m-d');
                             $query->where('trans_date',$search_keyword);
                        });                        
                    }
                    else {
                        $query->where('trans_by','!=',NULL);
                    }
               })
              
              ->make(true);
         
     }
     
     /* Get Invoice list for backend
     */
    public function getBackendInvoiceActivityList(Request $request,$invoice)
    { 
       
      return DataTables::of($invoice)
               ->rawColumns(['anchor_id','action','status','comment'])
                ->addIndexColumn()
               
                ->addColumn(
                    'comment',
                    function ($invoice) { 
                      $color  = ['0' =>'','7'=>"badge badge-warning",'8' => "badge badge-success",'9' =>"badge badge-success",'10' =>"badge badge-success",'11' => "badge badge-danger",'12' => "badge badge-danger",'13' =>"badge badge-success",'14' => "badge badge-danger"];
                        if($invoice->status_id==0 && $invoice->updated_by==null) {
                              return $invoice->activity_name;
                      }
                })
               ->addColumn(
                    'status',
                    function ($invoice) {
                      $color  = ['0' =>'','7'=>"badge badge-warning",'8' => "badge badge-success",'9' =>"badge badge-success",'10' =>"badge badge-success",'11' => "badge badge-danger",'12' => "badge badge-danger",'13' =>"badge badge-success",'14' => "badge badge-danger"];
                      if($invoice->status_id!=0) {
                      return '<button type="button" class="'.$color[$invoice->status_id] .' btn-sm">'.$invoice->activity_name.'</button>';
                      }
                      else if($invoice->status_id==0 && $invoice->updated_by!=null)
                      {
                            return '<button type="button" class="badge badge-warning btn-sm">'.$invoice->activity_name.'</button>';
                      }

                })
                 ->addColumn(
                    'timestamp',
                    function ($invoice) {
                       return $invoice->created_at->format('j F Y H:i:s A'); 
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
                ->rawColumns(['app_id', 'contact','action','name'])
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
                        //return '<a id="app-id-' . $app->app_id . ' rel="tooltip" href="' . $link . '">' . \Helpers::formatIdWithPrefix($app->app_id, 'APP') . '</a>';
                        return \Helpers::formatIdWithPrefix($app->app_id, 'APP');
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
                        if($app->user_type && $app->user_type==1){
                            $anchorUserType='<small class="aprveAppListBtn">(Supplier)</small>'; 
                        }else if($app->user_type && $app->user_type==2){
                            $anchorUserType='<small class="aprveAppListBtn">(Buyer)</small>';
                        }else{
                            $anchorUserType='';
                        }
                        return $app->name ? $app->name .' '. $anchorUserType : $anchorUserType;
                })
                ->addColumn(
                    'assoc_anchor',
                    function ($app) {
                    if($app->anchor_id){
                       $userInfo = User::getUserByAnchorId($app->anchor_id);
                       $achorName= $userInfo->f_name . ' ' . $userInfo->l_name;
                    } else {
                       $achorName='';  
                    }                    
                    return $achorName;
                    
                })
                ->addColumn(
                    'contact',
                    function ($app) {                        
                        $contact = '';
                        $contact .= $app->email ? '<span><b>Email:&nbsp;</b>'.$app->email.'</span>' : '';
                        $contact .= $app->mobile_no ? '<br><span><b>Mob:&nbsp;</b>'.$app->mobile_no.'</span>' : '';
                        return $contact;
                })
                // ->addColumn(
                //     'user_type',
                //     function ($app) {                        
                //     if($app->user_type && $app->user_type==1){
                //        $anchorUserType='Supplier'; 
                //     }else if($app->user_type && $app->user_type==2){
                //         $anchorUserType='Buyer';
                //     }else{
                //         $anchorUserType='';
                //     }
                //        return $anchorUserType;
                // })
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
                        <a title=\"Pick Lead\"  data-toggle=\"modal\" data-target=\"#pickLead\" data-url =\"" . route('confirm_box', ['user_id' => $app->user_id , 'app_id' => $app->app_id] ) . "\" data-height=\"150px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" href=\"javascript:void();\">Pickup Case</a>
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
                        $act .= "<a  data-toggle=\"modal\" data-target=\"#editAnchorFrm\" data-url =\"" . route('edit_anchor_reg', ['anchor_id' => $users->anchor_id]) . "\" data-height=\"475px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Anchor Detail\"><i class=\"fa fa-edit\"></i></a>";
                     }
                     if(isset($users->file_path)){
                        $act .= "<a  href=". Storage::url($users->file_path) ." class=\"btn btn-action-btn   btn-sm\" type=\"button\" target=\"blank\" title=\"View CAM\"> <i class=\"fa fa-eye\"></i></a>";
                     }
                     if(isset($users->bank_account_id)){
                        $act .= "<a  data-toggle=\"modal\" data-target=\"#edit_bank_account\" data-url =\"" . route('add_anchor_bank_account',['anchor_id' => $users->anchor_id,'bank_account_id'=>$users->bank_account_id]) . "\" data-height=\"475px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Bank Detail\"><i class=\"fa fa-plus-square\"></i></a>";
                     }
                     if(!isset($users->bank_account_id)){
                         $act .= "<a  data-toggle=\"modal\" data-target=\"#add_bank_account\" data-url =\"" . route('add_anchor_bank_account',['anchor_id' => $users->anchor_id]) . "\" data-height=\"475px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Add Bank Detail\"><i class=\"fa fa-plus-square\"></i></a>";
                     }
//                     if(isset($users)){
//                        $act .= "<a  data-toggle=\"modal\" data-target=\"#add_bank_account\" data-url =\"" . route('add_anchor_bank_account',['anchor_id' => $users->anchor_id, 'bank_account_id' => $bank['bank_account_id']]) . "\" data-height=\"475px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Bank Detail\"><i class=\"fa fa-edit\"></i></a>";
//                     }
                     
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
                                             <label class="badge badge-danger current-status">In Active</label>
                                             
                                          </div></b>':'<div class="btn-group ">
                                             <label class="badge badge-success current-status">Active</label>
                                             
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
                    return  "<a title=\"Edit Role\" data-toggle=\"modal\" data-target=\"#addRoleFrm\" data-url =\"" . route('add_role', ['role_id' => $role->id]) . "\" data-height=\"300px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-edit\"></i></a> &nbsp; <a title=\"Manage Permission\" id=\"" . $role->id . "\" href =\"" . route('manage_role_permission', ['role_id' => $role->id, 'name' =>$role->name ]) . "\" rel=\"tooltip\"   > <i class='fa fa-2x fa-cog'></i></a>";
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
                    //$disc = ($role->is_active == 1)?'Active':'Not Active'; 
                    return ($role->is_active == 1)?'<div class="btn-group "> <label class="badge badge-success current-status">Active</label>  
                                          </div></b>':'<div class="btn-group "> <label class="badge badge-danger current-status">In Active</label> </div></b>';
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
                    $user_edit =  "<a title=\"Edit User\"  data-toggle=\"modal\" data-target=\"#manageUserRole\" data-url =\"" . route('edit_user_role', ['role_id' => $role->id,'user_id'=>$role->user_id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-edit\"></i></a>"; 
                    $user_email = "<a title=\"Change User Password\"  data-toggle=\"modal\" data-target=\"#manageUserRolePassword\" data-url =\"" . route('change_user_role_password', ['role_id' => $role->id,'user_id'=>$role->user_id]) . "\" data-height=\"195px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-expeditedssl\"></i></a>";
                    return '<div class="btn-group"><label>'. $user_edit .'</label> <label>'. $user_email .'</label></div>';;
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
                    'product_id',
                    function ($program) {                   
                      return $program->mp_product_name;
                    })
                ->editColumn(
                    'prgm_name',
                    function ($program) {                   
                      return $program->prgm_name;
                    })
                ->editColumn(
                    'f_name',
                    function ($program) {                   
                        return $program->f_name;
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
                        if ($request->get('search_keyword') != '') {                        
                            $query->where(function ($query) use ($request) {
                                $search_keyword = trim($request->get('search_keyword'));
                                $query->where('prgm_name', 'like',"%$search_keyword%")
                                ->orWhere('f_name', 'like', "%$search_keyword%");
                            });                        
                        }
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
                    return number_format($charges->chrg_calculation_amt,2);
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
                            ->orWhere('chrg_calculation_amt', 'like', "%$search_keyword%")
                            ->orWhere('chrg_name', 'like', "%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }
    
     public function getVouchersList(Request $request, $vouchers){
        return DataTables::of($vouchers)
                ->addColumn(
                    'voucher_code',
                    function ($vouchers) {
                    return $vouchers->voucher_name .'('. (date("Y") - 1) .'-'. date('y') .')';
                })
                ->addColumn(
                    'voucher_name',
                    function ($vouchers) {
                    return $vouchers->voucher_name;
                })
                ->addColumn(
                    'transaction_type',
                    function ($vouchers) {
                    return $vouchers->transType->trans_name ?? '';
                })
                ->addColumn(
                    'action',
                    function ($vouchers) {
                    return "No action";
                })
                ->make(true);
    }
    
       public function getLmsChargeLists(Request $request, $charges){
         $this->chrg_applicable_ids = array(
            '1' => 'Limit Amount',
            '2' => 'Outstanding Amount',
            '3' => 'Outstanding Principal',
            '4' => 'Outstanding Interest',
            '5' => 'Overdue Amount'
        );
        return DataTables::of($charges)
                ->rawColumns(['chrg_type'])
                ->addColumn(
                    'chrg_type',
                    function ($charges) {
                   return $charges->ChargeMaster->chrg_name;
                })
                ->addColumn(
                    'chrg_calculation_type',
                    function ($charges) {
                    return $charges->ChargeMaster->chrg_calculation_type == 1 ? 'Fixed' : 'Percent';
                })
                ->addColumn(
                    'chrg_calculation_amt',
                    function ($charges) {
                    return number_format($charges->amount);
                })  
                ->addColumn(
                    'is_gst_applicable',
                    function ($charges) {
                     return (!empty($charges->transaction->gst) && $charges->transaction->gst == 1) ? 'Yes' : 'No'; 
                })      
                 ->addColumn(
                    'charge_percent',
                    function ($charges) {
                     return ($charges->percent) ? $charges->percent : 'N/A'; 
                })   
                ->addColumn(
                    'chrg_applicable_id',
                    function ($charges) {
                   return $this->chrg_applicable_ids[$charges->chrg_applicable_id] ?? 'N/A'; 
                })
                ->addColumn(
                    'effective_date',
                    function ($charges) {
                   return $charges->transaction->trans_date ?: 'N/A';
                }) 
                ->addColumn(
                    'applicability',
                    function ($charges) {
                    return ($charges->ChargeMaster->chrg_type == 1) ? 'Auto' : 'Manual';
                })
                 ->addColumn(
                    'chrg_desc',
                    function ($charges) {
                     return $charges->ChargeMaster->chrg_desc ?? 'N/A';
                })
                ->addColumn(
                    'created_at',
                    function ($charges) {
                    return ($charges->created_at) ? date('d-M-Y',strtotime($charges->created_at)) : '---';
                })
               
                 ->filter(function ($query) use ($request) {
                   if ($request->get('user_id') != '') {
                            $query->whereHas('transaction', function ($query) use ($request) {
                            $search_keyword = trim($request->get('user_id'));
                            $query->where('user_id',$search_keyword);
                        });
                    }
                      if ($request->get('from_date') != '') {
                        $query->where(function ($query) use ($request) {
                            $from = str_replace('/', '-', $request->get('from_date'));
                            $converedDate = date("Y-m-d H:i:s", strtotime($from));
                            $query->whereDate('created_at','>=' , $converedDate);
                        });
                    }
                    if ($request->get('to_date') != '') {
                        $query->where(function ($query) use ($request) {
                            $to_date = str_replace('/', '-', $request->get('to_date'));
                            $query->whereDate('created_at','<=' , date('Y-m-d H:i:s', strtotime($to_date)) );
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
            '4' => 'Pre Offer',
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
                    'product_type',
                    function ($documents) {
                        $productTypes = '';
                        if(isset($documents->product_document)) {
                            foreach ($documents->product_document as $value) {
                                $productTypes .= $value->product->product_name.', ';
                            }
                        }
                    return rtrim($productTypes, ', ');
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
                       $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editDocumentsFrame" title="Edit Document Detail" data-url ="'.route('edit_documents',['id' => $documents->id]).'" data-height="320px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                       $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success' : 'danger').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                     return $status;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = $request->get('search_keyword');
                            $query->where('doc_name', 'like', "%$search_keyword%");
                        });
                    }
                    if ($request->get('doc_type_id') != '') {
                        $query->where(function ($query) use ($request) {
                            $doc_type_id = $request->get('doc_type_id');
                            $query->where('doc_type_id', $doc_type_id);
                        });
                    }
                    if ($request->get('product_type') != '') {
                        $query->whereHas('product_document', function ($query) use ($request) {
                            $product_type = $request->get('product_type');
                            $query->where('product_id', $product_type);
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
                    'status',
                    function ($user) {
                    return ($user->is_active == 1)? 'Active': 'In-active'; 
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
                               'prgm_type',
                               function ($program) {
                           return ($program->prgm_type == 1) ?'Vendor Finance': 'Channel Finance';
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
                            $act = "<a  href='". route('add_sub_program',['anchor_id'=> $program->anchor_id, 'program_id'=> $program->prgm_id ,'parent_program_id' => request()->get('program_id') ,  'action' => 'edit'] )."' class=\"btn btn-action-btn btn-sm\" title=\"Edit Sub-Program\"><i class=\"fa fa-edit\"></a>";
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
                ->rawColumns(['customer_id','customer_name', 'status','limit', 'consume_limit', 'available_limit','anchor','action'])

                ->editColumn(
                    'app_id',
                    function ($customer) {
                        //return $customer->app_id;
                        return \Helpers::formatIdWithPrefix($customer->app_id, 'APP');
                    }
                ) 
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
                        $email = $customer->user->email;

                        $data = '';
                        $data .= $full_name ? '<span><b>Name:&nbsp;</b>'.$full_name.'</span>' : '';
                        $data .= $email ? '<br><span><b>Email:&nbsp;</b>'.$email.'</span>' : '';

                        return $data;
                    }
                )
                ->editColumn(
                    'limit',
                    function ($customer) {
                        $this->totalLimit = 0;
                        if(isset($customer->user->app->prgmLimits)) {
                            foreach ($customer->user->app->prgmLimits as $value) {
                                $this->totalLimit += $value->limit_amt;
                            }
                        }
                    return '<label><i class="fa fa-inr">'.number_format($this->totalLimit).'</i></label>';
                })
                ->editColumn(
                    'consume_limit',
                    function ($customer) {
                        $this->totalCunsumeLimit = 0;
                        if(isset($customer->user->app->acceptedOffers)) {
                            foreach ($customer->user->app->acceptedOffers as $value) {
                                $this->totalCunsumeLimit += $value->prgm_limit_amt;
                            }
                        }
                    return '<label><i class="fa fa-inr">'.number_format($this->totalCunsumeLimit).'</i></label>';
                })
                ->editColumn(
                    'available_limit',
                    function ($customer) {
                    
                    return '<label><i class="fa fa-inr">'.number_format($this->totalLimit - $this->totalCunsumeLimit).'</i></label>';
                })
                ->editColumn(
                    'anchor',
                    function ($customer) {
                        $anchor = ($customer->user->anchor->comp_name) ?: '--';
                        $prgm =  ($customer->user->is_buyer == 1) ? 'Vender Finance' : 'Channel Finance';
                        $data = '';
                        $data .= $anchor ? '<span><b>Anchor:&nbsp;</b>'.$anchor.'</span>' : '';
                        $data .= $prgm ? '<br><span><b>Program:&nbsp;</b>'.$prgm.'</span>' : '';
                        return $data;
                })
                ->editColumn(
                    'status',
                    function ($customer) {
                    if ($customer->is_assign == 0) {
                        return "<label class=\"badge badge-success current-status\">Sanctioned</label>";
                    } else {
                        return "<span style='color:green'>Assigned</span>";
                    }
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        if ($request->has('search_keyword')) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->whereHas('user', function($query1) use ($search_keyword) {
                                $query1->where('f_name', 'like',"%$search_keyword%")
                                ->orWhere('l_name', 'like', "%$search_keyword%")
                                ->orWhere('email', 'like', "%$search_keyword%");
                            });

                        }
                    }
                    if ($request->get('customer_id') != '') {
                        if ($request->has('customer_id')) {
                            $customer_id = trim($request->get('customer_id'));
                                $query->where('customer_id', 'like',"%$customer_id%");
                        }
                    }
                })
                ->make(true);
    }
    
    /*
     * 
     * get all lms customer list
     */
    public function lmsGetDisbursalCustomers(Request $request, $customer)
    {
        return DataTables::of($customer)
                ->rawColumns(['customer_id', 'app_id','bank', 'total_invoice_amt', 'total_disburse_amt', 'total_actual_funded_amt' ,'status', 'action'])
                ->addColumn(
                    'customer_id',
                    function ($customer) {
                        $this->overDueFlag = 0;
                        $disburseAmount = 0;
                        $apps = $customer->app;
                        if ($this->overDueFlag == 0) {
	                        foreach ($apps as $app) {
	                            foreach ($app->invoices as $inv) {
	                                $invoice = $inv->toArray();
	                                $dueDate = strtotime((isset($invoice['invoice_due_date'])) ? $invoice['invoice_due_date'] : ''); // or your date as well
	                                $now = strtotime(date('Y-m-d'));
	                                $datediff = ($dueDate - $now);
	                                $days = round($datediff / (60 * 60 * 24));
	                                if ($this->overDueFlag ==0 && $days < 0) {
	                        			$this->overDueFlag = 1;
	                                }
	                            }
	                        }
	                    }

                        return ($this->overDueFlag == 0) ? "<input type='checkbox' class='user_id' value=".$customer->user_id.">" : '-';
                    }
                )
                ->addColumn(
                    'customer_code',
                    function ($customer) {
                        return $link = $customer->customer_id;
                        // return "<a id=\"" . $customer->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $customer->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                    }
                )
                ->editColumn(
                    'app_id',
                    function ($customer) {
                        return \Helpers::formatIdWithPrefix($customer->app_id, 'APP');
                    }
                )
                ->addColumn(
                    'ben_name',
                    function ($customer) {

                        if ($customer->user->is_buyer == 2) {
                            $benName = (isset($customer->user->anchor_bank_details->acc_name)) ? $customer->user->anchor_bank_details->acc_name : '';
                        } else {
                            $benName =  (isset($customer->bank_details->acc_name)) ? $customer->bank_details->acc_name : '';
                        }
                        return $benName;
                    }
                )     
                ->editColumn(
                    'bank',
                        function ($customer) {
                        if ($customer->user->is_buyer == 2) {
                            $bank_name = (isset($customer->user->anchor_bank_details->bank->bank_name)) ? $customer->user->anchor_bank_details->bank->bank_name : '';
                        } else {
                            $bank_name = (isset($customer->bank_details->bank->bank_name)) ? $customer->bank_details->bank->bank_name : '';
                        }


                        if ($customer->user->is_buyer == 2) {
                            $ifsc_code = (isset($customer->user->anchor_bank_details->ifsc_code)) ? $customer->user->anchor_bank_details->ifsc_code : '';
                        } else {
                            $ifsc_code = (isset($customer->bank_details->ifsc_code)) ? $customer->bank_details->ifsc_code : '';
                        }

                        if ($customer->user->is_buyer == 2) {
                            $benAcc = (isset($customer->user->anchor_bank_details->acc_no)) ? $customer->user->anchor_bank_details->acc_no : '';
                        } else {
                            $benAcc = (isset($customer->bank_details->acc_no)) ? $customer->bank_details->acc_no : '';
                        }

                        $account = '';
                        $account .= $bank_name ? '<span><b>Bank:&nbsp;</b>'.$bank_name.'</span>' : '';
                        $account .= $ifsc_code ? '<br><span><b>IFSC:&nbsp;</b>'.$ifsc_code.'</span>' : '';
                        $account .= $benAcc ? '<br><span><b>Acc. #:&nbsp;</b>'.$benAcc.'</span>' : '';

                        return $account;

                    }
                )
                ->editColumn(
                    'total_invoice_amt',
                    function ($customer) {
                        $invoiceTotal = 0;
                        $apps = $customer->app->toArray();
                        foreach ($apps as $app) {
                            $invoiceTotal += array_sum(array_column($app['invoices'], 'invoice_approve_amount'));
                        }
                        return '<i class="fa fa-inr"></i> '.number_format($invoiceTotal).'';

                })
                ->editColumn(
                    'total_disburse_amt',
                    function ($customer) {
                        $fundedAmount = 0;
                        $apps = $customer->app;
                        foreach ($apps as $app) {
                            foreach ($app->invoices as $inv) {
                                $invoice = $inv->toArray();
                                $margin = $invoice['program_offer']['margin'];
                                $fundedAmount += $this->calculateFundedAmount($invoice, $margin);
                            }
                        }

                        return '<i class="fa fa-inr"></i> '.number_format($fundedAmount);
                })
                ->editColumn(
                    'total_actual_funded_amt',
                    function ($customer) {
                        $disburseAmount = 0;
                        $interest = 0;
                        $apps = $customer->app;
                        foreach ($apps as $app) {
                            foreach ($app->invoices as $inv) {
                                $invoice = $inv->toArray();
                                $margin = $invoice['program_offer']['margin'];
                                $fundedAmount = $this->calculateFundedAmount($invoice, $margin);
                                
                                $tenorDays = $this->calculateTenorDays($invoice);
                                $tInterest = $this->calInterest($fundedAmount, $invoice['program_offer']['interest_rate']/100, $tenorDays);
                                if($invoice['program_offer']['payment_frequency'] == 1 || empty($invoice['program_offer']['payment_frequency'])) {
                                    $interest = $tInterest;
                                }
                                $disburseAmount += round($fundedAmount - $interest, 2);
                            }
                        }

                        return '<i class="fa fa-inr"></i> '.number_format($disburseAmount);
                })
                ->editColumn(
                    'total_invoice',
                    function ($customer) {   
                        $invCount = 0;
                        $apps = $customer->app;
                        foreach ($apps as $app) {
                            foreach ($app->invoices as $inv) {
                                $invCount++;
                            }
                        }                 
                        return $invCount;
                })                       
                ->addColumn(
                    'status',
                    function ($customer) {
                        return ($this->overDueFlag == 1) ? '<label class="badge badge-warning current-status">pending</label>' : '<label class="badge badge-success current-status">success</label>';
                })                       
                ->addColumn(
                    'action',
                    function ($customer) {
                        $act = '';
                        $act = '<a  data-toggle="modal" data-target="#viewDisbursalCustomerInvoice" data-url ="' . route('lms_disbursal_invoice_view', ['user_id' => $customer->user_id, 'status' => $this->overDueFlag]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Invoices"><i class="fa fa-eye"></i></a>';
                        
                        return $act;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        if ($request->has('search_keyword')) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('customer_id', 'like',"%$search_keyword%");
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
            ->rawColumns(['action', 'role', 'amount','is_active'])

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
               // \helpers::getDoaLevelCity($doa);
                return \helpers::getDoaLevelCity($doa);
            })
            ->addColumn(
                    'amount',
                    function ($doa) {
                return \Helpers::formatCurreny($doa->min_amount) . ' - ' . \Helpers::formatCurreny($doa->max_amount);
            })
            ->editColumn(
                    'role',
                    function ($doa) {
                $roles = DoaLevelRole::getDoaLevelRoles($doa->doa_level_id)->map(function ($elem){
                    return $elem->role;
                });
              $rolesName = implode(',', array_unique($roles->toArray()));
                return rtrim($rolesName,', ');
            })                        
            ->addColumn(
                'is_active',
                function ($doa) {
                    return ($doa->is_active == '0')?'<div class="btn-group "> <label class="badge badge-warning current-status">In Active</label> </div></b>':'<div class="btn-group "> <label class="badge badge-success current-status">Active</label> </div></b>';
            })   
            ->addColumn(
                'action',
                function ($doa) {
                    $action = '<a  data-toggle="modal" data-target="#editDoaLevelFrame" data-url ="' . route('edit_doa_level', ['doa_level_id' => $doa->doa_level_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Level"><i class="fa fa-edit"></i></a>';
                    
                    //add_sub_program
                
                    if($doa->is_active){
                        return $action.'<a title="In Active" href="'.route('change_doa_status', [ 'doa_level_id'=> $doa->doa_level_id , 'is_active'=>0 ]).'"  class="btn btn-action-btn btn-sm doa_status "><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }else{
                        return $action.'<a title="Active" href="'.route('change_doa_status', [ 'doa_level_id'=> $doa->doa_level_id , 'is_active'=>1 ]).'"  class="btn btn-action-btn btn-sm  doa_status"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>';
                    }
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
    
    
    function getColenderList($request, $data)
    {
        return DataTables::of($data)
                        ->rawColumns(['action', 'is_active','email' ,'action' ,'status'])
                        ->editColumn(
                                'co_lender_id',
                                function ($data) {
                            return $data->co_lender_id;
                        })
                        ->editColumn(
                                'f_name',
                                function ($data) {
                            return $data->f_name;
                        })
                        ->editColumn(
                                'biz_name',
                                function ($data) {
                            return $data->biz_name;
                        })
                        ->editColumn(
                                'email',
                                function ($data) {
                            return "<a  data-original-title=\"Edit User\"  data-placement=\"top\" class=\"CreateUser\" >" . $data->comp_email . "</a> ";
                        })
                        ->editColumn(
                                'comp_phone',
                                function ($user) {
                            $achorId = $user->comp_phone;
                            return $achorId;
                        })
                        ->editColumn(
                                'created_at',
                                function ($user) {
                            return ($user->created_at) ? date('d-M-Y', strtotime($user->created_at)) : '---';
                        })
                        ->editColumn(
                                'status',
                                function ($user) {
                            
                          
                            if ($user->is_active) {
                                return '<div class="btn-group ">
                                             <label class="badge badge-success current-status">Active</label>
                                             
                                          </div></b>';
                            } else {
                                return '<div class="btn-group ">
                                             <label class="badge badge-warning current-status">In Active</label>
                                             
                                          </div></b>';
                            }
                        })
                        ->editColumn(
                                'action',
                                function ($data) {
                            $act = '';
                             if (Helpers::checkPermission('add_co_lender')) {
                                $act .= '<a data-toggle="modal"  data-height="550px" 
                           data-width="100%" 
                           data-target="#addcolenders"
                           data-url="' . route('add_co_lender', ['co_lender_id' => $data->co_lender_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Co-lender"><i class="fa fa-edit"></i></a>';
                            }
                            
                            return $act;
                           
                        })
                        ->make(true);
    }    
    
    /**
     * get disbursal list
     * 
     * @param object $request
     * @param object $data
     * @return mixed
     */
    function getDisbursalList($request, $data)
    {
        return DataTables::of($data)
                        ->rawColumns(['action', 'is_active', 'email', 'action', 'status'])
                        ->editColumn(
                                'disburse_date',
                                function ($data) {
                            return ($data->disburse_date) ? date('d-M-Y', strtotime($data->disburse_date)) : '---';
                        })
                        ->editColumn(
                                'invoice_no',
                                function ($data) {
                            return $data->invoice_no;
                        })
                        ->editColumn(
                                'inv_due_date',
                                function ($data) {
                            return ($data->inv_due_date) ? date('d-M-Y', strtotime($data->inv_due_date)) : '---';
                        })
                        ->editColumn(
                                'payment_due_date',
                                function ($data) {
                            return ($data->payment_due_date) ? date('d-M-Y', strtotime($data->payment_due_date)) : '---';
                        })
                        ->editColumn(
                                'invoice_approve_amount',
                                function ($data) {
                            return $data->invoice_approve_amount ? number_format($data->invoice_approve_amount) : '';
                        })
                        ->editColumn(
                                'principal_amount',
                                function ($data) {
                            //s dd($data->principal_amount);
                            return $data->principal_amount ? number_format($data->principal_amount) : '';
                        })
                        ->editColumn(
                                'status_name',
                                function ($data) {
                            return $data->status_name;
                        })
                        ->editColumn(
                                'disburse_amount',
                                function ($data) {
                            return $data->disburse_amount;
                        })
                        ->editColumn(
                                'total_interest',
                                function ($data) {
                            return $data->total_interest;
                        })
                        ->addColumn(
                                'settlement_date',
                                function ($data) {
                            return isset($data->settlement_date) ? $data->settlement_date : '-';
                        })
                        ->addColumn(
                                'settlement_amount',
                                function ($data) {
                            return isset($data->total_repaid_amt) ? $data->total_repaid_amt : '-';
                        })
                        ->editColumn(
                                'accured_interest',
                                function ($data) {
                            return isset($data->accured_interest) ? $data->accured_interest : '-';
                        })
                        ->addColumn(
                                'surplus_amount',
                                function ($data) {
                            return isset($data->surplus_amount) ? $data->surplus_amount : '-';
                        })
                        ->addColumn(
                                'action',
                                function ($data) {
                            $act = '<a data-toggle="modal"  data-height="550px" 
                            data-width="100%" 
                            data-target="#viewInterestAccrual"
                            data-url="' . route('view_interest_accrual', ['disbursal_id' => $data->disbursal_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="View Interest Accrual"><i class="fa fa-eye"></i></a>';

                            return $act;
                        })                        
                        ->filter(function ($query) use ($request) {
                            if ($request->get('search_keyword') != '') {
                                $query->where(function ($query) use ($request) {
                                    $search_keyword = trim($request->get('search_keyword'));
                                    $query->where('invoice.invoice_no', 'like', "%$search_keyword%");
                                });
                            }
                            if ($request->get('is_status') != '') {
                                $query->where(function ($query) use ($request) {
                                    $is_status = trim($request->get('is_status'));
                                    $query->where('disbursal.status_id', $is_status);
                                });
                            }
                            if ($request->get('from_date') != '') {
                                $query->where(function ($query) use ($request) {
                                    $from = str_replace('/', '-', $request->get('from_date'));
                                    $converedDate = date("Y-m-d H:i:s", strtotime($from));
                                    $query->whereDate('disbursal.disburse_date','>=' , $converedDate);
                                });
                            }
                            if ($request->get('to_date') != '') {
                                $query->where(function ($query) use ($request) {
                                    $to_date = str_replace('/', '-', $request->get('to_date'));
                                    $query->whereDate('disbursal.disburse_date','<=' , date('Y-m-d H:i:s', strtotime($to_date)) );
                                });
                            }
                        })
                        ->make(true);
    }

    // GST TAX
    public function getAllGST(Request $request, $data)
    {
        return DataTables::of($data)
                ->rawColumns(['is_active', 'action'])
                
                ->addColumn(
                    'tax_id',
                    function ($data) {
                        return $data->tax_id;
                })
                ->addColumn(
                    'tax_val',
                    function ($data) {
                    return $data->tax_value."%";
                })
                ->addColumn(
                    'is_active',
                    function ($data) {
                    $act = $data->is_active;
                    $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editGSTFrame" title="Edit States Detail" data-url ="'.route('edit_Gst', ['tax_id' => $data->tax_id]).'" data-height="310px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                    $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success pt-2 pl-3 pr-3' : 'danger pt-2').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                    return $status;
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('tax_name', 'like',"%$search_keyword%")
                            ->orWhere('code', 'like', "%$search_keyword%");
                        });
                    }
                })
                ->make(true);
    }

    // Segment
    public function getSegmentLists(Request $request, $data)
    {
        return DataTables::of($data)
                ->rawColumns(['is_active', 'action'])
                
                ->addColumn(
                    'id',
                    function ($data) {
                        return $data->id;
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
                    $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editSegmentFrame" title="Edit States Detail" data-url ="'.route('edit_segment', ['id' => $data->id]).'" data-height="150px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                    $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success pt-2 pl-3 pr-3' : 'danger pt-2').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
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

    // Constitution
    public function getAllConstitution(Request $request, $data)
    {
        return DataTables::of($data)
                ->rawColumns(['is_active', 'action'])
                
                ->addColumn(
                    'id',
                    function ($data) {
                        return $data->id;
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
                    $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editConstiFrame" title="Edit States Detail" data-url ="'.route('edit_constitution', ['id' => $data->id]).'" data-height="150px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                    $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success pt-2 pl-3 pr-3' : 'danger pt-2').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
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
    // LMS Customer Address
    public function addressGetCustomers(Request $request, $data)
    {
        return DataTables::of($data)
            ->rawColumns(['action', 'rcu_status'])
            ->addColumn(
                'biz_addr_id',
                function ($data) {
                    return $data->biz_addr_id;
                }
            )

            ->addColumn(
                'action',
                function ($data) use ($request) {

                    $checked = ($data->is_default == 1) ? 'checked' : null;
                    $act = '';

                    if ($data->rcu_status) {
                        $act .= '    <input type="checkbox"  ' . $checked . ' data-rel = "' . \Crypt::encrypt($data->biz_addr_id, $request->get('user_id')) . '"  class="make_default" name="add"><label for="add">Default</label> ';
                    }

                    if (Helpers::checkPermission('edit_addr')) {
                        $act .= '<a data-toggle="modal"  data-height="310px" 
                            data-width="100%" 
                            data-target="#editAddressFrame"
                            data-url="' . route('edit_addr', ['biz_addr_id' => $data->biz_addr_id, 'user_id' => $request->get('user_id')]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Address Detail"><i class="fa fa-edit"></i></a>';
                    }
                    return $act;
                }
            )

            ->editColumn(
                'rcu_status',
                function ($data) {
                    if ($data->rcu_status) {
                        return '<span class="badge badge-success">Active</span>';
                    } else {
                        return '<span class="badge badge-warning current-status">InActive</span>';
                    }
                }
            )
            
            ->filter(function ($query) use ($request) {
                if ($request->get('search_keyword') != '') {
                    $query->where(function ($query) use ($request) {
                        $search_keyword = trim($request->get('search_keyword'));
                        $query->where('chrg_desc', 'like', "%$search_keyword%")
                            ->orWhere('chrg_calculation_amt', 'like', "%$search_keyword%");
                    });
                }
            })
            ->make(true);
    }

    /**
     * get soa list
     * 
     * @param object $request
     * @param object $data
     * @return mixed
     */
    public function getSoaList(Request $request, $data)
    {
        return DataTables::of($data)
        ->rawColumns(['balance','narration'])
            ->addColumn('repay_trans_id', function($trans){
                return $trans->repay_trans_id;
            })
            ->addColumn('customer_id', function($trans){
                $data = '';
                if($trans->lmsUser){
                    $data = $trans->lmsUser->customer_id;
                }
                return $data;
            })
            ->addColumn('customer_name', function($trans){
                $data = '';
                if($trans->user){
                    $data = $trans->user->f_name.' '.$trans->user->m_name.' '.$trans->user->l_name;
                }
                return $data;
            })
            ->addColumn('invoice_no',function($trans){
                $data = '';
                if($trans->disbursal_id && $trans->disburse->invoice ){
                    $data = $trans->disburse->invoice->invoice_no; 
                }
                return $data;
            })
            ->addColumn('batch_no',function($trans){
                return $trans->batchNo;
            })
            ->addColumn('narration',function($trans){
                return $trans->narration;
            })
            ->addColumn(
                'virtual_acc_id',
                function ($trans) {
                    return $trans->virtual_acc_id;
                }
            )
            ->addColumn(
                'value_date',
                function ($trans) {
                    return date('d-m-Y',strtotime($trans->trans_date));
                }
            )
            ->editColumn(
                'trans_date',
                function ($trans) {
                    return date('d-m-Y',strtotime($trans->created_at));
                }
            )
            ->editColumn(
                'trans_type',
                function ($trans) {
                    if($trans->repay_trans_id && $trans->trans_detail->chrg_master_id!='0'){
                        return $trans->oppTransName;
                    }else{
                        return $trans->transname;
                    }
                }
            )
            ->editColumn(
                'currency',
                function ($trans) {
                    return 'INR';
                }
            )
            ->editColumn(
                'debit',
                function ($trans) {
                    if($trans->entry_type=='0'){
                        return number_format($trans->amount,2);
                    }else{
                        return '0.00';
                    }
                }
            )
            ->editColumn(
                'credit',
                function ($trans) {
                    if($trans->entry_type=='1'){
                        return '('.number_format($trans->amount,2).')';
                    }else{
                        return '(0.00)';
                    }
                }
            )
            ->editColumn(
                'balance',
                function ($trans) {
                    $data = '';
                    if($trans->balance<0){
                        $data = '<span style="color:red">'.number_format(abs($trans->balance), 2).'</span>';
                    }else{
                        $data = '<span style="color:green">'.number_format(abs($trans->balance), 2).'</span>';
                    }
                    return $data;
                }
            )
            ->filter(function ($query) use ($request) {

                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $query->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('trans_date', [$from_date, $to_date]);
                    });
                }

                if($request->get('search_keyword')!= ''){
                    $query->where(function ($query) use ($request) {
                        $customer_id = trim($request->get('customer_id'));
                        $query->where('customer_id', '=', "$customer_id");
                    });
                }
              
            })
            ->make(true);
    }
        // Equipment
        public function getEquipments(Request $request, $data)
        {
            return DataTables::of($data)
                    ->rawColumns(['is_active', 'action'])
                    
                    ->addColumn(
                        'id',
                        function ($data) {
                            return $data->id;
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
                        $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editEquipmentFrame" title="Edit Equipment Detail" data-url ="'.route('edit_equipment', ['id' => $data->id]).'" data-height="150px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                        $status = '<div class="btn-group"><label class="badge badge-'.($act==1 ? 'success pt-2 pl-3 pr-3' : 'danger pt-2').' current-status">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                        return $status;
                        }
                    )
                    ->filter(function ($query) use ($request) {
                        if ($request->get('search_keyword') != '') {
                            $query->where(function ($query) use ($request) {
                                $search_keyword = trim($request->get('search_keyword'));
                                $query->where('equipment_name', 'like',"%$search_keyword%");
                            });
                        }
                    })
                    ->make(true);
        }

    /**
     * 
     * get all lms customer list
     */
    public function lmsGetRefundCustomers(Request $request, $data)
    {
        return DataTables::of($data)
                ->rawColumns(['user_id','status', 'action'])
                ->addColumn(
                    'user_id',
                    function ($data) {
                        return '<input type="checkbox" class="user_ids" name="user_id" value="'.$data->user_id.'" onchange="disableInput(this)">';
                    }
                )
                ->addColumn(
                    'customer_code',
                    function ($data) {
                        return $link = $data->customer_id;
                    }
                )
                ->addColumn(
                    'ben_name',
                    function ($data) {
                        if ($data->user->is_buyer == 2) {
                            return (isset($data->user->anchor_bank_details->acc_name)) ? $data->user->anchor_bank_details->acc_name : '';
                        } else {
                            return (isset($data->bank_details->acc_name)) ? $data->bank_details->acc_name : '';
                        }
                    }
                )     
                ->editColumn(
                    'ben_bank_name',
                        function ($data) {
                        if ($data->user->is_buyer == 2) {
                            return (isset($data->user->anchor_bank_details->bank->bank_name)) ? $data->user->anchor_bank_details->bank->bank_name : '';
                        } else {
                            return (isset($data->bank_details->bank->bank_name)) ? $data->bank_details->bank->bank_name : '';
                        }
                        
                    }
                )
                ->editColumn(
                    'ben_ifsc',
                        function ($data) {
                        if ($data->user->is_buyer == 2) {
                            $ifsc_code = (isset($data->user->anchor_bank_details->ifsc_code)) ? $data->user->anchor_bank_details->ifsc_code : '';
                        } else {
                            $ifsc_code = (isset($data->bank_details->ifsc_code)) ? $data->bank_details->ifsc_code : '';
                        }
                        return $ifsc_code;
                    
                })
                ->editColumn(
                    'ben_account_no',
                        function ($data) {
                        if ($data->user->is_buyer == 2) {
                            $benAcc = (isset($data->user->anchor_bank_details->acc_no)) ? $data->user->anchor_bank_details->acc_no : '';
                        } else {
                            $benAcc = (isset($data->bank_details->acc_no)) ? $data->bank_details->acc_no : '';
                        }
                        return $benAcc;
                    
                })
                ->editColumn(
                    'surplus_amount',
                    function ($data) {
                        return $data->surplus_amount;

                })                      
                ->addColumn(
                    'status',
                    function ($data) {
                        return '<label class="badge badge-warning current-status">pending</label>';
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('search_keyword') != '') {
                        if ($request->has('search_keyword')) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('customer_id', 'like',"%$search_keyword%");
                        }
                    }

                    if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                        $query->whereHas('transaction',function ($query) use ($request) {
                            $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                            $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                            $query->WhereBetween('trans_date', [$from_date, $to_date]);
                        });
                    }
                })
                ->make(true);
    }

    /**
     * get Payment Advice list
     * 
     * @param object $request
     * @param object $data
     * @return mixed
     */
    public function getPaymentAdvice(Request $request, $data)
    {
        return DataTables::of($data)
            ->rawColumns(['action'])

            ->addColumn('customer_id',function($trans){
                $data = '';
                if($trans->lmsUser->customer_id){
                    $data = $trans->lmsUser->customer_id; 
                }
                return $data;
            })
            ->addColumn('f_name',function($trans){
                return $trans->f_name.' '.$trans->m_name.' '.$trans->l_name;
            })
            ->addColumn(
                'trans_date',
                function ($transaction) {
                    return date('d-M-Y',strtotime($transaction->trans_date));
                }
            )
            ->editColumn(
                'created_at',
                function ($transaction) {
                    return date('d-M-Y',strtotime($transaction->created_at));
                }
            )
            ->editColumn(
                'amount',
                function ($transaction) {
                    return $transaction->amount;
                }
            )
            ->addColumn(
                'action',
                function ($data) {
                $act = $data->action;
                $refund='';
                if (empty($data->req_id)) {
                $refund = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#paymentRefundInvoice" title="Payment Refund" data-url ="'.route('payment_refund_index', ['trans_id' => $data->trans_id]).'" data-height="350px" data-width="100%" data-placement="top"><i class="fa fa-undo"></a>';
                }
                $download = '<a class="btn btn-action-btn btn-sm"  title="Download Excel sheet" href ="'.route('payment_advice_excel', ['trans_id' => $data->trans_id]).'"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a> &nbsp; '. $refund .'';
                return $download;
                }
            )
            ->filter(function ($query) use ($request) {

                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $query->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('trans_date', [$from_date, $to_date]);
                    });
                }
                if($request->get('search_keyword')!= ''){
                    $query->whereHas('lmsUser',function ($query) use ($request) {
                        $search_keyword = trim($request->get('search_keyword'));
                        $query->where('customer_id', 'like', "%$search_keyword%");                       
                    });
                }
            })
            ->make(true);
    }

    /*      
     * Get application list for colenders
     */
    public function getColenderAppList(Request $request, $app)
    {
        return DataTables::of($app)
                ->rawColumns(['app_id', 'action', 'status'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $link = route('colender_view_offer', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . \Helpers::formatIdWithPrefix($app->app_id, 'APP')  . "</a> ";
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
                    $status = $app->colender->co_lender_status;
                    //$app_status = config('inv_common.app_status');                    
                    return '<label class="badge '.(($status == 0)? "badge-primary":(($status == 1)? "badge-success": "badge-warning")).'">'.(($status == 0)? "Pending":(($status == 1)? "Accepted": "Rejected")).'</label>';

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
                        $query->whereHas('colender', function($query1) use ($request) {
                        $is_status = trim($request->get('is_status'));
                            $query1->where('co_lender_status', $is_status);
                        });                        
                    }
                })
                ->make(true);
    }


    //Base Rate
    public function getBaseRateList(Request $request, $baserates) {

        return DataTables::of($baserates)
                        ->rawColumns(['is_active','action'])
                        ->addColumn(
                                'bank_id', function ($baserates) {
                            return $baserates->bank->bank_name ?: 'N/A';
                        })
                        ->addColumn(
                                'base_rate', function ($baserates) {
                            return $baserates->base_rate;
                        })
                        ->addColumn(
                                'created_at', function ($baserates) {
                            return ($baserates->created_at) ? date('d-M-Y', strtotime($baserates->created_at)) : '---';
                        })
                        ->addColumn(
                                'created_by', function ($baserates) {
                            return $baserates->userDetail->f_name . ' ' . $baserates->userDetail->l_name;
                        })
                        ->addColumn(
                                'is_active', function ($baserates) {
                            $act = $baserates->is_active;
                            $status = '<div class="btn-group"><label class="badge badge-' . ($act == 1 ? 'success' : 'danger') . ' current-status">' . ($act == 1 ? 'Active' : 'In-Active') . '&nbsp; &nbsp;</label></div>';
                            return $status;
                        }
                        )
                        ->addColumn(
                                'action', function ($baserates) {
                             $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editBaseRateFrame" title="Edit Base Rate Detail" data-url ="' . route('edit_base_rate', ['id' => $baserates->id]) . '" data-height="350px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                             return $edit;
                        })
                        ->filter(function ($query) use ($request) {
                            if ($request->get('search_keyword') != '') {
                                $query->whereHas('bank', function ($query) use ($request) {
                                    $search_keyword = trim($request->get('search_keyword'));
                                    $query->where('bank_name', 'like', "%$search_keyword%");
                                });
                            }
                        })
                        ->make(true);
    }

        public function getTransTypeListByDataProvider(Request $request, $dataRecords)
        {
            
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'trans_type',
                        function ($dataRecords) {
                        return $dataRecords->trans_type;
                    }) 
                    ->make(true);
        }

        public function getJournalByDataProvider(Request $request, $dataRecords)
        {
            
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'name',
                        function ($dataRecords) {
                        return $dataRecords->name;
                    }) 
                    ->editColumn(
                        'journal_type',
                        function ($dataRecords) {
                        return $dataRecords->journal_type;
                    }) 
                    ->editColumn(
                        'is_active',
                        function ($dataRecords) {
                        return ($dataRecords->is_active==1) ? 'Yes' : 'No';
                    }) 
                    ->addColumn(
                        'action',
                        function ($dataRecords) {
                            return '<a class="btn btn-action-btn btn-sm" href ="'.route('get_fin_journal', ['journal_id' => $dataRecords->id]).'"><i class="fa fa-edit">Edit</a>';
                        }
                    )
                    ->make(true);
        }

        public function getAccountByDataProvider(Request $request, $dataRecords)
        {
            
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'account_code',
                        function ($dataRecords) {
                        return $dataRecords->account_code;
                    }) 
                    ->editColumn(
                        'account_name',
                        function ($dataRecords) {
                        return $dataRecords->account_name;
                    }) 
                    ->editColumn(
                        'is_active',
                        function ($dataRecords) {
                        return ($dataRecords->is_active==1) ? 'Yes' : 'No';
                    }) 
                    ->addColumn(
                        'action',
                        function ($dataRecords) {
                            return '<a class="btn btn-action-btn btn-sm" href ="'.route('get_fin_account', ['account_id' => $dataRecords->id]).'"><i class="fa fa-edit">Edit</a>';
                        }
                    )
                    ->make(true);
        }

        public function getVariableByDataProvider(Request $request, $dataRecords)
        {
            
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'name',
                        function ($dataRecords) {
                        return $dataRecords->name;
                    })
                    ->make(true);
        }

        public function getJeConfigByDataProvider(Request $request, $dataRecords)
        {
            
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'journal_name',
                        function ($dataRecords) {
                        return $dataRecords->journal_name;
                    })
                    ->editColumn(
                        'journal_type',
                        function ($dataRecords) {
                        return $dataRecords->journal_type;
                    })
                    ->editColumn(
                        'trans_type',
                        function ($dataRecords) {
                        return $dataRecords->trans_type;
                    })
                    ->editColumn(
                        'variable_name',
                        function ($dataRecords) {
                        return $dataRecords->variable_name;
                    })
                    ->addColumn(
                        'action',
                        function ($dataRecords) {
                            return '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#addJiConfig" title="Add Ji Config" data-url ="'.route('add_ji_config', ['je_config_id' => $dataRecords->je_config_id]).'" data-height="600px" data-width="100%" data-placement="top">Add Ji Item</a>'
                            .'<a class="btn btn-action-btn btn-sm" href ="'.route('create_je_config', ['je_config_id' => $dataRecords->je_config_id, 'trans_config_id' => $dataRecords->trans_config_id, 'journal_id' => $dataRecords->journal_id]).'">Edit</a>';
                        }
                    )
                    ->make(true);
        }

        public function getJiConfigByDataProvider(Request $request, $dataRecords)
        {
            
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'account_name',
                        function ($dataRecords) {
                        return $dataRecords->account_name;
                    })
                    ->editColumn(
                        'is_partner',
                        function ($dataRecords) {
                        return $dataRecords->is_partner;
                    })
                    ->editColumn(
                        'label',
                        function ($dataRecords) {
                        return $dataRecords->label;
                    })
                    ->editColumn(
                        'value_type',
                        function ($dataRecords) {
                        return $dataRecords->value_type;
                    })
                    ->editColumn(
                        'config_value',
                        function ($dataRecords) {
                        return $dataRecords->config_value;
                    })
                    ->addColumn(
                        'action',
                        function ($dataRecords) {
                            return '<a class="btn btn-action-btn btn-sm" href ="'.route('add_ji_config', ['je_config_id' => $dataRecords->je_config_id, 'ji_config_id' => $dataRecords->ji_config_id]).'"><i class="fa fa-edit">Edit</a>';
                        }
                    )
                    ->make(true);
        }

        public function getTallyData(Request $request, $dataRecords){
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'date',
                        function ($dataRecords) {
                        return $dataRecords->voucher_date;
                    })
                    ->editColumn(
                        'ledger_name',
                        function ($dataRecords) {
                        return $dataRecords->ledger_name;
                    })
                    ->editColumn(
                        'amount',
                        function ($dataRecords) {
                        return $dataRecords->amount;
                    }) 
                    ->editColumn(
                        'amount_type',
                        function ($dataRecords) {
                        return $dataRecords->entry_type == '1' ? 'Credit' : 'Debit';
                    }) 
                    ->editColumn(
                        'reference_no',
                        function ($dataRecords) {
                        return $dataRecords->ref_no;
                    })
                    ->editColumn(
                        'batch_no',
                        function ($dataRecords) {
                        return $dataRecords->batch_no;
                    })   
                    ->editColumn(
                        'voucher_type',
                        function ($dataRecords) {
                        return $dataRecords->voucher_type;
                    })     
                    ->editColumn(
                        'voucher_code',
                        function ($dataRecords) {
                        return $dataRecords->voucher_code;
                    })      
                    ->editColumn(
                        'mode_of_pay',
                        function ($dataRecords) {
                        return $dataRecords->trans_type;
                    })       
                    ->editColumn(
                        'narration',
                        function ($dataRecords) {
                        return $dataRecords->narration;
                    }) 
                    ->make(true);
        }

        public function getTransactionsByDataProvider(Request $request, $dataRecords){
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'date',
                        function ($dataRecords) {
                        return $dataRecords->date;
                    }) 
                    ->editColumn(
                        'label',
                        function ($dataRecords) {
                        return $dataRecords->label;
                    }) 
                    ->editColumn(
                        'account_name',
                        function ($dataRecords) {
                        return $dataRecords->account_name.'-'.$dataRecords->account_code;
                    }) 
                    ->editColumn(
                        'biz_id',
                        function ($dataRecords) {
                        return $dataRecords->biz_id;
                    })  
                    ->editColumn(
                        'invoice_id',
                        function ($dataRecords) {
                        return $dataRecords->invoice_id;
                    })   
                    ->editColumn(
                        'invoice_no',
                        function ($dataRecords) {
                        return !empty($dataRecords->invoice_id) ? $dataRecords->invoice_id : $dataRecords->trans_id;
                    }) 
                    ->editColumn(
                        'debit_amount',
                        function ($dataRecords) {
                        return $dataRecords->debit_amount;
                    }) 
                    ->editColumn(
                        'credit_amount',
                        function ($dataRecords) {
                        return $dataRecords->credit_amount;
                    })  
                    ->editColumn(
                        'reference',
                        function ($dataRecords) {
                        return $dataRecords->reference;
                    })   
                    ->editColumn(
                        'journals_name',
                        function ($dataRecords) {
                        return $dataRecords->journals_name;
                    })      
                    ->editColumn(
                        'journal_type',
                        function ($dataRecords) {
                        return $dataRecords->journal_type;
                    })    
                    ->editColumn(
                        'full_name',
                        function ($dataRecords) {
                        return $dataRecords->f_name . ' ' . $dataRecords->m_name . ' ' . $dataRecords->l_name;
                    }) 
                    ->make(true);
        }

    
    public function getCreateBatchData(Request $request, $data){
        return DataTables::of($data)
        ->rawColumns(['trans_id','action'])
        ->editColumn(
            'trans_id',
            function ($data) {
                return '<input type="checkbox" id="trans_id'.$data->trans_id.'" name="trans_id[]" value="'.$data->trans_id.'" checked=
                "true" onchange="disableInput(this)">';
            }
        )
        ->addColumn(
            'customer_id',
            function ($data) {
                return $link = $data->lmsUser->customer_id;
            }
        )
        ->addColumn(
            'trans_date',
            function ($data) {
                return date('d-M-Y',strtotime($data->trans_date));
            }
        )     
        ->addColumn(
            'invoice_no',
            function ($data) {
                $result = '';
                if($data->disburse){
                    $result = $data->disburse->invoice->invoice_no;
                }
                return $result;
            }
        )
        ->editColumn(
            'amount',
            function ($data) {
                return $data->amount;
            }
        )
        ->addColumn(
            'balance_amount',
            function ($data) {
                return $data->amount-$data->settled_amount;
            }
        )
        ->addColumn(
            'action',
            function ($data) {
                return '<input type="text"  class="transType'.$data->trans_type.'" transId="trans_id'.$data->trans_id.'" name="settledAmount['.$data->trans_id.']" value="'.($data->amount-$data->settled_amount).'">';

            }
        )     
        ->filter(function ($query) use ($request) {

            if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                $query->where(function ($query) use ($request) {
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                    $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                    $query->WhereBetween('trans_date', [$from_date, $to_date]);
                });
            }
            //if($request->get('user_ids')!= ''){
                $query->where(function ($query) use ($request) {
                    $query->whereIn('user_id',$request->user_ids);
                });
            //}
          
        })                 
     
        ->make(true);
    } 

    public function getEditBatchData(Request $request, $data){
        return DataTables::of($data)
        ->rawColumns(['trans_id','action'])
        ->editColumn(
            'trans_id',
            function ($data) {
                return "<input type='checkbox' class='trans_ids' name='trans_id[$data->trans_id]' value=".$data->trans_id." checked='true'>";
            }
        )
        ->addColumn(
            'customer_id',
            function ($data) {
                return $link = $data->lmsUser->customer_id;
            }
        )
        ->addColumn(
            'trans_date',
            function ($data) {
                return date('d-M-Y',strtotime($data->trans_date));
            }
        )     
        ->addColumn(
            'invoice_no',
            function ($data) {
                $result = '';
                if($data->disburse){
                    $result = $data->disburse->invoice->invoice_no;
                }
                return $result;
            }
        )
        ->editColumn(
            'amount',
            function ($data) {
                return $data->amount;
            }
        )
        ->addColumn(
            'balance_amount',
            function ($data) {
                return $data->amount-$data->settled_amount;
            }
        )
        ->addColumn(
            'action',
            function ($data) {
                return '<input type="text" name="settledAmount['.$data->trans_id.']" value="'.($data->amount-$data->settled_amount).'">';

            }
        )     
        ->filter(function ($query) use ($request) {

           /*  if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                $query->where(function ($query) use ($request) {
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                    $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                    $query->WhereBetween('trans_date', [$from_date, $to_date]);
                });
            }
            //if($request->get('user_ids')!= ''){
                $query->where(function ($query) use ($request) {
                    $query->whereIn('user_id',$request->user_ids);
                });
            //} */
          
        })                 
     
        ->make(true);
    }
    
    public function getRequestList(Request $request, $data){
        return DataTables::of($data)
        ->rawColumns(['ref_code','assignee','assignedBy','action'])
        ->editColumn(
            'ref_code',
            function ($data) {
                $result = '';
                                           
                $result .= '<a 
                data-toggle="modal" 
                data-target="#lms_view_process_refund" 
                data-url="'.route('lms_view_process_refund', ['req_id' => $data->req_id, 'view' => 1 ]).'"
                data-height="400px" 
                data-width="100%" 
                data-placement="top" title="Process Refund" class="btn btn-action-btn btn-sm">' . $data->ref_code . '</a>';
                
                //$result .= $data->ref_code;                              
                return $result;
            }
        )
        ->addColumn(
            'customer_id',
            function ($data) {
                return $data->customer_id;  //$data->req_type_name;
            }
        )
        ->addColumn(
            'biz_entity_name',
            function ($data) {
                return \Helpers::getEntityNameByUserId($data->user_id);  //$data->req_type_name;
            }
        )            
        ->editColumn(
            'type',
            function ($data) {
                return config('lms.REQUEST_TYPE_DISP.'.$data->req_type);  //$data->req_type_name;
            }
        )
        ->editColumn(
            'amount',
            function ($data) {
                return number_format($data->amount,2);
            }
        )     
        ->editColumn(
            'created_at',
            function ($data) {
                return \Helpers::convertDateTimeFormat($data->created_at, 'Y-m-d H:i:s', 'j F, Y h:i A');  //date('d-m-Y',strtotime($data->created_at));
            }
        )
        ->addColumn(
            'assignee',
            function ($data) {
                $assignee = \Helpers::getReqCurrentAssignee($data->req_id);
                //return $data->assignee .  '<br><small>(' . $data->assignee_role . ')</small>';
                return $assignee ? $assignee->assignee .  '<br><small>(' . $assignee->assignee_role . ')</small>' : '';
            }
        )
        ->addColumn(
            'assignedBy',
            function ($data) {
                $from = \Helpers::getReqCurrentAssignee($data->req_id);
                //return $data->assigned_by.  '<br><small>(' . $data->from_role . ')</small>';
                return $from ? $from->assigned_by .  '<br><small>(' . $from->from_role . ')</small>' : '';
            }
        )  
        ->editColumn(
            'status',
            function ($data){
                $roleData = User::getBackendUser(\Auth::user()->user_id);
                $isRequestOwner = \Helpers::isRequestOwner($data->req_id, \Auth::user()->user_id);
                if (isset($roleData[0]) && $roleData[0]->is_superadmin != 1 && $isRequestOwner) {
                    return \Helpers::getApprRequestStatus($data->req_id, \Auth::user()->user_id);
                } else {
                    return config('lms.REQUEST_STATUS_DISP.'. $data->req_status . '.SYSTEM');
                }
            }
        )   
        ->editColumn(
            'action',
            function ($data){
                $result = '';
                $isLastStage = \Helpers::isReqInLastWfStage($data->req_id);
                $isRequestOwner = \Helpers::isRequestOwner($data->req_id, \Auth::user()->user_id);
                if ($isRequestOwner && $data->req_status != config('lms.REQUEST_STATUS.PROCESSED')) {
                    if ($isLastStage) {
                        $data_target = "#lms_move_prev_stage";
                        $route = route('lms_req_move_prev_stage', ['req_id' => $data->req_id, 'back_stage' => 1 ]);
                        $url_title = 'Move to Previous Stage';
                    } else {
                        $data_target = "#lms_move_next_stage";
                        $route = route('lms_req_move_next_stage', ['req_id' => $data->req_id ]);
                        $url_title = 'Move to Next Stage';
                    }
                    $result .= '<a 
                    data-toggle="modal" 
                    data-target="' . $data_target . '" 
                    data-url="'.$route.'"
                    data-height="270px" 
                    data-width="100%" 
                    data-placement="top" title="' . $url_title . '" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';
     
                    //$stage = \Helpers::getRequestCurrentStage($data->req_id);
                    $statusList = \Helpers::getRequestStatusList($data->req_id);                     
                    //if ($data->req_status == config('lms.REQUEST_STATUS.APPROVED')) {
                    if (count($statusList) > 0) {
                        if ($data->req_type == config('lms.REQUEST_TYPE.REFUND')) {                            
                            $result .= '<a 
                            data-toggle="modal" 
                            data-target="#lms_view_process_refund" 
                            data-url="'.route('lms_view_process_refund', ['req_id' => $data->req_id ]).'"
                            data-height="400px" 
                            data-width="100%" 
                            data-placement="top" title="Process Refund" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';
                        }
                    }
                    /* 
                    else {
                        $statusList = \Helpers::getRequestStatusList($data->req_id);                
                        if (count($statusList) > 0) {
                            $result .= '<a 
                            data-toggle="modal" 
                            data-target="#lms_update_request_status" 
                            data-url="'.route('lms_update_request_status', ['req_id' => $data->req_id ]).'"
                            data-height="270px" 
                            data-width="100%" 
                            data-placement="top" title="Update Status" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a>';
                        }                     
                    }
                    * 
                    */
                }
                return $result;
            }
        )  
        ->filter(function ($query) use ($request) {

           /*  if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                $query->where(function ($query) use ($request) {
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                    $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                    $query->WhereBetween('trans_date', [$from_date, $to_date]);
                });
            }
            //if($request->get('user_ids')!= ''){
                $query->where(function ($query) use ($request) {
                    $query->whereIn('user_id',$request->user_ids);
                });
            //} */
          
        })                 
     
        ->make(true);
    }

    public function getBankInvoiceByDataProvider(Request $request, $dataRecords)
    {
        
        return DataTables::of($dataRecords)
                ->editColumn(
                    'batch_id',
                    function ($dataRecords) {
                    return $dataRecords->batch_id;
                }) 
                ->editColumn(
                    'total_users',
                    function ($dataRecords) {
                    return $dataRecords->total_users;
                }) 
                ->editColumn(
                    'total_amt',
                    function ($dataRecords) {
                    return "₹ ".number_format($dataRecords->total_amt);
                }) 
                ->editColumn(
                    'created_by_user',
                    function ($dataRecords) {
                    return $dataRecords->created_by_user;
                }) 
                ->editColumn(
                    'created_at',
                    function ($dataRecords) {
                    //return $dataRecords->created_at->format('j F Y H:i:s A'); 
                    return ($dataRecords->created_at)? date('d-M-Y H:i:s A',strtotime($dataRecords->created_at)) : '---';
                }) 
                ->addColumn(
                    'action',
                    function ($dataRecords) {
                        return '<a class="btn btn-action-btn btn-sm" href ="'.route('backend_get_bank_invoice_customers', ['batch_id' => $dataRecords->disbursal_batch_id]).'">View Customers</a>';
                        //.'<a class="btn btn-action-btn btn-sm" href ="'.route('backend_get_bank_invoice').'"><i class="fa fa-download"></a>';
                    }
                )
                ->make(true);
    }

    public function getBankInvoiceCustomersByDataProvider(Request $request, $dataRecords)
    {
        
        return DataTables::of($dataRecords)
                ->rawColumns(['bank_detail', 'action']) 
                ->editColumn(
                    'customer_id',
                    function ($dataRecords) {
                    return $dataRecords->customer_id;
                }) 
                ->editColumn(
                    'biz_entity_name',
                    function ($dataRecords) {
                    return $dataRecords->biz_entity_name; // \Helpers::formatIdWithPrefix($dataRecords->app_id, 'APP');
                }) 
                ->editColumn(
                    'ben_name',
                    function ($dataRecords) {
                    return $dataRecords->ben_name;
                }) 
                ->editColumn(
                    'bank_detail',
                    function ($dataRecords) {
                        $account = '';
                        $account .= $dataRecords->bank_name ? '<span><b>Bank:&nbsp;</b>'.$dataRecords->bank_name.'</span>' : '';
                        $account .= $dataRecords->ifsc_code ? '<br><span><b>IFSC:&nbsp;</b>'.$dataRecords->ifsc_code.'</span>' : '';
                        $account .= $dataRecords->acc_no ? '<br><span><b>Acc. #:&nbsp;</b>'.$dataRecords->acc_no.'</span>' : '';
                        return $account;
                }) 
                ->editColumn(
                    'total_amt',
                    function ($dataRecords) {
                    return "₹ ".number_format($dataRecords->total_amt);
                }) 
                ->editColumn(
                    'total_invoice',
                    function ($dataRecords) {
                    return $dataRecords->total_invoice;
                }) 
                ->addColumn(
                    'action',
                    function ($dataRecords) use($request) {
                        //return '<a class="btn btn-action-btn btn-sm" href ="'.route('backend_view_disburse_invoice', ['batch_id' => $request->get('batch_id'), 'disbursed_user_id' => $dataRecords->user_id]).'"><i class="fa fa-eye" /></a>';
                        return '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#disburseInvoicePopUp" title="Disburse Invoice" data-url ="'.route('backend_view_disburse_invoice', ['batch_id' => $request->get('batch_id'), 'disbursed_user_id' => $dataRecords->user_id]).'" data-height="600px" data-width="100%" data-placement="top"><i class="fa fa-eye" /></a>';
                    }
                )
                ->make(true);
    }

    public function getDisburseInvoiceByDataProvider(Request $request, $dataRecords)
    {
        
        return DataTables::of($dataRecords)
                ->rawColumns([]) 
                ->editColumn(
                    'app_id',
                    function ($dataRecords) {
                    return \Helpers::formatIdWithPrefix($dataRecords->app_id, 'APP');
                }) 
                ->editColumn(
                    'invoice_no',
                    function ($dataRecords) {
                    return $dataRecords->invoice_no; 
                }) 
                ->editColumn(
                    'disburse_date',
                    function ($dataRecords) {
                    return $dataRecords->disburse_date;
                })  
                ->editColumn(
                    'inv_due_date',
                    function ($dataRecords) {
                    return $dataRecords->inv_due_date;
                })               
                ->editColumn(
                    'disburse_amount',
                    function ($dataRecords) {
                    return "₹ ".number_format($dataRecords->disburse_amount);
                })    
                ->editColumn(
                    'disburse_type',
                    function ($dataRecords) {
                    return ($dataRecords->disburse_type==1) ? 'Online' : 'Offline';
                })              
                ->make(true);
    }


    /*
     * 
     * get all lms customer list
     */
    public function lmsGetSentToBankInvCustomers(Request $request, $disbursal)
    {
        return DataTables::of($disbursal)
                ->rawColumns(['disburse_detail','batch_id','bank', 'total_actual_funded_amt' ,'status', 'action'])
                ->editColumn(
                    'batch_id',
                    function ($disbursal) {
                        return (isset($disbursal->disbursal_batch->batch_id)) ? $disbursal->disbursal_batch->batch_id : '';
                    }
                )
                ->addColumn(
                    'customer_id',
                    function ($disbursal) {
                        return $link = $disbursal->lms_user->customer_id;
                        // return "<a id=\"" . $disbursal->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $disbursal->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                    }
                )
                ->addColumn(
                    'ben_name',
                    function ($disbursal) {

                        if ($disbursal->lms_user->user->is_buyer == 2) {
                            $benName = (isset($disbursal->lms_user->user->anchor_bank_details->acc_name)) ? $disbursal->lms_user->user->anchor_bank_details->acc_name : '';
                        } else {
                            $benName =  (isset($disbursal->lms_user->bank_details->acc_name)) ? $disbursal->lms_user->bank_details->acc_name : '';
                        }
                        return $benName;
                    }
                )     
                ->editColumn(
                    'bank',
                        function ($disbursal) {
                        if ($disbursal->lms_user->user->is_buyer == 2) {
                            $bank_name = (isset($disbursal->lms_user->user->anchor_bank_details->bank->bank_name)) ? $disbursal->lms_user->user->anchor_bank_details->bank->bank_name : '';
                        } else {
                            $bank_name = (isset($disbursal->lms_user->bank_details->bank->bank_name)) ? $disbursal->lms_user->bank_details->bank->bank_name : '';
                        }


                        if ($disbursal->lms_user->user->is_buyer == 2) {
                            $ifsc_code = (isset($disbursal->lms_user->user->anchor_bank_details->ifsc_code)) ? $disbursal->lms_user->user->anchor_bank_details->ifsc_code : '';
                        } else {
                            $ifsc_code = (isset($disbursal->lms_user->bank_details->ifsc_code)) ? $disbursal->lms_user->bank_details->ifsc_code : '';
                        }

                        if ($disbursal->lms_user->user->is_buyer == 2) {
                            $benAcc = (isset($disbursal->lms_user->user->anchor_bank_details->acc_no)) ? $disbursal->lms_user->user->anchor_bank_details->acc_no : '';
                        } else {
                            $benAcc = (isset($disbursal->lms_user->bank_details->acc_no)) ? $disbursal->lms_user->bank_details->acc_no : '';
                        }

                        $account = '';
                        $account .= $bank_name ? '<span><b>Bank:&nbsp;</b>'.$bank_name.'</span>' : '';
                        $account .= $ifsc_code ? '<br><span><b>IFSC:&nbsp;</b>'.$ifsc_code.'</span>' : '';
                        $account .= $benAcc ? '<br><span><b>Acc. #:&nbsp;</b>'.$benAcc.'</span>' : '';

                        return $account;

                    }
                )
                ->editColumn(
                    'total_actual_funded_amt',
                    function ($disbursal) {

                        return '<i class="fa fa-inr"></i> '.number_format($disbursal->total_disburse_amount);
                })
                ->editColumn(
                    'total_invoice',
                    function ($disbursal) {   
                        return $disbursal->total_invoice;
                }) 
                 ->addColumn(
                    'disburse_detail',
                    function ($disbursal) {                        
                        $inv_date = '';
                        $inv_date .= $disbursal->disburse_date ? '<span><b>Disburse Date:&nbsp;</b>'.Carbon::parse($disbursal->disburse_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $disbursal->payment_due_date ? '<br><span><b>Payment Due Date:&nbsp;</b>'.Carbon::parse($disbursal->payment_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $disbursal->invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$disbursal->invoice->tenor.'</span>' : '';
                        return $inv_date;
                })  
                ->addColumn(
                    'action',
                    function ($disbursal) {
                        $act = '';
                        $act = '<a  data-toggle="modal" data-target="#viewBatchSendToBankInvoice" data-url ="' . route('view_batch_user_invoice', ['user_id' => $disbursal->user_id, 'disbursal_batch_id' => $disbursal->disbursal_batch_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Invoices"><i class="fa fa-eye"></i></a>';
                        $act .= '<a  data-toggle="modal" data-target="#invoiceDisbursalTxnUpdate" data-url ="' . route('invoice_udpate_disbursal', ['user_id' => $disbursal->user_id, 'disbursal_batch_id' => $disbursal->disbursal_batch_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Invoices"><i class="fa fa-plus-square"></i></a>';
                        
                        return $act;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('customer_code') != '') {
                        if ($request->has('customer_code')) {
                            $customer_code = trim($request->get('customer_code'));
                            $query->whereHas('lms_user', function($query1) use ($customer_code) {
                                $query1->where('customer_id', 'like',"%$customer_code%");
                            });

                        }
                    }
                    if ($request->get('selected_date') != '') {
                        if ($request->has('selected_date')) {
                            $selected_date = trim($request->get('selected_date'));
                            $query->whereHas('disbursal_batch', function($query1) use ($selected_date) {
                                $query1->where('created_at', 'like',"%$selected_date%");
                            });

                        }
                    }
                    if ($request->get('batch_id') != '') {
                        if ($request->has('batch_id')) {
                            $batch_id = trim($request->get('batch_id'));
                            $query->whereHas('disbursal_batch', function($query1) use ($batch_id) {
                                $query1->where('disbursal_batch_id', 'like',"%$batch_id%");
                            });

                        }
                    }
                })
                ->make(true);
    }
}
