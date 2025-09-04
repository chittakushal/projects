<?php
session_start();
include 'db.php';

if (!isset($_GET['hackathon_id'])) {
    die("Hackathon ID is required.");
}

$hackathon_id = $_GET['hackathon_id'];

// Fetch hackathon details
$query = "SELECT * FROM hackathons WHERE hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hackathon_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Hackathon not found.");
}

$hackathon = $result->fetch_assoc();

// Fetch winners for the hackathon
$query = "SELECT users.name AS participant_name, users.email AS participant_email
          FROM winners
          JOIN users ON winners.user_id = users.user_id
          WHERE winners.hackathon_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hackathon_id);
$stmt->execute();
$winners_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Winners of <?= htmlspecialchars($hackathon['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-orange-600 mb-6">Winners of <?= htmlspecialchars($hackathon['name']) ?></h1>

        <?php if ($winners_result->num_rows > 0) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($winner = $winners_result->fetch_assoc()) : ?>
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($winner['participant_name']) ?></h2>
                        <p class="text-gray-600"><?= htmlspecialchars($winner['participant_email']) ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="text-gray-700 text-center">No participants or winners declared yet.</p>
        <?php endif; ?>

        <?php
        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
