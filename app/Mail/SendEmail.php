<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Models\FinanceModel;
use Illuminate\Support\Facades\Log;

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

            if ($this->mailData['attachments']) {
                foreach ($this->mailData['attachments'] as $attachment) {
                    $email->attach($attachment['file_path'], ['as' => $attachment['file_name']]);
                }
            }

            FinanceModel::logEmail($this->mailLogData);
        } catch (\Exception $e) {
            Log::error('Error building email: ' . $e->getMessage());
            throw $e;
        }

        return $email;
    }
}