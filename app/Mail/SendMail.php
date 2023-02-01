<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $message;
    public $_subject;
    public $email;

    public function __construct($message, $_subject, $email)
    {
        $this->message = $message;
        $this->_subject = $_subject;
        $this->email = $email;
    }

    public function build()
    {
        $user = User::where('email', $this->email)->first();
        return $this->subject($this->_subject)
            ->markdown('email.default')
            ->with(['user' => $user, 'mesage' => $this->message, 'url' => '#']);
    }
}
