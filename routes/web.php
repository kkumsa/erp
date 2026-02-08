<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

/*
|--------------------------------------------------------------------------
| 로케일 전환 (로그인 페이지 등 비인증에서 사용)
|--------------------------------------------------------------------------
*/
Route::get('locale/switch/{locale}', function (string $locale) {
    if (! in_array($locale, ['ko', 'en'])) {
        abort(404);
    }
    session(['locale' => $locale]);

    return redirect()->back()->cookie('locale', $locale, 60 * 24 * 365);
})->where('locale', 'ko|en')->middleware('web')->name('locale.switch');

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
