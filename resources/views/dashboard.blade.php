@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action active">Dashboard</a>
                <a href="{{ route('dashboard.doctors') }}" class="list-group-item list-group-item-action">Quản lý Bác sĩ</a>
                <a href="{{ route('dashboard.appointments') }}" class="list-group-item list-group-item-action">Quản lý Cuộc Hẹn</a>
            </div>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Bảng điều khiển</h1>
            </div>

            <div class="row">
                <!-- stats -->
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số bác sĩ</h5>
                            <h2 class="card-text">{{ $stats['total_doctors'] }}</h2>
                            <a href="{{ route('dashboard.doctors') }}" class="card-link">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số cuộc hẹn</h5>
                            <h2 class="card-text">{{ $stats['total_appointments'] }}</h2>
                            <a href="{{ route('dashboard.appointments') }}" class="card-link">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cuộc hẹn chờ xác nhận</h5>
                            <h2 class="card-text">{{ $stats['pending_appointments'] }}</h2>
                            <a href="{{ route('dashboard.appointments') }}?status=pending" class="card-link">Xem chi tiết</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Cuộc hẹn đã xác nhận</h5>
                            <h2 class="card-text">{{ $stats['confirmed_appointments'] }}</h2>
                            <a href="{{ route('dashboard.appointments') }}?status=confirmed" class="card-link">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- recent appointments -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cuộc hẹn gần đây</h5>
                    <a href="{{ route('dashboard.appointments') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày hẹn</th>
                                    <th>Bệnh nhân</th>
                                    <th>Bác sĩ</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_appointments as $appointment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</td>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection