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
                        <form class="d-flex gap-2" method="GET" action="{{ route('dashboard.appointments') }}">
                            <div class="input-group">
                                <select class="form-select" name="status">
                                    <option value="">Tất cả trạng thái</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                </select>
                                <button class="btn btn-outline-secondary" type="submit">Lọc</button>
                            </div>
                        </form>
                        <div class="d-flex gap-2">
                            <a href="{{ route('dashboard.appointments') }}" class="btn btn-outline-secondary">Xem tất cả</a>
                        </div>
                    </div>

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
                                @foreach($appointments as $appointment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
                                    <td>{{ $appointment->patient_name }}</td>
                                    <td>{{ $appointment->doctor->title }} {{ $appointment->doctor->name }}</td>
                                    <td>
                                        @php
                                        $statusColor = match($appointment->status) {
                                            'pending' => 'warning',
                                            'confirmed' => 'success',
                                            'cancelled' => 'danger',
                                            default => 'primary'
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }}">
                                            {{ Str::ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($appointment->notes, 50) }}</td>
                                    <td>
                                        <form action="{{ route('dashboard.appointments.update', $appointment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                                <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                                                <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                                <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                                <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                            </select>
                                        </form>
                                        <form action="{{ route('dashboard.appointments.destroy', $appointment->id) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa cuộc hẹn này không?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($appointments, 'hasPages') && $appointments->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <!-- Previous Page Link -->
                                    @if ($appointments->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $appointments->previousPageUrl() }}" rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    <!-- Pagination Elements -->
                                    @php
                                        $lastPage = method_exists($appointments, 'lastPage') ? $appointments->lastPage() : 1;
                                    @endphp
                                    @for ($page = 1; $page <= $lastPage; $page++)
                                        @if ($page == $appointments->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $appointments->url($page) }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    <!-- Next Page Link -->
                                    @if ($appointments->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $appointments->nextPageUrl() }}" rel="next">&raquo;</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">&raquo;</span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection