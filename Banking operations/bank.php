<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banking Dashboard</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        scroll-behavior: smooth;
        /* Enable smooth scrolling */
        background-color: #f2f2f2;
        color: #333;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    header {
        background-color:aqua;
        color: white;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    footer {
        background-color: #005D9A;
        color: white;
        padding: 10px;
        text-align: center;
        box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        margin-top: auto;
    }

    .container {
        padding: 20px;
        text-align: center;
        flex: 1;
    }

    .section {
        margin-top: 40px;
        padding: 30px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .section h2 {
        margin-top: 0;
        color: #005D9A;
    }

    .section a {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        font-size: 18px;
        color: white;
        background-color: #005D9A;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .section a:hover {
        background-color: #004E83;
    }

    #lo {
        display: flex;
        position: absolute;
        right: 20px;
        top: 50px;
        cursor: pointer;
        border: none;
        padding: 10px 15px 10px 15px;
        font-weight: bold;
        border-radius: 3px;
        background-color: #000000;
        color: #ffffff;
    }
    </style>
</head>

<body>
    <header>
        <a href="bank.php">
            <h1>Banking</h1>
        </a>
        <a href="logout.php"> <button id="lo">Logout</button></a>
    </header>
    <section>

        <div class="container">
            <div class="section" id="send-money">
                <h2>Send Money</h2>
                <p>Easily transfer money to anyone.</p>
                <a href="send.php">Go to Send Money</a>
            </div>

            <div class="section" id="mini-statement">
                <h2>Mini Statement</h2>
                <p>View your recent transactions.</p>
                <a href="mini.php">Go to Mini Statement</a>
            </div>

            <div class="section" id="check-balance">
                <h2>Check Balance</h2>
                <p>Check your account balance.</p>
                <a href="balence.php">Go to Check Balance</a>
            </div>
        </div>
    </section>
    <footer>
        <p>Copyrights &copy; reserved</p>
    </footer>
</body>

</html>