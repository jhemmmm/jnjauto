<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AppointmentController extends Controller
{
    //

    public function index()
    {
        return view('appointment');
    }

    /**
     * Return booking configuration from settings.
     */
    public function config()
    {
        $openingTime = Setting::get('opening_time', '07:00');
        $closingTime = Setting::get('closing_time', '17:00');

        return response()->json([
            'open_hour' => (int) substr($openingTime, 0, 2),
            'open_minute' => (int) substr($openingTime, 3, 2),
            'close_hour' => (int) substr($closingTime, 0, 2),
            'close_minute' => (int) substr($closingTime, 3, 2),
            'slot_minutes' => (int) Setting::get('slot_duration', 30),
            'slot_capacity' => (int) Setting::get('slot_capacity', 2),
            'business_name' => Setting::get('business_name', 'JNJ Auto Car Wash'),
            'business_phone' => Setting::get('business_phone', '(555) 123-4567'),
            'currency' => Setting::get('currency', 'PHP'),
        ]);
    }

    public function get(Request $request)
    {
        $date = $request->input('date');

        $appointments = Appointment::where('date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        return response()->json($appointments);
    }

    public function put(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'service' => 'required|integer',
            'size' => 'required|integer',
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Validate time is within operating hours
        $openingTime = Setting::get('opening_time', '07:00');
        $closingTime = Setting::get('closing_time', '17:00');
        $requestTime = $request->input('time');

        if ($requestTime < $openingTime || $requestTime >= $closingTime) {
            return response()->json(['message' => 'Selected time is outside operating hours ('.$openingTime.' – '.$closingTime.').'], 422);
        }

        // Validate slot capacity
        $slotCapacity = (int) Setting::get('slot_capacity', 2);
        $existingCount = Appointment::where('date', $request->input('date'))
            ->where('time', sprintf('%s:00', $requestTime))
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($existingCount >= $slotCapacity) {
            return response()->json(['message' => 'This time slot is already full. Please choose another.'], 422);
        }

        $appointment = Appointment::create([
            'date' => $request->input('date'),
            'time' => sprintf('%s:00', $request->input('time')),
            'service_id' => $request->input('service'),
            'size_id' => $request->input('size'),
            'customer_name' => $request->input('name'),
            'customer_email' => $request->input('email'),
            'customer_phone' => $request->input('phone'),
            'notes' => $request->input('notes'),
        ]);

        $appointment->load(['service', 'size']);

        Notification::notifyAdmins(
            'appointment_created',
            'New Appointment Booked',
            $appointment->customer_name.' booked a '.($appointment->service->name ?? 'service').' for '.\Carbon\Carbon::parse($appointment->date)->format('M d, Y').' at '.\Carbon\Carbon::parse($appointment->time)->format('g:i A').'.',
            'fa-solid fa-calendar-plus',
            'primary',
            '/panel/appointments',
            ['appointment_id' => $appointment->id]
        );

        Mail::to($appointment->customer_email)->send(new AppointmentConfirmation($appointment));

        return response()->json($appointment);
    }
}
