<?php
namespace App\Inv\Repositories\Contracts\Traits;

use Auth;

trait ApplicationTrait
{
    /**
     * Get Application Required documents
     * 
     * @param array $prgmDocsWhere
     * @return mixed
     */
    protected function getProgramDocs($prgmDocsWhere)
    {
        if ($prgmDocsWhere['stage_code'] == 'doc_upload') {
            $prgmDocs = $this->appRepo->getRequiredDocs(['doc_type_id' => 1]);
        } else {
            $prgmDocs = $this->docRepo->getProgramDocs($prgmDocsWhere);
        }
        return $prgmDocs ;
    }
    
    /**
     * Create Application Required Docs
     * 
     * @param array $prgmDocsWhere
     * @param integer $user_id
     * @param integer $app_id
     * @return mixed
     */
    protected function createAppRequiredDocs($prgmDocsWhere, $user_id, $app_id)
    {        
        $reqDocs = $this->getProgramDocs($prgmDocsWhere);
                        
        if($reqDocs && count($reqDocs) == 0) {
            return;
        }
        
        $appDocs = [];
        foreach($reqDocs as $doc) {
            //$appDocCheck = AppDocument::where('app_id', $app_id)->count();
            $appDocCheck = $this->docRepo->isAppDocFound($app_id, $doc->doc_id);
            
            if(!$appDocCheck){
                $curData = \Carbon\Carbon::now()->format('Y-m-d h:i:s');
                $appDocs[] = [
                    'rcu_status' => 0,
                    'user_id' => $user_id,
                    'app_id' => (int) $app_id,
                    'doc_id' => $doc->doc_id,
                    'is_upload' => 0,
                    'is_required' => 1,
                    'created_by' => Auth::user()->user_id,
                    'created_at' => $curData,
                    'updated_by' => Auth::user()->user_id,                   
                    'updated_at' => $curData, 
                ];
            }
        }
        
        $this->docRepo->saveAppRequiredDocs($appDocs);        
        return $reqDocs;
    }
}
