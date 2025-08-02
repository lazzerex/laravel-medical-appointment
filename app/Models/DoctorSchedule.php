<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id', 'date', 'start_time', 'end_time', 'type', 'reason', 'is_available'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    // Get type display name
    public function getTypeDisplayAttribute()
    {
        $types = [
            'unavailable' => 'Không có mặt',
            'custom_hours' => 'Giờ đặc biệt',
            'holiday' => 'Ngày lễ'
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Scope for different exception types
    public function scopeUnavailable($query)
    {
        return $query->where('type', 'unavailable');
    }

    public function scopeCustomHours($query)
    {
        return $query->where('type', 'custom_hours');
    }

    public function scopeHolidays($query)
    {
        return $query->where('type', 'holiday');
    }

    // Scope for future exceptions
    public function scopeFuture($query)
    {
        return $query->where('date', '>=', now()->toDateString());
    }

    // Check if this exception blocks all appointments
    public function blocksAllAppointments()
    {
        return in_array($this->type, ['unavailable', 'holiday']);
    }
}