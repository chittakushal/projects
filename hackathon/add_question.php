<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

include 'db.php';

$org_id = $_SESSION['org_id'];
$hackathon_id = isset($_GET['hackathon_id']) ? $_GET['hackathon_id'] : null;
$round_id = isset($_GET['round_id']) ? $_GET['round_id'] : null;

if (!$hackathon_id || !$round_id) {
    echo "<script>alert('Invalid hackathon or round ID'); window.location.href = 'manage_rounds.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and fetch form data
    $question = isset($_POST['question']) ? $_POST['question'] : '';
    $op1 = isset($_POST['op1']) ? $_POST['op1'] : '';
    $op2 = isset($_POST['op2']) ? $_POST['op2'] : '';
    $op3 = isset($_POST['op3']) ? $_POST['op3'] : '';
    $op4 = isset($_POST['op4']) ? $_POST['op4'] : '';
    $correct_answer = isset($_POST['correct_answer']) ? $_POST['correct_answer'] : '';

    if ($question && $op1 && $op2 && $op3 && $op4 && $correct_answer) {
        // Insert question into quiz_type table
        $query = "INSERT INTO quiz_type (round_id, hackathon_id, question, op1, op2, op3, op4, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissssss", $round_id, $hackathon_id, $question, $op1, $op2, $op3, $op4, $correct_answer);

        if ($stmt->execute()) {
            echo "<script>alert('Question added successfully!'); window.location.href = 'manage_rounds.php?hackathon_id=" . $hackathon_id . "';</script>";
        } else {
            echo "<script>alert('Error adding question'); window.location.href = 'add_question.php?round_id=" . $round_id . "&hackathon_id=" . $hackathon_id . "';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('All fields are required.');</script>";
    }
}

// Delete question functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_query = "DELETE FROM quiz_type WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Question deleted successfully!'); window.location.href = 'add_question.php?round_id=" . $round_id . "&hackathon_id=" . $hackathon_id . "';</script>";
    } else {
        echo "<script>alert('Error deleting question'); window.location.href = 'add_question.php?round_id=" . $round_id . "&hackathon_id=" . $hackathon_id . "';</script>";
    }
    $delete_stmt->close();
}

// Fetch existing questions
$query = "SELECT id, question FROM quiz_type WHERE round_id = ? AND hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $round_id, $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-orange-600 text-center mb-6">Add Question to Round</h1>
        <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
            <form method="POST">
                <div class="mb-4">
                    <label for="question" class="block text-lg font-medium text-gray-700">Question</label>
                    <textarea id="question" name="question" rows="4" class="w-full p-3 border border-gray-300 rounded-md" required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="op1" class="block text-lg font-medium text-gray-700">Option 1</label>
                        <input type="text" id="op1" name="op1" class="w-full p-3 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="op2" class="block text-lg font-medium text-gray-700">Option 2</label>
                        <input type="text" id="op2" name="op2" class="w-full p-3 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="op3" class="block text-lg font-medium text-gray-700">Option 3</label>
                        <input type="text" id="op3" name="op3" class="w-full p-3 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="op4" class="block text-lg font-medium text-gray-700">Option 4</label>
                        <input type="text" id="op4" name="op4" class="w-full p-3 border border-gray-300 rounded-md" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="correct_answer" class="block text-lg font-medium text-gray-700">Correct Answer</label>
                    <input type="text" id="correct_answer" name="correct_answer" class="w-full p-3 border border-gray-300 rounded-md" required>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="bg-orange-600 text-white px-6 py-3 rounded-md shadow-md hover:bg-orange-700">Add Question</button>
                </div>
            </form>
        </div>

        <!-- Display previous questions -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-orange-600 mb-4">Previous Questions</h2>
            <?php if (count($questions) > 0): ?>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <ul>
                        <?php foreach ($questions as $question): ?>
                            <li class="flex justify-between items-center mb-4">
                                <span class="text-lg"><?php echo htmlspecialchars($question['question']); ?></span>
                                <a href="add_question.php?hackathon_id=<?php echo $hackathon_id; ?>&round_id=<?php echo $round_id; ?>&delete_id=<?php echo $question['id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p>No questions added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
