<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Seed sample notifications.
     */
    public function run(): void
    {
        $admin = User::where('role_id', 2)->first();

        if (!$admin) {
            return;
        }

        $notifications = [
            [
                'type'       => 'appointment_created',
                'title'      => 'New Appointment Booked',
                'message'    => 'John Doe booked a Full Wash for Mar 10, 2026 at 9:00 AM.',
                'icon'       => 'fa-solid fa-calendar-plus',
                'icon_color' => 'primary',
                'link'       => '/panel/appointments',
                'created_at' => now()->subMinutes(5),
            ],
            [
                'type'       => 'appointment_completed',
                'title'      => 'Appointment Completed',
                'message'    => 'Jane Smith\'s appointment has been marked as completed.',
                'icon'       => 'fa-solid fa-circle-check',
                'icon_color' => 'success',
                'link'       => '/panel/appointments',
                'created_at' => now()->subMinutes(30),
            ],
            [
                'type'       => 'low_stock',
                'title'      => 'Low Stock Alert',
                'message'    => 'Car Shampoo is running low — only 3 bottles remaining.',
                'icon'       => 'fa-solid fa-triangle-exclamation',
                'icon_color' => 'warning',
                'link'       => '/panel/inventory',
                'created_at' => now()->subHours(1),
            ],
            [
                'type'       => 'appointment_cancelled',
                'title'      => 'Appointment Cancelled',
                'message'    => 'Mike Johnson\'s appointment has been marked as cancelled.',
                'icon'       => 'fa-solid fa-circle-xmark',
                'icon_color' => 'danger',
                'link'       => '/panel/appointments',
                'created_at' => now()->subHours(2),
                'read_at'    => now()->subHours(1),
            ],
            [
                'type'       => 'out_of_stock',
                'title'      => 'Out of Stock',
                'message'    => 'Microfiber Towels is now out of stock!',
                'icon'       => 'fa-solid fa-box-open',
                'icon_color' => 'danger',
                'link'       => '/panel/inventory',
                'created_at' => now()->subHours(3),
            ],
            [
                'type'       => 'appointment_created',
                'title'      => 'New Appointment Booked',
                'message'    => 'Sarah Connor booked an Interior Cleaning for Mar 11, 2026 at 2:00 PM.',
                'icon'       => 'fa-solid fa-calendar-plus',
                'icon_color' => 'primary',
                'link'       => '/panel/appointments',
                'created_at' => now()->subHours(5),
                'read_at'    => now()->subHours(4),
            ],
            [
                'type'       => 'appointment_in_progress',
                'title'      => 'Appointment In Progress',
                'message'    => 'David Lee\'s appointment has been marked as in progress.',
                'icon'       => 'fa-solid fa-spinner',
                'icon_color' => 'warning',
                'link'       => '/panel/appointments',
                'created_at' => now()->subHours(6),
                'read_at'    => now()->subHours(5),
            ],
            [
                'type'       => 'low_stock',
                'title'      => 'Low Stock Alert',
                'message'    => 'Wheel Cleaner Spray is running low — only 5 units remaining.',
                'icon'       => 'fa-solid fa-triangle-exclamation',
                'icon_color' => 'warning',
                'link'       => '/panel/inventory',
                'created_at' => now()->subDay(),
                'read_at'    => now()->subHours(20),
            ],
        ];

        foreach ($notifications as $data) {
            Notification::create(array_merge($data, [
                'user_id' => $admin->id,
            ]));
        }
    }
}
