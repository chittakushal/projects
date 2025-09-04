<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

if (!isset($_GET['team_id'])) {
    die("Team ID is required.");
}

$user_id = $_SESSION['user_id'];
$team_id = $_GET['team_id'];

// Verify user is a team member
$member_check = "SELECT 1 FROM team_members WHERE team_id = ? AND user_id = ?";
$check_stmt = $conn->prepare($member_check);
$check_stmt->bind_param("ii", $team_id, $user_id);
$check_stmt->execute();

if ($check_stmt->get_result()->num_rows === 0) {
    die("Access denied. You are not a member of this team.");
}

// Get team details
$team_query = "SELECT t.*, h.name as hackathon_name 
               FROM teams t 
               JOIN hackathons h ON t.hackathon_id = h.hackathon_id 
               WHERE t.team_id = ?";
$team_stmt = $conn->prepare($team_query);
$team_stmt->bind_param("i", $team_id);
$team_stmt->execute();
$team = $team_stmt->get_result()->fetch_assoc();

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $insert_msg = "INSERT INTO messages (team_id, sender_id, message) VALUES (?, ?, ?)";
        $msg_stmt = $conn->prepare($insert_msg);
        $msg_stmt->bind_param("iis", $team_id, $user_id, $message);
        $msg_stmt->execute();

        // Redirect to avoid resubmission on refresh
        header("Location: " . $_SERVER['PHP_SELF'] . "?team_id=" . $team_id);
        exit();
    }
}

// Handle message deletion
if (isset($_POST['delete_message_id']) && is_numeric($_POST['delete_message_id'])) {
    $message_id = $_POST['delete_message_id'];
    $delete_query = "DELETE FROM messages WHERE message_id = ? AND sender_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $message_id, $user_id);
    $delete_stmt->execute();
}

// Get messages with sender information
$messages_query = "SELECT m.*, u.name as sender_name 
                  FROM messages m 
                  JOIN users u ON m.sender_id = u.user_id 
                  WHERE m.team_id = ? 
                  ORDER BY m.timestamp DESC 
                  LIMIT 100";
$messages_stmt = $conn->prepare($messages_query);
$messages_stmt->bind_param("i", $team_id);
$messages_stmt->execute();
$messages_result = $messages_stmt->get_result();

// Get team members
$members_query = "SELECT u.user_id, u.name 
                 FROM team_members tm 
                 JOIN users u ON tm.user_id = u.user_id 
                 WHERE tm.team_id = ?";
$members_stmt = $conn->prepare($members_query);
$members_stmt->bind_param("i", $team_id);
$members_stmt->execute();
$members_result = $members_stmt->get_result();

$members = [];
while ($member = $members_result->fetch_assoc()) {
    $members[$member['user_id']] = $member['name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Chat - <?= htmlspecialchars($team['name']) ?></title>
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
        .messages-container {
            height: calc(100vh - 400px);
            min-height: 400px;
        }
        .message-bubble {
            max-width: 80%;
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="glass-card rounded-xl p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($team['name']) ?></h1>
                    <p class="text-gray-600">
                        Hackathon: <?= htmlspecialchars($team['hackathon_name']) ?>
                    </p>
                </div>
                <a href="team.php?team_id=<?= $team_id ?>&hackathon_id=<?= $team['hackathon_id'] ?>" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Team Details
                </a>
            </div>

            <div class="flex gap-6">
                <!-- Team Members Sidebar -->
                <div class="w-64 shrink-0">
                    <div class="bg-white rounded-lg p-4">
                        <h2 class="font-semibold text-gray-900 mb-3">Team Members</h2>
                        <ul class="space-y-2">
                            <?php foreach ($members as $memberId => $memberName): ?>
                                <li class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    <?= htmlspecialchars($memberName) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="flex-1 flex flex-col">
                    <div class="messages-container bg-white rounded-lg p-4 mb-4 overflow-y-auto flex flex-col-reverse">
                        <?php while ($message = $messages_result->fetch_assoc()): ?>
                            <div class="mb-4 <?= $message['sender_id'] == $user_id ? 'ml-auto' : '' ?>">
                                <div class="message-bubble <?= $message['sender_id'] == $user_id ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900' ?> rounded-lg p-3">
                                    <p class="mb-1"><?= htmlspecialchars($message['message']) ?></p>
                                    <div class="text-xs <?= $message['sender_id'] == $user_id ? 'text-blue-100' : 'text-gray-500' ?>">
                                        <?= htmlspecialchars($message['sender_name']) ?> • 
                                        <?= date('M j, g:i a', strtotime($message['timestamp'])) ?>
                                    </div>
                                </div>
                                <?php if ($message['sender_id'] == $user_id): ?>
                                    <form method="POST" action="" class="mt-2">
                                        <input type="hidden" name="delete_message_id" value="<?= $message['message_id'] ?>">
                                        <button type="submit" class="text-red-600 text-xs hover:underline">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <form method="POST" class="flex gap-4">
                        <input type="text" 
                               name="message" 
                               required 
                               placeholder="Type your message..."
                               class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Send
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Auto-scroll to bottom of messages on page load
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.querySelector('.messages-container');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    });

    // Optional: Add auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 5000);
    </script>

    <?php
    $check_stmt->close();
    $team_stmt->close();
    $messages_stmt->close();
    $members_stmt->close();
    $conn->close();
    ?>
</body>
</html>
