<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorWeeklySchedule;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with(['hospital', 'specialty', 'weeklySchedules'])
            ->orderBy('name')
            ->get();
        
        return view('schedules.index', compact('doctors'));
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['weeklySchedules', 'scheduleExceptions.doctor']);
        
        // Get upcoming exceptions
        $upcomingExceptions = $doctor->scheduleExceptions()
            ->future()
            ->orderBy('date')
            ->limit(10)
            ->get();

        return view('schedules.show', compact('doctor', 'upcomingExceptions'));
    }

    public function editWeekly(Doctor $doctor)
    {
        $weeklySchedules = $doctor->getWeeklySchedule();
        
        return view('schedules.edit-weekly', compact('doctor', 'weeklySchedules'));
    }

    public function updateWeekly(Request $request, Doctor $doctor)
    {
        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|integer|between:1,7',
            'schedules.*.is_active' => 'boolean',
            'schedules.*.start_time' => 'required_if:schedules.*.is_active,true|nullable|date_format:H:i',
            'schedules.*.end_time' => 'required_if:schedules.*.is_active,true|nullable|date_format:H:i|after:schedules.*.start_time',
        ]);

        foreach ($request->schedules as $scheduleData) {
            $dayOfWeek = $scheduleData['day_of_week'];
            $isActive = isset($scheduleData['is_active']) && $scheduleData['is_active'];

            if ($isActive) {
                DoctorWeeklySchedule::updateOrCreate(
                    ['doctor_id' => $doctor->id, 'day_of_week' => $dayOfWeek],
                    [
                        'start_time' => $scheduleData['start_time'],
                        'end_time' => $scheduleData['end_time'],
                        'is_active' => true,
                    ]
                );
            } else {
                // If not active, either delete or mark as inactive
                DoctorWeeklySchedule::where('doctor_id', $doctor->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->update(['is_active' => false]);
            }
        }

        return redirect()->route('schedules.show', $doctor)
            ->with('success', 'Đã cập nhật lịch làm việc hàng tuần thành công!');
    }

    public function createException(Doctor $doctor)
    {
        return view('schedules.create-exception', compact('doctor'));
    }

    public function storeException(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today|unique:doctor_schedules,date,NULL,id,doctor_id,' . $doctor->id,
            'type' => 'required|in:unavailable,custom_hours,holiday',
            'reason' => 'nullable|string|max:255',
            'start_time' => 'required_if:type,custom_hours|nullable|date_format:H:i',
            'end_time' => 'required_if:type,custom_hours|nullable|date_format:H:i|after:start_time',
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'date' => $request->date,
            'type' => $request->type,
            'reason' => $request->reason,
            'start_time' => $request->type === 'custom_hours' ? $request->start_time : null,
            'end_time' => $request->type === 'custom_hours' ? $request->end_time : null,
            'is_available' => $request->type === 'custom_hours',
        ]);

        return redirect()->route('schedules.show', $doctor)
            ->with('success', 'Đã thêm ngoại lệ lịch làm việc thành công!');
    }

    public function editException(DoctorSchedule $exception)
    {
        return view('schedules.edit-exception', compact('exception'));
    }

    public function updateException(Request $request, DoctorSchedule $exception)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today|unique:doctor_schedules,date,' . $exception->id . ',id,doctor_id,' . $exception->doctor_id,
            'type' => 'required|in:unavailable,custom_hours,holiday',
            'reason' => 'nullable|string|max:255',
            'start_time' => 'required_if:type,custom_hours|nullable|date_format:H:i',
            'end_time' => 'required_if:type,custom_hours|nullable|date_format:H:i|after:start_time',
        ]);

        $exception->update([
            'date' => $request->date,
            'type' => $request->type,
            'reason' => $request->reason,
            'start_time' => $request->type === 'custom_hours' ? $request->start_time : null,
            'end_time' => $request->type === 'custom_hours' ? $request->end_time : null,
            'is_available' => $request->type === 'custom_hours',
        ]);

        return redirect()->route('schedules.show', $exception->doctor)
            ->with('success', 'Đã cập nhật ngoại lệ lịch làm việc thành công!');
    }

    public function destroyException(DoctorSchedule $exception)
    {
        $doctor = $exception->doctor;
        $exception->delete();

        return redirect()->route('schedules.show', $doctor)
            ->with('success', 'Đã xóa ngoại lệ lịch làm việc thành công!');
    }

    // API endpoint for getting doctor's availability
    public function getAvailability(Request $request, Doctor $doctor)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $date = $request->date;
        $scheduleStatus = $doctor->getScheduleStatus($date);
        
        return response()->json([
            'date' => $date,
            'doctor' => $doctor->name,
            'schedule_status' => $scheduleStatus,
            'available_slots' => $scheduleStatus['available_slots'],
        ]);
    }

    // API endpoint for getting multiple days availability
    public function getMonthAvailability(Request $request, Doctor $doctor)
    {
        $request->validate([
            'year' => 'required|integer|min:2024',
            'month' => 'required|integer|between:1,12',
        ]);

        $year = $request->year;
        $month = $request->month;
        
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $availability = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $scheduleStatus = $doctor->getScheduleStatus($dateStr);
            
            $availability[] = [
                'date' => $dateStr,
                'day_of_week' => $date->dayOfWeek,
                'status' => $scheduleStatus['status'],
                'available_slots_count' => count($scheduleStatus['available_slots']),
                'has_appointments' => $scheduleStatus['available_slots'] !== $this->generateAllSlots($scheduleStatus),
            ];
        }

        return response()->json($availability);
    }

    private function generateAllSlots($scheduleStatus)
    {
        if ($scheduleStatus['status'] === 'unavailable' || !isset($scheduleStatus['start_time'])) {
            return [];
        }

        $slots = [];
        $current = Carbon::parse($scheduleStatus['start_time']);
        $end = Carbon::parse($scheduleStatus['end_time']);

        while ($current->lt($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes(30);
        }

        return $slots;
    }
}