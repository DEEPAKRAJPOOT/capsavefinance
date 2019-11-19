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

    public function __construct()
    {
        $this->middleware('auth');
       
    }
  
    public function index()
    {
        $app_id = 1;
        $arrData = DB::table('note')->join('users', 'users.user_id', '=', 'note.created_by')->select('note.*', 'users.f_name', 'users.m_name', 'users.l_name')->where('note.app_id', $app_id)->get();  
        return view('backend.notes.notes',compact('arrData'));
    }


    public  function store(Request $request)
    {
        $notesData = $request->get('notesData');
        $app_id = '1';
        DB::table('note')->insert(
            ['note_data' => $notesData,'app_id'=>$app_id,'created_by'=>Auth::user()->user_id]
        );
        return response()->json(['message'=>'Note inserted successfully','status'=>1]);
    }


    public function showNoteForm()
    {
        return view('backend.notes.notesForm');
    }


}
