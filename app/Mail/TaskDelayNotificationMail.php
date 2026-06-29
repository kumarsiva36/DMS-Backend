<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskDelayNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $details;
    public function __construct($details)
    {
        //
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {

        $subject = "\xF0\x9F\x92\xA3 Attention Required: Delayed Task Notification \xF0\x9F\x92\xA3";
        return $this->subject($subject)
        ->view('TaskDelayNotification')
        ->with('details', $this->details)
        ->attach($this->details['pdf_path']);
    }
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Task Delay Notification Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
