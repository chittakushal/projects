<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['hackathon_id'])) {
    die("Hackathon ID is required.");
}

$hackathon_id = $_GET['hackathon_id'];
$user_id = $_SESSION['user_id'];

// Check if user is already in a team for this hackathon
$check_query = "SELECT tm.team_id 
                FROM team_members tm 
                INNER JOIN teams t ON tm.team_id = t.team_id 
                WHERE tm.user_id = ? AND t.hackathon_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $user_id, $hackathon_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    $team_data = $check_result->fetch_assoc();
    header("Location: team.php?hackathon_id=" . $hackathon_id . "&team_id=" . $team_data['team_id']);
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $team_name = trim($_POST['team_name']);
    
    if (empty($team_name)) {
        $error = "Team name is required.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create the team
            $insert_team = "INSERT INTO teams (name, hackathon_id, team_leader_id) VALUES (?, ?, ?)";
            $team_stmt = $conn->prepare($insert_team);
            $team_stmt->bind_param("sii", $team_name, $hackathon_id, $user_id);
            $team_stmt->execute();
            
            $team_id = $conn->insert_id;
            
            // Add the user as a team member
            $insert_member = "INSERT INTO team_members (team_id, user_id) VALUES (?, ?)";
            $member_stmt = $conn->prepare($insert_member);
            $member_stmt->bind_param("ii", $team_id, $user_id);
            $member_stmt->execute();
            
            $conn->commit();
            
            header("Location: team.php?hackathon_id=" . $hackathon_id . "&team_id=" . $team_id);
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred while creating the team. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Team</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #FDFCFB 0%, #E2D1C3 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-md">
        <div class="glass-card rounded-xl p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Create Your Team</h1>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label for="team_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Team Name
                    </label>
                    <input type="text" 
                           id="team_name" 
                           name="team_name" 
                           required 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter team name">
                </div>

                <div class="flex gap-4">
                    <a href="hackathon.php?hackathon_id=<?= $hackathon_id ?>" 
                       class="flex-1 px-4 py-2 bg-gray-500 text-white rounded-lg text-center hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg text-center hover:bg-blue-700 transition-colors">
                        Create Team
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
