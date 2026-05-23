<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function envelope(): Envelope
    {
        $businessName = Setting::get('business_name', 'JNJ Auto Car Wash');

        return new Envelope(
            subject: 'Appointment Confirmed - '.$businessName,
        );
    }

    public function content(): Content
    {
        $this->appointment->loadMissing(['service', 'size']);

        return new Content(
            view: 'emails.appointment-confirmation',
            with: [
                'appointment' => $this->appointment,
                'businessName' => Setting::get('business_name', 'JNJ Auto Car Wash'),
                'businessPhone' => Setting::get('business_phone', '(+63) 919-123-4567'),
                'businessEmail' => Setting::get('business_email', 'info@jnjauto.com'),
                'businessAddress' => Setting::get('business_address', ''),
                'businessLogoUrl' => $this->businessLogoUrl(),
                'currency' => Setting::get('currency', 'PHP'),
            ],
        );
    }

    private function businessLogoUrl(): string
    {
        $logo = Setting::get('business_logo');

        return $logo ? asset('storage/'.$logo) : asset('images/logo.png');
    }
}
