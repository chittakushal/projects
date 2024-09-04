<?php
    include 'database.php';
    $sql="SELECT balance FROM balance";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $bal = $row['balance'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Balance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: aqua;
            color: black;
            padding: 20px;
            text-align: center;
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
            margin-top: 60px;
            font-size: 20px;
            text-align: center;
        }

        .balance-container {
            width: 50%;
            margin: 0 auto;
        }

        p {
            margin-top: 20px;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <header>
        <!-- <h1>Banking - Check Balance</h1> -->
        <a href="bank.php" > <h1>Banking - Check Balance</h1></a>
    </header>

    <div class="container">
        <div class="balance-container">
            <h2>Your Current Balance</h2>
            <p><?php echo $bal ."/-"?></p>
            
        </div>
    </div>

    <footer>
        <p>Copyrights &copy; reserved</p>
    </footer>


    
</body>
</html>
