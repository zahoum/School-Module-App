<?php
require 'config.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: page2.php");
            exit();
        } else {
            $error = "Incorrected Password";
        }
    } else {
        $error = "User Name Is Not Defind";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LogIn Page</title>
    <style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
    font-size: 28px;
}

form {
    background-color: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 400px;
}

.error-message {
    background-color: #fee;
    color: #c33;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 20px;
    text-align: center;
    border: 1px solid #fcc;
}

.form-group {
    margin-bottom: 25px;
}

label {
    display: block;
    margin-bottom: 8px;
    color: #555;
    font-weight: 600;
}

input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    transition: border-color 0.3s;
}

input[type="text"]:focus,
input[type="password"]:focus {
    outline: none;
    border-color: #667eea;
}

button[type="submit"] {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

button[type="submit"]:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

button[type="submit"]:active {
    transform: translateY(0);
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

@media (max-width: 480px) {
    form {
        padding: 30px 20px;
        margin: 20px;
    }
    
    h2 {
        font-size: 24px;
    }
}
    </style>
</head>
<body>
    
    
    <?php if (isset($error)) { echo "<p style='color:red'>$error</p>"; } ?>
    
    <form method="POST">
        UserName <input type="text" name="username" required><br><br>
        Passwod <input type="password" name="password" required><br><br>
        <button type="submit" name="login">Log In</button>
    </form>
</body>
</html>