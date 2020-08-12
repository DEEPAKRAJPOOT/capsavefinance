<?php
namespace App\Libraries\Ui;
use DB;
use Auth;
use Config;
use Helpers;
use Session;
use DateTime;
use DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\User;
use Illuminate\Support\Facades\Crypt;
use App\Inv\Repositories\Models\Anchor;
use Illuminate\Support\Facades\Storage;
use App\Libraries\Ui\DataRendererHelper;
use App\Contracts\Ui\DataProviderInterface;
use App\Inv\Repositories\Models\AnchorUser;
use App\Inv\Repositories\Models\BizInvoice;
use App\Inv\Repositories\Models\Application;
use App\Inv\Repositories\Models\AppAssignment;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Contracts\Traits\LmsTrait;
use App\Inv\Repositories\Models\Master\DoaLevelRole;
use App\Inv\Repositories\Models\Lms\InterestAccrualTemp;
use App\Inv\Repositories\Models\Lms\UserInvoiceRelation;

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
                ->rawColumns(['id','name', 'checkbox', 'anchor', 'action', 'email','assigned', 'active'])
                ->addColumn(
                    'id',
                    function ($user) {
                    //$link = '000'.$user->user_id;
                    $link = \Helpers::formatIdWithPrefix($user->user_id, 'LEADID');
                        return "<a id=\"" . $user->user_id . "\" href=\"".route('lead_detail', ['user_id' => $user->user_id])."\" rel=\"tooltip\"   >$link</a> ";
                        
                    }
                )
                ->editColumn(
                        'name',
                        function ($user) {
                    $panInfo = $user->pan_no && !empty($user->pan_no) ? '<br><strong>PAN:</strong> ' . $user->pan_no : ''; 
                    $full_name = $user->f_name.' '.$user->l_name . $panInfo;
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
                       //$userInfo=User::getUserByAnchorId((int) $user->UserAnchorId);
                       //$achorId= $userInfo->f_name.' '.$userInfo->l_name;
                        $achorId = Helpers::getAnchorsByUserId($user->user_id);
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
                // ->editColumn(
                //         'assigned',
                //         function ($user) {
                //     if ($user->is_assign == 0) {
                //         return "<label class=\"badge badge-warning current-status\">Pending</label>";
                //     } else {
                //         return "<span style='color:green'>Assigned</span>";
                //     }
                // })
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
                                ->orWhere(\DB::raw("CONCAT(rta_users.f_name,' ',rta_users.l_name)"), 'like', "%$by_nameOrEmail%")
                                ->orWhere('users.email', 'like', "%$by_nameOrEmail%")
                                ->orWhere('anchor_user.pan_no', 'like', "%$by_nameOrEmail%");
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
                    if ($request->get('pan') != '') {
                        $query->where(function ($query) use ($request) {
                            $pan = $request->get('pan');
                            $query->where('anchor_user.pan_no', $pan);
                        });
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
                ->rawColumns(['app_id','biz_entity_name','assignee', 'status', 'assigned_by', 'action','assoc_anchor', 'contact','name', 'app_code'])
                ->addColumn(
                    'app_code',
                    function ($app) {
                        $user_role = Helpers::getUserRole(\Auth::user()->user_id)[0]->pivot->role_id;
                        $app_id = $app->app_id;
                        $app_code = $app->app_code;
                        $parent_app_id = $app->parent_app_id;
                        $ret = '';
                        $permission = Helpers::checkPermission('company_details');
                        if($permission){
                           if($user_role == config('common.user_role.APPROVER'))
                                $link = route('cam_report', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);
                           else
                                $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);
                           $ret = "<a id='app-id-$app_id' href='$link' rel='tooltip'>" . $app_code . "</a>";
                                                     
                        } else {
                            $ret = "<a id='app-id-$app_id' rel='tooltip'>" . $app_code . "</a>";
                        }
                        
                        if (!empty($parent_app_id)) {
                            $aData = Application::getAppData((int)$parent_app_id);
                            if ($permission) {
                                $ret .= "<br><small>Parent:</small><br><a href='" . route('company_details', ['biz_id' => $aData->biz_id, 'app_id' => $parent_app_id]) . "' rel='tooltip'>" . \Helpers::formatIdWithPrefix($parent_app_id, 'APP') . "</a>";
                            } else {
                                $ret .= "<br><small>Parent:</small><br><a rel='tooltip'>" . \Helpers::formatIdWithPrefix($parent_app_id, 'APP') . "</a>";
                            }
                        } 
                           
                        return $ret;
                    }
                )
                ->addColumn(
                    'biz_entity_name',
                    function ($app) {                        
                        $panInfo = $app->pan_no && !empty($app->pan_no) ? '<br><strong>PAN:</strong> ' . $app->pan_no : ''; 
                        return $app->biz_entity_name ? $app->biz_entity_name . $panInfo : '';
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
                // ->addColumn(
                //     'assoc_anchor',
                //     function ($app) {
                    
                //     if($app->anchor_id){
                //         $achorName = Helpers::getAnchorsByUserId($app->user_id);
                //     } else {
                //        $achorName='';  
                //     }                    
                //     return $achorName;
                    
                // })
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
                    //$app_status = config('common.app_status');                    
                    //$status = isset($app_status[$app->status]) ? $app_status[$app->status] : '';    // $app->status== 1 ? 'Completed' : 'Incomplete';
                    $status = isset($app->status_name) ? $app->status_name : ''; 

                    $link = '<a title="View Application Status" href="#" data-toggle="modal" data-target="#viewApplicationStatus" data-url="' . route('view_app_status_list', ['app_id' => $app->app_id, 'note_id' => $app->note_id, 'user_id' => $app->user_id, 'curr_status_id' => $app->curr_status_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="aprveAppListBtn">View Status</a>';

                    if(Helpers::checkPermission('view_app_status_list') ){
                        $status .= $link;                        
                    }
                    return $status;
                })
                ->addColumn(
                    'action',
                    function ($app) use ($request) {
                        $act = '';
                        $lmsStatus = config('lms.LMS_STATUS');
                        $view_only = Helpers::isAccessViewOnly($app->app_id);
                        if ($view_only && in_array($app->status, [0,1,2])) {
                           //if(Helpers::checkPermission('add_app_note')){
                                $act = $act . '<a title="Add App Note" href="#" data-toggle="modal" data-target="#addCaseNote" data-url="' . route('add_app_note', ['app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="190px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-sticky-note" aria-hidden="true"></i></a>';
                            //}                            
                        }
                        if ($view_only && $app->status == 1) {
                          //// $act = $act . '<a title="Copy application" href="#" data-toggle="modal" data-target="#addAppCopy" data-url="' . route('add_app_copy', ['user_id' =>$app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]) . '" data-height="190px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm">Copy Application</a>';

                            if(Helpers::checkPermission('send_case_confirmBox')){
                                $currentStage = Helpers::getCurrentWfStage($app->app_id);
                                $roleData = Helpers::getUserRole();     
                                $hasSupplyChainOffer = Helpers::hasSupplyChainOffer($app->app_id);
                                if ($currentStage && ( (!$lmsStatus && $currentStage->order_no < 16) || ($lmsStatus && $currentStage->order_no <= 16) ) ) {                                                                                                           
                                    $moveToBackStageUrl = '&nbsp;<a href="#" title="Move to Back Stage" data-toggle="modal" data-target="#assignCaseFrame" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id'), 'assign_case' => 1]) . '" data-height="320px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-reply" aria-hidden="true"></i></a> ';
                                    if ($currentStage->order_no == 16 && !$hasSupplyChainOffer ) {
                                        if ($app->curr_status_id != config('common.mst_status_id')['DISBURSED']) {
                                            $act = $act . $moveToBackStageUrl;
                                        }
                                    } else {
                                        $act = $act . '&nbsp;<a href="#" title="Move to Next Stage" data-toggle="modal" data-target="#sendNextstage" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="370px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-share" aria-hidden="true"></i></a> ';    

                                        if ($roleData[0]->id != 4 && !empty($currentStage->assign_role)) {
                                            $act = $act . $moveToBackStageUrl;
                                        }
                                    }
                                }
                            }                                                        
                        }
                        
                        
                        if ($lmsStatus && $app->renewal_status == 1) {
                            $act = $act . '&nbsp;<a href="#" title="Copy/Renew Application" data-toggle="modal" data-target="#confirmCopyApp" data-url="' . route('copy_app_confirmbox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id, 'app_type' => 1]) . '" data-height="200px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-files-o" aria-hidden="true"></i></a> ';
                        }

                        //$where=[];
                        //$where['user_id'] = $app->user_id;
                        //$where['status'] = [0,1];
                        //$appData = Application::getApplicationsData($where);
                        $appData = Application::checkAppByPan($app->user_id);
                        if ($lmsStatus && $app->status == 2 && !$appData) { //Limit Enhancement
                            $act = $act . '&nbsp;<a href="#" title="Limit Enhancement" data-toggle="modal" data-target="#confirmEnhanceLimit" data-url="' . route('copy_app_confirmbox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id, 'app_type' => 2]) . '" data-height="200px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-user-plus" aria-hidden="true"></i></a> ';
                            $act = $act . '&nbsp;<a href="#" title="Reduce Limit" data-toggle="modal" data-target="#confirmReduceLimit" data-url="' . route('copy_app_confirmbox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id, 'app_type' => 3]) . '" data-height="200px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-user-times" aria-hidden="true"></i></a> ';
                        }
                        
                        //Route for Application Rejection
                        // if (Helpers ::checkPermission('reject_app') && ($app->curr_status_id === null && $app->curr_status_id !== config('common.mst_status_id')['APP_REJECTED'])) {
                        if (Helpers::isChangeAppStatusAllowed($app->curr_status_id) && Helpers ::checkPermission('reject_app')) {
                           $act = $act . '<a title="Modify Status" href="#" data-toggle="modal" data-target="#rejectApplication" data-url="' . route('reject_app', ['app_id' => $app->app_id, 'note_id' => $app->note_id, 'user_id' => $app->user_id, 'curr_status_id' => $app->curr_status_id]) . '" data-height="250px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-cog" aria-hidden="true"></i></a>';
                        }
                        
                        return $act;
                                      
                    }
                )
                ->filter(function ($query) use ($request) {
                    
                    if ($request->get('search_keyword') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->where('app.app_code', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%")
                            ->orWhere('anchor_user.pan_no', 'like', "%$search_keyword%");
                        });                        
                    }
                    if ($request->get('is_assign') != '') {
                        $query->where(function ($query) use ($request) {
                            $is_assigned = $request->get('is_assign');
                            $query->where('app.is_assigned', $is_assigned);
                        });
                    }
                    if ($request->get('status') != '') {
                        $query->where(function ($query) use ($request) {
                            $status = $request->get('status');
                            if ($status == 1 || $status == 2) {
                                $query->where('app.renewal_status', $status);  
                            } else if ($status == 3) {
                                $query->where('app.app_type', 2);
                            } else if ($status == 4) {
                                $query->where('app.app_type', 3);
                            } else {
                                $query->where('app.curr_status_id', $status);
                            }
                        });
                    }  
                    
                    if ($request->get('pan') != '') {
                        $query->where(function ($query) use ($request) {
                            $pan = $request->get('pan');
                            $query->where('anchor_user.pan_no', $pan);
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
                ->rawColumns(['app_id', 'action','assoc_anchor', 'status', 'app_code'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $app_code = $app->app_code;
                        $link = route('backend_fi', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . $app_code. "</a> ";
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
                       //$userInfo=User::getUserByAnchorId((int)$app->anchor_id);
                       //$achorName= ($userInfo)? ucwords($userInfo->f_name.' '.$userInfo->l_name): 'NA';
                        $achorName = Helpers::getAnchorsByUserId($app->user_id); 
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
                    $app_status = config('common.app_status');                    
                    return '<label class="badge '.(($app->status == 1 || $app->status == 2)? "badge-primary":"badge-warning").'">'.(($app->status == 1 || $app->status == 2)? $app_status[$app->status] : $app_status[$app->status] ).'</label>';

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
                            $query->where('app.app_code', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%");
                        });                        
                    }
                    // if ($request->get('is_status') != '') {
                    //     $query->where(function ($query) use ($request) {
                    //         $is_assigned = $request->get('is_status');
                    //         $query->where('app.status', $is_assigned);
                    //     });
                    // }
                    
                })
                ->make(true);
    }

    /*      
     * Get user application list for frontend
     */
    public function getUserAppList(Request $request, $app)
    {
      
        return DataTables::of($app)
                ->rawColumns(['app_id', 'action', 'assoc_anchor', 'status'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $link = route('business_information_open', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . \Helpers::formatIdWithPrefix( $app->app_id, 'APP') . "</a> ";
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
                       //$userInfo=User::getUserByAnchorId((int) $app->anchor_id);
                         //$achorName= ($userInfo)? ucwords($userInfo->f_name.' '.$userInfo->l_name): 'NA';
                        $achorName = Helpers::getAnchorsByUserId($app->user_id);
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
                    $app_status = config('common.app_status');                    
                    //return '<label class="badge '.(($app->status == 1)? "badge-primary":"badge-warning").'">'.(isset($app_status[$app->status]) ? $app_status[$app->status] : '' ).'</label>';
                    $app_status_class = config('common.APP_STATUS_LABEL_CLASS.'.$app->curr_status_id) ? config('common.APP_STATUS_LABEL_CLASS.'.$app->curr_status_id) : 'badge-primary';
                    return '<label class="badge '. $app_status_class .'">'.(isset($app->status_name) ? $app->status_name : '' ).'</label>';

                })
                ->addColumn(
                    'action',
                    function ($app) use ($request) {
                        return '<div class="d-flex inline-action-btn">
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
                            $query->where('app.curr_status_id', $is_assigned);
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
                       if($invoice->mstStatus->id!=12) { 
                        $inv_date .= $invoice->invoice_date ? '<span><b>Inv. Date:&nbsp;</b>'.$invoice->invoice_date.'</span>' : '';
                        $inv_date .= $invoice->invoice_due_date ? '<br><span><b>Inv. Due Date:&nbsp;</b>'.$invoice->invoice_due_date.'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor IN Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                       }
                       else
                       {
                           
                        $inv_date .= $invoice->disbursal ? '<span><b>Disburse Date:&nbsp;</b>'.Carbon::parse($invoice->disbursal->disburse_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->disbursal ? '<br><span><b>Payment Due Date:&nbsp;</b>'.Carbon::parse($invoice->disbursal->payment_due_date)->format('d-m-Y').'</span>' : '';
                        $inv_date .= $invoice->tenor ? '<br><span><b>Tenor In Days:&nbsp;</b>'.$invoice->tenor.'</span>' : '';
                       
                       }
                        return $inv_date;
                })  
                ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        $inv_amount = '';
                      if($invoice->mstStatus->id!=12) 
                      {   
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amount:&nbsp;</b>'.$invoice->invoice_amount.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Inv. Approve Amount:&nbsp;</b>'.$invoice->invoice_approve_amount.'</span>' : '';
                      }
                      else
                      {
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.:&nbsp;</b>'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= ($invoice->disbursal) ? '<br><span><b>Disburse Amt.:&nbsp;</b>'.number_format($invoice->disbursal->principal_amount).'</span>' : '';
                        $inv_amount .= ($invoice->disbursal) ? '<br><span><b>Actual Disburse Amt.:&nbsp;</b>'.number_format($invoice->disbursal->disburse_amount).'</span>' : '';
                       
                      }
                        return $inv_amount;
                })
                 ->addColumn(            
                    'status',
                    function ($invoice) {                        
                    
                        $act = $invoice->mstStatus->status_name ? $invoice->mstStatus->status_name : '';

                        if($invoice->invoice_disbursed) { 
                        $act .='&nbsp;&nbsp;<a data-toggle="modal"  data-height="550px" data-width="100%" data-target="#viewInterestAccrual" data-url="' . route('view_interest_accrual', ['invoice_disbursed_id' =>$invoice->invoice_disbursed->invoice_disbursed_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="View Interest Accrual"><i class="fa fa-eye"></i></a>';
                        }
                        return $act;
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('invoice_no') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $invoice_keyword = trim($request->get('invoice_no'));
                            $query->where('invoice_no',"$invoice_keyword");
                        });                        
                    }
                    
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
                        $inv_approval = Config::get('common.inv_approval');
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        $user_type  =  DB::table('users')->where(['user_id' => $id])->first();
                        if(in_array($chkUser->id,$inv_approval) && $user_type->user_type==2)
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
                       
                             return '<input type="checkbox" data-id="'.$invoice->supplier_id.'" name="chkstatus" value="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="chkstatus">';
                      }
                      else {
                        return "";
                      }
                    })
                  ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                         return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
                  
                       
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span></br>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.</b>:&nbsp;'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                        
                })
                ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(
                    'action',
                    function ($invoice) {
                     $action ="";
                      if(($invoice->file_id != 0)) {
                          $action .='<a href="'.route('download_storage_file', ['file_id' => $invoice->userFile->file_id ]).'"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
                         } else  {
                            /// return '<input type="file" name="doc_file" id="file'.$invoice->invoice_id.'" dir="1"  onchange="uploadFile('.$invoice->app_id.','.$invoice->invoice_id.')" title="Upload Invoice">';
                           $action .='<div class="image-upload"><label for="file-input"><i class="fa fa-upload circle btnFilter" aria-hidden="true"></i> </label>
                                     <input name="doc_file" id="file-input" type="file" class="file'.$invoice->invoice_id.'" dir="1"  onchange="uploadFile('.$invoice->app_id.','.$invoice->invoice_id.')" title="Upload Invoice"/></div>';
                         }   
                        $id = Auth::user()->user_id;
                        $inv_approval = Config::get('common.inv_approval');
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        $user_type  =  DB::table('users')->where(['user_id' => $id])->first();
                        if(in_array($chkUser->id,$inv_approval) && $user_type->user_type==2)
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
                     if($customer!=3)
                     {  
                      $action .='<a title="Edit" href="#" data-amount="'.(($invoice->invoice_amount) ? $invoice->invoice_amount : '' ).'" data-approve="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount : '' ).'"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" data-toggle="modal" data-target="#myModal7" class="btn btn-action-btn btn-sm changeInvoiceAmount"><i class="fa fa-edit" aria-hidden="true"></i></a>';
                     }
                      $expl  =  explode(",",$invoice->program->invoice_approval); 
                      if(in_array($customer, $expl)) 
                      {  
                        
                          $action .='<a title="Approve" data-status="8" data-amount="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount  : '' ).'"  data-user="'.(($invoice->supplier_id) ? $invoice->supplier_id : '' ).'"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="btn btn-action-btn btn-sm pendingApproveInv"><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>';
                      
                      }
                      return $action;
                })
                   ->filter(function ($query) use ($request) {
                  
                    if ($request->get('biz_id') != '') {                        
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
                          $action .='<a href="'.route('download_storage_file', ['file_id' => $invoice->userFile->file_id ]).'" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>';
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
                    $id = Auth::user()->user_id;
                    $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                    $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                    if($chkUser->id!=11) 
                        {
                           return '<input type="checkbox" name="chkstatus" value="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="chkstatus">';
                
                        }
                        })
                 ->addColumn(
                    'invoice_id',
                    function ($invoice) use ($request)  {     
                         
                              return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                       
             })
             
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span></br>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.</b>:&nbsp;'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.\Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })
               ->addColumn(
                    'action',
                    function ($invoice) {
                     $action = "";
                     $id = Auth::user()->user_id;
                     $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                     $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                      if($chkUser->id!=11) 
                     {
                      $action .='<a title="Disbursed Que" data-status="9"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class="btn btn-action-btn btn-sm disburseInv"><i class="fa fa-share-square" aria-hidden="true"></i></a>';
                      $action .='</br></br><div class="d-flex"><select  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv2"><option value="0">Change Status</option><option value="7">Pending</option><option value="14"> Reject</option></select></div>';
                     }
                     return  $action;
                })
                 ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
                });
                        }
                    
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
                        $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();

                        $this->overDueFlag = 0;
                        $disburseAmount = 0;
                        $apps = $invoice->supplier->apps;
                        if ($this->overDueFlag == 0) {
                            foreach ($apps as $app) {
                                foreach ($app->disbursed_invoices as $inv) {
                                    $invc = $inv->toArray();
                                    $invc['invoice_disbursed'] = $inv->invoice_disbursed->toArray();
                                    if ((isset($invc['invoice_disbursed']['payment_due_date']))) {
                                        if (!is_null($invc['invoice_disbursed']['payment_due_date'])) {
                                            $calDay = $invc['invoice_disbursed']['grace_period'];
                                            $dueDate = strtotime($invc['invoice_disbursed']['payment_due_date']."+ $calDay Days");
                                            $dueDate = $dueDate ?? 0; // or your date as well
                                            $now = strtotime(date('Y-m-d'));
                                            $datediff = ($dueDate - $now);
                                            $days = round($datediff / (60 * 60 * 24));
                                            if ($this->overDueFlag == 0 && $days < 0 && $invc['is_repayment'] == 0) {
                                                $this->overDueFlag = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                       // return  "<input type='checkbox' class='invoice_id' name='checkinvoiceid' value=".$invoice->invoice_id.">";
                        return ($this->overDueFlag == 1 || $chkUser->id == 11 ) ? '-' : "<input type='checkbox' class='invoice_id' name='checkinvoiceid' value=".$invoice->invoice_id.">";

                     })
                ->addColumn(
                    'anchor_id',
                    function ($invoice) use ($request)  {    
                   
                        return '<a href="'.route("view_invoice_details",["invoice_id" => $invoice->invoice_id]).'">'.$invoice->invoice_no.'</a>';
        
                })
               ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                       $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span></br>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.</b>:&nbsp;'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                })
                    ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'. \Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(
                    'action',
                    function ($invoice) {
                        $id = Auth::user()->user_id;
                        $inv_approval = Config::get('common.inv_approval');
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        $user_type  =  DB::table('users')->where(['user_id' => $id])->first();
                        if(in_array($chkUser->id,$inv_approval) && $user_type->user_type==2)
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
                    if($customer!=3 && $chkUser->id!=11)
                      { 
                          $action .='</br><div class="d-flex"><select data-amount="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount  : '' ).'"  data-user="'.(($invoice->supplier_id) ? $invoice->supplier_id : '' ).'"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv3"><option value="0">Change Status</option><option value="7">Pending</option>';
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
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span></br>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.</b>:&nbsp;'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                })
                ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
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
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span></br>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.</b>:&nbsp;'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                })
                  ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'. \Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })      
                  ->addColumn(
                    'action',
                    function ($invoice) use ($request) {
                        $id = Auth::user()->user_id;
                        $inv_approval = Config::get('common.inv_approval');
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        $user_type  =  DB::table('users')->where(['user_id' => $id])->first();
                        if(in_array($chkUser->id,$inv_approval) && $user_type->user_type==2)
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
                      if($customer!=3 && $chkUser->id!=11)
                      { 
                        
                       $action .= '<div class="d-flex"><select data-amount="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount  : '' ).'"  data-user="'.(($invoice->supplier_id) ? $invoice->supplier_id : '' ).'"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv4"><option value="0">Change Status</option>';
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
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
                       return  (isset($invoice->invoice_disbursed->disbursal->disbursal_batch->batch_id)) ? $invoice->invoice_disbursed->disbursal->disbursal_batch->batch_id : '';
                })
              ->addColumn(
                    'anchor_name',
                    function ($invoice) {  
                        $comp_name = '';
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'customer_detail',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
               })
                 ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'. \Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })     
                   ->addColumn(
                    'action',
                    function ($invoice) use ($request) {
                       $act="";
                     /// $act .='<div class="d-flex inline-action-btn">&nbsp;&nbsp;<a data-toggle="modal"  data-target="#modalInvoiceDisbursed" data-height="430px" data-width="100%" accesskey="" data-url ="'.route("invoice_success_status",["invoice_id" => $invoice->invoice_id,'app_id' => $invoice->app_id]).'"> <button class="btn-upload btn-sm" type="button" title="View Disbursement"> <i class="fa fa-eye"></i></button></a></div>';
                      if(($invoice->invoice_disbursed)) { 
                      $act .='</br><a data-toggle="modal"  data-height="550px" 
                            data-width="100%" 
                            data-target="#viewInterestAccrual"
                            data-url="' . route('view_interest_accrual', ['invoice_disbursed_id' =>$invoice->invoice_disbursed->invoice_disbursed_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="View Interest Accrual"><i class="fa fa-eye"></i></a>';
                      }
                            return $act;
                })
                  ->filter(function ($query) use ($request) {
                  
                   if ($request->get('biz_id') != '') {                        
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->invoice_amount ? '<span><b>Inv. Amt.:&nbsp;</b>'.number_format($invoice->invoice_amount).'</span></br>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<span><b>Inv. Appr. Amt.</b>:&nbsp;'.number_format($invoice->invoice_approve_amount).'</span>' : '';
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'. \Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })  
                 ->filter(function ($query) use ($request) {
                  
                    if ($request->get('biz_id') != '') {                        
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        $inv_amount .= $invoice->limit_exceed ? '<br><span class="error">Limit Exceed</span>' : '';
                        return $inv_amount;
                       
                })
                ->addColumn(            
                    'remark',
                    function ($invoice) {                        
                    
                 return $invoice->remark;
                      
                       
                })
                ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.strip_tags($invoice->Invoiceuser->f_name).'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'.\Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })
                 ->addColumn(
                    'action',
                    function ($invoice) use ($request) {
                        $id = Auth::user()->user_id;
                        $inv_approval = Config::get('common.inv_approval');
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                         $user_type  =  DB::table('users')->where(['user_id' => $id])->first();
                        if(in_array($chkUser->id,$inv_approval) && $user_type->user_type==2)
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
                      if($customer!=3 && $chkUser->id!=11)
                      {  
                       $action .= '<div class="d-flex"><select data-amount="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount  : '' ).'"  data-user="'.(($invoice->supplier_id) ? $invoice->supplier_id : '' ).'"  data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv6"><option value="0">Change Status</option>';
                       if(in_array($customer, $expl)) 
                       {
                        $action .='<option value="8">Approve</option>';
                       }
                      }  
                     
                        return $action;
                })
                 ->filter(function ($query) use ($request) {
                  
                    if ($request->get('biz_id') != '') {                        
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
                        $comp_name .= $invoice->anchor->comp_name ? '<span><b>Anchor Business Name:&nbsp;</b>'.$invoice->anchor->comp_name.'</span>' : '';
                        $comp_name .= $invoice->program->prgm_name ? '<br><span><b>Program:&nbsp;</b>'.$invoice->program->prgm_name.'</span>' : '';
                        return $comp_name;
                })
                ->addColumn(
                    'supplier_name',
                    function ($invoice) { 
                        $custo_name = '';
                        $custo_name .= "<a id=\"" . $invoice->lms_user->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $invoice->lms_user->user_id,'app_id' => $invoice->lms_user->app_id])."\" rel=\"tooltip\"   >".$invoice->lms_user->customer_id."</a></br>";
                        $custo_name .= $invoice->supplier->f_name ? '<span><b>Name:&nbsp;</b>'.$invoice->supplier->f_name.'</span>' : '';
                        $custo_name .= $invoice->business->biz_entity_name ? '<br><b>Business Name :</b>'.$invoice->business->biz_entity_name.'</span></br>' : '';
                        $custo_name .= $invoice->is_adhoc ? '<span style="color:green;">Adhoc Limit</span></br>' : '';
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
                        $inv_amount .= $invoice->program_offer ? '<br><span><b>Margin.:&nbsp;</b>'.$invoice->program_offer->margin.' %</span>' : '';
                        return $inv_amount;
                })
                   ->addColumn(            
                    'updated_at',
                    function ($invoice) {                        
                        $inv_amount = '';
                        $inv_amount .= $invoice->Invoiceuser ? '<span><b>Name:&nbsp;</b>'.$invoice->Invoiceuser->f_name.'&nbsp;'.$invoice->Invoiceuser->l_name.'</span>' : '';
                        $inv_amount .= $invoice->invoice_approve_amount ? '<br><span><b>Date & Time:&nbsp;</b>'. \Helpers::convertDateTimeFormat($invoice->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A').'</span>' : '';
                        return $inv_amount;
                })     
                 ->addColumn(
                    'action',
                    function ($invoice) {
                        $id = Auth::user()->user_id;
                        $inv_approval = Config::get('common.inv_approval');
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        
                         $user_type  =  DB::table('users')->where(['user_id' => $id])->first();
                        if(in_array($chkUser->id,$inv_approval) && $user_type->user_type==2)
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
                     if($invoice->userDetail->is_active==1)
                     {
                       if( $chkUser->id!=11)
                      { 
                       $action .= '<div class="d-flex"><select data-amount="'.(($invoice->invoice_approve_amount) ? $invoice->invoice_approve_amount  : '' ).'"   data-user="'.(($invoice->supplier_id) ? $invoice->supplier_id : '' ).'" data-id="'.(($invoice->invoice_id) ? $invoice->invoice_id : '' ).'" class=" btn-success rounded approveInv5"><option value="0">Change Status</option>';
                       $action .= '<option value="7">Pending</option>';
                    //    if(in_array($customer, $expl)) 
                    //    {
                    //     $action .='<option value="8">Approve</option>';
                    //    }
                        $action .='</select></div>';
                      }
                     }
                     
                        return $action;

                })
                 ->filter(function ($query) use ($request) {
                  
                    if ($request->get('biz_id') != '') {                        
                       $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('biz_id'));
                    $query->where('invoice_no', 'like',"%$search_keyword%")
                    ->orwhereHas('business', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     })
                     ->orwhereHas('anchor', function ($q) use ($search_keyword){
                        $q->where('comp_name', 'like', "%$search_keyword%");
                     });
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
         return DataTables::of($trans)
               ->rawColumns(['trans_by', 'customer_name', 'customer_id','customer_detail','created_by', 'action'])
                ->addIndexColumn()
                
                ->addColumn(
                    'customer_id',
                    function ($trans) { 
                        $link = $trans->lmsUser->customer_id;
                        return "<a id=\"" . $trans->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $trans->user_id])."\" rel=\"tooltip\"   >$link</a> "; 
                })
                ->addColumn(
                    'customer_name',
                    function ($trans) { 
                        $full_name = $trans->user->f_name.' '.$trans->user->l_name;
                        $email = $trans->user->email;

                        $data = '';
                        $data .= $full_name ? '<span><b>Name:&nbsp;</b>'.$full_name.'</span>' : '';
                        $data .= $email ? '<br><span><b>Email:&nbsp;</b>'.$email.'</span>' : '';

                        return $data;
                })
                ->addColumn(
                    'customer_detail',
                    function ($trans) { 
                        $payment = '';
                        $payment .= $trans->created_at ? '<span><b>Trans. Date:&nbsp;</b>'.Carbon::parse($trans->date_of_payment)->format('d-m-Y').'</span>' : '';
                        $payment .= $trans->amount ? '<br><span><b>Trans. Amount:&nbsp;</b> ₹ '.number_format($trans->amount,2).'</span>' : '';
                        return $payment;
                })
               
                 ->addColumn(
                    'trans_type',
                    function ($trans) {
                        return $trans->paymentname;
                })
                 ->addColumn(
                    'comment',
                    function ($trans) {                        
                        return $trans->description ? $trans->description : '';
                })  
                ->addColumn(
                    'created_by',
                    function ($trans) {
                        $created_by = '';
                        $created_by .= $trans->creator ? '<span><b>Name:&nbsp;</b>'.$trans->creator->f_name.'&nbsp;'.$trans->creator->l_name.'</span>' : '';
                        $created_by .= '<br><span><b>Date & Time:&nbsp;</b>'.\Helpers::convertDateTimeFormat($trans->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i A').'</span>';

                        return $created_by;                        
                })
                ->addColumn(
                    'action',
                    function ($trans) {
                        $act = '';
                        if ($trans->action_type == 3) {
                            $act .= "<a data-toggle=\"modal\" data-target=\"#editPaymentFrm\" data-url =\"" . route('edit_payment', ['payment_id' => $trans->payment_id]) . "\" data-height=\"400px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Payment\"><i class=\"fa fa-edit\"></i></a>";
                        }

                        if($trans->is_settled == '0' && $trans->action_type == '1' && $trans->trans_type == '17' && strtotime(\Helpers::convertDateTimeFormat($trans->sys_created_at, 'Y-m-d H:i:s', 'Y-m-d')) == strtotime(\Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'Y-m-d')) ){
                            $act .= '<button  onclick="delete_payment(\''. route('delete_payment', ['payment_id' => $trans->payment_id, '_token'=> csrf_token()] ) .'\',this)" ><i class="fa fa-trash"></i></button>';
                        }

                        if ($trans->action_type == 1 && isset($trans->userFile->file_path)) {                            
                            //$act .= '<a title="Download Cheque" href="'. \Storage::url($trans->userFile->file_path) .'" download="'. $trans->userFile->file_name . '"><i class="fa fa-download"></i></a>';
                            $act .= '<a title="Download" href="'. route('download_storage_file', ['file_id' => $trans->userFile->file_id ]) .'" class="btn btn-action-btn btn-sm" ><i class="fa fa-download"></i></a>';
                        }
                        if (Helpers::checkPermission('edit_payment') && ($trans->action_type == 3 || ($trans->action_type == 1 && $trans->payment_type == 2))) {
                            $act .= "&nbsp;&nbsp;<a  data-toggle=\"modal\" data-target=\"#editPaymentFrm\" data-url =\"" . route('edit_payment', ['payment_id' => $trans->payment_id, 'payment_type' => $trans->payment_type]) . "\" data-height=\"400px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Payment\"><i class=\"fa fa-edit\"></i></a>";
                        }                        
                    return $act;
                   
                })
                ->filter(function ($query) use ($request) {
                    if ($request->get('type') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('type'));
                            $query->where('s',$search_keyword);
                           
                        });                        
                    }
                    if ($request->get('date') != '') {                        
                        $query->where(function ($query) use ($request) {
                             $search_keyword = Carbon::createFromFormat('d/m/Y', $request->get('date'))->format('Y-m-d');
                            $query->where('created_at', 'like',"%$search_keyword%");
                        });                        
                    }
                    if ($request->get('search_keyword') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('search_keyword'));
                            $query->whereHas('lmsUser', function ($query) use($search_keyword) {
                                $query->where('customer_id', 'like',"%$search_keyword%");

                            }); 
                        });                        
                    }
               })
              ->make(true);
     }
     
     /* Get Invoice list for backend
     */
    public function getBackendInvoiceActivityList(Request $request,$invoice)
    { 
       
      return DataTables::of($invoice)
               ->rawColumns(['anchor_id','action','status','comment','update'])
                ->addIndexColumn()
               ->addColumn(
                    'amount',
                    function ($invoice) {
                    //   dd($invoice->invoice->invoice_amount);
                       return ($invoice->invoice_id) ? number_format($invoice->invoice->invoice_amount) : 'N/A'; 
             })
                ->addColumn(
                    'comment',
                    function ($invoice) { 
                     return ($invoice->comm_txt) ? $invoice->comm_txt : 'N/A'; 
                })
               ->addColumn(
                    'status',
                    function ($invoice) {
                           $color  = ['0' =>'','7'=>"badge badge-warning",'8' => "badge badge-success",'9' =>"badge badge-success",'10' =>"badge badge-success",'11' => "badge badge-danger",'12' => "badge badge-danger",'13' =>"badge badge-success",'14' => "badge badge-danger",'28' =>"badge badge-danger"];
                                 
                           if($invoice->invoice_amt=='')
                           {
                              return '<button type="button" class="'.$color[$invoice->status->id].' btn-sm">'.$invoice->status->status_name.'</button>';
                           }
                           
                  })
                  ->addColumn(
                    'update',
                    function ($invoice) {
                          return '&nbsp;'.$invoice->user->f_name.'&nbsp;'.$invoice->user->l_name.'';
                           
                  })
                 ->addColumn(
                    'timestamp',
                    function ($invoice) {
                        return \Helpers::convertDateTimeFormat($invoice->created_at, 'Y-m-d H:i:s','d-m-Y h:i A');
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
                ->rawColumns(['app_id', 'contact','assoc_anchor','action','name'])
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
                // ->addColumn(
                //     'assoc_anchor',
                //     function ($app) {
                //     if($app->anchor_id){
                //        //$userInfo = User::getUserByAnchorId($app->anchor_id);
                //        //$achorName= $userInfo->f_name . ' ' . $userInfo->l_name;
                //         $achorName = Helpers::getAnchorsByUserId($app->user_id);
                //     } else {
                //        $achorName='';  
                //     }                    
                //     return $achorName;
                    
                // })
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
                    //return $app->status == 1 ? 'Completed' : 'Incomplete';
                    $app_status = config('common.app_status');                               
                    return isset($app_status[$app->status]) ? $app_status[$app->status] : ''; 
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
                            $query->where('app_code', 'like',"%$search_keyword%")
                            ->orWhere('biz.biz_entity_name', 'like', "%$search_keyword%")
                            ->orWhere('users.email', 'like',"%$search_keyword%");
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
                    $comp_name = ucwords(strtolower($user->comp_name)); 
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
                        $act .= "<a  href=". route('download_storage_file', ['file_id' => $users->file_id ]) ." class=\"btn btn-action-btn   btn-sm\" type=\"button\" target=\"blank\" title=\"View CAM\"> <i class=\"fa fa-eye\"></i></a>";
                        // 
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
                                $query->where('u.f_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('u.l_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere(\DB::raw("CONCAT(rta_u.f_name,' ',rta_u.l_name)"), 'like', "%$by_nameOrEmail%")
                                ->orWhere('u.email', 'like', "%$by_nameOrEmail%");
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
                ->rawColumns(['id', 'checkbox', 'action', 'assoc_anchor', 'email','assigned', 'status'])
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
                    //$panInfo = $user->pan_no && !empty($user->pan_no) ? '<br><strong>PAN:</strong> ' . $user->pan_no : ''; 
                    $biz_name = ucwords(strtolower($user->biz_name));
                    return $biz_name;

                })
                ->editColumn(
                    'pan_no',
                    function ($user) {
                    $pan_no = ($user->pan_no) ? $user->pan_no : '';
                    return $pan_no;
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
                ->addColumn(
                    'assoc_anchor',
                    function ($user) {
                    if($user->anchor_id){
                       //$userInfo = User::getUserByAnchorId($app->anchor_id);
                       $achorName= ucwords($user->comp_name);
                        // $achorName = Helpers::getAnchorById($user->anchor_id);                        
                    } else {
                       $achorName='';  
                    }                    
                    return $achorName;
                    
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
                                $query->where('anchor_user.name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('anchor_user.l_name', 'like', "%$by_nameOrEmail%")                                  
                                ->orWhere(\DB::raw("CONCAT(rta_anchor_user.name,' ',rta_anchor_user.l_name)"), 'like', "%$by_nameOrEmail%")
                                ->orWhere('anchor_user.email', 'like', "%$by_nameOrEmail%")
                                ->orWhere('anchor_user.pan_no', 'like', "%$by_nameOrEmail%")
                                ->orWhere('anchor.comp_name', 'like', "%$by_nameOrEmail%");
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
                    if ($request->has('pan')) {
                        if ($request->get('pan') != '') {
                            $query->where(function ($query) use ($request) {
                                $pan = $request->get('pan');                                
                                $query->where('anchor_user.pan_no',$pan);
                                //$query->where('anchor_user.pan_no', 'like',"%$pan%");
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
                    return ($role->u_active == 1)?'<div class="btn-group "> <label class="badge badge-success current-status">Active</label>  
                                          </div></b>':'<div class="btn-group "> <label class="badge badge-danger current-status">In Active</label> </div></b>';
                })
                ->editColumn(
                    'created_at',
                    function ($role) {
                    return ($role->created_on)? \Helpers::convertDateTimeFormat($role->created_on, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s') : '---';

                })
                ->editColumn(
                    'updated_by',
                    function ($role) {
                    return ($role->updated)? $role->updated : '---';

                })
                ->addColumn(
                    'action',
                    function ($role) {
                    $user_edit =  "<a title=\"Edit User\"  data-toggle=\"modal\" data-target=\"#manageUserRole\" data-url =\"" . route('edit_user_role', ['role_id' => $role->id,'user_id'=>$role->user_id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-edit\"></i></a>"; 
                    $user_email = "<a title=\"Change User Password\"  data-toggle=\"modal\" data-target=\"#manageUserRolePassword\" data-url =\"" . route('change_user_role_password', ['role_id' => $role->id,'user_id'=>$role->user_id]) . "\" data-height=\"195px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-expeditedssl\"></i></a>";
                    //$assign_doa_level =  "<a title=\"Assign Doa Level Role\"  data-toggle=\"modal\" data-target=\"#assignDoaLevelRole\" data-url =\"" . route('assign_doal_level_role', ['role_id' => $role->id,'user_id'=>$role->user_id]) . "\" data-height=\"430px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\"><i class=\"fa fa-edit\"></i></a>"; 
                    return '<div class="btn-group"><label>'. $user_edit .'</label> <label>'. $user_email .'</label></div>';
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
        $type = ['Company (GST Address)', 'Company (Communication Address)', 'Company ()', 'Company (Warehouse Address)', 'Company (Factory Address)', 'Management Address', 'Additional Address'];
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
                ->rawColumns([ 'action', 'active','status','reason' ,'anchor_limit'])                
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
                /*
                ->editColumn(
                        'reason',
                        function ($program) {
                    $res = '';
                    if ($program->modify_reason_type) {
                        $reasonList = config('common.program_modify_reasons');
                        $link = route('view_end_program_reason', ['program_id'=> $program->prgm_id] );                                
                        $res .= '<small><a href="#" title="View Reason" data-toggle="modal" data-target="#showEndProgramReason" data-url="'. $link . '" data-height="200px" data-width="100%" data-placement="top">' .$reasonList[$program->modify_reason_type]  . '</a></small>';
                        }
                         return  $res;

                    })*/                        
                        ->addColumn(
                    'action',
                    function ($program) {
                        $action = '';
                      if(Helpers::checkPermission('manage_sub_program')){
                          $action .='<a title="View Sub-Program" href="'.route('manage_sub_program',['program_id'=>$program->prgm_id ,'anchor_id'=>$program->anchor_id]).'" class="btn btn-action-btn btn-sm "><i class="fa fa-cog" aria-hidden="true"></i></a>';
                      }
                   
                      /*
                      $editType = \Helpers::isProgamEditAllowed($program->prgm_id);
                      if (Helpers::checkPermission('edit_program') && $editType == 2){  
                          $action .= '<a href="#" title="Modify Program Limit" data-toggle="modal" data-target="#modifyProgramLimit" data-url="' . route('confirm_end_program', ['anchor_id'=> $program->anchor_id, 'program_id'=> $program->prgm_id ,'parent_program_id' => request()->get('program_id'), 'action' => 'edit', 'type' => 'anchor_program']) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a> ';                          
                      } else if (Helpers::checkPermission('edit_program') && $editType == 1) {
                          $action .= '<a title="Edit Program" data-toggle="modal"  data-height="420px" data-width="100%" data-target="#editProgram" data-url="' . route('edit_program', ['program_id'=>$program->prgm_id ,'anchor_id'=>$program->anchor_id]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Program"><i class="fa fa-edit"></i></a>';
                      }
                      */
                      
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
                    'sac_code',
                    function ($charges) {
                    return $charges->sac_code;
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
                    'chrg_tiger_id',
                    function ($charges) {
                    return config('common.chrg_trigger_list')[$charges->chrg_tiger_id] ?? 'N/A'; 
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
                    return $charges->amount;
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
                   return $charges->transaction->trans_date ? date('d-m-Y',strtotime($charges->transaction->trans_date)) : 'N/A';
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
                    return ($charges->created_at) ? date('d-m-Y',strtotime($charges->created_at)) : '---';
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
                ->rawColumns(['user_id', 'action', 'status'])
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
                        // dd($user);
                    return isset($user->agency->comp_name) ? $user->agency->comp_name : '';
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
                    return ($user->is_active == 0)? 
                    '<div class="btn-group ">
                    <label class="badge badge-warning current-status">In Active</label>
                    </div></b>':'<div class="btn-group ">
                    <label class="badge badge-success current-status">Active</label>
                    </div></b>';
                }) 
                ->editColumn(
                    'created_at',
                    function ($user) {
                    return ($user->created_at)? date('d-M-Y',strtotime($user->created_at))   : '';
                })
                ->addColumn(
                    'action',
                    function ($user) {
                       $act = '';
                     //if(Helpers::checkPermission('edit_anchor_reg')){
                        $act = "<a  data-toggle=\"modal\" data-target=\"#editAgencyUserFrame\" data-url =\"" . route('edit_agency_user_reg', ['user_id' => $user->user_id]) . "\" data-height=\"350px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-action-btn btn-sm\" title=\"Edit Agency User Detail\"><i class=\"fa fa-edit\"></i></a>";
                     //}
                     if($user->is_active){
                        return $act.'<a title="In Active" href="'.route('change_agency_user_status', ['user_id' => $user->user_id, 'is_active' => 0]).'"  class="btn btn-action-btn btn-sm user_status "><i class="fa fa-eye" aria-hidden="true"></i></a>';
                    }else{
                        return $act.'<a title="Active" href="'.route('change_agency_user_status', ['user_id' => $user->user_id, 'is_active' => 1]).'"  class="btn btn-action-btn btn-sm  user_status"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>';
                    }
                    }
                )
                ->filter(function ($query) use ($request) {
                    if ($request->get('by_name') != '') {
                        $query->where(function ($query) use ($request) {
                            $search_keyword = trim($request->get('by_name'));
                            $query->where('users.f_name', 'like',"%$search_keyword%")
                            ->orWhere(\DB::raw("CONCAT(rta_users.f_name,' ',rta_users.l_name)"), 'like', "%$search_keyword%")
                             ->orwhereHas('agency', function ($q) use ($search_keyword){
                                $q->where('comp_name', 'like', "%$search_keyword%");
                            })
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
        $this->anchor_utilized_balance = \Helpers::getAnchorUtilizedLimit(request()->get('program_id'));
        return DataTables::of($program)
                        ->rawColumns(['prgm_id','f_name','updated_by','user_id', 'status', 'action' ,'anchor_sub_limit' ,'anchor_limit' ,'loan_size','utilized_limit','reason'])
                        ->editColumn(
                                'prgm_id',
                                function ($program) {                                                      
                            $ret = '<strong>ID:</strong> '. $program->prgm_id . '<br>';
                            $ret .= '<strong>Name:</strong> ' . $program->product_name . '<br>';
                            $ret .= '<strong>Type:</strong> ' . ($program->prgm_type == 1 ? 'Vendor Finance' : 'Channel Finance');
                            if ($program->copied_prgm_id) {
                            $link = route('view_sub_program',['anchor_id'=> $program->anchor_id, 'program_id'=> $program->copied_prgm_id ,'parent_program_id' => request()->get('program_id') ,  'action' => 'view'] );  
                            $ret .= '<br><strong>Parent: </strong><a href="' . $link . '">' . $program->copied_prgm_id . '</a>';
                            }
                            return $ret;
                        })                        
                        ->editColumn(
                                'f_name',
                                function ($program) {
                            $ret = '<strong>Name:</strong> '. $program->f_name . '<br>';
                            $ret .= '<strong>Total Limit:</strong> '. \Helpers::formatCurreny($program->anchor_limit) . '<br>';
                            $ret .= '<strong>Remaining Limit:</strong> '. \Helpers::formatCurreny($program->anchor_limit - $this->anchor_utilized_balance );
                            return $ret;
                        })                        
                        ->editColumn(
                                'anchor_sub_limit',
                                function ($program) {
                            $ret = '<strong>Limit:</strong> '. \Helpers::formatCurreny($program->anchor_sub_limit) . '<br>';
                            $ret .= '<strong>Utilized Limit in Offer:</strong> '. \Helpers::formatCurreny(\Helpers::getPrgmBalLimit($program->prgm_id)) . '<br>';
                            $ret .= '<strong>Loan Size:</strong> '. \Helpers::formatCurreny($program->min_loan_size) .'-' . \Helpers::formatCurreny($program->max_loan_size);
                            return  $ret;
                        })                       
                        ->addColumn(
                                'updated_by',
                                function ($program) {                            
                            $ret = '<strong>By:</strong> '. (isset($program->updatedByUser) ? $program->updatedByUser->f_name . ' ' . $program->updatedByUser->l_name : '') . '<br>';
                            $ret .= '<strong>Date:</strong> '. \Helpers::convertDateTimeFormat($program->updated_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d/m/Y') ;
                             return  $ret;
                           
                        })
                        ->addColumn(
                                'reason',
                                function ($program) {
                            $res = '';
                            if ($program->copied_prgm_id) {
                                $reasonList = config('common.program_modify_reasons');
                                $link = route('view_end_program_reason', ['program_id'=> $program->prgm_id] );                                
                                $res .= '<small><a href="#" title="View Reason" data-toggle="modal" data-target="#showEndProgramReason" data-url="'. $link . '" data-height="200px" data-width="100%" data-placement="top">' .$reasonList[$program->modify_reason_type]  . '</a></small>';
                            }
                             return  $res;
                           
                        })                        
                        ->editColumn(
                                'status',
                                function ($program) {
                            if ($program->status == '0') {
                                $res = '<div class="btn-group ">
                            
                                             <label class="badge badge-warning current-status">In Active</label>
                                             
                                          </div></b>';
                            } else if ($program->status == '1') {
                                $res = '<div class="btn-group ">
                            
                                             <label class="badge badge-success current-status">Active</label>
                                             
                                          </div></b>';                                
                            } else if ($program->status == '2') {
                                $res = '<div class="btn-group ">
                                             <label class="badge badge-secondary current-status">End</label>
                                             
                                          </div></b>';
                            } else if ($program->status == '3') {
                                $res = '<div class="btn-group ">
                                             <label class="badge badge-danger current-status">Reject</label>
                                             
                                          </div></b>';
                            }
                            return $res;
                        })
                        ->addColumn(
                                'action',
                                function ($program) {
                            $act = '';
                            //if (Helpers::checkPermission('view_sub_program')){
                                $act = "<a  href='". route('view_sub_program',['anchor_id'=> $program->anchor_id, 'program_id'=> $program->prgm_id ,'parent_program_id' => request()->get('program_id') ,  'action' => 'view'] )."' class=\"btn btn-action-btn btn-sm\" title=\"View Sub-Program\"><i class=\"fa fa-eye\" aria-hidden=\"true\"></i></a>";
                            //}
                            if (!in_array($program->status, [2,3]) && !Helpers::checkApprPrgm($program->prgm_id, $isOfferAcceptedOrRejected=false)) { 
                            if ($program->is_edit_allow == 1) {    
                                $act .= '<a href="#" title="Modify Program Limit" data-toggle="modal" data-target="#modifyProgramLimit" data-url="' . route('confirm_end_program', ['anchor_id'=> $program->anchor_id, 'program_id'=> $program->prgm_id ,'parent_program_id' => request()->get('program_id'), 'action' => 'edit']) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a> ';
                            } else {                                
                                $act .= "<a  href='". route('add_sub_program',['anchor_id'=> $program->anchor_id, 'program_id'=> $program->prgm_id ,'parent_program_id' => request()->get('program_id') ,  'action' => 'edit', 'reason_type'=> $program->modify_reason_type] )."' class=\"btn btn-action-btn btn-sm\" title=\"Edit Sub-Program\"><i class=\"fa fa-edit\" aria-hidden=\"true\"></i></a>";
                            }
                            }
                            
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
                        return "<a id=\"" . $customer->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $customer->user_id,'app_id' => $customer->app_id])."\" rel=\"tooltip\"   >$link</a> ";
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
                        /*
                        if(isset($customer->user->app->prgmLimits)) {
                            foreach ($customer->user->app->prgmLimits as $value) {
                                $this->totalLimit += $value->limit_amt;
                            }
                        }
                        */
                    
                        $appPrgmLimit = AppProgramLimit::getProductLimit($customer->app_id, 1);
                        foreach ($appPrgmLimit as $value) {
                            $this->totalLimit += $value->product_limit;
                        }
                    return '<label><i class="fa fa-inr">'.number_format($this->totalLimit).'</i></label>';
                })
                ->editColumn(
                    'consume_limit',
                    function ($customer) {                        
                        $this->totalCunsumeLimit = 0;
                        /*
                        if(isset($customer->user->app->acceptedOffers)) {
                            foreach ($customer->user->app->acceptedOffers as $value) {
                                $this->totalCunsumeLimit += $value->prgm_limit_amt;
                            }
                        }
                         * 
                         */
                        $appPrgmLimit = AppProgramLimit::getUtilizeLimit($customer->app_id, 1);                        
                        foreach ($appPrgmLimit as $value) {
                            $this->totalCunsumeLimit += $value->utilize_limit;
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
                        //$anchor = ($customer->user->anchor->comp_name) ?: '--';
                        $anchor = Helpers::getAnchorsByUserId($customer->user_id);
                        $prgm =  ($customer->user->is_buyer == 1) ? 'Vender Finance' : 'Channel Finance';
                        $data = '';
                        //$data .= $anchor ? '<span><b>Anchor:&nbsp;</b>'.$anchor.'</span>' : '';
                        //$data .= $prgm ? '<br><span><b>Program:&nbsp;</b>'.$prgm.'</span>' : '';
                        $data .= $anchor;
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
                                if( $invoice['program']['interest_borne_by'] == 2 && ($invoice['program_offer']['payment_frequency'] == 1 || empty($invoice['program_offer']['payment_frequency'])) ) {
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
                    'product_type',
                    function ($doa) {
                return ($doa->product) ? $doa->product->product_name : '';
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
                        $query->where('doa_level.level_name', 'like',"%$search_keyword%")
                        ->orWhere('doa_level.level_code', 'like',"%$search_keyword%")
                        ;                                    
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
                        ->filter(function ($query) use ($request) {
                            if ($request->get('search_keyword') != '') {
                                $query->where(function ($query) use ($request) {
                                    $search_keyword = trim($request->get('search_keyword'));
                                    $query->where('f_name', 'like', "%$search_keyword%")
                                    ->orWhere('biz_name', 'like', "%$search_keyword%")
                                    ->orWhere('email', 'like', "%$search_keyword%");
                                });
                            }
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
        // start for default button
        $currCompData = \App\Inv\Repositories\Models\Lms\UserInvoiceRelation::getUserCurrCompany($request->user_id);
        $request->baid = ($currCompData)? $currCompData->biz_addr_id : 0;
        // end for default button


        return DataTables::of($data)
            ->rawColumns(['action', 'is_active', 'rcu_status'])
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

                    /*if ($data->is_active) {
                        $act .= '    <input type="checkbox"  ' . $checked . ' data-rel = "' . \Crypt::encrypt($data->biz_addr_id, $request->get('user_id')) . '"  class="make_default" name="add"><label for="add">Default</label> ';
                    }*/

                    if (Helpers::checkPermission('edit_addr') && $request->baid != $data->biz_addr_id) {
                        $act .= '<a data-toggle="modal"  data-height="450px" 
                            data-width="100%" 
                            data-target="#editAddressFrame"
                            data-url="' . route('edit_addr', ['biz_addr_id' => $data->biz_addr_id, 'user_id' => $request->get('user_id')]) . '"  data-placement="top" class="btn btn-action-btn btn-sm" title="Edit Address Detail"><i class="fa fa-edit"></i></a>';
                    }
                    return $act;
                }
            )

            ->editColumn(
                'is_active',
                function ($data) {
                    if ($data->is_default) {
                        $is_default = '<span class="badge badge-info">Default</span>';
                    } else {
                        $is_default = '';
                    }

                    if ($data->is_active) {
                        return '<span class="badge badge-success">Active</span> &nbsp;&nbsp;'.$is_default;
                    } else {
                        return '<span class="badge badge-warning current-status">InActive</span> &nbsp;&nbsp;'.$is_default;
                    }
                }
            )

            ->editColumn(
                'rcu_status',
                function ($data) {
                    if ($data->rcu_status) {
                        return '<span class="badge badge-success">Done</span>';
                    } else {
                        return '<span class="badge badge-warning current-status">Pending</span>';
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
        $this->soa_balance = 0;
        return DataTables::of($data)
        ->rawColumns(['balance','narration'])
            ->addColumn('payment_id', function($trans){
                return $trans->payment_id;
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
                return $trans->invoiceno;
            })
            ->addColumn('batch_no',function($trans){
                return $trans->batchNo;
            })
            ->addColumn('narration',function($trans){
                return "<b>".$trans->narration."<b>";
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
                    return \Helpers::convertDateTimeFormat($trans->sys_created_at ?? $trans->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y');
                }
            )
            ->editColumn(
                'trans_type',
                function ($trans) {
                    return $trans->transname;
                }
            )
            ->editColumn(
                'currency',
                function ($trans) {
                    if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                        return '';
                    }else{
                        return 'INR';
                    }
                }
            )
            ->addColumn(
                'sub_amount',
                function($trans){
                    if($trans->payment_id && !in_array($trans->trans_type,[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.REPAYMENT')])){
                        return number_format($trans->amount,2);
                    }
                }   
            )->editColumn(
                'debit',
                function ($trans) {
                    /*if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                        return '';
                    }
                    else*/
                    if($trans->entry_type=='0' && $trans->amount > '0'){
                        $this->soa_balance += $trans->amount;
                        return number_format($trans->amount,2);
                    }else{
                        return '';
                    }
                }
            )
            ->editColumn(
                'credit',
                function ($trans) {
                    /*if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                        return '';
                    }
                    else*/
                    if($trans->entry_type=='1' && $trans->amount > '0'){
                        $this->soa_balance -= $trans->amount;
                        return '('.number_format($trans->amount,2).')';
                    }else{
                        return '';
                    }
                }
            )
            ->addColumn(
                'backgroundColor',
                function($trans){
                    return $trans->soabackgroundcolor;
                }
            )
            ->editColumn(
                'balance',
                function ($trans) {

                    $data = '';
                    /*if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                        $data = '';
                    }
                    else*/
                    if($this->soa_balance<0){
                        $data = '<span style="color:red">'.number_format(abs($this->soa_balance), 2).'</span>';
                    }else{
                        $data = '<span style="color:green">'.number_format(abs($this->soa_balance), 2).'</span>';
                    }
                    return $data;
                }
            )
            ->filter(function ($query) use ($request) {

                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $query->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('sys_created_at', [$from_date, $to_date]);
                    });
                }
                if($request->has('trans_entry_type')){
                    if($request->trans_entry_type != ''){
                        $trans_entry_type = explode('_',$request->trans_entry_type);
                        $trans_type = $trans_entry_type[0];
                        $entry_type = $trans_entry_type[1];
                        if($trans_type){
                            $query->where('trans_type',$trans_type);
                        }
                        if($entry_type != ''){
                            $query->where('entry_type',$entry_type);
                        }
                    }
                }

                $query->whereHas('lmsUser',function ($query) use ($request) {
                    $customer_id = trim($request->get('customer_id')) ?? null ;
                    $query->where('customer_id', '=', "$customer_id");
                });
                
              
            })
            ->make(true);
    }

    /**
     * get Colender soa list
     * 
     * @param object $request
     * @param object $data
     * @return mixed
     */
    public function getColenderSoaList(Request $request, $data, $colenderCurrShare) {
        $this->colender_balance = 0;
        return DataTables::of($data)
        ->rawColumns(['balance','narration'])
        ->addColumn('payment_id', function($trans){
            $co_lender_percent = $trans->co_lender_percent ?? 0;
            $this->colender_share = round($co_lender_percent/100,2);
            $this->colender_debit = 0;
            $this->colender_credit = 0;
            return $trans->payment_id;
        })
        ->addColumn('customer_id', function($trans){
            $data = $trans->lmsUser->customer_id ?? '';
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
            return $trans->invoiceno;
        })
        ->addColumn('batch_no',function($trans){
            return $trans->batchNo;
        })
        ->addColumn('narration',function($trans){
            return "<b>".$trans->narration."<b>";
        })
        ->addColumn('virtual_acc_id', function ($trans) {
            return $trans->virtual_acc_id;
        })
        ->addColumn('value_date', function ($trans) {
            return date('d-m-Y',strtotime($trans->trans_date));
        })
        ->editColumn('trans_date', function ($trans) {
            return \Helpers::convertDateTimeFormat($trans->sys_created_at ?? $trans->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y');
        })
        ->editColumn('trans_type', function ($trans) {
            return $trans->transname;
        })
        ->editColumn('currency', function ($trans) {
            if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                return '';
            }else{
                return 'INR';
            }
        })
        ->addColumn('sub_amount', function($trans) {
            if($trans->payment_id && !in_array($trans->trans_type,[config('lms.TRANS_TYPE.REFUND'),config('lms.TRANS_TYPE.REPAYMENT')])){
                $this->sub_amount = ($trans->amount * $this->colender_share);
                return number_format($this->sub_amount,2);
            }
        })
        ->editColumn('debit', function ($trans) {
            if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                return '';
            }elseif($trans->entry_type=='0'){
                $this->colender_debit = ($trans->amount*$this->colender_share);
                return number_format($this->colender_debit, 2);
            }else{
                return '(0.00)';
            }
        })
        ->editColumn('credit',  function ($trans) {
            if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                return '';
            }elseif($trans->entry_type=='1'){
                $this->colender_credit = ($trans->amount*$this->colender_share);
                return '('.number_format($this->colender_credit, 2).')';
            }else{
                return '(0.00)';
            }
        })
        ->addColumn(
            'backgroundColor',
            function($trans){
                return $trans->soabackgroundcolor;
            }
        )
        ->editColumn('balance', function ($trans) {
            $data = '';
            $this->colender_balance = ($this->colender_balance + $this->colender_debit - $this->colender_credit);
            if($trans->payment_id && in_array($trans->trans_type,[config('lms.TRANS_TYPE.REPAYMENT'),config('lms.TRANS_TYPE.FAILED')])){
                $data = '';
            }
            elseif($this->colender_balance<0){
                $data = '<span style="color:red">'. number_format($this->colender_balance, 2) .'</span>';
            }else{
                $data = '<span style="color:green">'. number_format($this->colender_balance, 2) .'</span>';
            }
            return $data;
        })
        ->filter(function ($query) use ($request) {
            if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                $query->where(function ($query) use ($request) {
                    $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d 00:00:00');
                    $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d 23:59:59');
                    $query->WhereBetween('sys_created_at', [$from_date, $to_date]);
                });
            }
            if($request->has('trans_entry_type')){
                if($request->trans_entry_type != ''){
                    $trans_entry_type = explode('_',$request->trans_entry_type);
                    $trans_type = $trans_entry_type[0];
                    $entry_type = $trans_entry_type[1];
                    if($trans_type){
                        $query->where('trans_type',$trans_type);
                    }
                    if($entry_type != ''){
                        $query->where('entry_type',$entry_type);
                    }
                }
            }
            if($request->get('user_id')!= ''){
                $query->where(function ($query) use ($request) {
                    $user_id = trim($request->get('user_id'));
                    $query->where('transactions.user_id', '=', "$user_id");
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
                ->rawColumns(['app_id','assoc_anchor', 'action', 'status'])
                ->addColumn('app_id', function ($app) {
                        $link = route('colender_view_offer', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . \Helpers::formatIdWithPrefix($app->app_id, 'APP')  . "</a> ";
                })
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
                        //$userInfo=User::getUserByAnchorId((int)$app->anchor_id);
                        //$achorName= ($userInfo)? ucwords($userInfo->f_name.' '.$userInfo->l_name): 'NA';                        
                        $achorName = Helpers::getAnchorsByUserId($app->user_id);
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
                    $soaBtn = '<a href="'. route('view_colander_soa', ['biz_id' => $app->biz_id, 'app_id' => $app->app_id, 'user_id' => $app->user_id]) .'" class="badge badge-success">View SOA</a>';                    
                    return '<label class="badge '.(($status == 0)? "badge-primary":(($status == 1)? "badge-success": "badge-warning")).'">'.(($status == 0)? "Pending":(($status == 1)? "Accepted": "Rejected")).'</label> &nbsp; ' . $soaBtn;

                })
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

    /*
     * 
     * get all lms customer list
     */
    public function lmsColenderCustomers(Request $request, $customer) {
        return DataTables::of($customer)
                ->rawColumns(['customer_id','app_id', 'customer_name', 'status','limit', 'consume_limit', 'available_limit','anchor','action'])

                ->editColumn('app_id', function ($customer) {
                    $link = route('colender_view_offer', ['biz_id' => $customer->getBusinessId->biz_id ?? '', 'app_id' => $customer->app_id]);
                        return "<a id=\"app-id-" . $customer->app_id . "\" href=\"" . $link . "\" title=\"View Offer\" rel=\"tooltip\">" . \Helpers::formatIdWithPrefix($customer->app_id, 'APP')  . "</a> ";
                }) 
                ->addColumn('customer_id', function ($customer) {
                        $link = $customer->customer_id;
                        return "<a id=\"" . $customer->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $customer->user_id,'app_id' => $customer->app_id])."\" rel=\"tooltip\"   >$link</a> ";

                })
                ->addColumn('virtual_acc_id', function ($customer) {
                        return $customer->virtual_acc_id;
                })     
                ->editColumn('customer_name', function ($customer) {
                        $full_name = $customer->user->f_name.' '.$customer->user->l_name;
                        $email = $customer->user->email;
                        $data = '';
                        $data .= $full_name ? '<span><b>Name:&nbsp;</b>'.$full_name.'</span>' : '';
                        $data .= $email ? '<br><span><b>Email:&nbsp;</b>'.$email.'</span>' : '';
                        return $data;
                })
                ->editColumn('limit', function ($customer) {                        
                        $this->totalLimit = 0;
                        $appPrgmLimit = AppProgramLimit::getProductLimit($customer->app_id, 1);
                        foreach ($appPrgmLimit as $value) {
                            $this->totalLimit += $value->product_limit;
                        }
                        return '<label><i class="fa fa-inr">'.number_format($this->totalLimit).'</i></label>';
                })
                ->editColumn('consume_limit',  function ($customer) {                        
                        $this->totalCunsumeLimit = 0;
                        $appPrgmLimit = AppProgramLimit::getUtilizeLimit($customer->app_id, 1);                        
                        foreach ($appPrgmLimit as $value) {
                            $this->totalCunsumeLimit += $value->utilize_limit;
                        }                      
                        return '<label><i class="fa fa-inr">'.number_format($this->totalCunsumeLimit).'</i></label>';
                })
                ->editColumn('available_limit', function ($customer) {
                    return '<label><i class="fa fa-inr">'.number_format($this->totalLimit - $this->totalCunsumeLimit).'</i></label>';
                })
                ->editColumn('anchor', function ($customer) {
                        $anchor = ($customer->user->anchor->comp_name) ?: '--';
                        $prgm =  ($customer->user->is_buyer == 1) ? 'Vender Finance' : 'Channel Finance';
                        $data = '';
                        $data .= $anchor ? '<span><b>Anchor:&nbsp;</b>'.$anchor.'</span>' : '';
                        $data .= $prgm ? '<br><span><b>Program:&nbsp;</b>'.$prgm.'</span>' : '';
                        return $data;
                })
                ->editColumn('status', function ($customer) {
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
                                ->orWhere(\DB::raw("CONCAT(f_name,' ',l_name)"), 'like', "%$search_keyword%")
                                ->orWhere('email', 'like', "%$search_keyword%")
                                ->orWhere('customer_id', 'like', "%$search_keyword%");
                            });

                        }
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
                                'start_date', function ($baserates) {
                            return ($baserates->start_date) ? date('d-M-Y', strtotime($baserates->start_date)) : '---';
                        })
                        ->addColumn(
                                'end_date', function ($baserates) {
                            return ($baserates->end_date) ? date('d-M-Y', strtotime($baserates->end_date)) : '---';
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
                        return $dataRecords->entry_type;
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
                        return sprintf('%04d', $dataRecords->voucher_no);
                    })      
                    ->editColumn(
                        'mode_of_pay',
                        function ($dataRecords) {
                        return $dataRecords->mode_of_pay;
                    })        
                    ->editColumn(
                        'trans_type',
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

        public function getTallyBatchData(Request $request, $dataRecords){
            return DataTables::of($dataRecords)
                    ->editColumn(
                        'created_at',
                        function ($dataRecords) {
                        return $dataRecords->created_at;
                    })
                    ->editColumn(
                        'batch_no',
                        function ($dataRecords) {
                        return $dataRecords->batch_no;
                    })
                    ->editColumn(
                        'records_in_batch',
                        function ($dataRecords) {
                        return $dataRecords->record_cnt;
                    }) 
                    ->editColumn(
                        'action',
                        function ($dataRecords) {
                        $btn = '<a class="btn btn-success btn-sm" href="'.route('export_txns', ['batch_no' => $dataRecords->batch_no]).'">Download Report</a>';
                        return $btn;
                    }) 
                    ->make(true);
        }

        public function getToSettlePayments(Request $request, $dataRecords){
            return DataTables::of($dataRecords)
                    ->rawColumns(['customer_id','updated_by','action'])
                    ->addColumn(
                    'customer_id',
                        function ($dataRecords) {
                            $link = \Helpers::formatIdWithPrefix($dataRecords->user_id, 'CUSTID');
                            return "<a id=\"" . $dataRecords->user_id . "\" href=\"".route('lms_get_customer_applications', ['user_id' => $dataRecords->user_id])."\" rel=\"tooltip\" >$link</a> ";
                    })
                    ->editColumn(
                        'user_name',
                        function ($dataRecords) {
                            $full_name = $dataRecords->getUserName->f_name .' '.$dataRecords->getUserName->m_name . ' '. $dataRecords->getUserName->l_name;
                        return $full_name;
                    })
                    ->editColumn(
                        'business_name',
                        function ($dataRecords) {
                        if($dataRecords->biz_id)
                        return $dataRecords->getBusinessName->biz_entity_name;
                    })
                    ->editColumn(
                        'virtual_account',
                        function ($dataRecords) {
                        return $dataRecords->virtual_acc;
                    })
                    ->editColumn(
                        'amount',
                        function ($dataRecords) {
                        return "₹ ".number_format($dataRecords->amount,2);
                    }) 
                    ->editColumn(
                        'trans_type',
                        function ($dataRecords) {   
                            return $dataRecords->paymentname;
                    }) 
                    ->addColumn(
                        'date_of_payment', 
                        function ($dataRecords) {
                        return Carbon::parse($dataRecords->date_of_payment)->format('d-m-Y');
                    })
                    ->editColumn(
                        'updated_by',
                        function ($dataRecords) {
                        $createdByName = $dataRecords->getCreatedByName->f_name .' '.$dataRecords->getCreatedByName->m_name . ' '. $dataRecords->getCreatedByName->l_name;
                        $dateofPay = \Helpers::convertDateTimeFormat($dataRecords->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i A');
                        $updated_by = "$createdByName<br />$dateofPay";
                        return $updated_by;
                    }) 
                    ->editColumn(
                        'action',
                        function ($dataRecords) {
                            $btn = '';

                            if($dataRecords->is_settled == '0' && $dataRecords->action_type == '1' && $dataRecords->trans_type == '17' && strtotime(\Helpers::convertDateTimeFormat($dataRecords->sys_created_at, 'Y-m-d H:i:s', 'Y-m-d')) == strtotime(\Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'Y-m-d')) ){
                                $btn .= '<button class="btn btn-action-btn btn-sm"  title="Delete Payment" onclick="delete_payment(\''. route('delete_payment', ['payment_id' => $dataRecords->payment_id, '_token'=> csrf_token()] ) .'\',this)" ><i class="fa fa-trash"></i></button>';
                            }

                            if($dataRecords->is_settled == '1' && $dataRecords->action_type == '1' && $dataRecords->trans_type == '17' && strtotime(\Helpers::convertDateTimeFormat($dataRecords->sys_created_at, 'Y-m-d H:i:s', 'Y-m-d')) == strtotime(\Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'Y-m-d')) ){
                                $btn .= '<button class="btn btn-action-btn btn-sm"  title="Revert Apportionment" onclick="delete_payment(\''. route('undo_apportionment', ['payment_id' => $dataRecords->payment_id, '_token'=> csrf_token()] ) .'\',this)" ><i class="fa fa-undo"></i></button>';
                            }

                            if(Helpers::checkPermission('apport_unsettled_view') && $dataRecords->is_settled == 0){
                                if($dataRecords->isApportPayValid['isValid']){
                                    $btn .= "<a title=\"Unsettled Transactions\"  class='btn btn-action-btn btn-sm' href ='".route('apport_unsettled_view',[ 'user_id' => $dataRecords->user_id , 'payment_id' => $dataRecords->payment_id])."'>Unsettled Transactions</a>"; 
                                }elseif($dataRecords->isApportPayValid['error']){
                                    $btn .= "<span class=\"d-inline-block text-truncate\" style=\"max-width: 150px; color:red; font:9px;\">(". $dataRecords->isApportPayValid['error'] . ")</span>";
                                }
                            }elseif(Helpers::checkPermission('lms_refund_payment_advise') && $dataRecords->is_refundable && !$dataRecords->refundReq){
                                $btn .= '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#paymentRefundInvoice" title="Payment Refund" data-url ="'.route('lms_refund_payment_advise', ['payment_id' => $dataRecords->payment_id]).'" data-height="350px" data-width="100%" data-placement="top"><i class="fa fa-list-alt"></i></a>';
                            } 

                            return $btn;
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
    //refund
    public function getApprovedRefundList(Request $request, $data){
        return DataTables::of($data)
        ->rawColumns(['ref_code','assignee','banck_detail','action'])
        ->editColumn(
            'ref_code',
            function ($data) {
                $result = '<a 
                data-toggle="modal" 
                data-target="#lms_view_process_refund" 
                data-url="'.route('lms_refund_request_view', ['req_id' => $data->refund_req_id, 'view' => 1 ]).'"
                data-height="400px" 
                data-width="100%" 
                data-placement="top" title="Process Refund" class="btn btn-action-btn btn-sm">' . $data->ref_code . '</a>';
                return $result;
            }
        )
        ->addColumn(
            'customer_id',
            function ($data) {
                return $data->payment->lmsUser->customer_id;  //$data->req_type_name;
            }
        )
        ->addColumn(
            'biz_entity_name',
            function ($data) {
                // return \Helpers::getEntityNameByUserId($data->payment->user_id);  //$data->req_type_name;
                return $data->payment->getUserName->biz->biz_entity_name;
            }
        )            
        ->editColumn(
            'amount',
            function ($data) {
                return number_format($data->refund_amount,2);
            }
        )     
        ->editColumn(
            'created_at',
            function ($data) {
                return \Helpers::convertDateTimeFormat($data->created_at, 'Y-m-d H:i:s', 'd-m-Y h:i A');
            }
        )
        ->editColumn(
            'batch_no',
            function ($data){
                if($data->refund_req_batch_id){
                    return $data->batch->batch_no;
                }
            }
        )
        ->editColumn(
            'banck_detail', function ($dataRecords) {
                $account = '';
                $account .= $dataRecords->bank_name ? '<span><b>Bank:&nbsp;</b>'.$dataRecords->bank_name.'</span>' : '';
                $account .= $dataRecords->ifsc_code ? '<br><span><b>IFSC:&nbsp;</b>'.$dataRecords->ifsc_code.'</span>' : '';
                $account .= $dataRecords->acc_no ? '<br><span><b>Acc. #:&nbsp;</b>'.$dataRecords->acc_no.'</span>' : '';
                return $account;
            }
        )
        ->editColumn(
            'updated_at', function ($data) {
                return \Helpers::convertDateTimeFormat($data->updated_at, 'Y-m-d H:i:s', 'd-m-Y h:i A');
            }
        )
        ->editColumn(
            'status',
            function ($data){
                return config('lms.REQUEST_STATUS_DISP.'. $data->status . '.SYSTEM');
            }
        )
        ->editColumn(
            'action',
            function ($data){
                $result = '';
                if ((int)$data->status == (int)config('lms.REQUEST_STATUS.SEND_TO_BANK') ) {
                    $result = '<a  data-toggle="modal" data-target="#invoiceDisbursalTxnUpdate" data-url ="' . route('refund_udpate_disbursal', [
                    'payment_id' => $data->payment_id,  
                    'refund_req_batch_id' => $data->refund_req_batch_id,
                    'refund_req_id'=>$data->refund_req_id
                    ]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Invoices"><i class="fa fa-plus-square"></i></a>';
                }
                return $result;
            }
        )  
        ->filter(function ($query) use ($request) {
            if ($request->get('search_keyword') != '') {
                $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('search_keyword'));
                    $query->where('ref_code', 'like',"%$search_keyword%")
                    ->orwhereHas('payment.biz', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     });
                });
            }
        })
        ->make(true);

    }
    //refund
    public function getRequestList(Request $request, $data){
        return DataTables::of($data)
        ->rawColumns(['id','ref_code','assignee','assignedBy','action'])
        ->editColumn(
            'id',
            function ($data) {
                return '<input class="refund-request" type="checkbox" name="refundRequest[]" value="'.$data->refund_req_id.'">';
            }
        )
        ->editColumn(
            'ref_code',
            function ($data) {
                $result = '<a 
                data-toggle="modal" 
                data-target="#lms_view_process_refund" 
                data-url="'.route('lms_refund_request_view', ['req_id' => $data->refund_req_id, 'view' => 1 ]).'"
                data-height="400px" 
                data-width="100%" 
                data-placement="top" title="Process Refund" class="btn btn-action-btn btn-sm">' . $data->ref_code . '</a>';
                return $result;
            }
        )
        ->addColumn(
            'customer_id',
            function ($data) {
                return $data->payment->lmsUser->customer_id;  //$data->req_type_name;
            }
        )
        ->addColumn(
            'biz_entity_name',
            function ($data) {
                // return \Helpers::getEntityNameByUserId($data->payment->user_id);  //$data->req_type_name;
                return $data->payment->getUserName->biz->biz_entity_name;
            }
        )            
        ->editColumn(
            'type',
            function ($data) {
                return config('lms.REQUEST_TYPE_DISP.1');  //$data->req_type_name;
            }
        )
        ->editColumn(
            'amount',
            function ($data) {
                return number_format($data->refund_amount,2);
            }
        )     
        ->editColumn(
            'created_at',
            function ($data) {
                return \Helpers::convertDateTimeFormat($data->created_at, 'Y-m-d H:i:s', 'd-m-Y h:i A');
            }
        )
        ->editColumn(
            'updated_at',
            function ($data) {
                return \Helpers::convertDateTimeFormat($data->updated_at, 'Y-m-d H:i:s', 'd-m-Y h:i A');
            }
        )
        ->addColumn(
            'assignee',
            function ($data) {
                $assignee = \Helpers::getReqCurrentAssignee($data->refund_req_id);
                return $assignee ? $assignee->assignee .  '<br><small>(' . $assignee->assignee_role . ')</small>' : '';
            }
        )
        ->addColumn(
            'assignedBy',
            function ($data) {
                $from = \Helpers::getReqCurrentAssignee($data->refund_req_id);
                return $from ? $from->assigned_by .  '<br><small>(' . $from->from_role . ')</small>' : '';
            }
        )  
        ->editColumn(
            'status',
            function ($data){
                return config('lms.REQUEST_STATUS_DISP.'. $data->status . '.SYSTEM');
            }
        )
        ->filter(function ($query) use ($request) {
            if ($request->get('search_keyword') != '') {
                $query->where(function ($query) use ($request) {
                    $search_keyword = trim($request->get('search_keyword'));
                    $query->where('ref_code', 'like',"%$search_keyword%")
                    ->orwhereHas('payment.biz', function ($q) use ($search_keyword){
                        $q->where('biz_entity_name', 'like', "%$search_keyword%");
                     });
                });
            }
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
                        $account = '';
                        $account .= $disbursal->bank_name ? '<span><b>Bank:&nbsp;</b>'.$disbursal->bank_name.'</span>' : '';
                        $account .= $disbursal->ifsc_code ? '<br><span><b>IFSC:&nbsp;</b>'.$disbursal->ifsc_code.'</span>' : '';
                        $account .= $disbursal->acc_no ? '<br><span><b>Acc. #:&nbsp;</b>'.$disbursal->acc_no.'</span>' : '';

                        return $account;

                    }
                )
                ->editColumn(
                    'total_actual_funded_amt',
                    function ($disbursal) {

                        return '<i class="fa fa-inr"></i> '.number_format($disbursal->total_disburse_amount, 2);
                })
                ->editColumn(
                    'total_invoice',
                    function ($disbursal) {   
                        return $disbursal->invoice_disbursed->count();
                }) 
                ->editColumn('updated_at', function($disbursal){
                    return \Helpers::convertDateTimeFormat($disbursal->updated_at, 'Y-m-d H:i:s','d-m-Y h:i A');
                })
                ->addColumn(
                    'action',
                    function ($disbursal) {
                        $id = Auth::user()->user_id;
                        $role_id = DB::table('role_user')->where(['user_id' => $id])->pluck('role_id');
                        $chkUser =    DB::table('roles')->whereIn('id',$role_id)->first();
                        $act = '';
                        $act = '<a  data-toggle="modal" data-target="#viewBatchSendToBankInvoice" data-url ="' . route('view_batch_user_invoice', ['user_id' => $disbursal->user_id, 'disbursal_batch_id' => $disbursal->disbursal_batch_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="View Invoices"><i class="fa fa-eye"></i></a>';
                        if( $chkUser->id!=11)
                        {  
                           $act .= '<a  data-toggle="modal" data-target="#invoiceDisbursalTxnUpdate" data-url ="' . route('invoice_udpate_disbursal', ['user_id' => $disbursal->user_id, 'disbursal_batch_id' => $disbursal->disbursal_batch_id]) . '" data-height="350px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm" title="Update Transaction"><i class="fa fa-plus-square"></i></a>';
                        }
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

    // get user invoice list
    public function getUserInvoiceList(Request $request, $data)
    {
        return DataTables::of($data)
            ->rawColumns(['action'])
            ->editColumn(
                'gst_address',
                function ($data) {
                    return $data->gst_addr;
                }
            )
            ->editColumn(
                'invoice_date',
                function ($data) {
                    return date('d/m/Y', strtotime($data->invoice_date));
                }
            )  
            ->editColumn('invoice_type',  function ($data) {
                    return ($data->invoice_type == 'C' ? 'Charge' : 'Interest');
                }
            )   
            ->editColumn(
                'pan_no',
                function ($data) {
                    return $data->pan_no;
                }
            )     
            ->editColumn(
                'biz_gst_no',
                function ($data) {
                    return $data->biz_gst_no;
                }
            )     
            ->editColumn(
                'reference_no',
                function ($data) {
                    // return $data->reference_no;
                    return \Helpers::formatIdWithPrefix($data->user_id, 'CUSTID');
                }
            )      
            ->editColumn(
                'invoice_no',
                function ($data) {
                    return $data->invoice_no;
                }
            )        
            ->editColumn(
                'place_of_supply',
                function ($data) {
                    return $data->place_of_supply;
                }
            )      
            ->editColumn(
                'action',
                function ($data) {
                return  "<a title='Download User Invoice' href='".route('download_user_invoice', ['user_id' => $data->user_id, 'user_invoice_id' => $data->user_invoice_id])."' class='btn btn-success btn-sm'><i style='color:#fff' class='fa fa-download'> Download</a>";
                }
            )
            ->filter(function ($query) use ($request) {
                   if (!empty($request->get('from_date')) && !empty($request->get('to_date'))) {               
                        $query->where(function ($query) use ($request) {
                            $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d H:i:s');
                            $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d H:i:s');
                            $query->whereBetween('invoice_date',  [$from_date, $to_date]);
                        });                        
                    }
                    if(!empty($request->get('invoice_no'))){
                        $query->where(function ($query) use ($request) {
                           $invoice_no = trim($request->get('invoice_no'));
                           $query->where('invoice_no', 'like', "%$invoice_no%");
                        });
                    }
                    
                })
            ->make(true);
    }
        
    /*      
     * Get application list
     */
    public function getRenewalAppList(Request $request, $app)
    {
        return DataTables::of($app)
                ->rawColumns(['app_id','assignee', 'assigned_by','assoc_anchor', 'action','contact','name'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $user_role = Helpers::getUserRole(\Auth::user()->user_id)[0]->pivot->role_id;
                        $app_id = $app->app_id;
                        /*
                        if(Helpers::checkPermission('company_details')){
                           if($user_role == config('common.user_role.APPROVER'))
                                $link = route('cam_report', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);
                           else
                                $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);
                           return "<a id='app-id-$app_id' href='$link' rel='tooltip'>" . \Helpers::formatIdWithPrefix($app_id, 'APP') . "</a>";
                        }else{
                            return "<a id='app-id-$app_id' rel='tooltip'>" . \Helpers::formatIdWithPrefix($app_id, 'APP') . "</a>";
                        }
                        */
                        $link = route('company_details', ['biz_id' => $app->biz_id, 'app_id' => $app_id]);                        
                        return "<a id='app-id-$app_id' href='$link' rel='tooltip'>" . \Helpers::formatIdWithPrefix($app_id, 'APP') . "</a>";
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
                ->addColumn(
                    'assoc_anchor',
                    function ($app) {
                        //return "<a  data-original-title=\"Edit User\" href=\"#\"  data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";
                    /////return isset($app->assoc_anchor) ? $app->assoc_anchor : '';
                    
                    if($app->anchor_id){
                       //$userInfo = User::getUserByAnchorId($app->anchor_id);
                       //$achorName= $userInfo->f_name . ' ' . $userInfo->l_name;
                        $achorName = Helpers::getAnchorsByUserId($app->user_id);
                    } else {
                       $achorName='';  
                    }                    
                    return $achorName;
                    
                })            
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
                        if ($view_only && $app->renewal_status == 1) {
                            
                            /*
                            if (Helpers::checkPermission('add_app_note')){
                                $act = $act . '<a title="Add App Note" href="#" data-toggle="modal" data-target="#addCaseNote" data-url="' . route('add_app_note', ['app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="190px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>';
                            }
                            if(Helpers::checkPermission('send_case_confirmBox')){
                                $currentStage = Helpers::getCurrentWfStage($app->app_id);
                                $roleData = Helpers::getUserRole();     
                                $hasSupplyChainOffer = Helpers::hasSupplyChainOffer($app->app_id);
                                if ($currentStage && $currentStage->order_no <= 16 ) {                                                                                                           
                                    $moveToBackStageUrl = '&nbsp;<a href="#" title="Move to Back Stage" data-toggle="modal" data-target="#assignCaseFrame" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id'), 'assign_case' => 1]) . '" data-height="320px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
                                    if ($currentStage->order_no == 16 && !$hasSupplyChainOffer ) {
                                        if ($app->curr_status_id != config('common.mst_status_id')['DISBURSED']) {
                                            $act = $act . $moveToBackStageUrl;
                                        }
                                    } else {
                                        $act = $act . '&nbsp;<a href="#" title="Move to Next Stage" data-toggle="modal" data-target="#sendNextstage" data-url="' . route('send_case_confirmBox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $request->get('biz_id')]) . '" data-height="370px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';    

                                        if ($roleData[0]->id != 4 && !empty($currentStage->assign_role)) {
                                            $act = $act . $moveToBackStageUrl;
                                        }
                                    }
                                }
                            }
                            */
                            $act = $act . '&nbsp;<a href="#" title="Copy/Renew Application" data-toggle="modal" data-target="#confirmCopyApp" data-url="' . route('copy_app_confirmbox', ['user_id' => $app->user_id,'app_id' => $app->app_id, 'biz_id' => $app->biz_id]) . '" data-height="200px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> ';
                            
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
    * 
    * Get All Running Transactions
    */
    public function getRunningTrans(Request $request, $trans)
    {
        return DataTables::of($trans)
            ->rawColumns(['select'])
            ->addColumn('disb_date', function($trans){
                return Carbon::parse($trans->trans_date)->format('d-m-Y');
            })
            ->addColumn('invoice_no', function($trans){
                if($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id){
                    return $trans->invoiceDisbursed->invoice->invoice_no;
                }
            })
            ->addColumn('trans_type', function($trans){
                return $trans->transName;
            })
            ->addColumn('total_repay_amt', function($trans){
                return "₹ ".number_format($trans->amount,2);
            })
            ->addColumn('outstanding_amt', function($trans){
                return "₹ ".number_format($trans->outstanding,2);
            })
            ->addColumn('select', function($trans){
                $type = $trans->transType->chrg_master_id != 0  ? 'charges' : ($trans->transType->id == config('lms.TRANS_TYPE.INTEREST') ? 'interest' : '');
                $result = "<input class='check' transtype='$type' type='checkbox' value=".$trans->outstanding." name='check[".$trans->trans_running_id."]' >";
                return $result;
            })
        ->make(true);
    }
       

     /*
     * 
     * Get All Unsettled Transactions
     */
    public function getUnsettledTrans(Request $request, $trans,$payment)
    {
        return DataTables::of($trans)
            ->rawColumns(['select', 'pay','outstanding_amt'])
            ->addColumn('disb_date', function($trans){
                return Carbon::parse($trans->parenttransdate)->format('d-m-Y');
            })
            ->addColumn('invoice_no', function($trans){
                if($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id){
                    return $trans->invoiceDisbursed->invoice->invoice_no;
                }
            })
            ->addColumn('trans_type', function($trans){
                return $trans->transName;
            })
            ->addColumn('total_repay_amt', function($trans){
                return "₹ ".number_format($trans->amount,2);
            })
            ->addColumn('outstanding_amt', function($trans)use($payment){
                $outResult = "₹ ".number_format($trans->outstanding,2);
                if($payment && in_array($trans->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
                    $accuredInterest = $trans->tempInterest;
                    if(!is_null($accuredInterest)){
                        $outResult .= " <span style=\"color:red\">(".number_format($accuredInterest,2).")</span>";
                    }
                }
                return $outResult;
            })
            ->addColumn('payment_date', function($trans)use($payment){
                if($payment){
                    return Carbon::parse($payment->date_of_payment)->format('d-m-Y');
                }
            })
            ->addColumn('pay', function($trans)use($payment){
                $result = '';
                if($payment){
                    if($payment && in_array($trans->trans_type,[config('lms.TRANS_TYPE.INTEREST'),config('lms.TRANS_TYPE.INTEREST_OVERDUE')])){
                        $accuredInterest = $trans->tempInterest;
                        if(!is_null($accuredInterest)){
                            return  "<input class='pay' id='".$trans->trans_id."' readonly='true' type='text' max='".round($accuredInterest,2)."' name='payment[".$trans->trans_id."]'>";
                        }
                    }
                    $result = "<input class='pay' id='".$trans->trans_id."' readonly='true' type='text' max='".round($trans->outstanding,2)."' name='payment[".$trans->trans_id."]'>";
                    
                }
                return $result;
            })
            ->addColumn('select', function($trans){
                $type = $trans->transType->chrg_master_id != 0  ? 'charges' : ($trans->transType->id == config('lms.TRANS_TYPE.INTEREST') ? 'interest' : '');
                $result = "<input class='check' transtype='$type' type='checkbox' name='check[".$trans->trans_id."]' onchange='apport.onCheckChange(".$trans->trans_id.")'>";
                return $result;
            })
           
            ->make(true);
    }

    /*
     * 
     * Get All Settled Transactions
     */
    public function getSettledTrans(Request $request, $trans)
    {
        return DataTables::of($trans)
            ->rawColumns(['select', 'pay'])
            ->addColumn('disb_date', function($trans){
                return Carbon::parse($trans->parenttransdate)->format('d-m-Y');
            })
            ->addColumn('invoice_no', function($trans){
                if($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id){
                    return $trans->invoiceDisbursed->invoice->invoice_no;
                }
            })
            ->addColumn('trans_type', function($trans){
                return $trans->transName;
            })
            ->addColumn('total_repay_amt', function($trans){
                return "₹ ".number_format($trans->amount,2);
            })
            ->addColumn('settled_amt', function($trans){
                return "₹ ".number_format($trans->refundoutstanding,2);
            })
            ->addColumn('payment_date', function($trans){
                if($trans->payment){
                    return Carbon::parse($trans->payment->date_of_payment)->format('d-m-Y');
                }
            })
            ->addColumn('select', function($trans){
                $result = '';
                $flag = true;
                if($trans->invoice_disbursed_id ){
                    if($trans->invoiceDisbursed->invoice->program_offer->payment_frequency == 1 && $trans->outstanding == 0)
                    $flag = false;
                }
                
                if($trans->payment && strtotime(\Helpers::convertDateTimeFormat($trans->sys_created_at, 'Y-m-d H:i:s', 'Y-m-d')) == strtotime(\Helpers::convertDateTimeFormat(Helpers::getSysStartDate(), 'Y-m-d H:i:s', 'Y-m-d')) && $flag && !in_array($trans->trans_type,[config('lms.TRANS_TYPE.FAILED')])){
                    $result = "<input type='checkbox' name='check[".$trans->trans_id."]'>";
                }
                return $result;
            })
            ->make(true);
    }

    /*
     * 
     * Get All Refund Transactions
     */
    public function getRefundTrans(Request $request, $trans)
    {
        return DataTables::of($trans)
            ->rawColumns(['select', 'refund'])
            ->addColumn('disb_date', function($trans){
                return Carbon::parse($trans->trans_date)->format('d-m-Y');
            })
            ->addColumn('invoice_no', function($trans){
                if($trans->invoice_disbursed_id && $trans->invoiceDisbursed->invoice_id){
                    return $trans->invoiceDisbursed->invoice->invoice_no;
                }
            })
            ->addColumn('trans_type', function($trans){
                return $trans->transName;
            })
            ->addColumn('total_repay_amt', function($trans){
                return "₹ ".number_format($trans->amount,2);
            })
            ->addColumn('outstanding_amt', function($trans){
                return "₹ ".number_format($trans->refundoutstanding,2);
            })
            ->addColumn('refund', function($trans){
                $result = "<input class='refund' id='".$trans->trans_id."' readonly='true' type='text' max='".round($trans->refundoutstanding,2)."' name='refund[".$trans->trans_id."]' onchange='apport.onRefundChange(".$trans->trans_id.")'>";
                return $result;
            })
            ->addColumn('select', function($trans){
                $type = $trans->transType->chrg_master_id != 0  ? 'charges' : ($trans->transType->id == config('lms.TRANS_TYPE.INTEREST') ? 'interest' : '');
                $result = "<input class='check' transtype='$type' type='checkbox' name='check[".$trans->trans_id."]' onchange='apport.onRefundCheckChange(".$trans->trans_id.")'>";
                return $result;
            })
            ->make(true);
    }

    /**
    * get customer primary and capsave location
    */
   public function getCustAndCapsLoc(Request $request, $data) {
       $this->sr_no = 1;
       return DataTables::of($data)
           ->rawColumns(['is_active'])
           ->editColumn(
               'sr_no',
               function ($user) {
               return $this->sr_no++;
           })  
           ->editColumn(
               'created_at',
               function ($user) {
               return ($user->created_at)? date('d-M-Y',strtotime($user->created_at)) : '---';
           })  
           ->editColumn(
               'comp_addr',
               function ($user) {
               return $user->capsavBizAddr->cmp_add .' '. $user->capsavBizAddr->city;
           })  
           ->editColumn(
               'user_addr',
               function ($user) {
               return $user->userBizAddr->addr_1. ' '. $user->userBizAddr->addr_2. ' '. $user->userBizAddr->city_name;
           })  
           ->editColumn(
               'comp_state',
               function ($user) {
               return $user->getCompanyState->name;
           })  
           ->editColumn(
               'user_state',
               function ($user) {
               return $user->getUserState->name;
           })
           ->addColumn(
               'is_active',
               function ($data) {
                   $id = $data->user_invoice_rel_id;
                   $btn = "<a title='Address Unpublish' href='".route('get_user_invoice_unpublished', ['user_id' => $data->user_id, 'user_invoice_rel_id' => $data->user_invoice_rel_id])."' class='btn btn-action-btn btn-sm'> <i class='fa fa-edit'></i></a>";
                   $status = ($data->is_active == '2')?'<div class="btn-group "> <label class="badge badge-warning current-status">In Active</label> </div></b>':'<div class="btn-group "> <label class="badge badge-success current-status">Active</label>&nbsp;'. $btn.'</div></b>';
                   return $status;
           })
           
           ->make(true);
   }

   public function getAllCustomers(Request $request, $data) {
       $this->sr_no = 1;
       return DataTables::of($data)
           ->rawColumns(['is_active'])
           ->editColumn('sr_no', function ($user) {
               return $this->sr_no++;
           })  
           ->editColumn('customer_name', function ($user) {
               return $user->f_name. ' '. $user->m_name. ' '. $user->l_name;
           })  
           ->editColumn('email', function ($user) {
               return $user->email;
           })  
           ->editColumn('mobile', function ($user) {
               return $user->mobile_no;
           })   
           ->editColumn('biz_name', function ($user) {
               return $user->biz_name;
           })  
           ->editColumn('registered_on',  function ($user) {
               return $user->created_at;
           })  
           ->addColumn('is_active',  function ($user) {
                   $status = ($user->is_active == '2')?'<div class="btn-group "> <label class="badge badge-warning current-status">In Active</label> </div></b>':'<div class="btn-group "> <label class="badge badge-success current-status">Active</label></div></b>';
                   return $status;
           })
           ->filter(function ($query) use ($request) {
                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $query->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('created_at', [$from_date, $to_date]);
                    });
                }
                if($request->get('search_keyword')!= ''){
                    $query->where(function ($query) use ($request) {
                        $search_keyword = trim($request->get('search_keyword'));
                        $query->where('f_name', 'like',"%$search_keyword%");
                    });
                }
              
            })
           
           ->make(true);
   }
   public function leaseRegister(Request $request, $data) {
       $this->sr_no = 1;
       return DataTables::of($data)
           ->editColumn('state', function ($invoiceRec) {
               return $invoiceRec->name;
           })
           ->editColumn('gstn', function ($invoiceRec) {
               $inv_comp_data = json_decode($invoiceRec->inv_comp_data, TRUE);
               return ($inv_comp_data['gst_no'] ?? $invoiceRec->biz_gst_no);
           })    
           ->editColumn('user_id', function ($invoiceRec) {
               return \Helpers::formatIdWithPrefix($invoiceRec->user_id, 'LEADID');
           })      
           ->editColumn('customer_name', function ($invoiceRec) {
               return $invoiceRec->biz_entity_name;
           })  
           ->editColumn('customer_address', function ($invoiceRec) {
               return $invoiceRec->gst_addr;
           })   
           ->editColumn('customer_gstn', function ($invoiceRec) {
               return $invoiceRec->biz_gst_no;
           })  
           ->editColumn('sac_code',  function ($invoiceRec) {
               return ($invoiceRec->sac_code != 0 ? $invoiceRec->sac_code : '000');
           })   
           ->editColumn('contract_no',  function ($invoiceRec) {
               return 'HEL/'.($invoiceRec->sac_code != 0 ? $invoiceRec->sac_code : '000');
           })     
           ->editColumn('invoice_no', function ($invoiceRec) {
               return $invoiceRec->invoice_no;
           })    
           ->editColumn('invoice_date', function ($invoiceRec) {
               return date('d-m-Y', strtotime($invoiceRec->invoice_date));
           })  
           ->editColumn('base_amount',  function ($invoiceRec) {
               return number_format($invoiceRec->base_amount, 2);
           })    
           ->editColumn('sgst_rate',  function ($invoiceRec) {
               return ($invoiceRec->sgst_rate != 0 ? $invoiceRec->sgst_rate . '%' : '-');
           })    
           ->editColumn('sgst_amount',  function ($invoiceRec) {
               return ($invoiceRec->sgst_amount != 0 ? number_format($invoiceRec->sgst_amount, 2) : '-');
           })    
           ->editColumn('cgst_rate',  function ($invoiceRec) {
               return ($invoiceRec->cgst_rate != 0 ? $invoiceRec->cgst_rate . '%' : '-');
           })    
           ->editColumn('cgst_amount',  function ($invoiceRec) {
               return ($invoiceRec->cgst_amount != 0 ? number_format($invoiceRec->cgst_amount, 2) : '-');
           })    
           ->editColumn('igst_rate',  function ($invoiceRec) {
               return ($invoiceRec->igst_rate != 0 ? $invoiceRec->igst_rate . '%' : '-');
           })      
           ->editColumn('igst_amount',  function ($invoiceRec) {
               return ($invoiceRec->igst_amount != 0 ? number_format($invoiceRec->igst_amount, 2) : '-');
           })        
           ->editColumn('total_rate',  function ($invoiceRec) {
                $totalRate = ($invoiceRec->sgst_rate + $invoiceRec->igst_rate + $invoiceRec->cgst_rate);
               return ($totalRate != 0 ? $totalRate . '%' : '-');
           })          
           ->editColumn('total_tax',  function ($invoiceRec) {
                $totalTax = ($invoiceRec->sgst_amount + $invoiceRec->igst_amount + $invoiceRec->cgst_amount);
               return ($totalTax != 0 ? number_format($totalTax, 2) : '-');
           })         
           ->editColumn('total_amt',  function ($invoiceRec) {
               return number_format($invoiceRec->base_amount + $invoiceRec->sgst_amount + $invoiceRec->cgst_amount + $invoiceRec->igst_amount, 2);
           })           
           ->editColumn('cash_flow',  function ($invoiceRec) {
               return (!empty($invoiceRec->invoice_type) && $invoiceRec->invoice_type == 'C') ? 'Charge' : 'Interest';
           })           
           ->editColumn('considered_in',  function ($invoiceRec) {
               return date('M-Y', strtotime($invoiceRec->invoice_date));
           })  
           ->filter(function ($query) use ($request) {
                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $query->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d 00:00:00');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d 23:59:59');
                        $query->WhereBetween('invoice_date', [$from_date, $to_date]);
                    });
                }
                if($request->get('user_id')!= ''){
                    $query->where(function ($query) use ($request) {
                        $user_id = trim($request->get('user_id'));
                        $query->where('user_id', '=',$user_id);
                    });
                }
              
            })
           
           ->make(true);
   }
   
   
    public function getReportAllInvoice(Request $request,$invoice)
    {  
        
        return DataTables::of($invoice)
               ->rawColumns(['batch_no'])
           
                ->addColumn(
                    'batch_no',
                    function ($invoice) { 
                        
                        return '<b>'.$invoice->disbursal->disbursal_batch->batch_id.'</b>';
                     
                    })
                  ->addColumn(
                    'batch_date',
                    function ($invoice)  {     
                           return  date('d/m/Y',strtotime($invoice->disbursal->disbursal_batch->created_at));
                  })
             
              ->addColumn(
                    'bills_no',
                    function ($invoice) {  
                      return $invoice->invoice->invoice_no;
                   })
                ->addColumn(
                    'bill_date',
                    function ($invoice) { 
                    return  Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y');
                        })
                 ->addColumn(
                    'due_date',
                    function ($invoice) {                        
                      return  Carbon::parse($invoice->payment_due_date)->format('d/m/Y');
                     
                })  
                ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        return   number_format($invoice->invoice->invoice_amount);
                       
                      
                })
                ->addColumn(            
                    'invoice_appr_amount',
                    function ($invoice) {                        
                          return number_format($invoice->invoice->invoice_approve_amount);  
                         })
                ->addColumn(
                    'balance',
                    function ($invoice) {
                       
                           return   number_format($invoice->invoice->invoice_approve_amount);   
                       
                    })
                   ->filter(function ($query) use ($request) {
                    if ($request->get('from_date') != '' && $request->get('to_date') != '') {                        
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('payment_due_date', [$from_date, $to_date]);
                    }
                     if ($request->get('customer_id') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $customer_id = trim($request->get('customer_id'));
                             $query->whereHas('invoice.lms_user', function($query1) use ($customer_id) {
                                $query1->where('customer_id', 'like',"%$customer_id%");
                             });
                            
                        });                        
                    }
                 })
              ->make(true);
    }  
    
     public function getReportAllOverdueInvoice(Request $request,$invoice)
    {  
        
        return DataTables::of($invoice)
               ->rawColumns(['batch_no','od'])
           
                ->addColumn(
                    'batch_no',
                    function ($invoice) { 
                        
                        return '<b>'.$invoice->disbursal->disbursal_batch->batch_id.'</b>';
                     
                    })
                  ->addColumn(
                    'batch_date',
                    function ($invoice)  {     
                           return  date('d/m/Y',strtotime($invoice->disbursal->disbursal_batch->created_at));
                  })
             
              ->addColumn(
                    'bills_no',
                    function ($invoice) {  
                      return $invoice->invoice->invoice_no;
                   })
                ->addColumn(
                    'bill_date',
                    function ($invoice) { 
                    return  Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y');
                        })
                 ->addColumn(
                    'due_date',
                    function ($invoice) {                        
                      return  Carbon::parse($invoice->payment_due_date)->format('d/m/Y');
                     
                })  
                ->addColumn(            
                    'invoice_amount',
                    function ($invoice) {                        
                        return   number_format($invoice->invoice->invoice_amount);
                       
                      
                })
                ->addColumn(            
                    'invoice_appr_amount',
                    function ($invoice) {                        
                          return number_format($invoice->invoice->invoice_approve_amount);  
                         })
                ->addColumn(
                    'balance',
                    function ($invoice) {
                       
                           return   number_format($invoice->invoice->invoice_approve_amount);   
                       
                    })
                   ->addColumn(
                    'od',
                    function ($invoice) {
                       
                           return '<b>'.$invoice->InterestAccrual->count().'</b>';
                       
                    }) 
                   ->filter(function ($query) use ($request) {
                    if ($request->get('from_date') != '' && $request->get('to_date') != '') {                        
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('payment_due_date', [$from_date, $to_date]);
                    }
                     if ($request->get('customer_id') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $customer_id = trim($request->get('customer_id'));
                             $query->whereHas('invoice.lms_user', function($query1) use ($customer_id) {
                                $query1->where('customer_id', 'like',"%$customer_id%");
                             });

                            
                        });                        
                    }
                 })
              ->make(true);
    }  
     public function getInvoiceRealisationList(Request $request,$invoice)
    {  
        
        return DataTables::of($invoice)
               ->rawColumns(['debtor_name','od','business','relisation_date'])
           
                ->addColumn(
                    'debtor_name',
                    function ($invoice) { 
                     return '<b>'.$invoice->invoice->anchor->comp_name.'</b>';
                    })
                  ->addColumn(
                    'debtor_acc_no',
                    function ($invoice)  {     
                           return  ($invoice->Invoice->anchor->anchorAccount) ? $invoice->Invoice->anchor->anchorAccount->acc_no : '' ;
                  })
             
              ->addColumn(
                    'invoice_date',
                    function ($invoice) {  
                        return  Carbon::parse($invoice->invoice->invoice_date)->format('d/m/Y');
                   })
                     ->addColumn(
                    'invoice_due_amount',
                    function ($invoice) {  
                        return  number_format($invoice->invoice->invoice_approve_amount);
                   })
                ->addColumn(
                    'invoice_due_amount_date',
                    function ($invoice) { 
                    return  Carbon::parse($invoice->payment_due_date)->format('d/m/Y');
                        })
                 ->addColumn(
                    'grace_period',
                    function ($invoice) {                        
                      return  $invoice->grace_period;
                     
                })  
                ->addColumn(            
                    'relisation_date',
                    function ($invoice) {  
                      $payment  = '';                   
                       foreach($invoice->transaction as $row)
                      {
                           if( $row->payment->date_of_payment)
                           {
                             $payment.= Carbon::parse($row->payment->date_of_payment)->format('d/m/Y')."</br>";
                           }
                           
                      }
                    return substr($payment,0,-1);
                       
                })
                ->addColumn(            
                    'relisation_amount',
                    function ($invoice) {                        
                          return number_format($invoice->invoice->invoice_approve_amount);  
                         })
                ->addColumn(
                    'cheque',
                    function ($invoice) {
                       
                      $chk  = '';                   
                       foreach($invoice->transaction as $row)
                      {
                           if( $row->payment->utr_no)
                           {
                             $chk.$row->payment->utr_no.",";
                           }
                            if( $row->payment->unr_no)
                           {
                             $chk.= $row->payment->unr_no.",";
                           }
                            if( $row->payment->cheque_no)
                           {
                             $chk.= $row->payment->cheque_no.",";
                           }
                      }
                    return substr($chk,0,-1);
                     
                       
                    })
                   ->addColumn(
                    'od',
                    function ($invoice) {
                       
                           return '<b>'.($invoice->InterestAccrual) ? $invoice->InterestAccrual->count() : ''.'</b>';
                       
                    }) 
                    ->addColumn(
                    'business',
                    function ($invoice) {
                       
                           return '<b>'.$invoice->invoice->business->biz_entity_name.'</b>';
                       
                    }) 
                   ->filter(function ($query) use ($request) {
                    if ($request->get('from_date') != '' && $request->get('to_date') != '') {                        
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('payment_due_date', [$from_date, $to_date]);
                    }
                     if ($request->get('customer_id') != '') {                        
                        $query->where(function ($query) use ($request) {
                            $customer_id = trim($request->get('customer_id'));
                             $query->whereHas('invoice.lms_user', function($query1) use ($customer_id) {
                                $query1->where('customer_id', 'like',"%$customer_id%");
                             });

                            
                        });                        
                    }
                 })
              ->make(true);
    }   

    public function getEodList(Request $request,$eod)
    {  
        return DataTables::of($eod)
            ->rawColumns(['status'])
           
            ->addColumn( 'current_sys_date', function ($eod) {
                if($eod->created_at) 
                return \Helpers::convertDateTimeFormat($eod->created_at, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i A');
            })

            ->addColumn( 'sys_started_at', function ($eod) {
                if($eod->sys_start_date) 
                return \Helpers::convertDateTimeFormat($eod->sys_start_date, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i A');
            })

            ->addColumn( 'sys_stopped_at', function ($eod) {
                if($eod->sys_end_date)
                return \Helpers::convertDateTimeFormat($eod->sys_end_date, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i A'); 
            })

            ->addColumn( 'eod_process_mode', function ($eod) {
                $processedBy = null;
                switch ($eod->eod_process_mode) {
                    case '1':
                        $processedBy = 'Automatically';
                        break;
                    case '2':
                        $processedBy = 'Manually';
                        break;
                    default:
                        $processedBy = ''; 
                        break;
                }
                return $processedBy;
            })

            ->addColumn( 'eod_process_started_at', function ($eod) {
                if($eod->eod_process_start) 
                return \Helpers::convertDateTimeFormat($eod->eod_process_start, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s A');
            })

            ->addColumn( 'eod_process_stopped_at', function ($eod) {
                if($eod->eod_process_end)
                return \Helpers::convertDateTimeFormat($eod->eod_process_end, $fromDateFormat='Y-m-d H:i:s', $toDateFormat='d-m-Y h:i:s A'); 
            })

            ->addColumn( 'total_sec', function ($eod) { 
                if($eod->total_sec){
                    $dt1 = new DateTime("@0");
                    $dt2 = new DateTime("@$eod->total_sec");
                    $days = $dt1->diff($dt2)->format('%a');
                    $hours = $dt1->diff($dt2)->format('%h');
                    $minutes = $dt1->diff($dt2)->format('%i');
                    $seconds =  $dt1->diff($dt2)->format('%s');
                    $result = '';
                     
                    $result .= $days? $days. ' days, ':''; 
                    $result .= $hours? $hours. ' hours, ':''; 
                    $result .= $minutes? $minutes. ' minutes, ':''; 
                    $result .= $seconds? $seconds. ' seconds ':'';   
                    return $result;              
                }
            })

            ->addColumn( 'status', function ($eod) { 
                $status = null;
                switch ($eod->status) {
                    case '0':
                        $status = '<i class="fa fa-circle-o-notch fa-spin  fa-fw margin-bottom"></i>'; 
                        break;
                    case '1':
                        $status = '<i class="fa fa-check" title="Passed" style="color:green" aria-hidden="true"></i>'; 
                        break;
                    case '2':
                        $status = '<i class="fa fa-ban" title="Stopped" style="color:black" aria-hidden="true"></i>'; 
                        break;
                    case '3':
                        $status = '<i class="fa fa-times" title="Failed" style="color:red" aria-hidden="true"></i>'; 
                        break;
                    default:
                        $status = ''; 
                        break;
                }
                return $status;
            })
            // ->filter(function ($query) use ($request) {
            // })
            ->make(true);
    } 

    public function getCibilReportLms(Request $request, $data) {
       return DataTables::of($data)
           ->rawColumns(['pull_status'])
           ->addColumn('pull_status',  function ($user) {
                   $pull_status = '<a href="'.route('download_lms_cibil_reports', ['type'=>'excel', 'batch_no' => $user->batch_no]).'" class="btn btn-success btn-sm">Download Report</a>';
                   return $pull_status;
           })
           ->filter(function ($query) use ($request) {
                if($request->get('from_date')!= '' && $request->get('to_date')!=''){
                    $query->where(function ($query) use ($request) {
                        $from_date = Carbon::createFromFormat('d/m/Y', $request->get('from_date'))->format('Y-m-d');
                        $to_date = Carbon::createFromFormat('d/m/Y', $request->get('to_date'))->format('Y-m-d');
                        $query->WhereBetween('created_at', [$from_date, $to_date]);
                    });
                }
                if($request->get('search_keyword')!= ''){
                    $query->where(function ($query) use ($request) {
                        $search_keyword = trim($request->get('search_keyword'));
                        $query->where('batch_no', 'like',"%$search_keyword%");
                    });
                }
              
            })
           ->make(true);
   }
   
   /**
    * TDS Data table listing
    * 
    * @param Request $request
    * @param type $data
    * @return type
    */
   public function tds(Request $request, $data) {
       $this->sr_no = 1;
       return DataTables::of($data)    
           ->editColumn('user_id', function ($tds) {
               return \Helpers::formatIdWithPrefix($tds->user_id, 'LEADID');
           })      
           ->editColumn('customer_name', function ($tds) {
               return $tds->biz_entity_name;
           })  
           ->editColumn('trans_name', function ($tds) {
               return $tds->trans_name ;//== 3 ? 'tds' : '';
           })   
           ->editColumn('date_of_payment', function ($tds) {
               return date('d-m-Y', strtotime($tds->date_of_payment));
           })
           ->editColumn('trans_date', function ($tds) {
               return date('d-m-Y', strtotime($tds->trans_date));
           })
           ->editColumn('amount',  function ($tds) {
               return $tds->amount;
           })
           ->editColumn('trans_by',  function ($tds) {
               $full_name = $tds->f_name.' '.$tds->l_name;
               return $full_name;
           })
           ->editColumn('tds_certificate_no',  function ($tds) {
               return $tds->tds_certificate_no;
           })
           ->editColumn('file_id',  function ($tds) {
               return $tds->file_id == 0 ? 'N' : '';
           }) 
           ->filter(function ($query) use ($request) {
                if($request->get('user_id')!= ''){
                    $query->where(function ($query) use ($request) {
                        $user_id = trim($request->get('user_id'));
                        $query->where('payments.user_id', '=',$user_id);
                    });
                }
              
            })
           
           ->make(true);
   }
   


    // TDS in master
    public function getTDSLists(Request $request, $data)
    {
        $this->sr_no = 1;
        return DataTables::of($data)
                ->rawColumns(['is_active', 'action', ''])
                
                ->editColumn(
                    'sr_no',
                    function ($data) {
                    return $this->sr_no++;
                }) 
                ->addColumn(
                'start_date', 
                function ($data) {
                    return ($data->start_date) ? date('d-M-Y', strtotime($data->start_date)) : '---';
                })
                ->addColumn(
                'end_date', 
                function ($data) {
                    return ($data->end_date) ? date('d-M-Y', strtotime($data->end_date)) : '---';
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
                    $edit = '<a class="btn btn-action-btn btn-sm" data-toggle="modal" data-target="#editTDSFrame" title="Edit TDS Detail" data-url ="'.route('edit_tds', ['id' => $data->id]).'" data-height="300px" data-width="100%" data-placement="top"><i class="fa fa-edit"></a>';
                    $status = '<div class="btn-group""><label class="badge badge-'.($act==1 ? 'success' : 'danger pt-2').' current-status" style="margin-bottom: 13px">'.($act==1 ? 'Active' : 'In-Active').'&nbsp; &nbsp;</label> &nbsp;'. $edit.'</div>';
                    return $status;
                    }
                )
                ->make(true);
    }
   
}
