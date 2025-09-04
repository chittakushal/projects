<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

include('db.php');

// Fetch all hackathons
$hackathons = mysqli_query($conn, "SELECT hackathon_id, name FROM hackathons");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $college = mysqli_real_escape_string($conn, $_POST['college']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $hackathon_id = mysqli_real_escape_string($conn, $_POST['hackathon']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user already exists
    $user_check = mysqli_query($conn, "SELECT user_id FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($user_check) > 0) {
        $row = mysqli_fetch_assoc($user_check);
        $user_id = $row['user_id'];

        // Update user details
        $update_sql = "UPDATE users SET name='$name', college='$college', password='$hashed_password' WHERE user_id='$user_id'";
        mysqli_query($conn, $update_sql);
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, college, role, password) VALUES ('$name', '$email', '$college', 'student', '$hashed_password')";
        mysqli_query($conn, $sql);
        $user_id = mysqli_insert_id($conn);
    }

    // Ensure the user is registered for the selected hackathon
    $participant_check = mysqli_query($conn, "SELECT * FROM participants WHERE user_id = '$user_id' AND hackathon_id = '$hackathon_id'");
    if (mysqli_num_rows($participant_check) > 0) {
        $message = "You are already registered for this hackathon.";
    } else {
        $participant_sql = "INSERT INTO participants (user_id, hackathon_id) VALUES ('$user_id', '$hackathon_id')";
        mysqli_query($conn, $participant_sql);
        $message = "Registration successful! Your details have been updated and you are registered for the selected hackathon.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #E6B9A6 0%, #939185 100%);
        }
    </style>
</head>
<body class="min-h-screen text-gray-800">
    <?php include 'navbar.php'; ?>
    
    <div class="max-w-lg mx-auto mt-16 p-8 bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl">
        <h2 class="text-center text-3xl font-bold mb-8 text-[#2F3645]">Student Registration</h2>

        <?php if (isset($message)): ?>
            <div class="text-center p-4 rounded-lg mb-6 text-white <?php echo (strpos($message, 'Error') === false) ? 'bg-[#2F3645]' : 'bg-red-500'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="student_register.php" method="POST" class="space-y-6">
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
                <label for="college" class="block font-medium mb-2 text-[#2F3645]">College Name</label>
                <input type="text" id="college" name="college" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
            </div>

            <div>
                <label for="password" class="block font-medium mb-2 text-[#2F3645]">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
            </div>
            
            <div>
                <label for="hackathon" class="block font-medium mb-2 text-[#2F3645]">Select Hackathon</label>
                <select id="hackathon" name="hackathon" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F3645] focus:border-transparent">
                    <option value="">-- Select a Hackathon --</option>
                    <?php while ($row = mysqli_fetch_assoc($hackathons)): ?>
                        <option value="<?php echo $row['hackathon_id']; ?>">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" 
                class="w-full py-4 px-6 bg-[#2F3645] text-white rounded-lg font-medium hover:bg-[#939185] transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2F3645]">
                Register
            </button>
        </form>
    </div>
</body>
</html>