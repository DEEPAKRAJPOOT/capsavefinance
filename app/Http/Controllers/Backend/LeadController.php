<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.lead.index');
    }

    
    /**
     * Display a listing of the resource.
     * All leads
     * @return \Illuminate\Http\Response
     */
     public function leadspool(){
         
         return view('backend.lead.leadpoll');
     }
     
     
    /**
     * Edit backend Lead
     * 
     * @param Request $request
     * @return type
     */
     
     public function editBackendLead(Request $request){
         try {
                $user_id = $request->get('user_id'); 
                    return view('backend.edit_lead');
                
         } catch (Exception $ex) {
             dd($ex);
         }
       
     }
     
     
}
