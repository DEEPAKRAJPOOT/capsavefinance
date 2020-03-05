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
use App\Inv\Repositories\Models\FinanceModel;
use App\Inv\Repositories\Contracts\Traits\CommonTrait;
use App\Inv\Repositories\Models\CamReviewSummPrePost;
use App\Inv\Repositories\Models\AppProgramOffer;
use App\Inv\Repositories\Models\Business;

class ReviewerSummary extends Mailable
{
    use Queueable, SerializesModels;
    use CommonTrait;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mstRepo, $user)
    {
        $this->mstRepo = $mstRepo;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(Request $request)
    {
        $preCondArr = [];
        $postCondArr = [];
        $this->func_name = __FUNCTION__;
        $offerPTPQ = '';
        $appId = $request->get('app_id');
        $bizId = $request->get('biz_id');
        $businessDetails = Business::find($bizId);
        $preCondArr = $postCondArr = array();
        $limitOfferData = AppProgramLimit::getLimitWithOffer($appId, $bizId, config('common.PRODUCT.LEASE_LOAN'));
        $reviewerSummaryData = CamReviewerSummary::where('biz_id','=',$bizId)->where('app_id','=',$appId)->first();        
        if(isset($limitOfferData->prgm_offer_id) && $limitOfferData->prgm_offer_id) {
            $offerPTPQ = OfferPTPQ::getOfferPTPQR($limitOfferData->prgm_offer_id);
        }
        $preCondArr=[];
        $postCondArr=[];
        if(isset($reviewerSummaryData['cam_reviewer_summary_id'])) {
            $dataPrePostCond = CamReviewSummPrePost::where('cam_reviewer_summary_id', $reviewerSummaryData['cam_reviewer_summary_id'])
                            ->where('is_active', 1)->get();
            $dataPrePostCond = $dataPrePostCond ? $dataPrePostCond->toArray() : [];
            if(!empty($dataPrePostCond)) {
              $preCondArr = array_filter($dataPrePostCond, array($this, "filterPreCond"));
              $postCondArr = array_filter($dataPrePostCond, array($this, "filterPostCond"));
            }
        } 
        
        //Get PreOffer Docs
        $appRepo = \App::make('App\Inv\Repositories\Contracts\ApplicationInterface');   
        $appProductIds = [];
        $appProducts = $appRepo->getApplicationProduct($appId);
        foreach($appProducts->products as $product){
            array_push($appProductIds, $product->pivot->product_id);
        }        
        $preOfferDocs=[];        
        $prgmDocs = $appRepo->getRequiredDocs(['doc_type_id' => 4], $appProductIds);
        foreach ($prgmDocs as $key => $value) {
            $preOfferDocs[] = $value->doc_id;
        }
        //config('common.review_summ_mail_docs_id') + 
        $fileArray = AppDocumentFile::getReviewerSummaryPreDocs($appId, $preOfferDocs);
        $leaseOfferData = $facilityTypeList = array();
        $leaseOfferData = AppProgramOffer::getAllOffers($appId, '3');
        $facilityTypeList= $this->mstRepo->getFacilityTypeList()->toarray();
        $arrStaticData = array();
        $arrStaticData['rentalFrequency'] = array('1'=>'Yearly','2'=>'Bi-Yearly','3'=>'Quarterly','4'=>'Monthly');
        $arrStaticData['rentalFrequencyForPTPQ'] = array('1'=>'Year','2'=>'Bi-Yearly','3'=>'Quarter','4'=>'Months');
        $arrStaticData['securityDepositType'] = array('1'=>'INR','2'=>'%');
        $arrStaticData['securityDepositOf'] = array('1'=>'Loan Amount','2'=>'Asset Value','3'=>'Asset Base Value','4'=>'Sanction');
        $arrStaticData['rentalFrequencyType'] = array('1'=>'Advance','2'=>'Arrears');  
        $dispAppId = 'CAPS' . sprintf('%06d', $appId);
        $supplyOfferData = $appRepo->getAllOffers($appId, 1);//for supply chain  
        $email = $this->view('emails.reviewersummary.reviewersummarymail', [
            'limitOfferData'=> $limitOfferData,
            'reviewerSummaryData'=> $reviewerSummaryData,
            'offerPTPQ' => $offerPTPQ,
            'preCondArr' => $preCondArr,
            'postCondArr' => $postCondArr,
            'leaseOfferData'=> $leaseOfferData,
            'arrStaticData' => $arrStaticData,
            'facilityTypeList' => $facilityTypeList,
            'receiverUserName' => $this->user['receiver_user_name'],
            'appId' => $appId,
            'url' => 'https://'. config('proin.backend_uri'),
            'dispAppId' => $dispAppId,
            'supplyOfferData' => $supplyOfferData

        ]);        
        // $loggerData = [
        //         'email_from' => config('common.FRONTEND_FROM_EMAIL'),
        //         'email_to' => config('common.review_summ_mails'),
        //         'email_type' => $this->func_name,
        //         'name' => NULL,
        //         'subject' => 'Reviewer Summary Detail',
        //         'body' => $email,
        // ];

        //$email_subject = 'Application ' . $dispAppId . ' is waiting for your approval - '.$businessDetails->biz_entity_name;
        $email_subject = 'New Application is waiting for your approval ' . $businessDetails->biz_entity_name;
        $email->subject($email_subject);

        if($fileArray) {
            foreach($fileArray as $key=>$val) {
                if(file_exists(storage_path('app/public/'.$val['file_path']))) {
                    
                    $email->attach(storage_path('app/public/'.$val['file_path']),
                    [
                        'as' => $val['file_name']
                    ]);
                    //$loggerData['file_path'][] = 'app/public/'.$val['file_path'];
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
                //$loggerData['file_path'][] = 'app/public/'.$camFile['file_path'];
            }
        }
        //$filepath = implode('||', $loggerData['file_path']);
        //$loggerData['file_path'] = $filepath;
        //FinanceModel::logEmail($loggerData);
        return $email;
    }
}
