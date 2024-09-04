<?php
session_start();
require_once('../utils/config.php');

$news_title = $news_description = $category = "";
$news_title_err = $news_description_err = $category_err = $cover_image_err = "";

if (isset($_GET['news_id'])) {
    $news_id = $_GET['news_id'];

    if (!isset($_SESSION["user_id"])) {
        header("location: ../utils/login.php");
        exit;
    }

    $sql = "SELECT news_title, news_description, category FROM news WHERE news_id = ? AND owner_id = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("ii", $param_news_id, $param_owner_id);

        $param_news_id = $news_id;
        $param_owner_id = $_SESSION["user_id"];

        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($news_title, $news_description, $category);
                $stmt->fetch();
            } else {
                header("location: error.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        $stmt->close();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty(trim($_POST["news_title"]))) {
            $news_title_err = "Please enter a news title.";
        } else {
            $news_title = trim($_POST["news_title"]);
        }

        if (empty(trim($_POST["news_description"]))) {
            $news_description_err = "Please enter a news description.";
        } else {
            $news_description = trim($_POST["news_description"]);
        }

        if (empty(trim($_POST["category"]))) {
            $category_err = "Please enter a category.";
        } else {
            $category = trim($_POST["category"]);
        }

        if (empty($news_title_err) && empty($news_description_err) && empty($category_err)) {
            $sql = "UPDATE news SET news_title = ?, news_description = ?, category = ? WHERE news_id = ? AND owner_id = ?";

            if ($stmt = $mysqli->prepare($sql)) {
                $stmt->bind_param("sssii", $param_title, $param_description, $param_category, $param_news_id, $param_owner_id);

                $param_title = $news_title;
                $param_description = $news_description;
                $param_category = $category;
                $param_news_id = $news_id;
                $param_owner_id = $_SESSION["user_id"];

                if ($stmt->execute()) {
                    if ($_FILES["cover_image"]["error"] == 0) {
                        $cover_image = file_get_contents($_FILES["cover_image"]["tmp_name"]);

                        $sql_cover = "UPDATE news SET cover_image = ? WHERE news_id = ? AND owner_id = ?";

                        if ($stmt_cover = $mysqli->prepare($sql_cover)) {
                            $stmt_cover->bind_param("bii", $param_image, $param_news_id, $param_owner_id);

                            $stmt_cover->send_long_data(0, $cover_image);
                            $param_news_id = $news_id;
                            $param_owner_id = $_SESSION["user_id"];

                            if ($stmt_cover->execute()) {
                                header("location: my_news.php");
                                exit();
                            } else {
                                echo "Something went wrong while updating the cover image. Please try again later.";
                            }

                            $stmt_cover->close();
                        }
                    } else {
                        header("location: my_news.php");
                        exit();
                    }
                } else {
                    echo "Something went wrong. Please try again later.";
                }

                $stmt->close();
            }
        }
    }

    $mysqli->close();
} else {
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update News</title>
<style>
    /* CSS using the new color palette */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0; /* Light gray background */
        color: #333; /* Dark text color */
        margin: 0;
        padding: 0;
    }

    .wrapper {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff; /* White background */
        border: 1px solid #ccc; /* Light gray border */
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Light shadow */
    }

    h2 {
        color: #DA7297; /* Dark pink title */
    }

    label {
        font-weight: bold;
        color: #333; /* Dark text color */
    }

    .form-control {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc; /* Light gray border */
        border-radius: 4px;
        box-sizing: border-box;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.has-error .form-control {
        border-color: #DA7297; /* Dark pink border color for error */
    }

    .help-block {
        color: #DA7297; /* Dark pink error message */
        font-size: 12px;
        margin-top: 5px;
    }

    .btn {
        display: inline-block;
        padding: 8px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        font-size: 14px;
    }

    .btn-primary {
        background-color: #667BC6; /* Blue button */
        color: #fff; /* White text */
    }

    .btn-primary:hover {
        background-color: #4A5A9F; /* Darker blue on hover */
    }

    .btn-secondary {
        background-color: #FFB4C2; /* Pink button */
        color: #333; /* Dark text */
        margin-left: 10px;
    }

    .btn-secondary:hover {
        background-color: #E396A9; /* Darker pink on hover */
    }
    input["file"]{
        padding:40px;
    }
    </style>
</head>
<body>
<?php include('../utils/navbar.php'); ?>

<div class="wrapper">
    <h2>Update News</h2>
    <p>Please edit the input values and submit to update the news article.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?news_id=' . $news_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group <?php echo (!empty($news_title_err)) ? 'has-error' : ''; ?>">
            <label>News Title</label>
            <input type="text" name="news_title" class="form-control" value="<?php echo htmlspecialchars($news_title); ?>">
            <span class="help-block"><?php echo $news_title_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($news_description_err)) ? 'has-error' : ''; ?>">
            <label>News Description</label>
            <textarea name="news_description" class="form-control"><?php echo htmlspecialchars($news_description); ?></textarea>
            <span class="help-block"><?php echo $news_description_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($category_err)) ? 'has-error' : ''; ?>">
            <label>Category</label>
            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>">
            <span class="help-block"><?php echo $category_err; ?></span>
        </div>
        <div class="form-group">
            <label>Cover Image</label>
            <input type="file" name="cover_image" class="form-control">
            <span class="help-block"><?php echo $cover_image_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="my_news.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>    
</body>
</html>
