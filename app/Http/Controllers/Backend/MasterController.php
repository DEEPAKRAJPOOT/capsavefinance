<?php

namespace App\Http\Controllers\Backend;

use Helpers;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\CountryMasterRequest;
use App\Http\Requests\Master\StateMasterRequest;
use App\Http\Requests\Master\ClusterMasterRequest;
use App\Http\Requests\Master\RightTypeMasterRequest;
use App\Http\Requests\Master\RightMasterRequest;
use App\Http\Requests\Master\SourceMasterRequest;
use App\Inv\Repositories\Models\Master\Country;
use App\Inv\Repositories\Models\Master\Cluster;
use App\Inv\Repositories\Models\Master\State;
use App\Inv\Repositories\Models\Master\RightType;
use App\Inv\Repositories\Models\Master\Source;
use App\Inv\Repositories\Models\Rights;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;

class MasterController extends Controller
{

     /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user)
    {
        $this->userRepo = $user;
    }

    /**
     * Show country list.
     *
     * @return Response
     */
    public function viewCountryList(Request $request)
    {    
        return view('backend.master.country.index');
    }
    
    /*
     * show country edit form 
     * @param Request $request 
     */
    public function showCountriesForm(Request $request) 
    {

        try {
            $id = (int) $request->query('id');
            $country_data = [];
            if (!empty($id)) {
                $country_data = Country::getCountryById($id);
            }
            return view('backend.master.country.add_edit_countries')
                            ->with(['country_data' => $country_data]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }

    /*
     * save or update country
     * @param Request $request 
     */
    public function addEditCountries(CountryMasterRequest $request) 
    {
        
        try {
            $postArr = [];
            $country_id = $request->get('country_id');
            $postArr['country_name'] = !empty($request->get('country_name')) ? $request->get('country_name') : null;
            $postArr['country_code'] = !empty($request->get('country_code')) ? $request->get('country_code') : null;
            $postArr['is_active'] = !empty($request->get('status')) ? $request->get('status') : 0;
            $response = Country::saveOrEditCountry($postArr, (int) $country_id);
            Session::flash('message', 'Country save successfully!');
            if ($country_id)
                Session::flash('message', 'Country updated successfully!');
            return redirect(route('manage_country'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /**
     * Show State list.
     *
     * @return Response
     */
    public function viewStateList(Request $request)
    {
        return view('backend.master.state.index');
    }

    
     /*
     * Show State form
     * @param Request $request 
     */
    public function showStateForm(Request $request) 
    {
       
        try {
            $id = (int) $request->query('id');
            $state_data = [];
            if (!empty($id)) {
                $state_data = State::getStateById($id);
            }
            $countryDropDown = Country::getDropDown();
            
            return view('backend.master.state.add_edit_state')
                        ->with(['state_data' => $state_data])
                        ->with(['countryDropDown' => $countryDropDown]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    
    /*
     * save or update state
     * @param Request $request 
     */
    public function addEditState(StateMasterRequest $request) 
    {
        
        try {
            $postArr = [];
            $country_id = $request->get('state_id');
            $postArr['country_id'] = !empty($request->get('country_id')) ? $request->get('country_id') : null;
            $postArr['name'] = !empty($request->get('state_name')) ? $request->get('state_name') : null;
            $postArr['is_active'] = !empty($request->get('status')) ? $request->get('status') : 0;
            $response = State::saveOrEditState($postArr, (int) $country_id);
            Session::flash('message', trans('master.state_save_msg'));
            if ($country_id)
                Session::flash('message', trans('master.state_update_msg'));
            return redirect(route('manage_state'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /**
     * Show Cluster list.
     *
     * @return Response
     */
    public function viewClusterList(Request $request)
    {
        return view('backend.master.cluster.index');
    }
    
     /*
     * Show Clustor form
     * @param Request $request 
     */
    public function showClusterForm(Request $request) 
    {
       
        try {
            $id = (int) $request->query('id');
            $cluster_data = [];
            if (!empty($id)) {
                $cluster_data = Cluster::getClusterById($id);
            }
            
            return view('backend.master.cluster.add_edit_cluster')
                            ->with(['cluster_data' => $cluster_data]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
     /*
     * save or update country
     * @param Request $request 
     */
    public function addEditCluster(ClusterMasterRequest $request) 
    {
        
        try {
            $postArr = [];
            $cluster_id = $request->get('cluster_id');
            
            $postArr['title'] = !empty($request->get('title')) ? $request->get('title') : null;
            $postArr['is_active'] = !empty($request->get('status')) ? $request->get('status') : 0;
            $response = Cluster::saveOrEditCluster($postArr, (int) $cluster_id);
            Session::flash('message', trans('master.cluster_save_msg'));
            if ($cluster_id)
                Session::flash('message', trans('master.cluster_update_msg'));
            return redirect(route('manage_cluster'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /**
     * Show Rights Type list.
     *
     * @return Response
     */
    public function viewRightTypeList(Request $request)
    {
        return view('backend.master.right_type.index');
    }
    
    /*
     * Show Rights Type form
     * @param Request $request 
     */
    public function showRightTypeForm(Request $request) 
    {
       
        try { 
            $id = (int) $request->query('id');
            
            $rt_data = [];
            if (!empty($id)) {
                $rt_data = RightType::getRightTypeById($id);
            }
            
            return view('backend.master.right_type.add_edit_right_type')
                            ->with(['rt_data' => $rt_data]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /*
     * save or update Rights type
     * @param Request $request 
     */
    public function addEditRightType(RightTypeMasterRequest $request) 
    {

        try {
            $postArr = [];
            $rt_id = $request->get('rt_id');
            
            $postArr['title'] = !empty($request->get('title')) ? $request->get('title') : null;
            $postArr['is_active'] = !empty($request->get('status')) ? $request->get('status') : 0;
            $response = RightType::saveOrEditRightType($postArr, (int) $rt_id);
            Session::flash('message', trans('master.rt_save_msg'));
            if ($rt_id)
                Session::flash('message', trans('master.rt_update_msg'));
            return redirect(route('manage_right_type'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /**
     * Show Source list.
     *
     * @return Response
     */
    public function viewSourceList(Request $request)
    {   
        return view('backend.master.source.index');
    }
    
    /*
     * show Source form
     * @param Request $request 
     */
    public function showSourceForm(Request $request) 
    {
       
        try { 
            $id = (int) $request->query('id');
            
            $source_data = [];
            if (!empty($id)) {
                $source_data = Source::getSourceById($id);
            }
            
            return view('backend.master.source.add_edit_source')
                            ->with(['source' => $source_data]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }
    
    /*
     * save or update Source
     * @param Request $request 
     */
    public function addEditSource(SourceMasterRequest $request) 
    {

        try {
            $postArr = [];
            $source_id = $request->get('source_id');
            
            $postArr['title'] = !empty($request->get('title')) ? $request->get('title') : null;
            $postArr['is_active'] = !empty($request->get('status')) ? $request->get('status') : 0;
            $response = Source::saveOrEditSource($postArr, (int) $source_id);
            Session::flash('message', trans('master.source_save_msg'));
            if ($source_id)
                Session::flash('message', trans('master.source_update_msg'));
            return redirect(route('manage_source'));
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }

    /**
     * Show rights list.
     *
     * @return Response
     */
    public function viewRightList(Request $request)
    {
        return view('backend.master.right.index');
    }

    /*
     * show Source form
     * @param Request $request
     */
    public function showRightForm(Request $request)
    {

        try {
            $id = (int) $request->query('id');
            $source_data = [];
            if (!empty($id)) {
                $source_data = Rights::getRightDetailsByID($id);
            }

          return view('backend.master.right.add_edit_right')->with(['right' => $source_data]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }

    /*
     * show rights form
     * @param Request $request
     */
    public function viewSingleRight(Request $request)
    {
        $id = (int) $request->query('id');
         $right_data = Rights::getRightDetailsByID($id);
         return view('backend.master.right.view_right')->with(['right' => $right_data]);
    }

    /*
     * save or update Rights
     * @param Request $request
     */
    public function addEditRight(RightMasterRequest $request)
    {

        try {
            
            $postArr = [];
            $right_id = $request->get('right_id');

            $postArr['title'] = !empty($request->get('title')) ? $request->get('title') : null;
            $postArr['type_id'] = !empty($request->get('type_id')) ? $request->get('type_id') : null;
            $postArr['number'] = !empty($request->get('number')) ? $request->get('number') : null;
            $postArr['inventor'] = !empty($request->get('inventor')) ? $request->get('inventor') : null;
            $postArr['assignee'] = !empty($request->get('assignee')) ? $request->get('assignee') : null;
            $postArr['cluster_id'] = !empty($request->get('cluster_id')) ? $request->get('cluster_id') : null;
            $postArr['date'] = !empty($request->get('date')) ? Helpers::getDateByFormat($request->get('date'),'m/d/Y', 'Y-m-d') : null;
            $postArr['expiry_date'] = !empty($request->get('expiry_date')) ? Helpers::getDateByFormat($request->get('expiry_date'),'m/d/Y', 'Y-m-d') : null;
            $postArr['right_for_id'] = !empty($request->get('right_for_id')) ? $request->get('right_for_id') : null;
            $postArr['url'] = !empty($request->get('url')) ? $request->get('url') : null;
            $postArr['description'] = !empty($request->get('description')) ? $request->get('description') : null;
            $postArr['keywords'] = !empty($request->get('keywords')) ? $request->get('keywords') : null;
            $postArr['is_trading'] = !empty($request->get('is_trading')) ? $request->get('is_trading') : 0;
            $postArr['trading_type'] = !empty($request->get('trading_type')) ? $request->get('trading_type') : 2;
            $postArr['phase'] = !empty($request->get('phase')) ? $request->get('phase') : 0;
            $postArr['is_featured'] = !empty($request->get('is_featured')) ? $request->get('is_featured') : 0;
            $postArr['user_id'] = !empty($request->get('user_id')) ? $request->get('user_id') : 0;
          
            $response = Rights::saveOrEditRight($postArr, (int) $right_id);
            Session::flash('message', "New Rifgts has been Inserted");
            if ($right_id)
                Session::flash('message', 'Rights has been updated');
            return redirect()->route('manage_rights');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($e))->withInput();
        }
    }

}
