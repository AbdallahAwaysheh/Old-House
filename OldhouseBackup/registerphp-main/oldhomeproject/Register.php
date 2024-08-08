<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="regist.css">
</head>
<body>
    <div class="main">
        <img src="img/Furniture_store-bro_1.webp">
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
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "registration";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $first_name = $_POST['first-name'];
    $last_name = $_POST['last-name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $user_type = 'user'; 

    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, password, dob, gender, mobile, email, address, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $first_name, $last_name, $password, $dob, $gender, $mobile, $email, $address, $user_type);

    if ($stmt->execute() === TRUE) {
        // echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>











































































