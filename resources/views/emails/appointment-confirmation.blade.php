<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Appointment Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h1 style="font-size: 22px; margin-bottom: 12px;">Appointment Confirmed</h1>

    <p>Hello {{ $appointment->customer_name }},</p>

    <p>Your appointment with {{ $businessName }} has been booked.</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="font-weight: bold;">Service</td>
            <td>{{ $appointment->service?->name ?? 'Service' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Vehicle Size</td>
            <td>{{ $appointment->size?->name ?? 'Vehicle' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Date</td>
            <td>{{ $appointment->date?->format('F d, Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Time</td>
            <td>{{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}</td>
        </tr>
        @if (! is_null($appointment->amount))
            <tr>
                <td style="font-weight: bold;">Amount</td>
                <td>{{ $currency }} {{ number_format((float) $appointment->amount, 2) }}</td>
            </tr>
        @endif
        @if ($appointment->notes)
            <tr>
                <td style="font-weight: bold;">Notes</td>
                <td>{{ $appointment->notes }}</td>
            </tr>
        @endif
    </table>

    @if ($businessPhone || $businessAddress)
        <p>
            @if ($businessPhone)
                Contact: {{ $businessPhone }}<br>
            @endif
            @if ($businessAddress)
                Address: {{ $businessAddress }}
            @endif
        </p>
    @endif

    <p>Thank you.</p>
</body>
</html>
