<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Inv\Repositories\Models\Master\EmailTemplate;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mail_subject;
    public $data;

    public function __construct($mail_subject,$data)
    {
        // dd($this->mail_subject);
        $this->mail_subject = $mail_subject;
        $this->data = $data;
        // dd($this->data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $x = EmailTemplate::getEmailTemplate("APPROVER_MAIL_FOR_PENDING_CASES");
        // dd($x->message);
        return $this->subject($this->mail_subject)->html($x->message);
        // return $this->text($x);
    }
}
