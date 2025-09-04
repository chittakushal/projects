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
$org_id = isset($_SESSION['org_id']) ? $_SESSION['org_id'] : null;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is registered for the hackathon
$is_registered = false;
$is_team_member = false;
$team_id = null;
$current_round = 1; // Default to the first round

if ($user_id) {
    // Check if the user is registered
    $reg_query = "SELECT * FROM participants WHERE user_id = ? AND hackathon_id = ?";
    $reg_stmt = $conn->prepare($reg_query);
    $reg_stmt->bind_param("ii", $user_id, $hackathon_id);
    $reg_stmt->execute();
    $reg_result = $reg_stmt->get_result();
    if ($reg_result->num_rows > 0) {
        $is_registered = true;
        $participant_data = $reg_result->fetch_assoc();
        $current_round = $participant_data['current_round']; // Get the current round of the participant
    }

    // Check if the user is part of a team
    if ($is_registered) {
        $team_query = "SELECT tm.team_id FROM team_members tm INNER JOIN teams t ON tm.team_id = t.team_id WHERE tm.user_id = ? AND t.hackathon_id = ?";
        $team_stmt = $conn->prepare($team_query);
        $team_stmt->bind_param("ii", $user_id, $hackathon_id);
        $team_stmt->execute();
        $team_result = $team_stmt->get_result();
        if ($team_result->num_rows > 0) {
            $is_team_member = true;
            $team_data = $team_result->fetch_assoc();
            $team_id = $team_data['team_id'];
        }
    }
}

// Check if hackathon is over
$current_date = date('Y-m-d');
$is_hackathon_over = $current_date > $hackathon['end_date'];

// Fetch all rounds
$rounds_query = "SELECT * FROM rounds WHERE hackathon_id = ? ORDER BY id ASC";
$rounds_stmt = $conn->prepare($rounds_query);
$rounds_stmt->bind_param("i", $hackathon_id);
$rounds_stmt->execute();
$rounds_result = $rounds_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hackathon['name']) ?></title>
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
        .copy-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            position: relative;
            transition: background-color 0.3s;
        }

        .copy-btn:hover {
            background-color: #0056b3;
        }

        .tooltip {
            visibility: hidden;
            background-color: black;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 5px;
            position: absolute;
            top: -35px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 14px;
            white-space: nowrap;
        }

        .copy-btn:active .tooltip {
            visibility: visible;
        }
    </style>
</head>
<body class="min-h-screen">
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-12 max-w-5xl">
        <?php if ($hackathon['file_path']) : ?>
            <div class="relative h-96 rounded-2xl overflow-hidden mb-8 glass-card">
                <img src="<?= htmlspecialchars($hackathon['file_path']) ?>" alt="<?= htmlspecialchars($hackathon['name']) ?>" class="w-full h-full object-cover">
            </div>
        <?php endif; ?>

        <h1 class="text-4xl font-bold text-gray-900 mb-8"><?= htmlspecialchars($hackathon['name']) ?></h1>
        <div class="space-y-8">
            <div class="glass-card rounded-xl p-8">
                <p class="text-lg text-gray-700 leading-relaxed"><?= $hackathon['description'] ?></p>
            </div>
            <br><br>
            <div class="flex gap-4 flex-wrap">
                <?php if ($user_id && $is_registered) : ?>
                    <?php if ($is_team_member) : ?>
                        <a href="team.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>&team_id=<?= $team_id ?>" 
                           class="btn bg-green-600 text-white px-6 py-3 rounded-lg font-semibold">
                            Go to Team
                        </a>
                    <?php else : ?>
                        <a href="make_team.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" 
                           class="btn bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold">
                            Make Team
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($is_hackathon_over && $hackathon['is_certificates_issued']) : ?>
                        <a target="_blank" href="get_certificate.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>&user_id=<?= $user_id ?>" 
                           class="btn bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold">
                            🎓 Get Certificate
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Rounds Section -->
        
<div>
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Rounds</h2>
    <div class="space-y-4">
        <?php
        $round_number = 1;
        while ($round = $rounds_result->fetch_assoc()) :
            $is_round_enabled = $round_number <= $current_round; // Enable rounds sequentially
            
            // Check if there are winners for this round
            $winners_query = "SELECT COUNT(*) as winner_count FROM round_winners WHERE round_id = ? AND hackathon_id = ?";
            $winners_stmt = $conn->prepare($winners_query);
            $winners_stmt->bind_param("ii", $round['id'], $hackathon_id);
            $winners_stmt->execute();
            $winners_result = $winners_stmt->get_result();
            $winners_data = $winners_result->fetch_assoc();
            $has_winners = $winners_data['winner_count'] > 0;
            $winners_stmt->close();
        ?>
            <div class="glass-card rounded-xl p-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <?= htmlspecialchars($round['name']) ?>
                    </h3>
                    <div class="flex gap-2">
                        <?php if ($has_winners) : ?>
                            <a href="check_winners.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>&round_id=<?= $round['id'] ?>" 
                               class="btn bg-yellow-500 text-white px-4 py-2 rounded-lg font-medium">
                                🏆 View Winners
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($user_id && $is_registered && !$is_hackathon_over) : ?>
                            <?php if ($is_round_enabled) : ?>
                                <?php if ($round['type'] == 'documenttype') : ?>
                                    <a href="submit_document.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>&round_id=<?= $round['id'] ?>&user_id=<?= $user_id ?>" 
                                       class="btn bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium">
                                        Submit Document
                                    </a>
                                <?php elseif ($round['type'] == 'quiztype') : ?>
                                    <a href="answer_quiz.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>&round_id=<?= $round['id'] ?>&user_id=<?= $user_id ?>" 
                                       class="btn bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                                        Take Quiz
                                    </a>
                                <?php endif; ?>
                            <?php else : ?>
                                <button class="btn bg-gray-400 text-white px-4 py-2 rounded-lg font-medium cursor-not-allowed" disabled>
                                    Complete Round <?= $round_number - 1 ?> to Unlock
                                </button>
                            <?php endif; ?>
                        <?php else : ?>
                            <button class="btn bg-gray-400 text-white px-4 py-2 rounded-lg font-medium cursor-not-allowed" disabled>
                                <?= $is_hackathon_over ? 'Round Closed' : 'Register to Participate' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php
        $round_number++;
        endwhile;
        ?>
    </div>
</div>
<button class="copy-btn" onclick="copyCurrentURL()">
            Copy Page URL
            <span class="tooltip" id="tooltip">Copied!</span>
</button>
            <!-- Show Winners Button -->
            <div class="mt-10 text-center">
                <a href="winners.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" 
                   class="btn bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold">
                    🏆 Show Winners
                </a>
            </div>
        </div>
    </main>
    <?php $stmt->close(); $conn->close(); ?>

    <script>
        function copyCurrentURL() {
            const currentURL = window.location.href; // Get the current page URL
            navigator.clipboard.writeText(currentURL).then(() => {
                const tooltip = document.getElementById("tooltip");
                tooltip.style.visibility = "visible";
                setTimeout(() => {
                    tooltip.style.visibility = "hidden";
                }, 1000);
            }).catch(err => {
                console.error("Failed to copy: ", err);
            });
        }
    </script>
</body>
</html>