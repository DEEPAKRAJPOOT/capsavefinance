<?php

namespace App\Http\Controllers\Application;

use Auth;
use Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\Traits\StorageAccessTraits;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface;
use Session;
use Redirect;
class DefaultController extends Controller
{
    /**
     * User repository
     *
     * @var object
     */
    protected $userRepo;
    protected $application;

    use StorageAccessTraits;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(InvUserRepoInterface $user,
                                ApplicationInterface $application)
    {
        $this->userRepo    = $user;
        $this->application = $application;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    
    
    /**
     * show Wish List
     * 
     * @return type
     */
    
     public function showWishlistForm() {
        return view('framePopup.wishlist');
    }
    
    /**
     * Save wish list form...
     * 
     * @param Request $request
     * @return type
     */
    
    public function saveWishlistForm(Request $request) {
        $arrFileData = [];
        $arrFileData['type_id'] = (int)$request->get("type_id");
        $arrFileData['email'] = (!empty($request->get("email"))) ? $request->get("email"): '';
        $arrFileData['phone'] = (!empty($request->get("phone"))) ? $request->get("phone"): '';
        $arrFileData['areas_of_interest'] = (!empty($request->get("areas_of_interest"))) ? $request->get("areas_of_interest"): NULL;
        $this->userRepo->saveWishlistForm($arrFileData);
        Session::put('message',trans('success_messages.wishlist_completed'));
        Session::flash('is_accept', 1);
        return redirect()->route('wishlist');
    }
    
    
    public function connectWithoutLogin(Request $request) {
        $from_user_id = (int) decrypt($request->get("from_user_id"));
        $to_user_id = (int)decrypt($request->get("to_user_id"));
        if(isset(Auth::user()->id)) {  
            if($to_user_id && Auth::user()->id == $to_user_id) {
                return Redirect::route('profile');
            } else {
                return redirect('/login');
            }
        } else {
            return redirect('/login');
        }
    }

 }