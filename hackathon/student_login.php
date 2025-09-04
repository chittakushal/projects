<?php 
session_start(); 
include('db.php');

if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'student'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid email or password.";
        }
    } else {
        $message = "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366F1;
            --primary-dark: #4F46E5;
            --secondary: #F8FAFC;
            --accent: #818CF8;
            --text: #1F2937;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
        }

        .button-animation {
            transition: all 0.3s ease;
        }

        .button-animation:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>
    
    <div class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="glass-effect w-full max-w-md p-8 rounded-2xl shadow-xl">
            <div class="mb-8 text-center">
                <h2 class="text-3xl font-semibold text-text mb-2">Welcome Back</h2>
                <p class="text-gray-600">Sign in to continue your learning journey</p>
            </div>

            <?php if (isset($message)): ?>
                <div class="mb-6 p-4 rounded-lg text-white text-center <?php echo (strpos($message, 'Error') === false) ? 'bg-green-500' : 'bg-red-500'; ?> animate-appear">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="student_login.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required 
                        class="input-focus w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 placeholder-gray-400"
                        placeholder="Enter your email"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="input-focus w-full px-4 py-3 rounded-lg border border-gray-300 text-gray-900 placeholder-gray-400"
                        placeholder="Enter your password"
                    >
                </div>

           

                <button 
                    type="submit" 
                    class="button-animation w-full py-3 px-6 bg-primary hover:bg-primary-dark text-black font-medium rounded-lg shadow-md hover:shadow-lg"
                >
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-600">
                Don't have an account? 
                <a href="student_register.php" class="text-primary hover:text-primary-dark font-medium">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>