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

// ── Financial Alerts: overdue invoices + subscription expiry (daily 6 AM) ──
Schedule::command('financial:process-alerts')
    ->dailyAt('06:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/financial-alerts.log'));

// ── Security: purge expired API tokens (daily 4 AM) ──
Schedule::command('sanctum:prune-expired')
    ->dailyAt('04:00')
    ->withoutOverlapping();

// ── Security: clean old audit logs (weekly Sunday 4 AM) ──
Schedule::command('security:audit --clean')
    ->weeklyOn(0, '04:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/security-cleanup.log'));

// ── Session: prune expired sessions (daily 5 AM) ──
Schedule::command('session:prune')
    ->dailyAt('05:00')
    ->withoutOverlapping();

// ── Backup: daily DB + uploads backup (3 AM) ──
Schedule::command('backup:run')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/backup.log'));

// ── Backup: cleanup old backups (weekly Sunday 3:30 AM) ──
Schedule::command('backup:clean')
    ->weeklyOn(0, '03:30')
    ->withoutOverlapping()
    ->onOneServer();

// ── Backup: monitor health (daily 6:30 AM) ──
Schedule::command('backup:monitor')
    ->dailyAt('06:30')
    ->onOneServer();

// ── Security: deactivate dormant users (weekly Monday 3 AM) ──
Schedule::command('users:deactivate-dormant --days=90')
    ->weeklyOn(1, '03:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/dormant-users.log'));

// ── Deadline reminders + subscription limit warnings (daily 7:30 AM) ──
Schedule::command('infrahub:deadline-reminders')
    ->dailyAt('07:30')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/deadline-reminders.log'));

// ── Activity log pruning: keep 180 days of audit history (1st of month 5 AM) ──
Schedule::command('activitylog:clean --days=180')
    ->monthlyOn(1, '05:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/activitylog-clean.log'));
