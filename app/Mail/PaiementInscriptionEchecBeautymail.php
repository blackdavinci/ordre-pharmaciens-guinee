<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Snowfire\Beautymail\Beautymail;
use Illuminate\Queue\SerializesModels;

class PaiementInscriptionEchecBeautymail extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;

    /**
     * Create a new message instance.
     */
    public function __construct($inscription)
    {
        $this->inscription = $inscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Paiement Inscription Echec',
        );
    }

    /**
     * Get the message content definition.
     */
    public function build(Beautymail $beautymail)
    {
        // Directly return the content definition (you can set the email content here)
        return $beautymail->send('emails.minty', [
            'inscription' => $this->inscription,  // Pass the inscription data to the view
        ])
            ->to('ousmaneciss1@gmail.com')
            ->from('no-reply@yourapp.com', 'Votre Application'); // Set the sender info
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
