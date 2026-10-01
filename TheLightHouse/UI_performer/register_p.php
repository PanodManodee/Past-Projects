<?php
require_once('dbconn.php');
session_start();

if (isset($_POST['register'])) {

    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $username = $_POST['username'];
    $password = $_POST['passwd'];
    $alias = $_POST['alias'];
    $pay_meth = $_POST["pay_meth"];

    // Hash password safely
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("
        INSERT INTO performer (username, password, fname, lname, p_alias, payment_method)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameters (all strings)
    $stmt->bind_param("ssssss", $username, $hash, $firstname, $lastname, $alias, $pay_meth);

    // Execute
    if (!$stmt->execute()) {
        die("Register failed: " . $stmt->error);
    }

    // Get new customer ID
    $new_id = $stmt->insert_id;

    // Store ID in session
    $_SESSION['performer_id'] = $new_id;
    $_SESSION['p_alias'] = $alias;

    // Close statement
    $stmt->close();

    header("Location: home_p.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <div id="top">
        <ul id="menu">
            <li><a href="../UI_1/home_c_noacc.php">Home</a></li>
        </ul>
    </div>

    <div id="content">
        <br>
        <div id="back">
            <a href="login_p.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Register New Performer Account</h2>
        </div>
        <div id="book_search">

            <!--IDK IF THE ACTION IS CORRECT. (SO AS OTHER'S TBH) -->

            <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
                <label>First Name</label>
                <input type="text" name="fname"><br><br><br>
                <label>Last Name</label>
                <input type="text" name="lname"><br><br><br>
                <label>Username</label>
                <input type="text" name="username"><br><br><br>
                <label>Password</label>
                <input type="password" name="passwd"><br><br><br>
                <label>Alias</label>
                <input type="text" name="alias" style="margin-left: 20px;"><br><br><br>
                <label>payment method</label>
                <select name="pay_meth">
                    <option value="Prompt Pay">Prompt Pay</option>
                    <option value="True Money Wallet">True Money Wallet</option>
                    <option value="Credit card">Credit card</option>
                    <option value="Paypal">Paypal</option>
                </select><br><br><br>

        </div>
        <div id="zone">

            <!-- Register and return to home page -->
            <input type="submit" name="register" value="Register" id="register"><br><br><br>
            </form>
        </div>
    </div>
</body>

</html>