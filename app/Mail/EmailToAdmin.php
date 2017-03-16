<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailToAdmin extends Mailable
{
    use Queueable, SerializesModels;
    
    public $emailData;
    
    /**
     * Create a new message instance.
     *
     * @param $emailData
     */
    public function __construct(array $emailData)
    {
        $this->emailData = (object) $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.admin')
            ->subject('You have a new email from FeedGist');
    }
}
