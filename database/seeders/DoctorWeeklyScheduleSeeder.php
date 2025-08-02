<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\DoctorWeeklySchedule;
use Illuminate\Support\Facades\DB;

class DoctorWeeklyScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing weekly schedules
        DoctorWeeklySchedule::truncate();

        // Get all doctors
        $doctors = Doctor::all();

        if ($doctors->isEmpty()) {
            $this->command->info('No doctors found. Please add doctors first.');
            return;
        }

        $this->command->info('Seeding weekly schedules for ' . $doctors->count() . ' doctors...');

        foreach ($doctors as $doctor) {
            $this->createScheduleForDoctor($doctor);
        }

        $this->command->info('Weekly schedules seeded successfully!');
    }

    private function createScheduleForDoctor(Doctor $doctor)
    {
        // Define different schedule patterns
        $schedulePatterns = [
            'full_time_weekdays' => [
                ['day' => 1, 'start' => '08:00', 'end' => '17:00'], // Monday
                ['day' => 2, 'start' => '08:00', 'end' => '17:00'], // Tuesday
                ['day' => 3, 'start' => '08:00', 'end' => '17:00'], // Wednesday
                ['day' => 4, 'start' => '08:00', 'end' => '17:00'], // Thursday
                ['day' => 5, 'start' => '08:00', 'end' => '17:00'], // Friday
            ],
            'full_time_with_saturday' => [
                ['day' => 1, 'start' => '08:00', 'end' => '17:00'], // Monday
                ['day' => 2, 'start' => '08:00', 'end' => '17:00'], // Tuesday
                ['day' => 3, 'start' => '08:00', 'end' => '17:00'], // Wednesday
                ['day' => 4, 'start' => '08:00', 'end' => '17:00'], // Thursday
                ['day' => 5, 'start' => '08:00', 'end' => '17:00'], // Friday
                ['day' => 6, 'start' => '08:00', 'end' => '12:00'], // Saturday morning
            ],
            'part_time_mornings' => [
                ['day' => 1, 'start' => '08:00', 'end' => '12:00'], // Monday
                ['day' => 2, 'start' => '08:00', 'end' => '12:00'], // Tuesday
                ['day' => 3, 'start' => '08:00', 'end' => '12:00'], // Wednesday
                ['day' => 4, 'start' => '08:00', 'end' => '12:00'], // Thursday
                ['day' => 5, 'start' => '08:00', 'end' => '12:00'], // Friday
            ],
            'part_time_afternoons' => [
                ['day' => 1, 'start' => '13:00', 'end' => '17:00'], // Monday
                ['day' => 2, 'start' => '13:00', 'end' => '17:00'], // Tuesday
                ['day' => 3, 'start' => '13:00', 'end' => '17:00'], // Wednesday
                ['day' => 4, 'start' => '13:00', 'end' => '17:00'], // Thursday
                ['day' => 5, 'start' => '13:00', 'end' => '17:00'], // Friday
            ],
            'three_days_week' => [
                ['day' => 1, 'start' => '08:00', 'end' => '17:00'], // Monday
                ['day' => 3, 'start' => '08:00', 'end' => '17:00'], // Wednesday
                ['day' => 5, 'start' => '08:00', 'end' => '17:00'], // Friday
            ],
            'extended_hours' => [
                ['day' => 1, 'start' => '07:00', 'end' => '19:00'], // Monday
                ['day' => 2, 'start' => '07:00', 'end' => '19:00'], // Tuesday
                ['day' => 3, 'start' => '07:00', 'end' => '19:00'], // Wednesday
                ['day' => 4, 'start' => '07:00', 'end' => '19:00'], // Thursday
                ['day' => 5, 'start' => '07:00', 'end' => '15:00'], // Friday
            ]
        ];

        // Randomly assign a schedule pattern to each doctor
        $patternKeys = array_keys($schedulePatterns);
        $randomPattern = $patternKeys[array_rand($patternKeys)];
        $schedule = $schedulePatterns[$randomPattern];

        // Create weekly schedule entries for this doctor
        foreach ($schedule as $daySchedule) {
            DoctorWeeklySchedule::create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $daySchedule['day'],
                'start_time' => $daySchedule['start'],
                'end_time' => $daySchedule['end'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info("✓ Created {$randomPattern} schedule for Dr. {$doctor->name}");
    }
}

// Alternative: Quick manual insert if you want to run it directly
class QuickScheduleSeeder
{
    public static function seedForSpecificDoctor($doctorId, $pattern = 'full_time_weekdays')
    {
        $schedules = [
            'full_time_weekdays' => [
                ['day' => 1, 'start' => '08:00', 'end' => '17:00'],
                ['day' => 2, 'start' => '08:00', 'end' => '17:00'],
                ['day' => 3, 'start' => '08:00', 'end' => '17:00'],
                ['day' => 4, 'start' => '08:00', 'end' => '17:00'],
                ['day' => 5, 'start' => '08:00', 'end' => '17:00'],
            ]
        ];

        foreach ($schedules[$pattern] as $daySchedule) {
            DoctorWeeklySchedule::create([
                'doctor_id' => $doctorId,
                'day_of_week' => $daySchedule['day'],
                'start_time' => $daySchedule['start'],
                'end_time' => $daySchedule['end'],
                'is_active' => true,
            ]);
        }
    }
}