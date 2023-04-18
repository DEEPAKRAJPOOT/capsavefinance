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
            Log::error('Failed to unserialize mail data: '.$e->getMessage());
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
            $email = $this->view('email')
                        ->with(['baseUrl' => $this->mailData['base_url'], 'varContent' => $this->mailData['mail_body']])
                        ->subject($this->mailData['mail_subject']);

            if ($this->mailData['attachment_path']) {
                $email->attach($this->mailData['attachment_path']);
            }
            // To update mail Log Data status on email_logger table
            DB::table('email_logger')
            ->where('id', $this->mailLogDataId)
            ->update(['status' => 1]);
        } catch (\Exception $e) {
            Log::error('Error building email: ' . $e->getMessage());
            throw $e;
        }

        return $email;
    }
}