<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IMS</title>
    <script>
        // Apply stored theme early to avoid flash
        (function () {
            try {
                const t = localStorage.getItem('ims-theme');
                if (t === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.documentElement.setAttribute('data-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    document.documentElement.setAttribute('data-theme', 'light');
                }
            } catch (e) { /* ignore */ }
        })();
        // Ensure Tailwind uses class-based dark mode
        try {
            window.tailwind = window.tailwind || {};
            tailwind.config = tailwind.config || {};
            tailwind.config.darkMode = 'class';
        } catch (e) { /* ignore */ }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="/css/ims.css" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            color-scheme: light
        }

        html.dark {
            color-scheme: dark
        }

        html {
            font-size: 16px
        }

        .visually-hidden-focusable:focus {
            position: static !important;
            left: auto !important;
            top: auto !important;
            width: auto !important;
            height: auto !important
        }
    </style>
</head>

<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="fixed inset-x-0 top-0 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14">
                <div class="flex items-center gap-6">
                    <a href="/" class="flex items-center gap-3">
                        <i class="fa-solid fa-hospital text-2xl text-emerald-400"></i>
                        <span class="font-semibold text-lg">IMS</span>
                    </a>
                    <nav class="hidden sm:flex gap-2" aria-label="Primary navigation">
                        @php $r = request()->route(); @endphp
                        @auth
                            @php $role = auth()->user()->role ?? null; @endphp
                        <a href="{{ route('dashboard') }}"
                            class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('dashboard') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                            aria-current="{{ request()->routeIs('dashboard') ? 'page' : '' }}">Dashboard</a>

                        {{-- Patients: doctor, receptionist, manager (admin allowed by middleware) --}}
                        @if(in_array($role, ['doctor','receptionist','manager']) || $role === 'admin')
                            <a href="{{ route('patients.index') }}"
                                class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('patients.*') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                aria-current="{{ request()->routeIs('patients.*') ? 'page' : '' }}">Patients</a>
                        @endif

                        {{-- Staff: manager and admin --}}
                        @if(in_array($role, ['manager']) || $role === 'admin')
                            <a href="{{ route('staff.index') }}"
                                class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('staff.*') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                aria-current="{{ request()->routeIs('staff.*') ? 'page' : '' }}">Staff</a>
                        @endif

                        {{-- Upload: radiologist (admin allowed) --}}
                        @if(in_array($role, ['radiologist']) || $role === 'admin')
                            <a href="{{ route('images.index') }}"
                                class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('images.*') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                aria-current="{{ request()->routeIs('images.*') ? 'page' : '' }}">Upload</a>
                        @endif

                        {{-- Diagnosis: doctor and radiologist (admin allowed) --}}
                        @if(in_array($role, ['doctor','radiologist']) || $role === 'admin')
                            <a href="{{ route('diagnoses.index') }}"
                                class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('diagnoses.*') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                aria-current="{{ request()->routeIs('diagnoses.*') ? 'page' : '' }}">Diagnosis</a>
                        @endif

                        {{-- Reports: accountant, manager (admin allowed) --}}
                        @if(in_array($role, ['accountant','manager']) || $role === 'admin')
                            <a href="{{ route('reports.index') }}"
                                class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('reports.*') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                aria-current="{{ request()->routeIs('reports.*') ? 'page' : '' }}">Reports</a>
                        @endif

                        {{-- Billing: accountant, manager (admin allowed) --}}
                        @if(in_array($role, ['accountant','manager']) || $role === 'admin')
                            <a href="{{ route('billing.index') }}"
                                class="px-3 py-1 rounded focus:outline-none focus:ring-2 {{ request()->routeIs('billing.*') ? 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-white' : 'hover:bg-slate-100 dark:hover:bg-slate-800' }}"
                                aria-current="{{ request()->routeIs('billing.*') ? 'page' : '' }}">Billing</a>
                        @endif
                        @endauth
                    </nav>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2">
                        <button id="font-decrease" title="Decrease font size"
                            class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800"><i
                                class="fa-solid fa-minus"></i></button>
                        <button id="font-reset" title="Reset font size"
                            class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800"><i
                                class="fa-solid fa-text-height"></i></button>
                        <button id="font-increase" title="Increase font size"
                            class="p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800"><i
                                class="fa-solid fa-plus"></i></button>
                    </div>
                    <button id="read-page" title="Read page aloud"
                        class="p-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white"><i
                            class="fa-solid fa-play"></i></button>
                    <button id="theme-toggle" aria-pressed="false" title="Toggle theme"
                        class="p-2 rounded bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-white"><i
                            class="fa-solid fa-moon"></i></button>
                    @auth
                        <div class="hidden sm:flex items-center gap-2 pl-3 border-l border-slate-700">
                            <span class="text-sm user-name">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="ml-2 text-sm text-rose-400">LOGOUT</button>
                            </form>
                        </div>
                    @else
                        <a class="ml-3 text-sm" href="{{ route('login') }}">LOGIN</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>


    <main id="main" class="pt-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @yield('content')

        <footer class="fixed bottom-0 inset-x-0 z-40 text-center bg-white dark:bg-slate-900 text-sm text-slate-500 py-3 shadow fade-in" aria-label="Site footer">
            <div>IMS — Developed by <a href="#" id="contrast-toggle">Mohamed Nizam Mohamed Mizhar | Kingston ID:
                    K2525754 | ESoft Student ID: E256330</a></div>
        </footer>

    </main>

    <!-- Toast container -->
    <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>

    <script>
        // UI helpers: theme toggle, font size controls, read page (TTS), high contrast
        (function () {
            const root = document.documentElement;
            const themeBtn = document.getElementById('theme-toggle');
            const readBtn = document.getElementById('read-page');
            const contrast = document.getElementById('contrast-toggle');
            const fontInc = document.getElementById('font-increase');
            const fontDec = document.getElementById('font-decrease');
            const fontReset = document.getElementById('font-reset');

            // Theme: keep both data-theme and class for compatibility with CSS and Tailwind
            const storedTheme = localStorage.getItem('ims-theme');
            function applyTheme(t) {
                if (t === 'dark') {
                    root.classList.add('dark');
                    root.setAttribute('data-theme', 'dark');
                } else {
                    root.classList.remove('dark');
                    root.setAttribute('data-theme', 'light');
                }
                if (themeBtn) themeBtn.setAttribute('aria-pressed', t === 'dark');
            }

            let cur = storedTheme || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            applyTheme(cur);
            if (themeBtn) themeBtn.addEventListener('click', () => {
                cur = cur === 'dark' ? 'light' : 'dark';
                localStorage.setItem('ims-theme', cur);
                applyTheme(cur);
            });

            // Font size
            const base = 16;
            let scale = Number(localStorage.getItem('ims-font-scale') || 1);
            function applyScale() {
                document.documentElement.style.fontSize = (base * scale) + 'px';
                localStorage.setItem('ims-font-scale', scale);
            }
            applyScale();
            if (fontInc) fontInc.addEventListener('click', () => { scale = Math.min(2, +(scale + 0.1).toFixed(2)); applyScale(); });
            if (fontDec) fontDec.addEventListener('click', () => { scale = Math.max(0.8, +(scale - 0.1).toFixed(2)); applyScale(); });
            if (fontReset) fontReset.addEventListener('click', () => { scale = 1; applyScale(); });

            // High contrast
            if (contrast) contrast.addEventListener('click', function (e) { e.preventDefault(); document.body.classList.toggle('high-contrast'); });

            // Read page (TTS) - more resilient: read selection or #main content
            if (readBtn) {
                if ('speechSynthesis' in window) {
                    readBtn.addEventListener('click', function () {
                        const sel = window.getSelection().toString().trim();
                        const main = document.getElementById('main');
                        const text = sel || (main ? main.innerText : document.body.innerText);
                        if (!text || text.trim().length === 0) return;
                        const utter = new SpeechSynthesisUtterance(text.replace(/\s+/g, ' '));
                        utter.rate = 1;
                        // Choose a voice if available
                        const voices = speechSynthesis.getVoices();
                        if (voices && voices.length) utter.voice = voices[0];
                        speechSynthesis.cancel();
                        speechSynthesis.speak(utter);
                    });
                } else {
                    // Hide TTS button if unsupported
                    readBtn.style.display = 'none';
                }
            }
        })();
    </script>

    <script>
        // Toast helper: create and show toast messages (auto-dismiss)
        (function () {
            const container = document.getElementById('toast-container');
            if (!container) return;

            function createToast(message, type = 'success', timeout = 5000) {
                const toast = document.createElement('div');
                toast.className = `toast toast-${type} fade-in`;
                toast.setAttribute('role', 'status');
                toast.setAttribute('aria-live', 'polite');
                toast.innerHTML = `
                    <div class="toast-content">${message}</div>
                    <button class="toast-close" aria-label="Dismiss">&times;</button>
                `;
                container.appendChild(toast);

                const remove = () => { toast.classList.add('toast-hide'); setTimeout(() => toast.remove(), 220); };
                toast.querySelector('.toast-close').addEventListener('click', remove);
                if (timeout > 0) setTimeout(remove, timeout);
            }

            // Push server-side flash messages into toasts
            try {
                @if(session('success'))
                    createToast({!! json_encode(session('success')) !!}, 'success');
                @endif
                @if(session('error'))
                    createToast({!! json_encode(session('error')) !!}, 'error');
                @endif
                @if(session('warning'))
                    createToast({!! json_encode(session('warning')) !!}, 'warning');
                @endif
                @if(session('info'))
                    createToast({!! json_encode(session('info')) !!}, 'info');
                @endif
            } catch (e) { /* ignore */ }

            // Provide global helper if needed
            window.IMS = window.IMS || {};
            window.IMS.showToast = createToast;
        })();
    </script>
    @stack('scripts')
</body>

</html>