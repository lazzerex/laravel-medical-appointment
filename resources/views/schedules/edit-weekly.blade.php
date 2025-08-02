@extends('layouts.app')

@section('title', 'Chỉnh sửa Lịch Tuần - ' . $doctor->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('dashboard.doctors') }}" class="list-group-item list-group-item-action">Quản lý Bác sĩ</a>
                <a href="{{ route('dashboard.appointments') }}" class="list-group-item list-group-item-action">Quản lý Cuộc Hẹn</a>
                <a href="{{ route('dashboard.specialties.index') }}" class="list-group-item list-group-item-action">Quản lý Dịch vụ</a>
                <a href="{{ route('schedules.index') }}" class="list-group-item list-group-item-action active">Quản lý Lịch Làm Việc</a>
            </div>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1>Lịch Làm Việc Hàng Tuần</h1>
                    <p class="text-muted mb-0">
                        <strong>{{ $doctor->title }} {{ $doctor->name }}</strong> -
                        {{ $doctor->specialty->name }} - {{ $doctor->hospital->name }}
                    </p>
                </div>
                <div>
                    <!-- <a href="{{ route('schedules.show', $doctor) }}" class="btn btn-secondary me-2">
                        <i class="fas fa-eye"></i> Xem Chi Tiết
                    </a> -->
                    <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Có lỗi xảy ra:</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week me-2"></i>
                        Cấu hình lịch làm việc hàng tuần
                    </h5>
                    <small class="text-muted">Khung giờ được chia thành các slot 30 phút</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('schedules.update-weekly', $doctor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            @php
                            $days = [
                            1 => ['name' => 'Thứ 2', 'color' => 'primary'],
                            2 => ['name' => 'Thứ 3', 'color' => 'success'],
                            3 => ['name' => 'Thứ 4', 'color' => 'info'],
                            4 => ['name' => 'Thứ 5', 'color' => 'warning'],
                            5 => ['name' => 'Thứ 6', 'color' => 'danger'],
                            6 => ['name' => 'Thứ 7', 'color' => 'secondary'],
                            7 => ['name' => 'Chủ nhật', 'color' => 'dark']
                            ];
                            @endphp

                            @foreach($days as $dayOfWeek => $dayInfo)
                            @php
                            $schedule = $weeklySchedules->get($dayOfWeek);
                            $isActive = $schedule && $schedule->is_active;
                            $dayIndex = $loop->index; // This gives us 0, 1, 2, 3, 4, 5, 6
                            @endphp

                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="day-schedule-card border rounded p-3 {{ $isActive ? 'border-' . $dayInfo['color'] : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-{{ $dayInfo['color'] }}">
                                            <i class="fas fa-calendar-day me-2"></i>{{ $dayInfo['name'] }}
                                        </h6>
                                        <div class="form-check form-switch">
                                            <!-- Hidden input to ensure a value is always sent -->
                                            <input type="hidden" name="schedules[{{ $dayIndex }}][is_active]" value="0">
                                            <input class="form-check-input day-toggle"
                                                type="checkbox"
                                                id="day_{{ $dayOfWeek }}_active"
                                                name="schedules[{{ $dayIndex }}][is_active]"
                                                value="1"
                                                {{ $isActive ? 'checked' : '' }}
                                                data-day="{{ $dayOfWeek }}">
                                            <label class="form-check-label" for="day_{{ $dayOfWeek }}_active">
                                                <small>Làm việc</small>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Always include day_of_week -->
                                    <input type="hidden" name="schedules[{{ $dayIndex }}][day_of_week]" value="{{ $dayOfWeek }}">

                                    <div class="time-inputs {{ !$isActive ? 'd-none' : '' }}" id="time_inputs_{{ $dayOfWeek }}">
                                        <div class="row">
                                            <div class="col-6">
                                                <label class="form-label small">Giờ bắt đầu</label>
                                                <input type="time"
                                                    class="form-control form-control-sm"
                                                    name="schedules[{{ $dayIndex }}][start_time]"
                                                    value="{{ $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '08:00' }}"
                                                    step="1800"
                                                    {{ !$isActive ? 'disabled' : '' }}>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small">Giờ kết thúc</label>
                                                <input type="time"
                                                    class="form-control form-control-sm"
                                                    name="schedules[{{ $dayIndex }}][end_time]"
                                                    value="{{ $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00' }}"
                                                    step="1800"
                                                    {{ !$isActive ? 'disabled' : '' }}>
                                            </div>
                                        </div>

                                        @if($isActive && $schedule)
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                Tổng: {{ \Carbon\Carbon::parse($schedule->start_time)->diffInHours(\Carbon\Carbon::parse($schedule->end_time)) }} giờ
                                                @if(method_exists($schedule, 'getTimeSlots'))
                                                ({{ count($schedule->getTimeSlots()) }} khung 30 phút)
                                                @endif
                                            </small>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="no-schedule-message {{ $isActive ? 'd-none' : '' }}" id="no_schedule_{{ $dayOfWeek }}">
                                        <small class="text-muted">
                                            <i class="fas fa-moon me-1"></i>Không làm việc
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Lưu ý: Thay đổi lịch sẽ không ảnh hưởng đến các cuộc hẹn đã đặt
                                </small>
                            </div>
                            <div>
                                <a href="{{ route('schedules.index') }}" class="btn btn-secondary me-2">
                                    Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Lưu Lịch Làm Việc
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Xem trước lịch làm việc
                    </h6>
                </div>
                <div class="card-body">
                    <div id="schedulePreview" class="schedule-preview">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .day-schedule-card {
        transition: all 0.3s ease;
        background: #fafafa;
    }

    .day-schedule-card.border-primary {
        background: rgba(13, 110, 253, 0.05);
    }

    .day-schedule-card.border-success {
        background: rgba(25, 135, 84, 0.05);
    }

    .day-schedule-card.border-info {
        background: rgba(13, 202, 240, 0.05);
    }

    .day-schedule-card.border-warning {
        background: rgba(255, 193, 7, 0.05);
    }

    .day-schedule-card.border-danger {
        background: rgba(220, 53, 69, 0.05);
    }

    .day-schedule-card.border-secondary {
        background: rgba(108, 117, 125, 0.05);
    }

    .day-schedule-card.border-dark {
        background: rgba(33, 37, 41, 0.05);
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .schedule-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .preview-day {
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .preview-active {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .preview-inactive {
        background: #e9ecef;
        color: #6c757d;
    }

    /* Auto-save status styles */
    #saveStatus {
        position: sticky;
        top: 20px;
        z-index: 1000;
        margin-bottom: 20px;
    }

    .auto-save-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        z-index: 9999;
        display: none;
    }

    .auto-save-indicator.saving {
        background: rgba(255, 193, 7, 0.9);
        color: #000;
        display: block;
    }

    .auto-save-indicator.saved {
        background: rgba(40, 167, 69, 0.9);
        display: block;
    }

    .auto-save-indicator.error {
        background: rgba(220, 53, 69, 0.9);
        display: block;
    }

    /* Enhanced form styles */
    .day-schedule-card {
        transition: all 0.3s ease;
        position: relative;
    }

    .day-schedule-card.has-changes {
        border-left: 4px solid #ffc107;
        background: rgba(255, 193, 7, 0.05);
    }

    .day-schedule-card.has-changes::before {
        content: '●';
        position: absolute;
        top: -5px;
        right: -5px;
        color: #ffc107;
        font-size: 20px;
    }

    /* Loading state for inputs */
    input:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Notification animations */
    .alert {
        animation: slideInRight 0.3s ease;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Save button enhancements */
    .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #20c997, #17a2b8);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(40, 167, 69, 0.3);
    }

    /* Preview enhancements */
    .preview-day {
        transition: all 0.3s ease;
    }

    .preview-day.preview-active {
        animation: pulseGreen 0.5s ease;
    }

    @keyframes pulseGreen {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
            background-color: #d4edda;
        }

        100% {
            transform: scale(1);
        }
    }

    
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let autoSaveInterval;
        let hasChanges = false;
        let lastSavedData = null;

        // Initialize auto-save functionality
        initAutoSave();

        function initAutoSave() {
            // Capture initial form state
            lastSavedData = getFormData();

            // Start auto-save timer 
            //autoSaveInterval = setInterval(autoSave, 10000);

            // Save on page unload
            window.addEventListener('beforeunload', function(e) {
                if (hasChanges) {
                    saveImmediately();
                }
            });

            // Track changes
            setupChangeTracking();

            // Manual save button
            setupManualSave();

            console.log('Auto-save initialized');
        }

        function setupChangeTracking() {
            // Track checkbox changes
            document.querySelectorAll('.day-toggle').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    const day = this.dataset.day;
                    const timeInputs = document.getElementById('time_inputs_' + day);
                    const noScheduleMsg = document.getElementById('no_schedule_' + day);

                    if (this.checked) {
                        timeInputs.classList.remove('d-none');
                        noScheduleMsg.classList.add('d-none');
                        // Enable time inputs
                        timeInputs.querySelectorAll('input[type="time"]').forEach(input => {
                            input.disabled = false;
                        });
                    } else {
                        timeInputs.classList.add('d-none');
                        noScheduleMsg.classList.remove('d-none');
                        // Disable time inputs
                        timeInputs.querySelectorAll('input[type="time"]').forEach(input => {
                            input.disabled = true;
                        });
                    }

                    markAsChanged();
                    updatePreview();
                    updateSaveStatus('Có thay đổi chưa lưu...', 'warning');
                });
            });

            // Track time input changes
            document.querySelectorAll('input[type="time"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    markAsChanged();
                    updatePreview();
                    updateSaveStatus('Có thay đổi chưa lưu...', 'warning');
                });
            });
        }

        function setupManualSave() {
            // Replace form submission with AJAX
            const form = document.querySelector('form');
            const submitBtn = form.querySelector('button[type="submit"]');

            // Prevent default form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                saveImmediately();
            });

            // Add manual save button
            const manualSaveBtn = document.createElement('button');
            manualSaveBtn.type = 'button';
            manualSaveBtn.className = 'btn btn-success me-2';
            manualSaveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Lưu Ngay';
            manualSaveBtn.addEventListener('click', saveImmediately);

            // Insert before the original submit button
            submitBtn.parentNode.insertBefore(manualSaveBtn, submitBtn);

            // Hide original submit button
            submitBtn.style.display = 'none';
        }

        function markAsChanged() {
            hasChanges = true;
        }

        function getFormData() {
            const form = document.querySelector('form');
            const formData = new FormData(form);
            const data = {};

            // Convert FormData to regular object for comparison
            for (let [key, value] of formData.entries()) {
                if (data[key]) {
                    // Handle multiple values for same key
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            }

            return JSON.stringify(data);
        }

        function hasFormChanged() {
            const currentData = getFormData();
            return currentData !== lastSavedData;
        }

        function autoSave() {
            if (hasChanges && hasFormChanged()) {
                console.log('Auto-saving...');
                performSave(false); // false = auto save, not manual
            }
        }

        function saveImmediately() {
            console.log('Manual save triggered');
            performSave(true); // true = manual save
        }

        function performSave(isManual = false) {
            const form = document.querySelector('form');
            const formData = new FormData(form);

            // Validate before saving
            if (!validateForm()) {
                if (isManual) {
                    updateSaveStatus('Dữ liệu không hợp lệ!', 'danger');
                }
                return;
            }

            // Show saving status
            updateSaveStatus(isManual ? 'Đang lưu...' : 'Tự động lưu...', 'info');

            // Debug: Log form data
            console.log('Saving form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF token not found');
                updateSaveStatus('Lỗi bảo mật!', 'danger');
                return;
            }

            // Perform AJAX save
            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json().then(data => ({
                        data,
                        status: response.status
                    }));
                })
                .then(({
                    data,
                    status
                }) => {
                    console.log('Response data:', data);

                    if (status >= 200 && status < 300) {
                        // Success
                        hasChanges = false;
                        lastSavedData = getFormData();
                        updateSaveStatus(
                            isManual ? 'Đã lưu thành công!' : 'Tự động lưu thành công',
                            'success'
                        );

                        if (data.message) {
                            showNotification(data.message, 'success');
                        }
                    } else {
                        // Error response
                        console.error('Save failed:', data);
                        updateSaveStatus('Lưu thất bại!', 'danger');

                        if (data.errors) {
                            showValidationErrors(data.errors);
                        } else if (data.message) {
                            showNotification(data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Save error:', error);
                    updateSaveStatus('Lỗi kết nối!', 'danger');
                    showNotification('Không thể kết nối đến máy chủ', 'error');
                });
        }

        function validateForm() {
            // Check if at least one day is active
            let hasActiveDay = false;
            document.querySelectorAll('.day-toggle:checked').forEach(function(toggle) {
                hasActiveDay = true;
            });

            if (!hasActiveDay) {
                return false;
            }

            // Validate time ranges for active days
            let hasError = false;
            document.querySelectorAll('.day-toggle:checked').forEach(function(toggle) {
                const day = toggle.dataset.day;
                const dayIndex = parseInt(day) - 1;
                const startTime = document.querySelector(`input[name="schedules[${dayIndex}][start_time]"]`);
                const endTime = document.querySelector(`input[name="schedules[${dayIndex}][end_time]"]`);

                if (startTime && endTime && startTime.value && endTime.value) {
                    if (startTime.value >= endTime.value) {
                        hasError = true;
                    }
                }
            });

            return !hasError;
        }

        function updateSaveStatus(message, type) {
            let statusElement = document.getElementById('saveStatus');

            if (!statusElement) {
                // Create status element
                statusElement = document.createElement('div');
                statusElement.id = 'saveStatus';
                statusElement.className = 'alert alert-info mt-2';

                // Insert after form
                const form = document.querySelector('form');
                form.parentNode.insertBefore(statusElement, form.nextSibling);
            }

            // Update status
            statusElement.className = `alert alert-${type} mt-2`;
            statusElement.innerHTML = `<i class="fas fa-info-circle me-2"></i>${message}`;

            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    statusElement.style.display = 'none';
                }, 3000);
            } else {
                statusElement.style.display = 'block';
            }
        }

        function showNotification(message, type) {
            // Create notification
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
            notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

            // Insert at top of container
            const container = document.querySelector('.col-md-10');
            container.insertBefore(notification, container.firstChild);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        function showValidationErrors(errors) {
            Object.keys(errors).forEach(field => {
                console.error(`${field}: ${errors[field].join(', ')}`);
            });

            showNotification('Vui lòng kiểm tra lại dữ liệu nhập', 'error');
        }

        function updatePreview() {
            const preview = document.getElementById('schedulePreview');
            if (!preview) return;

            let previewHtml = '';
            const dayNames = ['', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];

            for (let day = 1; day <= 7; day++) {
                const toggle = document.querySelector(`input[data-day="${day}"]`);
                const dayIndex = day - 1;
                const startTime = document.querySelector(`input[name="schedules[${dayIndex}][start_time]"]`);
                const endTime = document.querySelector(`input[name="schedules[${dayIndex}][end_time]"]`);

                if (toggle && toggle.checked && startTime && endTime && startTime.value && endTime.value) {
                    previewHtml += `<div class="preview-day preview-active">
                    ${dayNames[day]}: ${startTime.value} - ${endTime.value}
                </div>`;
                } else {
                    previewHtml += `<div class="preview-day preview-inactive">
                    ${dayNames[day]}: Nghỉ
                </div>`;
                }
            }

            preview.innerHTML = previewHtml;
        }

        // Initial preview
        updatePreview();

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (autoSaveInterval) {
                clearInterval(autoSaveInterval);
            }
        });

        console.log('Enhanced schedule editor with auto-save loaded');
    });
</script>
@endsection