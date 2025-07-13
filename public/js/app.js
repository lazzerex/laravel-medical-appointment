class AppointmentBooking {
    constructor() {
        this.currentStep = 1;
        this.maxSteps = 4;
        this.currentMonth = new Date().getMonth();
        this.currentYear = new Date().getFullYear();
        this.selectedDate = null;
        this.selectedTime = null;
        this.selectedDoctor = null;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateCalendar();
        this.updateButtons();

        // Setup CSRF token for AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.axios = window.axios || {};
        window.axios.defaults = window.axios.defaults || {};
        window.axios.defaults.headers = window.axios.defaults.headers || {};
        window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
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

                if (!hospital || !specialty || !doctor) {
                    alert('Vui lòng chọn đầy đủ thông tin bệnh viện, chuyên khoa và bác sĩ.');
                    return false;
                }
                return true;

            case 2:
                if (!this.selectedDate || !this.selectedTime) {
                    alert('Vui lòng chọn ngày và giờ khám.');
                    return false;
                }
                return true;

            case 3:
                const name = document.getElementById('name').value;
                const phone = document.getElementById('phone').value;
                const email = document.getElementById('email').value;

                if (!name || !phone || !email) {
                    alert('Vui lòng điền đầy đủ thông tin cá nhân.');
                    return false;
                }

                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert('Email không hợp lệ.');
                    return false;
                }

                this.submitAppointment();
                return false; // Prevent normal step progression

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
        }
    }

    updateCalendar() {
        const monthNames = [
            'Tháng 1, 2025', 'Tháng 2, 2025', 'Tháng 3, 2025', 'Tháng 4, 2025',
            'Tháng 5, 2025', 'Tháng 6, 2025', 'Tháng 7, 2025', 'Tháng 8, 2025',
            'Tháng 9, 2025', 'Tháng 10, 2025', 'Tháng 11, 2025', 'Tháng 12, 2025'
        ];

        document.getElementById('currentMonth').textContent = monthNames[this.currentMonth];

        const firstDay = new Date(this.currentYear, this.currentMonth, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - (firstDay.getDay() || 7) + 1);

        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';

        for (let i = 0; i < 42; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + i);

            const dayElement = document.createElement('div');
            dayElement.classList.add('calendar-day');
            dayElement.textContent = currentDate.getDate();

            if (currentDate.getMonth() !== this.currentMonth) {
                dayElement.classList.add('other-month');
            }

            if (currentDate < new Date().setHours(0, 0, 0, 0)) {
                dayElement.classList.add('disabled');
            } else {
                dayElement.addEventListener('click', () => this.selectDate(currentDate));
            }

            calendarDays.appendChild(dayElement);
        }
    }

    selectDate(date) {
        // Remove previous selection
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });

        // Add selection to clicked date
        event.target.classList.add('selected');

        this.selectedDate = date;
        document.getElementById('selectedDate').value = date.toISOString().split('T')[0];

        // Update time slot header
        const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        const dayName = dayNames[date.getDay()];
        const dateStr = `${dayName}, Th${date.getMonth() + 1} ${date.getDate()}`;
        document.querySelector('.time-slot-header').textContent = dateStr;

        // Load available time slots
        this.loadTimeSlots(date);
    }

    async loadTimeSlots(date) {
        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = '<div class="loading">Đang tải...</div>';

        if (!this.selectedDoctor) {
            timeSlotsContainer.innerHTML = '<div class="loading">Vui lòng chọn bác sĩ trước</div>';
            return;
        }

        // TẠO LỊCH MẪU ĐỂ TEST
        const mockSlots = [
            '08:00', '08:30', '09:00', '09:30', '10:00', '10:30',
            '14:00', '14:30', '15:00', '15:30', '16:00', '16:30'
        ];

        timeSlotsContainer.innerHTML = '';

        mockSlots.forEach(slot => {
            const slotElement = document.createElement('button');
            slotElement.type = 'button';
            slotElement.classList.add('time-slot');
            slotElement.textContent = slot;
            slotElement.addEventListener('click', () => this.selectTimeSlot(slot, slotElement));
            timeSlotsContainer.appendChild(slotElement);
        });
    }

    selectTimeSlot(time, element) {
        // Remove previous selection
        document.querySelectorAll('.time-slot.selected').forEach(slot => {
            slot.classList.remove('selected');
        });

        // Add selection to clicked slot
        element.classList.add('selected');

        this.selectedTime = time;
        document.getElementById('selectedTime').value = time;
    }

    prevMonth() {
        this.currentMonth--;
        if (this.currentMonth < 0) {
            this.currentMonth = 11;
            this.currentYear--;
        }
        this.updateCalendar();
    }

    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 11) {
            this.currentMonth = 0;
            this.currentYear++;
        }
        this.updateCalendar();
    }

    async submitAppointment() {
        const formData = new FormData(document.getElementById('appointmentForm'));

        try {
            const response = await fetch('/appointments', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const result = await response.json();

            if (result.success) {
                this.currentStep = 4;
                this.updateUI();
            } else {
                alert('Có lỗi xảy ra khi đặt hẹn. Vui lòng thử lại.');
            }
        } catch (error) {
            console.error('Error submitting appointment:', error);
            alert('Có lỗi xảy ra khi đặt hẹn. Vui lòng thử lại.');
        }
    }
}

// Initialize the application when the page loads
document.addEventListener('DOMContentLoaded', function () {
    new AppointmentBooking();
});