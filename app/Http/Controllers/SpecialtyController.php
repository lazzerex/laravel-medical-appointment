<?php

namespace App\Http\Controllers;

use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::paginate(10); // Show 10 specialties per page
        return view('specialties.index', compact('specialties'));
    }

    public function create()
    {
        return view('specialties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties',
            'description' => 'nullable|string',
        ]);

        Specialty::create($validated);
        
        return redirect()->route('dashboard.specialties.index')
            ->with('success', 'Dịch vụ đã được tạo thành công!');
    }

    public function edit(Specialty $specialty)
    {
        return view('specialties.edit', compact('specialty'));
    }

    public function update(Request $request, Specialty $specialty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:specialties,name,' . $specialty->id,
            'description' => 'nullable|string',
        ]);

        $specialty->update($validated);
        
        return redirect()->route('dashboard.specialties.index')
            ->with('success', 'Dịch vụ đã được cập nhật thành công!');
    }

    public function destroy(Specialty $specialty)
    {
        // Prevent deletion if there are doctors associated with this specialty
        if ($specialty->doctors()->exists()) {
            return redirect()->route('dashboard.specialties.index')
                ->with('error', 'Không thể xóa dịch vụ này vì có bác sĩ đang sử dụng.');
        }
        
        $specialty->delete();
        
        return redirect()->route('dashboard.specialties.index')
            ->with('success', 'Dịch vụ đã được xóa thành công!');
    }
}
