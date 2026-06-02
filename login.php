<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EcoSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Smooth Load Animations */
        .fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        .delay-100 {
            animation-delay: 0.1s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-300 {
            animation-delay: 0.3s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Input Autofill styling for Chrome */
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px white inset !important;
        }
    </style>
</head>

<body class="bg-white min-h-screen font-sans flex overflow-hidden">

    <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-emerald-600 to-teal-900 text-white flex-col justify-between p-12 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-20 pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full border-4 border-white border-opacity-20"></div>
            <div class="absolute top-1/2 right-1/4 w-64 h-64 rounded-full border-4 border-white border-opacity-10 transform -translate-y-1/2"></div>
            <div class="absolute -bottom-32 -right-32 w-full h-full rounded-full bg-white opacity-5"></div>
        </div>

        <div class="relative z-10 fade-in-up">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-leaf text-2xl"></i>
                </div>
                <span class="font-bold text-3xl tracking-tight">EcoSync</span>
            </div>
        </div>

        <div class="relative z-10 fade-in-up delay-100 mb-20">
            <h1 class="text-5xl font-bold mb-6 leading-tight">Smart Waste<br>Management.</h1>
            <p class="text-emerald-100 text-lg max-w-md leading-relaxed">
                Monitor bin levels in real-time, optimize collection routes, and keep your campus clean with data-driven insights.
            </p>
        </div>

        <div class="relative z-10 flex gap-4 text-sm text-emerald-200 fade-in-up delay-200">
            <p>&copy; 2026 EcoSync System</p>
            <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 relative">
        <div class="w-full max-w-md">

            <div class="lg:hidden flex items-center gap-3 mb-8 fade-in-up">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-leaf text-xl"></i>
                </div>
                <span class="font-bold text-2xl text-gray-900 tracking-tight">EcoSync</span>
            </div>

            <div class="fade-in-up delay-100">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome back</h2>
                <p class="text-gray-500 mb-6">Please enter your details to access the dashboard.</p>
            </div>

            <div id="errorMessage" class="hidden p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 fade-in-up delay-100">
                Invalid email or password.
            </div>

            <form id="loginForm" class="space-y-6">

                <div class="fade-in-up delay-200">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="far fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" id="email" class="block w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-gray-50 hover:bg-gray-100 focus:bg-white" placeholder="admin@ecosync.com" required>
                    </div>
                </div>

                <div class="fade-in-up delay-300">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" id="password" class="block w-full pl-11 pr-12 py-3.5 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all bg-gray-50 hover:bg-gray-100 focus:bg-white" placeholder="••••••••" required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-600 transition-colors focus:outline-none">
                            <i class="far fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between fade-in-up delay-300">
                    <div class="flex items-center">
                        <input id="remember-me" type="checkbox" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded cursor-pointer transition-colors accent-emerald-600">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer">
                            Remember for 30 days
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-500 transition-colors">Forgot password?</a>
                    </div>
                </div>

                <div class="fade-in-up delay-300 pt-2">
                    <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                        Sign in
                        <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </button>
                </div>

            </form>

            <div class="mt-8 text-center fade-in-up delay-300">
                <p class="text-sm text-gray-600">
                    Don't have an account?
                    <a href="#" class="font-semibold text-emerald-600 hover:text-emerald-500 transition-colors">Contact Administrator</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Password Visibility Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        // Form Submission Logic
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault(); // Stop the page from instantly reloading

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorBox = document.getElementById('errorMessage');
            const submitBtn = document.querySelector('button[type="submit"]');

            // Show a loading spinner on the button
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            errorBox.classList.add('hidden'); // Hide any old errors

            try {
                // Send the data directly to your PHP API
                const response = await fetch('login_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email,
                        password
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Save the user data in the browser so dashboard.html knows who logged in
                    localStorage.setItem('ecosync_session', JSON.stringify(data.user));
                    // Glide straight into the dashboard
                    window.location.href = 'dashboard.php';
                } else {
                    // Show the red error box from PHP
                    errorBox.textContent = data.message;
                    errorBox.classList.remove('hidden');
                    submitBtn.innerHTML = 'Sign in <i class="fas fa-arrow-right ml-2 text-xs"></i>';
                }
            } catch (err) {
                // If PHP crashes or the server is down
                errorBox.textContent = "Cannot connect to the server. Check XAMPP.";
                errorBox.classList.remove('hidden');
                submitBtn.innerHTML = 'Sign in <i class="fas fa-arrow-right ml-2 text-xs"></i>';
            }
        });
    </script>
</body>

</html>