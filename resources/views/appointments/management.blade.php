@extends('layouts.app')

@section('title', 'Quản lý Cuộc Hẹn')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('dashboard.doctors') }}" class="list-group-item list-group-item-action">Quản lý Bác sĩ</a>
                <a href="{{ route('dashboard.appointments') }}" class="list-group-item list-group-item-action active">Quản lý Cuộc Hẹn</a>
                <a href="{{ route('dashboard.specialties.index') }}" class="list-group-item list-group-item-action">Quản lý Dịch vụ</a>
            </div>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Quản lý Cuộc Hẹn</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <!-- Fixed filter form -->
                        <form class="d-flex gap-2" method="GET" action="{{ route('dashboard.appointments') }}">
                            <div class="input-group" style="width: 300px;">
                                <select class="form-select" name="status" id="statusFilter">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                </select>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                            </div>
                        </form>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('dashboard.appointments') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-refresh"></i> Xem tất cả
                            </a>
                        </div>
                    </div>

                    <!-- Display current filter status -->
                    @if(request('status'))
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        Đang hiển thị cuộc hẹn với trạng thái: 
                        <strong>
                            @switch(request('status'))
                                @case('pending')
                                    Chờ xác nhận
                                    @break
                                @case('confirmed')
                                    Đã xác nhận
                                    @break
                                @case('cancelled')
                                    Đã hủy
                                    @break
                                @case('completed')
                                    Đã hoàn thành
                                    @break
                                @default
                                    {{ request('status') }}
                            @endswitch
                        </strong>
                        <a href="{{ route('dashboard.appointments') }}" class="btn btn-sm btn-outline-primary ms-2">
                            Xóa bộ lọc
                        </a>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ngày hẹn</th>
                                    <th>Giờ hẹn</th>
                                    <th>Bệnh nhân</th>
                                    <th>Bác sĩ</th>
                                    <th>Trạng thái</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $appointment->patient_name }}</strong>
                                            @if($appointment->patient_phone)
                                                <br><small class="text-muted">{{ $appointment->patient_phone }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $appointment->doctor->title }} {{ $appointment->doctor->name }}</td>
                                    <td>
                                        @php
                                        $statusConfig = match($appointment->status) {
                                            'pending' => ['color' => 'warning', 'text' => 'Chờ xác nhận'],
                                            'confirmed' => ['color' => 'success', 'text' => 'Đã xác nhận'],
                                            'cancelled' => ['color' => 'danger', 'text' => 'Đã hủy'],
                                            'completed' => ['color' => 'primary', 'text' => 'Đã hoàn thành'],
                                            default => ['color' => 'secondary', 'text' => ucfirst($appointment->status)]
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $statusConfig['color'] }}">
                                            {{ $statusConfig['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($appointment->notes)
                                            <span title="{{ $appointment->notes }}">
                                                {{ Str::limit($appointment->notes, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Không có ghi chú</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <form action="{{ route('dashboard.appointments.update', $appointment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: 140px;">
                                                    <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                                    <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                                    <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                                    <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                                </select>
                                            </form>
                                            <form action="{{ route('dashboard.appointments.destroy', $appointment->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa cuộc hẹn này không?')" title="Xóa cuộc hẹn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                            <p>Không có cuộc hẹn nào được tìm thấy</p>
                                            @if(request('status'))
                                                <a href="{{ route('dashboard.appointments') }}" class="btn btn-sm btn-primary">
                                                    Xem tất cả cuộc hẹn
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination with filter preservation -->
                    @if(method_exists($appointments, 'hasPages') && $appointments->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $appointments->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Optional: Auto-submit on select change for better UX
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            // Auto-submit the form when selection changes for instant filtering
            this.form.submit();
        });
    }
});
</script>
@endsection