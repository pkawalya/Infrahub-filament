<x-filament-panels::page>
    <div class="max-w-xl mx-auto">
        @php
            $bannerConfig = match ($this->reason) {
                'expired' => [
                    'icon' => 'heroicon-o-clock',
                    'title' => 'Password Expired',
                    'message' => 'Your password is over ' . config('security.password.max_age_days', 90) . ' days old. Please set a new password.',
                    'bg' => 'bg-danger-50 dark:bg-danger-500/10',
                    'ring' => 'ring-danger-200 dark:ring-danger-500/20',
                    'iconColor' => 'text-danger-600 dark:text-danger-400',
                    'titleColor' => 'text-danger-800 dark:text-danger-200',
                    'textColor' => 'text-danger-700 dark:text-danger-300',
                ],
                'admin_forced' => [
                    'icon' => 'heroicon-o-shield-exclamation',
                    'title' => 'Password Reset Required',
                    'message' => 'A password change has been required for your account.',
                    'bg' => 'bg-warning-50 dark:bg-warning-500/10',
                    'ring' => 'ring-warning-200 dark:ring-warning-500/20',
                    'iconColor' => 'text-warning-600 dark:text-warning-400',
                    'titleColor' => 'text-warning-800 dark:text-warning-200',
                    'textColor' => 'text-warning-700 dark:text-warning-300',
                ],
                default => [
                    'icon' => 'heroicon-o-exclamation-triangle',
                    'title' => 'Set Your Password',
                    'message' => 'Please set a personal password before continuing.',
                    'bg' => 'bg-warning-50 dark:bg-warning-500/10',
                    'ring' => 'ring-warning-200 dark:ring-warning-500/20',
                    'iconColor' => 'text-warning-600 dark:text-warning-400',
                    'titleColor' => 'text-warning-800 dark:text-warning-200',
                    'textColor' => 'text-warning-700 dark:text-warning-300',
                ],
            };
        @endphp

        <div class="mb-6 rounded-xl {{ $bannerConfig['bg'] }} p-4 ring-1 {{ $bannerConfig['ring'] }}">
            <div class="flex gap-3">
                <x-dynamic-component :component="$bannerConfig['icon']"
                    class="h-6 w-6 {{ $bannerConfig['iconColor'] }} shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-semibold {{ $bannerConfig['titleColor'] }}">{{ $bannerConfig['title'] }}</h3>
                    <p class="text-sm {{ $bannerConfig['textColor'] }} mt-1">{{ $bannerConfig['message'] }}</p>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-lg bg-gray-50 dark:bg-white/5 p-4 text-sm text-gray-600 dark:text-gray-400">
            <p class="font-medium text-gray-700 dark:text-gray-300 mb-2">Password Requirements:</p>
            <ul class="space-y-1 ml-4 list-disc">
                <li>Minimum {{ config('security.password.min_length', 10) }} characters</li>
                <li>Uppercase, lowercase, number, and special character</li>
                <li>Cannot reuse last {{ config('security.password.prevent_reuse', 5) }} passwords</li>
            </ul>
        </div>

        <form wire:submit="save">
            {{ $this->form }}
            <div class="mt-6">
                <x-filament::button type="submit" size="lg" class="w-full">
                    Set New Password & Continue
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>