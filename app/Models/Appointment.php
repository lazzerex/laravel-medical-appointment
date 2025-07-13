<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'hospital_id', 'specialty_id', 'doctor_id', 'patient_name', 
        'patient_phone', 'patient_email', 'notes', 'appointment_date', 
        'appointment_time', 'status'
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'appointment_time' => 'datetime:H:i',
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
