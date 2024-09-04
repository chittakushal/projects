<?php
    $servername="localhost";
    $username="root";
    $password="";
    $database="21711A0574";

    $con=mysqli_connect($servername,$username,$password,$database);

    if(!$con)
    {
        die("error is".mysqli_error($con));
    }
?>