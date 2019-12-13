<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\AppNote;
use Auth;
use Session;
use App\Inv\Repositories\Contracts\ApplicationInterface as InvAppRepoInterface;

class NotesController extends Controller {

    protected $appRepo;

    public function __construct(InvAppRepoInterface $app_repo)
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
        $this->appRepo = $app_repo;
    }

    public function index(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = AppNote::showData($app_id);
        return view('backend.notes.notes', compact('arrData', 'app_id'));
    }

    public function store(Request $request)
    {
        $arrData['note_data'] = $request->get('notesData');
        $arrData['app_id'] = $request->get('app_id');
        $arrData['created_by'] = Auth::user()->user_id;
        AppNote::create($arrData);
        return response()->json(['message' => 'Note inserted successfully', 'status' => 1]);
    }

    public function showNoteForm(Request $request)
    {
        $app_id = $request->get('app_id');
        return view('backend.notes.notesForm', compact('app_id'));
    }

    /**
     * show pd notes list 
     * @param Request $request
     */
    public function pdNotesList(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = [];
        return view('backend.pdNotes.pd_notes', compact('arrData', 'app_id'));
    }

    /**
     * Show pd notes form
     * 
     * @param Request $request
     * @return type mixed
     */
    public function showPdNotesForm(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = [];
        return view('backend.pdNotes.pdNotesForm', compact('arrData', 'app_id'));
    }

    /**
     * Save pd notes
     * 
     * @param Request $request
     * @return type mixed
     */
    public function savePdNotes(Request $request)
    {
        try {
            $app_id = $request->get('app_id');
            $type = $request->get('type');
            $comments = $request->get('comments');

            $this->appRepo->savePdNotes([
                'app_id' => $app_id,
                'type' => $type,
                'comments' => $comments,
                'created_by' => Auth::user()->user_id,
                'created_at' => \Carbon\Carbon::now(),
            ]);
            Session::flash('message', trans('success_messages.pd_notes_saved'));
            Session::flash('operation_status', 1);
            return redirect()->back();
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }

}
