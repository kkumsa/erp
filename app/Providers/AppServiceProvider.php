<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\Task;
use App\Models\Milestone;
use App\Models\Leave;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Payment;
use App\Observers\TaskObserver;
use App\Observers\MilestoneObserver;
use App\Observers\LeaveObserver;
use App\Observers\ExpenseObserver;
use App\Observers\PurchaseOrderObserver;
use App\Observers\LeadObserver;
use App\Observers\OpportunityObserver;
use App\Observers\PaymentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // HTTPS 강제 (프로덕션 환경 또는 프록시 뒤에서)
        if ($this->app->environment('production') || 
            request()->header('X-Forwarded-Proto') === 'https' ||
            str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Model Observer 등록
        Task::observe(TaskObserver::class);
        Milestone::observe(MilestoneObserver::class);
        Leave::observe(LeaveObserver::class);
        Expense::observe(ExpenseObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        Lead::observe(LeadObserver::class);
        Opportunity::observe(OpportunityObserver::class);
        Payment::observe(PaymentObserver::class);

        // Super Admin은 모든 권한을 가짐
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
