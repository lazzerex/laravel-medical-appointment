@extends('layouts.app')

@section('title', 'Quản lý Dịch vụ')

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
                <h1>Quản lý Dịch vụ</h1>
                <a href="{{ route('dashboard.specialties.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm Dịch vụ Mới
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Dịch vụ</th>
                                    <th>Mô tả</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($specialties as $specialty)
                                    <tr>
                                        <td>{{ $specialty->id }}</td>
                                        <td>{{ $specialty->name }}</td>
                                        <td>{{ $specialty->description ?? 'Không có mô tả' }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ route('dashboard.specialties.edit', $specialty) }}" class="btn btn-sm btn-primary me-2">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('dashboard.specialties.destroy', $specialty) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa dịch vụ này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($specialties, 'hasPages') && $specialties->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            <nav>
                                <ul class="pagination pagination-sm">
                                    <!-- Previous Page Link -->
                                    @if ($specialties->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">&laquo;</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $specialties->previousPageUrl() }}" rel="prev">&laquo;</a>
                                        </li>
                                    @endif

                                    <!-- Pagination Elements -->
                                    @php
                                        $lastPage = method_exists($specialties, 'lastPage') ? $specialties->lastPage() : 1;
                                    @endphp
                                    @for ($page = 1; $page <= $lastPage; $page++)
                                        @if ($page == $specialties->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $page }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $specialties->url($page) }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    <!-- Next Page Link -->
                                    @if ($specialties->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $specialties->nextPageUrl() }}" rel="next">&raquo;</a>
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
