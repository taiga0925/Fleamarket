<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $messageContent)
    {
        $this->user = $user;
        $this->messageContent = str_replace("\n", "\n", $messageContent);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('COACHTECHフリマからのお知らせ')->markdown('emails.notification')->with(['messageContent' => $this->messageContent]);
    }
}
