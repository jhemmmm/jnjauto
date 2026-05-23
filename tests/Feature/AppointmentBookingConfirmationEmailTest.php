<?php

namespace Tests\Feature;

use App\Mail\AppointmentConfirmation;
use App\Mail\AppointmentStatusUpdated;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Size;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AppointmentBookingConfirmationEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_receives_confirmation_email_after_booking_an_appointment(): void
    {
        Mail::fake();

        $service = Service::create([
            'name' => 'Full Wash',
            'description' => 'Exterior and interior cleaning package.',
            'price' => 299,
        ]);
        $size = Size::create([
            'name' => 'Small',
            'description' => 'Sedan / Hatchback',
            'multiplier' => 1,
        ]);

        $response = $this->postJson(route('appointment.api.put'), [
            'date' => '2026-05-25',
            'time' => '09:00',
            'service' => $service->id,
            'size' => $size->id,
            'name' => 'Juan Dela Cruz',
            'email' => 'juan@example.com',
            'phone' => '09171234567',
            'notes' => 'NAA 4821',
        ]);

        $response->assertOk();

        $appointment = Appointment::firstOrFail();

        Mail::assertSent(AppointmentConfirmation::class, function (AppointmentConfirmation $mail) use ($appointment) {
            return $mail->hasTo('juan@example.com')
                && $mail->appointment->is($appointment);
        });
    }

    public function test_customer_receives_email_when_appointment_status_changes(): void
    {
        Mail::fake();

        $user = User::factory()->create(['role_id' => 2]);
        $service = Service::create([
            'name' => 'Full Wash',
            'description' => 'Exterior and interior cleaning package.',
            'price' => 299,
        ]);
        $size = Size::create([
            'name' => 'Small',
            'description' => 'Sedan / Hatchback',
            'multiplier' => 1,
        ]);
        $appointment = Appointment::create([
            'date' => '2026-05-25',
            'time' => '09:00:00',
            'service_id' => $service->id,
            'size_id' => $size->id,
            'customer_name' => 'Juan Dela Cruz',
            'customer_email' => 'juan@example.com',
            'customer_phone' => '09171234567',
            'notes' => 'NAA 4821',
            'status' => 'scheduled',
        ]);

        $response = $this
            ->actingAs($user)
            ->patchJson(route('panel.api.appointments.status', $appointment), [
                'status' => 'completed',
            ]);

        $response->assertOk();
        $this->assertSame('completed', $appointment->refresh()->status);

        Mail::assertSent(AppointmentStatusUpdated::class, function (AppointmentStatusUpdated $mail) use ($appointment) {
            return $mail->hasTo('juan@example.com')
                && $mail->appointment->is($appointment);
        });
    }
}
