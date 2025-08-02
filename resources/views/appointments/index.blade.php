@extends('layouts.app')

@section('title', 'Đặt Hẹn Khám')

@section('content')
<div class="d-flex mb-4" style="justify-content: flex-start;">
    <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="background: #000; margin-left: 20px;">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>
</div>
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
                    <div id="successContent">
                        <i class="fas fa-check-circle"></i>
                        <h3>Đặt hẹn khám của bạn đã hoàn tất!</h3>
                        <p>Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.</p>
                        <button type="button" id="submitAppointmentBtn" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi yêu cầu đặt hẹn
                        </button>
                    </div>
                    <div id="loadingContent" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Đang gửi yêu cầu đặt hẹn...</p>
                    </div>
                </div>
            </div>
        </form>

        <div class="btn-navigation">
            <button type="button" class="btn btn-secondary" id="prevBtn">QUAY LẠI</button>
            <button type="button" class="btn btn-primary" id="nextBtn">TIẾP TỤC</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Enhanced Appointment Booking Styles */

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8fafc;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        text-align: center;
        font-size: 2.5rem;
        color: #1a202c;
        margin-bottom: 30px;
        font-weight: 700;
        letter-spacing: -0.025em;
    }

    /* Step Indicator Styles */
    .steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
        padding: 0 20px;
    }

    .step {
        flex: 1;
        text-align: center;
        position: relative;
        padding: 0 10px;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .step.active .step-number {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .step.completed .step-number {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .step.completed .step-number::after {
        content: '✓';
        font-size: 18px;
        font-weight: bold;
    }

    .step-title {
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        transition: color 0.3s ease;
    }

    .step.active .step-title {
        color: #dc3545;
        font-weight: 700;
    }

    .step.completed .step-title {
        color: #28a745;
    }

    .step-line {
        position: absolute;
        top: 20px;
        left: 50%;
        right: -50%;
        height: 2px;
        background: linear-gradient(90deg, #e2e8f0, #cbd5e0);
        z-index: -1;
        transition: background 0.3s ease;
    }

    .step:last-child .step-line {
        display: none;
    }

    .step.completed .step-line {
        background: linear-gradient(90deg, #28a745, #20c997);
    }

    /* Form Container */
    .form-container {
        background: white;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-step {
        display: none;
        animation: fadeInUp 0.5s ease;
    }

    .form-step.active {
        display: block;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 24px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }

    select,
    input,
    textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        transition: all 0.3s ease;
        background-color: white;
    }

    select:focus,
    input:focus,
    textarea:focus {
        outline: none;
        border-color: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        transform: translateY(-1px);
    }

    select:disabled,
    input:disabled {
        background-color: #f9fafb;
        color: #9ca3af;
        cursor: not-allowed;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    /* Enhanced Calendar Styles */
    .calendar-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .calendar-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    .calendar {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 0 10px;
    }

    .calendar-header h3 {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .calendar-nav {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #6b7280;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .calendar-nav:hover {
        color: #dc3545;
        background-color: #fef2f2;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        margin-bottom: 20px;
    }

    .calendar-weekday {
        text-align: center;
        font-weight: 600;
        color: #6b7280;
        font-size: 12px;
        padding: 8px 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .calendar-day {
        min-height: 44px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
        border: 2px solid transparent;
        background: #f9fafb;
    }

    .calendar-day:hover {
        background-color: #f3f4f6;
        transform: scale(1.05);
    }

    .calendar-day.selected {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .calendar-day.disabled {
        color: #d1d5db;
        cursor: not-allowed;
        background: #f9fafb;
    }

    .calendar-day.disabled:hover {
        transform: none;
        background: #f9fafb;
    }

    .calendar-day.other-month {
        color: #d1d5db;
        background: transparent;
    }

    .calendar-day.today {
        border-color: #3b82f6;
        font-weight: 700;
    }

    /* Enhanced availability states */
    .calendar-day.available {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-color: #10b981;
        color: #065f46;
    }

    .calendar-day.available:hover {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        transform: scale(1.08);
    }

    .calendar-day.fully-booked {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-color: #f59e0b;
        color: #92400e;
        cursor: not-allowed;
    }

    .calendar-day.unavailable {
        background: linear-gradient(135deg, #fef2f2, #fecaca);
        border-color: #ef4444;
        color: #991b1b;
        cursor: not-allowed;
    }

    .slots-count {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Time Slots Section */
    .time-slots {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
    }

    .time-slot-header {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
    }

    #timeSlots {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        gap: 8px;
        max-height: 300px;
        overflow-y: auto;
    }

    .time-slot {
        display: block;
        width: 100%;
        padding: 10px 8px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        color: #374151;
    }

    .time-slot:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-color: #dc3545;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .time-slot.selected {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        border-color: #dc3545;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    .no-slots,
    .loading {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
        grid-column: 1 / -1;
    }

    .no-slots i,
    .loading i {
        font-size: 32px;
        margin-bottom: 12px;
        display: block;
        color: #d1d5db;
    }

    .slots-info {
        grid-column: 1 / -1;
        text-align: center;
        margin-top: 12px;
        padding: 8px 12px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-radius: 6px;
        font-size: 13px;
        color: #1e40af;
    }

    .slots-info i {
        margin-right: 6px;
    }

    /* Button Styles */
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-width: 120px;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    .btn-primary {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
    }

    .btn-primary:hover:not(:disabled) {
        background: linear-gradient(135deg, #c82333, #a71e2a);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 53, 69, 0.3);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #5a6268);
        color: white;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.2);
    }

    .btn-secondary:hover:not(:disabled) {
        background: linear-gradient(135deg, #5a6268, #495057);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(108, 117, 125, 0.3);
    }

    .btn-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid #e5e7eb;
    }

    /* Message Styles */
    .success-message {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        border: 1px solid #10b981;
        text-align: center;
        font-size: 16px;
        font-weight: 500;
    }

    .error-message,
    .error-alert {
        background: linear-gradient(135deg, #fef2f2, #fecaca);
        color: #991b1b;
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid #f87171;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .description {
        color: #6b7280;
        margin-bottom: 24px;
        line-height: 1.6;
        font-size: 15px;
    }

    /* Phone Input */
    .phone-input {
        display: flex;
        gap: 12px;
    }

    .country-code {
        width: 100px;
        flex-shrink: 0;
    }

    /* Navbar Enhancement */
    .navbar {
        background: linear-gradient(135deg, #1f2937, #374151) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 1rem 0;
        margin-bottom: 2rem;
    }

    .navbar-brand {
        color: white !important;
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: -0.025em;
    }

    .navbar .btn {
        padding: 8px 16px;
        font-size: 14px;
        min-width: auto;
    }

    /* Utility Classes */
    .hidden {
        display: none;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #6b7280;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    /* Loading Animation */
    .loading::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid #e5e7eb;
        border-radius: 50%;
        border-top-color: #dc3545;
        animation: spin 1s linear infinite;
        margin-left: 8px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .container {
            padding: 16px;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 24px;
        }

        .form-container {
            padding: 24px 20px;
        }

        .steps {
            margin-bottom: 32px;
            padding: 0 10px;
        }

        .step-title {
            font-size: 12px;
        }

        .btn-navigation {
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            width: 100%;
        }

        #timeSlots {
            grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
            gap: 6px;
        }

        .calendar-day {
            min-height: 36px;
            font-size: 13px;
        }
    }

    @media (max-width: 480px) {
        .step-number {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .calendar-grid {
            gap: 2px;
        }

        .calendar-day {
            min-height: 32px;
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    class EnhancedAppointmentBooking {
        constructor() {
            this.currentStep = 1;
            this.maxSteps = 4;
            this.currentMonth = new Date().getMonth();
            this.currentYear = new Date().getFullYear();
            this.selectedDate = null;
            this.selectedTime = null;
            this.selectedDoctor = null;
            this.doctorScheduleData = null;
            this.calendarData = {};

            this.init();
        }

        init() {
            this.setupEventListeners();
            this.updateCalendar();
            this.updateButtons();

            // Setup CSRF token for AJAX requests
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }
        }

        setupEventListeners() {
            // Navigation buttons
            document.getElementById('nextBtn').addEventListener('click', () => this.nextStep());
            document.getElementById('prevBtn').addEventListener('click', () => this.prevStep());

            // Hospital and specialty change
            document.getElementById('hospital').addEventListener('change', () => this.loadDoctors());
            document.getElementById('specialty').addEventListener('change', () => this.loadDoctors());

            // Doctor selection
            document.getElementById('doctor').addEventListener('change', (e) => {
                this.selectedDoctor = e.target.value;
                if (this.selectedDoctor) {
                    const doctorName = e.target.options[e.target.selectedIndex].text;
                    document.getElementById('doctorName').textContent = doctorName;
                    this.loadDoctorSchedule();
                }
            });

            // Calendar navigation
            document.getElementById('prevMonth').addEventListener('click', () => this.prevMonth());
            document.getElementById('nextMonth').addEventListener('click', () => this.nextMonth());

            // Form submission
            document.getElementById('appointmentForm').addEventListener('submit', (e) => {
                e.preventDefault();
                this.submitAppointment();
            });
        }

        nextStep() {
            if (this.validateCurrentStep()) {
                if (this.currentStep < this.maxSteps) {
                    this.currentStep++;
                    this.updateUI();

                    // Load calendar data when entering step 2
                    if (this.currentStep === 2 && this.selectedDoctor) {
                        this.loadCalendarData();
                    }
                }
            }
        }

        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateUI();
            }
        }

        validateCurrentStep() {
            switch (this.currentStep) {
                case 1:
                    const hospital = document.getElementById('hospital').value;
                    const specialty = document.getElementById('specialty').value;
                    const doctor = document.getElementById('doctor').value;

                    if (!hospital) {
                        this.showError('Vui lòng chọn bệnh viện');
                        return false;
                    }
                    if (!specialty) {
                        this.showError('Vui lòng chọn chuyên khoa');
                        return false;
                    }
                    if (!doctor) {
                        this.showError('Vui lòng chọn bác sĩ');
                        return false;
                    }
                    return true;

                case 2:
                    if (!this.selectedDate || !this.selectedTime) {
                        this.showError('Vui lòng chọn ngày và giờ khám');
                        return false;
                    }
                    return true;

                case 3:
                    const name = document.getElementById('name').value.trim();
                    const phone = document.getElementById('phone').value.trim();
                    const email = document.getElementById('email').value.trim();

                    if (!name) {
                        this.showError('Vui lòng nhập họ tên');
                        return false;
                    }
                    if (!phone) {
                        this.showError('Vui lòng nhập số điện thoại');
                        return false;
                    }
                    if (!email) {
                        this.showError('Vui lòng nhập email');
                        return false;
                    }

                    // Validate email format
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        this.showError('Email không hợp lệ');
                        return false;
                    }

                    // Validate phone
                    const phoneRegex = /^[0-9+\-\s()]{10,15}$/;
                    if (!phoneRegex.test(phone)) {
                        this.showError('Số điện thoại không hợp lệ');
                        return false;
                    }

                    return true;

                default:
                    return true;
            }
        }

        updateUI() {
            // Update steps
            document.querySelectorAll('.step').forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('active', 'completed');

                if (stepNum === this.currentStep) {
                    step.classList.add('active');
                } else if (stepNum < this.currentStep) {
                    step.classList.add('completed');
                }
            });

            // Update form steps
            document.querySelectorAll('.form-step').forEach((step, index) => {
                const stepNum = index + 1;
                step.classList.remove('active');

                if (stepNum === this.currentStep) {
                    step.classList.add('active');
                }
            });

            this.updateButtons();
        }

        updateButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            prevBtn.style.display = this.currentStep === 1 ? 'none' : 'inline-block';

            if (this.currentStep === this.maxSteps) {
                nextBtn.style.display = 'none';
            } else if (this.currentStep === 3) {
                nextBtn.textContent = 'HOÀN THÀNH';
            } else {
                nextBtn.textContent = 'TIẾP TỤC';
                nextBtn.style.display = 'inline-block';
            }
        }

        async loadDoctors() {
            const hospitalId = document.getElementById('hospital').value;
            const specialtyId = document.getElementById('specialty').value;
            const doctorSelect = document.getElementById('doctor');

            if (!hospitalId || !specialtyId) {
                doctorSelect.innerHTML = '<option value="">Vui lòng chọn bệnh viện và chuyên khoa trước</option>';
                doctorSelect.disabled = true;
                return;
            }

            try {
                doctorSelect.innerHTML = '<option value="">Đang tải...</option>';
                doctorSelect.disabled = true;

                const response = await fetch(`/doctors?hospital_id=${hospitalId}&specialty_id=${specialtyId}`);
                const doctors = await response.json();

                doctorSelect.innerHTML = '<option value="">Chọn bác sĩ</option>';
                doctors.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `${doctor.title} ${doctor.name}`;
                    doctorSelect.appendChild(option);
                });

                doctorSelect.disabled = false;
            } catch (error) {
                console.error('Error loading doctors:', error);
                doctorSelect.innerHTML = '<option value="">Lỗi khi tải danh sách bác sĩ</option>';
                this.showError('Không thể tải danh sách bác sĩ');
            }
        }

        async loadDoctorSchedule() {
            if (!this.selectedDoctor) return;

            try {
                const response = await fetch(`/doctor-schedule?doctor_id=${this.selectedDoctor}`);
                const data = await response.json();
                this.doctorScheduleData = data;
            } catch (error) {
                console.error('Error loading doctor schedule:', error);
            }
        }

        async loadCalendarData() {
            if (!this.selectedDoctor) return;

            try {
                const response = await fetch(`/calendar-data?doctor_id=${this.selectedDoctor}&year=${this.currentYear}&month=${this.currentMonth + 1}`);
                const data = await response.json();
                this.calendarData = data;
                this.updateCalendar();
            } catch (error) {
                console.error('Error loading calendar data:', error);
                // Fallback to basic calendar
                this.updateCalendar();
            }
        }

        updateCalendar() {
            const monthNames = [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4',
                'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8',
                'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ];

            document.getElementById('currentMonth').textContent =
                `${monthNames[this.currentMonth]} ${this.currentYear}`;

            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const startDate = new Date(firstDay);

            // Adjust to start from Monday
            const dayOfWeek = firstDay.getDay();
            const mondayOffset = dayOfWeek === 0 ? 6 : dayOfWeek - 1;
            startDate.setDate(firstDay.getDate() - mondayOffset);

            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            for (let i = 0; i < 42; i++) {
                const currentDate = new Date(startDate);
                currentDate.setDate(startDate.getDate() + i);

                const dayElement = this.createDayElement(currentDate);
                calendarDays.appendChild(dayElement);
            }
        }

        createDayElement(date) {
            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            dayElement.textContent = date.getDate();

            const isCurrentMonth = date.getMonth() === this.currentMonth;
            const isToday = date.toDateString() === new Date().toDateString();
            const isPast = date < new Date().setHours(0, 0, 0, 0);

            if (!isCurrentMonth) {
                dayElement.classList.add('other-month');
            }

            if (isToday) {
                dayElement.classList.add('today');
            }

            if (isPast) {
                dayElement.classList.add('disabled');
                return dayElement;
            }

            // Check if we have calendar data for this date
            if (this.calendarData && this.calendarData.calendar_data) {
                const dateStr = date.toISOString().split('T')[0];
                const dayData = this.calendarData.calendar_data.find(d => d.date === dateStr);

                if (dayData) {
                    if (dayData.is_available && dayData.available_slots_count > 0) {
                        dayElement.classList.add('available');
                        dayElement.title = `${dayData.available_slots_count} khung giờ trống`;

                        // Add slots count indicator
                        const slotsIndicator = document.createElement('span');
                        slotsIndicator.className = 'slots-count';
                        slotsIndicator.textContent = dayData.available_slots_count;
                        dayElement.appendChild(slotsIndicator);

                        dayElement.addEventListener('click', () => this.selectDate(date));
                    } else if (dayData.is_fully_booked) {
                        dayElement.classList.add('fully-booked');
                        dayElement.title = 'Đã hết chỗ';
                    } else {
                        dayElement.classList.add('unavailable');
                        dayElement.title = dayData.reason || 'Không có lịch làm việc';
                    }
                } else {
                    // No schedule data - assume unavailable
                    dayElement.classList.add('unavailable');
                    dayElement.title = 'Không có lịch làm việc';
                }
            } else {
                // No calendar data loaded yet - make clickable for basic functionality
                if (isCurrentMonth && !isPast) {
                    dayElement.addEventListener('click', () => this.selectDate(date));
                }
            }

            return dayElement;
        }

        async selectDate(date) {
            // Remove previous selection
            document.querySelectorAll('.calendar-day.selected').forEach(day => {
                day.classList.remove('selected');
            });

            // Add selection to clicked date
            event.target.classList.add('selected');

            this.selectedDate = date;
            // Format date as YYYY-MM-DD
            document.getElementById('selectedDate').value = date.getFullYear() + '-' +
                String(date.getMonth() + 1).padStart(2, '0') + '-' +
                String(date.getDate()).padStart(2, '0');

            // Update time slot header
            const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
            const dayName = dayNames[date.getDay()];
            const dateStr = `${dayName}, Th${date.getMonth() + 1} ${date.getDate()}`;
            document.querySelector('.time-slot-header').textContent = dateStr;

            // Load available time slots
            await this.loadTimeSlots(date);
        }

        async loadTimeSlots(date) {
            const timeSlotsContainer = document.getElementById('timeSlots');
            timeSlotsContainer.innerHTML = '<div class="loading">Đang tải khung giờ...</div>';

            if (!this.selectedDoctor) {
                timeSlotsContainer.innerHTML = '<div class="loading">Vui lòng chọn bác sĩ trước</div>';
                return;
            }

            try {
                const dateStr = date.toISOString().split('T')[0];
                const response = await fetch(`/available-slots?doctor_id=${this.selectedDoctor}&date=${dateStr}`);
                const data = await response.json();

                this.renderTimeSlots(data);

            } catch (error) {
                console.error('Error loading time slots:', error);
                timeSlotsContainer.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Không thể tải khung giờ trống</p>
                </div>
            `;
            }
        }

        renderTimeSlots(data) {
            const timeSlotsContainer = document.getElementById('timeSlots');

            if (data.status !== 'available' || !data.slots || data.slots.length === 0) {
                timeSlotsContainer.innerHTML = `
                <div class="no-slots">
                    <i class="fas fa-calendar-times"></i>
                    <p>${data.message || 'Không có khung giờ trống'}</p>
                </div>
            `;
                return;
            }

            timeSlotsContainer.innerHTML = '';

            // Create time slots
            data.slots.forEach(slot => {
                const slotElement = document.createElement('button');
                slotElement.type = 'button';
                slotElement.classList.add('time-slot');
                slotElement.textContent = slot.display || slot.time;
                slotElement.addEventListener('click', () => this.selectTimeSlot(slot.time, slotElement));
                timeSlotsContainer.appendChild(slotElement);
            });

            // Add info message
            if (data.message) {
                const messageElement = document.createElement('div');
                messageElement.className = 'slots-info';
                messageElement.innerHTML = `<i class="fas fa-info-circle"></i> ${data.message}`;
                timeSlotsContainer.appendChild(messageElement);
            }
        }

        selectTimeSlot(time, element) {
            // Remove previous selection
            document.querySelectorAll('.time-slot.selected').forEach(slot => {
                slot.classList.remove('selected');
            });

            // Add selection to clicked slot
            element.classList.add('selected');

            this.selectedTime = time;
            const timeInput = document.getElementById('selectedTime');
            timeInput.value = time;

            console.log('Time slot selected:', time);
            console.log('Time input value set to:', timeInput.value);
        }

        prevMonth() {
            this.currentMonth--;
            if (this.currentMonth < 0) {
                this.currentMonth = 11;
                this.currentYear--;
            }

            if (this.selectedDoctor) {
                this.loadCalendarData();
            } else {
                this.updateCalendar();
            }
        }

        nextMonth() {
            this.currentMonth++;
            if (this.currentMonth > 11) {
                this.currentMonth = 0;
                this.currentYear++;
            }

            if (this.selectedDoctor) {
                this.loadCalendarData();
            } else {
                this.updateCalendar();
            }
        }

        async submitAppointment() {
            const nextBtn = document.getElementById('nextBtn');
            const form = document.getElementById('appointmentForm');
            const formData = new FormData(form);

            // Log form data for debugging
            console.log('Form data before submission:');
            for (let [key, value] of formData.entries()) {
                console.log(key, ':', value);
            }

            // Validate required fields
            const requiredFields = ['hospital_id', 'specialty_id', 'doctor_id', 'patient_name', 'patient_phone', 'patient_email', 'appointment_date', 'appointment_time'];
            const missingFields = [];

            requiredFields.forEach(field => {
                if (!formData.get(field)) {
                    missingFields.push(field);
                }
            });

            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                this.showError('Vui lòng điền đầy đủ thông tin bắt buộc.');
                nextBtn.disabled = false;
                nextBtn.textContent = 'HOÀN THÀNH';
                return;
            }

            try {
                // Show loading state
                nextBtn.disabled = true;
                nextBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

                const response = await fetch('/appointments', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin'
                });

                const result = await response.json();

                if (result.success) {
                    // Move to success step
                    this.currentStep = 4;
                    this.updateUI();

                    // Update success message
                    const successMessage = document.querySelector('.success-message');
                    if (successMessage) {
                        successMessage.textContent = result.message;
                    }
                } else {
                    this.showError(result.message || 'Có lỗi xảy ra khi đặt hẹn');

                    if (result.errors) {
                        // Handle specific field errors
                        Object.keys(result.errors).forEach(field => {
                            console.error(`${field}: ${result.errors[field].join(', ')}`);
                        });

                        // If time slot error, refresh the slots
                        if (result.errors.appointment_time) {
                            this.loadTimeSlots(this.selectedDate);
                        }
                    }
                }

            } catch (error) {
                console.error('Error submitting appointment:', error);
                this.showError('Có lỗi xảy ra. Vui lòng thử lại');
            } finally {
                // Restore button state
                nextBtn.disabled = false;
                nextBtn.textContent = 'HOÀN THÀNH';
            }
        }

        showError(message) {
            // Remove existing error
            const existingError = document.querySelector('.error-alert');
            if (existingError) {
                existingError.remove();
            }

            // Create new error alert
            const errorAlert = document.createElement('div');
            errorAlert.className = 'error-alert error-message';
            errorAlert.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;

            // Insert before form
            const formContainer = document.querySelector('.form-container');
            formContainer.insertBefore(errorAlert, formContainer.firstChild);

            // Scroll to error
            errorAlert.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });

            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (errorAlert) {
                    errorAlert.remove();
                }
            }, 5000);
        }
    }

    // Initialize the enhanced application when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        const app = new EnhancedAppointmentBooking();

        // Add event listener for the manual submission button
        const submitBtn = document.getElementById('submitAppointmentBtn');
        if (submitBtn) {
            submitBtn.addEventListener('click', async function() {
                const form = document.getElementById('appointmentForm');
                const formData = new FormData(form);
                const successContent = document.getElementById('successContent');
                const loadingContent = document.getElementById('loadingContent');

                // Show loading state
                successContent.style.display = 'none';
                loadingContent.style.display = 'block';

                try {
                    const response = await fetch('/appointments', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        credentials: 'same-origin'
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Show success message
                        loadingContent.innerHTML = `
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <h3>Thành công!</h3>
                            <p>${result.message || 'Đặt hẹn khám thành công!'}</p>
                            <p>Mã đặt hẹn: ${result.appointment?.id || ''}</p>
                        `;

                        // Disable the submit button
                        if (submitBtn) submitBtn.style.display = 'none';
                    } else {
                        // Show error message
                        loadingContent.innerHTML = `
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <h3>Lỗi</h3>
                            <p>${result.message || 'Có lỗi xảy ra khi đặt hẹn. Vui lòng thử lại.'}</p>
                            <button type="button" class="btn btn-secondary mt-3" onclick="window.location.reload()">
                                <i class="fas fa-sync-alt"></i> Thử lại
                            </button>
                        `;
                    }
                } catch (error) {
                    console.error('Error submitting appointment:', error);
                    loadingContent.innerHTML = `
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <h3>Lỗi kết nối</h3>
                        <p>Không thể kết nối đến máy chủ. Vui lòng kiểm tra kết nối mạng và thử lại.</p>
                        <button type="button" class="btn btn-secondary mt-3" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt"></i> Thử lại
                        </button>
                    `;
                }
            });
        }
    });
</script>
@endpush