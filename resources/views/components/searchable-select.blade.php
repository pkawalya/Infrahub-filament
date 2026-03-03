{{-- Searchable Select Component --}}
@props(['wireModel' => '', 'options' => [], 'placeholder' => '-- Select --', 'required' => false])
<div x-data="{
    open: false,
    search: '',
    value: @entangle($wireModel),
    options: @js($options),
    get filtered() {
        if (!this.search) return Object.entries(this.options);
        const s = this.search.toLowerCase();
        return Object.entries(this.options).filter(([k, v]) => v.toLowerCase().includes(s));
    },
    get label() { return this.options[this.value] || ''; },
    pick(k) { this.value = k; this.open = false; this.search = ''; },
    reset() { this.value = ''; this.open = false; this.search = ''; },
    toggle() { this.open = !this.open; if (this.open) this.$nextTick(() => this.$refs.q.focus()); }
}" x-on:click.outside="open = false; search = ''" style="position:relative;width:100%">

    {{-- Trigger --}}
    <div x-on:click="toggle()" class="ss-trigger"
        style="width:100%;padding:8px 32px 8px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;cursor:pointer;position:relative;box-sizing:border-box;height:38px;display:flex;align-items:center;transition:all .15s"
        :style="open && 'border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1)'">
        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:1.2"
            :style="value ? 'color:#1e293b' : 'color:#94a3b8'" x-text="label || '{{ $placeholder }}'"></span>
    </div>
    <style>
        .ss-trigger::after {
            content: '';
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid #94a3b8;
            pointer-events: none;
        }
    </style>

    {{-- Dropdown Panel --}}
    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" style="
            position:absolute; z-index:9999; margin-top:4px;
            min-width:280px; width:max-content; max-width:400px;
            left:0;
            background:#ffffff;
            border:1px solid #e2e8f0;
            border-radius:12px;
            box-shadow:0 20px 60px -12px rgba(0,0,0,.2), 0 0 0 1px rgba(0,0,0,.03);
            overflow:hidden; display:flex; flex-direction:column;
            max-height:300px;
         ">

        {{-- Search --}}
        <div style="padding:12px;background:#f8fafc;border-bottom:1px solid #e2e8f0">
            <div style="position:relative">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#94a3b8;pointer-events:none"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" x-model="search" x-ref="q" x-on:keydown.escape="open = false"
                    placeholder="Type to search..."
                    style="width:100%;padding:9px 12px 9px 34px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;background:#fff;transition:border-color .15s,box-shadow .15s"
                    onfocus="this.style.borderColor='#6366f1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,.08)'"
                    onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
            </div>
        </div>

        {{-- Options --}}
        <div style="overflow-y:auto;flex:1;padding:6px">
            @unless($required)
                <div x-show="value" x-on:click="reset()"
                    style="padding:9px 14px;font-size:14px;color:#ef4444;cursor:pointer;border-radius:8px;display:flex;align-items:center;gap:8px;margin-bottom:2px;font-weight:500"
                    onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background=''">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        style="flex-shrink:0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear selection
                </div>
            @endunless
            <template x-for="[key, lbl] in filtered" :key="key">
                <div x-on:click="pick(key)"
                    style="padding:9px 14px;font-size:14px;cursor:pointer;border-radius:8px;display:flex;align-items:center;gap:10px;transition:background .1s;margin-bottom:1px"
                    :style="key == value
                        ? 'background:#eef2ff;color:#4338ca;font-weight:600'
                        : 'color:#334155'" onmouseover="if(!this.dataset.a)this.style.background='#f1f5f9'"
                    onmouseout="this.style.background=this.dataset.a?'#eef2ff':'transparent'"
                    :data-a="key == value ? '1' : ''">
                    <div style="width:18px;height:18px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:4px"
                        :style="key == value ? 'background:#4338ca' : 'border:1.5px solid #cbd5e1'">
                        <svg x-show="key == value" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white"
                            stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                    <span x-text="lbl" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></span>
                </div>
            </template>
            <div x-show="filtered.length === 0" style="padding:24px 16px;text-align:center;color:#94a3b8">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    style="margin:0 auto 8px;color:#cbd5e1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <div style="font-size:13px;font-weight:500">No matches found</div>
            </div>
        </div>
    </div>
</div>