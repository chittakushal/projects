<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("location: ../utils/login.php");
    exit;
}

require_once('../utils/config.php');

if (isset($_GET['news_id'])) {
    $news_id = $_GET['news_id'];

    $sql = "SELECT news_title, news_description, cover_image, category FROM news WHERE news_id = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $param_news_id);
        $param_news_id = $news_id;

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($news_title, $news_description, $cover_image, $category);
                $stmt->fetch();

                function convertCustomTags($content) {
                    $pattern = '/:([h1-6]{1}):([^:]+):\\1:/';
                    $replacement = '<$1>$2</$1>';
                    return preg_replace($pattern, $replacement, $content);
                }

                $processed_description = convertCustomTags($news_description);

                // Function to add new paragraphs after every period
                function addParagraphs($content) {
                    $sentences = explode('.', $content);
                    $paragraphs = array_map('trim', $sentences);
                    return '<p>' . implode('.</p><p>', $paragraphs) . '.</p>';
                }

                $processed_description = addParagraphs($processed_description);
            } else {
                header("location: news_feed.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        $stmt->close();
    }
} else {
    header("location: news_feed.php");
    exit();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news_title); ?></title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #FFFAB7;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #FFFFFF;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #667BC6;
            margin-bottom: 20px;
            font-size:15px;
            text-align:left
        }

        .news-image {
            margin-right: 20px;
            max-width: 500px;
            height: 300px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 30px;
        }

        .news-description {
            font-size: 16px;
            line-height: 1.8;
            color: #666;
            overflow: hidden;
        }

        .category {
            font-weight: bold;
            color: #007BFF;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            background-color: #FFB4C2;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .back-link:hover {
            background-color: #DA7297;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include('../utils/navbar.php'); ?>

    <div class="wrapper">
        <h1><?php echo htmlspecialchars($news_title); ?></h1>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($cover_image); ?>" alt="Cover Image" class="news-image">
        <div class="news-content">
            <div class="news-description"><?php echo $processed_description; ?></div>
            <p><strong>Category:</strong> <span class="category"><?php echo htmlspecialchars($category); ?></span></p>
        </div>
        <div style="clear: both;"></div>
        <a href="news_feed.php" class="back-link">Back to News Feed</a>
    </div>
</body>
</html>
