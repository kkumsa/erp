<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

/*
|--------------------------------------------------------------------------
| 사용자 환경설정 API (Filament 인증 기반)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth'])->prefix('admin/api')->group(function () {
    // 환경설정 조회
    Route::get('/preferences/{key}', function (string $key) {
        try {
            $user = auth()->user();
            return response()->json([
                'key' => $key,
                'value' => $user->getPreference($key),
            ]);
        } catch (\Throwable $e) {
            return response()->json(['key' => $key, 'value' => null], 200);
        }
    })->name('preferences.get');

    // 환경설정 저장
    Route::post('/preferences', function (\Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'key' => 'required|string|max:100',
            ]);

            $user = auth()->user();
            $user->setPreference($request->input('key'), $request->input('value'));

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'error' => '유효하지 않은 요청'], 422);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => '저장 실패'], 500);
        }
    })->name('preferences.set');
});
