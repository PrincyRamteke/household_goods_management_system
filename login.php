<?php
    session_start();
    if($_SESSION && $_SESSION['loggedin'] && $_SESSION['loggedin'] == true) {
        header("location: home.php");
        exit;
    }
    $signup = true;
    $error = "";
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        include 'db_connect.php';
        if($_POST['cpassword']) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $cpassword = $_POST['cpassword'];
            if($password != $cpassword) {
                $err = "Please enter same password";
            } else {
                $passwordHash = password_hash($password.'e1@Q', PASSWORD_DEFAULT);
                $insert_sql = "INSERT INTO `users` (`userName`, `password`) VALUES ( '$username', '$passwordHash' )";
                // echo $insert_sql;
                if($connection->query($insert_sql) == true) {
                    $error = "";
                    session_start();
                    $_SESSION['loggedin'] = true;
                    $_SESSION['userName'] = $username;
                    header("location: index.php");
                } else {
                    $error = $connection->error;
                }
            }
        } else {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $select_sql = "SELECT * FROM `users` WHERE `userName`='$username';";
            $result = $connection->query($select_sql);
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    if(password_verify($password.'e1@Q', $row['password'])) {
                        session_start();
                        $_SESSION['loggedin'] = true;
                        $_SESSION['userName'] = $username;
                        header("location: index.php");
                    } else {
                        $error = "Invalid credentials";
                    }
                }
            } else {
                $error = "Invalid credentials";
            }
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="login.css">
  <title>Login</title>
</head>

<body>
  <h3>Login or Sign up</h3>
  <?php
        echo "<h5>$error</h5>";
    ?>
  <form method="post">
    <input type="text" name="username" id="username" placeholder="Enter your username" />
    <input type="password" name="password" id="password" placeholder="Enter your password" />
    <input type="password" name="cpassword" id="cpassword" placeholder="Confirm your password only if signing up" />
    <button type="submit" class="btn">Sign up</button>
    <button type="submit" class="btn">Login</button>
  </form>
</body>

</html>