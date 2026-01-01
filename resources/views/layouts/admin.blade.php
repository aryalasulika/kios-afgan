<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Admin Panel - Kios Afgan</title>
</head>

<body class="bg-gray-100 font-sans text-gray-900 leading-normal tracking-normal">

    <!-- Mobile Sidebar Backdrop -->
    <!-- Z-Index: 40. Below Sidebar (50). Above Header/Content. -->
    <div id="sidebarBackdrop"
        class="fixed inset-0 bg-black opacity-50 z-40 hidden cursor-pointer transition-opacity duration-300 md:hidden"
        onclick="window.toggleSidebar()">
    </div>

    <!-- Sidebar -->
    <!-- Z-Index: 50. Topmost to slide over content on mobile. -->
    <aside id="sidebar"
        class="bg-gray-800 text-white w-64 min-h-screen flex flex-col fixed top-0 left-0 z-50 transform -translate-x-full transition-transform duration-300 md:translate-x-0 shadow-xl">

        <div class="p-6 text-2xl font-bold border-b border-gray-700 flex justify-between items-center h-20">
            <span>Kios Afgan Admin</span>
            <!-- Close Button (Mobile Only) -->
            <button onclick="window.toggleSidebar()"
                class="md:hidden text-gray-400 hover:text-white focus:outline-none p-2 rounded hover:bg-gray-700 transition"
                aria-label="Close Sidebar">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 p-4 overflow-y-auto">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.index') }}"
                        class="flex items-center py-3 px-4 rounded hover:bg-gray-700 transition duration-200 {{ request()->routeIs('admin.index') ? 'bg-gray-700' : '' }}">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}"
                        class="flex items-center py-3 px-4 rounded hover:bg-gray-700 transition duration-200 {{ request()->routeIs('products.*') ? 'bg-gray-700' : '' }}">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Produk & Stok
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}"
                        class="flex items-center py-3 px-4 rounded hover:bg-gray-700 transition duration-200 {{ request()->routeIs('reports.*') ? 'bg-gray-700' : '' }}">
                        <svg class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan Penjualan
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-gray-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition duration-200">
                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <!-- Added md:ml-64 to push content on desktop. Removed transition-all to prevent jank on resize. -->
    <div class="flex-1 flex flex-col min-h-screen md:ml-64">

        <!-- Mobile Header -->
        <header class="bg-white shadow h-20 flex items-center justify-between px-4 md:hidden z-10 sticky top-0">
            <h1 class="text-xl font-bold text-gray-800">Kios Afgan</h1>
            <!-- Hamburger Button -->
            <button onclick="window.toggleSidebar()"
                class="text-gray-600 hover:text-gray-900 focus:outline-none p-2 rounded hover:bg-gray-100 transition"
                aria-label="Open Sidebar">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white p-4 text-center text-gray-500 text-sm border-t">
            &copy; {{ date('Y') }} Kios Afgan System
        </footer>
    </div>

    <!-- Script -->
    <script>
        window.toggleSidebar = function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const body = document.body;

            if (!sidebar || !backdrop) return;

            const isHidden = sidebar.classList.contains('-translate-x-full');

            if (isHidden) {
                // OPEN
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                body.classList.add('overflow-hidden');
            } else {
                // CLOSE
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                body.classList.remove('overflow-hidden');
            }
        };

        // Handle Resize to reset state
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebarBackdrop');
                const body = document.body;

                // Ensure backdrop is hidden and body scroll is restored on Desktop
                if (backdrop && !backdrop.classList.contains('hidden')) {
                    backdrop.classList.add('hidden');
                    body.classList.remove('overflow-hidden');
                    // Note: We don't add -translate-x-full back to sidebar because on desktop it should be visible (handled by md:translate-x-0). 
                    // But if we switch back to mobile, we want it hidden? 
                    // Actually, if we just resized to desktop, we should assume the user wants to see the desktop view.
                    // Generally, it's safer to re-add -translate-x-full so if they go back to mobile it's closed.
                    sidebar.classList.add('-translate-x-full');
                }
            }
        });
    </script>
</body>

</html>