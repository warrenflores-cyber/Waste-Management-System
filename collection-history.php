<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection History - Smart Waste Management System</title>

    <script>
        if (!localStorage.getItem('ecosync_session')) {
            window.location.replace('login.php');
        }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">

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

        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
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

                <a href="dashboard.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-chart-pie w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Dashboard</span>
            </a>

                <a href="bin-status.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl text-gray-600 dark:text-gray-400 hover:bg-white/40 dark:hover:bg-slate-800/50 hover:text-gray-900 dark:hover:text-white font-semibold transition-all duration-200">
                    <i class="fas fa-trash-alt w-6 text-center text-lg group-hover:text-amber-500 transition-colors"></i>
                <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300">Bin Status</span>
            </a>

                <a href="collection-history.php" class="nav-item group flex items-center px-4 py-3.5 rounded-2xl bg-white/50 dark:bg-slate-800/80 shadow-sm text-amber-600 dark:text-amber-500 font-bold relative overflow-hidden transition-all duration-200 backdrop-blur-md border border-white/60 dark:border-slate-600/50">
                    <div class="indicator absolute left-0 top-1/4 bottom-1/4 w-1 bg-amber-500 rounded-r-full transition-all duration-300"></div>
                <i class="fas fa-clock w-6 text-center text-lg"></i>
                    <span class="ml-3 nav-label whitespace-nowrap transition-all duration-300 text-gray-900 dark:text-white">History</span>
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
                <div class="profile-box p-3 rounded-2xl bg-white/40 backdrop-blur-md border border-white/50 shadow-sm flex items-center cursor-pointer hover:bg-white/60 transition-all group relative overflow-hidden" style="right: 5px;">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-700 dark:text-emerald-400 font-bold flex-shrink-0 border-2 border-white/80 dark:border-slate-700 shadow-sm">
                    JW
                </div>
                <div class="ml-3 nav-label transition-all duration-300 whitespace-nowrap">
                        <p class="text-sm font-bold text-gray-900">John Warren</p>
                        <p class="text-xs font-medium text-gray-600">Admin</p>
                </div>
            </div>
        </div>

            <button id="toggleSidebar" class="absolute -right-3.5 top-24 w-7 h-7 rounded-full bg-white/80 backdrop-blur-md border border-white shadow-sm flex items-center justify-center text-gray-600 hover:text-amber-500 hover:shadow-md transition-all z-30 focus:outline-none">
            <i id="toggleIcon" class="fas fa-chevron-left text-xs transition-transform duration-300"></i>
        </button>
    </aside>

    <!-- Main Content -->
        <main id="mainContent" class="main-content flex-1 h-full overflow-y-auto custom-scrollbar transition-all duration-300 bg-white/30 rounded-tl-[2.5rem] rounded-bl-[2.5rem] shadow-[inset_10px_0_30px_rgba(255,255,255,0.4)] border-l border-white/50">
            <div id="collection-history-page" class="p-10">
                <div class="mb-10">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-1 drop-shadow-sm">Collection History</h1>
                    <p class="text-gray-700 font-medium">View past waste collection records</p>
            </div>

            <!-- Filters -->
                <div class="bg-white/30 backdrop-blur-2xl rounded-[1.5rem] p-6 shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            <input type="text" id="historySearch" placeholder="Search by Bin ID, Location, or Action..." class="w-full pl-12 pr-4 py-2.5 bg-white/40 backdrop-blur-xl border border-white/60 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-200 shadow-sm text-gray-900 placeholder-gray-500">
                    </div>
                    <div class="flex gap-4">
                        <div class="relative">
                            <label for="startDate" class="sr-only">Start Date</label>
                                <input type="date" id="startDate" class="px-4 py-2.5 bg-white/40 backdrop-blur-xl border border-white/60 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-200 shadow-sm text-gray-900">
                        </div>
                        <div class="relative">
                            <label for="endDate" class="sr-only">End Date</label>
                                <input type="date" id="endDate" class="px-4 py-2.5 bg-white/40 backdrop-blur-xl border border-white/60 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-200 shadow-sm text-gray-900">
                        </div>
                            <button id="clearDates" class="px-4 py-2.5 bg-white/50 backdrop-blur-md border border-white/60 text-gray-700 font-bold rounded-xl hover:bg-white/70 transition-colors shadow-sm hidden">
                            Clear Dates
                        </button>
                    </div>
                        <button onclick="exportCSV()" class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 text-white font-bold shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                        <i class="fas fa-file-csv"></i>
                        Export CSV
                    </button>
                </div>
            </div>

            <!-- Table -->
                <div class="bg-white/30 dark:bg-slate-800/40 backdrop-blur-2xl rounded-[1.5rem] shadow-[0_8px_32px_rgba(0,0,0,0.03)] border border-white/50 dark:border-slate-700/50 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                                <tr class="bg-white/40 dark:bg-slate-800/60 border-b border-white/50 dark:border-slate-700/50">
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-200">Date</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-200">Time</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-200">Bin ID</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-200">Location</th>
                                    <th class="px-6 py-4 text-left text-sm font-bold text-gray-900 dark:text-gray-200">Action Taken</th>
                            </tr>
                        </thead>
                            <tbody id="historyTableBody" class="divide-y divide-white/40 dark:divide-slate-700/50 text-gray-800 dark:text-gray-300 font-medium"></tbody>
                    </table>
                </div>
            </div>
                <div class="mt-4 text-sm font-medium text-gray-600 dark:text-gray-400 ml-2" id="historyCount"></div>
        </div>
    </main>
    </div>

    <script src="app.js?v=1.1"></script>
</body>

</html>