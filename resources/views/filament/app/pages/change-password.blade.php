<x-filament-panels::page>
    <div class="max-w-xl mx-auto">
        <div
            class="mb-6 rounded-xl bg-warning-50 dark:bg-warning-500/10 p-4 ring-1 ring-warning-200 dark:ring-warning-500/20">
            <div class="flex gap-3">
                <x-heroicon-o-exclamation-triangle
                    class="h-6 w-6 text-warning-600 dark:text-warning-400 shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-semibold text-warning-800 dark:text-warning-200">Password Change Required</h3>
                    <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                        You must change your password before you can access the platform.
                        Enter the password from your welcome email as your current password, then choose a new secure
                        password.
                    </p>
                </div>
            </div>
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