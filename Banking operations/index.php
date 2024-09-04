<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My website</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color:aqua;
            color: black;
            /* text-decoration: underline; */
            padding: 20px;
            text-align: center;
        }
        
        header a{
            text-decoration: none;
            color: white;
        }

        footer {
            background-color: aqua;
            color: black;
            padding: 10px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .container {
            padding: 20px;
            font-size: 20px;
            margin-top: 60px;
            font-size: 20px;
        }
        
    </style>
</head>
<body>
    <header>
        <a href="#" ><h1>Banking</a>
    </header>

    <div class="container">
        <center>
        
        <!-- <form  action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <table  cellspacing="7" width="25%">
                <tr>
                    <td><label for="uname">Account no:</label></td>
                    <td><input type="text" id="uname"></td>
                </tr>
                <tr>
                    <td><label for="password">Password:</label></td>
                    <td><input type="password" id="password"></td>
                </tr>
                
            </table>
            <input type="submit" value="Login">
        </form> -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="name">Username:</label><br>
        <input type="text" id="name" name="name"><br><br>
        <label for="pwd">Password:</label><br>
        <input type="password" id="pwd" name="pwd"><br><br>
        <input type="submit" value="Login">
        </form>
    
        
    </center>
    </div>

    <footer>
        <p>Copyrights &copy; reserved</p>
    </footer>



    

    <?php
    // if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //     $name = $_POST["uname"];
    //     $pwd = $_POST["password"];
	//     if( $name == '' || $pwd == ''){
	//         echo "Name and passsword should not be null";
	//     } else if ($name === "user123" && $pwd === "admin") {
    //         header("Location: bank.php");
    //         exit;
    //     } else {
    //         echo "Invalid username or password. Please try again.";
    //     }
    // }
    session_start();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $pwd = $_POST["pwd"];
	    if( $name == '' || $pwd == ''){
	        echo '<script>alert("Name and passsword should not be null")</script>';
	    } else if ($name === "kushal" && $pwd === "admin") {
            header("Location: bank.php");
            exit;
        } else {
            echo '<script>alert("Invalid username or password. Please try again.")</script>';
        }
    }
    ?>
</body>
</html>