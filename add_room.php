<?php
include 'auth.php';
ensureAdmin();
include '../includes/db.php';

$message = '';
$error = '';

$hotelsResult = mysqli_query($conn, 'SELECT id, hotel_name FROM hotels ORDER BY hotel_name');

function ensureRoomImageColumn($conn) {
    $columnResult = mysqli_query($conn, "SHOW COLUMNS FROM rooms LIKE 'image'");
    if ($columnResult && mysqli_num_rows($columnResult) === 0) {
        mysqli_query($conn, "ALTER TABLE rooms ADD COLUMN image VARCHAR(255)");
    }
}

if (isset($_POST['add'])) {
    $hotel_id = (int) ($_POST['hotel_id'] ?? 0);
    $room_type = trim($_POST['room_type'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $room_image = trim($_POST['room_image'] ?? '');

    if ($hotel_id <= 0 || $room_type === '' || $price === '') {
        $error = 'Hotel, room type, and price are required.';
    } else {
        $roomTypeEscaped = mysqli_real_escape_string($conn, $room_type);
        $priceValue = (int) $price;
        $roomImageEscaped = mysqli_real_escape_string($conn, $room_image);

        $hotelCheck = mysqli_query($conn, "SELECT id FROM hotels WHERE id = $hotel_id LIMIT 1");
        if (!$hotelCheck || mysqli_num_rows($hotelCheck) === 0) {
            $error = 'Selected hotel does not exist.';
        } else {
            ensureRoomImageColumn($conn);
            $imageColumnExists = mysqli_query($conn, "SHOW COLUMNS FROM rooms LIKE 'image'");

            if ($imageColumnExists && mysqli_num_rows($imageColumnExists) > 0) {
                $query = "INSERT INTO rooms (hotel_id, room_type, price, availability, image) VALUES ($hotel_id, '$roomTypeEscaped', $priceValue, 'Available', '$roomImageEscaped')";
            } else {
                $query = "INSERT INTO rooms (hotel_id, room_type, price, availability) VALUES ($hotel_id, '$roomTypeEscaped', $priceValue, 'Available')";
            }

            if (mysqli_query($conn, $query)) {
                $message = 'Room added successfully.';
            } else {
                $error = 'Unable to add room. Please try again.';
            }
        }
    }
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Add Room</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($hotelsResult && mysqli_num_rows($hotelsResult) > 0): ?>
        <form method="POST" action="add_room.php">
            <div class="mb-3">
                <label for="hotel_id" class="form-label">Hotel</label>
                <select id="hotel_id" name="hotel_id" class="form-select" required>
                    <option value="">Select hotel</option>
                    <?php while ($hotel = mysqli_fetch_assoc($hotelsResult)): ?>
                        <option value="<?= htmlspecialchars($hotel['id']) ?>"><?= htmlspecialchars($hotel['hotel_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="room_type" class="form-label">Room Type</label>
                <input type="text" id="room_type" name="room_type" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" id="price" name="price" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="room_image" class="form-label">Room Image URL (optional)</label>
                <input type="text" id="room_image" name="room_image" class="form-control" placeholder="https://example.com/room.jpg or images/room.jpg">
                <div class="form-text">Enter a full URL or a filename from the images/ folder.</div>
            </div>
            <button type="submit" name="add" class="btn btn-success">Add Room</button>
        </form>
    <?php else: ?>
        <div class="alert alert-warning">No hotels available. Please add a hotel first.</div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
