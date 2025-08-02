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
                                @endphp
                                
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="day-schedule-card border rounded p-3 {{ $isActive ? 'border-' . $dayInfo['color'] : '' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0 text-{{ $dayInfo['color'] }}">
                                                <i class="fas fa-calendar-day me-2"></i>{{ $dayInfo['name'] }}
                                            </h6>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input day-toggle" 
                                                       type="checkbox" 
                                                       id="day_{{ $dayOfWeek }}_active"
                                                       name="schedules[{{ $loop->index }}][is_active]"
                                                       value="1"
                                                       {{ $isActive ? 'checked' : '' }}
                                                       data-day="{{ $dayOfWeek }}">
                                                <label class="form-check-label" for="day_{{ $dayOfWeek }}_active">
                                                    <small>Làm việc</small>
                                                </label>
                                            </div>
                                        </div>

                                        <input type="hidden" name="schedules[{{ $loop->index }}][day_of_week]" value="{{ $dayOfWeek }}">
                                        
                                        <div class="time-inputs {{ !$isActive ? 'd-none' : '' }}" id="time_inputs_{{ $dayOfWeek }}">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="form-label small">Giờ bắt đầu</label>
                                                    <input type="time" 
                                                           class="form-control form-control-sm" 
                                                           name="schedules[{{ $loop->index }}][start_time]"
                                                           value="{{ $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '08:00' }}"
                                                           step="1800">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small">Giờ kết thúc</label>
                                                    <input type="time" 
                                                           class="form-control form-control-sm" 
                                                           name="schedules[{{ $loop->index }}][end_time]"
                                                           value="{{ $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00' }}"
                                                           step="1800">
                                                </div>
                                            </div>
                                            
                                            @if($isActive && $schedule)
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Tổng: {{ \Carbon\Carbon::parse($schedule->start_time)->diffInHours(\Carbon\Carbon::parse($schedule->end_time)) }} giờ
                                                        ({{ count($schedule->getTimeSlots()) }} khung 30 phút)
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

                        <!-- Quick Actions -->
                        <!-- <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-lightbulb me-2"></i>Thao tác nhanh:</h6>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" id="setWeekdays">
                                            Thứ 2 - Thứ 6 (8:00-17:00)
                                        </button>
                                        <button type="button" class="btn btn-outline-success" id="setSaturday">
                                            + Thứ 7 (8:00-12:00)
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" id="clearAll">
                                            Xóa tất cả
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Lưu ý: Thay đổi lịch sẽ không ảnh hưởng đến các cuộc hẹn đã đặt
                                </small>
                            </div>
                            <div>
                                <!-- <a href="{{ route('schedules.show', $doctor) }}" class="btn btn-secondary me-2">
                                    Hủy
                                </a> -->
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

.day-schedule-card.border-primary { background: rgba(13, 110, 253, 0.05); }
.day-schedule-card.border-success { background: rgba(25, 135, 84, 0.05); }
.day-schedule-card.border-info { background: rgba(13, 202, 240, 0.05); }
.day-schedule-card.border-warning { background: rgba(255, 193, 7, 0.05); }
.day-schedule-card.border-danger { background: rgba(220, 53, 69, 0.05); }
.day-schedule-card.border-secondary { background: rgba(108, 117, 125, 0.05); }
.day-schedule-card.border-dark { background: rgba(33, 37, 41, 0.05); }

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle time inputs when day is enabled/disabled
    document.querySelectorAll('.day-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            const day = this.dataset.day;
            const timeInputs = document.getElementById('time_inputs_' + day);
            const noScheduleMsg = document.getElementById('no_schedule_' + day);
            
            if (this.checked) {
                timeInputs.classList.remove('d-none');
                noScheduleMsg.classList.add('d-none');
            } else {
                timeInputs.classList.add('d-none');
                noScheduleMsg.classList.remove('d-none');
            }
            
            updatePreview();
        });
    });

    // Quick action buttons
    document.getElementById('setWeekdays').addEventListener('click', function() {
        // Enable Monday to Friday, 8:00-17:00
        for (let day = 1; day <= 5; day++) {
            const toggle = document.querySelector(`input[data-day="${day}"]`);
            const startTime = document.querySelector(`input[name*="[${day-1}][start_time]"]`);
            const endTime = document.querySelector(`input[name*="[${day-1}][end_time]"]`);
            
            if (toggle && startTime && endTime) {
                toggle.checked = true;
                startTime.value = '08:00';
                endTime.value = '17:00';
                
                // Trigger change event
                toggle.dispatchEvent(new Event('change'));
            }
        }
        updatePreview();
    });

    document.getElementById('setSaturday').addEventListener('click', function() {
        // Enable Saturday, 8:00-12:00
        const toggle = document.querySelector(`input[data-day="6"]`);
        const startTime = document.querySelector(`input[name*="[5][start_time]"]`);
        const endTime = document.querySelector(`input[name*="[5][end_time]"]`);
        
        if (toggle && startTime && endTime) {
            toggle.checked = true;
            startTime.value = '08:00';
            endTime.value = '12:00';
            
            toggle.dispatchEvent(new Event('change'));
        }
        updatePreview();
    });

    document.getElementById('clearAll').addEventListener('click', function() {
        document.querySelectorAll('.day-toggle').forEach(function(toggle) {
            toggle.checked = false;
            toggle.dispatchEvent(new Event('change'));
        });
        updatePreview();
    });

    // Update preview
    function updatePreview() {
        const preview = document.getElementById('schedulePreview');
        let previewHtml = '';
        
        const dayNames = ['', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
        
        for (let day = 1; day <= 7; day++) {
            const toggle = document.querySelector(`input[data-day="${day}"]`);
            const startTime = document.querySelector(`input[name*="[${day-1}][start_time]"]`);
            const endTime = document.querySelector(`input[name*="[${day-1}][end_time]"]`);
            
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

    // Listen to time input changes
    document.querySelectorAll('input[type="time"]').forEach(function(input) {
        input.addEventListener('change', updatePreview);
    });

    // Initial preview
    updatePreview();

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        let hasActiveDay = false;
        document.querySelectorAll('.day-toggle').forEach(function(toggle) {
            if (toggle.checked) hasActiveDay = true;
        });
        
        if (!hasActiveDay) {
            e.preventDefault();
            alert('Vui lòng chọn ít nhất một ngày làm việc!');
            return false;
        }
        
        // Validate time ranges
        let hasError = false;
        document.querySelectorAll('.day-toggle:checked').forEach(function(toggle, index) {
            const dayIndex = Array.from(document.querySelectorAll('.day-toggle')).indexOf(toggle);
            const startTime = document.querySelector(`input[name="schedules[${dayIndex}][start_time]"]`);
            const endTime = document.querySelector(`input[name="schedules[${dayIndex}][end_time]"]`);
            
            if (startTime && endTime && startTime.value >= endTime.value) {
                hasError = true;
                const dayName = toggle.closest('.day-schedule-card').querySelector('h6').textContent.trim().split(' ')[2];
                alert(`Giờ kết thúc phải sau giờ bắt đầu cho ${dayName}!`);
            }
        });
        
        if (hasError) {
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection