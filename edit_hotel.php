<?php
include 'auth.php';
ensureAdmin();
include '../includes/db.php';

$message = '';
$error = '';

$hotelId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($hotelId <= 0) {
    header('Location: hotels.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_hotel'])) {
    $hotelName = trim($_POST['hotel_name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($hotelName === '' || $location === '') {
        $error = 'Hotel name and location are required.';
    } else {
        $hotelEscaped = mysqli_real_escape_string($conn, $hotelName);
        $locationEscaped = mysqli_real_escape_string($conn, $location);
        $imageEscaped = mysqli_real_escape_string($conn, $image);

        $updateQuery = "UPDATE hotels SET hotel_name = '$hotelEscaped', location = '$locationEscaped', image = '$imageEscaped' WHERE id = $hotelId";
        if (mysqli_query($conn, $updateQuery)) {
            $message = 'Hotel details updated successfully.';
        } else {
            $error = 'Unable to update hotel. Please try again.';
        }
    }
}

$hotelQuery = "SELECT * FROM hotels WHERE id = $hotelId LIMIT 1";
$hotelResult = mysqli_query($conn, $hotelQuery);
$hotel = $hotelResult ? mysqli_fetch_assoc($hotelResult) : null;

if (!$hotel) {
    header('Location: hotels.php');
    exit;
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1>Edit Hotel</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_hotel.php?id=<?= urlencode($hotelId) ?>">
        <div class="mb-3">
            <label for="hotel_name" class="form-label">Hotel Name</label>
            <input type="text" id="hotel_name" name="hotel_name" class="form-control" value="<?= htmlspecialchars($hotel['hotel_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" value="<?= htmlspecialchars($hotel['location']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image URL</label>
            <input type="text" id="image" name="image" class="form-control" value="<?= htmlspecialchars($hotel['image']) ?>">
            <div class="form-text">Add a full URL or local filename stored in the images/ folder.</div>
        </div>
        <button type="submit" name="update_hotel" class="btn btn-primary">Save Changes</button>
        <a href="hotels.php" class="btn btn-secondary ms-2">Back to Hotels</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>