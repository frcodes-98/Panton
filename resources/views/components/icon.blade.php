@props(['name'])

<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('home')
            <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1z" />
            @break
        @case('users')
            <path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20" />
            <circle cx="9" cy="8" r="3" />
            <path d="M20 20v-1.5a3.2 3.2 0 0 0-2.4-3.1" />
            <path d="M16 5.1a3 3 0 0 1 0 5.8" />
            @break
        @case('group')
            <path d="M8 11a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z" />
            <path d="M16.5 11a2.5 2.5 0 1 0-2.5-2.5 2.5 2.5 0 0 0 2.5 2.5Z" />
            <path d="M3 19v-1.2A3.8 3.8 0 0 1 6.8 14h2.4A3.8 3.8 0 0 1 13 17.8V19" />
            <path d="M16 14h1.2A3.8 3.8 0 0 1 21 17.8V19" />
            @break
        @case('workspace')
            <rect x="3" y="4" width="18" height="14" rx="2" />
            <path d="M3 9h18M8 20h8" />
            @break
        @case('module')
            <path d="M12 3 20 7.5v9L12 21 4 16.5v-9Z" />
            <path d="M12 12 20 7.5M12 12v9M12 12 4 7.5" />
            @break
        @case('widget')
            <rect x="3" y="4" width="8" height="7" rx="1.5" />
            <rect x="13" y="4" width="8" height="4" rx="1.5" />
            <rect x="13" y="10" width="8" height="8" rx="1.5" />
            <rect x="3" y="13" width="8" height="5" rx="1.5" />
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path d="M12 3v2.2M12 18.8V21M4.9 6.5l1.6 1.6M17.5 15.9l1.6 1.6M3 12h2.2M18.8 12H21M4.9 17.5l1.6-1.6M17.5 8.1l1.6-1.6" />
            @break
        @case('marquee')
            <path d="M4 7h16M4 12h10M4 17h13" />
            @break
        @case('document')
            <path d="M7 3.5h7l4 4V20a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4.5a1 1 0 0 1 1-1Z" />
            <path d="M14 3.5V8h4.5M8.5 12.5h7M8.5 16h5" />
            @break
        @case('wrench')
            <path d="M14.5 6.5a3.5 3.5 0 0 0-4.8 4.6L4 16.8 7.2 20l5.7-5.7a3.5 3.5 0 0 0 4.6-4.8L15 12l-2.5-2.5 2-3Z" />
            @break
        @case('tasks')
            <rect x="4" y="4" width="16" height="16" rx="2" />
            <path d="M8 9h8M8 12h8M8 15h5" />
            @break
        @case('process')
            <path d="M20 12a8 8 0 1 1-2.2-5.5" />
            <path d="M20 4v5h-5" />
            @break
        @case('key')
            <circle cx="8" cy="12" r="3.5" />
            <path d="M11.2 12H20l-1.5 2H16l-1.2-2" />
            @break
        @case('bell')
            <path d="M6 16V11a6 6 0 1 1 12 0v5l1.5 2H4.5Z" />
            <path d="M10 19a2 2 0 0 0 4 0" />
            @break
        @case('book')
            <path d="M5 4.5A1.5 1.5 0 0 1 6.5 3H19v15H6.5A1.5 1.5 0 0 0 5 19.5z" />
            <path d="M5 19.5A1.5 1.5 0 0 0 6.5 21H19v-3M9 7h6" />
            @break
        @case('megaphone')
            <path d="M4 10v4a1 1 0 0 0 1 1h2l6 4V5L7 9H5a1 1 0 0 0-1 1Z" />
            <path d="M17 9a4 4 0 0 1 0 6M8 15l1 5" />
            @break
        @case('badge')
            <circle cx="12" cy="9" r="5" />
            <path d="m9 13.5-1.5 7L12 18l4.5 2.5-1.5-7" />
            @break
        @case('calendar')
            <rect x="4" y="5" width="16" height="15" rx="2" />
            <path d="M4 10h16M8 3v4M16 3v4M8 14h2M14 14h2" />
            @break
        @case('building')
            <path d="M5 21V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16M15 10h3a1 1 0 0 1 1 1v10M3 21h18" />
            <path d="M10 7v4M8 9h4M9 17h2" />
            @break
        @case('clipboard')
            <rect x="5" y="4.5" width="14" height="16.5" rx="2" />
            <path d="M9 3h6v3H9zM8.5 11h7M8.5 15h5" />
            @break
        @case('shield')
            <path d="M12 3 5 6v5c0 4.5 3 8 7 10 4-2 7-5.5 7-10V6Z" />
            <path d="m9 12 2 2 4-4" />
            @break
        @case('heart')
            <path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" />
            @break
        @case('box')
            <path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5Z" />
            <path d="m4 7.5 8 4.5 8-4.5M12 12v9" />
            @break
        @case('chevron')
            <path d="m9 6 6 6-6 6" />
            @break
        @case('check')
            <path d="m5 12 4.5 4.5L19 7" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="6" />
            <path d="m20 20-3.5-3.5" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('pencil')
            <path d="M4 20h4l10.5-10.5a2 2 0 0 0 0-2.8l-.2-.2a2 2 0 0 0-2.8 0L5 16.5Z" />
            <path d="m13 7 4 4" />
            @break
        @case('trash')
            <path d="M5 7h14M9 7V5h6v2M8 7l.8 12h6.4L16 7" />
            @break
        @case('logout')
            <path d="M10 7V5a1 1 0 0 1 1-1h8v16h-8a1 1 0 0 1-1-1v-2" />
            <path d="M4 12h10M11 9l3 3-3 3" />
            @break
        @default
            <circle cx="12" cy="12" r="8" />
    @endswitch
</svg>
