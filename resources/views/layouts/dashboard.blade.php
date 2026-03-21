<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard | Orens Pro' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-item-active {
            background: rgba(255, 107, 0, 0.1);
            color: #FF6B00;
            border-right: 4px solid #FF6B00;
        }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#F8F9FA] text-[#1A1A1A] font-sans antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-white shadow-2xl transition-transform -translate-x-full lg:translate-x-0">
            <div class="flex flex-col h-full bg-white">
                <div class="p-8 flex items-center gap-3">
                    <div class="w-10 h-10 bg-orens rounded-xl flex items-center justify-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <span class="text-xl font-bold text-gray-800 tracking-tight">Orens <span class="text-orens">Pro</span></span>
                </div>

                <nav class="flex-1 px-4 space-y-2 py-4">
                    <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Main Menu</p>
                    
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('dashboard') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('organisations.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('admin/organisations*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            <span class="font-medium">Organisations</span>
                        </a>
                        <a href="{{ route('divisions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('admin/divisions*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span class="font-medium">Divisions</span>
                        </a>
                    @endif

                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'leader')
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('admin/users*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="font-medium">{{ auth()->user()->role === 'admin' ? 'Users' : 'My Members' }}</span>
                        </a>
                        <a href="{{ route('sessions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('sessions*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-medium">Sessions</span>
                        </a>
                    @endif

                    @if(auth()->user()->role === 'member')
                        <a href="{{ route('attendance.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('attendance*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            <span class="font-medium">My History</span>
                        </a>
                    @endif
                    <div class="pt-4 border-t border-gray-50 mt-4">
                        <p class="px-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-2">Settings</p>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ Request::is('profile*') ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <span class="font-medium">My Profile</span>
                        </a>
                    </div>
                </nav>

                <div class="p-6">
                    <div class="bg-orens/5 rounded-2xl p-4">
                        <p class="text-[10px] font-bold text-orens uppercase tracking-widest mb-1">Your Division</p>
                        <p class="text-xs font-semibold text-gray-700">{{ auth()->user()->division->name ?? 'Global' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-72 bg-gray-50 min-h-screen">
            <!-- Topbar -->
            <header class="bg-white border-b border-gray-100 sticky top-0 z-30 px-8 h-20 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800 hidden sm:block">Overall Engagement</h2>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-900 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-xs font-medium text-orens capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-8 right-8 z-[100] flex flex-col gap-4"></div>

    <template id="toast-template">
        <div class="toast-item transform translate-y-4 opacity-0 transition-all duration-500 bg-white rounded-2xl shadow-2xl border border-gray-100 p-5 flex items-center gap-4 min-w-[320px] max-w-md">
            <div class="toast-icon w-10 h-10 rounded-xl flex items-center justify-center shrink-0"></div>
            <div class="flex-1">
                <p class="toast-title text-sm font-bold text-gray-800"></p>
                <p class="toast-message text-xs text-gray-500 font-medium mt-0.5"></p>
            </div>
            <button class="toast-close text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>

    <style>
        .toast-item.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        
        if(toggle) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }

        function showToast(type, title, message) {
            const container = document.getElementById('toast-container');
            const template = document.getElementById('toast-template');
            const toast = template.content.cloneNode(true).querySelector('.toast-item');
            
            const iconContainer = toast.querySelector('.toast-icon');
            const titleEl = toast.querySelector('.toast-title');
            const messageEl = toast.querySelector('.toast-message');
            const closeBtn = toast.querySelector('.toast-close');

            titleEl.textContent = title;
            messageEl.textContent = message;

            if (type === 'success') {
                iconContainer.classList.add('bg-green-50', 'text-green-500');
                iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>';
            } else {
                iconContainer.classList.add('bg-red-50', 'text-red-500');
                iconContainer.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>';
            }

            container.appendChild(toast);
            
            // Trigger animation
            requestAnimationFrame(() => toast.classList.add('show'));

            const remove = () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 500);
            };

            closeBtn.onclick = remove;
            setTimeout(remove, 5000);
        }

        const successMsg = @json(session('success'));
        const errorMsg = @json(session('error'));

        if (successMsg) {
            showToast('success', 'Success!', successMsg);
        }

        if (errorMsg) {
            showToast('error', 'Error!', errorMsg);
        }
    </script>
</body>
</html>
