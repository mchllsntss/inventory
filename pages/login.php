<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            height: 100vh;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e9 50%, #d0e8d5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Optional very soft green overlay glow */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 70%, rgba(34, 197, 94, 0.08) 0%, transparent 40%);
            pointer-events: none;
            z-index: 0;
        }

        /* Login card - green theme */
        .login-card {
            opacity: 0;
            transform: translateY(30px);
            animation: slideUpFade 1s ease-out forwards;
            background: linear-gradient(135deg, #ffffff 0%, #f8fdf8 100%);
            border: 1px solid #a5d6a7;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(34, 197, 94, 0.12) inset;
            border-radius: 20px;
            width: 100%;
            max-width: 420px;
            padding: 2.5rem 2rem;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(6px);
        }

        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Floating logo */
        .logo {
            animation: float 3.5s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50%      { transform: translateY(-10px) scale(1.015); }
        }

        /* Input focus - green glow */
        input:focus {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.22);
            border-color: #22c55e;
            outline: none;
        }

        /* Button */
        .login-btn {
            background: linear-gradient(45deg, #16a34a, #22c55e);
            color: white;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .login-btn:hover {
            background: linear-gradient(45deg, #15803d, #16a34a);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(34, 197, 94, 0.35);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Title gradient */
        .login-label {
            font-size: 2.1rem;
            font-weight: 800;
            background: linear-gradient(90deg, #1b5e20, #2e7d32, #43a047);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: 1.2px;
        }

        /* Error message */
        .error-message {
            background-color: #ffebee;
            border-left: 5px solid #c62828;
            color: #b71c1c;
            border-radius: 8px;
        }

        /* Links */
        a, .forgot-password {
            color: #2e7d32;
            transition: color 0.2s;
        }

        a:hover, .forgot-password:hover {
            color: #1b5e20;
        }

        .text-gray-600 { color: #555; }
        .text-gray-700 { color: #333; }
    </style>
</head>

<body>

    <?php include '../components/header.php'; ?>
    
    <div class="login-card">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="../images/logo.png" alt="Logo" class="logo w-56 h-auto drop-shadow-xl">
        </div>
        
        <!-- Login Label -->
        <label class="block text-center mb-8 login-label">LOGIN</label>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="mb-6 p-4 error-message rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="../controller/login_controller.php">
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2" for="username">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Username
                    </span>
                </label>
                <input type="text" class="w-full px-4 py-3 rounded-lg border border-gray-300 text-sm focus:outline-none transition duration-200" id="username" name="username" required placeholder="Enter username">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        Password
                    </span>
                </label>
                <div class="relative">
                    <input type="password" class="w-full px-4 py-3 rounded-lg border border-gray-300 text-sm focus:outline-none pr-12 transition duration-200" id="password" name="password" required placeholder="Enter password">
                    <img src="../images/eye2.png" id="passwordToggle" class="absolute right-4 top-1/2 transform -translate-y-1/2 cursor-pointer w-6 h-6 hover:opacity-80 transition" onclick="togglePassword()">
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="login-btn w-full py-3.5 rounded-full text-base font-semibold shadow-md focus:outline-none">
                    Login
                </button>
            </div>
        </form>
        
    </div>

    <!-- Password visibility toggle -->
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var passwordToggle = document.getElementById("passwordToggle");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.src = "../images/eye.png";
            } else {
                passwordInput.type = "password";
                passwordToggle.src = "../images/eye2.png";
            }
        }
    </script>

</body>
</html>