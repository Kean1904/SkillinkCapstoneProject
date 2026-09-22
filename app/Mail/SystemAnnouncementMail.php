<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipient;
    public $title;
    public $messageBody;
    public $type; // 'announcement' or 'maintenance'

    /**
     * Create a new message instance.
     *
     * @param User $recipient
     * @param string $title
     * @param string $messageBody
     * @param string $type
     * @return void
     */
    public function __construct($recipient, string $title, string $messageBody, string $type = 'announcement')
    {
        $this->recipient = $recipient;
        $this->title = $title;
        $this->messageBody = $messageBody;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $prefix = ($this->type === 'maintenance') ? '🔧 [System Advisory]' : '📢 [PESO Announcement]';
        return new Envelope(
            subject: "{$prefix} {$this->title} — SKILLINK Magalang",
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
            view: 'emails.system_announcement',
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
