<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6 flex items-center justify-between">
            {{-- Test Email Button --}}
            <x-filament::button type="button" wire:click="sendTestEmail" wire:loading.attr="disabled" color="gray"
                icon="heroicon-o-paper-airplane">
                <span wire:loading.remove wire:target="sendTestEmail">Send Test Email</span>
                <span wire:loading wire:target="sendTestEmail">Sending...</span>
            </x-filament::button>

            {{-- Save Button --}}
            <x-filament::button type="submit" icon="heroicon-o-check">
                Save Email Settings
            </x-filament::button>
        </div>
    </form>

    {{-- Quick Setup Guide --}}
    <div class="mt-8 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] p-5">
        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
            <x-heroicon-o-light-bulb class="w-4 h-4 text-amber-500" />
            Quick Setup Guide
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs text-gray-500 dark:text-gray-400">
            <div class="space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">📧 Gmail</p>
                <p>Host: <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">smtp.gmail.com</code></p>
                <p>Port: <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">587</code></p>
                <p>Encryption: <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">TLS</code></p>
                <p class="text-[10px] text-gray-400">Use App Password from Google Account</p>
            </div>
            <div class="space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">📬 Outlook / Office 365</p>
                <p>Host: <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">smtp.office365.com</code></p>
                <p>Port: <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">587</code></p>
                <p>Encryption: <code class="bg-gray-100 dark:bg-white/5 px-1 rounded">TLS</code></p>
            </div>
            <div class="space-y-1">
                <p class="font-semibold text-gray-700 dark:text-gray-300">🔧 Custom SMTP</p>
                <p>Get credentials from your email provider</p>
                <p>Common ports: 465 (SSL), 587 (TLS)</p>
                <p class="text-[10px] text-gray-400">Click "Send Test Email" to verify</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>