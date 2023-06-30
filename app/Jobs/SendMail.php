<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Inv\Repositories\Models\FinanceModel;
// use App\Inv\Repositories\Models\AppAssignment;
// use App\Inv\Repositories\Entities\User\UserRepository;
// use App\Inv\Repositories\Entities\Application\ApplicationRepository;


class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $funcName;
    protected $bcc;
    protected $cc;
    protected $mail_subject;
    protected $mail_body;
    protected $data;
    protected $email_content;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to = [], $bcc, $cc, $mail_subject, $mail_body,$funcName,$data,$email_content)
    {
        $this->to = $to;
        // $this->from = $from;
        $this->bcc = $bcc;
        $this->cc = $cc;
        $this->mail_subject = $mail_subject;
        $this->mail_body = $mail_body;
        $this->funcName = $funcName;
        $this->data = $data;
        $this->email_content = $email_content;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        // dd($this->funcName);
        try {
            $to = $this->to;    
            // $from = $this->from;    
            $bcc = $this->bcc;    
            $cc = $this->cc;    
            $mail_subject = $this->mail_subject;    
            $mail_body = $this->mail_body;    
            $mailContent = [
                'email_from' => config('common.FRONTEND_FROM_EMAIL'),
                'email_to' => $to,
                'email_cc' => $cc ?? NULL,
                'email_bcc' => $bcc ?? NULL,
                'email_type' => $this->funcName,
                'user_name' => $this->data['approver_name'],
                'name' => $this->email_content->name,
                'subject' => $mail_subject,
                'body' => $mail_body,
                // 'att_name' => $att_name ?? NULL,
                // 'attachment' => $data['attachment'] ?? NULL,
            ];
            FinanceModel::logEmail($mailContent);
        }
        catch(Exception $e) {
            $this->failed($e);
        }
    }

    public function failed($exception)
    {
        $exception->getMessage();
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addSeconds(5);
    }
}
