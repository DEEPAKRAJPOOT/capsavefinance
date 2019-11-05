<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PartnerFormRequest;
use App\Inv\Repositories\Contracts\OwnerInterface as InvOwnerRepoInterface;
use Session;

class OwnerController extends Controller {

    protected $ownerRepo;

    public function __construct(InvOwnerRepoInterface $ownerRepo) {
        $this->ownerRepo = $ownerRepo;
    }

    /**
     * Show the authorized signatory form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showAuthorizedSignatoryForm()
    {
        $userId  = Session::has('userId') ? Session::get('userId') : 0;
        $userArr = [];
        if ($userId > 0) {
            $userArr = $this->userRepo->find($userId);
        }

        return view('auth.authorized-signatory', compact('userArr'));
    } 

    /**
     * Show the business information form.
     *
     * @return \Illuminate\Http\Response
     */
    //////////////////Save Promoter Multiple Details///////////////////////// 
    public function saveAuthorizedSignatory(Request $request) {
        try {
            $arrFileData = $request->all();
            $owner_info = $this->ownerRepo->saveOwnerInfo($arrFileData); //Auth::user()->id
          if ($owner_info) {
                return response()->json(['message' =>trans('success_messages.basic_saved_successfully')]);
            } else {
               return response()->json(['message' =>trans('success_messages.oops_something_went_wrong')]);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
