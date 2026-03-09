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
        // ── Override SMTP transport: reads from DB settings first, .env fallback ──
        $this->app->afterResolving('mail.manager', function ($manager) {
            $manager->extend('smtp', function () {
                // DB settings (group=email) take priority over .env
                $host = Setting::getValue('mail_host') ?: config('mail.mailers.smtp.host', 'smtp.gmail.com');
                $port = (int) (Setting::getValue('mail_port') ?: config('mail.mailers.smtp.port', 587));
                $username = Setting::getValue('mail_username') ?: config('mail.mailers.smtp.username', '');
                $password = Setting::getValue('mail_password') ?: config('mail.mailers.smtp.password', '');
                $fromAddress = Setting::getValue('mail_from_address') ?: config('mail.from.address', '');
                $fromName = Setting::getValue('mail_from_name') ?: config('mail.from.name', config('app.name'));

                // Build transport with STARTTLS (port 587) or SSL (port 465)
                $tls = ($port !== 465); // port 465 = implicit SSL, others = STARTTLS
                $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($host, $port, $tls);
                $transport->setUsername($username);
                $transport->setPassword($password);

                // Apply from address to config so Laravel uses it
                config([
                    'mail.from.address' => $fromAddress,
                    'mail.from.name' => $fromName,
                ]);

                return $transport;
            });
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

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
