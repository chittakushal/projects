<?php
session_start();
include 'db.php';

if (!isset($_GET['hackathon_id'])) {
    die("Hackathon ID is required.");
}

$hackathon_id = $_GET['hackathon_id'];

$query = "SELECT * FROM hackathons WHERE hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Hackathon not found.");
}

$hackathon = $result->fetch_assoc();

$query = "SELECT u.user_id, u.name, u.email FROM participants p INNER JOIN users u ON p.user_id = u.user_id WHERE p.hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hackathon_id);
$stmt->execute();
$participants_result = $stmt->get_result();

// Handle making a participant a winner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_winner'])) {
    $user_id = $_POST['user_id'];

    $check_winner_query = "SELECT * FROM winners WHERE user_id = ? AND hackathon_id = ?";
    $check_stmt = $conn->prepare($check_winner_query);
    $check_stmt->bind_param("ii", $user_id, $hackathon_id);
    $check_stmt->execute();
    $winner_result = $check_stmt->get_result();

    if ($winner_result->num_rows == 0) {
        $insert_winner_query = "INSERT INTO winners (user_id, hackathon_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_winner_query);
        $insert_stmt->bind_param("ii", $user_id, $hackathon_id);
        $insert_stmt->execute();
        echo "<p class='text-green-500'>Winner added successfully!</p>";
    } else {
        echo "<p class='text-red-500'>This participant is already a winner.</p>";
    }
}

// Handle revoking a winner
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['revoke_winner'])) {
    $user_id = $_POST['user_id'];

    // Delete winner from the winners table
    $delete_winner_query = "DELETE FROM winners WHERE user_id = ? AND hackathon_id = ?";
    $delete_stmt = $conn->prepare($delete_winner_query);
    $delete_stmt->bind_param("ii", $user_id, $hackathon_id);

    if ($delete_stmt->execute()) {
        echo "<p class='text-red-500'>Winner revoked successfully!</p>";
    } else {
        echo "<p class='text-red-500'>Failed to revoke winner. Please try again.</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants - <?= htmlspecialchars($hackathon['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            padding: 8px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-orange-50 to-orange-100 min-h-screen">
    <?php include 'navbar.php'; ?>

    <?php
    // Fetch winners
    $winners_query = "SELECT u.user_id, u.name, u.email FROM winners w 
                     INNER JOIN users u ON w.user_id = u.user_id 
                     WHERE w.hackathon_id = ?";
    $winners_stmt = $conn->prepare($winners_query);
    $winners_stmt->bind_param("i", $hackathon_id);
    $winners_stmt->execute();
    $winners_result = $winners_stmt->get_result();

    // Store winner IDs for easy checking
    $winner_ids = array();
    while ($winner = $winners_result->fetch_assoc()) {
        $winner_ids[] = $winner['user_id'];
    }
    $winners_result->data_seek(0); // Reset result pointer
    ?>

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-orange-600">
                <?= htmlspecialchars($hackathon['name']) ?>
            </h1>
            <div class="flex gap-4">
                <span class="bg-orange-100 text-orange-800 px-4 py-2 rounded-full">
                    👥 <?= $participants_result->num_rows ?> Participants
                </span>
                <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full">
                    🏆 <?= count($winner_ids) ?> Winners
                </span>
            </div>
        </div>

        <!-- Winners Section -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-yellow-600 mb-6 flex items-center">
                <span class="mr-2">🏆</span> Winners
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($winners_result->num_rows > 0): ?>
                    <?php while ($winner = $winners_result->fetch_assoc()): ?>
                        <div class="card bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 relative shadow-lg border border-yellow-200">
                            <div class="badge bg-yellow-400 text-yellow-900">🏆</div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                <?= htmlspecialchars($winner['name']) ?>
                            </h3>
                            <p class="text-gray-600 mb-4"><?= htmlspecialchars($winner['email']) ?></p>
                            <div class="flex gap-2">
                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="user_id" value="<?= $winner['user_id'] ?>">
                                    <button type="submit" name="revoke_winner" 
                                            class="w-full h-12 bg-red-100 text-red-600 px-4 py-2 rounded-lg hover:bg-red-200 transition-colors">
                                        Revoke Winner
                                    </button>
                                </form>
                                <a href="performance.php?user_id=<?= $winner['user_id'] ?>&hackathon_id=<?= $hackathon_id ?>" 
                                   class="flex-1 h-12  bg-blue-100 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors text-center">
                                    Performance
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-8 bg-yellow-50 rounded-xl border border-yellow-200">
                        <p class="text-yellow-600">No winners declared yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Participants Section -->
        <div>
            <h2 class="text-2xl font-semibold text-orange-600 mb-6 flex items-center">
                <span class="mr-2">👥</span> Participants
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($participants_result->num_rows > 0): ?>
                    <?php while ($participant = $participants_result->fetch_assoc()): ?>
                        <div class="card bg-white rounded-xl p-6 relative shadow-md">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                <?= htmlspecialchars($participant['name']) ?>
                            </h3>
                            <p class="text-gray-600 mb-4"><?= htmlspecialchars($participant['email']) ?></p>
                            <div class="flex gap-2">
                                <?php if (!in_array($participant['user_id'], $winner_ids)): ?>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="user_id" value="<?= $participant['user_id'] ?>">
                                        <button type="submit" name="make_winner" 
                                                class="w-full bg-green-100 text-green-600 px-4 h-12 py-2 rounded-lg hover:bg-green-200 transition-colors">
                                            Make Winner
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <a href="performance.php?user_id=<?= $participant['user_id'] ?>&hackathon_id=<?= $hackathon_id ?>" 
                                   class="flex-1 bg-blue-100 text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-200 h-12 transition-colors text-center">
                                    Performance
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-3 text-center py-8 bg-white rounded-xl">
                        <p class="text-gray-600">No participants have registered for this hackathon yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Add success/error message fadeout
        setTimeout(() => {
            const messages = document.querySelectorAll('.text-green-500, .text-red-500');
            messages.forEach(msg => {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
            });
        }, 3000);
    </script>
</body>
</html>
