<?php
session_start();

$ADMIN_USER = "susom";
$ADMIN_PASS = "susom123";

if(isset($_POST['login']))
{
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if(
        $username === $ADMIN_USER &&
        $password === $ADMIN_PASS
    )
    {
        $_SESSION['admin'] = true;

        header("Location: manage_cards.php");
        exit();
    }

    $error = "Invalid Username or Password";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>RFID Admin Login</title>

<style>

:root{
--bg:#07090f;
--surface:#0c1018;
--surface2:#111928;
--cyan:#00cfff;
--cyan-border:rgba(0,207,255,.18);
--text:#b8cce0;
--bright:#e4f0ff;
}

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Space Grotesk',sans-serif;
}

body{
background:#07090f;
color:var(--text);
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}

.login-box{
width:420px;
background:var(--surface);
border:1px solid var(--cyan-border);
border-radius:14px;
padding:30px;
}

.title{
color:var(--bright);
text-align:center;
margin-bottom:10px;
}

.subtitle{
text-align:center;
margin-bottom:25px;
color:#7f9ab0;
}

input{
width:100%;
padding:12px;
background:var(--surface2);
border:1px solid var(--cyan-border);
border-radius:8px;
color:white;
margin-bottom:15px;
}

button{
width:100%;
padding:12px;
background:var(--cyan);
border:none;
border-radius:8px;
font-weight:bold;
cursor:pointer;
}

.error{
background:#ff336620;
padding:12px;
border-radius:8px;
margin-top:10px;
color:#ff3366;
}

.back-btn{
display:block;
text-align:center;
margin-top:15px;
color:var(--cyan);
text-decoration:none;
}
</style>

</head>

<body>

<div class="login-box">

<div class="logo">
🔐
</div>

<div class="title">
RFID Admin Login
</div>

<div class="subtitle">
Access Card Management System
</div>

<form method="POST">

<div class="input-group">
<input
type="text"
name="username"
placeholder="Username"
required>
</div>

<div class="input-group">
<input
type="password"
name="password"
placeholder="Password"
required>
</div>

<button
type="submit"
name="login">
Login
</button>

</form>

<?php
if(isset($error))
{
    echo "<div class='error'>$error</div>";
}
?>

<a href="index.php" class="back-btn">
← Back to Dashboard
</a>

</div>

</body>
</html>