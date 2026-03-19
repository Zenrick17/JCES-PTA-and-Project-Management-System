<?php

namespace App\Mail;

use App\Models\ProjectContribution;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentApproved extends Mailable
{
    use Queueable, SerializesModels;

    public ProjectContribution $contribution;

    public function __construct(ProjectContribution $contribution)
    {
        $this->contribution = $contribution;
    }

    public function build()
    {
        $parentName = trim(($this->contribution->parent?->first_name ?? '') . ' ' . ($this->contribution->parent?->last_name ?? ''));
        $subject = 'Payment Approved';
        if ($parentName !== '') {
            $subject = $subject . ' - ' . $parentName;
        }

        return $this->subject($subject)
            ->view('emails.payment-approved')
            ->with([
                'contribution' => $this->contribution,
            ]);
    }
}
