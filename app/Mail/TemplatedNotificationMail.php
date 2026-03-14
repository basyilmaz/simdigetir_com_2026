<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplatedNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        public string $subjectLine,
        public string $bodyText,
        public array $context = []
    ) {}

    public function build(): static
    {
        return $this
            ->subject($this->subjectLine)
            ->view('emails.notification-template', [
                'bodyText' => $this->bodyText,
                'context' => $this->context,
            ]);
    }
}

