<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
//use GuzzleHttp\Psr7\Request;

class NotesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
       
    }
  
    public function index()
    {
        $arrData = DB::table('note')->join('users', 'users.user_id', '=', 'note.created_by')->select('note.*', 'users.f_name', 'users.m_name', 'users.l_name')->get();     
        return view('backend.notes.notes',compact('arrData'));
    }

    public  function store(Request $request)
    {
        $notesData = $request->get('notesData');
        DB::table('note')->insert(
            ['note_data' => $notesData,'created_by'=>Auth::user()->user_id]
        );
        return response()->json(['message'=>'Note inserted successfully','status'=>1]);
    }


    public function showNoteForm(){
        return view('backend.notes.notesForm');
    }
}
