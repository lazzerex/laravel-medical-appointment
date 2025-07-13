<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
Route::get('/', [AppointmentController::class, 'index'])->name('appointments.index');
Route::get('/doctors', [AppointmentController::class, 'getDoctorsByHospitalAndSpecialty'])->name('appointments.doctors');
Route::get('/available-slots', [AppointmentController::class, 'getAvailableSlots'])->name('appointments.slots');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');

// Route::get('/', function () {
//     return view('welcome');
// });
