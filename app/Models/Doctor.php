<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function getAvailableSlots($date)
    {
        $schedule = $this->schedules()->where('date', $date)->first();
        if (!$schedule) {
            return [];
        }

        $slots = [];
        $current = \Carbon\Carbon::parse($date . ' ' . $schedule->start_time);
        $end = \Carbon\Carbon::parse($date . ' ' . $schedule->end_time);

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($schedule->slot_duration);
        }

        // Remove booked slots
        $bookedSlots = $this->appointments()
            ->where('appointment_date', $date)
            ->pluck('appointment_time')
            ->map(function ($time) {
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        return array_diff($slots, $bookedSlots);
    }
}
