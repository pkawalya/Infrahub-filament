{{-- Global Fullscreen Toggle — injected on every page via render hook --}}
<div x-data="{ fs: false }"
    x-on:keydown.escape.window="if(fs) { fs = false; document.documentElement.classList.remove('app-fullscreen'); document.body.style.overflow = ''; }"
    style="position: fixed; bottom: 16px; right: 16px; z-index: 90;">
    <button type="button" x-on:click="
            fs = !fs;
            if (fs) {
                document.documentElement.classList.add('app-fullscreen');
                document.body.style.overflow = 'hidden';
            } else {
                document.documentElement.classList.remove('app-fullscreen');
                document.body.style.overflow = '';
            }
        " :class="fs ? 'fs-btn fs-btn-active' : 'fs-btn'" :title="fs ? 'Exit Fullscreen (Esc)' : 'Fullscreen'">
        <template x-if="!fs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15" />
            </svg>
        </template>
        <template x-if="fs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 9V4.5M9 9H4.5M9 9 3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5 5.25 5.25" />
            </svg>
        </template>
    </button>
</div>

<style>
    /* Floating button */
    .fs-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, .1);
        background: white;
        color: #64748b;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .1), 0 0 0 1px rgba(0, 0, 0, .04);
        cursor: pointer;
        transition: all .2s;
    }

    .fs-btn:hover {
        background: #eef2ff;
        color: #4f46e5;
        border-color: #c7d2fe;
        transform: scale(1.05);
    }

    .fs-btn-active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    .fs-btn-active:hover {
        background: #4338ca;
    }

    .dark .fs-btn {
        background: #1e293b;
        color: #94a3b8;
        border-color: rgba(255, 255, 255, .1);
    }

    .dark .fs-btn:hover {
        background: #312e81;
        color: #a5b4fc;
    }

    .dark .fs-btn-active {
        background: #6366f1;
        color: white;
    }

    /* Fullscreen mode: hide topbar, sidebars, sub-nav; span full width */
    html.app-fullscreen .fi-topbar,
    html.app-fullscreen .fi-sidebar,
    html.app-fullscreen .fi-topbar+div>nav,
    html.app-fullscreen nav[aria-label="Sub-navigation"],
    html.app-fullscreen [class*="fi-resource-sub-navigation"],
    html.app-fullscreen .fi-breadcrumbs {
        display: none !important;
    }

    html.app-fullscreen .fi-layout {
        padding-top: 0 !important;
    }

    html.app-fullscreen .fi-main-ctn,
    html.app-fullscreen .fi-section-content-ctn,
    html.app-fullscreen main {
        max-width: 100vw !important;
        width: 100% !important;
        margin: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    html.app-fullscreen .fi-page {
        padding: 16px 16px 12px !important;
    }

    /* Remove the sub-nav sidebar grid so content takes full row */
    html.app-fullscreen .fi-resource-pages-page,
    html.app-fullscreen [class*="fi-resource"]>.grid {
        display: block !important;
    }

    html.app-fullscreen [class*="fi-resource"]>.grid> :first-child:not(.fi-page) {
        display: none !important;
    }

    html.app-fullscreen [class*="fi-resource"]>.grid>.fi-page,
    html.app-fullscreen [class*="fi-resource"]>.grid> :last-child {
        grid-column: 1 / -1 !important;
    }
</style>