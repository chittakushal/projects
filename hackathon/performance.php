<?php
session_start();
include 'db.php';

if (!isset($_GET['user_id']) || !isset($_GET['hackathon_id'])) {
    die("User ID and Hackathon ID are required.");
}

$user_id = $_GET['user_id'];
$hackathon_id = $_GET['hackathon_id'];

// Check if current user is an organizer
$is_organizer = true;
 
// Handle score submission from organizer
if (isset($_POST['submit_score']) && $is_organizer) {
    $round_id = $_POST['round_id'];
    $score = $_POST['score'];
    $feedback = $_POST['feedback'];
    
    // Check if score already exists
    $check_query = "SELECT * FROM document_submission_scores 
                   WHERE user_id = ? AND hackathon_id = ? AND round_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("iii", $user_id, $hackathon_id, $round_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing score
        $update_query = "UPDATE document_submission_scores 
                        SET score = ?, feedback = ?, evaluated_by = ?, evaluated_at = CURRENT_TIMESTAMP 
                        WHERE user_id = ? AND hackathon_id = ? AND round_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("dsiiii", $score, $feedback, $org_id, $user_id, $hackathon_id, $round_id);
        $update_stmt->execute();
        $message = "Score updated successfully!";
    } else {
        // Insert new score
        $insert_query = "INSERT INTO document_submission_scores 
                        (user_id, hackathon_id, round_id, score, feedback, evaluated_by) 
                        VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iiidsi", $user_id, $hackathon_id, $round_id, $score, $feedback, $org_id);
        $insert_stmt->execute();
        $message = "Score submitted successfully!";
    }
}

// Fetch rounds
$query = "SELECT id, name, type FROM rounds WHERE hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hackathon_id);
$stmt->execute();
$rounds_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-gray-700 mb-6">Performance Details</h1>
        
        <?php if (isset($message)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($rounds_result->num_rows > 0): ?>
            <?php while ($round = $rounds_result->fetch_assoc()): ?>
                <div class="bg-white shadow-md rounded-lg p-6 mb-4">
                    <h2 class="font-semibold text-gray-800"><?= htmlspecialchars($round['name']) ?> (<?= $round['type'] ?>)</h2>

                    <?php if ($round['type'] === 'quiztype'): ?>
                        <?php
                        $score_query = "SELECT score FROM quiz_type_score WHERE user_id = ? AND hackathon_id = ? AND round_id = ?";
                        $score_stmt = $conn->prepare($score_query);
                        $score_stmt->bind_param("iii", $user_id, $hackathon_id, $round['id']);
                        $score_stmt->execute();
                        $score_result = $score_stmt->get_result();
                        $score = $score_result->fetch_assoc();
                        ?>
                        <?php if ($score): ?>
                            <p class="text-green-600">Score: <?= htmlspecialchars($score['score']) ?></p>
                        <?php else: ?>
                            <p class="text-gray-600">No score available for this round.</p>
                        <?php endif; ?>
                    <?php elseif ($round['type'] === 'documenttype'): ?>
                        <?php
                        // Fetch document submission
                        $document_query = "SELECT filepath FROM document_type_submissions WHERE user_id = ? AND hackathon_id = ? AND round_id = ?";
                        $document_stmt = $conn->prepare($document_query);
                        $document_stmt->bind_param("iii", $user_id, $hackathon_id, $round['id']);
                        $document_stmt->execute();
                        $document_result = $document_stmt->get_result();
                        $document = $document_result->fetch_assoc();
                        
                        // Fetch document score
                        $doc_score_query = "SELECT score, feedback FROM document_submission_scores 
                                          WHERE user_id = ? AND hackathon_id = ? AND round_id = ?";
                        $doc_score_stmt = $conn->prepare($doc_score_query);
                        $doc_score_stmt->bind_param("iii", $user_id, $hackathon_id, $round['id']);
                        $doc_score_stmt->execute();
                        $doc_score_result = $doc_score_stmt->get_result();
                        $doc_score = $doc_score_result->fetch_assoc();
                        ?>
                        
                        <div class="mt-3">
                            <?php if ($document): ?>
                                <div class="flex items-center space-x-2 mb-4">
                                    <a href="<?= htmlspecialchars($document['filepath']) ?>" download class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Download Submission</a>
                                </div>
                                
                                <!-- Score Display Section -->
                                <?php if ($doc_score): ?>
                                    <div class="bg-green-50 border border-green-200 rounded p-4 mb-4">
                                        <p class="font-semibold text-green-700">Score: <?= htmlspecialchars($doc_score['score']) ?>/100</p>
                                        <?php if (!empty($doc_score['feedback'])): ?>
                                            <p class="text-gray-700 mt-2">Feedback: <?= nl2br(htmlspecialchars($doc_score['feedback'])) ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-gray-600 italic mb-4">This submission has not been scored yet.</p>
                                <?php endif; ?>
                                
                                <!-- Organizer Scoring Form -->
                                <?php if ($is_organizer): ?>
                                    <div class="mt-4 border-t pt-4">
                                        <h3 class="text-lg font-medium mb-3">Evaluate Submission</h3>
                                        <form method="POST" action="">
                                            <input type="hidden" name="round_id" value="<?= $round['id'] ?>">
                                            
                                            <div class="mb-4">
                                                <label for="score" class="block text-sm font-medium text-gray-700 mb-1">Score (0-100):</label>
                                                <input type="number" id="score" name="score" min="0" max="100" step="0.01" 
                                                       value="<?= $doc_score ? htmlspecialchars($doc_score['score']) : '' ?>"
                                                       class="w-full p-2 border rounded" required>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label for="feedback" class="block text-sm font-medium text-gray-700 mb-1">Feedback (Optional):</label>
                                                <textarea id="feedback" name="feedback" rows="3" class="w-full p-2 border rounded"><?= $doc_score ? htmlspecialchars($doc_score['feedback']) : '' ?></textarea>
                                            </div>
                                            
                                            <button type="submit" name="submit_score" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                                                <?= $doc_score ? 'Update Score' : 'Submit Score' ?>
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-gray-600">No document submitted for this round.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-700">No rounds found for this hackathon.</p>
        <?php endif; ?>

        <?php
        $stmt->close();
        if (isset($score_stmt)) $score_stmt->close();
        if (isset($document_stmt)) $document_stmt->close();
        if (isset($doc_score_stmt)) $doc_score_stmt->close();
        if (isset($check_stmt)) $check_stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
        if (isset($insert_stmt)) $insert_stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>