<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hospital;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Tạo bệnh viện
        $hospitals = [
            [
                'name' => 'Bệnh viện Đa khoa Thành phố',
                'address' => '123 Đường Nguyễn Huệ, Quận 1, TP.HCM',
                'phone' => '028-3822-7848'
            ],
            [
                'name' => 'Bệnh viện Chợ Rẫy',
                'address' => '201B Nguyễn Chí Thanh, Quận 5, TP.HCM',
                'phone' => '028-3855-4269'
            ],
            [
                'name' => 'Bệnh viện Đại học Y Dược',
                'address' => '215 Hồng Bàng, Quận 5, TP.HCM',
                'phone' => '028-3855-2225'
            ]
        ];

        foreach ($hospitals as $hospital) {
            Hospital::create($hospital);
        }

        // Tạo chuyên khoa
        $specialties = [
            ['name' => 'Khoa Nội', 'description' => 'Khám và điều trị các bệnh nội khoa'],
            ['name' => 'Khoa Ngoại', 'description' => 'Khám và điều trị các bệnh ngoại khoa'],
            ['name' => 'Khoa Sản', 'description' => 'Khám và điều trị các bệnh phụ khoa, sản khoa'],
            ['name' => 'Khoa Nhi', 'description' => 'Khám và điều trị các bệnh nhi khoa'],
            ['name' => 'Khoa Tai Mũi Họng', 'description' => 'Khám và điều trị các bệnh tai mũi họng'],
            ['name' => 'Khoa Mắt', 'description' => 'Khám và điều trị các bệnh về mắt'],
            ['name' => 'Khoa Da Liễu', 'description' => 'Khám và điều trị các bệnh da liễu'],
            ['name' => 'Khoa Tim Mạch', 'description' => 'Khám và điều trị các bệnh tim mạch']
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }

        // Tạo bác sĩ
        $doctors = [
            // Bệnh viện Đa khoa Thành phố
            ['name' => 'Nguyễn Văn An', 'title' => 'ThS.BS', 'hospital_id' => 1, 'specialty_id' => 1],
            ['name' => 'Trần Thị Bình', 'title' => 'PGS.TS', 'hospital_id' => 1, 'specialty_id' => 2],
            ['name' => 'Lê Văn Cường', 'title' => 'BS.CKI', 'hospital_id' => 1, 'specialty_id' => 3],
            ['name' => 'Phạm Thị Dung', 'title' => 'BS.CKII', 'hospital_id' => 1, 'specialty_id' => 4],
            
            // Bệnh viện Chợ Rẫy
            ['name' => 'Hoàng Văn Em', 'title' => 'GS.TS', 'hospital_id' => 2, 'specialty_id' => 1],
            ['name' => 'Vũ Thị Phương', 'title' => 'ThS.BS', 'hospital_id' => 2, 'specialty_id' => 5],
            ['name' => 'Đỗ Văn Giang', 'title' => 'BS.CKI', 'hospital_id' => 2, 'specialty_id' => 6],
            ['name' => 'Ngô Thị Hạnh', 'title' => 'BS.CKII', 'hospital_id' => 2, 'specialty_id' => 7],
            
            // Bệnh viện Đại học Y Dược
            ['name' => 'Phan Văn Hùng', 'title' => 'PGS.TS', 'hospital_id' => 3, 'specialty_id' => 8],
            ['name' => 'Đặng Thị Loan', 'title' => 'ThS.BS', 'hospital_id' => 3, 'specialty_id' => 1],
            ['name' => 'Lương Văn Minh', 'title' => 'BS.CKI', 'hospital_id' => 3, 'specialty_id' => 2],
            ['name' => 'Chu Thị Nga', 'title' => 'BS.CKII', 'hospital_id' => 3, 'specialty_id' => 3],
        ];

        foreach ($doctors as $doctor) {
            Doctor::create($doctor);
        }

        // Tạo lịch khám cho bác sĩ (30 ngày tới)
        $doctors = Doctor::all();
        $startDate = Carbon::now();
        
        foreach ($doctors as $doctor) {
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);
                
                // Bỏ qua chủ nhật
                if ($date->dayOfWeek === 0) {
                    continue;
                }
                
                // Tạo lịch sáng (8:00 - 11:30)
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'date' => $date->toDateString(),
                    'start_time' => '08:00',
                    'end_time' => '11:30',
                    'slot_duration' => 30,
                    'is_available' => true
                ]);
                
                // Tạo lịch chiều (13:30 - 17:00) - chỉ cho thứ 2, 4, 6
                if (in_array($date->dayOfWeek, [1, 3, 5])) {
                    DoctorSchedule::create([
                        'doctor_id' => $doctor->id,
                        'date' => $date->toDateString(),
                        'start_time' => '13:30',
                        'end_time' => '17:00',
                        'slot_duration' => 30,
                        'is_available' => true
                    ]);
                }
            }
        }
    }
}