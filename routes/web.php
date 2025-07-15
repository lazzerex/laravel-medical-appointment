<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentManagementController;
use App\Http\Controllers\DashboardController;

// Appointment booking (public)
Route::get('/', [AppointmentController::class, 'index'])->name('appointments.form');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/doctors', [AppointmentController::class, 'getDoctorsByHospitalAndSpecialty'])->name('appointments.doctors');
Route::get('/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.slots');

// Dashboard (admin/management)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard/doctors', [DoctorController::class, 'index'])->name('dashboard.doctors');
Route::get('/dashboard/doctors/create', [DoctorController::class, 'create'])->name('dashboard.doctors.create');
Route::post('/dashboard/doctors', [DoctorController::class, 'store'])->name('dashboard.doctors.store');
Route::get('/dashboard/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('dashboard.doctors.edit');
Route::put('/dashboard/doctors/{doctor}', [DoctorController::class, 'update'])->name('dashboard.doctors.update');
Route::delete('/dashboard/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('dashboard.doctors.destroy');

Route::get('/dashboard/appointments', [AppointmentManagementController::class, 'index'])->name('dashboard.appointments');
Route::put('/dashboard/appointments/{appointment}', [AppointmentManagementController::class, 'update'])->name('dashboard.appointments.update');
Route::delete('/dashboard/appointments/{appointment}', [AppointmentManagementController::class, 'destroy'])->name('dashboard.appointments.destroy');
