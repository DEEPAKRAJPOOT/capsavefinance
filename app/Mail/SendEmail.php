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
            $this->mailLogData['email_to'] = $this->_convertEmailArray($this->mailData['email_to']) ?? NULL;
            $this->mailLogData['email_cc'] = $this->_convertEmailArray($this->mailData['email_cc']) ?? NULL;
            $this->mailLogData['email_bcc'] = $this->_convertEmailArray($this->mailData['email_bcc']) ?? NULL;
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
            $email_to = $this->_convertEmailArray($this->mailData['email_to']) ?? NULL;
            $email_cc = $this->_convertEmailArray($this->mailData['email_cc']) ?? NULL;
            $email_bcc = $this->_convertEmailArray($this->mailData['email_bcc']) ?? NULL;
            $emailFields = ['to' => $email_to, 'cc' => $email_cc, 'bcc' => $email_bcc];
            if (empty($email_to) && empty($email_cc) && empty($email_bcc)) {
                throw new \Exception("No email recipient specified.");
            }
            foreach ($emailFields as $field => $emails) {
                    if (is_null($emails)) {
                        continue;
                    }
                    $invalidEmails = array_filter($emails, function($email) {
                        return !filter_var($email, FILTER_VALIDATE_EMAIL);
                    });
                    
                    if (!empty($invalidEmails)) {
                        $errorMsg = 'Invalid email address(es) in ' . $field . ': ' . implode(', ', $invalidEmails);
                        throw new \Exception($errorMsg);
                    }                    
            }
            $email = $this->view('email')
                        ->with(['baseUrl' => $this->mailData['base_url'], 'varContent' => $this->mailData['mail_body']])
                        ->subject($this->mailData['mail_subject']);
            
            //Set Null if To has no email id
            if(empty($email_to)){
                $email->to = array();
            }

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
            DB::table('email_logger')
                ->where('id', $this->mailLogDataId)
                ->update(['status' => 1]);
            return $email;
        } catch (\Exception $e) {
            // If an exception occurs, update the status to 0 indicating a failure
            DB::table('email_logger')
                ->where('id', $this->mailLogDataId)
                ->update(['status' => 0]);
            throw $e;
        }

    }

    private function _convertEmailArray($emailArray)
    {
        if (is_array($emailArray)) {
            // Check if it is a single email entry
            if (is_string($emailArray)) {
                return [$emailArray];
            }
    
            // If it's an array of email entries
            $emails = [];
            foreach ($emailArray as $item) {
                if (is_string($item) && !empty($item)) {
                    $emails[] = $item;
                } elseif (is_array($item) && isset($item['email']) && !empty($item['email'])) {
                    $emails[] = $item['email'];
                }
            }
            return $emails;
        }
        return [];
    }
}