<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
            ['key' => 'business_name',    'value' => 'JNJ Auto Car Wash'],
            ['key' => 'business_email',   'value' => 'info@jnjauto.com'],
            ['key' => 'business_phone',   'value' => '(555) 123-4567'],
            ['key' => 'business_address', 'value' => '123 Main Street, Manila, Philippines'],
            ['key' => 'opening_time',     'value' => '07:00'],
            ['key' => 'closing_time',     'value' => '17:00'],
            ['key' => 'slot_duration',    'value' => '30'],
            ['key' => 'slot_capacity',    'value' => '2'],
            ['key' => 'currency',         'value' => 'PHP'],
            ['key' => 'timezone',         'value' => 'Asia/Manila'],
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
