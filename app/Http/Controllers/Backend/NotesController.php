<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Display a listing of the Notes.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //    echo  "jhjh";
        return view('backend.notes.notes');
    }
}
