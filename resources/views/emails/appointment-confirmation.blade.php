<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Appointment Confirmed</title>
</head>
<body style="margin: 0; padding: 0; background: #f3f7fb; font-family: Arial, Helvetica, sans-serif; color: #1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background: #f3f7fb; padding: 28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; background: #ffffff; border: 1px solid #dbeafe; border-radius: 18px; overflow: hidden;">
                    <tr>
                        <td style="background: #1b88bb; padding: 26px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="104" valign="middle">
                                        <span style="display: inline-block; padding: 8px; background: #ffffff; border-radius: 16px;">
                                            <img src="{{ $businessLogoUrl }}" width="72" alt="{{ $businessName }} logo" style="display: block; width: 72px; max-width: 72px; height: auto; border: 0;">
                                        </span>
                                    </td>
                                    <td valign="middle" style="color: #ffffff;">
                                        <div style="font-size: 13px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; opacity: 0.9;">{{ $businessName }}</div>
                                        <div style="font-size: 25px; line-height: 1.2; font-weight: 800; margin-top: 4px;">Appointment Confirmed</div>
                                        <div style="font-size: 14px; line-height: 1.5; margin-top: 6px; opacity: 0.9;">Your booking is now on our schedule.</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px 28px 8px;">
                            <p style="margin: 0 0 12px; font-size: 16px;">Hello {{ $appointment->customer_name }},</p>
                            <p style="margin: 0; color: #4b5563; font-size: 15px; line-height: 1.7;">
                                Thank you for booking with <strong>{{ $businessName }}</strong>. Please review your appointment details below.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 18px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e5eef5; border-radius: 14px; overflow: hidden;">
                                <tr>
                                    <td colspan="2" style="background: #eff8fc; padding: 14px 18px; color: #0f6f99; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em;">Booking Details</td>
                                </tr>
                                <tr>
                                    <td style="width: 38%; padding: 14px 18px; border-bottom: 1px solid #edf2f7; color: #64748b; font-size: 13px; font-weight: 700;">Service</td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; font-size: 14px; font-weight: 700;">{{ $appointment->service?->name ?? 'Service' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; color: #64748b; font-size: 13px; font-weight: 700;">Vehicle Size</td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; font-size: 14px; font-weight: 700;">{{ $appointment->size?->name ?? 'Vehicle' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; color: #64748b; font-size: 13px; font-weight: 700;">Date</td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; font-size: 14px;">{{ $appointment->date?->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; color: #64748b; font-size: 13px; font-weight: 700;">Time</td>
                                    <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; font-size: 14px;">{{ \Carbon\Carbon::parse($appointment->time)->format('g:i A') }}</td>
                                </tr>
                                @if (! is_null($appointment->amount))
                                    <tr>
                                        <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; color: #64748b; font-size: 13px; font-weight: 700;">Amount</td>
                                        <td style="padding: 14px 18px; border-bottom: 1px solid #edf2f7; color: #1b88bb; font-size: 15px; font-weight: 800;">{{ $currency }} {{ number_format((float) $appointment->amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if ($appointment->notes)
                                    <tr>
                                        <td style="padding: 14px 18px; color: #64748b; font-size: 13px; font-weight: 700;">Notes</td>
                                        <td style="padding: 14px 18px; font-size: 14px;">{{ $appointment->notes }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 4px 28px 30px;">
                            <div style="background: #f8fafc; border-radius: 14px; padding: 16px 18px; color: #4b5563; font-size: 13px; line-height: 1.7;">
                                <strong style="color: #1f2937;">Need help?</strong><br>
                                @if ($businessPhone)
                                    Contact: {{ $businessPhone }}<br>
                                @endif
                                @if ($businessEmail)
                                    Email: {{ $businessEmail }}<br>
                                @endif
                                @if ($businessAddress)
                                    Address: {{ $businessAddress }}
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 18px 28px; background: #f8fbfd; color: #64748b; font-size: 12px; text-align: center;">
                            Thank you for choosing {{ $businessName }}.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
