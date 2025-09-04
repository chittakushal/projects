<?php
session_start();
include 'db.php';

if (!isset($_GET['hackathon_id']) || !isset($_GET['round_id'])) {
    die("Hackathon ID and Round ID are required.");
}

$hackathon_id = $_GET['hackathon_id'];
$round_id = $_GET['round_id'];

// Get hackathon details
$hack_query = "SELECT * FROM hackathons WHERE hackathon_id = ?";
$hack_stmt = $conn->prepare($hack_query);
$hack_stmt->bind_param("i", $hackathon_id);
$hack_stmt->execute();
$hack_result = $hack_stmt->get_result();

if ($hack_result->num_rows == 0) {
    die("Hackathon not found.");
}

$hackathon = $hack_result->fetch_assoc();
$hack_stmt->close();

// Get round details
$round_query = "SELECT * FROM rounds WHERE id = ? AND hackathon_id = ?";
$round_stmt = $conn->prepare($round_query);
$round_stmt->bind_param("ii", $round_id, $hackathon_id);
$round_stmt->execute();
$round_result = $round_stmt->get_result();

if ($round_result->num_rows == 0) {
    die("Round not found for this hackathon.");
}

$round = $round_result->fetch_assoc();
$round_stmt->close();

// Get winners for this round
$winners_query = "SELECT rw.user_id, rw.team_id, u.name, u.email, u.college, t.name as team_name 
                  FROM round_winners rw 
                  JOIN users u ON rw.user_id = u.user_id 
                  LEFT JOIN teams t ON rw.team_id = t.team_id 
                  WHERE rw.round_id = ? AND rw.hackathon_id = ?";
$winners_stmt = $conn->prepare($winners_query);
$winners_stmt->bind_param("ii", $round_id, $hackathon_id);
$winners_stmt->execute();
$winners_result = $winners_stmt->get_result();
$winners = $winners_result->fetch_all(MYSQLI_ASSOC);
$winners_stmt->close();

// If we need score data
if ($round['type'] == 'quiztype') {
    $scores_query = "SELECT u.user_id, qts.score 
                    FROM quiz_type_score qts 
                    JOIN users u ON qts.user_id = u.user_id 
                    WHERE qts.round_id = ? AND qts.hackathon_id = ?";
    $scores_stmt = $conn->prepare($scores_query);
    $scores_stmt->bind_param("ii", $round_id, $hackathon_id);
    $scores_stmt->execute();
    $scores_result = $scores_stmt->get_result();
    $scores = [];
    
    while ($score = $scores_result->fetch_assoc()) {
        $scores[$score['user_id']] = $score['score'];
    }
    $scores_stmt->close();
} elseif ($round['type'] == 'documenttype') {
    $scores_query = "SELECT u.user_id, dss.score, dss.feedback 
                    FROM document_submission_scores dss 
                    JOIN users u ON dss.user_id = u.user_id 
                    WHERE dss.round_id = ? AND dss.hackathon_id = ?";
    $scores_stmt = $conn->prepare($scores_query);
    $scores_stmt->bind_param("ii", $round_id, $hackathon_id);
    $scores_stmt->execute();
    $scores_result = $scores_stmt->get_result();
    $scores = [];
    $feedback = [];
    
    while ($score = $scores_result->fetch_assoc()) {
        $scores[$score['user_id']] = $score['score'];
        $feedback[$score['user_id']] = $score['feedback'];
    }
    $scores_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Round Winners - <?= htmlspecialchars($round['name']) ?></title>
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

    <main class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">
                🏆 Round Winners: <?= htmlspecialchars($round['name']) ?>
            </h1>
            <a href="hackathon.php?hackathon_id=<?= $hackathon_id ?>" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                Back to Hackathon
            </a>
        </div>
        
        <div class="glass-card p-6 rounded-xl mb-8">
            <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($hackathon['name']) ?></h2>
            <p class="text-gray-600">
                <?= $round['type'] == 'quiztype' ? 'Quiz Round' : 'Document Submission Round' ?>
            </p>
        </div>

        <?php if (count($winners) > 0): ?>
            <div class="glass-card rounded-xl overflow-hidden">
                <div class="bg-yellow-500 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">Congratulations to our Winners!</h3>
                </div>
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Participant
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        College
                                    </th>
                                    <?php if (isset($scores)): ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <?php endif; ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Team
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($winners as $winner): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($winner['name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($winner['email']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= htmlspecialchars($winner['college']) ?>
                                    </td>
                                    <?php if (isset($scores)): ?>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if (isset($scores[$winner['user_id']])): ?>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($scores[$winner['user_id']]) ?>
                                            </div>
                                            <?php if (isset($feedback) && isset($feedback[$winner['user_id']])): ?>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Feedback: <?= htmlspecialchars($feedback[$winner['user_id']]) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-sm text-gray-500">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= $winner['team_id'] ? htmlspecialchars($winner['team_name']) : 'Individual Participant' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="glass-card p-8 rounded-xl text-center">
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No winners announced yet</h3>
                <p class="text-gray-500">
                    Winners for this round haven't been announced. Check back later!
                </p>
            </div>
        <?php endif; ?>
    </main>
    <?php $conn->close(); ?>
</body>
</html>