<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $otp;
    public $type;

    public function __construct($otp, $type = 'Verification')
    {
        $this->otp = $otp;
        $this->type = $type;
    }

    public function build()
    {               
        return $this->subject('Admin OTP ' . $this->type)
            ->view('emails.admin-otp')
            ->with(['otp' => $this->otp, 'type' => $this->type]);
    }
}
