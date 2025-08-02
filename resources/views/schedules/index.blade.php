@extends('layouts.app')

@section('title', 'Quản lý Lịch Làm Việc')

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
                <h1>Quản lý Lịch Làm Việc Bác Sĩ</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Danh sách Bác sĩ và Lịch Làm Việc
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bác sĩ</th>
                                    <th>Bệnh viện</th>
                                    <th>Chuyên khoa</th>
                                    <th>Lịch tuần</th>
                                    <th>Ngoại lệ sắp tới</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($doctors as $doctor)
                                <tr>
                                    <td>
                                        <strong>{{ $doctor->title }} {{ $doctor->name }}</strong>
                                    </td>
                                    <td>{{ $doctor->hospital->name }}</td>
                                    <td>{{ $doctor->specialty->name }}</td>
                                    <td>
                                        @if($doctor->weeklySchedules->where('is_active', true)->count() > 0)
                                            <div class="schedule-preview">
                                                @foreach($doctor->weeklySchedules->where('is_active', true) as $schedule)
                                                    <span class="badge bg-success me-1 mb-1">
                                                        {{ $schedule->day_name }}: 
                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                                Chưa có lịch làm việc
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $upcomingExceptions = $doctor->scheduleExceptions()
                                                ->where('date', '>=', now()->toDateString())
                                                ->orderBy('date')
                                                ->limit(3)
                                                ->get();
                                        @endphp
                                        
                                        @if($upcomingExceptions->count() > 0)
                                            @foreach($upcomingExceptions as $exception)
                                                <div class="mb-1">
                                                    <span class="badge bg-{{ $exception->type === 'unavailable' || $exception->type === 'holiday' ? 'danger' : 'warning' }} me-1">
                                                        {{ \Carbon\Carbon::parse($exception->date)->format('d/m') }}
                                                    </span>
                                                    <small class="text-muted">{{ $exception->type_display }}</small>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Không có</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('schedules.show', $doctor) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="Xem chi tiết lịch">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('schedules.edit-weekly', $doctor) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Sửa lịch tuần">
                                                <i class="fas fa-calendar-week"></i>
                                            </a>
                                            <a href="{{ route('schedules.create-exception', $doctor) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Thêm ngoại lệ">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-user-md fa-2x mb-2"></i>
                                            <p>Không có bác sĩ nào trong hệ thống</p>
                                            <a href="{{ route('dashboard.doctors.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Thêm Bác sĩ
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số bác sĩ</h5>
                            <h2 class="card-text">{{ $doctors->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">Có lịch làm việc</h5>
                            <h2 class="card-text">{{ $doctors->filter(function($doctor) { return $doctor->weeklySchedules->where('is_active', true)->count() > 0; })->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">Chưa có lịch</h5>
                            <h2 class="card-text text-warning">{{ $doctors->filter(function($doctor) { return $doctor->weeklySchedules->where('is_active', true)->count() === 0; })->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stats-card">
                        <div class="card-body">
                            <h5 class="card-title">Ngoại lệ hôm nay</h5>
                            <h2 class="card-text">{{ $doctors->sum(function($doctor) { return $doctor->scheduleExceptions()->where('date', now()->toDateString())->count(); }) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.schedule-preview {
    max-width: 200px;
}

.badge {
    font-size: 0.7rem;
}

.btn-group .btn {
    margin-right: 2px;
}

.stats-card {
    transition: transform 0.2s;
}

.stats-card:hover {
    transform: translateY(-2px);
}
</style>
@endsection