<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Money</title>
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

        .form-container {
            width: 50%;
            margin: 0 auto;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <!-- <h1>Banking - Send Money</h1> -->
        <a href="bank.php" ><h1>Banking - Send Money</h1></a>
    </header>

    <div class="container">
        <div class="form-container">
            <form id="payment-form" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'])?>">
                <label for="recipient">Recipient:</label><br>
                <input type="text" id="recipient" name="recipient" required><br>
                <label for="amount">Amount:</label><br>
                <input type="number" id="amount" name="amount" required><br>
                <button type="submit">Send Money</button>
            </form>
        </div>
    </div>

    <footer>
        <p>Copyrights &copy; reserved</p>
    </footer>
</body>
</html>
<?php
require "database.php";
if($_SERVER['REQUEST_METHOD']=="POST")
    {
            $name = $_REQUEST['recipient'];
            $amount=$_POST['amount'];
            $sql = "Select balance from balance";
            $result = mysqli_query($con,$sql);
            $row = mysqli_fetch_assoc($result);
            $balance=$row['balance'];
            if($balance>=$amount)
            {
                $balance-=$amount;
                $sql="insert into stu(Recipient,Amount,Balance) values('$name','$amount','$balance')";
                $result = mysqli_query($con,$sql);
                if($result)echo "<script>alert('Transaction Successful')</script>";
                $sql = "update balance set balance = $balance,date = current_timestamp()";
                mysqli_query($con,$sql);
            }
            else
            {
                echo "<script>alert('Insufficient balance')</script>";
            }
            mysqli_close($con);
    }
    ?>
