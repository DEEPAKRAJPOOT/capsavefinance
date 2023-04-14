<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Models\Master\EmailTemplate;
use App\Inv\Repositories\Models\FinanceModel;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailData;
    public $mailLogData;

    public function __construct($mailDataSerialized, $mailLogDataSerialized)
    {
        // Deserialize the data
        $this->mailData = unserialize($mailDataSerialized);
        $this->mailLogData = unserialize($mailLogDataSerialized);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('email')
                    ->with(['baseUrl' => $this->mailData['base_url'], 'varContent' => $this->mailData['mail_body']])
                    ->subject($this->mailData['mail_subject']);
        if ($this->mailData['attachment_path']) {
            $email->attach($this->mailData['attachment_path']);
        }
        FinanceModel::logEmail($this->mailLogData);
        return $email;
    }

}
