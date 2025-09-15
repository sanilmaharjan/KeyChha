<?php
session_start();
$conn = new mysqli("localhost", "root", "", "keychha");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, fullname, username, password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $fullname, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['logged_in'] = true;

            $stmt->close();
            $conn->close();

            header("Location: index.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Login - KeyChha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles/registerAndLogin.css" />
</head>

<body>
    <div class="background-blobs"></div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-form">
                    <h3>Welcome Back</h3>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Account registered! Please login.</div>
                    <?php elseif (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required />
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <div class="login-link">
                        <p class="mt-3 mb-0">Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>