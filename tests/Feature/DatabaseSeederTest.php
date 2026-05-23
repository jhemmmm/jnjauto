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

    public function test_database_seeder_creates_production_like_philippine_month_data(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-23 10:15:00'));

        $this->seed(DatabaseSeeder::class);

        $appointmentDays = Appointment::query()
            ->select('date')
            ->distinct()
            ->get()
            ->count();

        $this->assertSame(31, $appointmentDays);
        $this->assertGreaterThan(220, Appointment::count());
        $this->assertGreaterThan(0, Appointment::where('customer_name', '-')->count());
        $this->assertGreaterThan(0, Appointment::where('customer_name', '!=', '-')->count());
        $this->assertGreaterThan(10, Appointment::where('customer_name', '!=', '-')->distinct('customer_name')->count('customer_name'));
        $this->assertGreaterThan(0, Appointment::whereNotNull('customer_email')->count());
        $this->assertGreaterThan(0, Appointment::whereNotNull('customer_phone')->count());
        $this->assertSame(Appointment::count(), Appointment::where('notes', 'like', '___ ____')->count());

        $this->assertGreaterThan(15, InventoryItem::count());
        $this->assertSame(0, InventoryItem::whereNotNull('sku')->count());
        $inventoryValue = (float) InventoryItem::selectRaw('SUM(cost * quantity) as value')->value('value');
        $this->assertGreaterThanOrEqual(18000, $inventoryValue);
        $this->assertLessThanOrEqual(20000, $inventoryValue);
        $this->assertGreaterThan(0, Service::whereHas('inventoryItems')->count());
        $this->assertGreaterThan(0, InventoryLog::where('reference_type', 'appointment')->count());
        $this->assertGreaterThanOrEqual(0, InventoryItem::min('quantity'));
    }
}
