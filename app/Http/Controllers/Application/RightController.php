<?php

namespace App\Http\Controllers\Application;

use File;
use Auth;
use Session;
use Zip;
use Helpers;
use Carbon\Carbon;
use Mail;
use Event;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RightFormRequest;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Payment;
use PayPal\Api\PaymentHistory;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Input;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\Traits\ApiAccessTrait;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Models\User;
use Illuminate\Support\Facades\Storage;
use Image;

class RightController extends Controller {

    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;

    /**
     * Application repository
     *
     * @var App\Inv\Repositories\Contracts\ApplicationInterface
     */
    protected $application;

    use ApiAccessTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user, InvAppInterface $application) {
        $this->middleware('auth');
        $this->userRepo = $user;
        $this->application = $application;


        /** PayPal api context * */
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'], $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    /**
     * Show add rights form
     *
     * @return Response
     */
    public function showAddRightForm(Request $request) {
        try {



            $returnData = Session::get('rightData');

            $rightData = [];
            if (is_array($returnData)) {
                $rightData = $returnData;
                Session::forget('rightData');
            }


            $currentDate = date('Y-m-d');

            if (Auth::user()->membership == '2' && Auth::user()->membership_expired != null && Auth::user()->membership_expired != '' && $currentDate <= Auth::user()->membership_expired) {
                $membershipDetail['membership_expired'] = Auth::user()->membership_expired;
                $membershipDetail['membership'] = Auth::user()->membership;
                $membershipDetail['is_membership'] = 1;
            } else {
                $membershipDetail['membership_expired'] = NULL;
                $membershipDetail['membership'] = 1;

                $result = User::updateUser((int) Auth::user()->id, $membershipDetail);
                $membershipDetail['is_membership'] = 0;
                $user = Auth::user();
                $user->membership = 1;
                $user->membership_expired = NULL;
                Auth::setUser($user);
            }


            $draftedRight = [];
            $draftedRight = $this->application->getDraftedRight(Auth::user()->id);

            $rightDetail = $this->application->getRightDetailByuserID(Auth::user()->id);
            //$rightId = $rightDetail->id;
            $similarRights = [];
            if (!empty($rightDetail[0])) {
                $rightId = $rightDetail[0]->id;
                $similarRights = $this->application->getSimilarRights($rightDetail[0]->keywords, $rightDetail[0]->title, $rightId,Auth::user()->id);
            }
            $similarRandomRights = $this->application->getSimilarRandomRights(5,Auth::user()->id);
            //return view('frontend.add_right', compact('draftedRight','similarRights'));
            if (isset($similarRights) && count($similarRights) > 0) {
                return view('frontend.add_right', compact('draftedRight', 'similarRights', 'rightData', 'membershipDetail'));
            } else {
                $similarRights = $similarRandomRights;
                return view('frontend.add_right', compact('draftedRight', 'similarRights', 'rightData', 'membershipDetail'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show add rights form
     *
     * @return Response
     */
    public function showEditRightForm(Request $request) {
        try {
            $rightid = $request->get('right_id');
            $draftedRight = [];
            $draftedRight = $this->application->getRightDetail(Auth::user()->id, $rightid);
            $draftedAttach = $this->application->getAttachmentRights(Auth::user()->id, $rightid);
            $rightDetail = $this->application->getRightDetailByuserID(Auth::user()->id);
            //$rightId = $rightDetail->id;
            $similarRights = [];
            if (!empty($rightDetail[0])) {
                $rightId = $rightDetail[0]->id;
                $similarRights = $this->application->getSimilarRights($rightDetail[0]->keywords, $rightDetail[0]->title, $rightId,Auth::user()->id);
            }
            $similarRandomRights = $this->application->getSimilarRandomRights(5,Auth::user()->id);
            //return view('frontend.add_right', compact('draftedRight','similarRights'));
            if (isset($similarRights) && count($similarRights) > 0) {
                return view('frontend.edit_right', compact('draftedRight', 'similarRights', 'draftedAttach'));
            } else {
                $similarRights = $similarRandomRights;
                return view('frontend.edit_right', compact('draftedRight', 'similarRights', 'draftedAttach'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show add rights form
     *
     * @return Response
     */
    public function confirmPaymentForm(Request $request) {
        $payData = [];
        $fee = Session::get('paypal_amount');
        $curruntRoute = Session::get('curruntRoute');

        $payData['fee'] = $fee;
        $payData['curruntRoute'] = $curruntRoute;
        $payData['buyforyear'] = 0;
        $payData['purchase_type'] = 0;
        if ($curruntRoute == 'save_buy_right_popup') {
            $payData['buyforyear'] = Session::get('buyforyear');
            $payData['purchase_type'] = Session::get('purchase_type');
        }


        $rightData = [];
        if ($curruntRoute == 'add_right') {

            $myRightnumber = !empty(Session::get('myRightnumber')) ? Session::get('myRightnumber') : '';

            $tmpRight = $this->application->getTempRightByNumber($myRightnumber);
            $rightData = [];
            if ($tmpRight) {
                $rightData['number'] = $tmpRight->number;
                $rightData['user_id'] = $tmpRight->user_id;
                $rightData['title'] = $tmpRight->title;
                $rightData['type_id'] = $tmpRight->type_id;
                $rightData['right_type'] = $tmpRight->right_type;
                $rightData['thumbnail'] = $tmpRight->thumbnail;
                $rightData['inventor'] = $tmpRight->inventor;
                $rightData['assignee'] = $tmpRight->assignee;
                $rightData['cluster_id'] = $tmpRight->cluster_id;
                $rightData['right_group'] = $tmpRight->right_group;
                $rightData['date'] = $tmpRight->date;
                $rightData['expiry_date'] = $tmpRight->expiry_date;
                $rightData['right_for_id'] = $tmpRight->right_for_id;
                $rightData['right_for'] = $tmpRight->right_for;
                $rightData['url'] = $tmpRight->url;
                $rightData['description'] = $tmpRight->description;
                $rightData['keywords'] = $tmpRight->keywords;
                $rightData['is_exclusive_purchase'] = $tmpRight->is_exclusive_purchase;
                $rightData['is_non_exclusive_purchase'] = $tmpRight->is_non_exclusive_purchase;
                $rightData['exclusive_purchase_price'] = $tmpRight->exclusive_purchase_price;
                $rightData['non_exclusive_purchase_price'] = $tmpRight->non_exclusive_purchase_price;
                $rightData['subscription'] = $tmpRight->subscription;
                $rightData['num_of_month'] = $tmpRight->num_of_month;
                $rightData['phase'] = $tmpRight->phase;
                $rightData['attachment'] = $tmpRight->attachment;
                $rightData['attachment_file_name'] = $tmpRight->attachment_file_name;
                $rightData['tmp_right_id'] = $tmpRight->id;
            }

            Session::put('rightData', $rightData);
        } elseif (@$payData['curruntRoute'] == 'save_buy_right_popup' || @$payData['curruntRoute'] == 'save_report' || @$payData['curruntRoute'] == 'accept_term_condition') {


            $right_id = !empty(Session::get('current_right_ids')) ? Session::get('current_right_ids') : '';

            $objRight = $this->application->getRightDetailsByID($right_id);

            if ($objRight) {
                $rightData['number'] = $objRight[0]->number;
                $rightData['user_id'] = $objRight[0]->user_id;
                $rightData['title'] = $objRight[0]->title;
                $rightData['type_id'] = $objRight[0]->type_id;
                $rightData['right_type'] = $objRight[0]->mrt;
                $rightData['thumbnail'] = $objRight[0]->thumbnail;
                $rightData['inventor'] = $objRight[0]->inventor;
                $rightData['assignee'] = $objRight[0]->assignee;
                $rightData['cluster_id'] = $objRight[0]->cluster_id;
                $rightData['right_group'] = $objRight[0]->mct;
                $rightData['date'] = $objRight[0]->date;
                $rightData['expiry_date'] = $objRight[0]->expiry_date;
                $rightData['right_for_id'] = $objRight[0]->right_for_id;
                $rightData['right_for'] = $objRight[0]->mrft;
                $rightData['url'] = $objRight[0]->url;
                $rightData['description'] = $objRight[0]->description;
                $rightData['keywords'] = $objRight[0]->keywords;
                $rightData['is_exclusive_purchase'] = $objRight[0]->is_exclusive_purchase;
                $rightData['is_non_exclusive_purchase'] = $objRight[0]->is_non_exclusive_purchase;
                $rightData['exclusive_purchase_price'] = $objRight[0]->exclusive_purchase_price;
                $rightData['non_exclusive_purchase_price'] = $objRight[0]->non_exclusive_purchase_price;
                $rightData['subscription'] = $objRight[0]->subscription;
                $rightData['num_of_month'] = $objRight[0]->num_of_month;
                $rightData['phase'] = $objRight[0]->phase;
                $rightData['attachment'] = $objRight[0]->attachment;
                $rightData['attachment_file_name'] = $objRight[0]->attachment_file_name;
                $rightData['right_id'] = $objRight[0]->id;
            }
        }

        return view('frontend.confirm_payment', compact('payData', 'rightData'));
    }

    /**
     * Show add rights form
     *
     * @return Response
     */
    public function confirmPayment(Request $request) {
        $fee = Session::get('paypal_amount');
        $redirect_url = Helpers::PayWithPaypal($fee, $this->_api_context);
        return Redirect::away($redirect_url);
    }

    /**
     *
     * @param \App\Http\Controllers\Application\RegistrationFormRequest $request
     * @param \App\Http\Controllers\Application\StorageManagerInterface $storage
     * @return type
     */
    public function saveRightForm(RightFormRequest $request, StorageManagerInterface $storage) {

        try {

            $data = [];
            $apiLog = [];
            $thumbnail = $request->file('thumbnail');
            $rightId = $request->get('right_id') ? (int) $request->get('right_id') : null;
            $arrFileData = [];
            $arrInputData = [];
            $arrMemebership = [];
            $arrFileData = $request->all();
            $pprise = 0;

            if (@$arrFileData['tmp_number'] != '') {
                $arrFileData['number'] = $arrFileData['tmp_number'];
            } else {
                $arrFileData['number'] = str_replace(' ', '', Helpers::getRightTypeById((int) $arrFileData['type_id'])->title . $this->getRandomToken(6) . time());
            }


            $arrFileData['user_id'] = (int) Auth::user()->id;
            $rightAttchment = [];
            $attchmentFileAdd = [];



            $isEdit = 0;
            if ($rightId > 0) {
                $isEdit = 1;
            }

            $valfiles = $request->file("attachements");
//             $acceptArr = ['application/zip', 'application/x-rar-compressed', 'application/octet-stream','application/octet-stream', 'application/x-zip-compressed', 'multipart/x-zip'];
//             if (!in_array($valfiles[0]->getMimeType(), $acceptArr))
//             {
//                  return redirect()->back()->withInput(Input::all())->withErrors('Sorry! Please upload only zip file.');
//             }       
            //get radio button value..
            $execl = $request->get('exclusive');
            $ex_text_box = $request->get('num_of_month_' . $execl);

            //end radio button valye
            //get checbox value
            $rd = $request->get('is_exclusive_purchase');
//            if($rd == 1 ){
//                $arrFileData['is_exclusive_purchase'] = 1;
//                $arrFileData['is_non_exclusive_purchase'] = null;
//                $pprise = (int) $request->get('exclusive_purchase_price');
//            }else{
//                $arrFileData['is_non_exclusive_purchase'] = 1;
//                $arrFileData['is_exclusive_purchase'] = null;
//                $pprise = (int) $request->get('non_exclusive_purchase_price');
//            }

            $arrInputData['is_exclusive_purchase'] = $arrFileData['is_exclusive_purchase'] = (int) $request->get('is_exclusive_purchase');
            $arrInputData['is_non_exclusive_purchase'] = $arrFileData['is_non_exclusive_purchase'] = (int) $request->get('is_non_exclusive_purchase');
            $arrInputData['non_exclusive_purchase_price'] = $arrFileData['non_exclusive_purchase_price'] = $request->get('non_exclusive_purchase_price');
            $arrInputData['exclusive_purchase_price'] = $arrFileData['exclusive_purchase_price'] = $request->get('exclusive_purchase_price');


            //end checkbox value

            if ($request->get('date')) {
                $arrInputData['date'] = $arrFileData['date'] = Helpers::getDateByFormat($request->get('date'), 'm/d/Y', 'Y-m-d');
            }

            if ($request->get('expiry_date')) {
                $arrInputData['expiry_date'] = $arrFileData['expiry_date'] = Helpers::getDateByFormat($request->get('expiry_date'), 'm/d/Y', 'Y-m-d');
            }

            $arrFileData[] = $request->get('expiry_date');





            $message = trans('success_messages.right_posted_successfully');
            if ($request->get('draft') && !empty($request->get('draft'))) {
                $arrFileData['phase'] = 0;
                $message = trans('success_messages.right_saved_successfully');
            } else {
                $arrFileData['phase'] = 1;
            }

            if ($isEdit == 0) {// add right in tmp table
                $currentDate = date('Y-m-d');
                $isRedirectToPayPal = 1;

                if (Auth::user()->membership == '2' && Auth::user()->membership_expired != null && Auth::user()->membership_expired != '' && $currentDate <= Auth::user()->membership_expired) {
                    $isRedirectToPayPal = 0;
                }

                //if (Auth::user()->membership == '2' && $currentDate <= Auth::user()->membership_expired) {
                // $isRedirectToPayPal = 0;
                // }



                $arrInputData['title'] = $arrFileData['title'];
                $arrInputData['url'] = $arrFileData['url'];
                $arrInputData['inventor'] = $arrFileData['inventor'];
                $arrInputData['assignee'] = $arrFileData['assignee'];
                $arrInputData['description'] = $arrFileData['description'];
                $arrInputData['keywords'] = $arrFileData['keywords'];
                $arrInputData['type_id'] = $arrFileData['type_id'];
                $arrInputData['right_for_id'] = $arrFileData['right_for_id'];
                $arrInputData['cluster_id'] = $arrFileData['cluster_id'];
                $arrInputData['number'] = $arrFileData['number'];
                $arrInputData['user_id'] = $arrFileData['user_id'];
                $arrInputData['is_exclusive_purchase'] = $arrFileData['is_exclusive_purchase'];
                $arrInputData['is_non_exclusive_purchase'] = $arrFileData['is_non_exclusive_purchase'];
                $arrInputData['non_exclusive_purchase_price'] = $arrFileData['non_exclusive_purchase_price'];
                $arrInputData['exclusive_purchase_price'] = $arrFileData['exclusive_purchase_price'];
                $arrInputData['phase'] = $arrFileData['phase'];


                if ($execl == 1) {

                    $arrInputData['num_of_month'] = $arrFileData['num_of_month'] = NULL;
                    $arrMemebership['membership_expired'] = NULL;
                } else if ($execl == 2) {

                    $arrInputData['num_of_month'] = $arrFileData['num_of_month'] = $ex_text_box;
                    $membership_expired = date("Y-m-d", strtotime("+" . $ex_text_box . " months", strtotime(date('Y-m-d'))));
                    $arrMemebership['membership_expired'] = $membership_expired;
                }

                $arrMemebership['membership'] = $execl;
                $arrInputData['subscription'] = $execl;
                $arrFileData['subscription'] = $execl;

                // save data in temp table
                if ($isRedirectToPayPal == 1) {

                    if (count($thumbnail) > 0) {
                        $fileSize = $thumbnail->getClientSize();
                        $extension = $thumbnail->getClientOriginalExtension();

                        if ($fileSize < config('inv_common.RIGHTS_THUMBNAIL_MAX_SIZE')) {
                            $userBaseDir = 'appDocs/rightsThumbnailsTemp/' . Auth::user()->id;
                            $userFileName = rand(0, 9999) . time() . '.' . $extension;
                            $arrInputData['thumbnail'] = $arrFileData["thumbnail"] = $userFileName;

                            $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($thumbnail));

                            //resizee image
                            $userBaseDirApp = "app/" . $userBaseDir;
                            $ImageRealPath = storage_path() . DIRECTORY_SEPARATOR . $userBaseDirApp . DIRECTORY_SEPARATOR . $userFileName;
                            $destinationPath = storage_path() . DIRECTORY_SEPARATOR . $userBaseDirApp;
                            //echo $ImageRealPath; exit;
                            chmod($ImageRealPath, 777);
                            $img = Image::make($ImageRealPath);
                            $img2 = Image::make($ImageRealPath);
                            $newimage = "60X60_" . $userFileName;
                            $bignewimage = "715X135_" . $userFileName;
                            $img->resize(60, 60, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($destinationPath . DIRECTORY_SEPARATOR . $newimage);


                            $img2->resize(715, 135, function ($constraint) {

                                $constraint->aspectRatio();
                            })->save($destinationPath . DIRECTORY_SEPARATOR . $bignewimage);



                            //$destinationPath = public_path('/images');
                            //$image->move($destinationPath, $input['imagename']);
                            /////////
                            // Delete the temporary file
                            File::delete($thumbnail);
                        } else {
                            return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                        }
                    } else {
                        if (@$arrFileData['tmp_thumbnail'] != '') {
                            $arrInputData['thumbnail'] = $arrFileData["thumbnail"] = $arrFileData['tmp_thumbnail'];
                        }
                    }

                    //upload pics in storage....>
                    $files = $request->file("attachements");
                    if (count($files) > 0) {
                        for ($i = 0; $i < count($files); $i++) {
                            $userProfile = $files[$i];
                            $fileSize = $userProfile->getClientSize();
                            $extension = $userProfile->getClientOriginalExtension();
                            $orgFileName = $userProfile->getClientOriginalName();
                            if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                                $userBaseDir = 'appDocs/rightsAttachmentsTemp/' . Auth::user()->id . '/' . $arrInputData['number'];
                                $userFileName = rand(0, 9999) . time() . '.' . $extension;
                                array_push($rightAttchment, $userFileName);
                                array_push($attchmentFileAdd, $userBaseDir);
                                $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($userProfile));
                                // Delete the temporary file
                                //zip end here
                                File::delete($userProfile);
                            } else {
                                return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                            }
                        }
                    } else {
                        if (@$arrFileData['tmp_attachment'] != '') {
                            $arrInputData['attachment'] = $arrFileData["attachment"] = $arrFileData['tmp_attachment'];
                        }


                        if (@$arrFileData['tmp_attachment_file_name'] != '') {
                            $arrInputData['attachment_file_name'] = $arrFileData["attachment_file_name"] = $arrFileData['tmp_attachment_file_name'];
                        }
                    }


                    //-------------------------

                    if (count($rightAttchment) > 0) {
                        for ($j = 0; $j < count($rightAttchment); $j++) {
                            $arrInputData['attachment'] = $rightAttchment[$j];
                            $arrInputData['attachment_file_name'] = $orgFileName;
                        }
                    }

                    if (@$arrFileData["tmp_right_id"] != '') {

                        $result = $this->application->updateRightsTemp((int) @$arrFileData["tmp_right_id"], $arrInputData);
                    } else {
                        $result = $this->application->saveRightsTemp($arrInputData);
                    }

                    $tmp_right_id = $result;


                    $post = array(
                        'type' => (int) $arrFileData['type_id'] - 1,
                        'createdAt' => time(),
                        'subscriptionType' => $execl - 1, //for Select Subscription value due to blockchain api
                        'totalCost' => 12.10,
                        'exclusiveTermsCost' => (int) $request->get('exclusive_purchase_price'),
                        'exclusiveTermsDuration' => 12,
                        'nonExclusiveTermsCost' => $request->get('non_exclusive_purchase_price'),
                        'nonExclusiveTermsDuration' => 12,
                        'layerCost' => 5,
                        'tradingType' => 1,
                        'uniqueId' => Auth::user()->email,
                        'rightNumber' => $arrFileData['number'],
                        'reportTime' => time(),
                    );
                    Session::put('myPost', $post);
                    Session::put('myRightAttchment', $rightAttchment);
                    Session::put('myAttchmentFileAdd', $attchmentFileAdd);
                    Session::put('curruntRoute', \Request::route()->getName());
                    Session::put('myRightnumber', $arrFileData['number']);
                    Session::put('current_right_ids', $result);


                    // membership fee according to subscription

                    if ($arrInputData['subscription'] == 2) {

                        $fee = config('proin.premium_retailer_fee');
                        Session::put('paypal_amount', $arrInputData['num_of_month'] * $fee);
                    } else {
                        $fee = config('proin.free_holder_fee');
                        Session::put('paypal_amount', $fee);
                    }

                    $payData['fee'] = $fee;
                    $payData['payment_for'] = 'add_right';
                    $payData['subscription'] = $arrInputData['subscription'];
                    //confirm_payment 
                    return redirect(route('confirm_payment'));
                    // return view('frontend.confirm_payment', compact('payData'));
                    // $redirect_url = Helpers::PayWithPaypal($fee, $this->_api_context);
                    // return Redirect::away($redirect_url);
                } else {

                    if (count($thumbnail) > 0) {
                        $fileSize = $thumbnail->getClientSize();
                        $extension = $thumbnail->getClientOriginalExtension();

                        if ($fileSize < config('inv_common.RIGHTS_THUMBNAIL_MAX_SIZE')) {
                            $userBaseDir = 'appDocs/rightsThumbnails/' . Auth::user()->id;
                            $userFileName = rand(0, 9999) . time() . '.' . $extension;
                            $arrInputData['thumbnail'] = $arrFileData["thumbnail"] = $userFileName;

                            $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($thumbnail));

                            //resizee image
                            $userBaseDirApp = "app/" . $userBaseDir;
                            $ImageRealPath = storage_path() . DIRECTORY_SEPARATOR . $userBaseDirApp . DIRECTORY_SEPARATOR . $userFileName;
                            $destinationPath = storage_path() . DIRECTORY_SEPARATOR . $userBaseDirApp;
                            //echo $ImageRealPath; exit;
                            chmod($ImageRealPath, 777);
                            $img = Image::make($ImageRealPath);
                            $img2 = Image::make($ImageRealPath);
                            $newimage = "60X60_" . $userFileName;
                            $bignewimage = "715X135_" . $userFileName;
                            $img->resize(60, 60, function ($constraint) {
                                $constraint->aspectRatio();
                            })->save($destinationPath . DIRECTORY_SEPARATOR . $newimage);


                            $img2->resize(715, 135, function ($constraint) {

                                $constraint->aspectRatio();
                            })->save($destinationPath . DIRECTORY_SEPARATOR . $bignewimage);



                            //$destinationPath = public_path('/images');
                            //$image->move($destinationPath, $input['imagename']);
                            /////////
                            // Delete the temporary file
                            File::delete($thumbnail);
                        } else {
                            return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                        }
                    }

                    $result = $this->application->saveRights($arrFileData);

                    // event fire
                    $userMailArr = [];
                    $userMailArr['name'] = Auth::user()->first_name;
                    $userMailArr['lname'] = Auth::user()->last_name;
                    $userMailArr['email'] = Auth::user()->email;
                    if($arrFileData['non_exclusive_purchase_price']) {
                       $userMailArr['non_exclusive_purchase_price'] = '$' . $arrFileData['non_exclusive_purchase_price'];
                    } else {
                       $userMailArr['non_exclusive_purchase_price'] = '';
                    }
                     
                    if($arrFileData['exclusive_purchase_price']) {
                        $userMailArr['exclusive_purchase_price'] = '$' . $arrFileData['exclusive_purchase_price'];
                    } else {
                        $userMailArr['exclusive_purchase_price'] = '';
                    }
                    $userMailArr['title'] = $arrFileData['title'];
                    $userMailArr['createdAt'] = $arrFileData['date'];
                    Event::fire("right.add", serialize($userMailArr));

                    //upload rightDocs
                    //upload pics in storage....>
                    $files = $request->file("attachements");
                    if (count($files) > 0) {
                        for ($i = 0; $i < count($files); $i++) {
                            $userProfile = $files[$i];
                            $fileSize = $userProfile->getClientSize();
                            $extension = $userProfile->getClientOriginalExtension();
                            $orgFileName = $userProfile->getClientOriginalName();
                            if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                                $userBaseDir = 'appDocs/rightsAttachments/' . Auth::user()->id . '/' . $result;
                                $userFileName = rand(0, 9999) . time() . '.' . $extension;
                                array_push($rightAttchment, $userFileName);
                                array_push($attchmentFileAdd, $userBaseDir);
                                $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($userProfile));
                                // Delete the temporary file
                                //zip end here
                                File::delete($userProfile);
                            } else {
                                return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                            }
                        }
                    }

                    // save attachment
                    $rightIds = $result;
                    $dataArray = [];
                    if (count($rightAttchment) > 0) {
                        for ($j = 0; $j < count($rightAttchment); $j++) {
                            $dataArray['user_id'] = Auth::user()->id;
                            $dataArray['right_id'] = $rightIds;
                            $dataArray['attachment'] = $rightAttchment[$j];
                            $dataArray['attachment_file_name'] = $orgFileName;
                            //save data  table...
                            $result = $this->application->saveAttachmentRights($dataArray); 
                        }
                    }
                }
            } else {

                if (count($thumbnail) > 0) {
                    $fileSize = $thumbnail->getClientSize();
                    $extension = $thumbnail->getClientOriginalExtension();

                    if ($fileSize < config('inv_common.RIGHTS_THUMBNAIL_MAX_SIZE')) {
                        $userBaseDir = 'appDocs/rightsThumbnails/' . Auth::user()->id;
                        $userFileName = rand(0, 9999) . time() . '.' . $extension;
                        $arrFileData["thumbnail"] = $userFileName;

                        $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($thumbnail));

                        //resizee image
                        $userBaseDirApp = "app/" . $userBaseDir;
                        $ImageRealPath = storage_path() . DIRECTORY_SEPARATOR . $userBaseDirApp . DIRECTORY_SEPARATOR . $userFileName;
                        $destinationPath = storage_path() . DIRECTORY_SEPARATOR . $userBaseDirApp;
                        //echo $ImageRealPath; exit;
                        chmod($ImageRealPath, 777);
                        $img = Image::make($ImageRealPath);
                        $img2 = Image::make($ImageRealPath);
                        $newimage = "60X60_" . $userFileName;
                        $bignewimage = "715X135_" . $userFileName;
                        $img->resize(60, 60, function ($constraint) {
                            $constraint->aspectRatio();
                        })->save($destinationPath . DIRECTORY_SEPARATOR . $newimage);


                        $img2->resize(715, 135, function ($constraint) {

                            $constraint->aspectRatio();
                        })->save($destinationPath . DIRECTORY_SEPARATOR . $bignewimage);



                        //$destinationPath = public_path('/images');
                        //$image->move($destinationPath, $input['imagename']);
                        /////////
                        // Delete the temporary file
                        File::delete($thumbnail);
                    } else {
                        return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                    }
                }


                $result = $this->application->updateRights($rightId, $arrFileData);

                //upload rightDocs
                //upload pics in storage....>
                $files = $request->file("attachements");
                if (count($files) > 0) {
                    for ($i = 0; $i < count($files); $i++) {
                        $userProfile = $files[$i];
                        $fileSize = $userProfile->getClientSize();
                        $extension = $userProfile->getClientOriginalExtension();
                        $orgFileName = $userProfile->getClientOriginalName();
                        if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                            $userBaseDir = 'appDocs/rightsAttachments/' . Auth::user()->id . '/' . $result;
                            $userFileName = rand(0, 9999) . time() . '.' . $extension;
                            array_push($rightAttchment, $userFileName);
                            array_push($attchmentFileAdd, $userBaseDir);
                            $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($userProfile));
                            // Delete the temporary file
                            //zip end here
                            File::delete($userProfile);
                        } else {
                            return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                        }
                    }
                }

                $rightIds = $result;
                $dataArray = [];
                if (count($rightAttchment) > 0) {
                    for ($j = 0; $j < count($rightAttchment); $j++) {
                        $dataArray['user_id'] = Auth::user()->id;
                        $dataArray['right_id'] = $rightIds;
                        $dataArray['attachment'] = $rightAttchment[$j];
                        $dataArray['attachment_file_name'] = $orgFileName;
                        //save data  table...
                        $attachRight   = $this->application->getFirstAttachmentRight(Auth::user()->id, (int)$rightId);
                        if($rightId > 0) {
                            if($attachRight){
                                $this->application->updateAttachmentRights($attachRight->right_id, $dataArray);
                            }
                        } else {
                            $result = $this->application->saveAttachmentRights($dataArray);
                        }  
                    }
                }
            }



            if ($result) {
                Session::flash('message', $message);
                return redirect(route('front_dashboard'));
            } else {
                return redirect()->back()->withErrors(trans('auth.oops_something_went_wrong'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show add right form
     *
     * @return Response
     */
    public function listAllRights(Request $request) {
        try {
            if (!empty($request->get('recent_views'))) {
                Session::put('recent_viewed', $request->get('recent_views'));
            } else {
                Session::forget('recent_viewed');
            }
            return view('frontend.all_right');
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Show right details
     *
     * @return Response
     */
    public function rightDetails(Request $request) {
        try {

            $isZip = 0;
            $fileList = [];
            $onlyfile = [];
            $myFolder = null;
            $validFlag = 1;
            $userId = (int) $request->get('user_id');
            $rightId = (int) $request->get('right_id');

            $recentViewed = $this->application->getRecentViewedByUserId((int) Auth::user()->id);

            $rightIdsArr = [];
            if ($recentViewed) {
                $rightIdsArr = explode(',', $recentViewed->recent_right_ids);

                if (!in_array($rightId, $rightIdsArr)) {
                    array_unshift($rightIdsArr, $rightId);
                    $rightIdStr = implode(',', $rightIdsArr);
                    $strArr = [];
                    $strArr['recent_right_ids'] = $rightIdStr;
                    $this->application->updateRecentViewed($recentViewed->id, $strArr);
                }
            } else {
                $strArr = [];
                $strArr['recent_right_ids'] = $rightId . ",";
                $strArr['user_id'] = (int) Auth::user()->id;
                $strArr['created_by'] = (int) Auth::user()->id;
                $strArr['updated_by'] = (int) Auth::user()->id;

                $this->application->insertRecentViewed($strArr);
            }

            $rightDetail = $this->application->getRightDetail($userId, $rightId);


            $similarRights = $this->application->getSimilarRights($rightDetail->keywords, $rightDetail->title, $rightId,(int) Auth::user()->id);

            $similarRandomRights = $this->application->getSimilarRandomRights(5,(int) Auth::user()->id);
            //return view('frontend.add_right', compact('draftedRight','similarRights'));
            if (isset($similarRights) && count($similarRights) > 0) {
                
            } else {
                $similarRights = $similarRandomRights;
            }
            //dd($similarRights);
            $rightDetailAttachments = $this->application->getAttachmentRights($userId, $rightId);
            $at = 0;
            if (count($rightDetailAttachments) > 0) {
                $ex = explode('.', $rightDetailAttachments[0]->attachment);
                if ($ex[1] == 'zip' || $ex[1] == 'ZIP') {
                    //extract zip            
                    $rr = storage_path('app/appDocs/rightsAttachments/' . $userId . '/' . $rightId . '/' . $rightDetailAttachments[0]->attachment);
                    if (Zip::check($rr)) {
                        $isZip = 1;
                        $zip = Zip::open($rr);
                        //$zip->extract(storage_path('app/appDocs/rightsAttachments/'.$userId.'/'.$rightId.'/ExtractZipFiles'));
                    }
                    if ($isZip == 1) {
                        //$dirList = scandir(storage_path('app/appDocs/rightsAttachments/'.$userId.'/'.$rightId.'/ExtractZipFiles'));
                        //$myFolder = $dirList[2];
                        //$fileList = scandir(storage_path('app/appDocs/rightsAttachments/'.$userId.'/'.$rightId.'/ExtractZipFiles/'.$myFolder),1);
                    }
                    $at = $rightDetailAttachments[0]->attachment_file_name;
                    //end zip
                } else {
                    $onlyfile = $rightDetailAttachments;
                    $at = $rightDetailAttachments[0]->attachment_file_name;
                }
            } else {
                $onlyfile = $rightDetailAttachments;
                $at = $rightDetailAttachments[0]->attachment_file_name;
            }


            //voating on Right
            $validFlag = $this->getVotingOnRight($userId, $rightId);

            ///checek is shop close or not
            if ($validFlag == 0 && $rightDetail->ready_to_sale == 0) {
                $setReadyToSale = $this->setReadyTosale(Auth::user()->id, $rightId);
                $this->setRepotationScoreOfuser($rightId);
            }
            return view('frontend.right_details', compact('rightDetail', 'rightDetailAttachments', 'similarRights', 'isZip', 'fileList', 'onlyfile', 'myFolder', 'validFlag', 'rr', 'at'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Generate Random number For right
     * @return type
     */
    public function getRandomToken($length) {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet.= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }

        return $token;
    }

    /**
     * Popup for term an dcondition
     * @return type
     */
    public function termConditionPopup(Request $request) {
        try {
            $userId = $request->get('user_id');
            $rightId = $request->get('right_id');
            return view('framePopup.termConditionPopup')
                            ->with('userId', $userId)
                            ->with('rightId', $rightId);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Accept  term an dcondition
     * @return type
     */
    public function acceptCondition(Request $request) {
        try {
            $userId = $request->get('user_id');
            $rightId = $request->get('right_id');

            $arrFileData = [];
            $arrUserData = [];

            Session::put('paypal_amount', 1);
            Session::put('curruntRoute', \Request::route()->getName());
            Session::put('current_right_ids', $rightId);


            Session::flash('is_accept', 1);
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Popup for valid Claim right
     * 
     *  @return type
     */
    public function validClaimPopup(Request $request) {
        $userId = $request->get('user_id');
        $rightId = $request->get('right_id');
        return view('framePopup.validAndClaimPopup')
                        ->with('userId', $userId)
                        ->with('rightId', $rightId);
    }

    /**
     * Accept  valid and claim
     * @return type
     */
    public function postValidClaim(Request $request, StorageManagerInterface $storage) {
        try {
            $userId = $request->get('user_id'); //wsose right
            $logedUser_id = Auth::user()->id; //current user login
            $rightId = $request->get('right_id'); //right id
            $comment = $request->get('comment');
            $claim = $request->get('claim');
            $arrRightData = [];
            $arrClaimList = [];
            $arrChecklist = [];
            $claimAttchment = [];
            $attchmentFileAdd = [];
            $arr = [];

            if ($claim == 1) {
                if (is_array($request->get('check_nvl')) && is_array($request->get('check_fun'))) {
                    $arrChecklist = array_merge($request->get('check_nvl'), $request->get('check_fun'));
                } elseif (!is_array($request->get('check_nvl')) && is_array($request->get('check_fun'))) {
                    $arrChecklist = $request->get('check_fun');
                } elseif (is_array($request->get('check_nvl')) && !is_array($request->get('check_fun'))) {
                    $arrChecklist = $request->get('check_nvl');
                }
                $checkList = implode(', ', $arrChecklist);
            } else {
                if (is_array($request->get('check_in')) && is_array($request->get('check_in_f'))) {
                    $arrChecklist = array_merge($request->get('check_in'), $request->get('check_in_f'));
                } elseif (!is_array($request->get('check_in')) && is_array($request->get('check_in_f'))) {
                    $arrChecklist = $request->get('check_in_f');
                } elseif (is_array($request->get('check_in')) && !is_array($request->get('check_in_f'))) {
                    $arrChecklist = $request->get('check_in');
                }

                $checkList = implode(', ', $arrChecklist);
            }

            $totalCal = $this->getValidateCalculation($checkList);
            $getRightDetail = $this->application->find($rightId);
            $arrClaimList['right_owner'] = $userId;
            $arrClaimList['valid_by'] = $logedUser_id;
            $arrClaimList['right_id'] = $rightId;
            $arrClaimList['validate_checklist'] = $checkList;
            $arrClaimList['comment'] = $comment;
            if ($claim == 1) {
                $arrClaimList['valid_final_score'] = $totalCal;
                $arr['valid_scout_score'] = $getRightDetail->valid_scout_score + 1;
            } else {
                $arrClaimList['invalid_final_score'] = $totalCal;
                $arr['invalid_scout_score'] = $getRightDetail->invalid_scout_score + 1;
            }
            //save cliam
            $result = $this->application->saveClaim($arrClaimList);

            //upload pics in storage....
            $files = $request->file("validattachment");

            if (count($files) > 0) {
                for ($i = 0; $i < count($files); $i++) {
                    $userProfile = $files[$i];
                    $fileSize = $userProfile->getClientSize();
                    $extension = $userProfile->getClientOriginalExtension();
                    if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                        $userBaseDir = 'appDocs/claimAttachments/' . Auth::user()->id . '/' . $rightId;
                        $userFileName = rand(0, 9999) . time() . '.' . $extension;
                        array_push($claimAttchment, $userFileName);
                        array_push($attchmentFileAdd, $userBaseDir);
                        $ar['right_id'] = $rightId;
                        $ar['claim_id'] = $result;
                        $ar['claim_attacment'] = $userFileName;
                        $this->application->saveClaimAttachment($ar);
                        $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($userProfile));
                        File::delete($userProfile);
                    } else {
                        return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                    }
                }
            }

            $post = array(
                'rightAddress' => $getRightDetail->address,
                'response' => (int) $claim - 1,
                'reportTime' => time(),
                'uniqueId' => Auth::user()->email,
                'score' => $totalCal,
            );

            //call api

            $status = $this->claimOnRights($claimAttchment, $attchmentFileAdd, $post);
            $eew = json_decode($status);
            $apiLog['request'] = json_encode($post);
            $apiLog['responce'] = $status;
            $apiLog['source'] = 'Claim On Right';
            $apiLog['right_no'] = $getRightDetail->number;
            Helpers::saveApiLog($apiLog);
            if ($eew != null && !empty($eew->transaction_id)) {
                $this->application->updateRights($rightId, ['batch_address' => $eew->transaction_id]);
            }

            //update rights flag
            $arr['is_validate_by_scout'] = 1;
            if ($getRightDetail->first_validate_time == null) {
                $arr['first_validate_time'] = Carbon::now();
            }

            $updateRight = $this->application->updateRights($rightId, $arr);
            $updateRight = $this->application->updateStacklog(Auth::user()->id, $rightId, ['is_validate' => 1]);
            //ready to sale
            $setReadyToSale = $this->setReadyTosale(Auth::user()->id, $rightId);
            $this->setRepotationScoreOfuser($rightId);

            $userMailArr = [];
            $userMailArr['name'] = Auth::user()->first_name;
            $userMailArr['lname'] = Auth::user()->last_name;
            $userMailArr['email'] = Auth::user()->email;
            $userMailArr['title'] = $getRightDetail->title;
            $userMailArr['evaluated_by'] = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $userMailArr['createdAt'] = $getRightDetail->date;
            $userData1 = $this->userRepo->find((int) $userId);
            $userMailArr['email_owner'] = $userData1->email;
            Event::fire("right.evaluate", serialize($userMailArr));

            Session::flash('is_accept', 1);
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Calculation valid and claim
     * @return type
     */
    public function getValidateCalculation($checkList) {
        try {
            $novelityClaim = 0;
            $functionlityClaim = 0;
            $checklist = explode(',', $checkList);
            $novelity = Helpers::getTypeWiseList(1)->toArray();
            $functionlity = Helpers::getTypeWiseList(2)->toArray();

            for ($i = 0; $i < count($checklist); $i++) {
                if (in_array($checklist[$i], $novelity)) {
                    $novelityClaim++;
                }
                if (in_array($checklist[$i], $functionlity)) {
                    $functionlityClaim++;
                }
            }

            $novelityCalculation = (config('inv_common.NOVELTY_CAL_VAL') * ($novelityClaim / count($novelity)));
            $functionlityCalculation = (config('inv_common.FUNCTIONLITY_CAL_VAL') * ($functionlityClaim / count($functionlity)));
            $totalCal = $novelityCalculation + $functionlityCalculation;

            return $totalCal;
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Calculation voting of rights
     * @return type
     */
    public function getVotingOnRight($userId, $rightId) {
        $getRightDetail = $this->application->find($rightId);
        if ($getRightDetail->is_validate_by_scout == 1) {
            $totalCount = (int) $getRightDetail->valid_scout_score + (int) $getRightDetail->invalid_scout_score;
            $scoutValidateTime = $getRightDetail->first_validate_time;

            $getClmRights = $this->application->getClaimByID($rightId)->toArray();



            if(count($getClmRights) > 4){
                if(count($getClmRights) > 4 && count($getClmRights) == 5 && $getRightDetail->is_first_validate == 0) {
                    $this->application->updateRights($rightId, ['first_validate_time' => Carbon::now(),'is_first_validate' => 1]);
                } 

                $scoutValidateTime = $getRightDetail->first_validate_time;
                $to = Carbon::parse($scoutValidateTime);
                $from = Carbon::now();
                // dd($from, $to, $scoutValidateTime,$rightId,$getRightDetail);
                $diff_in_minutes = $to->diffInHours($from);

                // dd($diff_in_minutes);
                //$diff_in_minutes = $to->diffInMinutes($from);
                if ($totalCount == 9 || $diff_in_minutes > 24) {
                    $validFlag = 0;
                    return $validFlag;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        } else {
            return 1;
        }
    }

    /**
     * Popup for term an buyRightPopup
     * @return type
     */
    public function buyRightPopup(Request $request) {
        try {
            $rightId = $request->get('right_id');
            $userData = $this->userRepo->find((int) Auth::user()->id);
            $rightDetail = $this->application->getRightByRightID($rightId);
            $holderData = $this->userRepo->find((int) $rightDetail->user_id);
            $now = Carbon::now();
            return view('framePopup.purchageRightsPopup')
                            ->with('rightId', $rightId)
                            ->with('now', $now)
                            ->with('rightDetail', $rightDetail)
                            ->with('holderData', $holderData)
                            ->with('userData', $userData);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Popup for term an buyRightPopup
     * @return type
     */
    public function savebuyRightPopup(Request $request) {
        try {
            $rightId = $request->get('right_id');
            $buyforyear = $request->get('buyforyear');
            $updatedprice = $request->get('updatedprice');
            $purchase_type = $request->get('purchase_type');

            $rightDetail = $this->application->getRightByRightID($rightId);

            $userId = $rightDetail->user_id;

            //------------- save data in temp----------------
            $purchaseBy = (int) Auth::user()->id;
            $createdBy = (int) $rightDetail->user_id;
            $expireDate = date('Y-m-d', strtotime('+' . $buyforyear . ' years'));

            $rightPrice = $updatedprice;
            $orderDetailTmp = [];
            $orderDetailTmp['right_id'] = $rightId;
            $orderDetailTmp['right_created_by'] = $createdBy;
            $orderDetailTmp['user_id'] = $purchaseBy;
            $orderDetailTmp['payment_method'] = "paypal";
            $orderDetailTmp['right_price'] = $rightPrice;
            $orderDetailTmp['trasation_id'] = "ABCDEFGH2345abnFGH";
            $orderDetailTmp['no_of_year'] = $buyforyear;
            $orderDetailTmp['expire_date'] = $expireDate;
            $orderDetailTmp['purchase_type'] = $purchase_type;
            $result = $this->application->savePurchaseRightTemp($orderDetailTmp);

            if ($result) {
                $postApi = array(
                    'uniqueId' => Auth::user()->email,
                    'rightAddress' => $rightDetail->address,
                    'cost' => $rightPrice,
                    'layersPurchased' => '2',
                    'buyerAddress' => Auth::user()->address,
                    'reportTime' => time(),
                    'purchaseType' => 1,
                    'purchasePeriod' => 24
                );

                Session::put('myPost', $postApi);
                Session::put('curruntRoute', \Request::route()->getName());
                Session::put('current_right_ids', $rightId);
                Session::put('purchege_right_id', $result);
                Session::put('buyforyear', $buyforyear);
                Session::put('purchase_type', $purchase_type);
                Session::put('ownership_expiry', $expireDate);
                Session::put('paypal_amount', $rightPrice);
                Session::put('payoutmode', 'payoutmode');
                Session::put('payoutmode', 'payoutmode');
                Session::flash('is_accept', 1);
                return redirect()->back();
            }

            //----------- end ------------------------------
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Get prise on right
     * @param type $rightDetail
     * @return $rightPrice
     */
    function getPriceOfRight($rightDetail) {
        $rightPrice = '';
        if ($rightDetail->is_exclusive_purchase == 1) {
            $rightPrice = $rightDetail->exclusive_purchase_price;
        } else if ($rightDetail->is_non_exclusive_purchase == 1) {
            $rightPrice = $rightDetail->non_exclusive_purchase_price;
        }

        return $rightPrice;
    }

    /**
     * Send mail on purchage right
     * @param type $rightDetail
     * @param type $BuyerEmail
     * @param type $BuyerName
     * @param type $rightPrice
     */
    function onPurchaseRightMail($rightDetail, $BuyerEmail, $BuyerName, $rightPrice) {
        //$user = unserialize($user);
        //dd($userData);

        $orderDate = date('Y-M-D : H-i-s');
        $totalPrice = $rightPrice;
        $rightImage = $rightDetail->thumbnail;
        $rightTitle = $rightDetail->title;
        $baseUrl = url('/');
        $Logopath = $baseUrl . "/frontend/inside/images/logo.png";
        $siteUrl = $baseUrl;



        $storagePath = $baseUrl . "/storage/app/appDocs/rightsThumbnails/" . $rightDetail->user_id . "/" . "60X60_" . $rightImage;
        //echo $storagePath2; exit;

        if (!file_exists(storage_path("app/appDocs/rightsThumbnails/" . $rightDetail->user_id . "/" . "60X60_" . $rightImage))) {
            $thumbImage = '<img src="' . asset('/frontend/inside/images/home/thumb-1.png') . '">';
        } else {
            $thumbImage = '<img src="' . $storagePath . '" width="60">';
        }


        //Send mail to User
        $email_content = EmailTemplate::getEmailTemplate("PURCHASE_RIGHS");
        if ($email_content) {
            $mail_body = str_replace(
                    ['%inventrustUrl', '%inventrustLogo', '%orderDate', '%totalPrice', '%rightImage', '%rightTitle', '%rightPrice'], [$siteUrl, $Logopath, $orderDate, $totalPrice, $thumbImage, $rightTitle, $rightPrice], $email_content->message
            );

            // echo $toEmail = $userData->email; exit;
            // "Table Name :==".inv_right_order_detail
            Mail::send('email', ['varContent' => $mail_body,
                    ], function ($message) use ($BuyerEmail, $email_content) {
                $message->from(config('common.FRONTEND_FROM_EMAIL'), config('common.FRONTEND_FROM_EMAIL_NAME'));
                //$message->to($BuyerEmail, $BuyerName)->subject($email_content->subject);
                if ($message->to('amitsuman@prosofo.co', "amit")->subject($email_content->subject)) {
                    mail('amitsuman@prosofo.co', 'mail success', 'This is a success mail');
                } else {
                    mail('amitsuman@prosofo.co', 'fail mail', 'This is a fail  mail');
                }
            });
        }
    }

    /**
     * Calculation voting of right
     * @return type
     */
    public function setReadyTosale($userId, $rightId) {
        $isReadyToSale = 0;
        $getRightDetail = $this->application->find($rightId);
        $validScore = $getRightDetail->valid_scout_score;
        $inValidScore = $getRightDetail->invalid_scout_score;
        $isSet = $this->getVotingOnRight($userId, $rightId);
        if ($isSet == 0) {
            if ($validScore >= $inValidScore) {
                $updateRight = $this->application->updateRights($rightId, ['ready_to_sale' => 1]);
            } else {
                $updateRight = $this->application->updateRights($rightId, ['ready_to_sale' => 2]);
            }
        }
        return true;
    }

    /**
     * Set repotation Score of user
     * @return type
     */
    public function setRepotationScoreOfuser($rightId) {
        try {

            $rightData = $this->application->find($rightId);
            $ready_to_sale = $rightData->ready_to_sale;
            $newScore = 0;
            $getClmRights = $this->application->getClaimByID($rightId)->toArray();
            if (count($getClmRights > 0)) {
                if ($ready_to_sale == 1) {
                    foreach ($getClmRights as $right) {
                        $newScore = 0;
                        $userDetail = $this->userRepo->find((int) $right['valid_by']);
                        $currentScore = $userDetail->repotation_score;
                        if (!empty($right['valid_final_score'])) {
                            $newScore = $currentScore + $right['valid_final_score'];
                            $this->userRepo->updateUser(['repotation_score' => $newScore], (int) $right['valid_by']);
                        } else {
                            $newScore = $currentScore - $right['invalid_final_score'];
                            $this->userRepo->updateUser(['repotation_score' => $newScore], (int) $right['valid_by']);
                        }
                    }
                }
                if ($ready_to_sale == 2) {
                    foreach ($getClmRights as $right) {
                        $newScore = 0;
                        $userDetail = $this->userRepo->find((int) $right['valid_by']);
                        $currentScore = $userDetail->repotation_score;
                        if (!empty($right['invalid_final_score'])) {
                            $newScore = $currentScore + $right['invalid_final_score'];
                            $this->userRepo->updateUser(['repotation_score' => $newScore], (int) $right['valid_by']);
                        } else {
                            $newScore = $currentScore - $right['valid_final_score'];
                            $this->userRepo->updateUser(['repotation_score' => $newScore], (int) $right['valid_by']);
                        }
                    }
                }
            }
        } catch (Exception $ex) {
            dd($ex);
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * Calculate Commitsssion
     * 
     * @param type $result
     * @param type $rightId
     * @param type $rightPrice
     */
    function calculateCommission($result, $rightId, $rightPrice) {

        $rightvalidUserIds = $this->application->getRightCommissiontoScoutID($rightId);
        $commition_array = [];
        $totalScout = count($rightvalidUserIds);
        if($totalScout) {
        $tostoreComm = (($rightPrice * config('inv_common.RIGHT_SELL_COMMISSION_TO_STORE')) / 100);
        $toAdminComm = (($rightPrice * config('inv_common.RIGHT_SELL_COMMISSION_TO_SCOUT')) / 100);
        $toholderComm = (($rightPrice * config('inv_common.RIGHT_SELL_COMMISSION_TO_RIGHTHOLDER')) / 100);
        /////////
        $BountyFromStore = (($tostoreComm * 0.3) / 100);
        $BountyFromAdmin = (($toAdminComm * 0.3) / 100);
        $BountyFromHolder = (($toholderComm * 2.4) / 100);
        $totalBounty = ($BountyFromStore + $BountyFromAdmin + $BountyFromHolder);

        $tostoreFinal = ($tostoreComm - $BountyFromStore);
        $toAdminFinal = ($toAdminComm - $BountyFromAdmin);
        $toholderFinal = ($toholderComm - $BountyFromHolder);

        $commission['purchase_id'] = $result;
        $commission['right_id'] = $rightId;
        $commission['to_store'] = $tostoreFinal;
        $commission['to_scout'] = $toAdminFinal;
        $commission['to_right_holder'] = $toholderFinal;
        $commission['to_right_bounty_holder'] = $totalBounty;
        $this->application->saveRightCommission($commission);


//////////Scout Commition

        
            $scoutCommition = $toAdminFinal / $totalScout;
            $i = 0;
            foreach ($rightvalidUserIds as $rightvalidUserId) {

                $email = $rightvalidUserId->email;
                $scoutID = $rightvalidUserId->valid_by;

                $commissionScout['purchase_id'] = $result;
                $commissionScout['right_id'] = $rightId;
                $commissionScout['scout_id'] = $scoutID;
                $commissionScout['to_scout'] = $scoutCommition;

                $payoutArray[$i]['email'] = $email;
                $payoutArray[$i]['valid_by'] = $scoutID;
                $payoutArray[$i]['purchase_id'] = $result;
                $payoutArray[$i]['right_id'] = $rightId;
                $payoutArray[$i]['to_scout'] = $scoutCommition;

                $commitionID = $this->application->saveRightCommissionScout($commissionScout);
                
                // event fire
                /*$userScoutData = $this->userRepo->getUserDetail((int) $scoutID);
                $getRightDetail = $this->application->find($rightId);
                $rightMailArr = [];
                $rightMailArr['scout_name'] = $userScoutData->first_name;
                $rightMailArr['scout_lname'] = $userScoutData->last_name;
                $rightMailArr['scout_email'] = $userScoutData->email;
                $rightMailArr['scout_commission'] = $scoutCommition;
                $rightMailArr['title'] = $getRightDetail->number;
                Event::fire("right.commission_mail", serialize($rightMailArr));
                 */
                
                $payoutArray[$i]['sender_item_id'] = $commitionID;
                $i++;
            }
            //Send Payout

            $payout = "payout_" . date('Y-m-d-h-i-s') . "_" . $rightId . "_" . $result;
            $emailSubject = "You have a payout!";
            $emailMessage = "You have received a payout! Thanks for using our service!";
            $sender_batch_header['sender_batch_id'] = $payout;
            $sender_batch_header['email_subject'] = $emailSubject;
            $sender_batch_header['email_message'] = $emailMessage;

            $clientId = config('inv_common.PAYPAL_CLIENT_ID');
            $secret = config('inv_common.PAYPAL_SECRET');

            //    echo $clientId."<br>".$secret; exit;
            $getPaypalAuth = $this->getPaypalAuth($clientId, $secret);
            $requestRespoces = $this->saveRightCommissionScoutPaypal($payoutArray, $sender_batch_header, $getPaypalAuth);

            $request = $requestRespoces['postData'];
            $respoce = json_encode($requestRespoces['resData']);

            $payoutLog['right_id'] = $rightId;
            $payoutLog['request'] = $request;
            $payoutLog['responce'] = json_encode($respoce);

            $this->application->saveRightCommissionPayout($payoutLog);
        }

        ////////Scout Commition
        // get valid score userID
        //$this->application->saveRightCommissiontoScout($commission);
    }

    /**
     * Pay With Paypall
     * 
     * @param Request $request
     * @return type
     */
    public function getPaymentStatus() {
        //dd(Session::all(), Input::get('PayerID'));
        $payPalArr = [];
        $parr = [];
        $ritArr = [];
        $myPost['cost'] = 0;
        $currentRightId = !empty(Session::get('current_right_ids')) ? Session::get('current_right_ids') : '';
        $myPost = !empty(Session::get('myPost')) ? Session::get('myPost') : '';

        $myRightAttchment = !empty(Session::get('myRightAttchment')) ? Session::get('myRightAttchment') : '';
        $myAttchmentFileAdd = !empty(Session::get('myAttchmentFileAdd')) ? Session::get('myAttchmentFileAdd') : '';
        $curruntRoute = !empty(Session::get('curruntRoute')) ? Session::get('curruntRoute') : '';
        $myRightnumber = !empty(Session::get('myRightnumber')) ? Session::get('myRightnumber') : '';
        $myMembership = !empty(Session::get('myMembership')) ? Session::get('myMembership') : '';
        $myMembershipExpired = !empty(Session::get('myMembershipExpired')) ? Session::get('myMembershipExpired') : '';


        $purchageRightId = !empty(Session::get('purchege_right_id')) ? Session::get('purchege_right_id') : '';
        $reportRightId = !empty(Session::get('report_right_ids')) ? Session::get('report_right_ids') : '';

        $payoutmode = !empty(Session::get('payoutmode')) ? Session::get('payoutmode') : '';

        Session::forget('current_right_ids');
        Session::forget('myPost');
        Session::forget('myRightAttchment');
        Session::forget('myAttchmentFileAdd');
        Session::forget('curruntRoute');
        Session::forget('myRightnumber');
        Session::forget('payoutmode');

        $payPalArr['paypal_payment_id'] = !empty(Session::get('paypal_payment_id')) ? Session::get('paypal_payment_id') : null;
        $payPalArr['paymentId'] = !empty(Input::get('paymentId')) ? Input::get('paymentId') : null;
        $payPalArr['token'] = !empty(Input::get('token')) ? Input::get('token') : null;
        $payPalArr['PayerID'] = !empty(Input::get('PayerID')) ? Input::get('PayerID') : null;
        $responceJson = json_encode($payPalArr);



        $getRightDetail = $this->application->find($currentRightId);
        $ritArr['right_id'] = $currentRightId;
        $ritArr['purchage_by'] = (int) Auth::user()->id;

        $ritArr['amount'] = Session::get('paypal_amount');


        $requestJson = json_encode($ritArr);

        $parr['request'] = $requestJson;
        $parr['responce'] = $responceJson;


       

        //----temp

        /** Get the payment ID before session clear * */
        $payment_id = Session::get('paypal_payment_id');
        $payment_id = Input::get('paymentId');

        /** clear the session payment ID * */
        Session::forget('paypal_payment_id');
        if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {
            Session::flash('error', 'Payment failed');
            if ($curruntRoute == 'add_right') {

                $tmpRight = $this->application->getTempRightByNumber($myRightnumber);
                $rightData2 = [];
                if ($tmpRight) {
                    $rightData2['number'] = $tmpRight->number;
                    $rightData2['user_id'] = $tmpRight->user_id;
                    $rightData2['title'] = $tmpRight->title;
                    $rightData2['type_id'] = $tmpRight->type_id;
                    $rightData2['thumbnail'] = $tmpRight->thumbnail;
                    $rightData2['inventor'] = $tmpRight->inventor;
                    $rightData2['assignee'] = $tmpRight->assignee;
                    $rightData2['cluster_id'] = $tmpRight->cluster_id;
                    $rightData2['date'] = $tmpRight->date;
                    $rightData2['expiry_date'] = $tmpRight->expiry_date;
                    $rightData2['right_for_id'] = $tmpRight->right_for_id;
                    $rightData2['url'] = $tmpRight->url;
                    $rightData2['description'] = $tmpRight->description;
                    $rightData2['keywords'] = $tmpRight->keywords;
                    $rightData2['is_exclusive_purchase'] = $tmpRight->is_exclusive_purchase;
                    $rightData2['is_non_exclusive_purchase'] = $tmpRight->is_non_exclusive_purchase;
                    $rightData2['exclusive_purchase_price'] = $tmpRight->exclusive_purchase_price;
                    $rightData2['non_exclusive_purchase_price'] = $tmpRight->non_exclusive_purchase_price;
                    $rightData2['subscription'] = $tmpRight->subscription;
                    $rightData2['num_of_month'] = $tmpRight->num_of_month;
                    $rightData2['phase'] = $tmpRight->phase;
                    $rightData2['attachment'] = $tmpRight->attachment;
                    $rightData2['attachment_file_name'] = $tmpRight->attachment_file_name;
                    $rightData2['tmp_right_id'] = $tmpRight->id;
                }
                Session::put('rightData', $rightData2);
                return Redirect::route('add_right');
            } else if ($curruntRoute == 'save_buy_right_popup') {
                $tmpPurchaseRight1 = $this->application->getTempPurchaseRightById((int) $purchageRightId);
                if ($tmpPurchaseRight1) {


                    $right_id = $tmpPurchaseRight1->right_id;
                    $user_id = $tmpPurchaseRight1->right_created_by;

                    $this->application->deleteTempPurchaseRightByID((int) $purchageRightId);

                    return Redirect::route('right_details', ['user_id' => $user_id, 'right_id' => $right_id]);
                }
            } else {
                return Redirect::route('front_dashboard');
            }
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {
            $status = 'No BlocKchain Api';
            //call block chain api on add right=>ok
            if($curruntRoute == 'add_right') {


                //--------Add Right after success payment---------
                $tmpRight = $this->application->getTempRightByNumber($myRightnumber);
                $rightData = [];
                if ($tmpRight) {
                    $rightData['number'] = $tmpRight->number;
                    $rightData['user_id'] = $tmpRight->user_id;
                    $rightData['title'] = $tmpRight->title;
                    $rightData['type_id'] = $tmpRight->type_id;
                    $rightData['thumbnail'] = $tmpRight->thumbnail;
                    $rightData['inventor'] = $tmpRight->inventor;
                    $rightData['assignee'] = $tmpRight->assignee;
                    $rightData['cluster_id'] = $tmpRight->cluster_id;
                    $rightData['date'] = $tmpRight->date;
                    $rightData['expiry_date'] = $tmpRight->expiry_date;
                    $rightData['right_for_id'] = $tmpRight->right_for_id;
                    $rightData['url'] = $tmpRight->url;
                    $rightData['description'] = $tmpRight->description;
                    $rightData['keywords'] = $tmpRight->keywords;
                    $rightData['is_exclusive_purchase'] = $tmpRight->is_exclusive_purchase;
                    $rightData['is_non_exclusive_purchase'] = $tmpRight->is_non_exclusive_purchase;
                    $rightData['exclusive_purchase_price'] = $tmpRight->exclusive_purchase_price;
                    $rightData['non_exclusive_purchase_price'] = $tmpRight->non_exclusive_purchase_price;
                    $rightData['subscription'] = $tmpRight->subscription;
                    $rightData['num_of_month'] = $tmpRight->num_of_month;
                    $rightData['phase'] = $tmpRight->phase;


                    // save rigth data
                    $result = $this->application->saveRights($rightData);

                    // save attachment
                    $rightIds = $result;
                    $dataArray['user_id'] = Auth::user()->id;
                    $dataArray['right_id'] = $rightIds;
                    $dataArray['attachment'] = $tmpRight->attachment;
                    $dataArray['attachment_file_name'] = $tmpRight->attachment_file_name;
                    //save data  table...
                    $result = $this->application->saveAttachmentRights($dataArray);

                    //shift
                    if ($tmpRight->thumbnail != '' && $tmpRight->thumbnail != null) {
                        $newimage = "60X60_" . $tmpRight->thumbnail;
                        $bignewimage = "715X135_" . $tmpRight->thumbnail;
                        $thumBaseDir = 'appDocs/rightsThumbnails/' . Auth::user()->id;
                        $thumTempDir = 'appDocs/rightsThumbnailsTemp/' . Auth::user()->id;

                        Storage::move($thumTempDir . DIRECTORY_SEPARATOR . $newimage, $thumBaseDir . DIRECTORY_SEPARATOR . $newimage);
                        Storage::move($thumTempDir . DIRECTORY_SEPARATOR . $bignewimage, $thumBaseDir . DIRECTORY_SEPARATOR . $bignewimage);
                        Storage::move($thumTempDir . DIRECTORY_SEPARATOR . $tmpRight->thumbnail, $thumBaseDir . DIRECTORY_SEPARATOR . $tmpRight->thumbnail);
                    }

                    // shift right doc temp to right folder
                    if ($tmpRight->attachment != '' && $tmpRight->attachment != null) {
                        $userBaseDir = 'appDocs/rightsAttachments/' . Auth::user()->id . '/' . $rightIds;

                        $sorceFile = 'appDocs/rightsAttachmentsTemp/' . Auth::user()->id . '/' . $tmpRight->number . DIRECTORY_SEPARATOR . $tmpRight->attachment;

                        Storage::makeDirectory($userBaseDir);

                        Storage::move($sorceFile, $userBaseDir . DIRECTORY_SEPARATOR . $tmpRight->attachment);
                    }



                    if ($rightData['subscription'] == '2' && $rightData['num_of_month'] != '' && $rightData['num_of_month'] != null && $rightData['num_of_month'] > 0) {
                        $month = (int) $rightData['num_of_month'];
                        $arrMemebership['membership'] = '2';
                        $arrMemebership['membership_expired'] = date("Y-m-d", strtotime("+" . $month . " months", strtotime(date('Y-m-d'))));

                        $this->application->updateMembership((int) Auth::user()->id, $arrMemebership);
                    }

                    // delete rigjt's temp data

                    $tmp = $this->application->deleteTempRightByUserId(Auth::user()->id);
                    Storage::deleteDirectory('appDocs/rightsAttachmentsTemp/' . Auth::user()->id);
                    Storage::deleteDirectory('appDocs/rightsThumbnailsTemp/' . Auth::user()->id);

                    // event fire
                    $userMailArr = [];
                    $userMailArr['name'] = Auth::user()->first_name;
                    $userMailArr['lname'] = Auth::user()->last_name;
                    $userMailArr['email'] = Auth::user()->email;
                    if($rightData['non_exclusive_purchase_price']) {
                        $userMailArr['non_exclusive_purchase_price'] = '$' . $rightData['non_exclusive_purchase_price'];
                    } else {
                        $userMailArr['non_exclusive_purchase_price'] = '';
                    }
                    if($rightData['exclusive_purchase_price']) {
                        $userMailArr['exclusive_purchase_price'] = '$' . $rightData['exclusive_purchase_price'];
                    } else {
                        $userMailArr['exclusive_purchase_price'] = '';
                    }
                    
                    $userMailArr['title'] = $rightData['title'];
                    $userMailArr['createdAt'] = $rightData['date'];
                    Event::fire("right.add", serialize($userMailArr));
                }

                //------------------------------------------------ 
                // save paypal log
                $responseTrans = $this->getPaypalPaymentDetail($payPalArr['paymentId']);
                if (@$responseTrans['name'] == "INVALID_RESOURCE_ID") {
                    $parr['paypal_response'] = '';
                } else {
                    
                    $parr['paypal_response'] = json_encode($responseTrans);
                }

                $parr['payment_by'] = (int) Auth::user()->id;
                $parr['txns_type'] = '1';
                $parr['right_id']=(int)$rightIds;
                
                $paypal_log_id = $this->application->savePaypalResponce($parr);


                $status = $this->onAddRights($tmpRight->attachment, $userBaseDir, $myPost);
                $eer = json_decode($status);
                $apiLog['request'] = json_encode($myPost);
                $apiLog['responce'] = $status;
                $apiLog['source'] = 'onTime add Right';
                $apiLog['paypal_log_id'] = $paypal_log_id;
                $apiLog['right_no'] = $myRightnumber;
                Helpers::saveApiLog($apiLog);
                if ($eer != null && !empty($eer->transaction_id)) {
                    $result = $this->application->updateRights($rightIds, ['batch_address' => $eer->transaction_id]);
                }
                if ($rightIds) {
                    $result = $this->application->updateRights($rightIds, ['is_payment' => 1]);
                }
            }
            //call block chain api on report right==>ok
            else if ($curruntRoute == 'save_report') {
                if (!empty($myRightAttchment)) {
                    $status = $this->reportOnRights($myRightAttchment[0], $myAttchmentFileAdd[0], $myPost);
                } else {
                    $myRightAttchment = [];
                    $myAttchmentFileAdd = [];
                    $status = $this->reportOnRights($myRightAttchment, $myAttchmentFileAdd, $myPost);
                }
                $eer = json_decode($status);

                // save paypal log
                $responseTrans = $this->getPaypalPaymentDetail($payPalArr['paymentId']);
                if (@$responseTrans['name'] == "INVALID_RESOURCE_ID") {
                    $parr['paypal_response'] = '';
                } else {
                    
                    $parr['paypal_response'] = json_encode($responseTrans);
                }

                $parr['payment_by'] = (int) Auth::user()->id;
                $parr['txns_type'] = '4';
                $parr['right_id'] = (int)$currentRightId;
                $paypal_log_id = $this->application->savePaypalResponce($parr);

                $apiLog['request'] = json_encode($myPost);
                $apiLog['responce'] = $status;
                $apiLog['source'] = 'onTime Report a Right';
                $apiLog['paypal_log_id'] = $paypal_log_id;
                $apiLog['right_no'] = $getRightDetail->number;
                Helpers::saveApiLog($apiLog);
                if ($eer != null && !empty($eer->transaction_id)) {
                    $result = $this->application->updateReportById($reportRightId, ['transaction_id' => $eer->transaction_id]);
                }
                if ($reportRightId) {
                    $result = $this->application->updateReportById($reportRightId, ['is_payment' => 1]);
                }
            }

            //call block chain api on buyRigtht-->ok
            else if ($curruntRoute == 'save_buy_right_popup') {

                $purchase_type = Session::get('purchase_type');
                $ownership_expiry = Session::get('ownership_expiry');
                //-------------------------------------
                $status = $this->onBuyRights($myPost);
                $eer = json_decode($status);

                $tmpPurchaseRight = $this->application->getTempPurchaseRightById((int) $purchageRightId);
                if ($tmpPurchaseRight) {
                    // save purchase right
                    $arrPaymentList = [];
                    $currentRightId = $tmpPurchaseRight->right_id;
                    $arrPaymentList['right_id'] = $tmpPurchaseRight->right_id;
                    $arrPaymentList['right_created_by'] = $tmpPurchaseRight->right_created_by;
                    $arrPaymentList['user_id'] = $tmpPurchaseRight->user_id;
                    $arrPaymentList['payment_method'] = $tmpPurchaseRight->payment_method;
                    $arrPaymentList['right_price'] = $tmpPurchaseRight->right_price;
                    $arrPaymentList['trasation_id'] = (@$eer->transaction_id) ? $eer->transaction_id : '';
                    $arrPaymentList['is_payment_success'] = 1;

                    $result = $this->application->savePurchaseRight($arrPaymentList);

                    // save order detail
                    $purchase_id = $result;
                    if ($result) {
                        $orderDetail = [];
                        $commission = [];

                        $orderDetail['no_of_year'] = $tmpPurchaseRight->no_of_year;
                        $orderDetail['expire_date'] = $tmpPurchaseRight->expire_date;
                        $orderDetail['purchase_id'] = $purchase_id;
                        $orderDetail['purchase_type'] = $tmpPurchaseRight->purchase_type;



                        $this->application->savePurchaseOrder($orderDetail);

                        if ($purchase_type == '1') {
                            $result11 = $this->application->updateRights($currentRightId, ['is_exclusive_ownership' => '1', 'ownership_expiry' => $ownership_expiry]);
                        }

                        $this->application->deleteTempPurchaseRightByID((int) $purchageRightId);

                        Session::forget('purchase_type');
                        Session::forget('ownership_expiry');


                         if ($payPalArr['paymentId'] != null && $payoutmode == 'payoutmode') {
                            $rightPrice = Session::get('paypal_amount');
                            //  echo $purchageRightId."===>".$currentRightId."====".$rightPrice;
                            //$this->calculateCommission($result, $currentRightId, $rightPrice);
                            //$this->calculateCommission($purchase_id, $currentRightId, $rightPrice);
                            $this->calculateCommission($purchase_id, $currentRightId, $tmpPurchaseRight->right_price);
                            $payoutmode = "";
                        }




                        // save paypal log
                        $responseTrans = $this->getPaypalPaymentDetail($payPalArr['paymentId']);
                        if (@$responseTrans['name'] == "INVALID_RESOURCE_ID") {
                            $parr['paypal_response'] = '';
                        } else {

                            $parr['paypal_response'] = json_encode($responseTrans);
                        }

                        $parr['payment_by'] = (int) Auth::user()->id;
                        $parr['txns_type'] = '2';
                        $parr['right_id'] = (int)$currentRightId;
                        $paypal_log_id = $this->application->savePaypalResponce($parr);

                        $apiLog['request'] = json_encode($myPost);
                        $apiLog['responce'] = $status;
                        $apiLog['source'] = 'onTime Buy Right';
                        $apiLog['paypal_log_id'] = $paypal_log_id;
                        $apiLog['right_no'] = $getRightDetail->number;
                        Helpers::saveApiLog($apiLog);

                        $userPurchaseData = $this->userRepo->getUserDetail((int) $tmpPurchaseRight->user_id);
                        $BuyerEmail = $userPurchaseData->email;
                        $BuyerName = $userPurchaseData->first_name . " " . $userPurchaseData->last_name;

                        $rightDetail = $this->application->getRightByRightID($tmpPurchaseRight->right_id);
                        $objRight = $this->application->getRightDetailsByID($tmpPurchaseRight->right_id);

                        $sendmailUserArr = [];

                        $sendmailUserArr['name'] = $BuyerName;

                        $sendmailUserArr['email'] = $BuyerEmail;

                        $sendmailUserArr['price'] = $tmpPurchaseRight->right_price;
                        $sendmailUserArr['rightDetail'] = $rightDetail;
                        $sendmailUserArr['rightData'] = $objRight[0];
                        $sendmailUserArr['purchase_type'] = $tmpPurchaseRight->purchase_type;
                        $sendmailUserArr['buyforyear'] = $tmpPurchaseRight->no_of_year;

                        Event::fire("right.purchase", serialize($sendmailUserArr));
                    }

                    // event fire for email
                }
                //-----------------------------------
            }

            //call block chain api on stack right
            else if ($curruntRoute == 'accept_term_condition') {
                // save paypal log
                $responseTrans = $this->getPaypalPaymentDetail($payPalArr['paymentId']);
                if (@$responseTrans['name'] == "INVALID_RESOURCE_ID") {
                    $parr['paypal_response'] = '';
                } else {
                    
                    $parr['paypal_response'] = json_encode($responseTrans);
                }

                $parr['payment_by'] = (int) Auth::user()->id;
                $parr['txns_type'] = '3';
                $parr['right_id'] = (int)$currentRightId;
                
                $paypal_log_id = $this->application->savePaypalResponce($parr);
                
                $apiLog['request'] = json_encode($myPost);
                $apiLog['responce'] = $status;
                $apiLog['source'] = 'onTime put Stack on right';
                $apiLog['paypal_log_id'] = $paypal_log_id;
                $apiLog['right_no'] = $getRightDetail->number;
                Helpers::saveApiLog($apiLog);
                $data = [];
                $data['user_id'] = Auth::user()->id;
                $data['right_id'] = $currentRightId;
                $arrFileData['is_put_stack_value'] = 1;
                $arrUserData['is_put_stack'] = 1;
                $result = $this->application->updateRights($currentRightId, $arrFileData);
                $this->userRepo->updateUser($arrUserData, Auth::user()->id);
                $putId = $this->application->saveStacklog($data);
                $this->application->updateStacklog(Auth::user()->id, $currentRightId, ['is_payment' => 1]);
                Session::flash('message', 'Payment Done Successfully');
                return Redirect::route('right_details', ['user_id' => $getRightDetail->user_id, 'right_id' => $currentRightId]);
            }


           

            if($curruntRoute == 'add_right') {
               Session::flash('message', 'Payment done successfully. We will notify you once it gets evaluated.');
            } else if($curruntRoute == 'save_buy_right_popup'){
               Session::flash('message', 'Payment is successful. You can view it in your profile.');
 
            }
            else if($curruntRoute == 'accept_term_condition'){
               Session::flash('message', 'Payment is successful. You can evaluate the right using "Evaluate it" option.');

            }
            else {
               Session::flash('message', 'Payment Done Successfully');

            }


            return Redirect::route('front_dashboard');
        }
        if ($curruntRoute == 'add_right') {
            Session::flash('error', 'Payment failed');

            $tmpRight = $this->application->getTempRightByNumber($myRightnumber);
            $rightData2 = [];
            if($tmpRight){
                $rightData2['number'] = $tmpRight->number;
                $rightData2['user_id'] = $tmpRight->user_id;
                $rightData2['title'] = $tmpRight->title;
                $rightData2['type_id'] = $tmpRight->type_id;
                $rightData2['thumbnail'] = $tmpRight->thumbnail;
                $rightData2['inventor'] = $tmpRight->inventor;
                $rightData2['assignee'] = $tmpRight->assignee;
                $rightData2['cluster_id'] = $tmpRight->cluster_id;
                $rightData2['date'] = $tmpRight->date;
                $rightData2['expiry_date'] = $tmpRight->expiry_date;
                $rightData2['right_for_id'] = $tmpRight->right_for_id;
                $rightData2['url'] = $tmpRight->url;
                $rightData2['description'] = $tmpRight->description;
                $rightData2['keywords'] = $tmpRight->keywords;
                $rightData2['is_exclusive_purchase'] = $tmpRight->is_exclusive_purchase;
                $rightData2['is_non_exclusive_purchase'] = $tmpRight->is_non_exclusive_purchase;
                $rightData2['exclusive_purchase_price'] = $tmpRight->exclusive_purchase_price;
                $rightData2['non_exclusive_purchase_price'] = $tmpRight->non_exclusive_purchase_price;
                $rightData2['subscription'] = $tmpRight->subscription;
                $rightData2['num_of_month'] = $tmpRight->num_of_month;
                $rightData2['phase'] = $tmpRight->phase;
                $rightData2['attachment'] = $tmpRight->attachment;
                $rightData2['attachment_file_name'] = $tmpRight->attachment_file_name;
                $rightData2['tmp_right_id'] = $tmpRight->id;
            }
            Session::put('rightData', $rightData2);
            return Redirect::route('add_right');
        } else if ($curruntRoute == "save_buy_right_popup") {

            Session::flash('error', 'Payment failed');
            return Redirect::route('right_details', ['user_id' => @$user_id, 'right_id' => @$right_id]);
        } else {
            Session::flash('error', 'Payment failed');
            return Redirect::route('front_dashboard');
        }
    }

    /**
     * Pay With Paypall on Buy product
     * 
     * @param Request $request
     * @return type
     */
    public function getPaymentPageonBuy() {

        $redirect_url = Helpers::PayWithPaypal(1, $this->_api_context);
        return Redirect::away($redirect_url);
    }

    /**
     * Popup for report it 
     * @return type
     */
    public function reportPopup(Request $request) {
        try {
            $userId = $request->get('user_id');
            $rightId = $request->get('right_id');
            return view('framePopup.reportPopup')
                            ->with('userId', $userId)
                            ->with('rightId', $rightId);
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    /**
     * save Popup for report it 
     * @return type
     */
    public function saveReportPopup(Request $request, StorageManagerInterface $storage) {
        try {
            $userId = $request->get('user_id'); //wsose right
            $logedUser_id = Auth::user()->id; //current user login
            $rightId = $request->get('right_id'); //right id
            $comment = $request->get('comment');
            $claim = $request->get('claim');
            $arrRightData = [];
            $arrClaimList = [];
            $arrChecklist = [];
            $claimAttchment = [];
            $attchmentFileAdd = [];


            $arr = [];
            if (is_array($request->get('check_in')) && is_array($request->get('check_in_f'))) {
                $arrChecklist = array_merge($request->get('check_in'), $request->get('check_in_f'));
            } elseif (!is_array($request->get('check_in')) && is_array($request->get('check_in_f'))) {
                $arrChecklist = $request->get('check_in_f');
            } elseif (is_array($request->get('check_in')) && !is_array($request->get('check_in_f'))) {
                $arrChecklist = $request->get('check_in');
            }
            $checkList = implode(', ', $arrChecklist);

            $totalCal = $this->getValidateCalculation($checkList);
            $arrClaimList['right_owner'] = $userId;
            $arrClaimList['valid_by'] = $logedUser_id;
            $arrClaimList['right_id'] = $rightId;
            $arrClaimList['invalidate_checklist'] = $checkList;
            $arrClaimList['comment'] = $comment;
            $arrClaimList['invalid_final_score'] = $totalCal;

            //save cliam
            $result_obj = $this->application->saveInvalidReport($arrClaimList);

            //upload pics in storage....
            $files = $request->file("validattachment");

            if (count($files) > 0) {
                for ($i = 0; $i < count($files); $i++) {
                    $userProfile = $files[$i];
                    $fileSize = $userProfile->getClientSize();
                    $extension = $userProfile->getClientOriginalExtension();
                    if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                        $userBaseDir = 'appDocs/claimAttachments/' . Auth::user()->id . '/' . $rightId;
                        $userFileName = rand(0, 9999) . time() . '.' . $extension;
                        array_push($claimAttchment, $userFileName);
                        array_push($attchmentFileAdd, $userBaseDir);
                        $ar['right_id'] = $rightId;
                        $ar['claim_id'] = $result_obj;
                        $ar['claim_attacment'] = $userFileName;
                        $this->application->saveReportAttachment($ar);
                        $storage->engine()->put($userBaseDir . DIRECTORY_SEPARATOR . $userFileName, File::get($userProfile));
                        File::delete($userProfile);
                    } else {
                        return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                    }
                }
            }

            $getDetail = $this->application->find($rightId);
            $post = array(
                'rightAddress' => $getDetail->address,
                'score' => $totalCal,
                'reportTime' => time(),
                'uniqueId' => Auth::user()->email
            );
            Session::put('myPost', $post);
            Session::put('myRightAttchment', $claimAttchment);
            Session::put('myAttchmentFileAdd', $attchmentFileAdd);
            Session::put('curruntRoute', \Request::route()->getName());
            Session::put('current_right_ids', $rightId);
            Session::put('report_right_ids', $result_obj);
            $tenPersent = ($this->application->getBounty($rightId) * 10) / 100;
            Session::put('paypal_amount', $tenPersent);

            Session::flash('is_accept', 1);

            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

    public function downloadZipFile(Request $request) {
        $userId = (int) $request->get('user_id');
        $rightId = (int) $request->get('right_id');
        $rightDetailAttachments = $this->application->getAttachmentRights($userId, $rightId);
        return response()->download(storage_path('app/appDocs/rightsAttachments/' . $userId . '/' . $rightId . '/' . $rightDetailAttachments[0]->attachment), $rightDetailAttachments[0]->attachment_file_name);
    }

    public function downloadZipFileLatest(Request $request) {
        $userId = (int) $request->get('user_id');
        $rightId = (int) $request->get('right_id');
        $rightDetailAttachments = $this->application->getAttachmentRightsLatest($userId, $rightId);
        return response()->download(storage_path('app/appDocs/rightsAttachments/' . $userId . '/' . $rightId . '/' . $rightDetailAttachments[0]->attachment), $rightDetailAttachments[0]->attachment_file_name);
    }

    public function listSellingRights(Request $request) {
        try {
            $trendingRights = [];
            $trendingRights = $this->application->getTrendingRights(config('inv_common.TRENDING_RIGHTS_LIMIT_ON_DASHBOARD'));
            $rcntRgtIdString = '';
            $recentRights = [];
            // dd($trendingRights);
            return view('frontend.allselling_rights', compact('trendingRights', 'rcntRgtIdString', 'recommendedConnection'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function listRecommRights(Request $request) {
        try {

            $recommendedRights = [];
            $recommendedConnection = [];
            if (Auth::user()->is_scout == 1 && Auth::user()->is_admin_approved == 1) {
                $recommendedRights = $this->application->getRecommendedRights(config('inv_common.RECOMMENDED_RIGHTS_LIMIT_ON_DASHBOARD'));
            } else {
                $recommendedRights = $this->application->getRecommendedRightsUser(config('inv_common.RECOMMENDED_RIGHTS_LIMIT_ON_DASHBOARD'));
            }

            $recentTable = $this->userRepo->getUserDetail((int) Auth::user()->id);
            //dd($recentTable);
            $rcntRgtIdString = '';
            $recentRights = [];
            // dd($trendingRights);
            return view('frontend.allrecomm_rights', compact('recommendedRights', 'rcntRgtIdString', 'recommendedConnection'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

    public function listRecentlyRights(Request $request) {
        try {

            $recommendedConnection = [];
            $recommendedConnection = $this->userRepo->getConnectionUsers();



            $recentTable = $this->userRepo->getUserDetail((int) Auth::user()->id);
            //dd($recentTable);

            $rcntRgtIdString = '';
            $recentRights = [];
            if (isset($recentTable->recentViewedRights->recent_right_ids)) {
                $rcntRgtIdString = $recentTable->recentViewedRights->recent_right_ids;
                $recentRightIds = explode(',', $recentTable->recentViewedRights->recent_right_ids);
                //dd($recentRightIds);
                $recentRightIds = array_reverse($recentRightIds, true);
                // dd($recentRightIds);

                $recentRights = $this->application->getRecentViewedRights($recentRightIds, config('inv_common.RECENT_RIGHTS_LIMIT_ON_DASHBOARD'));
            }
            // dd($trendingRights);

            return view('frontend.allrecently_rights', compact('recentRights', 'rcntRgtIdString', 'recommendedConnection'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }

}
