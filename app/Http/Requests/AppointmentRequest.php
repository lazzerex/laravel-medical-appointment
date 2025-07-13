<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'doctor_id' => 'required|exists:doctors,id',
            'patient_name' => 'required|string|max:255',
            'patient_phone' => 'required|string|max:15',
            'patient_email' => 'required|email|max:255',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'hospital_id.required' => 'Vui lòng chọn bệnh viện.',
            'specialty_id.required' => 'Vui lòng chọn chuyên khoa.',
            'doctor_id.required' => 'Vui lòng chọn bác sĩ.',
            'patient_name.required' => 'Vui lòng nhập họ tên.',
            'patient_phone.required' => 'Vui lòng nhập số điện thoại.',
            'patient_email.required' => 'Vui lòng nhập email.',
            'patient_email.email' => 'Email không hợp lệ.',
            'appointment_date.required' => 'Vui lòng chọn ngày khám.',
            'appointment_date.after_or_equal' => 'Ngày khám phải từ hôm nay trở đi.',
            'appointment_time.required' => 'Vui lòng chọn giờ khám.',
        ];
    }
}
