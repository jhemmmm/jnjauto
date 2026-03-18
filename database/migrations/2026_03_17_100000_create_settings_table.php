<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default business settings
        $defaults = [
            ['key' => 'business_name', 'value' => 'JNJ Auto Car Wash'],
            ['key' => 'business_email', 'value' => 'info@jnjauto.com'],
            ['key' => 'business_phone', 'value' => '(+63) 919-123-4567'],
            ['key' => 'business_address', 'value' => '123 I Have, No Idea, Naga City, Philippines'],
            ['key' => 'opening_time', 'value' => '07:00'],
            ['key' => 'closing_time', 'value' => '17:00'],
            ['key' => 'slot_duration', 'value' => '30'],
            ['key' => 'slot_capacity', 'value' => '2'],
            ['key' => 'currency', 'value' => 'PHP'],
            ['key' => 'timezone', 'value' => 'Asia/Manila'],
            ['key' => 'business_logo', 'value' => null],
            ['key' => 'app_name_first', 'value' => 'Wash'],
            ['key' => 'app_name_last', 'value' => 'Wise'],
            ['key' => 'show_emergency_phone', 'value' => '1'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
