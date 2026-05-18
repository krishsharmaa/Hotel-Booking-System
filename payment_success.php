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
    $error = 'Invalid booking selected.';
} else {
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
    } elseif (strtolower($booking['payment_status'] ?? 'pending') !== 'paid') {
        $error = 'Payment is not completed for this booking yet.';
    }
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1>Payment Success</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <a href="bookings.php" class="btn btn-secondary mt-3">Back to My Bookings</a>
    <?php else: ?>
        <div class="alert alert-success">Your payment was successful.</div>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($booking['hotel_name']) ?></h5>
                <p class="card-text">
                    Room: <?= htmlspecialchars($booking['room_type']) ?><br>
                    Location: <?= htmlspecialchars($booking['location']) ?><br>
                    Booking Date: <?= htmlspecialchars($booking['booking_date']) ?><br>
                    Paid Amount: ₹<?= htmlspecialchars($booking['price']) ?><br>
                    Payment Date: <?= htmlspecialchars($booking['payment_date']) ?><br>
                </p>
            </div>
        </div>
        <a href="receipt.php?booking_id=<?= urlencode($booking['id']) ?>" class="btn btn-info">View Receipt</a>
        <a href="bookings.php" class="btn btn-primary ms-2">Back to My Bookings</a>
        <a href="../hotels.php" class="btn btn-secondary ms-2">Continue Browsing</a>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php';
