<?php
session_start();
include 'includes/db.php';

function getHotelImageSrc($image) {
    if (empty($image)) {
        return 'https://via.placeholder.com/900x300?text=No+Image+Available';
    }

    $trimmed = trim($image);
    if (preg_match('#^(https?://|data:|/)#i', $trimmed)) {
        return $trimmed;
    }

    return 'images/' . ltrim($trimmed, '/\\');
}

$hotelId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = '';
$error = '';

if ($hotelId <= 0) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Invalid hotel ID.</div></div>';
    include 'includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: user/login.php');
        exit;
    }

    $roomId = isset($_POST['room_id']) ? (int) $_POST['room_id'] : 0;
    $bookingDate = trim($_POST['booking_date'] ?? '');
    $userId = (int) $_SESSION['user_id'];

    if ($roomId <= 0 || $bookingDate === '') {
        $error = 'Please select a room and booking date.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $bookingDate) || $bookingDate < date('Y-m-d')) {
        $error = 'Please choose a valid booking date.';
    } else {
        $roomCheck = mysqli_query($conn, "SELECT * FROM rooms WHERE id = $roomId AND hotel_id = $hotelId LIMIT 1");
        $room = $roomCheck ? mysqli_fetch_assoc($roomCheck) : null;

        if (!$room) {
            $error = 'Room not found for this hotel.';
        } elseif (strtolower($room['availability']) !== 'available') {
            $error = 'This room is not available for booking.';
        } else {
            $bookingDateEscaped = mysqli_real_escape_string($conn, $bookingDate);
            $insertBooking = "INSERT INTO bookings (user_id, room_id, booking_date, status, payment_status) VALUES ($userId, $roomId, '$bookingDateEscaped', 'Booked', 'Pending')";
            if (mysqli_query($conn, $insertBooking)) {
                mysqli_query($conn, "UPDATE rooms SET availability = 'Booked' WHERE id = $roomId");
                $message = 'Room booked successfully. Payment is pending until you complete checkout.';
            } else {
                $error = 'Unable to complete the booking. Please try again.';
            }
        }
    }
}

$hotelQuery = "SELECT * FROM hotels WHERE id = $hotelId";
$hotelResult = mysqli_query($conn, $hotelQuery);
$hotel = $hotelResult ? mysqli_fetch_assoc($hotelResult) : null;

if (!$hotel) {
    echo '<div class="container mt-5"><div class="alert alert-warning">Hotel not found.</div></div>';
    include 'includes/footer.php';
    exit;
}

$roomsQuery = "SELECT * FROM rooms WHERE hotel_id = $hotelId";
$roomsResult = mysqli_query($conn, $roomsQuery);

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1><?= htmlspecialchars($hotel['hotel_name']) ?></h1>
            <p class="text-muted"><?= htmlspecialchars($hotel['location']) ?></p>
        </div>
        <div>
            <a href="hotels.php" class="btn btn-secondary">Back to Hotels</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="user/bookings.php" class="btn btn-info ms-2">My Bookings</a>
            <?php else: ?>
                <a href="user/login.php?redirect=/hotel_booking_system/hotel_details.php?id=<?= $hotelId ?>" class="btn btn-primary ms-2">Login</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="mb-4">
        <img src="<?= htmlspecialchars(getHotelImageSrc($hotel['image'])) ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($hotel['hotel_name']) ?>">
    </div>

    <?php if ($roomsResult && mysqli_num_rows($roomsResult) > 0): ?>
        <div class="row">
            <?php while ($room = mysqli_fetch_assoc($roomsResult)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($room['room_type']) ?></h5>
                            <p class="card-text">
                                Price: ₹<?= htmlspecialchars($room['price']) ?><br>
                                Availability: <?= htmlspecialchars($room['availability']) ?>
                            </p>

                            <?php if (strtolower($room['availability']) === 'available'): ?>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="POST" class="mt-3">
                                        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">
                                        <div class="mb-2">
                                            <label for="booking_date_<?= htmlspecialchars($room['id']) ?>" class="form-label">Booking Date</label>
                                            <input type="date" id="booking_date_<?= htmlspecialchars($room['id']) ?>" name="booking_date" class="form-control" min="<?= date('Y-m-d') ?>" required>
                                        </div>
                                        <button type="submit" name="book_room" class="btn btn-success">Book Now</button>
                                    </form>
                                <?php else: ?>
                                    <a href="user/login.php?redirect=/hotel_booking_system/hotel_details.php?id=<?= $hotelId ?>" class="btn btn-primary mt-3">Login to Book</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn btn-secondary mt-3" disabled>Not Available</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No rooms available for this hotel yet.</div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php';
