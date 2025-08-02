<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorWeeklySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'day_of_week', 'start_time', 'end_time', 'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Helper method to get day name
    public function getDayNameAttribute()
    {
        $days = [
            1 => 'Thứ 2',
            2 => 'Thứ 3', 
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ nhật'
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }

    // Get available time slots for this day
    public function getTimeSlots()
    {
        if (!$this->is_active) {
            return [];
        }

        $slots = [];
        $current = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $slotDuration = 30; // minutes

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($slotDuration);
        }

        return $slots;
    }
}