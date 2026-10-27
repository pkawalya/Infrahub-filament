@php
    $ext = strtolower(trim($type ?? ''));
@endphp

@if(in_array($ext, ['doc', 'docx', 'word']))
    {{-- MS Word icon --}}
    <span class="asite-ft-icon asite-ft-word" title="Word Document (.{{ $ext }})">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="3" width="20" height="18" rx="3" fill="#2b579a"/>
            <path d="M6 7.5L8.2 16.5H9.8L11.5 10.5L13.2 16.5H14.8L17 7.5H15.2L13.8 13.8L12.2 7.5H10.8L9.2 13.8L7.8 7.5H6Z" fill="#ffffff" font-weight="bold"/>
        </svg>
    </span>
@elseif(in_array($ext, ['xls', 'xlsx', 'csv', 'excel']))
    {{-- MS Excel icon --}}
    <span class="asite-ft-icon asite-ft-excel" title="Excel Spreadsheet (.{{ $ext }})">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="3" width="20" height="18" rx="3" fill="#217346"/>
            <path d="M7 7.5L10.5 12L7 16.5H9L11.5 13.2L14 16.5H16L12.5 12L16 7.5H14L11.5 10.8L9 7.5H7Z" fill="#ffffff" font-weight="bold"/>
        </svg>
    </span>
@elseif(in_array($ext, ['pdf']))
    {{-- Adobe PDF icon --}}
    <span class="asite-ft-icon asite-ft-pdf" title="Adobe PDF (.pdf)">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="3" width="20" height="18" rx="3" fill="#d9381e"/>
            <text x="12" y="15.5" font-family="Arial, sans-serif" font-size="8.5" font-weight="900" fill="#ffffff" text-anchor="middle" letter-spacing="-0.5">PDF</text>
        </svg>
    </span>
@elseif(in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz', 'bz2']))
    {{-- Archive / Zip icon (colorful stack like ASITE) --}}
    <span class="asite-ft-icon asite-ft-zip" title="Archive Package (.{{ $ext }})">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="3" y="4" width="18" height="4" rx="1" fill="#7c3aed"/>
            <rect x="3" y="9" width="18" height="4" rx="1" fill="#0284c7"/>
            <rect x="3" y="14" width="18" height="4" rx="1" fill="#16a34a"/>
            <rect x="10" y="3" width="4" height="17" fill="#f59e0b" rx="1"/>
            <circle cx="12" cy="11" r="1.5" fill="#1e293b"/>
        </svg>
    </span>
@elseif(in_array($ext, ['dwg', 'dxf', 'cad', 'rvt', 'ifc']))
    {{-- CAD / Drawing icon --}}
    <span class="asite-ft-icon asite-ft-cad" title="CAD Drawing (.{{ $ext }})">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="3" width="20" height="18" rx="3" fill="#0891b2"/>
            <path d="M7 16L12 6L17 16M9 13H15" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
@elseif(in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp']))
    {{-- Image icon --}}
    <span class="asite-ft-icon asite-ft-img" title="Image (.{{ $ext }})">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="3" width="20" height="18" rx="3" fill="#9333ea"/>
            <circle cx="8" cy="8" r="2" fill="#ffffff"/>
            <path d="M4 17L9 12L13 16L16 13L20 17H4Z" fill="#ffffff"/>
        </svg>
    </span>
@else
    {{-- Generic document icon --}}
    <span class="asite-ft-icon asite-ft-generic" title="Document (.{{ $ext }})">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 2H14L20 8V20C20 21.1 19.1 22 18 22H6C4.9 22 4 21.1 4 20V4C4 2.9 4.9 2 6 2Z" fill="#64748b"/>
            <path d="M14 2V8H20L14 2Z" fill="#94a3b8"/>
            <line x1="8" y1="13" x2="16" y2="13" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
            <line x1="8" y1="16" x2="14" y2="16" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </span>
@endif
