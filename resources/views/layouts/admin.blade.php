<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Office Chat</title>
    <link rel="icon" type="image/png" href="/user/images/Office-Chat-fevicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; }
        .sidebar { 
            width: 260px; 
            height: 100vh; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            position: fixed; 
            top: 0; 
            left: 0; 
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .logo { 
            padding: 20px; 
            text-align: center; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .logo h4 { 
            color: white; 
            margin: 0; 
            font-weight: 600;
        }
        .sidebar-nav { 
            padding: 20px 0; 
        }
        .sidebar-nav a { 
            display: flex; 
            align-items: center; 
            padding: 12px 20px; 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            transition: all 0.3s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active { 
            background: rgba(255,255,255,0.1); 
            color: white; 
            padding-left: 30px;
        }
        .sidebar-nav a i { 
            width: 20px; 
            margin-right: 12px; 
        }
        .main-content { 
            margin-left: 260px; 
            min-height: 100vh;
        }
        .top-navbar { 
            background: white; 
            padding: 15px 30px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e9ecef;
        }
        .content-area { 
            padding: 30px; 
        }
        .card { 
            border: none; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.08); 
            border-radius: 10px;
        }
        .card-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 10px 10px 0 0 !important;
            border: none;
        }
        .btn-primary { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            border: none;
        }
        .btn-primary:hover { 
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%); 
        }
        .stats-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px;
        }
        .stats-card .display-6 { 
            font-weight: 700; 
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="/user/images/office-chat-logo.png" alt="Office Chat" style="height: 40px; width: auto; margin-bottom: 8px;">
            <h6 class="text-white-50 mb-0">Admin Panel</h6>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.departments.index') }}" class="{{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Departments
            </a>
            <a href="{{ route('admin.designations.index') }}" class="{{ request()->routeIs('admin.designations.*') ? 'active' : '' }}">
                <i class="fas fa-user-tag"></i> Designations
            </a>
            <a href="{{ route('admin.chat-monitor') }}" class="{{ request()->routeIs('admin.chat-monitor*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Chat Monitor
            </a>
            <a href="#" class="">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="#" class="">
                <i class="fas fa-chart-bar"></i> Analytics
            </a>
            <a href="#" class="">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">@yield('page-title', 'Dashboard')</h5>
                <small class="text-muted">@yield('page-subtitle', 'Welcome to admin panel')</small>
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>{{ auth('admin')->user()->name ?? 'Admin' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>