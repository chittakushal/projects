<?php
session_start();
if (!isset($_SESSION['org_id'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $description = $_POST['description'] ?? '';
    $content = $_POST['content'] ?? '';
    $image = $_FILES['image'] ?? null;
    $org_id = $_SESSION['org_id'];
    $errors = [];

    if (empty($name)) $errors[] = "Hackathon name is required.";
    if (empty($type)) $errors[] = "Hackathon type is required.";
    if ($type === 'offline' && empty($venue)) $errors[] = "Venue is required for offline hackathons.";
    if (empty($start_date) || empty($end_date)) $errors[] = "Start and end dates are required.";
    elseif ($start_date > $end_date) $errors[] = "Start date cannot be after end date.";
    if (empty($description)) $errors[] = "Description is required.";
    if (empty($content)) $errors[] = "Content is required.";

    $filePath = '';
    if ($image && $image['error'] === 0) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $filePath = $targetDir . uniqid() . '-' . basename($image['name']);
        $imageFileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }

        if (empty($errors) && !move_uploaded_file($image['tmp_name'], $filePath)) {
            $errors[] = "Failed to upload the image.";
        }
    } else {
        $errors[] = "Image is required.";
    }

    if (empty($errors)) {
        include 'db.php';

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        $stmt = $conn->prepare("INSERT INTO hackathons (name, type, venue, start_date, end_date, description, content, file_path,org_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)");
        $stmt->bind_param("ssssssssi", $name, $type, $venue, $start_date, $end_date, $description, $content, $filePath,$org_id);

        if ($stmt->execute()) {
            echo "<script>alert('Hackathon created successfully!'); window.location.href='organizer_dashboard.php';</script>";
        } else {
            $errors[] = "Failed to save hackathon. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Hackathon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
</head>
<body class="bg-orange-50 min-h-screen flex flex-col">
    <?php include 'navbar.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-orange-600 text-center mb-6">Create Hackathon</h1>
        <?php if (!empty($errors)) : ?>
            <div class="bg-red-100 text-red-800 p-4 mb-4 rounded">
                <ul>
                    <?php foreach ($errors as $error) : ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
            <div class="mb-4">
                <label for="name" class="block text-orange-700 font-semibold mb-2">Hackathon Name</label>
                <input type="text" name="name" id="name" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="mb-4">
                <label for="type" class="block text-orange-700 font-semibold mb-2">Type</label>
                <select name="type" id="type" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
                    <option value="">Select Type</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="venue" class="block text-orange-700 font-semibold mb-2">Venue</label>
                <input type="text" name="venue" id="venue" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="mb-4">
                <label for="start_date" class="block text-orange-700 font-semibold mb-2">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="mb-4">
                <label for="end_date" class="block text-orange-700 font-semibold mb-2">End Date</label>
                <input type="date" name="end_date" id="end_date" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-orange-700 font-semibold mb-2">Description</label>
                <textarea name="description" id="description" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400"></textarea>
            </div>

            <div class="mb-4">
                <label for="content" class="block text-orange-700 font-semibold mb-2">Content</label>
                <div id="editor" class="bg-white border border-orange-300 rounded-lg"></div>
                <input type="hidden" name="content" id="content">
            </div>

            <div class="mb-4">
                <label for="image" class="block text-orange-700 font-semibold mb-2">Upload Image</label>
                <input type="file" name="image" id="image" class="w-full border border-orange-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>

            <div class="text-center">
                <button type="submit" class="bg-orange-500 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-orange-600">Create Hackathon</button>
            </div>
        </form>
    </div>

    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'header': [1, 2, 3, false] }],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link', 'image'],
                    [{ 'color': [] }, { 'background': [] }],
                ]
            }
        });

        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('#content').value = quill.root.innerHTML;
        });
    </script>
</body>
</html>
