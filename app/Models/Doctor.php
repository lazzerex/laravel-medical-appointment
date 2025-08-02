<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'title', 'hospital_id', 'specialty_id', 'bio'];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function weeklySchedules()
    {
        return $this->hasMany(DoctorWeeklySchedule::class);
    }

    public function scheduleExceptions()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableSlots($date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek == 0 ? 7 : $carbonDate->dayOfWeek; // Convert Sunday from 0 to 7

        // First check if there's an exception for this specific date
        $exception = $this->scheduleExceptions()->where('date', $date)->first();
        
        if ($exception) {
            return $this->handleScheduleException($exception, $date);
        }

        // Get the weekly schedule for this day
        $weeklySchedule = $this->weeklySchedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$weeklySchedule) {
            return []; // No schedule for this day
        }

        // Generate time slots
        $slots = $this->generateTimeSlots($weeklySchedule->start_time, $weeklySchedule->end_time);

        // Remove booked slots
        return $this->removeBookedSlots($slots, $date);
    }

    /**
     * Handle schedule exceptions (holidays, custom hours, unavailable days)
     */
    private function handleScheduleException($exception, $date)
    {
        switch ($exception->type) {
            case 'unavailable':
            case 'holiday':
                return []; // No slots available
                
            case 'custom_hours':
                if ($exception->start_time && $exception->end_time) {
                    $slots = $this->generateTimeSlots($exception->start_time, $exception->end_time);
                    return $this->removeBookedSlots($slots, $date);
                }
                return [];
                
            default:
                return [];
        }
    }

    /**
     * Generate time slots between start and end time
     */
    private function generateTimeSlots($startTime, $endTime)
    {
        $slots = [];
        $current = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $slotDuration = 30; // minutes

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    /**
     * Remove already booked time slots
     */
    private function removeBookedSlots($slots, $date)
    {
        $bookedSlots = $this->appointments()
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed']) // Don't count cancelled appointments
            ->pluck('appointment_time')
            ->map(function ($time) {
                return Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return array_diff($slots, $bookedSlots);
    }

    /**
     * Check if a specific time slot is available
     */
    public function isSlotAvailable($date, $time)
    {
        $availableSlots = $this->getAvailableSlots($date);
        return in_array($time, $availableSlots);
    }

    /**
     * Get the doctor's schedule for a specific date with status
     */
    public function getScheduleStatus($date)
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = $carbonDate->dayOfWeek == 0 ? 7 : $carbonDate->dayOfWeek;

        // Check for exceptions first
        $exception = $this->scheduleExceptions()->where('date', $date)->first();
        
        if ($exception) {
            return [
                'type' => 'exception',
                'status' => $exception->type,
                'reason' => $exception->reason,
                'start_time' => $exception->start_time,
                'end_time' => $exception->end_time,
                'available_slots' => $this->getAvailableSlots($date)
            ];
        }

        // Check weekly schedule
        $weeklySchedule = $this->weeklySchedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$weeklySchedule) {
            return [
                'type' => 'no_schedule',
                'status' => 'unavailable',
                'reason' => 'Không có lịch làm việc',
                'available_slots' => []
            ];
        }

        return [
            'type' => 'weekly',
            'status' => 'available',
            'start_time' => $weeklySchedule->start_time,
            'end_time' => $weeklySchedule->end_time,
            'available_slots' => $this->getAvailableSlots($date)
        ];
    }

    /**
     * Get doctor's full weekly schedule
     */
    public function getWeeklySchedule()
    {
        return $this->weeklySchedules()
            ->orderBy('day_of_week')
            ->get()
            ->mapWithKeys(function ($schedule) {
                return [$schedule->day_of_week => $schedule];
            });
    }
}