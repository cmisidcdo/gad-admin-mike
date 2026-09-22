<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $periodLabel;
    public string $emailSubject;
    public ?string $customMessage;
    public string $actionUrl;
    public array $unsubmittedSectors;

    /**
     * Create a new message instance.
     */
    public function __construct(
        User $user,
        string $periodLabel,
        string $emailSubject = 'Reminder: Pending GAD Document Submission',
        ?string $customMessage = null,
        ?string $actionUrl = null,
        array $unsubmittedSectors = []
    ) {
        $this->user = $user;
        $this->periodLabel = $periodLabel;
        $this->emailSubject = $emailSubject;
        $this->customMessage = $customMessage;
        $this->actionUrl = $actionUrl ?? url('/document-archive');
        $this->unsubmittedSectors = $unsubmittedSectors;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
