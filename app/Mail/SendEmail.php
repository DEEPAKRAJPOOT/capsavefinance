<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Models\FinanceModel;
use Illuminate\Support\Facades\Log;
use DB;

class SendEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The email data.
     *
     * @var array
     */
    protected $mailData;

    /**
     * The email log data.
     *
     * @var array
     */
    protected $mailLogData;
    protected $mailLogDataId;

    /**
     * Create a new message instance.
     *
     * @param string $mailDataSerialized The serialized email data.
     * @param string $mailLogDataSerialized The serialized email log data.
     *
     * @return void
     */
    public function __construct(string $mailDataSerialized, string $mailLogDataSerialized)
    {
        try {
            // Deserialize the data
            $this->mailData = unserialize($mailDataSerialized);
            $this->mailLogData = unserialize($mailLogDataSerialized);
            $this->mailLogData['status'] = 0;
            $this->mailLogData['subject'] = $this->mailData['mail_subject'];
            $this->mailLogData['body'] = $this->mailData['mail_body'];
            $this->mailLogData['email_to'] = $this->mailData['email_to'];
            $this->mailLogData['email_cc'] = $this->mailData['email_cc'] ?? NULL;
            $this->mailLogData['email_bcc'] = $this->mailData['email_bcc'] ?? NULL;
            $this->mailLogDataId = FinanceModel::logEmail($this->mailLogData);
        } catch (\Exception $e) {
            // Log or handle the error
            throw $e;
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            $email_to = $this->mailData['email_to'];
            $email_cc = $this->mailData['email_cc'] ?? NULL;
            $email_bcc = $this->mailData['email_bcc'] ?? NULL;
            if(!empty($email_to)){
                foreach($email_to as $email){
                    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("Invalid email address.$email)");
                    }
                }
            }
            if(!empty($email_cc)){
                foreach($email_cc as $emailCc){
                    if (!filter_var(trim($emailCc), FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("Invalid email address.$emailCc)");
                    }
                }
            }
            if(!empty($email_bcc)){
                foreach($email_bcc as $emailBcc){
                    if (!filter_var(trim($emailBcc), FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("Invalid email address.$emailBcc)");
                    }
                }
            }
            $email = $this->view('email')
                        ->with(['baseUrl' => $this->mailData['base_url'], 'varContent' => $this->mailData['mail_body']])
                        ->subject($this->mailData['mail_subject']);

            if (!empty($this->mailData['attachments'])) {
                foreach ($this->mailData['attachments'] as $attachment) {
                    if ($attachment['isBinaryData']) {
                        $file_path = $attachment['file_path'];
                        // Check if the file path is base64 encoded
                        if (base64_decode($file_path, true) !== false) {
                            // The file path is base64 encoded
                            $file_path = base64_decode($file_path);
                        }
                        $email->attachData($file_path, $attachment['file_name']);
                    } else {
                        $email->attach($attachment['file_path'], ['as' => $attachment['file_name']]);
                    }
                }
            }
            // To update mail Log Data status on email_logger table
            DB::update("UPDATE rta_email_logger SET status = ? WHERE id = ?", [1, $this->mailLogDataId]);
        } catch (\Exception $e) {
            throw $e;
        }

        return $email;
    }
}