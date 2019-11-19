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
       
    }
  
    public function index()
    {
        $app_id = 1;
        $arrData = AppNote::showData($app_id);
        return view('backend.notes.notes',compact('arrData'));
    }


    public  function store(Request $request)
    {
        $arrData['note_data'] = $request->get('notesData');
        $arrData['created_by'] = Auth::user()->user_id;
        $arrData['app_id']= 1;
        AppNote::create($arrData);
        return response()->json(['message'=>'Note inserted successfully','status'=>1]);
    }


    public function showNoteForm()
    {
        return view('backend.notes.notesForm');
    }


}
