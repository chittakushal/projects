<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch hackathons the user is participating in
$query = "
    SELECT h.hackathon_id, h.name, h.description, h.file_path, h.start_date, h.end_date
    FROM hackathons h
    JOIN participants p ON h.hackathon_id = p.hackathon_id
    WHERE p.user_id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$hackathons_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Hackathons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body class="bg-white text-gray-800">

    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-orange-600 mb-8">My Hackathons</h1>

        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($hackathons_result->num_rows > 0): ?>
                <?php while ($hackathon = $hackathons_result->fetch_assoc()): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden">
                        <img src="<?= htmlspecialchars($hackathon['file_path']) ?>" alt="<?= htmlspecialchars($hackathon['name']) ?>" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-orange-600"><?= htmlspecialchars($hackathon['name']) ?></h2>
                            <p class="text-gray-600 mt-2 ellipsis"><?= htmlspecialchars(substr($hackathon['description'], 0, 120)) . (strlen($hackathon['description']) > 120 ? '...' : '') ?></p>
                            
                            <div class="mt-4 text-gray-600">
                                <p><strong>Start Date:</strong> <?= date('F j, Y', strtotime($hackathon['start_date'])) ?></p>
                                <p><strong>End Date:</strong> <?= date('F j, Y', strtotime($hackathon['end_date'])) ?></p>
                            </div>
                            
                            <a href="hackathon.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" class="text-orange-500 hover:text-orange-600 mt-4 inline-block">Learn More</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-700">You are not currently registered for any hackathons.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
