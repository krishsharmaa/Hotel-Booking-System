<?php
session_start();
include '../includes/db.php';

function cleanRedirect($redirect) {
    $redirect = trim($redirect);
    if ($redirect === '') {
        return '';
    }
    $parsed = parse_url($redirect);
    if (isset($parsed['scheme']) || isset($parsed['host'])) {
        return '';
    }
    if (strpos($redirect, '/hotel_booking_system/') === 0) {
        return $redirect;
    }
    if (strpos($redirect, 'hotel_booking_system/') === 0) {
        return '/' . ltrim($redirect, '/');
    }
    return '';
}

$message = '';
$redirect = cleanRedirect($_GET['redirect'] ?? '');

if (isset($_GET['registered']) && $_GET['registered'] === '1') {
    $message = 'Registration successful. Please login with your credentials.';
}

if (isset($_SESSION['user_id'])) {
    if ($redirect !== '') {
        header('Location: ' . $redirect);
    } else {
        header('Location: ../index.php');
    }
    exit;
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $redirect = cleanRedirect($_POST['redirect'] ?? $redirect);

    $query = "SELECT * FROM users
              WHERE email='" . mysqli_real_escape_string($conn, $email) . "'
              AND password='" . mysqli_real_escape_string($conn, $password) . "'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];

        if ($redirect !== '') {
            header('Location: ' . $redirect);
        } else {
            header('Location: ../index.php');
        }
        exit;
    }

    $message = 'Invalid Email or Password';
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-4">User Login</h2>

                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php<?= $redirect ? '?redirect=' . urlencode($redirect) : '' ?>">
                        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php';

