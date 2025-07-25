<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Specialty;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['hospital', 'specialty'])->get();
        $hospitals = Hospital::all();
        $specialties = Specialty::all();
        
        return view('doctors.index', compact('doctors', 'hospitals', 'specialties'));
    }

    public function create()
    {
        $hospitals = Hospital::all();
        $specialties = Specialty::all();
        return view('doctors.create', compact('hospitals', 'specialties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'hospital_id' => 'required|exists:hospitals,id',
            'specialty_id' => 'required|exists:specialties,id',
            'bio' => 'nullable|string',
        ]);

        Doctor::create($validated);
        
        return redirect()->route('dashboard.doctors')
            ->with('success', 'Bác sĩ đã được tạo thành công!');
    }

    public function edit(Doctor $doctor)
    {
        $hospitals = Hospital::all();
        $specialties = Specialty::all();
        return view('doctors.edit', compact('doctor', 'hospitals', 'specialties'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'hospital_id' => 'required|exists:hospitals,id',
            'specialty_id' => 'required|exists:specialties,id',
            'bio' => 'nullable|string',
        ]);

        $doctor->update($validated);
        
        return redirect()->route('dashboard.doctors')
            ->with('success', 'Bác sĩ đã được cập nhật thành công!');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        
        return redirect()->route('dashboard.doctors')
            ->with('success', 'Bác sĩ đã được xóa thành công!');
    }
}
