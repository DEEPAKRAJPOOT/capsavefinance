<?php

namespace App\Inv\Repositories\Contracts\Traits;

use File;
use Helpers;
use Response;
use Exception;
use Illuminate\Http\Request;

trait StorageAccessTraits
{

    /**
     * Get storage files images
     *
     * @param Request $request
     * @return Response
     * @auther Harish
     */
    public function accessStorageImages(Request $request)
    {
        try {
            
            $folder   = $request->get('folder');
            $filename = decrypt($request->get('file'));
             
            $path     = storage_path('app/appDocs/'.$folder.'/'.$filename);
            
            if (!File::exists($path)) {
                abort(404);
            }

            $file     = File::get($path);
            $type     = File::mimeType($path);
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            return $response;
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(Helpers::getExceptionMessage($ex));
        }
    }
}