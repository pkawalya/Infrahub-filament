<x-filament-panels::page>
    <form wire:submit.prevent="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Save User Manual
            </x-filament::button>
        </div>
    </form>

    {{-- Render list of uploaded image URLs so the user can easily copy and paste them --}}
    @php
        $images = $this->data['images'] ?? [];
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
    @endphp
    @if(is_array($images) && count($images) > 0)
        <div class="mt-8 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.9 2.9m-18 8.25h11.25a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0a2.25 2.25 0 00-2.25 2.25v14.25a2.25 2.25 0 002.25 2.25h9.75" />
                </svg>
                Uploaded Manual Images (URLs for Embedding)
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                Click **Copy Markdown** next to any image to copy its markdown reference code, then paste it directly into the Markdown Editor above.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($images as $img)
                    @php
                        $url = asset('storage/' . $img);
                        $markdownCode = '![' . basename($img) . '](' . $url . ')';
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 dark:border-white/5 bg-white dark:bg-gray-900">
                        <div class="flex items-center gap-3">
                            <img src="{{ $url }}" class="w-12 h-12 rounded object-cover border border-gray-300 dark:border-white/10" alt="" />
                            <div class="min-w-0">
                                <p class="text-xs font-semibold truncate text-gray-700 dark:text-gray-300">{{ basename($img) }}</p>
                                <code class="text-[10px] text-gray-500 dark:text-gray-400 select-all">{{ $url }}</code>
                            </div>
                        </div>
                        <button type="button" 
                                onclick="navigator.clipboard.writeText('{{ addslashes($markdownCode) }}'); this.innerText = 'Copied!'; setTimeout(() => this.innerText = 'Copy Markdown', 2000)"
                                class="px-2.5 py-1.5 rounded bg-amber-600 hover:bg-amber-500 text-white text-xs transition">
                            Copy Markdown
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-panels::page>
