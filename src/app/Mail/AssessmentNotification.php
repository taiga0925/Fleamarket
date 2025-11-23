<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Item;
use App\Models\Rating;

class AssessmentNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $item;
    public $rating;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Item $item, Rating $rating)
    {
        $this->item = $item;
        $this->rating = $rating;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【coachtechフリマ】取引が完了しました')
            ->view('emails.assessment_notification');
    }
}
