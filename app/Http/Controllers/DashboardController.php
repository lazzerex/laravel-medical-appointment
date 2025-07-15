<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_doctors' => Doctor::count(),
            'total_appointments' => Appointment::count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'confirmed_appointments' => Appointment::where('status', 'confirmed')->count(),
            'cancelled_appointments' => Appointment::where('status', 'cancelled')->count(),
            'completed_appointments' => Appointment::where('status', 'completed')->count(),
        ];

        $recent_appointments = Appointment::with(['doctor', 'hospital', 'specialty'])
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recent_appointments'));
    }
}
