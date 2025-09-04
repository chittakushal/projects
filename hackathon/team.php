<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['hackathon_id']) || !isset($_GET['team_id'])) {
    die("Missing required parameters.");
}

$hackathon_id = $_GET['hackathon_id'];
$team_id = $_GET['team_id'];
$user_id = $_SESSION['user_id'];

// Get team info and check if user is team leader
$team_query = "SELECT t.*, u.email as leader_email 
               FROM teams t 
               JOIN users u ON t.team_leader_id = u.user_id 
               WHERE t.team_id = ? AND t.hackathon_id = ?";
$team_stmt = $conn->prepare($team_query);
$team_stmt->bind_param("ii", $team_id, $hackathon_id);
$team_stmt->execute();
$team_result = $team_stmt->get_result();

if ($team_result->num_rows === 0) {
    die("Team not found.");
}

$team = $team_result->fetch_assoc();
$is_team_leader = ($team['team_leader_id'] == $user_id);

// Get team members
$members_query = "SELECT u.user_id, u.name, u.email 
                 FROM team_members tm 
                 JOIN users u ON tm.user_id = u.user_id 
                 WHERE tm.team_id = ?";
$members_stmt = $conn->prepare($members_query);
$members_stmt->bind_param("i", $team_id);
$members_stmt->execute();
$members_result = $members_stmt->get_result();

// Handle invitation submission
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $is_team_leader) {
    $invite_email = trim($_POST['email']);
    
    // Check if email exists in users table
    $user_check = "SELECT user_id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($user_check);
    $check_stmt->bind_param("s", $invite_email);
    $check_stmt->execute();
    $user_result = $check_stmt->get_result();
    
    if ($user_result->num_rows === 0) {
        $error = "User with this email does not exist.";
    } else {
        $invited_user = $user_result->fetch_assoc();
        
        // Check if user is already in team
        $member_check = "SELECT 1 FROM team_members WHERE team_id = ? AND user_id = ?";
        $member_stmt = $conn->prepare($member_check);
        $member_stmt->bind_param("ii", $team_id, $invited_user['user_id']);
        $member_stmt->execute();
        
        if ($member_stmt->get_result()->num_rows > 0) {
            $error = "User is already a team member.";
        } else {
            // Check if request already exists
            $request_check = "SELECT 1 FROM team_requests 
                            WHERE team_id = ? AND receiver_id = ? AND status = 'pending'";
            $req_stmt = $conn->prepare($request_check);
            $req_stmt->bind_param("ii", $team_id, $invited_user['user_id']);
            $req_stmt->execute();
            
            if ($req_stmt->get_result()->num_rows > 0) {
                $error = "An invitation is already pending for this user.";
            } else {
                // Create team request
                $insert_request = "INSERT INTO team_requests (team_id, sender_id, receiver_id) 
                                 VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_request);
                $insert_stmt->bind_param("iii", $team_id, $user_id, $invited_user['user_id']);
                
                if ($insert_stmt->execute()) {
                    $success = "Invitation sent successfully!";
                } else {
                    $error = "Failed to send invitation. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management - <?= htmlspecialchars($team['name']) ?></title>
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

    <main class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="glass-card rounded-xl p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Team: <?= htmlspecialchars($team['name']) ?></h1>
                <a href="team_messages.php?team_id=<?= $team_id ?>" 
                   class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Team Chat
                </a>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="space-y-8">
                <?php if ($is_team_leader): ?>
                    <div class="border-b pb-8">
                        <h2 class="text-xl font-semibold mb-4">Invite Team Member</h2>
                        <form method="POST" class="flex gap-4">
                            <input type="email" 
                                   name="email" 
                                   required 
                                   placeholder="Enter email address"
                                   class="flex-1 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                Send Invitation
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

                <div>
                    <h2 class="text-xl font-semibold mb-4">Team Members</h2>
                    <div class="space-y-4">
                        <?php while ($member = $members_result->fetch_assoc()): ?>
                            <div class="flex items-center justify-between p-4 bg-white rounded-lg border">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($member['name']) ?></p>
                                    <p class="text-gray-600 text-sm"><?= htmlspecialchars($member['email']) ?></p>
                                </div>
                                <?php if ($member['user_id'] == $team['team_leader_id']): ?>
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">Team Leader</span>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php
    $team_stmt->close();
    $members_stmt->close();
    $conn->close();
    ?>
</body>
</html>