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
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        $doctor = Doctor::find($request->doctor_id);
        $date = $request->date;
        
        $scheduleStatus = $doctor->getScheduleStatus($date);
        $availableSlots = $scheduleStatus['available_slots'];
        
        // Format slots for frontend
        $formattedSlots = array_map(function($slot) {
            return [
                'time' => $slot,
                'display' => Carbon::createFromFormat('H:i', $slot)->format('H:i'),
                'available' => true
            ];
        }, $availableSlots);
        
        return response()->json([
            'date' => $date,
            'status' => $scheduleStatus['status'],
            'message' => $this->getStatusMessage($scheduleStatus),
            'slots' => $formattedSlots,
            'total_slots' => count($formattedSlots)
        ]);
    }

    private function getStatusMessage($scheduleStatus)
    {
        switch ($scheduleStatus['status']) {
            case 'unavailable':
                return $scheduleStatus['reason'] ?? 'Bác sĩ không có lịch làm việc trong ngày này';
            case 'available':
                $slotsCount = count($scheduleStatus['available_slots']);
                if ($slotsCount === 0) {
                    return 'Tất cả các khung giờ đã được đặt';
                }
                return "Có {$slotsCount} khung giờ trống";
            default:
                return 'Không xác định được trạng thái lịch làm việc';
        }
    }

    public function store(AppointmentRequest $request)
    {
        // Debug log the incoming request data
        \Log::info('Appointment Request Data:', $request->all());
        
        // Additional validation for slot availability
        $doctor = Doctor::find($request->doctor_id);
        
        // Debug log the doctor and slot availability
        \Log::info('Doctor:', ['id' => $doctor ? $doctor->id : null]);
        \Log::info('Appointment Date/Time:', [
            'date' => $request->appointment_date,
            'time' => $request->appointment_time
        ]);
        
        if (!$doctor) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin bác sĩ.',
                'errors' => [
                    'doctor_id' => ['Không tìm thấy bác sĩ']
                ]
            ], 422);
        }
        
        if (!$request->appointment_date || !$request->appointment_time) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ngày và giờ khám.',
                'errors' => [
                    'appointment_date' => $request->appointment_date ? [] : ['Vui lòng chọn ngày khám'],
                    'appointment_time' => $request->appointment_time ? [] : ['Vui lòng chọn giờ khám']
                ]
            ], 422);
        }
        
        if (!$doctor->isSlotAvailable($request->appointment_date, $request->appointment_time)) {
            return response()->json([
                'success' => false,
                'message' => 'Khung giờ đã chọn không còn trống. Vui lòng chọn khung giõ khác.',
                'errors' => [
                    'appointment_time' => ['Khung giờ này không còn trống']
                ]
            ], 422);
        }

        // Check if appointment already exists (double booking prevention)
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có cuộc hẹn khác trong khung giờ này. Vui lòng chọn khung giờ khác.',
                'errors' => [
                    'appointment_time' => ['Khung giờ này đã được đặt']
                ]
            ], 422);
        }

        try {
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
                'appointment' => $appointment->load(['doctor', 'hospital', 'specialty'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt hẹn. Vui lòng thử lại.',
                'errors' => [
                    'general' => ['Lỗi hệ thống']
                ]
            ], 500);
        }
    }

    // New method to get doctor's weekly schedule
    public function getDoctorSchedule(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id'
        ]);

        $doctor = Doctor::find($request->doctor_id);
        $weeklySchedule = $doctor->getWeeklySchedule();
        
        $scheduleData = [];
        for ($day = 1; $day <= 7; $day++) {
            $schedule = $weeklySchedule->get($day);
            $scheduleData[] = [
                'day_of_week' => $day,
                'day_name' => $this->getDayName($day),
                'is_active' => $schedule ? $schedule->is_active : false,
                'start_time' => $schedule && $schedule->is_active ? $schedule->start_time : null,
                'end_time' => $schedule && $schedule->is_active ? $schedule->end_time : null,
            ];
        }

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'title' => $doctor->title
            ],
            'weekly_schedule' => $scheduleData
        ]);
    }

    // Get calendar data for a month
    public function getCalendarData(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'year' => 'required|integer|min:2024',
            'month' => 'required|integer|between:1,12'
        ]);

        $doctor = Doctor::find($request->doctor_id);
        $year = $request->year;
        $month = $request->month;
        
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();
        
        $calendarData = [];
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateStr = $date->toDateString();
            $scheduleStatus = $doctor->getScheduleStatus($dateStr);
            $availableSlots = $scheduleStatus['available_slots'];
            
            $calendarData[] = [
                'date' => $dateStr,
                'day' => $date->day,
                'day_of_week' => $date->dayOfWeek,
                'is_today' => $date->isToday(),
                'is_past' => $date->isPast(),
                'status' => $scheduleStatus['status'],
                'available_slots_count' => count($availableSlots),
                'is_fully_booked' => $scheduleStatus['status'] === 'available' && count($availableSlots) === 0,
                'is_available' => $scheduleStatus['status'] === 'available' && count($availableSlots) > 0,
                'reason' => $scheduleStatus['reason'] ?? null
            ];
        }

        return response()->json([
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'title' => $doctor->title
            ],
            'year' => $year,
            'month' => $month,
            'month_name' => $startDate->format('F Y'),
            'calendar_data' => $calendarData
        ]);
    }

    private function getDayName($dayOfWeek)
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

        return $days[$dayOfWeek] ?? 'Unknown';
    }
}