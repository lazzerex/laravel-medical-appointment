<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Hẹn Khám</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">Medical Appointment</a>
            <div class="d-flex">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary ms-2">Dashboard</a>
            </div>
        </div>
    </nav>
    <div class="container">
        <h1>ĐẶT HẸN KHÁM</h1>
        
        <div class="steps">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-title">Chọn vấn đề cần thăm khám</div>
                <div class="step-line"></div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-title">Chọn giờ khám</div>
                <div class="step-line"></div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-title">Thông tin cá nhân</div>
                <div class="step-line"></div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-title">Hoàn thành</div>
            </div>
        </div>

        <div class="form-container">
            <form id="appointmentForm">
                @csrf
                
                <!-- Step 1: Chọn bệnh viện và chuyên khoa -->
                <div class="form-step active" data-step="1">
                    <div class="description">
                        Vui lòng chọn địa điểm khám bệnh (bệnh viện) và dịch vụ khám.
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hospital">Chọn địa điểm khám bệnh</label>
                            <select id="hospital" name="hospital_id" required>
                                <option value="">Chọn địa điểm khám bệnh</option>
                                @foreach($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}">{{ $hospital->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="specialty">Dịch vụ khám</label>
                            <select id="specialty" name="specialty_id" required>
                                <option value="">Chọn dịch vụ khám</option>
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="doctor">Chọn bác sĩ</label>
                        <select id="doctor" name="doctor_id" required disabled>
                            <option value="">Vui lòng chọn địa điểm khám bệnh và dịch vụ khám trước</option>
                        </select>
                    </div>
                </div>

                <!-- Step 2: Chọn giờ khám -->
                <div class="form-step" data-step="2">
                    <div class="description">
                        Dưới đây là lịch khám của <strong id="doctorName"></strong>. Vui lòng chọn ngày giờ khám để đặt hẹn!
                    </div>
                    
                    <div class="calendar-container">
                        <div class="calendar">
                            <div class="calendar-header">
                                <button type="button" class="calendar-nav" id="prevMonth">&lt;</button>
                                <h3 id="currentMonth"></h3>
                                <button type="button" class="calendar-nav" id="nextMonth">&gt;</button>
                            </div>
                            
                            <div class="calendar-grid">
                                <div class="calendar-weekday">T2</div>
                                <div class="calendar-weekday">T3</div>
                                <div class="calendar-weekday">T4</div>
                                <div class="calendar-weekday">T5</div>
                                <div class="calendar-weekday">T6</div>
                                <div class="calendar-weekday">T7</div>
                                <div class="calendar-weekday">CN</div>
                            </div>
                            
                            <div id="calendarDays" class="calendar-grid"></div>
                        </div>
                        
                        <div class="time-slots">
                            <div class="time-slot-header">T2, Th7 14</div>
                            <div id="timeSlots" class="loading">Vui lòng chọn ngày</div>
                        </div>
                    </div>
                    
                    <input type="hidden" id="selectedDate" name="appointment_date">
                    <input type="hidden" id="selectedTime" name="appointment_time">
                </div>

                <!-- Step 3: Thông tin cá nhân -->
                <div class="form-step" data-step="3">
                    <div class="description">
                        Vui lòng điền thông tin liên hệ để hoàn tất đặt hẹn.
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Họ tên</label>
                            <input type="text" id="name" name="patient_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Điện thoại</label>
                            <div class="phone-input">
                                <select class="country-code">
                                    <option value="+84">🇻🇳 +84</option>
                                </select>
                                <input type="tel" id="phone" name="patient_phone" placeholder="912 345 678" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="patient_email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Ghi chú</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="Mô tả triệu chứng hoặc lý do khám..."></textarea>
                    </div>
                </div>

                <!-- Step 4: Hoàn thành -->
                <div class="form-step" data-step="4">
                    <div class="success-message">
                        Đặt hẹn khám của bạn đã hoàn tất. Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.
                    </div>
                </div>
            </form>
            
            <div class="btn-navigation">
                <button type="button" class="btn btn-secondary" id="prevBtn">QUAY LẠI</button>
                <button type="button" class="btn btn-primary" id="nextBtn">TIẾP TỤC</button>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>