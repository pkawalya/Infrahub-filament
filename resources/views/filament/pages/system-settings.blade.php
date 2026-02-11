<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Save Preferences
            </x-filament::button>
        </div>
    </form>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('settings-saved', () => {
                setTimeout(() => window.location.reload(), 500);
            });
        });
    </script>
</x-filament-panels::page>