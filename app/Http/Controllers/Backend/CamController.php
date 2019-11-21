<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FinanceInformationRequest as FinanceRequest;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Models\Business;
use App\Inv\Repositories\Models\BizOwner;
date_default_timezone_set('Asia/Kolkata');
use App\Inv\Repositories\Models\Cam;
use Auth;
use Session;

class CamController extends Controller
{
     protected $appRepo;
	  public function __construct(){
        $this->middleware('auth');
       
        }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.cam.overview');
    }

    public function camInformationSave(Request $request)
    {
    	$arrCamData = $request->all();
        $arrCamData['biz_id'] = '12';
        $arrCamData['app_id'] = '12';
        $userId = Auth::user()->user_id;
        if(!isset($arrCamData['rating_no'])){
            $arrCamData['rating_no'] = NULL;
        }
        Cam::creates($arrCamData, $userId);
        Session::flash('message',trans('Cam Information Saved Successfully'));
        return redirect()->route('cam_overview');
    }

    public function finance()
    {
        return view('backend.cam.finance');

    }
    public function finance_store(FinanceRequest $request, FinanceModel $fin)
    {
        $financeid = $this->getFinanceId();
        $insert_data = [];
        $post_data = $request->all();
        unset($post_data['_token']);
        $curr = date('Y');
        foreach ($post_data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                   $insert_data[$curr- 2 + $k][$key]= $v;
                }
            }else{
                $insert_data[$curr-2][$key]= $value;
               $insert_data[$curr-1][$key]= $value;
               $insert_data[$curr][$key]= $value;
            }
        }
        $insert_data[$curr-2]['finance_id'] = $financeid;
        $insert_data[$curr-2]['period_ended'] = date($curr-2 . '-03-31');
        $insert_data[$curr-1]['finance_id'] = $financeid;
        $insert_data[$curr-1]['period_ended'] = date($curr-1 . '-03-31');
        $insert_data[$curr]['finance_id'] = $financeid;
        $insert_data[$curr]['period_ended'] = date($curr . '-03-31');
        
        foreach ($insert_data as  $ins_arr) {
            $fin->create($ins_arr);
        }
        Session::flash('message',trans('Record Inserted Successfully'));
        return redirect()->route('cam_finance');
    }

    private function getFinanceId() {
        $y = date('Y') - 2018 + 64;
        $m = date('m') + 64;
        $d = date('d');
        $d = (($d <= 26) ? ($d + 64) : ($d + 23));
        $h = date('H') + 65;
        $i = date('i');
        $s = date('s');
        $no = chr($y) . chr($m) . chr($d) . chr($h). $i . $s. sprintf('%04d',mt_rand(0,9999));
        return $no;
    }

    private function financeid_reverse($value='AKSK31268170')
    {
        $date = substr($value, 0, 4);
        $time = substr($value, 4, 4);
        list($y , $m, $d, $h) = str_split($date);
        $y = ord($y) + 2018-64;
        $m = ord($m) -64;
        $d = is_string($d) ? ord($d) - 64 : ord($d) - 23;
        $h = ord($h) - 65;
        $datetime = "$y$m$d$h$time";
        return $datetime;
    }

    public function showCibilForm(Request $request){
        $biz_id=1;
        $arrCompanyDetail = Business::getCompanyDataByBizId($biz_id);
        $arrCompanyOwnersData = BizOwner::getCompanyOwnerByBizId($biz_id);
        return view('backend.cam.cibil', compact('arrCompanyDetail', 'arrCompanyOwnersData'));
    }

}
