<?php

namespace App\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use App\Models\Setting;

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
        // ── Safety Guard: Never allow debug mode in production ──
        if (app()->isProduction() && config('app.debug')) {
            abort(500, 'APP_DEBUG must be false in production. Set APP_DEBUG=false in your .env file.');
        }

        // ── Custom SMTP transport that bypasses Laravel's broken scheme detection ──
        // Register the 'infrahub' mailer in config so Laravel knows about it
        config(['mail.mailers.infrahub' => ['transport' => 'infrahub']]);
        config(['mail.default' => 'infrahub']);

        \Illuminate\Support\Facades\Mail::extend('infrahub', function () {
            // Cache mail settings for 1 hour to avoid DB queries on every request
            $mailConfig = cache()->remember('mail_settings', 3600, function () {
                return [
                    'host' => Setting::getValue('mail_host') ?: config('mail.mailers.smtp.host', 'smtp.gmail.com'),
                    'port' => (int) (Setting::getValue('mail_port') ?: config('mail.mailers.smtp.port', 587)),
                    'username' => Setting::getValue('mail_username') ?: config('mail.mailers.smtp.username', ''),
                    'password' => Setting::getValue('mail_password') ?: config('mail.mailers.smtp.password', ''),
                    'from_address' => Setting::getValue('mail_from_address') ?: config('mail.from.address', ''),
                    'from_name' => Setting::getValue('mail_from_name') ?: config('mail.from.name', config('app.name')),
                ];
            });

            $tls = ($mailConfig['port'] !== 465);
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($mailConfig['host'], $mailConfig['port'], $tls);
            $transport->setUsername($mailConfig['username']);
            $transport->setPassword($mailConfig['password']);

            config([
                'mail.from.address' => $mailConfig['from_address'],
                'mail.from.name' => $mailConfig['from_name'],
            ]);

            return $transport;
        });
        // ── Make ALL Select dropdowns searchable by default ──
        Select::configureUsing(fn(Select $select) => $select->searchable());

        // ── Admin bypass: super_admin & company_admin skip all policy checks ──
        Gate::before(function ($user, $ability) {
            if (in_array($user->user_type, ['super_admin', 'company_admin'])) {
                return true;
            }
        });

        // ── BOQ Variance auto-sync observers ──
        \App\Models\MaterialIssuance::observe(\App\Observers\MaterialIssuanceObserver::class);
        \App\Models\MaterialRequisitionItem::observe(\App\Observers\MaterialRequisitionItemObserver::class);

        // ── Module notification observers ──
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Task::observe(\App\Observers\TaskObserver::class);
        \App\Models\ChangeOrder::observe(\App\Observers\ChangeOrderObserver::class);
        \App\Models\PaymentCertificate::observe(\App\Observers\PaymentCertificateObserver::class);
        \App\Models\Drawing::observe(\App\Observers\DrawingObserver::class);
        \App\Models\SafetyIncident::observe(\App\Observers\SafetyIncidentObserver::class);
        \App\Models\DailySiteDiary::observe(\App\Observers\DailySiteDiaryObserver::class);
        \App\Models\Invoice::observe(\App\Observers\InvoiceObserver::class);
        \App\Models\WorkOrder::observe(\App\Observers\WorkOrderObserver::class);
        \App\Models\Rfi::observe(\App\Observers\RfiObserver::class);
        \App\Models\Submittal::observe(\App\Observers\SubmittalObserver::class);
        \App\Models\PurchaseOrder::observe(\App\Observers\PurchaseOrderObserver::class);
        \App\Models\MaterialRequisition::observe(\App\Observers\MaterialRequisitionObserver::class);
        \App\Models\SnagItem::observe(\App\Observers\SnagItemObserver::class);
        \App\Models\EquipmentAllocation::observe(\App\Observers\EquipmentAllocationObserver::class);

        // ── Flush mail cache when SMTP settings are changed ──
        // Ensures admin mail config changes take effect immediately.
        Setting::saved(function (Setting $setting) {
            $mailKeys = ['mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_from_address', 'mail_from_name'];
            if (in_array($setting->key ?? '', $mailKeys)) {
                \Illuminate\Support\Facades\Cache::forget('mail_settings');
            }
        });

        // Livewire components
        if (class_exists(\App\Filament\Resources\TicketResource\Pages\EditCommentModal::class)) {
            Livewire::component('edit-comment-modal', \App\Filament\Resources\TicketResource\Pages\EditCommentModal::class);
        }

        FilamentShield::buildPermissionKeyUsing(
            function (string $entity, string $affix, string $subject, string $case, string $separator) {
                return match (true) {
                    # if `configurePermissionIdentifierUsing()` was used previously, then this needs to be adjusted accordingly
                    is_subclass_of($entity, Resource::class) => Str::of($affix)
                        ->snake()
                        ->append('_')
                        ->append(
                            Str::of($entity)
                                ->afterLast('\\')
                                ->beforeLast('Resource')
                                ->replace('\\', '')
                                ->snake()
                                ->replace('_', '::')
                        )
                        ->toString(),
                    is_subclass_of($entity, Page::class) => Str::of('page_')
                        ->append(class_basename($entity))
                        ->toString(),
                    is_subclass_of($entity, Widget::class) => Str::of('widget_')
                        ->append(class_basename($entity))
                        ->toString()
                };
            }
        );
    }
}
