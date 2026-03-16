<x-filament-panels::page>
    <div class="space-y-6">
        <div
            class="px-4 py-5 bg-white border border-gray-200 rounded-xl shadow-sm dark:bg-white/5 dark:border-white/10 sm:px-6">
            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Active Sessions</h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500 dark:text-gray-400">
                <p>If necessary, you may log out of all of your other browser sessions across all of your devices. Some
                    of your recent sessions are listed below; however, this list may not be exhaustive.</p>
            </div>

            <div class="mt-6 space-y-4">
                @foreach ($sessions as $session)
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            @if ($session->agent['is_desktop'])
                                <x-heroicon-o-computer-desktop
                                    class="w-8 h-8 p-1.5 rounded-full text-primary-600 bg-primary-50 dark:bg-primary-500/10 dark:text-primary-400" />
                            @else
                                <x-heroicon-o-device-phone-mobile
                                    class="w-8 h-8 p-1.5 rounded-full text-primary-600 bg-primary-50 dark:bg-primary-500/10 dark:text-primary-400" />
                            @endif
                        </div>

                        <div class="flex-1">
                            <div class="text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $session->agent['platform'] ? $session->agent['platform'] : 'Unknown OS' }} -
                                    {{ $session->agent['browser'] ? $session->agent['browser'] : 'Unknown Browser' }}
                                </span>
                            </div>

                            <div class="text-xs text-gray-500">
                                {{ $session->ip_address }} •

                                @if ($session->is_current_device)
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">This device</span>
                                @else
                                    <span>Last active {{ $session->last_active }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200 dark:border-white/10">
                <div class="flex items-center">
                    <x-filament::button
                        x-on:click="$dispatch('open-modal', { id: 'confirm-logout-other-browser-sessions' })"
                        color="gray">
                        Log Out Other Browser Sessions
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    {{-- Logout Confirmation Modal --}}
    <x-filament::modal id="confirm-logout-other-browser-sessions" width="md">
        <x-slot name="heading">
            Log Out Other Browser Sessions
        </x-slot>

        <x-slot name="description">
            Please enter your password to confirm you would like to log out of your other browser sessions across all of
            your devices.
        </x-slot>

        <div class="mt-4">
            <x-filament::input.wrapper>
                <x-filament::input type="password" wire:model="password" placeholder="Password" x-ref="password"
                    x-on:keydown.enter="$wire.logoutOtherDeviceSessions($refs.password.value); $dispatch('close-modal', { id: 'confirm-logout-other-browser-sessions' })" />
            </x-filament::input.wrapper>
        </div>

        <x-slot name="footer">
            <x-filament::button color="secondary"
                x-on:click="$dispatch('close-modal', { id: 'confirm-logout-other-browser-sessions' })">
                Cancel
            </x-filament::button>

            <x-filament::button color="danger"
                x-on:click="$wire.logoutOtherDeviceSessions($refs.password.value); $dispatch('close-modal', { id: 'confirm-logout-other-browser-sessions' })">
                Log Out Other Browser Sessions
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>