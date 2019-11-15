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
//                ->editColumn(
//                        'status',
//                        function ($user) {
//                    if ($user->is_active == config('inv_common.ACTIVE')) {
//                        return "Active";
//                    } else {
//                        return "In Active";
//                    }
//                })
//                ->addColumn(
//                        'checkbox',
//                        function ($user) {
//                        $ids = $user->user_id;
//                    $chkBox = '<input type="checkbox" name="del_selected[]" value="'.$ids.'" class="checkAllBox del_selected" />';
//                    return $chkBox;
//                })
                ->editColumn(
                        'name',
                        function ($user) {
                    $full_name = $user->f_name.' '.$user->l_name;
                    return $full_name;
                    
                })
                ->editColumn(
                    'email',
                    function ($user) {
                    return "<a  data-original-title=\"Edit User\" href=\"#\"  data-placement=\"top\" class=\"CreateUser\" >".$user->email."</a> ";

                })
                ->editColumn(
                        'assigned',
                        function ($user) {
                    if ($user->is_assign == 0) {
                        return "<span style=\"color:red\">Not Assigned</span>";
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
                    return  "<a  data-toggle=\"modal\" data-target=\"#editLead\" data-url =\"" . route('edit_backend_lead', ['user_id' => $users->user_id]) . "\" data-height=\"500px\" data-width=\"100%\" data-placement=\"top\" class=\"btn btn-warning btn-sm  report-btn btn-x-sm\"><i class=\"fa fa-edit\"></a>";
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
}