@extends('layouts.app')

@section('title', 'Chỉnh sửa Dịch vụ')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <div class="list-group">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="{{ route('dashboard.doctors') }}" class="list-group-item list-group-item-action">Quản lý Bác sĩ</a>
                <a href="{{ route('dashboard.appointments') }}" class="list-group-item list-group-item-action">Quản lý Cuộc Hẹn</a>
                <a href="{{ route('dashboard.specialties.index') }}" class="list-group-item list-group-item-action active">Quản lý Dịch vụ</a>
            </div>
        </div>
        <div class="col-md-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Chỉnh sửa Dịch vụ: {{ $specialty->name }}</h1>
                <a href="{{ route('dashboard.specialties.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại Danh sách
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dashboard.specialties.update', $specialty) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên Dịch vụ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $specialty->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $specialty->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
