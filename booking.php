<?php
include 'auth.php';
ensureAdmin();
include '../includes/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $bookingId = isset($_POST['booking_id']) ? (int) $_POST['booking_id'] : 0;
    if ($bookingId > 0) {
        $bookingCheck = mysqli_query($conn, "SELECT * FROM bookings WHERE id = $bookingId AND status = 'Booked' LIMIT 1");
        if ($bookingCheck && mysqli_num_rows($bookingCheck) > 0) {
            $bookingRow = mysqli_fetch_assoc($bookingCheck);
            $roomId = (int) $bookingRow['room_id'];
            mysqli_query($conn, "UPDATE bookings SET status = 'Cancelled' WHERE id = $bookingId");
            mysqli_query($conn, "UPDATE rooms SET availability = 'Available' WHERE id = $roomId");
            $message = 'Booking cancelled successfully.';
        } else {
            $error = 'Unable to cancel this booking.';
        }
    } else {
        $error = 'Invalid booking selected for cancellation.';
    }
}

$query = "SELECT bookings.*, users.name AS user_name, rooms.room_type, hotels.hotel_name
 FROM bookings
 JOIN users ON bookings.user_id = users.id
 JOIN rooms ON bookings.room_id = rooms.id
 JOIN hotels ON rooms.hotel_id = hotels.id
 ORDER BY bookings.booking_date DESC";
$result = mysqli_query($conn, $query);

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1>All Bookings</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Hotel</th>
                        <th>Room</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <?php
                            $status = strtolower($row['status']);
                            $paymentStatus = strtolower($row['payment_status'] ?? 'pending');
                            $rowClass = $status === 'cancelled' ? 'table-danger' : ($paymentStatus === 'paid' ? 'table-success' : '');
                            $statusBadge = $status === 'booked' ? 'primary' : ($status === 'cancelled' ? 'secondary' : 'dark');
                            $paymentBadge = $paymentStatus === 'paid' ? 'success' : 'warning';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['hotel_name']) ?></td>
                            <td><?= htmlspecialchars($row['room_type']) ?></td>
                            <td><?= htmlspecialchars($row['booking_date']) ?></td>
                            <td><span class="badge bg-<?= $statusBadge ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            <td><span class="badge bg-<?= $paymentBadge ?>"><?= htmlspecialchars($row['payment_status'] ?? 'Pending') ?></span></td>
                            <td>
                                <?php if ($status === 'booked'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit" name="cancel_booking" class="btn btn-sm btn-danger">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-3">No bookings found.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
