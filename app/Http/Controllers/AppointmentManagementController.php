<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Http\Request;

class AppointmentManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['doctor', 'hospital', 'specialty'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc');

        // Filter by status if provided and not empty
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $appointments = $query->paginate(10);
        $doctors = Doctor::all();
        $hospitals = Hospital::all();
        $specialties = Specialty::all();
        
        return view('appointments.management', compact('appointments', 'doctors', 'hospitals', 'specialties'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment->update($validated);
        
        return redirect()->route('dashboard.appointments')
            ->with('success', 'Đã cập nhật trạng thái cuộc hẹn thành công!');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        
        return redirect()->route('dashboard.appointments')
            ->with('success', 'Đã xóa cuộc hẹn thành công!');
    }
}
