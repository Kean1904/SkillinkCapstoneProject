<?php

namespace App\Mail;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $job;
    public $applicant;

    /**
     * Create a new message instance.
     *
     * @param JobPost $job
     * @param User $applicant
     * @return void
     */
    public function __construct(JobPost $job, User $applicant)
    {
        $this->job = $job;
        $this->applicant = $applicant;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Bagong Aplikante para sa Trabaho: ' . $this->job->title . ' (SKILLINK Magalang)',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.application_alert',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
