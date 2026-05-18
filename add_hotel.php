<?php
include 'auth.php';
ensureAdmin();
include '../includes/db.php';

$message = '';
$error = '';

if (isset($_POST['add'])) {
    $hotel = trim($_POST['hotel'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $image = trim($_POST['image'] ?? '');

    if ($hotel === '' || $location === '') {
        $error = 'Hotel name and location are required.';
    } else {
        $hotelEscaped = mysqli_real_escape_string($conn, $hotel);
        $locationEscaped = mysqli_real_escape_string($conn, $location);
        $imageEscaped = mysqli_real_escape_string($conn, $image);

        $query = "INSERT INTO hotels (hotel_name, location, image) VALUES ('$hotelEscaped', '$locationEscaped', '$imageEscaped')";
        if (mysqli_query($conn, $query)) {
            $message = 'Hotel added successfully.';
        } else {
            $error = 'Unable to add hotel. Please try again.';
        }
    }
}

include '../includes/header.php';
include 'nav.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">Add Hotel</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="add_hotel.php">
        <div class="mb-3">
            <label for="hotel" class="form-label">Hotel Name</label>
            <input type="text" id="hotel" name="hotel" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" id="location" name="location" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image URL (optional)</label>
            <input type="text" id="image" name="image" class="form-control">
        </div>
        <button type="submit" name="add" class="btn btn-success">Add Hotel</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>