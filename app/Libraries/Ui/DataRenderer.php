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
                ->rawColumns(['id', 'checkbox', 'action', 'email'])
                ->addColumn(
                    'id',
                    function ($user) {
                    $link = '000'.$user->user_id;
                    
                        return "<a id=\"" . $user->user_id . "\" href=\"" . route('user_detail', ['user_id' => $user->user_id]) . "\" rel=\"tooltip\"   >$link</a> ";
                    }
                )
                ->editColumn(
                        'status',
                        function ($user) {
                    if ($user->status == config('inv_common.ACTIVE')) {

                        return "Active";
                    } else {
                        return "In Active";
                    }
                })
                ->addColumn(
                        'checkbox',
                        function ($user) {
                        $ids = $user->user_id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                
                ->editColumn(
                        'user_name',
                        function ($user) {
                    $full_name = $user->f_name.' '.$user->l_name;
                    return $full_name;
                    
                })


                ->editColumn(
                    'email',
                    function ($user) {
                    return "<a  data-original-title=\"Edit User\" href=\"" . route('view_user_detail', ['user_id' => $user->user_id]) . "\" data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";

                })



                



                ->addColumn(
                    'action',
                    function ($users) {
                        return "<a  data-original-title=\"Edit User\" href=\"" . route('edit_backend_user', ['user_id' => $users->user_id]) . "\" data-placement=\"top\" class=\"CreateUser\" ><i class=\"fa fa-edit\"></i></a> ";
                    }
                )
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_email') != '') {
                        if ($request->has('by_email')) {
                            $query->where(function ($query) use ($request) {
                                $by_nameOrEmail = trim($request->get('by_email'));
                                $query->where('users.first_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('users.last_name', 'like', "%$by_nameOrEmail%")
                                //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                
                                $query->where('users.status', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    /*
     * 
     * get all country list
     */
    public function getCountryList(Request $request, $countries) 
    {   

        return DataTables::of($countries)
                ->rawColumns(['checkbox','action'])
                ->editColumn(
                        'is_active',
                        function ($countries) {
                    if ($countries->is_active == 1) {
                        return "Active";
                        
                    } 
                    if ($countries->is_active == 0) {
                        return "In Active";
                        
                    } 

                })
                ->addColumn(
                        'checkbox',
                        function ($countries) {
                        $ids = $countries->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                ->addColumn('action',
                        function($countries) {
                    return view('backend/master/country/_action')->with('countries',
                                    $countries)->render();
                })
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_name') != '') {
                        if ($request->has('by_name')) {
                            $query->where(function ($query) use ($request) {
                                $by_name = trim($request->get('by_name'));
                                $query->where('mst_country.country_name', 'like',
                                        "%$by_name%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                $query->where('mst_country.is_active', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }

    /*
     * 
     * get all country list
     */
    public function getStateList(Request $request, $state)
    {

        return DataTables::of($state)
                ->rawColumns(['checkbox','action'])
                ->editColumn(
                        'is_active',
                        function ($state) {
                    if ($state->is_active == 1) {
                                return "Active";
                            }
                            if ($state->is_active == 0) {
                                return "In Active";
                            }
                        })
                ->addColumn(
                        'checkbox',
                        function ($state) {
                    $ids = $state->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                ->addColumn('action',
                        function($state) {
                    return view('backend/master/state/_action')->with('state',
                                    $state)->render();
                })
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_state_name') != '') {
                        if ($request->has('by_state_name')) {
                            $query->where(function ($query) use ($request) {
                                $by_name = trim($request->get('by_state_name'));
                                $query->where('mst_state.name', 'like',
                                        "%$by_name%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                
                                $query->where('mst_state.is_active', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    /*
     * 
     * get all cluster list
     */
    public function getClusterList(Request $request, $cluster)
    {

        return DataTables::of($cluster)
                ->rawColumns(['checkbox','action'])
                ->editColumn(
                        'is_active',
                        function ($cluster) {
                    if ($cluster->is_active == 1) {
                                return "Active";
                            }
                            if ($cluster->is_active == 0) {
                                return "In Active";
                            }
                        })
                ->addColumn(
                        'checkbox',
                        function ($cluster) {
                    $ids = $cluster->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                ->addColumn('action',
                        function($cluster) {
                    return view('backend/master/cluster/_action')->with('cluster',
                                    $cluster)->render();
                })
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_title') != '') {
                        if ($request->has('by_title')) {
                            $query->where(function ($query) use ($request) {
                                $by_name = trim($request->get('by_title'));
                                $query->where('mst_cluster.title', 'like',
                                        "%$by_name%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                
                                $query->where('mst_cluster.is_active', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    /*
     * 
     * get all Right type list
     */
    public function getRightTyprList(Request $request, $right)
    {

        return DataTables::of($right)
                ->rawColumns(['checkbox','action'])
                ->editColumn(
                        'is_active',
                        function ($right) {
                    if ($right->is_active == 1) {
                                return "Active";
                            }
                            if ($right->is_active == 0) {
                                return "In Active";
                            }
                        })
                ->addColumn(
                        'checkbox',
                        function ($right) {
                    $ids = $right->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                ->addColumn('action',
                        function($right) {
                    return view('backend/master/right_type/_action')->with('right',
                                    $right)->render();
                })
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_title') != '') {
                        if ($request->has('by_title')) {
                            $query->where(function ($query) use ($request) {
                                $by_name = trim($request->get('by_title'));
                                $query->where('mst_right_type.title', 'like',
                                        "%$by_name%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                
                                $query->where('mst_right_type.is_active', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    /*
     * 
     * get all Right type list
     */
    public function getSourceList(Request $request, $source)
    {

        return DataTables::of($source)
                ->rawColumns(['checkbox','action'])
                ->editColumn(
                        'is_active',
                        function ($source) {
                    if ($source->is_active == 1) {
                                return "Active";
                            }
                            if ($source->is_active == 0) {
                                return "In Active";
                            }
                        })
                ->addColumn(
                        'checkbox',
                        function ($source) {
                    $ids = $source->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                ->addColumn('action',
                        function($source) {
                    return view('backend/master/source/_action')->with('source',
                                    $source)->render();
                })
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_title') != '') {
                        if ($request->has('by_title')) {
                            $query->where(function ($query) use ($request) {
                                $by_name = trim($request->get('by_title'));
                                $query->where('mst_heard_from.title', 'like',
                                        "%$by_name%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                
                                $query->where('mst_heard_from.is_active', 'like',
                                        "%$by_status%");
                            });
                        }
                    }
                })
                ->make(true);
    }
    /*
     *
     * get all Right type list
     */
    public function getRightList(Request $request, $right)
    {
        return DataTables::of($right)
                ->rawColumns(['checkbox','action'])
                
            ->editColumn(
                        'user_name',
                        function ($right) {
                    $full_name = $right->first_name.' '.$right->last_name;
                    return $full_name;

                })
                
                ->editColumn(
                        'phase',
                        function ($right) {
                                return (($right->phase == 1)?'Completed' :'Draft');
                            })
                ->editColumn(
                        'is_featured',
                        function ($right) {
                                return (($right->is_featured == 1)?'Featured' :'Normal');
                            })
                
                ->addColumn(
                        'checkbox',
                        function ($right) {
                    $ids = $right->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                ->addColumn('action',
                        function($right) {
                    return view('backend/master/right/_action')->with('right',
                                    $right)->render();
                })
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_title') != '') {
                        if ($request->has('by_title')) {
                            $query->where(function ($query) use ($request) {
                                $by_name = trim($request->get('by_title'));
                                $query->where('title', 'like',
                                        "%$by_name%");
                               
                            });
                        }
                    }

                })
                ->make(true);
    }
    /*
     * 
     * get all country list
     */
    public function getScoutList(Request $request, $user)
    {
        return DataTables::of($user)
                ->rawColumns(['id', 'checkbox', 'action','is_approved'])
                ->addColumn(
                    'id',
                    function ($user) {
                    $link = '000'.$user->id;
                    return $link;
                        //return "<a style = 'color:#d24636;font-weight:bold' id=\"" . $user->id . "\" href=\"" . route('user_detail', ['user_id' => $user->id]) . "\" rel=\"tooltip\"   >$link</a> ";
                    }
                )
                ->addColumn(
                        'is_approved',
                        function ($user) {
                    if ($user->is_admin_approved == 1) {
                        $rr = '<span class="active" style = "color:white;padding:5px 12px 6px;background:#5abc48;border-radius:5px">Active</span>';
                        return $rr;
                    }
                    else if ($user->is_admin_approved == 2) {
                        $rr = '<span class="reject" style = "color:white;padding:5px 12px 6px;background:red;border-radius:5px">Reject</span>';
                        return $rr;
                    }
                    else {
                        $rr = '<span  class="pending" style = "color:white;padding:5px;background:Orange;border-radius:5px">Pending</span>'; 
                        return $rr;
                    }
                })
                ->addColumn(
                        'checkbox',
                        function ($user) {
                        $ids = $user->id;
                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
                    return $chkBox;
                })
                
                ->editColumn(
                        'user_name',
                        function ($user) {
                    $full_name = $user->first_name.' '.$user->last_name;
                    return $full_name;
                    
                })
                
                ->addColumn(
                    'action',
                    function ($user) {
                    return "<a style = 'color:#d24636;font-weight:bold' id=\"" . $user->id . "\" href=\"" . route('user_detail', ['user_id' => $user->id,'is_scout'=>1]) . "\" rel=\"tooltip\"   ><i class=\"fa fa-edit\"></i></a> ";
                    }
                )
                ->filter(function ($query) use ($request) {

                    if ($request->get('by_email') != '') {
                        if ($request->has('by_email')) {
                            $query->where(function ($query) use ($request) {
                                $by_nameOrEmail = trim($request->get('by_email'));
                                $query->where('users.first_name', 'like',"%$by_nameOrEmail%")
                                ->orWhere('users.last_name', 'like', "%$by_nameOrEmail%")
                                //->orWhere('users.full_name', 'like', "%$by_nameOrEmail%")
                                ->orWhere('users.email', 'like', "%$by_nameOrEmail%");
                            });
                        }
                    }
                    if ($request->get('status') != '') {
                        if ($request->has('status')) {
                            $query->where(function ($query) use ($request) {
                                $by_status = (int) trim($request->get('status'));
                                $query->where('users.is_admin_approved', 'like',
                                        "%$by_status%");
                                        
                            });
                        }
                    }
                })
                ->make(true);
    }

}