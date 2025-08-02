<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Doctor;
use Carbon\Carbon;

class AppointmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'hospital_id' => 'required|exists:hospitals,id',
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => [
                'required',
                'exists:doctors,id',
                function ($attribute, $value, $fail) {
                    // Validate that doctor belongs to selected hospital and specialty
                    $doctor = Doctor::find($value);
                    if ($doctor) {
                        if ($doctor->hospital_id != $this->hospital_id) {
                            $fail('Bác sĩ được chọn không thuộc bệnh viện đã chọn.');
                        }
                        if ($doctor->specialty_id != $this->specialty_id) {
                            $fail('Bác sĩ được chọn không thuộc chuyên khoa đã chọn.');
                        }
                    }
                },
            ],
            'patient_name' => 'required|string|max:255|regex:/^[\pL\s\-\.]+$/u',
            'patient_phone' => [
                'required',
                'string',
                'regex:/^[0-9+\-\s()]+$/',
                'min:10',
                'max:15'
            ],
            'patient_email' => 'required|email|max:255',
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before_or_equal:' . now()->addMonths(3)->toDateString(), // Max 3 months ahead
                function ($attribute, $value, $fail) {
                    // Don't allow appointments on Sundays (if needed)
                    $date = Carbon::parse($value);
                    if ($date->isSunday()) {
                        // Comment this out if you want to allow Sunday appointments
                        // $fail('Không thể đặt hẹn vào Chủ nhật.');
                    }
                },
            ],
            'appointment_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    // Validate time format and working hours
                    $time = Carbon::createFromFormat('H:i', $value);
                    $hour = $time->hour;
                    $minute = $time->minute;
                    
                    // Check if time is in 30-minute intervals
                    if (!in_array($minute, [0, 30])) {
                        $fail('Giờ hẹn phải là khung giờ 30 phút (ví dụ: 08:00, 08:30, 09:00).');
                    }
                    
                    // Check reasonable working hours (optional)
                    if ($hour < 6 || $hour > 22) {
                        $fail('Giờ hẹn phải trong khoảng 06:00 - 22:00.');
                    }
                },
                function ($attribute, $value, $fail) {
                    // Validate slot availability
                    if ($this->has('doctor_id') && $this->has('appointment_date')) {
                        $doctor = Doctor::find($this->doctor_id);
                        if ($doctor && !$doctor->isSlotAvailable($this->appointment_date, $value)) {
                            $fail('Khung giờ đã chọn không còn trống. Vui lòng chọn khung giờ khác.');
                        }
                    }
                },
            ],
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'hospital_id.required' => 'Vui lòng chọn bệnh viện.',
            'hospital_id.exists' => 'Bệnh viện được chọn không tồn tại.',
            
            'specialty_id.required' => 'Vui lòng chọn chuyên khoa.',
            'specialty_id.exists' => 'Chuyên khoa được chọn không tồn tại.',
            
            'doctor_id.required' => 'Vui lòng chọn bác sĩ.',
            'doctor_id.exists' => 'Bác sĩ được chọn không tồn tại.',
            
            'patient_name.required' => 'Vui lòng nhập họ tên.',
            'patient_name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'patient_name.regex' => 'Họ tên chỉ được chứa chữ cái, khoảng trắng, dấu gạch ngang và dấu chấm.',
            
            'patient_phone.required' => 'Vui lòng nhập số điện thoại.',
            'patient_phone.regex' => 'Số điện thoại không hợp lệ.',
            'patient_phone.min' => 'Số điện thoại phải có ít nhất 10 số.',
            'patient_phone.max' => 'Số điện thoại không được vượt quá 15 số.',
            
            'patient_email.required' => 'Vui lòng nhập email.',
            'patient_email.email' => 'Email không hợp lệ.',
            'patient_email.max' => 'Email không được vượt quá 255 ký tự.',
            
            'appointment_date.required' => 'Vui lòng chọn ngày khám.',
            'appointment_date.date' => 'Ngày khám không hợp lệ.',
            'appointment_date.after_or_equal' => 'Ngày khám phải từ hôm nay trở đi.',
            'appointment_date.before_or_equal' => 'Chỉ có thể đặt hẹn trong vòng 3 tháng tới.',
            
            'appointment_time.required' => 'Vui lòng chọn giờ khám.',
            'appointment_time.date_format' => 'Giờ khám không hợp lệ.',
            
            'notes.max' => 'Ghi chú không được vượt quá 1000 ký tự.',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     */
    public function attributes()
    {
        return [
            'hospital_id' => 'bệnh viện',
            'specialty_id' => 'chuyên khoa',
            'doctor_id' => 'bác sĩ',
            'patient_name' => 'họ tên',
            'patient_phone' => 'số điện thoại',
            'patient_email' => 'email',
            'appointment_date' => 'ngày khám',
            'appointment_time' => 'giờ khám',
            'notes' => 'ghi chú',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            $response = response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ.',
                'errors' => $validator->errors()
            ], 422);
            
            throw new \Illuminate\Validation\ValidationException($validator, $response);
        }
        
        parent::failedValidation($validator);
    }
}