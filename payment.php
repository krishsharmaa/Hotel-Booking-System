<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    $redirect = '/hotel_booking_system/user/payment.php?booking_id=' . urlencode($_GET['booking_id'] ?? '');
    header('Location: login.php?redirect=' . urlencode($redirect));
    exit;
}

include '../includes/db.php';

$userId = (int) $_SESSION['user_id'];
$bookingId = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
$message = '';
$error = '';

if ($bookingId <= 0) {
    $error = 'Invalid booking selected.';
}

if (!$error) {
    $bookingQuery = "SELECT bookings.*, rooms.room_type, rooms.price, hotels.hotel_name, hotels.location
        FROM bookings
        JOIN rooms ON bookings.room_id = rooms.id
        JOIN hotels ON rooms.hotel_id = hotels.id
        WHERE bookings.id = $bookingId AND bookings.user_id = $userId
        LIMIT 1";
    $bookingResult = mysqli_query($conn, $bookingQuery);
    $booking = $bookingResult ? mysqli_fetch_assoc($bookingResult) : null;

    if (!$booking) {
        $error = 'Booking not found.';
    } elseif (strtolower($booking['status']) !== 'booked') {
        $error = 'Only active bookings can be paid.';
    }
}

if (!$error && $booking && strtolower($booking['payment_status'] ?? 'pending') === 'paid') {
    $message = 'This booking has already been paid.';
}

if (!$error && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_now'])) {
    if (strtolower($booking['payment_status'] ?? 'pending') === 'paid') {
        $error = 'Payment is already completed for this booking.';
    } else {
        $cardNumber = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
        $expiry = trim($_POST['expiry'] ?? '');
        $cvc = trim($_POST['cvc'] ?? '');

        if ($cardNumber === '' || $expiry === '' || $cvc === '') {
            $error = 'Card number, expiry date, and CVC are required.';
        } elseif (!preg_match('/^\d{13,19}$/', $cardNumber)) {
            $error = 'Enter a valid card number.';
        } elseif (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiry, $expiryMatches)) {
            $error = 'Enter expiry in MM/YY format.';
        } elseif (!preg_match('/^\d{3,4}$/', $cvc)) {
            $error = 'Enter a valid CVC.';
        } else {
            $updateQuery = "UPDATE bookings SET payment_status = 'Paid', payment_date = CURDATE() WHERE id = $bookingId";
            if (mysqli_query($conn, $updateQuery)) {
                header('Location: payment_success.php?booking_id=' . urlencode($bookingId));
                exit;
            } else {
                $error = 'Unable to process payment. Please try again.';
            }
        }
    }
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1>Booking Payment</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!$error && $booking): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($booking['hotel_name']) ?></h5>
                <p class="card-text">
                    Room: <?= htmlspecialchars($booking['room_type']) ?><br>
                    Location: <?= htmlspecialchars($booking['location']) ?><br>
                    Booking Date: <?= htmlspecialchars($booking['booking_date']) ?><br>
                    Price: ₹<?= htmlspecialchars($booking['price']) ?><br>
                    Payment Status: <?= htmlspecialchars($booking['payment_status'] ?? 'Pending') ?><br>
                    <?php if (!empty($booking['payment_date'])): ?>
                        Payment Date: <?= htmlspecialchars($booking['payment_date']) ?><br>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <?php if (strtolower($booking['payment_status'] ?? 'pending') !== 'paid'): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="card_number" class="form-label">Card Number</label>
                    <input type="text" id="card_number" name="card_number" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="expiry" class="form-label">Expiry (MM/YY)</label>
                        <input type="text" id="expiry" name="expiry" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cvc" class="form-label">CVC</label>
                        <input type="text" id="cvc" name="cvc" class="form-control" required>
                    </div>
                </div>
                <button type="submit" name="pay_now" class="btn btn-success">Pay ₹<?= htmlspecialchars($booking['price']) ?> Now</button>
                <a href="bookings.php" class="btn btn-secondary ms-2">Back to Bookings</a>
            </form>
        <?php else: ?>
            <a href="bookings.php" class="btn btn-secondary">Back to Bookings</a>
        <?php endif; ?>
    <?php else: ?>
        <a href="bookings.php" class="btn btn-secondary">Back to Bookings</a>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php';
