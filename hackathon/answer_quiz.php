<?php
session_start();
require_once 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Validate required parameters
if (!isset($_GET['hackathon_id']) || !isset($_GET['round_id']) || !isset($_GET['user_id'])) {
    die("Required parameters are missing.");
}

$hackathon_id = (int)$_GET['hackathon_id'];
$round_id = (int)$_GET['round_id'];
$user_id = (int)$_GET['user_id'];

// Verify user
if ($user_id !== (int)$_SESSION['user_id']) {
    die("Unauthorized access.");
}

$message = '';

// Check if the user has already taken the quiz
$stmt = $conn->prepare("SELECT score FROM quiz_type_score WHERE user_id = ? AND hackathon_id = ? AND round_id = ?");
$stmt->bind_param("iii", $user_id, $hackathon_id, $round_id);
$stmt->execute();
$score_result = $stmt->get_result();
$quiz_taken = $score_result->num_rows > 0;

// Fetch questions
$stmt = $conn->prepare("SELECT * FROM quiz_type WHERE round_id = ? AND hackathon_id = ? ORDER BY id");
$stmt->bind_param("ii", $round_id, $hackathon_id);
$stmt->execute();
$questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$total_questions = count($questions);

if ($quiz_taken) {
    $score_data = $score_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - E-Hackathon</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Quiz Round</h1>

        <!-- Timer -->
        <div class="text-xl font-semibold bg-red-100 text-red-700 p-3 rounded-lg mb-4">
            Time Left: <span id="timer">05:00</span>
        </div>

        <?php if ($quiz_taken): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
                Your score: <?= $score_data['score'] ?> out of <?= $total_questions ?>
                (<?= round(($score_data['score'] / $total_questions) * 100) ?>%)
            </div>
        <?php else: ?>
            <form method="POST" id="quizForm">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-container bg-white rounded-lg shadow-lg p-6" id="question-<?= $index ?>" style="<?= $index == 0 ? '' : 'display: none;' ?>">
                        <h3 class="text-lg font-medium mb-4">
                            Question <?= $index + 1 ?>: <?= htmlspecialchars($question['question']) ?>
                        </h3>
                        <div class="space-y-3">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <label class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                                    <input type="radio" 
                                           name="answers[<?= $question['id'] ?>]" 
                                           value="<?= htmlspecialchars($question["op$i"]) ?>" 
                                           class="mr-3">
                                    <span><?= htmlspecialchars($question["op$i"]) ?></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="mt-6 flex justify-between">
                    <button type="button" 
                            id="prevBtn"
                            class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition-colors"
                            style="display: none;">
                        Previous
                    </button>

                    <button type="button" 
                            id="nextBtn"
                            class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                        Next
                    </button>

                    <button type="submit" 
                            id="submitBtn"
                            class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors"
                            style="display: none;">
                        Submit
                    </button>
                </div>
            </form>

            <script>
            let currentQuestion = 0;
            const totalQuestions = <?= $total_questions ?>;
            const questions = document.querySelectorAll(".question-container");
            const prevBtn = document.getElementById("prevBtn");
            const nextBtn = document.getElementById("nextBtn");
            const submitBtn = document.getElementById("submitBtn");
            const timerElement = document.getElementById("timer");

            function showQuestion(index) {
                questions.forEach((q, i) => {
                    q.style.display = i === index ? "block" : "none";
                });

                prevBtn.style.display = index === 0 ? "none" : "block";
                nextBtn.style.display = index === totalQuestions - 1 ? "none" : "block";
                submitBtn.style.display = index === totalQuestions - 1 ? "block" : "none";
            }

            prevBtn.addEventListener("click", () => {
                if (currentQuestion > 0) {
                    currentQuestion--;
                    showQuestion(currentQuestion);
                }
            });

            nextBtn.addEventListener("click", () => {
                if (currentQuestion < totalQuestions - 1) {
                    currentQuestion++;
                    showQuestion(currentQuestion);
                }
            });

            showQuestion(currentQuestion);

            // Timer Logic
            let timeLeft = 300; // 5 minutes in seconds
            function updateTimer() {
                let minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                timerElement.textContent = 
                    (minutes < 10 ? "0" : "") + minutes + ":" + 
                    (seconds < 10 ? "0" : "") + seconds;
                timeLeft--;

                if (timeLeft < 0) {
                    clearInterval(timerInterval);
                    autoSubmit();
                }
            }

            const timerInterval = setInterval(updateTimer, 1000);

            function autoSubmit() {
                let allInputs = document.querySelectorAll('input[type="radio"]');
                let answeredQuestions = new Set();

                // Mark unanswered questions as wrong
                allInputs.forEach(input => {
                    if (input.checked) {
                        answeredQuestions.add(input.name);
                    }
                });

                questions.forEach(q => {
                    let questionId = q.querySelector('input[type="radio"]')?.name;
                    if (questionId && !answeredQuestions.has(questionId)) {
                        let hiddenInput = document.createElement("input");
                        hiddenInput.type = "hidden";
                        hiddenInput.name = questionId;
                        hiddenInput.value = "wrong"; // Mark unanswered as wrong
                        document.getElementById("quizForm").appendChild(hiddenInput);
                    }
                });

                document.getElementById("quizForm").submit();
            }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
