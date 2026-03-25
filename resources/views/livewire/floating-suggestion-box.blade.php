<div x-data="{ animatePulse: true }" x-init="setTimeout(() => animatePulse = false, 5000)"
    class="floating-suggestion-box">

    {{-- Floating Trigger Button --}}
    <button wire:click="toggle" class="suggestion-fab {{ $isOpen ? 'is-open' : 'is-closed' }}"
        :class="{ 'pulse-ring': animatePulse }" title="Anonymous Suggestion Box" aria-label="Open suggestion box">
        <div class="fab-content">
            @if(!$isOpen)
                <svg xmlns="http://www.w3.org/2000/svg" class="fab-icon" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path
                        d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z" />
                    <line x1="9" y1="21" x2="15" y2="21" />
                    <line x1="10" y1="17" x2="14" y2="17" />
                </svg>
                <span class="fab-text">Suggestion Box</span>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="fab-icon" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            @endif
        </div>
    </button>

    {{-- Chat Panel --}}
    @if($isOpen)
        <div class="suggestion-panel" x-transition:enter="panel-enter" x-transition:enter-start="panel-enter-start"
            x-transition:enter-end="panel-enter-end" x-transition:leave="panel-leave"
            x-transition:leave-start="panel-leave-start" x-transition:leave-end="panel-leave-end">

            {{-- Header --}}
            <div class="suggestion-panel-header">
                <div class="header-left">
                    <div class="header-icon">💡</div>
                    <div>
                        <h3 class="header-title">Suggestion Box</h3>
                        <p class="header-sub">100% Anonymous • Your identity is never stored</p>
                    </div>
                </div>
                <button wire:click="close" class="header-close" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="suggestion-panel-body">
                @if($submitted)
                    {{-- Success State --}}
                    <div class="success-state">
                        <div class="success-icon">✅</div>
                        <h4>Thank you!</h4>
                        <p>Your anonymous suggestion has been submitted. No one can see who sent it.</p>
                        <button wire:click="$set('submitted', false)" class="btn-another">
                            Submit Another
                        </button>
                    </div>
                @else
                    {{-- Anonymous Badge --}}
                    <div class="anon-badge">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                            <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                        Your identity is never recorded
                    </div>

                    <form wire:submit.prevent="submit" class="suggestion-form">
                        {{-- Category & Priority --}}
                        <div class="form-row">
                            <div class="form-field">
                                <label for="sb-category">Category</label>
                                <select wire:model="category" id="sb-category">
                                    @foreach(\App\Models\ProjectSuggestion::$categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="sb-priority">Priority</label>
                                <select wire:model="priority" id="sb-priority">
                                    @foreach(\App\Models\ProjectSuggestion::$priorities as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Project (optional) --}}
                        @if(count($this->projects) > 1)
                            <div class="form-field">
                                <label for="sb-project">Project <span class="optional">(optional)</span></label>
                                <select wire:model="projectId" id="sb-project">
                                    <option value="">General / Company-wide</option>
                                    @foreach($this->projects as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- Suggestion Content --}}
                        <div class="form-field">
                            <label for="sb-content">Your Suggestion</label>
                            <textarea wire:model="content" id="sb-content" rows="4"
                                placeholder="Share your idea, concern, or improvement suggestion..." maxlength="2000"
                                required></textarea>
                            @error('content')
                                <span class="field-error">{{ $message }}</span>
                            @enderror
                            <div class="char-count">{{ strlen($content) }}/2000</div>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn-submit" wire:loading.attr="disabled" wire:target="submit">
                            <span wire:loading.remove wire:target="submit"
                                style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                                Send Anonymously
                            </span>
                            <span wire:loading wire:target="submit"
                                style="display: flex; align-items: center; justify-content: center;">
                                Sending...
                            </span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <style>
        .floating-suggestion-box {
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 9999;
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* ── FAB Button ─────────────────────────────────── */
        .suggestion-fab {
            height: 56px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(245, 158, 11, 0.4), 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            color: #fff;
        }

        .suggestion-fab.is-closed {
            padding: 0 20px;
            border-radius: 28px;
        }

        .suggestion-fab.is-open {
            width: 56px;
            padding: 0;
            border-radius: 50%;
        }

        .suggestion-fab:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 28px rgba(245, 158, 11, 0.5), 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .suggestion-fab:active {
            transform: scale(0.95);
        }

        .fab-icon {
            width: 24px;
            height: 24px;
        }

        .fab-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .fab-text {
            font-size: 15px;
            font-weight: 600;
            white-space: nowrap;
        }

        .suggestion-fab.pulse-ring::after {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 32px;
            border: 2px solid rgba(245, 158, 11, 0.6);
            animation: pulse-ring 2s ease-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        /* ── Panel ──────────────────────────────────────── */
        .suggestion-panel {
            position: absolute;
            bottom: 72px;
            left: 0;
            width: 380px;
            max-height: 560px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .dark .suggestion-panel {
            background: #1e293b;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.05);
        }

        .panel-enter {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .panel-enter-start {
            opacity: 0;
            transform: translateY(16px) scale(0.95);
        }

        .panel-enter-end {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .panel-leave {
            transition: all 0.2s ease-in;
        }

        .panel-leave-start {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .panel-leave-end {
            opacity: 0;
            transform: translateY(16px) scale(0.95);
        }

        /* ── Header ─────────────────────────────────────── */
        .suggestion-panel-header {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #fff;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-icon {
            font-size: 28px;
            line-height: 1;
        }

        .header-title {
            font-size: 15px;
            font-weight: 700;
            margin: 0;
        }

        .header-sub {
            font-size: 11px;
            opacity: 0.85;
            margin: 2px 0 0;
        }

        .header-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 8px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #fff;
            transition: background 0.2s;
        }

        .header-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* ── Body ───────────────────────────────────────── */
        .suggestion-panel-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
        }

        .anon-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #6b7280;
            background: #f3f4f6;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 16px;
        }

        .dark .anon-badge {
            background: #334155;
            color: #94a3b8;
        }

        /* ── Form ───────────────────────────────────────── */
        .suggestion-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-field label {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }

        .dark .form-field label {
            color: #cbd5e1;
        }

        .form-field .optional {
            font-weight: 400;
            color: #9ca3af;
        }

        .form-field select,
        .form-field textarea {
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            font-family: inherit;
            background: #fff;
            color: #1f2937;
            outline: none;
            transition: border-color 0.2s;
            resize: vertical;
        }

        .dark .form-field select,
        .dark .form-field textarea {
            background: #0f172a;
            border-color: #475569;
            color: #e2e8f0;
        }

        .form-field select:focus,
        .form-field textarea:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }

        .char-count {
            font-size: 11px;
            color: #9ca3af;
            text-align: right;
        }

        .field-error {
            font-size: 12px;
            color: #ef4444;
        }

        /* ── Submit Button ──────────────────────────────── */
        .btn-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: wait;
        }

        /* ── Success State ──────────────────────────────── */
        .success-state {
            text-align: center;
            padding: 24px 8px;
        }

        .success-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .success-state h4 {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 8px;
        }

        .dark .success-state h4 {
            color: #f1f5f9;
        }

        .success-state p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
            margin: 0 0 20px;
        }

        .dark .success-state p {
            color: #94a3b8;
        }

        .btn-another {
            padding: 10px 24px;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }

        .dark .btn-another {
            background: #334155;
            color: #e2e8f0;
            border-color: #475569;
        }

        .btn-another:hover {
            background: #e5e7eb;
        }

        .dark .btn-another:hover {
            background: #475569;
        }

        /* ── Mobile ─────────────────────────────────────── */
        @media (max-width: 480px) {
            .suggestion-panel {
                width: calc(100vw - 32px);
                left: -8px;
                max-height: 80vh;
            }

            .floating-suggestion-box {
                bottom: 16px;
                left: 16px;
            }
        }
    </style>
</div>