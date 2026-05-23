<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\Role;
use App\Models\Service;
use App\Models\Size;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $this->seedRolesAndUsers();

            $services = $this->seedServices();
            $sizes = $this->seedSizes();
            $items = $this->seedInventory();

            $this->syncServiceInventory($services, $items);
            $created = $this->seedMonthlyAppointments($services, $sizes);

            $this->command->info(
                "Month seeded: {$created['appointments']} appointments, {$created['completed']} completed sales, inventory stocked with empty SKUs."
            );
        });
    }

    private function seedRolesAndUsers(): void
    {
        $roles = [
            ['name' => 'Customer', 'description' => 'Regular customer with no special permissions.'],
            ['name' => 'Admin', 'description' => 'Administrator with full permissions.'],
            ['name' => 'Staff', 'description' => 'Staff member with limited permissions.'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }

        $adminRole = Role::where('name', 'Admin')->first();
        $staffRole = Role::where('name', 'Staff')->first();

        User::updateOrCreate(
            ['email' => 'admin@jnj.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role_id' => $adminRole?->id]
        );

        User::updateOrCreate(
            ['email' => 'staff@jnj.com'],
            ['name' => 'Jonel Alimuin', 'password' => bcrypt('password'), 'role_id' => $staffRole?->id]
        );
    }

    private function seedServices()
    {
        $services = [
            'Basic Wash' => [
                'description' => 'Exterior hand wash, rinse, and towel dry.',
                'price' => 149.00,
            ],
            'Full Wash' => [
                'description' => 'Exterior wash, interior vacuum, tire black, and glass cleaning.',
                'price' => 299.00,
            ],
            'Premium Detail' => [
                'description' => 'Full detail with wax, interior cleaning, tire black, and finishing spray.',
                'price' => 599.00,
            ],
            'Engine Wash' => [
                'description' => 'Engine bay cleaning with degreaser and controlled rinse.',
                'price' => 399.00,
            ],
        ];

        foreach ($services as $name => $data) {
            Service::updateOrCreate(['name' => $name], array_merge(['name' => $name], $data));
        }

        return Service::whereIn('name', array_keys($services))->get()->keyBy('name');
    }

    private function seedSizes()
    {
        $sizes = [
            'Small' => ['description' => 'Sedan / Hatchback', 'multiplier' => 1.00],
            'Medium' => ['description' => 'SUV / Crossover / MPV', 'multiplier' => 1.50],
            'Large' => ['description' => 'Pickup / Van / Full-size SUV', 'multiplier' => 2.00],
        ];

        foreach ($sizes as $name => $data) {
            Size::updateOrCreate(['name' => $name], array_merge(['name' => $name], $data));
        }

        return Size::whereIn('name', array_keys($sizes))->get()->keyBy('name');
    }

    private function seedInventory()
    {
        InventoryLog::query()
            ->where(function ($query) {
                $query
                    ->where('notes', 'Initial stock from week seeder.')
                    ->orWhere('notes', 'Daily usage.')
                    ->orWhere('notes', 'Initial stock from month seed data.')
                    ->orWhere('notes', 'like', 'Seeded completed service:%');
            })
            ->delete();

        $categories = [
            'Cleaning Chemicals' => [
                'description' => 'Car wash liquids and detailing chemicals used during service work.',
                'items' => [
                    ['name' => 'Car Shampoo', 'description' => 'Foaming wash shampoo for exterior cleaning.', 'unit' => 'liters', 'cost' => 160.00, 'quantity' => 650, 'reorder_level' => 120],
                    ['name' => 'Tire Black', 'description' => 'Tire dressing for finished wash jobs.', 'unit' => 'liters', 'cost' => 540.00, 'quantity' => 320, 'reorder_level' => 60],
                    ['name' => 'Engine Degreaser', 'description' => 'Degreaser for engine bay and heavy grime.', 'unit' => 'liters', 'cost' => 260.00, 'quantity' => 260, 'reorder_level' => 50],
                    ['name' => 'All Purpose Cleaner', 'description' => 'Dilutable cleaner for wheel wells, mats, and interior surfaces.', 'unit' => 'liters', 'cost' => 210.00, 'quantity' => 420, 'reorder_level' => 80],
                    ['name' => 'Wax Polish', 'description' => 'Liquid wax polish for premium detail jobs.', 'unit' => 'liters', 'cost' => 580.00, 'quantity' => 180, 'reorder_level' => 35],
                    ['name' => 'Quick Detailing Wax', 'description' => 'Spray wax used as a final detailer.', 'unit' => 'bottles', 'cost' => 95.00, 'quantity' => 240, 'reorder_level' => 45],
                    ['name' => 'Interior Freshener', 'description' => 'Interior fragrance used after cleaning.', 'unit' => 'bottles', 'cost' => 75.00, 'quantity' => 260, 'reorder_level' => 50],
                    ['name' => 'Glass Cleaner', 'description' => 'Windshield and window cleaner.', 'unit' => 'bottles', 'cost' => 90.00, 'quantity' => 240, 'reorder_level' => 45],
                    ['name' => 'Wiper Wash', 'description' => 'Ready-to-use windshield washer fluid.', 'unit' => 'liters', 'cost' => 110.00, 'quantity' => 160, 'reorder_level' => 30],
                ],
            ],
            'Supplies' => [
                'description' => 'Reusable and consumable towels, pads, mitts, and hand tools.',
                'items' => [
                    ['name' => 'Microfiber Towels', 'description' => 'Automotive microfiber towels for drying and buffing.', 'unit' => 'pcs', 'cost' => 45.00, 'quantity' => 420, 'reorder_level' => 90],
                    ['name' => 'Synthetic Chamois', 'description' => 'Synthetic chamois cloths for drying panels and glass.', 'unit' => 'pcs', 'cost' => 130.00, 'quantity' => 60, 'reorder_level' => 15],
                    ['name' => 'Sponge Pads', 'description' => 'Large sponge pads for exterior hand washing.', 'unit' => 'pcs', 'cost' => 55.00, 'quantity' => 90, 'reorder_level' => 25],
                    ['name' => 'Wash Mitt', 'description' => 'Microfiber wash mitts for safer contact washing.', 'unit' => 'pcs', 'cost' => 150.00, 'quantity' => 45, 'reorder_level' => 12],
                    ['name' => 'All-purpose Rags', 'description' => 'General rags for lower panels, door jambs, and utility cleaning.', 'unit' => 'pcs', 'cost' => 12.00, 'quantity' => 520, 'reorder_level' => 120],
                    ['name' => 'Detailing Brushes', 'description' => 'Small brushes for vents, badges, and tight trim areas.', 'unit' => 'pcs', 'cost' => 95.00, 'quantity' => 50, 'reorder_level' => 10],
                ],
            ],
            'Equipment' => [
                'description' => 'Replacement equipment parts and accessories used by the wash bay.',
                'items' => [
                    ['name' => 'Pressure Washer Nozzle', 'description' => 'Replacement nozzle tips for pressure washer use.', 'unit' => 'pcs', 'cost' => 480.00, 'quantity' => 10, 'reorder_level' => 3],
                    ['name' => 'Vacuum Filter Bag', 'description' => 'Replacement filter bags for shop vacuum units.', 'unit' => 'pcs', 'cost' => 210.00, 'quantity' => 28, 'reorder_level' => 8],
                    ['name' => 'Foam Cannon Bottle', 'description' => 'Foam cannon bottle for shampoo pre-wash.', 'unit' => 'pcs', 'cost' => 650.00, 'quantity' => 8, 'reorder_level' => 2],
                    ['name' => 'Spray Bottle', 'description' => 'Trigger bottles for cleaners and diluted chemicals.', 'unit' => 'pcs', 'cost' => 45.00, 'quantity' => 80, 'reorder_level' => 20],
                    ['name' => 'Hose Connector', 'description' => 'Quick-connect fittings for wash hoses.', 'unit' => 'pcs', 'cost' => 120.00, 'quantity' => 20, 'reorder_level' => 5],
                ],
            ],
        ];

        foreach ($categories as $categoryName => $categoryData) {
            $category = InventoryCategory::updateOrCreate(
                ['name' => $categoryName],
                ['name' => $categoryName, 'description' => $categoryData['description']]
            );

            foreach ($categoryData['items'] as $itemData) {
                $item = InventoryItem::firstOrNew(['name' => $itemData['name']]);
                $item->fill(array_merge($itemData, [
                    'category_id' => $category->id,
                    'sku' => null,
                ]));
                $item->save();

                $item->refreshStatus();

                InventoryLog::create([
                    'item_id' => $item->id,
                    'user_id' => User::where('email', 'staff@jnj.com')->value('id'),
                    'type' => 'stock_in',
                    'quantity' => $item->quantity,
                    'quantity_before' => 0,
                    'quantity_after' => $item->quantity,
                    'notes' => 'Initial stock from month seed data.',
                ]);
            }
        }

        return InventoryItem::whereIn('name', collect($categories)->flatMap(fn ($category) => collect($category['items'])->pluck('name'))->all())
            ->orderBy('id')
            ->get()
            ->unique('name')
            ->keyBy('name');
    }

    private function syncServiceInventory($services, $items): void
    {
        $serviceInventory = [
            'Basic Wash' => [
                'Car Shampoo' => 1,
                'All-purpose Rags' => 1,
            ],
            'Full Wash' => [
                'Car Shampoo' => 1,
                'All Purpose Cleaner' => 1,
                'Tire Black' => 1,
                'Glass Cleaner' => 1,
                'Interior Freshener' => 1,
            ],
            'Premium Detail' => [
                'Car Shampoo' => 2,
                'Wax Polish' => 1,
                'Quick Detailing Wax' => 1,
                'Tire Black' => 1,
                'Glass Cleaner' => 1,
                'Microfiber Towels' => 1,
            ],
            'Engine Wash' => [
                'Engine Degreaser' => 2,
                'All Purpose Cleaner' => 1,
            ],
        ];

        foreach ($serviceInventory as $serviceName => $inventoryRows) {
            $service = $services->get($serviceName);
            if (! $service) {
                continue;
            }

            $payload = [];
            foreach ($inventoryRows as $itemName => $quantity) {
                $item = $items->get($itemName);
                if ($item) {
                    $payload[$item->id] = ['quantity_per_service' => $quantity];
                }
            }

            $service->inventoryItems()->sync($payload);
        }
    }

    private function seedMonthlyAppointments($services, $sizes): array
    {
        $today = Carbon::today();
        $startDay = $today->copy()->startOfMonth();
        $endDay = $today->copy()->endOfMonth();

        $this->removePreviousSeededAppointments($startDay, $endDay);

        $timeSlots = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '11:00', '11:30', '13:00', '13:30', '14:00', '14:30',
            '15:00', '15:30', '16:00', '16:30',
        ];

        $plates = [
            'NAA 4821', 'NBD 7194', 'NCE 3086', 'NDR 6429', 'NEH 1753',
            'NFK 9042', 'NGM 2675', 'NHP 5318', 'NJR 8460', 'NKS 1937',
            'NLB 6084', 'NMC 2749', 'NNF 7352', 'NPG 4106', 'NQH 9521',
            'NRJ 3864', 'NSK 6207', 'NTM 1748', 'NVP 8093', 'NWR 2456',
            'DAB 5917', 'DAE 8362', 'DAH 2704', 'DAK 9148', 'DAM 4635',
            'DAP 7281', 'DAR 1596', 'DAT 6824', 'DAV 3079', 'DAX 9450',
            'YAA 5216', 'YBC 8742', 'YDE 3195', 'YFG 6408', 'YHJ 2861',
            'YKL 9573', 'YMN 4027', 'YPQ 7318', 'YRS 1684', 'YTV 5940',
        ];

        $serviceNames = $services->keys()->values()->all();
        $sizeNames = $sizes->keys()->values()->all();
        $pastStatusPattern = [
            'completed', 'completed', 'completed', 'completed', 'completed',
            'completed', 'completed', 'cancelled', 'completed', 'no_show',
        ];

        $appointmentCount = 0;
        $completedCount = 0;

        for ($date = $startDay->copy(), $dayIndex = 0; $date->lte($endDay); $date->addDay(), $dayIndex++) {
            $dailyCount = $this->dailyAppointmentCount($date, $dayIndex);
            $daySlots = collect($timeSlots)
                ->slice($dayIndex % 3)
                ->take($dailyCount)
                ->values();

            foreach ($daySlots as $slotIndex => $time) {
                $serviceName = $serviceNames[($dayIndex + $slotIndex) % count($serviceNames)];
                $sizeName = $sizeNames[($dayIndex + ($slotIndex * 2)) % count($sizeNames)];
                $service = $services->get($serviceName);
                $size = $sizes->get($sizeName);
                $plate = $plates[($appointmentCount + $dayIndex) % count($plates)];
                $status = $this->appointmentStatus($date, $time, $today, $pastStatusPattern, $dayIndex, $slotIndex);
                $amount = null;
                $completedAt = null;

                if ($status === 'completed') {
                    $amount = round(((float) $service->price) * ((float) $size->multiplier), 2);
                    $completedAt = $date->copy()
                        ->setTimeFromTimeString($time)
                        ->addMinutes($this->serviceDuration($serviceName, (float) $size->multiplier));
                }

                $appointment = Appointment::create([
                    'date' => $date->toDateString(),
                    'time' => $time,
                    'service_id' => $service->id,
                    'size_id' => $size->id,
                    'customer_name' => '-',
                    'customer_email' => null,
                    'customer_phone' => null,
                    'notes' => $this->appointmentNote($plate, $serviceName, $sizeName, $appointmentCount),
                    'status' => $status,
                    'amount' => $amount,
                    'completed_at' => $completedAt,
                ]);

                if ($status === 'completed') {
                    $this->consumeInventoryForCompletedAppointment($appointment);
                    $completedCount++;
                }

                $appointmentCount++;
            }
        }

        return [
            'appointments' => $appointmentCount,
            'completed' => $completedCount,
        ];
    }

    private function removePreviousSeededAppointments(Carbon $startDay, Carbon $endDay): void
    {
        $seededAppointmentIds = Appointment::query()
            ->whereBetween('date', [$startDay->toDateString(), $endDay->toDateString()])
            ->where(function ($query) {
                $query
                    ->where('customer_name', '-')
                    ->orWhere('customer_email', 'sample@email.com');
            })
            ->pluck('id');

        if ($seededAppointmentIds->isEmpty()) {
            return;
        }

        InventoryLog::query()
            ->where('reference_type', 'appointment')
            ->whereIn('reference_id', $seededAppointmentIds)
            ->delete();

        Appointment::whereKey($seededAppointmentIds)->delete();
    }

    private function dailyAppointmentCount(Carbon $date, int $dayIndex): int
    {
        $base = match (true) {
            $date->isSunday() => 5,
            $date->isSaturday() => 8,
            default => 10,
        };

        $variation = [0, 1, -1, 2, 0, -2, 1][$dayIndex % 7];

        return max(4, min(12, $base + $variation));
    }

    private function appointmentStatus(
        Carbon $date,
        string $time,
        Carbon $today,
        array $pastStatusPattern,
        int $dayIndex,
        int $slotIndex
    ): string {
        if ($date->gt($today)) {
            return 'scheduled';
        }

        if ($date->isSameDay($today)) {
            $slotHour = (int) substr($time, 0, 2);
            $currentHour = (int) now()->format('H');

            if ($slotHour > $currentHour) {
                return 'scheduled';
            }

            if ($slotHour === $currentHour) {
                return 'in_progress';
            }
        }

        return $pastStatusPattern[($dayIndex + $slotIndex) % count($pastStatusPattern)];
    }

    private function serviceDuration(string $serviceName, float $sizeMultiplier): int
    {
        $baseMinutes = [
            'Basic Wash' => 35,
            'Full Wash' => 55,
            'Premium Detail' => 110,
            'Engine Wash' => 65,
        ][$serviceName] ?? 45;

        return (int) round($baseMinutes * max(1, $sizeMultiplier));
    }

    private function appointmentNote(string $plate, string $serviceName, string $sizeName, int $index): string
    {
        $templates = [
            'Plate %s. Walk-in %s for %s vehicle; customer will return after service.',
            'Plate %s. Focus on wheel wells and lower panels; muddy from provincial road.',
            'Plate %s. Do not move dashboard items; interior belongings left in place.',
            'Plate %s. Company unit; release after staff inspection.',
            'Plate %s. Customer requested extra care on rims and side mirrors.',
            'Plate %s. Light interior odor; apply freshener after cleaning.',
            'Plate %s. Check windshield and wiper area before turnover.',
            'Plate %s. Repeat vehicle from monthly operations list.',
        ];

        return sprintf($templates[$index % count($templates)], $plate, $serviceName, strtolower($sizeName));
    }

    private function consumeInventoryForCompletedAppointment(Appointment $appointment): void
    {
        $service = $appointment->service()->with('inventoryItems')->first();
        if (! $service) {
            return;
        }

        $staffUserId = User::where('email', 'staff@jnj.com')->value('id');
        $multiplier = (float) ($appointment->size?->multiplier ?? 1);

        foreach ($service->inventoryItems as $item) {
            $used = max(1, (int) round(((float) $item->pivot->quantity_per_service) * $multiplier));
            $before = $item->quantity;
            $item->quantity = $before - $used;
            $item->save();
            $item->refreshStatus();

            InventoryLog::create([
                'item_id' => $item->id,
                'user_id' => $staffUserId,
                'type' => 'stock_out',
                'quantity' => -$used,
                'quantity_before' => $before,
                'quantity_after' => $item->quantity,
                'notes' => 'Seeded completed service: '.$service->name,
                'reference_type' => 'appointment',
                'reference_id' => $appointment->id,
            ]);
        }
    }
}
