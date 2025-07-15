@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('doctors.*') ? 'active' : '' }}" href="{{ route('dashboard.doctors') }}">
                            <i class="fas fa-user-md me-2"></i>
                            Quản lý Bác sĩ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('appointments.management') ? 'active' : '' }}" href="{{ route('dashboard.appointments') }}">
                            <i class="fas fa-calendar-check me-2"></i>
                            Quản lý Cuộc Hẹn
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">@yield('title')</h1>
            </div>

            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 48px 0 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    }

    .sidebar .nav-link {
        font-weight: 500;
        color: #24292e;
        padding: .5rem 1rem;
    }

    .sidebar .nav-link:hover {
        color: #0d6efd;
    }

    .sidebar .nav-link.active {
        color: #0d6efd;
        background-color: #f8f9fa;
    }

    .main {
        padding-top: 2rem;
    }
</style>
@endpush
