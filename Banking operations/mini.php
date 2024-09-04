<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Statement</title>
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

        .statement-container {
            width: 80%;
            margin: 0 auto;
            text-align: left;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<?php
    include 'database.php';
    $sql="SELECT balance FROM balance";
    $result = mysqli_query($con,$sql);
    $row = mysqli_fetch_assoc($result);
    $bal = $row['balance'];
?>

<body>
    <header>
       
        <a href="bank.php" >  <h1>Banking - Mini Statement</h1></a>
        
    </header>

    <div class="container">
        <div class="statement-container">
            <h2>Mini Statement</h2>
            <h4>Available balance : <?php echo $bal;?></h4>
            <?php
                include 'database.php';

                $sql = "SELECT * FROM stu";
                $result = mysqli_query($con,$sql);
                

                if (mysqli_num_rows($result)>0) {
                echo "<table>
                        <thead>
                        <tr>
                            <th>Sno
                            <th>Date</th>
                            <th>Recipient</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Balance</th>
                        </tr>
                        </thead><tbody>";
                    $sno = 1;
                while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . $sno . "</td>
                        <td>" . $row["Date"] . "</td>
                        <td>" . $row["Recipient"] . "</td>
                        <td>" . "Credit" . "</td>
                        <td>" . $row["Amount"] . "</td>
                        <td>" . $row["balance"] . "</td>
                    </tr>";
                    $sno++;
                }
                echo "</tbody></table>";
                } 
                else
                {
                    echo "NO DATA FOUND";
                }
            ?>
        </div>
    </div>

    <footer>
        <p>Copyrights &copy; reserved</p>
    </footer>
</body>
</html>