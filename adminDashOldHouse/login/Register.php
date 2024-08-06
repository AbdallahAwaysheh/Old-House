<?php
include("../includes/connection2.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="regist.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="main">
        <img src="../media/Furniture_store-bro_1.png">
        <div class="container">
            <div class="form-container">
                <h1>Join Us</h1>
                <form method="POST" action="Register.php">
                    <label for="first-name">First Name</label>
                    <input type="text" name="first-name" id="first-name" required>

                    <label for="last-name">Last Name</label>
                    <input type="text" name="last-name" id="last-name" required>

                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>

                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" id="dob" required>

                    <label for="gender">Gender</label>
                    <select name="gender" id="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>

                    <label for="mobile">Your Mobile</label>
                    <input type="text" name="mobile" id="mobile" required>

                    <label for="email">E-Mail</label>
                    <input type="email" name="email" id="email" required>

                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" required>

                    <button type="submit">Sign Up</button>
                    <label class="signin" for="signin">Already have an account? <a href="login.php">log in</a></label>
                </form>
            </div>
        </div>
    </div>

</body>

</html>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $user_type = 'user';

    // Validate inputs using regex
    $errors = [];

    // Validate mobile number (e.g., only digits and length 10)
    if (!preg_match('/^\d{10}$/', $mobile)) {
        $errors[] = "Invalid mobile number. It should be 10 digits.";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password (e.g., at least 8 characters, including one uppercase letter, one lowercase letter, and one digit)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one digit.";
    }

    if (empty($errors)) {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO customers (cus_fname, cus_lname, cus_pass, dob, gender, mobile, cus_email, shippingAddress, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssisss", $first_name, $last_name, $password_hashed, $dob, $gender, $mobile, $email, $address, $user_type);

        if ($stmt->execute() === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>