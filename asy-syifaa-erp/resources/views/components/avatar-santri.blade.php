@props(['gender' => 'L', 'fotoUrl' => null, 'canViewPhoto' => true, 'id' => 'default', 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'sm' => 'w-20 h-24',
        'lg' => 'w-44 h-52',
        default => 'w-36 h-44',
    };
    $svgClasses = match($size) {
        'sm' => 'w-16 h-20',
        'lg' => 'w-36 h-44',
        default => 'w-28 h-36',
    };
@endphp

<div class="{{ $sizeClasses }} rounded-xl bg-gray-100 dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 overflow-hidden flex items-center justify-center">
    @if($fotoUrl && $canViewPhoto)
        <img src="{{ asset('storage/' . $fotoUrl) }}" alt="Foto" class="w-full h-full object-cover">
    @elseif($gender === 'P')
        <svg viewBox="0 0 120 150" class="{{ $svgClasses }}">
            <defs>
                <linearGradient id="hijab-{{ $id }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#047857"/>
                    <stop offset="100%" style="stop-color:#065f46"/>
                </linearGradient>
            </defs>
            <ellipse cx="60" cy="65" rx="50" ry="60" fill="url(#hijab-{{ $id }})"/>
            <ellipse cx="60" cy="55" rx="30" ry="25" fill="#fde68a" opacity="0.15"/>
            <ellipse cx="48" cy="52" rx="4" ry="3" fill="#1f2937"/>
            <ellipse cx="72" cy="52" rx="4" ry="3" fill="#1f2937"/>
            <rect x="30" y="62" width="60" height="35" rx="5" fill="url(#hijab-{{ $id }})" opacity="0.9"/>
            <path d="M25 70 Q60 85 95 70" stroke="#065f46" stroke-width="2" fill="none"/>
            <rect x="20" y="120" width="80" height="30" rx="5" fill="url(#hijab-{{ $id }})"/>
            <text x="60" y="142" text-anchor="middle" fill="white" font-size="8" font-weight="bold">PHOTO ID</text>
        </svg>
    @else
        <svg viewBox="0 0 120 150" class="{{ $svgClasses }}">
            <circle cx="60" cy="50" r="30" fill="#d1d5db"/>
            <circle cx="60" cy="45" r="22" fill="#fbbf24" opacity="0.15"/>
            <circle cx="60" cy="45" r="20" fill="#9ca3af"/>
            <ellipse cx="50" cy="43" rx="3" ry="2.5" fill="#1f2937"/>
            <ellipse cx="70" cy="43" rx="3" ry="2.5" fill="#1f2937"/>
            <path d="M52 55 Q60 60 68 55" stroke="#6b7280" stroke-width="2" fill="none"/>
            <path d="M35 30 Q45 15 60 18 Q75 15 85 30" stroke="#6b7280" stroke-width="3" fill="#4b5563"/>
            <rect x="25" y="85" width="70" height="50" rx="10" fill="#e5e7eb"/>
            <rect x="40" y="95" width="40" height="10" rx="3" fill="#d1d5db"/>
            <rect x="20" y="120" width="80" height="30" rx="5" fill="#6b7280"/>
            <text x="60" y="142" text-anchor="middle" fill="white" font-size="8" font-weight="bold">PHOTO ID</text>
        </svg>
    @endif
</div>
