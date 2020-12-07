<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($txt)
    {
        $this->text = $txt;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('INTAXA Account Verification')->from('intaxa@sidescriptdev.com')->view('email.verifyAccount');
    }
}
