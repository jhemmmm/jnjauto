<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function envelope(): Envelope
    {
        $businessName = Setting::get('business_name', 'JNJ Auto Car Wash');

        return new Envelope(
            subject: 'Appointment Status Updated - '.$businessName,
        );
    }

    public function content(): Content
    {
        $this->appointment->loadMissing(['service', 'size']);

        return new Content(
            view: 'emails.appointment-status-updated',
            with: [
                'appointment' => $this->appointment,
                'statusLabel' => str($this->appointment->status)->replace('_', ' ')->title(),
                'businessName' => Setting::get('business_name', 'JNJ Auto Car Wash'),
                'businessPhone' => Setting::get('business_phone', '(+63) 919-123-4567'),
                'businessAddress' => Setting::get('business_address', ''),
                'currency' => Setting::get('currency', 'PHP'),
            ],
        );
    }
}
