<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:superadmin,internal_hr'])->group(function () {
    Route::get('/employees/{employee}/families', [App\Http\Controllers\FamilyController::class, 'index'])->name('families.index');
    Route::get('/employees/{employee}/families/create', [App\Http\Controllers\FamilyController::class, 'create'])->name('families.create');
    Route::post('/employees/{employee}/families', [App\Http\Controllers\FamilyController::class, 'store'])->name('families.store');
    Route::get('/employees/{employee}/families/{family}/edit', [App\Http\Controllers\FamilyController::class, 'edit'])->name('families.edit');
    Route::put('/employees/{employee}/families/{family}', [App\Http\Controllers\FamilyController::class, 'update'])->name('families.update');
    Route::delete('/employees/{employee}/families/{family}', [App\Http\Controllers\FamilyController::class, 'destroy'])->name('families.destroy');
});
