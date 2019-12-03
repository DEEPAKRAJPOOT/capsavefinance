<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\AppNote;
use Auth;
use Session;

class NotesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkBackendLeadAccess');
    }
  
    public function index(Request $request)
    {
        $app_id = $request->get('app_id');
        $arrData = AppNote::showData($app_id);
        return view('backend.notes.notes',compact('arrData', 'app_id'));
    }


    public  function store(Request $request)
    {
        $arrData['note_data'] = $request->get('notesData');
        $arrData['app_id'] = $request->get('app_id');
        $arrData['created_by'] = Auth::user()->user_id;
        AppNote::create($arrData);
        return response()->json(['message'=>'Note inserted successfully','status'=>1]);
    }


    public function showNoteForm(Request $request)
    {
        $app_id = $request->get('app_id');
        return view('backend.notes.notesForm', compact('app_id'));
    }


}
