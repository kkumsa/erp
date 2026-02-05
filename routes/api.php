<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 고객 API
    Route::apiResource('customers', \App\Http\Controllers\Api\CustomerController::class);

    // 프로젝트 API
    Route::apiResource('projects', \App\Http\Controllers\Api\ProjectController::class);

    // 청구서 API
    Route::apiResource('invoices', \App\Http\Controllers\Api\InvoiceController::class);
});
