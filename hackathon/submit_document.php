<?php
session_start();
include 'db.php';

if (!isset($_GET['hackathon_id'], $_GET['round_id'], $_GET['user_id'])) {
    die("Required parameters are missing.");
}

$hackathon_id = $_GET['hackathon_id'];
$round_id = $_GET['round_id'];
$user_id = $_GET['user_id'];

// Check if the user has already uploaded the document
$query = "SELECT * FROM document_type_submissions WHERE user_id = ? AND hackathon_id = ? AND round_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $hackathon_id, $round_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    $document = $result->fetch_assoc();
    $uploadedFilePath = $document['filepath'];
    $message = "You have already submitted a document. <a href='$uploadedFilePath' class='text-blue-600'>View your submission</a>.";
} else {
    // Process the document upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['document'];
            $uploadDir = 'uploads/';
            $fileName = time() . '_' . basename($file['name']);
            $targetFilePath = $uploadDir . $fileName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                // Start a transaction to ensure atomicity
                $conn->begin_transaction();

                try {
                    // Insert the document submission
                    $stmt = $conn->prepare("
                        INSERT INTO document_type_submissions (user_id, hackathon_id, round_id, filepath)
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE filepath = VALUES(filepath)
                    ");
                    $stmt->bind_param("iiis", $user_id, $hackathon_id, $round_id, $targetFilePath);
                    $stmt->execute();
                    $stmt->close();

                    // Update the participant's current round to the next round
                    $update_round_query = "UPDATE participants SET current_round = current_round + 1 WHERE user_id = ? AND hackathon_id = ?";
                    $update_round_stmt = $conn->prepare($update_round_query);
                    $update_round_stmt->bind_param("ii", $user_id, $hackathon_id);
                    $update_round_stmt->execute();
                    $update_round_stmt->close();

                    // Commit the transaction
                    $conn->commit();

                    $successMessage = "Document uploaded successfully!";
                } catch (Exception $e) {
                    // Rollback the transaction in case of an error
                    $conn->rollback();
                    $errorMessage = "An error occurred: " . $e->getMessage();
                }
            } else {
                $errorMessage = "Failed to upload the file.";
            }
        } else {
            $errorMessage = "No file uploaded or an error occurred.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen">
    <?php include 'navbar.php'; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-md mx-auto">
            <!-- Card Container -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
                    <h1 class="text-xl font-semibold text-gray-900">Submit Document</h1>
                </div>

                <!-- Content -->
                <div class="px-6 py-4">
                    <?php if (isset($message)): ?>
                        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex">
                                <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                                <p class="text-yellow-700"><?= $message ?></p>
                            </div>
                        </div>
                    <?php elseif (isset($successMessage)): ?>
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex">
                                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                                <p class="text-green-700"><?= $successMessage ?></p>
                            </div>
                        </div>
                    <?php elseif (isset($errorMessage)): ?>
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                                <p class="text-red-700"><?= $errorMessage ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!isset($uploadedFilePath)): ?>
                        <form method="post" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload Document
                                </label>
                                <div class="relative">
                                    <input type="file" 
                                           name="document" 
                                           id="document" 
                                           required 
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-indigo-50 file:text-indigo-700
                                                  hover:file:bg-indigo-100
                                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Accepted file formats: PDF, DOC, DOCX</p>
                            </div>

                            <button type="submit" 
                                    class="w-full flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 
                                           text-white text-sm font-medium rounded-lg shadow-sm 
                                           transition-colors duration-200">
                                <i class="fas fa-cloud-upload-alt mr-2"></i>
                                Upload Document
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
                    <a href="hackathon.php?hackathon_id=<?= $hackathon_id ?>" 
                       class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Hackathon
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
