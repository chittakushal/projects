<?php
session_start();
include('db.php');

if(isset($_SESSION['org_id'])){
    header("Location: organizer_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $college = mysqli_real_escape_string($conn, $_POST['college']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO organizers (name, email, company_name_or_college_name, role, password) 
            VALUES ('$name', '$email', '$college', 'organizer', '$hashed_password')";
    
    if (mysqli_query($conn, $sql)) {
        $message = "Registration successful!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #E6B9A6 0%, #939185 100%);
        }
    </style>
</head>
<body class="min-h-screen text-gray-800">
    <?php include 'navbar.php' ?>
    
    <div class="max-w-lg mx-auto mt-16 p-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl">
        <h2 class="text-center text-3xl font-bold mb-8 text-[#2F3645]">Organizer Registration</h2>

        <?php if (isset($message)): ?>
            <div class="text-center p-4 rounded-lg mb-6 text-white <?php echo (strpos($message, 'Error') === false) ? 'bg-[#2F3645]' : 'bg-red-500'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="organizer_register.php" method="POST" class="space-y-6">
            <div>
                <label for="name" class="block font-medium mb-2 text-[#2F3645]">Full Name</label>
                <input type="text" id="name" name="name" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
            </div>
            
            <div>
                <label for="email" class="block font-medium mb-2 text-[#2F3645]">Email</label>
                <input type="email" id="email" name="email" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
            </div>

            <div>
                <label for="college" class="block font-medium mb-2 text-[#2F3645]">College Name/Company Name</label>
                <input type="text" id="college" name="college" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
            </div>

            <div>
                <label for="password" class="block font-medium mb-2 text-[#2F3645]">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
            </div>

            <button type="submit" 
                class="w-full py-4 px-6 bg-[#2F3645] text-white rounded-lg font-medium hover:bg-[#939185] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2F3645]">
                Register
            </button>
        </form>
    </div>
</body>
</html>