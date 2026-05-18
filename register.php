<?php
include '../includes/db.php';

$message = '';
$error = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($name === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $nameEscaped = mysqli_real_escape_string($conn, $name);
        $emailEscaped = mysqli_real_escape_string($conn, $email);
        $passwordEscaped = mysqli_real_escape_string($conn, $password);

        $emailCheck = mysqli_query($conn, "SELECT id FROM users WHERE email = '$emailEscaped' LIMIT 1");
        if ($emailCheck && mysqli_num_rows($emailCheck) > 0) {
            $error = 'This email is already registered. Please login instead.';
        } else {
            $query = "INSERT INTO users (name, email, password) VALUES ('$nameEscaped', '$emailEscaped', '$passwordEscaped')";
            if (mysqli_query($conn, $query)) {
                $message = 'Registration successful. Please login below.';
            } else {
                $error = 'Unable to register. Please try again later.';
            }
        }
    }
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-4">Create Account</h2>

                    <?php if ($message): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-success">Register</button>
                    </form>

                    <p class="mt-3">Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php';
