<?php
session_start();

/* DESTROY SESSION */
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Logout</title>

<!-- AUTO REDIRECT AFTER 5 SECONDS -->
<meta http-equiv="refresh" content="5;url=login.php">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#141e30,#243b55);
}

.logout-box{
    background:#fff;
    padding:50px;
    border-radius:18px;
    text-align:center;
    box-shadow:0 5px 25px rgba(0,0,0,0.2);
    width:400px;
}

.logout-icon{
    font-size:70px;
    color:#dc3545;
    margin-bottom:20px;
}

h2{
    color:#1a1e2b;
    margin-bottom:15px;
}

p{
    color:#555;
    font-size:16px;
    margin-bottom:25px;
}

.loader{
    width:100%;
    height:8px;
    background:#eee;
    border-radius:10px;
    overflow:hidden;
}

.loader-bar{
    height:100%;
    width:100%;
    background:#dc3545;
    animation:load 5s linear forwards;
}

@keyframes load{

    from{
        width:100%;
    }

    to{
        width:0%;
    }

}

.login-btn{
    display:inline-block;
    margin-top:25px;
    padding:12px 22px;
    background:#0d6efd;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
    font-weight:600;
    transition:0.3s;
}

.login-btn:hover{
    background:#084298;
}

</style>

</head>

<body>

<div class="logout-box">

    <div class="logout-icon">
        ⏻
    </div>

    <h2>
        Logged Out Successfully
    </h2>

    <p>
        You will be redirected to login page in 5 seconds...
    </p>

    <div class="loader">
        <div class="loader-bar"></div>
    </div>

    <a href="login.php" class="login-btn">
        Login Again
    </a>

</div>

</body>
</html>