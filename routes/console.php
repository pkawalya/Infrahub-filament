<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Jobs
|--------------------------------------------------------------------------
|
| All background processing jobs are registered here.
| Run the scheduler via: php artisan schedule:work (dev) or cron (prod).
|
| Production crontab entry:
|   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
| Queue worker:
|   php artisan queue:work --queue=boq,alerts,default --tries=3 --backoff=30
|
*/

// ── BOQ Variance Sync (daily at 2 AM) ──
Schedule::command('boq:sync-variance --all')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/boq-variance.log'));

// ── Loan Alerts Processing (daily at 7 AM) ──
Schedule::command('loan:process-alerts')
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/loan-alerts.log'));

// ── Prune old queue jobs (weekly Sunday 3 AM) ──
Schedule::command('queue:prune-batches --hours=72')
    ->weeklyOn(0, '03:00');

// ── Clear expired password reset tokens (daily) ──
Schedule::command('auth:clear-resets')
    ->daily();

// ── Monthly Billing Generation (1st of month at 1 AM) ──
Schedule::command('billing:generate --finalize')
    ->monthlyOn(1, '01:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/billing.log'));
