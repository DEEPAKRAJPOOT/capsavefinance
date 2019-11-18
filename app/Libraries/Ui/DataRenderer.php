<?php

namespace App\Libraries\Ui;

use DataTables;
use Illuminate\Http\Request;
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
                        return "<a id=\"" . $user->user_id . "\" href=\"#\" rel=\"tooltip\"   >$link</a> ";
                        
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
                    $achorId = $user->anchor_id; 
                    return $achorId;
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
                    return  "<a  data-toggle=\"modal\" data-target=\"#editLead\" data-url =\"" . route('edit_backend_lead', ['user_id' => $users->user_id]) . "\" data-height=\"280px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-warning btn-sm  report-btn btn-x-sm\"><i class=\"fa fa-edit\"></a>";
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
                                
                                $query->where('users.is_assign', 'like',
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
                ->rawColumns(['app_id', 'action'])
                ->addColumn(
                    'app_id',
                    function ($app) {
                        $link = route('cam_overview', ['app_user_id' => $app->user_id, 'app_id' => $app->app_id]);
                        return "<a id=\"app-id-" . $app->app_id . "\" href=\"" . $link . "\" rel=\"tooltip\">" . $app->app_id . "</a> ";
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
                        return $app->assoc_anchor ? $app->assoc_anchor : '';
                })
                ->addColumn(
                    'user_type',
                    function ($app) {                        
                        return $app->user_type ? $app->user_type : '';
                })                
                ->addColumn(
                    'assignee',
                    function ($app) {
                        return $app->assignee ? $app->assignee : '';
                })
                ->addColumn(
                    'shared_detail',
                    function ($app) {
                    return $app->shared_detail ? $app->shared_detail : '';

                })
                ->editColumn(
                    'status',
                    function ($app) {
                    $app_status = config('common.app_status');
                    return $app_status[$app->status];

                })
                ->addColumn(
                    'action',
                    function ($app) {
                        //return  "<a  data-toggle=\"modal\" data-target=\"#editLead\" data-url =\"" . route('edit_backend_lead', ['user_id' => $users->user_id]) . "\" data-height=\"500px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-warning btn-sm  report-btn btn-x-sm\"><i class=\"fa fa-edit\"></a>";
                    return '<div class="d-flex inline-action-btn"><a title="Add App Note" href="#" data-toggle="modal" data-target="#noteFrame" data-url ="' . route('backend_notes_from') . '" data-height="500px" data-width="100%" data-placement="top" class="btn btn-action-btn btn-sm"><i class="fa fa-file-image-o" aria-hidden="true"></i></a>
									   <a href="#" data-toggle="modal" data-target="#myModal1" title="Add Status" class="btn btn-action-btn btn-sm"><i class="fa fa-outdent" aria-hidden="true"></i></a>
									   <a href="#" title="Assign Case" data-toggle="modal" data-target="#myModal2" class="btn btn-action-btn btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i></a> </div>';
                    }
                )
                ->filter(function ($query) use ($request) {
                    /*
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
                                
                                $query->where('users.is_assign', 'like',
                                        "%$by_status%");
                            });
                        }
                    }                    
                    */
                })
                ->make(true);
    }    
}