<?php

use Illuminate\Support\Facades\Route;

Route::post('auth', [\App\Http\Controllers\AuthController::class, 'store']);
Route::get('auth', [\App\Http\Controllers\AuthController::class, 'show']);
Route::delete('auth', [\App\Http\Controllers\AuthController::class, 'destroy']);

Route::apiResource('offices', \App\Http\Controllers\OfficeController::class);

Route::apiResource('users', \App\Http\Controllers\UserController::class);

Route::post('user_access/{id}', [\App\Http\Controllers\UserAccessController::class, 'store']);
Route::delete('user_access/{id}', [\App\Http\Controllers\UserAccessController::class, 'destroy']);

Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
Route::get('role_search', [\App\Http\Controllers\RoleSearchController::class, 'index']);

Route::apiResource('permissions', \App\Http\Controllers\PermissionController::class);
Route::get('permission_search', [\App\Http\Controllers\PermissionSearchController::class, 'index']);
