<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Inv\Repositories\Models\CamReviewerSummary;
use App\Inv\Repositories\Models\AppProgramLimit;
use App\Inv\Repositories\Models\AppDocumentFile;
use App\Inv\Repositories\Models\OfferPTPQ;
use App\Inv\Repositories\Models\UserAppDoc;

class ReviewerSummary extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $offerPTPQ = '';
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $limitOfferData = AppProgramLimit::getLimitWithOffer($appId, $bizId, config('common.PRODUCT.LEASE_LOAN'));
        $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$bizId)->where('app_id','=',$appId)->first();        
        if(isset($limitOfferData->prgm_offer_id) && $limitOfferData->prgm_offer_id) {
            $offerPTPQ = OfferPTPQ::getOfferPTPQR($limitOfferData->prgm_offer_id);
        }
        $fileArray = AppDocumentFile::getReviewerSummaryPreDocs($appId, config('common.review_summ_mail_docs_id'));
        $email = $this->view('emails.reviewersummary.reviewersummarymail', [
            'limitOfferData'=> $limitOfferData,
            'reviewerSummaryData'=> $reviewerSummaryData,
            'offerPTPQ' => $offerPTPQ
        ]);

        $email->subject('Reviewer Summary Detail');

        if($fileArray) {
            foreach($fileArray as $key=>$val) {
                if(file_exists(storage_path('app/public/'.$val['file_path']))) {
                    $email->attach(storage_path('app/public/'.$val['file_path']),
                    [
                        'as' => $val['file_name']
                    ]);
                }
            }
        }

        //Cam report files
        $camFile = UserAppDoc::getLatestDoc($appId, config('common.PRODUCT.LEASE_LOAN'), '2');
        if($camFile) {
            if(file_exists(storage_path('app/public/'.$camFile['file_path']))) {
                $email->attach(storage_path('app/public/'.$camFile['file_path']),
                [
                    'as' => $camFile['file_name']
                ]);
            }
        }
        
        return $email;
    }
}
