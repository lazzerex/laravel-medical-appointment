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
document.addEventListener('DOMContentLoaded', function () {
    const app = new EnhancedAppointmentBooking();

    // Add event listener for the manual submission button
    const submitBtn = document.getElementById('submitAppointmentBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', async function () {
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