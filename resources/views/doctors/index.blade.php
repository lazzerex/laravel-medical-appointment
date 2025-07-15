@extends('layouts.app')

@section('title', 'Quản lý Bác sĩ')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('dashboard.doctors') }}" class="list-group-item list-group-item-action active">Quản lý Bác sĩ</a>
                <a href="{{ route('dashboard.appointments') }}" class="list-group-item list-group-item-action">Quản lý Cuộc Hẹn</a>
            </div>
        </div>
        <div class="col-md-10">

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý Bác sĩ</h1>
        <a href="{{ route('dashboard.doctors') }}" class="btn btn-secondary">Quay lại</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Họ và tên</th>
                            <th>Chức danh</th>
                            <th>Bệnh viện</th>
                            <th>Chuyên khoa</th>
                            <th>Giới thiệu</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doctor)
                        <tr>
                            <td>{{ $doctor->name }}</td>
                            <td>{{ $doctor->title }}</td>
                            <td>{{ $doctor->hospital->name }}</td>
                            <td>{{ $doctor->specialty->name }}</td>
                            <td>{{ Str::limit($doctor->bio, 100) }}</td>
                            <td>
                                <a href="{{ route('dashboard.doctors.edit', $doctor->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('dashboard.doctors.destroy', $doctor->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa bác sĩ này không?')">Xóa</button>
                                </form>
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
</div>
@endsection
