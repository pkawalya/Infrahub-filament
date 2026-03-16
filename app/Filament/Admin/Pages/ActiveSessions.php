<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Jenssegers\Agent\Agent;

class ActiveSessions extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';
    protected static string|\UnitEnum|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 11;
    protected static ?string $title = 'Active Sessions';
    protected string $view = 'filament.admin.pages.active-sessions';

    public array $sessions = [];

    public function mount(): void
    {
        $this->loadSessions();
    }

    public function loadSessions(): void
    {
        if (config('session.driver') !== 'database') {
            $this->sessions = [];
            return;
        }

        $sessions = DB::table(config('session.table', 'sessions'))
            ->where('user_id', auth()->id())
            ->orderBy('last_activity', 'desc')
            ->get();

        $this->sessions = $sessions->map(function ($session) {
            $agent = $this->createAgent($session->user_agent);

            return (object) [
                'id' => $session->id,
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        })->toArray();
    }

    protected function createAgent($userAgent): Agent
    {
        return tap(new Agent, fn($agent) => $agent->setUserAgent($userAgent));
    }

    public function logoutOtherDeviceSessions(string $password): void
    {
        if (!\Illuminate\Support\Facades\Hash::check($password, auth()->user()->password)) {
            \Filament\Notifications\Notification::make()
                ->title('Invalid Password')
                ->danger()
                ->send();
            return;
        }

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', auth()->id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        $this->loadSessions();

        \Filament\Notifications\Notification::make()
            ->title('Other device sessions logged out')
            ->success()
            ->send();
    }
}
