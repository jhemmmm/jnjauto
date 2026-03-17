<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\Service;
use App\Models\Size;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class WeekSeeder extends Seeder
{
    /**
     * Seed a realistic week of car-wash data:
     * services, sizes, customers, appointments (7 days), and inventory usage.
     */
    public function run(): void
    {
        /* ──────────────────────────────────
         |  Services
         * ────────────────────────────────── */
        $services = [
            ['name' => 'Basic Wash',      'description' => 'Exterior hand wash and towel dry.',         'price' => 149.00],
            ['name' => 'Full Wash',        'description' => 'Exterior and interior cleaning package.',   'price' => 299.00],
            ['name' => 'Premium Detail',   'description' => 'Full detail with wax and interior shampoo.','price' => 599.00],
            ['name' => 'Engine Wash',      'description' => 'Engine bay cleaning and degreasing.',       'price' => 399.00],
        ];

        foreach ($services as $s) {
            Service::firstOrCreate(['name' => $s['name']], $s);
        }
        $serviceIds = Service::pluck('id', 'name');

        /* ──────────────────────────────────
         |  Sizes
         * ────────────────────────────────── */
        $sizes = [
            ['name' => 'Small',   'description' => 'Sedan / Hatchback',            'multiplier' => 1.00],
            ['name' => 'Medium',  'description' => 'SUV / Crossover',              'multiplier' => 1.50],
            ['name' => 'Large',   'description' => 'Truck / Van / Full-size SUV',  'multiplier' => 2.00],
        ];

        foreach ($sizes as $sz) {
            Size::firstOrCreate(['name' => $sz['name']], $sz);
        }
        $sizeIds = Size::pluck('id', 'name');

        /* ──────────────────────────────────
         |  Staff / Customers
         * ────────────────────────────────── */
        User::firstOrCreate(
            ['email' => 'staff@jnj.com'],
            ['name' => 'Mike Staff', 'password' => bcrypt('password'), 'role_id' => 3]
        );

        $customers = [
            ['name' => 'Juan Dela Cruz',    'email' => 'juan@email.com',    'phone' => '0917-111-2222'],
            ['name' => 'Maria Santos',      'email' => 'maria@email.com',   'phone' => '0918-222-3333'],
            ['name' => 'Carlos Reyes',      'email' => 'carlos@email.com',  'phone' => '0919-333-4444'],
            ['name' => 'Ana Lopez',         'email' => 'ana@email.com',     'phone' => '0920-444-5555'],
            ['name' => 'Pedro Garcia',      'email' => 'pedro@email.com',   'phone' => '0921-555-6666'],
            ['name' => 'Rosa Mendoza',      'email' => 'rosa@email.com',    'phone' => '0922-666-7777'],
            ['name' => 'Luis Ramos',        'email' => 'luis@email.com',    'phone' => '0923-777-8888'],
            ['name' => 'Sofia Cruz',        'email' => 'sofia@email.com',   'phone' => '0924-888-9999'],
            ['name' => 'Marco Villanueva',  'email' => 'marco@email.com',   'phone' => '0925-999-0000'],
            ['name' => 'Elena Torres',      'email' => 'elena@email.com',   'phone' => '0926-000-1111'],
        ];

        /* ──────────────────────────────────
         |  Inventory
         * ────────────────────────────────── */
        $categories = [
            'Cleaning Chemicals' => [
                ['name' => 'Car Shampoo',       'sku' => 'CHEM-001', 'unit' => 'liters',  'cost' => 85.00,  'quantity' => 40, 'reorder_level' => 10],
                ['name' => 'Tire Black',         'sku' => 'CHEM-002', 'unit' => 'liters',  'cost' => 120.00, 'quantity' => 15, 'reorder_level' => 5],
                ['name' => 'Engine Degreaser',   'sku' => 'CHEM-003', 'unit' => 'liters',  'cost' => 150.00, 'quantity' => 12, 'reorder_level' => 5],
                ['name' => 'Interior Freshener', 'sku' => 'CHEM-004', 'unit' => 'bottles', 'cost' => 65.00,  'quantity' => 25, 'reorder_level' => 8],
                ['name' => 'Wax Polish',         'sku' => 'CHEM-005', 'unit' => 'bottles', 'cost' => 220.00, 'quantity' => 8,  'reorder_level' => 3],
            ],
            'Supplies' => [
                ['name' => 'Microfiber Towels',  'sku' => 'SUP-001',  'unit' => 'pcs',     'cost' => 35.00,  'quantity' => 50, 'reorder_level' => 15],
                ['name' => 'Sponge Pads',        'sku' => 'SUP-002',  'unit' => 'pcs',     'cost' => 20.00,  'quantity' => 30, 'reorder_level' => 10],
                ['name' => 'Glass Cleaner',      'sku' => 'SUP-003',  'unit' => 'bottles', 'cost' => 55.00,  'quantity' => 18, 'reorder_level' => 5],
            ],
            'Equipment' => [
                ['name' => 'Pressure Washer Nozzle', 'sku' => 'EQP-001', 'unit' => 'pcs', 'cost' => 450.00, 'quantity' => 4, 'reorder_level' => 2],
                ['name' => 'Vacuum Filter Bag',      'sku' => 'EQP-002', 'unit' => 'pcs', 'cost' => 180.00, 'quantity' => 6, 'reorder_level' => 3],
            ],
        ];

        $staffUser = User::where('email', 'staff@jnj.com')->first();

        foreach ($categories as $catName => $items) {
            $cat = InventoryCategory::firstOrCreate(['name' => $catName]);
            foreach ($items as $itemData) {
                $item = InventoryItem::firstOrCreate(
                    ['sku' => $itemData['sku']],
                    array_merge($itemData, ['category_id' => $cat->id])
                );

                // initial stock-in log
                if ($item->wasRecentlyCreated) {
                    InventoryLog::create([
                        'item_id'         => $item->id,
                        'user_id'         => $staffUser?->id,
                        'type'            => 'stock_in',
                        'quantity'         => $item->quantity,
                        'quantity_before'  => 0,
                        'quantity_after'   => $item->quantity,
                        'notes'           => 'Initial stock from week seeder.',
                    ]);
                }
            }
        }

        /* ──────────────────────────────────
         |  Appointments – 7 days
         * ────────────────────────────────── */
        $today    = Carbon::today();
        $startDay = $today->copy()->subDays(6);  // 7 days including today

        $timeSlots = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30',
        ];

        $serviceNames  = array_keys($serviceIds->toArray());
        $sizeNames     = array_keys($sizeIds->toArray());
        $statuses      = ['completed', 'completed', 'completed', 'completed', 'cancelled', 'no_show']; // weighted toward completed
        $notesPool     = [
            null,
            'Regular customer.',
            'Please be careful with the rims.',
            'Has a baby seat - do not remove.',
            'Customer will wait on-site.',
            'Company fleet vehicle.',
            'Gift card payment.',
            null,
        ];

        $created = 0;

        for ($d = 0; $d < 7; $d++) {
            $date       = $startDay->copy()->addDays($d);
            $isToday    = $date->isToday();
            $isSunday   = $date->isSunday();

            // Fewer appointments on Sundays
            $count = $isSunday ? rand(3, 5) : rand(6, 10);

            // Pick random time slots for this day
            $daySlots = collect($timeSlots)->shuffle()->take($count)->sort()->values();

            foreach ($daySlots as $i => $time) {
                $cust    = $customers[array_rand($customers)];
                $svcName = $serviceNames[array_rand($serviceNames)];
                $szName  = $sizeNames[array_rand($sizeNames)];
                $service = Service::where('name', $svcName)->first();
                $size    = Size::where('name', $szName)->first();
                $price   = round(($service->price ?? 149.00) * ($size->multiplier ?? 1.00), 2);

                // Today's appointments: mix of scheduled, in_progress, completed
                if ($isToday) {
                    $currentHour = (int) now()->format('H');
                    $slotHour    = (int) substr($time, 0, 2);

                    if ($slotHour > $currentHour) {
                        $status = 'scheduled';
                    } elseif ($slotHour == $currentHour) {
                        $status = 'in_progress';
                    } else {
                        $status = (rand(1, 10) <= 8) ? 'completed' : 'cancelled';
                    }
                } else {
                    // Past days — mostly completed
                    $status = $statuses[array_rand($statuses)];
                }

                $completedAt = null;
                $amount      = null;

                if ($status === 'completed') {
                    $amount      = $price;
                    $completedAt = $date->copy()
                        ->setTimeFromTimeString($time)
                        ->addMinutes(rand(25, 55));
                }

                Appointment::create([
                    'date'           => $date->toDateString(),
                    'time'           => $time,
                    'service_id'     => $serviceIds[$svcName],
                    'size_id'        => $sizeIds[$szName],
                    'customer_name'  => $cust['name'],
                    'customer_email' => $cust['email'],
                    'customer_phone' => $cust['phone'],
                    'notes'          => $notesPool[array_rand($notesPool)],
                    'status'         => $status,
                    'amount'         => $amount,
                    'completed_at'   => $completedAt,
                ]);

                $created++;
            }
        }

        /* ──────────────────────────────────
         |  Inventory usage logs for the week
         * ────────────────────────────────── */
        $shampoo  = InventoryItem::where('sku', 'CHEM-001')->first();
        $towels   = InventoryItem::where('sku', 'SUP-001')->first();
        $tireBlk  = InventoryItem::where('sku', 'CHEM-002')->first();

        $usageItems = array_filter([$shampoo, $towels, $tireBlk]);

        foreach ($usageItems as $item) {
            for ($d = 6; $d >= 1; $d--) {
                $usedQty = rand(1, 3);
                $before  = $item->quantity;
                $after   = max(0, $before - $usedQty);

                InventoryLog::create([
                    'item_id'        => $item->id,
                    'user_id'        => $staffUser?->id,
                    'type'           => 'stock_out',
                    'quantity'        => $usedQty,
                    'quantity_before' => $before,
                    'quantity_after'  => $after,
                    'notes'          => 'Daily usage.',
                    'created_at'     => now()->subDays($d),
                    'updated_at'     => now()->subDays($d),
                ]);

                $item->quantity = $after;
            }

            // persist final quantity and refresh status
            $item->save();
            $item->refreshStatus();
        }

        $this->command->info("✅ Week seeded: {$created} appointments, inventory stocked & used.");
    }
}
