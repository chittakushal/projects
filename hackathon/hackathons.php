<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle registration if form is submitted via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_hackathon_id'])) {
    $hackathon_id = $_POST['register_hackathon_id'];

    // Check if the user is already registered for the hackathon
    $check_query = "SELECT * FROM participants WHERE user_id = ? AND hackathon_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $user_id, $hackathon_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo '<div class="bg-red-500 text-white p-4 rounded">You are already registered for this hackathon.</div>';
    } else {
        // Register the user for the hackathon
        $register_query = "INSERT INTO participants (user_id, hackathon_id) VALUES (?, ?)";
        $register_stmt = $conn->prepare($register_query);
        $register_stmt->bind_param("ii", $user_id, $hackathon_id);
        $register_stmt->execute();

        echo '<div class="bg-green-500 text-white p-4 rounded">You have successfully registered for the hackathon!</div>';
    }

    exit(); // Stop further processing after registration attempt
}

// Fetch hackathons that the user is not yet registered for
$query = "
    SELECT h.hackathon_id, h.name, h.description, h.file_path, h.start_date, h.end_date
    FROM hackathons h
    LEFT JOIN participants p ON h.hackathon_id = p.hackathon_id AND p.user_id = ?
    WHERE p.user_id IS NULL
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
    <title>Hackathons</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        <h1 class="text-3xl font-bold text-orange-600 mb-8">Upcoming Hackathons</h1>

        <div id="message"></div> <!-- To display messages -->

        <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                        
                        <!-- Register Button -->
                        <button class="register-btn text-white bg-orange-500 hover:bg-orange-600 mt-4 px-4 py-2 rounded-full" data-hackathon-id="<?= $hackathon['hackathon_id'] ?>">Register</button>

                        <!-- Learn More Button -->
                        <a href="hackathon.php?hackathon_id=<?= $hackathon['hackathon_id'] ?>" class="text-orange-500 hover:text-orange-600 mt-4 inline-block ml-4">Learn More</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle the registration process with AJAX
            $('.register-btn').click(function() {
                var hackathon_id = $(this).data('hackathon-id');
                
                $.ajax({
                    url: '', // Same page (hackathons.php)
                    method: 'POST',
                    data: {
                        register_hackathon_id: hackathon_id
                    },
                    success: function(response) {
                        $('#message').html(response); // Display message based on response
                    }
                });
            });
        });
    </script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
