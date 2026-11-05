<?php

namespace App\Mail;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffLoginMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Staff $staff, public User $user, public string $link, public int $days) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your FUNAAB Workforce login');
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.staff-login', with: [
            'name'     => trim(($this->staff->title?->title ? $this->staff->title->title . ' ' : '') . $this->staff->surname . ' ' . $this->staff->firstname),
            'staffNo'  => $this->staff->staff_no,
            'email'    => $this->user->email,
            'link'     => $this->link,
            'days'     => $this->days,
            'loginUrl' => route('welcome'),
        ]);
    }
}
