<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$org_id = $_SESSION['org_id'];
$round_id = isset($_GET['round_id']) ? $_GET['round_id'] : null;
$hackathon_id = isset($_GET['hackathon_id']) ? $_GET['hackathon_id'] : null;

if (!$round_id || !$hackathon_id) {
    echo "<script>alert('Invalid round or hackathon ID'); window.location.href = 'manage_hackathons.php';</script>";
    exit();
}

// Fetch existing timer if available
$query = "SELECT * FROM round_timers WHERE round_id = ? AND hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $round_id, $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();
$timer = $result->fetch_assoc();
$stmt->close();

// If a timer is set, get the current timer value
$current_timer = $timer ? $timer['timer'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_timer = isset($_POST['timer']) ? (int)$_POST['timer'] : 0;
    
    if ($new_timer > 0) {
        // Insert or update the timer in the database
        if ($timer) {
            $query = "UPDATE round_timers SET timer = ? WHERE round_id = ? AND hackathon_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $new_timer, $round_id, $hackathon_id);
        } else {
            $query = "INSERT INTO round_timers (round_id, hackathon_id, timer) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $round_id, $hackathon_id, $new_timer);
        }
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Timer set successfully'); window.location.href = 'manage_rounds.php?hackathon_id=$hackathon_id';</script>";
    } else {
        echo "<script>alert('Please enter a valid timer');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Timer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-semibold text-gray-900 mb-8">Set Timer for Round</h1>

        <form method="POST">
            <div class="mb-4">
                <label for="timer" class="block text-sm font-medium text-gray-700">Set Timer (in seconds)</label>
                <input type="number" name="timer" id="timer" value="<?= $current_timer ?>" min="1" required 
                       class="mt-2 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                Save Timer
            </button>
        </form>
    </div>
</body>
</html>
