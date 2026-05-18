<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include '../includes/db.php';

$userId = (int) $_SESSION['user_id'];
$bookingId = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : 0;
$error = '';
$booking = null;

if ($bookingId <= 0) {
    $error = 'Invalid receipt requested.';
} else {
    $query = "SELECT bookings.*, rooms.room_type, rooms.price, hotels.hotel_name, hotels.location, users.name AS user_name
        FROM bookings
        JOIN rooms ON bookings.room_id = rooms.id
        JOIN hotels ON rooms.hotel_id = hotels.id
        JOIN users ON bookings.user_id = users.id
        WHERE bookings.id = $bookingId AND bookings.user_id = $userId
        LIMIT 1";

    $result = mysqli_query($conn, $query);
    $booking = $result ? mysqli_fetch_assoc($result) : null;

    if (!$booking) {
        $error = 'Receipt not found.';
    } elseif (strtolower($booking['payment_status'] ?? 'pending') !== 'paid') {
        $error = 'This booking has not been paid yet.';
    }
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <a href="bookings.php" class="btn btn-secondary mt-3">Back to My Bookings</a>
    <?php else: ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Payment Receipt</h1>
                <p class="text-muted">Receipt #: <?= htmlspecialchars($booking['id']) ?> - <?= htmlspecialchars($booking['payment_date']) ?></p>
            </div>
            <a href="bookings.php" class="btn btn-secondary">Back to My Bookings</a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($booking['hotel_name']) ?></h5>
                <p class="card-text">
                    <strong>Guest:</strong> <?= htmlspecialchars($booking['user_name']) ?><br>
                    <strong>Room:</strong> <?= htmlspecialchars($booking['room_type']) ?><br>
                    <strong>Location:</strong> <?= htmlspecialchars($booking['location']) ?><br>
                    <strong>Booking Date:</strong> <?= htmlspecialchars($booking['booking_date']) ?><br>
                    <strong>Amount Paid:</strong> ₹<?= htmlspecialchars($booking['price']) ?><br>
                    <strong>Payment Status:</strong> <?= htmlspecialchars($booking['payment_status']) ?><br>
                    <strong>Payment Date:</strong> <?= htmlspecialchars($booking['payment_date']) ?><br>
                </p>
            </div>
        </div>

        <div class="alert alert-success">
            Thank you! Your payment has been completed successfully.
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php';
