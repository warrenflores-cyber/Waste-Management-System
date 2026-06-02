<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Smart Waste Management System</title>

    <script>
        if (!localStorage.getItem('ecosync_session')) {
            window.location.replace('login.php');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 4px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.6);
        }

        /* Smooth animation for the background blobs */
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* --- BULLETPROOF COLLAPSED SIDEBAR FIXES --- */
        aside#sidebar.collapsed {
            width: 5.5rem !important;
        }

        aside#sidebar.collapsed .nav-label {
            display: none !important;
        }

        aside#sidebar.collapsed .logo-container {
            padding: 0 !important;
            justify-content: center !important;
        }

        /* Forces the pill-shapes into perfect squares */
        aside#sidebar.collapsed .nav-item,
        aside#sidebar.collapsed .profile-box {
            padding: 0 !important;
            width: 3.5rem !important;
            height: 3.5rem !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            margin: 0 auto !important;
        }

        /* Snaps the orange indicator to the far left edge */
        aside#sidebar.collapsed .nav-item .indicator {
            left: 0 !important;
            width: 4px !important;
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
        }

        aside#sidebar.collapsed #toggleIcon {
            transform: rotate(180deg) !important;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-emerald-100 via-indigo-50 to-amber-100 dark:from-emerald-950 dark:via-slate-900 dark:to-indigo-950 text-gray-900 dark:text-white h-screen flex items-center justify-center p-4 lg:p-8 overflow-hidden relative transition-colors duration-500">

    <div class="absolute top-[-5%] left-[-5%] w-[30rem] h-[30rem] bg-emerald-300 dark:bg-emerald-800 rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-40 dark:opacity-20 animate-blob"></div>
    <div class="absolute bottom-[-10%] right-[-5%] w-[25rem] h-[25rem] bg-amber-300 dark:bg-amber-800 rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-40 dark:opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute top-[20%] left-[40%] w-[20rem] h-[20rem] bg-indigo-300 dark:bg-indigo-800 rounded-full mix-blend-multiply dark:mix-blend-color-dodge filter blur-3xl opacity-30 dark:opacity-20 animate-blob animation-delay-4000"></div>

    <div class="w-full max-w-[1600px] h-full bg-white/30 dark:bg-slate-900/60 backdrop-blur-2xl rounded-[2.5rem] shadow-[0_20px_60px_-15px_rgba(16,185,129,0.2)] dark:shadow-[0_20px_60px_-15px_rgba(0,0,0,0.5)] flex overflow-hidden border border-white/50 dark:border-slate-700/50 relative z-10">

        <aside id="sidebar" class="bg-transparent flex flex-col h-full transition-all duration-300 relative z-20 w-72 shrink-0 pt-6 pb-6 border-r border-white/30 dark:border-slate-700/50">

            <script>
                if (localStorage.getItem('ecosync_sidebar_collapsed') === 'true') {
                    document.getElementById('sidebar').classList.add('collapsed');
                }
            </script>

            <div class="logo-container h-20 flex items-center px-8 mb-4 transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-gray-900 flex items-center justify-center text-emerald-400 shadow-md flex-shrink-0">
                    <i class="fas fa-leaf text-lg"></i>
                </div>
                <span class="ml-3 font-extrabold text-xl text-gray-900 tracking-tight nav-label whitespace-nowrap transition-all duration-300">EcoSync</span>
            </div>

            <nav class="flex-1 overflow-y-auto py-2 px-4 space-y-2 custom-scrollbar">

                <a href="dashboard.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl bg-white/50 dark:bg-slate-800/80 shadow-sm text-amber-600 dark:text-amber-500 font-bold relative overflow-hidden transition-all duration-200 backdrop-blur-md border border-white/60 dark:border-slate-600/50">
                    <div class="indicator absolute left-0 top-1/4 bottom-1/4 w-1 bg-amber-500 rounded-r-full transition-all duration-300"></div>
                    <i class="fas fa-chart-pie w-6 text-center text-lg"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300 text-gray-900 dark:text-white">Dashboard</span>
                </a>

                <a href="bin-status.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-trash-alt w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Bin Status</span>
                </a>

                <a href="collection-history.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-clock w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">History</span>
                </a>

                <a href="reports.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-chart-line w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Reports</span>
                </a>

                <a href="users.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-users w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Users</span>
                </a>

                <div class="pt-6 pb-2">
                    <p class="px-4 text-xs font-bold text-gray-500/80 dark:text-gray-400 uppercase tracking-wider nav-label transition-all duration-300">Preferences</p>
                </div>

                <a href="settings.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-cog w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Settings</span>
                </a>

                <div class="pt-2 mt-4 border-t border-white/30 dark:border-slate-700/50">
                    <a href="#" id="logoutBtn" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-rose-50/50 dark:hover:bg-rose-900/20 hover:text-rose-600 dark:hover:text-rose-400 font-semibold transition-all duration-200">
                        <i class="fas fa-sign-out-alt w-6 text-center text-lg group-hover:text-rose-500 transition-colors"></i>
                        <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Logout</span>
                    </a>
                </div>
            </nav>

            <div class="profile-wrapper px-6 mt-4 transition-all duration-300">
                <div class="profile-box p-3 rounded-2xl bg-white/40 backdrop-blur-md border border-white/50 shadow-sm flex items-center cursor-pointer hover:bg-white/60 transition-all group relative overflow-hidden" style="
    right: 5px;
">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-700 dark:text-emerald-400 font-bold flex-shrink-0 border-2 border-white/80 dark:border-slate-700 shadow-sm">
                        JW
                    </div>
                    <div class="ml-3 nav-label transition-all duration-300 whitespace-nowrap">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">John Warren</p>
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Admin</p>
                    </div>
                </div>
            </div>

            <button id="toggleSidebar" class="absolute -right-3.5 top-24 w-7 h-7 rounded-full bg-white/80 dark:bg-slate-800 backdrop-blur-md border border-white dark:border-slate-600 shadow-sm flex items-center justify-center text-gray-600 dark:text-gray-400 hover:text-amber-500 dark:hover:text-amber-400 hover:shadow-md transition-all z-30 focus:outline-none">
                <i id="toggleIcon" class="fas fa-chevron-left text-xs transition-transform duration-300"></i>
            </button>
        </aside>

        <main id="mainContent" class="main-content flex-1 h-full overflow-y-auto custom-scrollbar transition-all duration-300 bg-white/30 dark:bg-slate-900/40 rounded-tl-[2.5rem] rounded-bl-[2.5rem] shadow-[inset_10px_0_30px_rgba(255,255,255,0.4)] dark:shadow-[inset_10px_0_30px_rgba(0,0,0,0.2)] border-l border-white/50 dark:border-slate-700/50">

            <div id="dashboard-page" class="p-10">

                <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
                    <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-1 drop-shadow-sm">Dashboard</h1>
                    <p class="text-gray-700 dark:text-gray-300 font-medium">Welcome to Smart Waste Management System</p>
                    </div>

                    <div class="hidden md:flex items-center space-x-6 mt-4 md:mt-0">
                        <div class="relative w-72">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                        <input type="text" placeholder="Search" class="w-full bg-white/40 dark:bg-slate-800/60 backdrop-blur-xl border border-white/60 dark:border-slate-600/50 rounded-full py-2.5 pl-12 pr-4 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-200 dark:focus:ring-amber-500/50 shadow-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400">
                        </div>
                    <div class="relative z-50">
                        <button id="notificationBellBtn" class="text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 relative w-10 h-10 bg-white/40 dark:bg-slate-800/60 backdrop-blur-xl rounded-full flex items-center justify-center shadow-sm border border-white/60 dark:border-slate-600/50 transition-colors focus:outline-none">
                            <i class="far fa-bell text-lg"></i>
                            <span id="notificationBadge" class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full shadow-[0_0_5px_rgba(244,63,94,0.6)] hidden"></span>
                        </button>

                        <div id="notificationDropdown" class="absolute right-0 mt-3 w-80 bg-white/95 dark:bg-slate-800/95 backdrop-blur-3xl rounded-2xl shadow-2xl border border-white/60 dark:border-slate-700/60 overflow-hidden hidden transform origin-top-right transition-all">
                            <div class="p-4 border-b border-gray-200/50 dark:border-slate-700/50 flex justify-between items-center bg-gray-50/50 dark:bg-slate-900/50">
                                <h3 class="font-bold text-gray-900 dark:text-white">Notifications</h3>
                                <span id="notificationCount" class="text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-400 px-2.5 py-1 rounded-full">0 New</span>
                            </div>
                            <div id="notificationList" class="max-h-[22rem] overflow-y-auto custom-scrollbar">
                                <div class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No new notifications</div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

                    <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 transition-all duration-300 hover:-translate-y-1 hover:bg-white/40 dark:hover:bg-slate-800/60 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-indigo-100/60 dark:bg-indigo-900/40 backdrop-blur-md rounded-2xl border border-indigo-200/50 dark:border-indigo-700/50 group-hover:scale-110 transition-transform">
                                <i class="fas fa-trash text-indigo-600 dark:text-indigo-400 text-xl drop-shadow-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-gray-600 dark:text-gray-400 font-bold text-sm mb-1 uppercase tracking-wider">Total Bins</h3>
                        <p id="statTotalBins" class="text-4xl font-extrabold text-gray-900 dark:text-white drop-shadow-sm">0</p>
                    </div>

                    <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 transition-all duration-300 hover:-translate-y-1 hover:bg-white/40 dark:hover:bg-slate-800/60 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-rose-100/60 dark:bg-rose-900/40 backdrop-blur-md rounded-2xl border border-rose-200/50 dark:border-rose-700/50 group-hover:scale-110 transition-transform">
                                <i class="fas fa-exclamation-triangle text-rose-600 dark:text-rose-400 text-xl drop-shadow-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-gray-600 dark:text-gray-400 font-bold text-sm mb-1 uppercase tracking-wider">Full Bins</h3>
                        <p id="statFullBins" class="text-4xl font-extrabold text-gray-900 dark:text-white drop-shadow-sm">0</p>
                    </div>

                    <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 transition-all duration-300 hover:-translate-y-1 hover:bg-white/40 dark:hover:bg-slate-800/60 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-emerald-100/60 dark:bg-emerald-900/40 backdrop-blur-md rounded-2xl border border-emerald-200/50 dark:border-emerald-700/50 group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 text-xl drop-shadow-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-gray-600 dark:text-gray-400 font-bold text-sm mb-1 uppercase tracking-wider">Collected Today</h3>
                        <p id="statCollectedToday" class="text-4xl font-extrabold text-gray-900 dark:text-white drop-shadow-sm">0</p>
                    </div>

                    <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 transition-all duration-300 hover:-translate-y-1 hover:bg-white/40 dark:hover:bg-slate-800/60 group">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-amber-100/60 dark:bg-amber-900/40 backdrop-blur-md rounded-2xl border border-amber-200/50 dark:border-amber-700/50 group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-line text-amber-600 dark:text-amber-400 text-xl drop-shadow-sm"></i>
                            </div>
                        </div>
                        <h3 class="text-gray-600 dark:text-gray-400 font-bold text-sm mb-1 uppercase tracking-wider">Avg Fill Level</h3>
                        <p id="statAvgFillLevel" class="text-4xl font-extrabold text-gray-900 dark:text-white drop-shadow-sm">0%</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-10">
                    <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] p-8 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 hover:bg-white/40 dark:hover:bg-slate-800/60 transition-colors duration-300">
                        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white mb-6 drop-shadow-sm">Fill Level Overview</h2>
                        <div class="space-y-4" id="fillLevelOverview"></div>
                    </div>

                    <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] p-8 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 hover:bg-white/40 dark:hover:bg-slate-800/60 transition-colors duration-300">
                        <h2 id="recentAlertsTitle" class="text-xl font-extrabold text-gray-900 dark:text-white mb-6 drop-shadow-sm">Recent Alerts (0)</h2>
                        <div class="space-y-4" id="recentAlerts"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="app.js?v=1.1"></script>
</body>

</html>