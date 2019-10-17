<?php

namespace App\Http\Controllers\Application;

use File;
use Auth;
use Helpers;
use Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Inv\Repositories\Contracts\Traits\StorageAccessTraits;
use App\Http\Requests\SkillFormRequest;
use App\Http\Requests\AwardsFormRequest;
use App\Http\Requests\EducationFormRequest;
use App\Http\Requests\ReasearchFormRequest;
use App\Inv\Repositories\Contracts\UserInterface as InvUserRepoInterface;
use App\Inv\Repositories\Libraries\Storage\Contract\StorageManagerInterface;
use App\Inv\Repositories\Contracts\ApplicationInterface;

class ScoutController extends Controller
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
        $this->middleware('auth');
        $this->userRepo    = $user;
        $this->application = $application;
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function showEducationForm(Request $request)
    {
        try 
        {
            Session::put('is_scout',1);
            $userId       = $request->get('user_id');
            $educationArr = [];
            if ($userId > 0) {
                $educationArr = $this->userRepo->getEductions($userId);
            }
            
            return view('scout.education', compact('educationArr','userId'));

        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex))->withInput();
        }
    }
    
    /**
     * Save Education Details.
     *
     * @return Response
     */
    
    public function saveEducationDetails(EducationFormRequest $request)
    {
        try {
            
            $userId  = $request->get('userId');
            $educationArr = [];
            if ($userId > 0) {

                // Delete previous record if available
                $educationArr = $this->userRepo->getEductions($userId);
                if (count($educationArr) > 0) {
                    $deletEducation = $this->userRepo->deleteEducations($userId);
                    if (!$deletEducation) {
                        return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                    }
                }
                $course                  = $request->get('course');
                $university              = $request->get('university');
                $date_attended_from_year = $request->get('date_attended_from_year');
                $date_attended_to_year   = $request->get('date_attended_to_year');
                $remarks                 = $request->get('remarks');

                if (count($course) > 0 && !empty($course)) {
                    for ($i = 0; $i < count($course); $i++) {
                        if($course[$i] != null){
                            $arrData               = [];
                            $arrData["user_id"]    = !empty($userId) && $userId != 0
                                    ? $userId : null;
                            $arrData["created_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;
                            $arrData["updated_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;

                            $arrData["university"] = !empty($university[$i] && $university[$i]
                                    !== '') ? $university[$i] : null;

                            $arrData["course"]                  = !empty($course[$i]
                                    && $course[$i] !== '') ? $course[$i] : null;
                            $arrData["date_attended_from_year"] = !empty($date_attended_from_year[$i]
                                    && $date_attended_from_year[$i] !== '') ? $date_attended_from_year[$i]
                                    : null;
                            $arrData["date_attended_to_year"]   = !empty($date_attended_to_year[$i]
                                    && $date_attended_to_year[$i] !== '') ? $date_attended_to_year[$i]
                                    : null;

                            $arrData["remarks"] = !empty($remarks[$i] && $remarks[$i]
                                    !== '') ? $remarks[$i] : null;

                            $this->userRepo->saveEducationDetails($arrData);
                        }
                    }
                    Session::flash('message',
                        trans('success_messages.education_saved_successfully'));
                    return redirect()->route('scout_skills',['userId' => $userId]);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.req_course_name'));
                }
            } else {
                return redirect()->back()->withErrors(trans('error_messages.user_id_not_found'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Show skill Details.
     *
     * @return Response
     */
    
    public function showSkillForm(Request $request)
    {
        $userId   = $request->get('userId');
        $skillArr = [];
        if ($userId > 0) {
            $skillArr = $this->userRepo->getSkills($userId);
        }
        return view('scout.skills', compact('skillArr'));
    }
    
    /**
     * Save skill Details.
     *
     * @return Response
     */
    public function saveSkills(SkillFormRequest $request)
    {
        try {
            $userId = (int) $request->get('userId');

            if ($userId > 0) {

                // Delete previous record if available
                $skillsArr = $this->userRepo->getSkills($userId);
                if (count($skillsArr) > 0) {
                    $deletedSkills = $this->userRepo->deleteSkills($userId);
                    if (!$deletedSkills) {
                        return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                    }
                }
                
                $skillsOtherArr = $this->userRepo->getOtherSkills($userId);
                if (count($skillsOtherArr) > 0) {
                    $deletedOtherSkills = $this->userRepo->deleteOtherSkills($userId);
                    if (!$deletedOtherSkills) {
                        return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                    }
                }

                $skillId = $request->get('skill_id');
                $level   = $request->get('level');
                $other_skill = $request->get('other_skill');
                if (count($skillId) > 0) {
                    for ($i = 0; $i < count($skillId); $i++) {
                        if($skillId[$i] != null){
                            $arrData               = [];
                            $arrData["user_id"]    = !empty($userId) && $userId != 0
                                    ? $userId : null;
                            $arrData["created_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;
                            $arrData["updated_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;

                            $arrData["skill_id"] = !empty($skillId[$i] && $skillId[$i]
                                    !== '') ? $skillId[$i] : null;

                            $arrData["level"] = !empty($level[$i] && $level[$i] !== '')
                                    ? $level[$i] : null;

                            $saveInsert = $this->userRepo->saveSkills($arrData);
                            if($other_skill[$i] != null) {
                                $arrOtherData["skill_id"] = $saveInsert;
                                $arrOtherData["other_skill"] = !empty($other_skill[$i] && $other_skill[$i]
                                    !== '') ? $other_skill[$i] : null;
                                $arrOtherData["user_id"]    = !empty($userId) && $userId != 0
                                    ? $userId : null;
                                $arrOtherData["level"] = !empty($level[$i] && $level[$i] !== '')
                                    ? $level[$i] : null;
                                $arrOtherData["created_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;
                                $arrOtherData["updated_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;
                                $this->userRepo->saveOtherSkills($arrOtherData);
                            }
                        }
                    }    
                    Session::flash('message',
                        trans('success_messages.skill_saved_successfully'));
                    return redirect()->route('scout_research_publication',['userId'=>$userId]);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                }
            } else {
                return redirect()->back()->withErrors(trans('error_messages.user_id_not_found'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Show research form
     * @return type
     */
    public function showResearchForm(Request $request)
    {
        $userId      = (int) $request->get('userId');
        $researchArr = [];
        if ($userId > 0) {
            $researchArr = $this->userRepo->getResearches($userId);
        }
        return view('scout.research', compact('researchArr'));
    }

    public function saveReasearchDetails(ReasearchFormRequest $request,
                                         StorageManagerInterface $storage)
    {
        try {
            $userId = (int) $request->get('userId');
            if ($userId > 0) {
                // Delete previous record if available
                $resArr = $this->userRepo->getResearches($userId);
                if (count($resArr) > 0) {
                    //$deletedRes = $this->userRepo->deleteResearches($userId);
                    $deletedRes = $this->userRepo->updateflagdeleteResearches($userId);
                    if (!$deletedRes) {
                        return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                    }
                }

                $title            = $request->get('title');
                $journalMagazine  = $request->get('journal_magazine');
                $publicationMonth = $request->get('publication_month');
                $publicationYear  = $request->get('publication_year');
                $attachment       = $request->file('attachment');
                $resArrFlagID = $this->userRepo->getResearchesById($userId);

                if (count($title) > 0) {
                    for ($i = 0; $i < count($title); $i++) {
                        if($title[$i] != null){
                            $arrData               = [];
                            $arrData["user_id"]    = !empty($userId) && $userId != 0
                                    ? $userId : null;
                            $arrData["created_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;
                            $arrData["updated_by"] = !empty($userId) && $userId != 0
                                    ? $userId : null;

                            $arrData["title"]            = !empty($title[$i] && $title[$i]
                                    !== '') ? $title[$i] : null;
                            $arrData["journal_magazine"] = !empty($journalMagazine[$i]
                                    && $journalMagazine[$i] !== '') ? $journalMagazine[$i]
                                    : null;

                            $arrData["publication_month"] = !empty($publicationMonth[$i]
                                    && $publicationMonth[$i] !== '') ? $publicationMonth[$i]
                                    : null;

                            $arrData["publication_year"] = !empty($publicationYear[$i]
                                    && $publicationYear[$i] !== '') ? $publicationYear[$i]
                                    : null;


                            if (isset($attachment[$i]) && count($attachment[$i]) > 0) {
                                $fileSize  = $attachment[$i]->getClientSize();
                                $extension = $attachment[$i]->getClientOriginalExtension();
                                if ($fileSize < config('inv_common.USER_PROFILE_MAX_SIZE')) {
                                    $userBaseDir           = 'appDocs/publications';
                                    $userFileName          = rand(0, 9999).time().'.'.$extension;
                                    $arrData["attachment"] = $userFileName;
                                    $storage->engine()->put($userBaseDir.DIRECTORY_SEPARATOR.$userFileName,
                                        File::get($attachment[$i]));
                                    // Delete the temporary file
                                    File::delete($attachment[$i]);
                                } else {
                                    return redirect()->back()->withErrors(trans('error_messages.file_size_error'));
                                }
                            } else {
                                if (isset($resArrFlagID[$i]) && count($resArrFlagID[$i]) > 0) {
                                    $arrData["attachment"] = $resArrFlagID[$i]->attachment;
                                }
                            }
                            
                            $resArrDele = $this->userRepo->getResearchesById($userId);
                            if (count($resArrDele) > 0) {
                                $deletedResArr = $this->userRepo->deleteResearchesbyFlag($userId);
                            if (!$deletedResArr) {
                            return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                            }
                            }
                            $this->userRepo->saveReasearchDetails($arrData);
                        }
                    }    
                    Session::flash('message',
                        trans('success_messages.research_saved_successfully'));
                    return redirect()->route('scout_awards_honors',['userId' => $userId]);
                } else {
                    return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                }
            } else {
                return redirect()->back()->withErrors(trans('error_messages.user_id_not_found'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    /**
     * Show award form
     * 
     * @return type
     */
    public function showAwardForm(Request $request)
    {
        $userId = (int) $request->get('userId');
        $awardArr = [];
        if ($userId > 0) {
            $awardArr = $this->userRepo->getAwards($userId);
        }
       
        return view('scout.awards', compact('awardArr'));
    }

    /**
     * Save Award
     * @param AwardsFormRequest $request
     * @return type
     */
    public function saveAwardsDetails(AwardsFormRequest $request)
    {
        try {
            $userId = (int) $request->get('userId');
            
            // Delete previous record if available
                 $awardArr = $this->userRepo->getAwards($userId);
                if (count($awardArr) > 0) {
                    $deletedAwrd = $this->userRepo->deleteAwards($userId);
                    if (!$deletedAwrd) {
                        return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                    }
                }
            
            
            if ($userId > 0) {
                $title       = $request->get('title');
                $description = $request->get('description');

                if (count($title) > 0) {
                    for ($i = 0; $i < count($title); $i++) {
                         if($title[$i] != null){
                                $arrData            = [];
                                $arrData["user_id"] = !empty($userId) && $userId != 0 ? $userId
                                        : null;

                                $arrData["created_by"] = !empty($userId) && $userId != 0
                                        ? $userId : null;
                                $arrData["updated_by"] = !empty($userId) && $userId != 0
                                        ? $userId : null;

                                $arrData["title"] = !empty($title[$i] && $title[$i] !== '')
                                        ? $title[$i] : null;

                                $arrData["description"] = !empty($description[$i] && $description[$i]
                                        !== '') ? $description[$i] : null;

                                $this->userRepo->saveAwards($arrData);
                            }
                        }
                        //update user scoute
                            $arr['is_scout'] = Session::get('is_scout');
                           
                            $this->userRepo->updateUser($arr, $userId); 
                            
                            Session::forget('is_scout');
                            Session::flash('message',
                            //trans('success_messages.registration_completed'));
                            trans('success_messages.registration_completed_scout'));
                        return redirect()->route('front_dashboard');
                    } else {
                        return redirect()->back()->withErrors(trans('error_messages.data_not_found'));
                    }
                } else {
                return redirect()->back()->withErrors(trans('error_messages.user_id_not_found'));
            }
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
    
    
    
    
    
 }