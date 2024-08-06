<?php
session_start();
require_once("../includes/connection2.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $error = "Please enter both email and password.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT cus_id, cus_pass, user_type FROM customers WHERE cus_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($password === $user['cus_pass']) {
                    $_SESSION['user_id'] = $user['cus_id'];
                    $_SESSION['user_type'] = $user['user_type'];

                    $redirect = ($user['user_type'] == 'admin') ? "../main/index.php" : "http://localhost/oldhomeproject/homepage.php";
                    header("Location: $redirect");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        } finally {
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css?v=<?= time() ?>">
</head>

<body>
    <div class="main">
        <img src="../media/Furniture_store-bro_1.png" alt="Furniture store illustration">
        <div class="container">
            <div class="form-container">
                <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <label for="email">E-Mail</label>
                    <input class="email" type="email" name="email" id="email" required>

                    <label for="password">Password</label>
                    <input class="password" type="password" name="password" id="password" required>

                    <?php if ($error) : ?>
                        <p class="error"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>

                    <button type="submit" name="submit">Log In</button>
                    <p class="login">Don't have an account? <a href="Register.php">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>