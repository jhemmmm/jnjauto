<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\InventoryItem;
use App\Models\InventoryLog;
use App\Models\Service;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_database_seeder_creates_limited_march_demo_data(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-23 10:15:00'));

        $this->seed(DatabaseSeeder::class);

        $appointmentDays = Appointment::query()
            ->select('date')
            ->distinct()
            ->get()
            ->count();

        $this->assertSame(24, $appointmentDays);
        $this->assertSame('2026-03-01', Carbon::parse(Appointment::min('date'))->toDateString());
        $this->assertSame('2026-03-24', Carbon::parse(Appointment::max('date'))->toDateString());
        $this->assertGreaterThanOrEqual(40, Appointment::count());
        $this->assertLessThanOrEqual(50, Appointment::count());
        $this->assertGreaterThan(0, Appointment::where('customer_name', '-')->count());
        $this->assertGreaterThan(0, Appointment::where('customer_name', '!=', '-')->count());
        $this->assertGreaterThan(10, Appointment::where('customer_name', '!=', '-')->distinct('customer_name')->count('customer_name'));
        $this->assertGreaterThan(0, Appointment::whereNotNull('customer_email')->count());
        $this->assertSame(
            Appointment::whereNotNull('customer_email')->count(),
            Appointment::where('customer_email', 'like', '%@gmail.com')->count()
        );
        $this->assertGreaterThan(0, Appointment::whereNotNull('customer_phone')->count());
        $this->assertSame(Appointment::count(), Appointment::where('notes', 'like', '___ ____')->count());

        $totalRevenue = (float) Appointment::completed()->sum('amount');

        $this->assertGreaterThan(0, $totalRevenue);
        $this->assertLessThan(30000, $totalRevenue);

        $this->assertGreaterThan(15, InventoryItem::count());
        $this->assertSame(0, InventoryItem::whereNotNull('sku')->count());
        $inventoryValue = (float) InventoryItem::selectRaw('SUM(cost * quantity) as value')->value('value');
        $this->assertGreaterThan(50000, $inventoryValue);
        $this->assertLessThanOrEqual(72000, $inventoryValue);
        $this->assertGreaterThan(0, Service::whereHas('inventoryItems')->count());
        $this->assertGreaterThan(0, InventoryLog::where('reference_type', 'appointment')->count());
        $this->assertSame(0, InventoryLog::whereNull('user_id')->count());
        $this->assertGreaterThanOrEqual(0, InventoryItem::min('quantity'));
    }
}
