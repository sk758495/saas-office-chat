<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-transition { transition: all 0.3s ease; }
        .gradient-bg { background: linear-gradient(135deg, #ff6b35 0%, #f7931e 50%, #ff4757 100%); }
        .orange-gradient { background: linear-gradient(135deg, #ff9a56 0%, #ff6b35 100%); }
        .red-gradient { background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%); }
    </style>
</head>
<body class="bg-orange-50">
    <!-- Top Navigation -->
    <nav class="gradient-bg shadow-lg relative z-30">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <button onclick="toggleSidebar()" class="text-white mr-4 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    @if(auth('admin')->user()->company && auth('admin')->user()->company->logo)
                        <img src="{{ asset('storage/' . auth('admin')->user()->company->logo) }}" 
                             class="h-8 w-8 mr-3 rounded-lg">
                    @endif
                    <span class="text-xl font-bold text-white">
                        {{ auth('admin')->user()->company ? auth('admin')->user()->company->name : 'Admin Panel' }}
                    </span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button class="flex items-center text-white hover:text-orange-200 transition-colors" 
                                onclick="toggleDropdown()">
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <span class="mr-2 font-medium">{{ auth('admin')->user()->name }}</span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div id="dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-2 z-50">
                            <a href="{{ route('company.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600">
                                <i class="fas fa-cog mr-2"></i>Settings
                            </a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-transition w-64 bg-white shadow-lg h-screen fixed lg:relative lg:translate-x-0 -translate-x-full z-20">
            <div class="p-6">
                <nav class="space-y-2">
                    <a href="{{ route('company.dashboard') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-colors {{ request()->routeIs('company.dashboard') ? 'bg-orange-50 text-orange-600 border-r-4 border-orange-500' : '' }}">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-orange-50 text-orange-600 border-r-4 border-orange-500' : '' }}">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-colors {{ request()->routeIs('admin.departments.*') ? 'bg-orange-50 text-orange-600 border-r-4 border-orange-500' : '' }}">
                        <i class="fas fa-building mr-3"></i>
                        Departments
                    </a>
                    <a href="{{ route('admin.designations.index') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-colors {{ request()->routeIs('admin.designations.*') ? 'bg-orange-50 text-orange-600 border-r-4 border-orange-500' : '' }}">
                        <i class="fas fa-briefcase mr-3"></i>
                        Designations
                    </a>
                    <a href="{{ route('admin.chat-monitor') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-colors {{ request()->routeIs('admin.chat-monitor*') ? 'bg-orange-50 text-orange-600 border-r-4 border-orange-500' : '' }}">
                        <i class="fas fa-comments mr-3"></i>
                        Chat Monitor
                    </a>
                    <a href="{{ route('company.settings') }}" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-orange-50 hover:text-orange-600 transition-colors {{ request()->routeIs('company.settings') ? 'bg-orange-50 text-orange-600 border-r-4 border-orange-500' : '' }}">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <div class="flex-1 lg:ml-0">
            <!-- Breadcrumb -->
            <div class="bg-white border-b px-6 py-4">
                <nav class="flex items-center space-x-2 text-sm">
                    <a href="{{ route('company.dashboard') }}" class="text-orange-600 hover:text-orange-800">
                        <i class="fas fa-home mr-1"></i>Dashboard
                    </a>
                    @if(!request()->routeIs('company.dashboard'))
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="text-gray-600">{{ ucfirst(str_replace('.', ' > ', request()->route()->getName())) }}</span>
                    @endif
                </nav>
            </div>

            <main>
                @if(session('success'))
                    <div class="max-w-7xl mx-auto px-6 py-4">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="max-w-7xl mx-auto px-6 py-4">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="max-w-7xl mx-auto px-6 py-4">
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('warning') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById('dropdown').classList.toggle('hidden');
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                document.getElementById('dropdown').classList.add('hidden');
            }
        });
    </script>
</body>
</html>