<?php

namespace App\Http\Controllers;

use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Http\Requests\AppointmentRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $hospitals = Hospital::all();
        $specialties = Specialty::all();
        
        return view('appointments.index', compact('hospitals', 'specialties'));
    }

    public function getDoctorsByHospitalAndSpecialty(Request $request)
    {
        $doctors = Doctor::where('hospital_id', $request->hospital_id)
            ->where('specialty_id', $request->specialty_id)
            ->get();
        
        return response()->json($doctors);
    }

    public function getAvailableSlots(Request $request)
    {
        $doctor = Doctor::find($request->doctor_id);
        $date = $request->date;
        
        $slots = $doctor->getAvailableSlots($date);
        
        return response()->json($slots);
    }

    public function store(AppointmentRequest $request)
    {
        $appointment = Appointment::create([
            'hospital_id' => $request->hospital_id,
            'specialty_id' => $request->specialty_id,
            'doctor_id' => $request->doctor_id,
            'patient_name' => $request->patient_name,
            'patient_phone' => $request->patient_phone,
            'patient_email' => $request->patient_email,
            'notes' => $request->notes,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đặt hẹn khám thành công! Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.',
            'appointment' => $appointment
        ]);
    }
}