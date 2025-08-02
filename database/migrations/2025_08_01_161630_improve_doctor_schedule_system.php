<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImproveDoctorScheduleSystem extends Migration
{
    public function up()
    {
        // First, let's create a new weekly_schedules table for recurring schedules
        Schema::create('doctor_weekly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week'); // 1=Monday, 2=Tuesday, ... 7=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Ensure one schedule per doctor per day
            $table->unique(['doctor_id', 'day_of_week']);
        });

        // Clean up and modify existing doctor_schedules table for exceptions only
        Schema::table('doctor_schedules', function (Blueprint $table) {
            // Remove the columns we don't need
            $table->dropColumn(['slot_duration', 'is_recurring']);
            
            // Add new columns for exception handling
            $table->enum('type', ['unavailable', 'custom_hours', 'holiday'])->default('unavailable');
            $table->string('reason')->nullable(); // Why this exception exists
            
            // Make start_time and end_time nullable for "unavailable" days
            $table->time('start_time')->nullable()->change();
            $table->time('end_time')->nullable()->change();
            
            // Add unique constraint to prevent duplicate exceptions on same date
            $table->unique(['doctor_id', 'date']);
        });

        // Seed some sample weekly schedules (optional)
        // You can run this manually or create a seeder
        /*
        DB::table('doctor_weekly_schedules')->insert([
            // Doctor 1: Monday to Friday, 8AM-5PM
            ['doctor_id' => 1, 'day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '17:00'],
            ['doctor_id' => 1, 'day_of_week' => 2, 'start_time' => '08:00', 'end_time' => '17:00'],
            ['doctor_id' => 1, 'day_of_week' => 3, 'start_time' => '08:00', 'end_time' => '17:00'],
            ['doctor_id' => 1, 'day_of_week' => 4, 'start_time' => '08:00', 'end_time' => '17:00'],
            ['doctor_id' => 1, 'day_of_week' => 5, 'start_time' => '08:00', 'end_time' => '17:00'],
            // Saturday different hours
            ['doctor_id' => 1, 'day_of_week' => 6, 'start_time' => '09:00', 'end_time' => '13:00'],
        ]);
        */
    }

    public function down()
    {
        Schema::dropIfExists('doctor_weekly_schedules');
        
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropColumn(['type', 'reason']);
            $table->dropUnique(['doctor_id', 'date']);
            
            // Restore original columns
            $table->integer('slot_duration')->default(30);
            $table->boolean('is_recurring')->default(false);
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
        });
    }
}