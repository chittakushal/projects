<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$org_id = $_SESSION['org_id'];
$hackathon_id = isset($_GET['hackathon_id']) ? $_GET['hackathon_id'] : null;

if (!$hackathon_id) {
    echo "<script>alert('Invalid hackathon ID'); window.location.href = 'manage_hackathons.php';</script>";
    exit();
}

$query = "SELECT * FROM rounds WHERE hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching rounds: " . $conn->error);
}

$rounds = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rounds</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'navbar.php'; ?>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-semibold text-gray-900">Manage Rounds</h1>
            <a href="add_round.php?hackathon_id=<?= $hackathon_id ?>" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors duration-200">
                <i class="fa fa-plus mr-2"></i>
                Add Round
            </a>
            
        </div>

        <?php if (count($rounds) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quiz Rounds Section -->
                <div class="space-y-6">
                    <h2 class="text-xl font-medium text-gray-900 mb-4">Quiz Rounds</h2>
                    <?php
                    $quizRounds = array_filter($rounds, fn($round) => $round['type'] === 'quiztype');
                    if (count($quizRounds) > 0): ?>
                        <?php foreach ($quizRounds as $round): ?>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($round['name']) ?></h3>
                                        <p class="text-sm text-gray-500 mt-1">Quiz Type Round</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="add_question.php?round_id=<?= $round['id'] ?>&hackathon_id=<?= $hackathon_id ?>" 
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-md transition-colors duration-200">
                                            Add Question
                                            <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                        </a>
                                        <a href="add_round_winner.php?round_id=<?= $round['id'] ?>&hackathon_id=<?= $hackathon_id ?>&type=quiz" 
                                           class="inline-flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium rounded-md transition-colors duration-200">
                                            Add Winner
                                            <i class="fas fa-trophy ml-2 text-xs"></i>
                                        </a>
                                        <a href="timer.php?round_id=<?= $round['id'] ?>&hackathon_id=<?= $hackathon_id ?>" 
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm font-medium rounded-md transition-colors duration-200">
                                        Set Timer
                                        <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                    </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                            <p class="text-sm text-gray-600">No quiz rounds available</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Document Rounds Section -->
                <div class="space-y-6">
                    <h2 class="text-xl font-medium text-gray-900 mb-4">Document Rounds</h2>
                    <?php
                    $docRounds = array_filter($rounds, fn($round) => $round['type'] === 'documenttype');
                    if (count($docRounds) > 0): ?>
                        <?php foreach ($docRounds as $round): ?>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($round['name']) ?></h3>
                                        <p class="text-sm text-gray-500 mt-1">Document Type Round</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="add_statement.php?round_id=<?= $round['id'] ?>&hackathon_id=<?= $hackathon_id ?>" 
                                           class="inline-flex items-center px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-medium rounded-md transition-colors duration-200">
                                            Add Statement
                                            <i class="fas fa-chevron-right ml-2 text-xs"></i>
                                        </a>
                                        <a href="add_round_winner.php?round_id=<?= $round['id'] ?>&hackathon_id=<?= $hackathon_id ?>&type=document" 
                                           class="inline-flex items-center px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-sm font-medium rounded-md transition-colors duration-200">
                                            Add Winner
                                            <i class="fas fa-trophy ml-2 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                            <p class="text-sm text-gray-600">No document rounds available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <div class="text-gray-500 mb-4">
                    <i class="fas fa-clipboard-list text-4xl"></i>
                </div>
                <h2 class="text-lg font-medium text-gray-900 mb-2">No Rounds Found</h2>
                <p class="text-sm text-gray-600">Get started by adding your first round</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>