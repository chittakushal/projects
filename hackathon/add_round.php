<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$org_id = $_SESSION['org_id'];
$hackathon_id = isset($_GET['hackathon_id']) ? $_GET['hackathon_id'] : null;

if (!$hackathon_id) {
    echo "<script>alert('Invalid hackathon ID'); window.location.href = 'manage_hackathons.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $round_name = trim($_POST['round_name']);
    $round_type = $_POST['round_type'];

    // Validate inputs
    if (empty($round_name) || empty($round_type)) {
        $error = "All fields are required.";
    } else {
        // Insert the round into the database
        $query = "INSERT INTO rounds (hackathon_id, name, type) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $hackathon_id, $round_name, $round_type);

        if ($stmt->execute()) {
            echo "<script>alert('Round added successfully'); window.location.href = 'manage_rounds.php?hackathon_id=$hackathon_id';</script>";
        } else {
            $error = "Error adding round: " . $conn->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Round</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-orange-600 text-center mb-6">Add Round</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label for="round_name" class="block text-gray-700 font-bold mb-2">Round Name:</label>
                <input type="text" id="round_name" name="round_name" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-orange-500" required>
            </div>

            <div class="mb-4">
                <label for="round_type" class="block text-gray-700 font-bold mb-2">Round Type:</label>
                <select id="round_type" name="round_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-orange-500" required>
                    <option value="">Select Round Type</option>
                    <option value="quiztype">Quiz</option>
                    <option value="documenttype">Document</option>
                </select>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded-md shadow-md hover:bg-orange-700">
                    Add Round
                </button>
            </div>
        </form>
    </div>
</body>
</html>
