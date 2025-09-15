<?php
$conn = new mysqli("localhost", "root", "", "keychha");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_id = uniqid("U");

    // Check if email already exists
    $check_email = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        $error = "This email is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO user (user_id, fullname, username, email, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $user_id, $fullname, $username, $email, $password);

        if ($stmt->execute()) {
            header("Location: login.php?success=1");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $check_email->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Register - KeyChha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles/registerAndLogin.css" />
</head>

<body>

    <div class="background-blobs"></div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-form">
                    <h3>Create Your Account</h3>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php elseif ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="post" onsubmit="return validateForm();">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="fullname" class="form-control" required pattern="[A-Za-z\s]+"
                                title="Only letters and spaces allowed" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required minlength="4"
                                pattern="[A-Za-z0-9_]+" title="Only letters, numbers, underscores" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required
                                minlength="6" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" id="confirm_password" class="form-control" required />
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>

                    <div class="login-link">
                        <p class="mt-3 mb-0">Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts/validateRegistration.js"></script>

    </script>

</body>

</html>