<?php
session_start();
if (isset($_SESSION['user'])!= "") {
    header("Location: index.php");
}
include_once 'dbconnect.php';

if (isset($_POST['signup'])) {

// get posted data and remove whitespace
    $uname = trim($_POST['uname']); 
    $email = trim($_POST['email']);
    $upass = trim($_POST['pass']);

    // hash password with SHA256;
    $password = hash('sha256', $upass);

    // check email exist or not
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $count = $result->num_rows;

    if ($count == 0) { // if email is not found add user

        $stmts = $conn->prepare("INSERT INTO users(username,email,password) VALUES(?, ?, ?)");
        $stmts->bind_param("sss", $uname, $email, $password);
        $res = $stmts->execute();//get result
        $stmts->close();

        $user_id = mysqli_insert_id($conn);
        if ($user_id > 0) {
            $_SESSION['user'] = $user_id; // set session and redirect to index page
            if (isset($_SESSION['user'])) {
                print_r($_SESSION);
                header("Location: index.php");
                exit;

                
            }

        } else {
            $errTyp = "danger";
            $errMSG = "Something went wrong, try again";
        }

    } else {
        $errTyp = "warning";
        $errMSG = "Email is already used";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="wrapper">
        <div class="form-holder">
        <img src="img/user.png" class="logo" alt="">
            <h2>Create New account</h2>
            <form method="post" class="form">

                <div class="form-group">
                    <img src="img/user.png" alt="">
                    <input type="text" name="uname" class="form-control" placeholder="User name" required/>
                </div>
                <div class="form-group">
                    <img src="img/email.png" alt="">
                    <input type="text" name="email" class="form-control" placeholder="Email" required/>
                </div>
                <div class="form-group">
                    <img src="img/lock.png" alt="">
                    <input type="password" name="pass" class="form-control" placeholder="Enter Password"    required/>
                </div>
                <div class="form-group">
                    <button type="submit"  name="signup">Register</button>
                </div>
                <div class="form-group">
                    <a href="login.php" class="right-link">Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>