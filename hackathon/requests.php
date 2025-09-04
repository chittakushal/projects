<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle request acceptance/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['request_id']) && isset($_POST['action'])) {
        $request_id = $_POST['request_id'];
        $action = $_POST['action'];
        
        // Get request details first
        $request_query = "SELECT * FROM team_requests WHERE request_id = ? AND receiver_id = ? AND status = 'pending'";
        $req_stmt = $conn->prepare($request_query);
        $req_stmt->bind_param("ii", $request_id, $user_id);
        $req_stmt->execute();
        $request = $req_stmt->get_result()->fetch_assoc();
        
        if ($request) {
            if ($action === 'accept') {
                $conn->begin_transaction();
                try {
                    // Update request status
                    $update_request = "UPDATE team_requests SET status = 'accepted' WHERE request_id = ?";
                    $update_stmt = $conn->prepare($update_request);
                    $update_stmt->bind_param("i", $request_id);
                    $update_stmt->execute();
                    
                    // Add user to team
                    $insert_member = "INSERT INTO team_members (team_id, user_id) VALUES (?, ?)";
                    $member_stmt = $conn->prepare($insert_member);
                    $member_stmt->bind_param("ii", $request['team_id'], $user_id);
                    $member_stmt->execute();
                    
                    $conn->commit();
                    header("Location: team_messages.php?team_id=" . $request['team_id']);
                    exit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $error = "An error occurred. Please try again.";
                }
            } else if ($action === 'decline') {
                $update_request = "UPDATE team_requests SET status = 'declined' WHERE request_id = ?";
                $update_stmt = $conn->prepare($update_request);
                $update_stmt->bind_param("i", $request_id);
                $update_stmt->execute();
            }
        }
    }
}

// Get pending team requests
$requests_query = "SELECT tr.*, t.name as team_name, u.name as sender_name 
                  FROM team_requests tr 
                  JOIN teams t ON tr.team_id = t.team_id 
                  JOIN users u ON tr.sender_id = u.user_id 
                  WHERE tr.receiver_id = ? AND tr.status = 'pending'";
$requests_stmt = $conn->prepare($requests_query);
$requests_stmt->bind_param("i", $user_id);
$requests_stmt->execute();
$requests_result = $requests_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Requests</title>
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

    <main class="container mx-auto px-4 py-12 max-w-2xl">
        <div class="glass-card rounded-xl p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Team Invitations</h1>

            <?php if ($requests_result->num_rows > 0): ?>
                <div class="space-y-4">
                    <?php while ($request = $requests_result->fetch_assoc()): ?>
                        <div class="bg-white p-6 rounded-lg border">
                            <h2 class="text-xl font-semibold mb-2">
                                Team: <?= htmlspecialchars($request['team_name']) ?>
                            </h2>
                            <p class="text-gray-600 mb-4">
                                Invited by: <?= htmlspecialchars($request['sender_name']) ?>
                            </p>
                            <div class="flex gap-4">
                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" 
                                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                        Accept
                                    </button>
                                </form>
                                <form method="POST" class="flex-1">
                                    <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit" 
                                            class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                                        Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="text-center text-gray-500 py-8">
                    No pending team invitations.
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>