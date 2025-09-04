<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$org_id = $_SESSION['org_id'];
$hackathon_id = isset($_GET['hackathon_id']) ? (int)$_GET['hackathon_id'] : null;
$round_id = isset($_GET['round_id']) ? (int)$_GET['round_id'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

if (!$hackathon_id || !$round_id || !$type) {
    echo "<script>alert('Invalid parameters'); window.location.href = 'manage_hackathons.php';</script>";
    exit();
}

// Verify the organizer owns this hackathon
$checkHackathonQuery = "SELECT * FROM hackathons WHERE hackathon_id = ? AND org_id = ?";
$stmt = $conn->prepare($checkHackathonQuery);
$stmt->bind_param("ii", $hackathon_id, $org_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('You don\\'t have permission to manage this hackathon'); window.location.href = 'manage_hackathons.php';</script>";
    exit();
}
$stmt->close();

// Verify the round belongs to this hackathon
$checkRoundQuery = "SELECT * FROM rounds WHERE id = ? AND hackathon_id = ?";
$stmt = $conn->prepare($checkRoundQuery);
$stmt->bind_param("ii", $round_id, $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Round not found for this hackathon'); window.location.href = 'manage_rounds.php?hackathon_id=" . $hackathon_id . "';</script>";
    exit();
}
$roundData = $result->fetch_assoc();
$stmt->close();

// Get round name
$roundName = $roundData['name'];

// Process winner selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $selectedUsers = isset($_POST['winners']) ? $_POST['winners'] : [];
    
    if (empty($selectedUsers)) {
        $error = "Please select at least one winner";
    } else {
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // First, remove existing winners for this round (if any)
            $deleteQuery = "DELETE FROM round_winners WHERE round_id = ? AND hackathon_id = ?";
            $stmt = $conn->prepare($deleteQuery);
            $stmt->bind_param("ii", $round_id, $hackathon_id);
            $stmt->execute();
            $stmt->close();
            
            // Insert new winners
            $insertQuery = "INSERT INTO round_winners (round_id, hackathon_id, user_id, team_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertQuery);
            
            foreach ($selectedUsers as $userId) {
                // Get team_id if available
                $teamId = null;
                $teamQuery = "SELECT team_id FROM team_members WHERE user_id = ? AND team_id IN (
                    SELECT team_id FROM teams WHERE hackathon_id = ?
                )";
                $teamStmt = $conn->prepare($teamQuery);
                $teamStmt->bind_param("ii", $userId, $hackathon_id);
                $teamStmt->execute();
                $teamResult = $teamStmt->get_result();
                
                if ($teamResult->num_rows > 0) {
                    $teamData = $teamResult->fetch_assoc();
                    $teamId = $teamData['team_id'];
                }
                $teamStmt->close();
                
                $stmt->bind_param("iiis", $round_id, $hackathon_id, $userId, $teamId);
                $stmt->execute();
            }
            
            $stmt->close();
            $conn->commit();
            
            $success = "Winners successfully added for " . htmlspecialchars($roundName);
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error adding winners: " . $e->getMessage();
        }
    }
}

// Fetch all participants for this hackathon
if ($type === 'quiz') {
    // For quiz type, fetch participants who have taken this quiz along with their scores
    $query = "SELECT u.user_id, u.name, u.email, u.college, qts.score 
              FROM users u
              JOIN quiz_type_score qts ON u.user_id = qts.user_id
              JOIN participants p ON u.user_id = p.user_id
              WHERE qts.hackathon_id = ? AND qts.round_id = ?
              ORDER BY qts.score DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $hackathon_id, $round_id);
} else if ($type === 'document') {
    // For document type, fetch participants who have submitted documents along with their scores (if evaluated)
    $query = "SELECT u.user_id, u.name, u.email, u.college, dts.filepath, 
              COALESCE(dss.score, 'Not evaluated') as score, dss.feedback
              FROM users u
              JOIN document_type_submissions dts ON u.user_id = dts.user_id
              JOIN participants p ON u.user_id = p.user_id
              LEFT JOIN document_submission_scores dss ON u.user_id = dss.user_id AND dss.round_id = dts.round_id
              WHERE dts.hackathon_id = ? AND dts.round_id = ?
              ORDER BY dss.score DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $hackathon_id, $round_id);
} else {
    // Fallback to get all participants
    $query = "SELECT u.user_id, u.name, u.email, u.college
              FROM users u
              JOIN participants p ON u.user_id = p.user_id
              WHERE p.hackathon_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $hackathon_id);
}

$stmt->execute();
$result = $stmt->get_result();
$participants = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get current winners for this round
$winnersQuery = "SELECT user_id FROM round_winners WHERE round_id = ? AND hackathon_id = ?";
$stmt = $conn->prepare($winnersQuery);
$stmt->bind_param("ii", $round_id, $hackathon_id);
$stmt->execute();
$winnersResult = $stmt->get_result();
$currentWinners = [];

while ($row = $winnersResult->fetch_assoc()) {
    $currentWinners[] = $row['user_id'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Round Winners</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-semibold text-gray-900">Add Round Winners</h1>
                <p class="mt-2 text-gray-600">Round: <?= htmlspecialchars($roundName) ?></p>
            </div>
            <a href="manage_rounds.php?hackathon_id=<?= $hackathon_id ?>" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                <i class="fa fa-arrow-left mr-2"></i>
                Back to Rounds
            </a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="mb-6 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                <p><?= $error ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <p><?= $success ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-medium text-gray-900 mb-4">Select Winners</h2>
                
                <?php if (count($participants) > 0): ?>
                    <form method="POST" action="">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Select
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            College
                                        </th>
                                        <?php if ($type === 'quiz' || $type === 'document'): ?>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Score
                                        </th>
                                        <?php endif; ?>
                                        <?php if ($type === 'document'): ?>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Submission
                                        </th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($participants as $participant): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="winners[]" value="<?= $participant['user_id'] ?>" 
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                                <?= in_array($participant['user_id'], $currentWinners) ? 'checked' : '' ?>>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($participant['name']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($participant['email']) ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($participant['college']) ?></div>
                                        </td>
                                        <?php if ($type === 'quiz'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($participant['score']) ?></div>
                                        </td>
                                        <?php elseif ($type === 'document'): ?>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?= htmlspecialchars($participant['score']) ?></div>
                                            <?php if (isset($participant['feedback']) && !empty($participant['feedback'])): ?>
                                            <div class="text-xs text-gray-500 mt-1">Feedback: <?= htmlspecialchars($participant['feedback']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (isset($participant['filepath']) && !empty($participant['filepath'])): ?>
                                            <a href="<?= htmlspecialchars($participant['filepath']) ?>" 
                                               class="text-blue-600 hover:text-blue-800" 
                                               target="_blank">View Submission</a>
                                            <?php else: ?>
                                            <span class="text-gray-400">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" name="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                                <i class="fa fa-trophy mr-2"></i>
                                Save Winners
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="bg-gray-50 rounded-md p-6 text-center">
                        <p class="text-gray-500">No participants found for this round.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Optional: Add JavaScript to make the UI more interactive
        document.addEventListener('DOMContentLoaded', function() {
            // If you want to add any client-side functionality
        });
    </script>
</body>
</html>