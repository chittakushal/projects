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
    $statement = isset($_POST['statement']) ? $_POST['statement'] : '';

    if ($statement) {
        // Insert statement into document_type table
        $query = "INSERT INTO document_type (round_id, hackathon_id, statement) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iis", $round_id, $hackathon_id, $statement);

        if ($stmt->execute()) {
            echo "<script>alert('Statement added successfully!'); window.location.href = 'manage_rounds.php?hackathon_id=" . $hackathon_id . "';</script>";
        } else {
            echo "<script>alert('Error adding statement'); window.location.href = 'add_statement.php?round_id=" . $round_id . "&hackathon_id=" . $hackathon_id . "';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Statement cannot be empty.');</script>";
    }
}

// Delete statement functionality
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_query = "DELETE FROM document_type WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('Statement deleted successfully!'); window.location.href = 'add_statement.php?round_id=" . $round_id . "&hackathon_id=" . $hackathon_id . "';</script>";
    } else {
        echo "<script>alert('Error deleting statement'); window.location.href = 'add_statement.php?round_id=" . $round_id . "&hackathon_id=" . $hackathon_id . "';</script>";
    }
    $delete_stmt->close();
}

// Fetch existing statements
$query = "SELECT id, statement FROM document_type WHERE round_id = ? AND hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $round_id, $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();
$statements = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Statement</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-orange-600 text-center mb-6">Add Statement to Round</h1>
        <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
            <form method="POST">
                <div class="mb-4">
                    <label for="statement" class="block text-lg font-medium text-gray-700">Statement</label>
                    <textarea id="statement" name="statement" rows="4" class="w-full p-3 border border-gray-300 rounded-md" required></textarea>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="bg-orange-600 text-white px-6 py-3 rounded-md shadow-md hover:bg-orange-700">Add Statement</button>
                </div>
            </form>
        </div>

        <!-- Display previous statements -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold text-orange-600 mb-4">Previous Statements</h2>
            <?php if (count($statements) > 0): ?>
                <div class="bg-white shadow-md rounded-lg p-6">
                    <ul>
                        <?php foreach ($statements as $statement): ?>
                            <li class="flex justify-between items-center mb-4">
                                <span class="text-lg"><?php echo htmlspecialchars($statement['statement']); ?></span>
                                <a href="add_statement.php?hackathon_id=<?php echo $hackathon_id; ?>&round_id=<?php echo $round_id; ?>&delete_id=<?php echo $statement['id']; ?>" class="text-red-600 hover:text-red-800">Delete</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <p>No statements added yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
