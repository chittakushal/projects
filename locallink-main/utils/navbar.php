<?php
 if (isset($_SESSION["user_id"], $_SESSION["username"], $_SESSION["role"])) {
switch ($_SESSION["role"]) {
case 'normal':
$dashboard_link = "/local/normal/normal_dashboard.php";
break;
case 'admin':
$dashboard_link = "/local/admin/admin_dashboard.php";
break;
case 'organization':
$dashboard_link = "/local/organization/organization_dashboard.php";
break;
case 'business':
$dashboard_link = "/local/business/business_dashboard.php";
break;
default:
$dashboard_link = "/local/index.php";
break;
}
$dashboard_text = "Dashboard";
$logout_link = "/local/utils/logout.php";  
$logout_text = "Logout";
} else {
$dashboard_link = "/local/utils/register.phpregister.php";  
$dashboard_text = "Register";
$logout_link = "/local/utils/login.php"; 
$logout_text = "Login";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
 
<style>
.navbar {
border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Bottom border */
padding-top: 0.5rem;
padding-bottom: 0.5rem;
}

.navbar-brand {
font-size: 1.8rem;  
font-weight: bold;
color: #333; 
}

.navbar-toggler {
border: none; 
}

.navbar-nav .nav-item {
margin-left: 15px;  
}

.navbar-nav .nav-link {
color: #333;  
font-weight: 500;
transition: color 0.3s ease;
}

.navbar-nav .nav-link:hover {
color: #007bff;  
}

.navbar-collapse {
justify-content: space-between;  
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container">
<a class="navbar-brand" href="/local/index.php">LocalLink</a>
<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav ml-auto">
<li class="nav-item">
<a class="nav-link" href="<?php echo $dashboard_link; ?>"><?php echo $dashboard_text; ?></a>
</li>
<li class="nav-item">
<a class="nav-link" href="<?php echo $logout_link; ?>"><?php echo $logout_text; ?></a>
</li>
</ul>
</div>
</div>
</nav>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 </body>
</html>

