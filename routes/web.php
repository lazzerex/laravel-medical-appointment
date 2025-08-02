<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SpecialtyController;
use App\Http\Controllers\ScheduleController;

// Appointment booking (public)
Route::get('/', [AppointmentController::class, 'index'])->name('appointments.form');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/doctors', [AppointmentController::class, 'getDoctorsByHospitalAndSpecialty'])->name('appointments.doctors');
Route::get('/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.slots');

// NEW: Enhanced appointment booking endpoints
Route::get('/calendar-data', [AppointmentController::class, 'getCalendarData'])->name('appointments.calendar-data');
Route::get('/doctor-schedule', [AppointmentController::class, 'getDoctorSchedule'])->name('appointments.doctor-schedule');

// Dashboard (admin/management)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Doctor Management
Route::get('/dashboard/doctors', [DoctorController::class, 'index'])->name('dashboard.doctors');
Route::get('/dashboard/doctors/create', [DoctorController::class, 'create'])->name('dashboard.doctors.create');
Route::post('/dashboard/doctors', [DoctorController::class, 'store'])->name('dashboard.doctors.store');
Route::get('/dashboard/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('dashboard.doctors.edit');
Route::put('/dashboard/doctors/{doctor}', [DoctorController::class, 'update'])->name('dashboard.doctors.update');
Route::delete('/dashboard/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('dashboard.doctors.destroy');

// Appointment Management
Route::get('/dashboard/appointments', [AppointmentManagementController::class, 'index'])->name('dashboard.appointments');
Route::put('/dashboard/appointments/{appointment}', [AppointmentManagementController::class, 'update'])->name('dashboard.appointments.update');
Route::delete('/dashboard/appointments/{appointment}', [AppointmentManagementController::class, 'destroy'])->name('dashboard.appointments.destroy');

// Specialty Management
Route::resource('/dashboard/specialties', SpecialtyController::class)->names('dashboard.specialties');

// NEW: Schedule Management Routes
Route::prefix('dashboard/schedules')->name('schedules.')->group(function () {
    Route::get('/', [ScheduleController::class, 'index'])->name('index');
    Route::get('/{doctor}', [ScheduleController::class, 'show'])->name('show');
    
    // Weekly Schedule Management
    Route::get('/{doctor}/edit-weekly', [ScheduleController::class, 'editWeekly'])->name('edit-weekly');
    Route::put('/{doctor}/weekly', [ScheduleController::class, 'updateWeekly'])->name('update-weekly');
    
    // Schedule Exception Management
    Route::get('/{doctor}/create-exception', [ScheduleController::class, 'createException'])->name('create-exception');
    Route::post('/{doctor}/exceptions', [ScheduleController::class, 'storeException'])->name('store-exception');
    Route::get('/exceptions/{exception}/edit', [ScheduleController::class, 'editException'])->name('edit-exception');
    Route::put('/exceptions/{exception}', [ScheduleController::class, 'updateException'])->name('update-exception');
    Route::delete('/exceptions/{exception}', [ScheduleController::class, 'destroyException'])->name('destroy-exception');
    
    // API endpoints for schedule data
    Route::get('/{doctor}/availability', [ScheduleController::class, 'getAvailability'])->name('availability');
    Route::get('/{doctor}/month-availability', [ScheduleController::class, 'getMonthAvailability'])->name('month-availability');
});