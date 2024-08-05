
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registration";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $conn->prepare("SELECT id, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password, $user_type);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_type'] = $user_type;

                if ($user_type == 'admin') {
                    header("Location: http://localhost/oldhomeproject/dashboard.php");
                } else {
                    header("Location: http://localhost/oldhomeproject/homepage.php");
                }
                exit();
            } else {
                $error = "Password or email is incorrect!";
            }
        } else {
            $error = "Account does not exist!";
        }

    $stmt->close();
    $conn->close();
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="login.css?v<?php echo time()?>">
</head>
<body>
    <div class="main">
        <img src="img/Furniture_store-bro_1.webp">
        <div class="container">
            <div class="form-container">
                <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                    <label for="email">E-Mail</label>
                    <input class="email" type="email" name="email" id="email" >
                    
                    <label for="password">Password</label>
                    <input class="password" type="password" name="password" id="password">
                    
                    <p class="error"><?php if (isset($error)){echo $error;} ?></p>

                    <button type="submit" name="submit">Log In</button>
                    <label class="login" for="login"> Don't have an account?<a href="Register.php">Sign Up</a></label>

                </form>
            </div>
        </div>
    </div>
</body>
</html>
